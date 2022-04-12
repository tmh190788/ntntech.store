<?php
/**
 * Basic field abstract class.
 *
 * @package xts
 */

namespace XTS\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Abstract class for the field.
 */
abstract class Field {

	/**
	 * ID of the field
	 *
	 * @var int
	 */
	private $_id;

	/**
	 * Args array for the field
	 *
	 * @var array
	 */
	public $args = array();

	/**
	 * Options array from the database for the field value.
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Options set prefix.
	 *
	 * @var string
	 */
	public $opt_name = XTS_THEME_SLUG;

	/**
	 * Field type
	 *
	 * @var string
	 */
	public $_type;

	/**
	 * Post object. Required for metabox field to get the value from the database.
	 *
	 * @var null
	 */
	private $_post = null;

	/**
	 * Term object. Required for metabox field to get the value from the database.
	 *
	 * @var null
	 */
	private $_term = null;

	/**
	 * Metabox object. Post or term.
	 *
	 * @var null
	 */
	private $_object = null;

	/**
	 * Presets IDs.
	 *
	 * @var null
	 */
	private $_presets = false;

	/**
	 * Is this field inherits value. (not use preset value)
	 *
	 * @var boolean
	 */
	private $_inherit_value;

	/**
	 * Extra wrapper CSS class.
	 *
	 * @var string
	 */
	public $extra_css_class = '';

	/**
	 * Construct the object.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args    Field args array.
	 * @param array  $options Options from the database.
	 * @param string $type    Field type.
	 * @param string $object  $object   Object for post or term.
	 */
	public function __construct( $args, $options, $type = 'options', $object = 'post' ) {
		$this->args = $args;
		$this->_id  = $args['id'];

		if ( $options ) {
			$this->options = $options;
		}

		$this->_type   = $type;
		$this->_object = $object;

		$this->extra_css_class  = 'xts-' . $this->args['type'] . '-control';
		$this->extra_css_class .= ' xts-' . $this->args['id'] . '-field';

		if ( $this->dependency_class() ) {
			$this->extra_css_class .= ' ' . $this->dependency_class();
		}

		if ( isset( $this->args['class'] ) ) {
			$this->extra_css_class .= ' ' . $this->args['class'];
		}

		if ( isset( $this->args['desktop_only'] ) && $this->args['desktop_only'] && in_array( 'desktop', $this->args['responsive_variants'] ) ) { // phpcs:ignore
			$this->extra_css_class .= ' xts-desktop-field xts-responsive-field';
		}

		if ( isset( $this->args['tablet_only'] ) && $this->args['tablet_only'] && in_array( 'tablet', $this->args['responsive_variants'] ) ) { // phpcs:ignore
			$this->extra_css_class .= ' xts-tablet-field xts-responsive-field';
		}

		if ( isset( $this->args['mobile_only'] ) && $this->args['mobile_only'] && in_array( 'mobile', $this->args['responsive_variants'] ) ) { // phpcs:ignore
			$this->extra_css_class .= ' xts-mobile-field xts-responsive-field';
		}

		if ( isset( $this->args['mobile_small_only'] ) && $this->args['mobile_small_only'] && in_array( 'mobile_small', $this->args['responsive_variants'] ) ) { // phpcs:ignore
			$this->extra_css_class .= ' xts-mobile-small-field xts-responsive-field';
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 300 );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ), 300 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'frontend_enqueue' ), 300 );
		add_action( 'admin_init', array( $this, 'admin_hooks' ), 300 );
	}

	/**
	 * Validate field value. For example check file ID and URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string or array $value Field value.
	 *
	 * @return mixed
	 */
	public function validate( $value ) {
		return $value;
	}

	/**
	 * ID getter
	 *
	 * @since 1.0.0
	 *
	 * @return int field id value.
	 */
	public function get_id() {
		return $this->_id;
	}

	/**
	 * Set post
	 *
	 * @since 1.0.0
	 *
	 * @param  object $object Post object for metaboxes fields.
	 */
	public function set_post( $object ) {
		if ( is_a( $object, 'WP_Post' ) && 'metabox' === $this->_type ) {
			$this->_post = $object;
		}
	}

	/**
	 * Render the field HTML based on the control class.
	 *
	 * @since 1.0.0
	 *
	 * @param object $object Post or Term object for metaboxes fields.
	 * @param bool   $preset Current field preset ID.
	 */
	public function render( $object = null, $preset = false ) {
		if ( $preset ) {
			$this->_presets = array( $preset );
		}

		if ( $preset && $this->is_inherit_value() && 'notice' !== $this->args['type'] ) {
			$this->extra_css_class .= ' xts-field-disabled';
		}

		if ( is_a( $object, 'WP_Post' ) ) {
			$this->_post = $object;
		} elseif ( is_a( $object, 'WP_Term' ) ) {
			$this->_term = $object;
		}

		$this->before();

		$this->render_control();

		$this->after();
	}

	/**
	 * Before the control output.
	 *
	 * @since 1.0.0
	 */
	public function before() {
		?>
			<div class="xts-field xts-col <?php echo esc_attr( $this->extra_css_class ); ?>" <?php $this->get_dependency_data_attribute(); ?> data-id="<?php echo esc_attr( $this->args['id'] ); ?>">
				<div class="xts-field-title">
					<span>
						<?php echo esc_html( $this->args['name'] ); ?>
					</span>

					<?php if ( isset( $this->args['responsive'] ) && $this->args['responsive'] ) : ?>
						<div class="xts-field-responsive-selector">
							<?php if ( in_array( 'desktop', $this->args['responsive_variants'] ) ) : // phpcs:ignore ?>
								<span class="xts-responsive-switch xts-inline-btn xts-switch-desktop <?php echo isset( $this->args['desktop_only'] ) ? 'xts-active' : ''; ?>">
									<?php esc_html_e( 'Desktop', 'xts-theme' ); ?>
								</span>
							<?php endif; ?>

							<?php if ( in_array( 'tablet', $this->args['responsive_variants'] ) ) : // phpcs:ignore ?>
								<span class="xts-responsive-switch xts-inline-btn xts-switch-tablet <?php echo isset( $this->args['tablet_only'] ) ? 'xts-active' : ''; ?>">
								<?php esc_html_e( 'Tablet', 'xts-theme' ); ?>
								</span>
							<?php endif; ?>

							<?php if ( in_array( 'mobile', $this->args['responsive_variants'] ) ) : // phpcs:ignore ?>
								<span class="xts-responsive-switch xts-inline-btn xts-switch-mobile <?php echo isset( $this->args['mobile_only'] ) ? 'xts-active' : ''; ?>">
									<?php esc_html_e( 'Mobile', 'xts-theme' ); ?>
								</span>
							<?php endif; ?>

							<?php if ( in_array( 'mobile_small', $this->args['responsive_variants'] ) ) : // phpcs:ignore ?>
								<span class="xts-responsive-switch xts-inline-btn xts-switch-mobile_small <?php echo isset( $this->args['mobile_small_only'] ) ? 'xts-active' : ''; ?>">
									<?php esc_html_e( 'Mobile small', 'xts-theme' ); ?>
								</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( false !== $this->_presets && 'notice' !== $this->args['type'] && isset( $_GET['preset'] ) ) : // phpcs:ignore ?>
						<label>
							<div class="xts-inherit-checkbox-wrapper">
								Inherit <input type="checkbox" <?php checked( true, $this->is_inherit_value() ); ?> data-name="<?php echo esc_attr( $this->args['id'] ); ?>" value="1">
							</div>
						</label>
					<?php endif; ?>
				</div>
				<div class="xts-field-inner">
		<?php
	}

	/**
	 * Set field's presets IDs.
	 *
	 * @since 1.0.0
	 *
	 * @param  int $id Presets Ids.
	 */
	public function set_presets( $id ) {
		$this->_presets = $id;
	}

	/**
	 * Set inherit value flag.
	 *
	 * @since 1.0.0
	 *
	 * @param  boolean $value Yes or no.
	 */
	public function inherit_value( $value ) {
		$this->_inherit_value = $value;
	}

	/**
	 * Inherit value flag getter.
	 *
	 * @since 1.0.0
	 */
	public function is_inherit_value() {
		return $this->_inherit_value;
	}

	/**
	 * Echo dependency data attribute.
	 *
	 * @since 1.0.0
	 */
	private function get_dependency_data_attribute() {
		if ( ! isset( $this->args['requires'] ) ) {
			return;
		}

		$data = '';

		foreach ( $this->args['requires'] as $dependency ) {
			if ( is_array( $dependency['value'] ) ) {
				$dependency['value'] = implode( ',', $dependency['value'] );
			}

			$data .= $dependency['key'] . ':' . $dependency['compare'] . ':' . $dependency['value'] . ';';
		}

		echo 'data-dependency="' . esc_attr( $data ) . '"';
	}

	/**
	 * Get dependency class.
	 *
	 * @since 1.0.0
	 */
	private function dependency_class() {
		if ( ! isset( $this->args['requires'] ) ) {
			return '';
		}

		$shown = true;

		foreach ( $this->args['requires'] as $dependency ) {
			if ( ! $shown ) {
				continue;
			}

			switch ( $dependency['compare'] ) {
				case 'equals':
					if ( isset( $this->options[ $dependency['key'] ] ) ) {
						if ( is_array( $dependency['value'] ) ) {
							$shown = in_array( $this->options[ $dependency['key'] ], $dependency['value'] ); // phpcs:ignore
						} else {
							$shown = $this->options[ $dependency['key'] ] == $dependency['value']; // phpcs:ignore
						}
					}
					break;
				case 'not_equals':
					if ( isset( $this->options[ $dependency['key'] ] ) ) {
						if ( is_array( $dependency['value'] ) ) {
							$shown = ! in_array( $this->options[ $dependency['key'] ], $dependency['value'] ); // phpcs:ignore
						} else {
							$shown = $this->options[ $dependency['key'] ] != $dependency['value']; // phpcs:ignore
						}
					}
					break;
			}
		}

		return ( $shown ) ? 'xts-shown' : 'xts-hidden';
	}

	/**
	 * After the control output.
	 *
	 * @since 1.0.0
	 */
	public function after() {
		if ( isset( $this->args['description'] ) && ! empty( $this->args['description'] ) ) {
			?>
				<p class="xts-description">
					<?php echo wp_kses( $this->args['description'], true ); ?>
				</p>
			<?php
		}
		?>

				</div>
			</div>
		<?php
	}

	/**
	 * Get input name for form tags like input, textarea etc.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $subkey  Subkey for array fields.
	 * @param bool $subkey2 Subkey for array fields. Second level.
	 * @param bool $subkey3 Subkey for array fields. Third level.
	 *
	 * @return string input field name.
	 */
	public function get_input_name( $subkey = false, $subkey2 = false, $subkey3 = false ) {
		$name = 'xts-' . $this->opt_name . '-options';

		$name .= '[' . $this->args['id'] . ']';

		if ( 'metabox' === $this->_type ) {
			$name = '_xts_' . $this->args['id'];
		}

		if ( false !== $subkey ) {
			$name .= '[' . $subkey . ']';
		}

		if ( false !== $subkey2 ) {
			$name .= '[' . $subkey2 . ']';
		}

		if ( false !== $subkey3 ) {
			$name .= '[' . $subkey3 . ']';
		}

		return $name;
	}

	/**
	 * Get field value from options array or from post meta data.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $subkey Subkey for array fields.
	 *
	 * @return mixed Field value.
	 */
	public function get_field_value( $subkey = false ) {
		$val = '';

		$object = $this->_post ? $this->_post : $this->_term;

		if ( 'metabox' === $this->_type && ! is_null( $object ) ) {
			$object_id = $this->_post ? $this->_post->ID : $this->_term->term_id;
			$val       = get_metadata( $this->_object, $object_id, $this->get_input_name(), true );
		} elseif ( false !== $this->_presets ) {
			foreach ( $this->_presets as $preset_id ) {
				if ( isset( $this->options[ $preset_id ] ) && isset( $this->options[ $preset_id ][ $this->args['id'] ] ) ) {
					$val = $this->options[ $preset_id ][ $this->args['id'] ];
				}
			}

			if ( empty( $val ) && '0' !== $val ) {
				$val = $this->options[ $this->args['id'] ];
			}
		} elseif ( isset( $this->options[ $this->args['id'] ] ) ) {
			$val = $this->options[ $this->args['id'] ];
		}

		// Single metadata value, or array of values. If the $meta_type or $object_id parameters are invalid, false is returned. If the meta value isn't set, an empty string or array is returned, respectively.
		if ( 'metabox' === $this->_type && empty( $val ) && '0' !== $val ) {
			$val = isset( $this->args['default'] ) ? $this->args['default'] : '';
		}

		$val = $this->validate( $val );

		if ( $subkey ) {
			return isset( $val[ $subkey ] ) ? $val[ $subkey ] : '';
		}

		if ( isset( $val['{{index}}'] ) ) {
			unset( $val['{{index}}'] );
		}

		return $val;
	}

	/**
	 * Get field options array. For select or buttons set field type.
	 *
	 * @since 1.0.0
	 *
	 * @return array Field options array.
	 */
	public function get_field_options() {
		if ( ! isset( $this->args['options'] ) ) {
			return array();
		}

		return $this->args['options'];
	}

	/**
	 * Admin hooks.
	 *
	 * @since 1.0.0
	 */
	public function admin_hooks() {}

	/**
	 * Enqueue required scripts and styles for controls.
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {}

	/**
	 * Enqueue required scripts and styles on frontend.
	 *
	 * @since 1.0.0
	 */
	public function frontend_enqueue() {}

	/**
	 * Output field's css code on frontend based on the control and its value.
	 *
	 * @since 1.0.0
	 */
	public function css_output() {}

	/**
	 * Sanitize field and its value before saving.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Field value string.
	 *
	 * @return mixed Sanitization result.
	 */
	public function sanitize( $value ) {
		$sanitization = new Sanitize( $this, $value );

		return $sanitization->sanitize();
	}
}
