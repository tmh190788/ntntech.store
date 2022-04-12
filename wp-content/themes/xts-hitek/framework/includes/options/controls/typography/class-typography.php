<?php
/**
 * Typography settings for particular CSS selectors.
 *
 * @package xts
 */

namespace XTS\Options\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Field;
use XTS\Google_Fonts;
use XTS\Options\Presets;

/**
 * Class for typography settings control.
 */
class Typography extends Field {
	/**
	 * Default field value.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $_default_value = array( // phpcs:ignore
		'selector'        => array(),
		'google'          => false,
		'custom'          => false,
		'custom-selector' => '',
		'font-family'     => '',
		'font-weight'     => '',
		'font-variant'    => '',
		'font-style'      => '',
		'font-size'       => '',
		'line-height'     => '',
		'color'           => '',
		'font-subset'     => '',
		'tablet'          => array(
			'font-size'   => '',
			'line-height' => '',
		),
		'mobile'          => array(
			'font-size'   => '',
			'line-height' => '',
		),
		'hover'           => array(
			'color' => '',
		),
		'active'          => array(
			'color' => '',
		),
		'text-transform'  => '',
	);
	/**
	 * Google fonts array.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $google_fonts = array();

	/**
	 * Construct the object.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args    Field args array.
	 * @param array  $options Options from the database.
	 * @param string $type    Field type.
	 * @param string $object  Object.
	 */
	public function __construct( $args, $options, $type = 'options', $object = 'post' ) {
		parent::__construct( $args, $options, $type, $object );

		$this->args = wp_parse_args(
			$args,
			array(
				'font-size'      => true,
				'line-height'    => true,
				'text-transform' => true,
				'color'          => true,
				'color-hover'    => false,
				'selector'       => '',
			)
		);

		$this->google_fonts = Google_Fonts::$all_google_fonts;
	}

	/**
	 * Displays the field control HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function render_control() {
		$value = $this->get_field_value();
		// get last index from the array.
		$key = 0;
		if ( is_array( $value ) ) {
			end( $value );
			$key = key( $value );
		}

		echo '<div id="' . esc_attr( $this->get_id() ) . '" class="xts-advanced-typography-field ' . ( ( $this->_is_multiple_field() ) ? 'xts-multiple-typography' : 'xts-single-typography' ) . '" data-id="' . esc_attr( $this->get_id() ) . '" data-key="' . esc_attr( $key ) . '">';

		echo '<div class="xts-typography-sections">';

		if ( is_array( $value ) && count( $value ) > 0 && $this->_is_multiple_field() ) {
			foreach ( $value as $index => $value ) {
				$this->render_section( $index );
			}
		} else {
			$this->render_section( 0 );
		}

		echo '</div>';

		if ( $this->_is_multiple_field() ) {
			$this->section_template( false );
			echo '<div class="xts-typography-btn-add xts-inline-btn xts-inline-btn-add">Add rule</div>';
		}

		echo '</div>';
	}

	/**
	 * Check is it multiple typography field.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean.
	 */
	private function _is_multiple_field() { // phpcs:ignore
		return isset( $this->args['selectors'] );
	}

	/**
	 * Renders one typography settings section based on index.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $index Section index.
	 *
	 * @return void.
	 */
	public function render_section( $index ) {
		$default_value = $this->_default_value;
		$value         = $this->get_field_value();

		if ( '{{index}}' === $index ) {
			return;
		}

		if ( isset( $value[ $index ] ) ) {
			$section_value = wp_parse_args( $value[ $index ], $default_value );
		} else {
			$section_value = $default_value;
		}

		// Is selected font a Google font.
		$google = '0';

		if ( isset( $this->google_fonts[ $section_value['font-family'] ] ) ) {
			$google = '1';
		}

		$this->section_template(
			$index,
			array(
				'google' => $google,
				'value'  => $section_value,
			)
		);
	}

	/**
	 * Displays the section template.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $i    Section index.
	 * @param array   $data Section data.
	 *
	 * @return void.
	 */
	public function section_template( $i, $data = array() ) {
		$index = ( false === $i ) ? '{{index}}' : $i;

		extract( // phpcs:ignore
			wp_parse_args(
				$data,
				array(
					'google' => '0',
					'value'  => $this->_default_value,
				)
			)
		);

		?>
		<div class="xts-typography-section <?php echo( ( false === $i ) ? 'xts-typography-template hide' : '' ); ?>" data-id="<?php echo esc_attr( $this->get_id() ); ?>-<?php echo esc_attr( $index ); ?>">
			<div class="xts-row xts-row-spacing-20">
				<input type="hidden" class="xts-typography-custom-input" name="<?php echo esc_attr( $this->get_input_name( $index, 'custom' ) ); ?>" value="<?php echo esc_attr( $value['custom'] ); ?>" />

				<div class="xts-typography-font-container xts-col">
					<div class="xts-row xts-row-spacing-20">
						<?php if ( $this->_is_multiple_field() ) : ?>
							<div class="xts-typography-selector-container xts-col">
								<select class="xts-typography-selector" name="<?php echo esc_attr( $this->get_input_name( $index, 'selector', '' ) ); ?>" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Assigned to elements', 'xts-theme' ); ?>">
									<?php
									$group = false;
									foreach ( $this->args['selectors'] as $id => $selector ) {
										if ( ! is_array( $selector ) ) {
											continue;
										}

										if ( ! isset( $selector['selector'] ) ) {
											if ( $group ) {
												echo '</optgroup>';
											}
											echo '<optgroup label="' . esc_attr( $selector['title'] ) . '">';
											$group = true;
											continue;
										}

										$selected = in_array( $id, $value['selector'], true ) ? ' selected="selected" ' : '';
										echo '<option value="' . esc_attr( $id ) . '" ' . $selected . '>'; // phpcs:ignore
										echo esc_html( $selector['title'] );
										echo '</option>';
									}
									if ( $group ) {
										echo '</optgroup>';
									}
									?>
								</select>
								<input type="text" placeholder="For ex.: .my-custom-class" class="xts-typography-custom-selector<?php echo( ( ! $value['custom'] ) ? ' hide' : '' ); ?>" name="<?php echo esc_attr( $this->get_input_name( $index, 'custom-selector' ) ); ?>" value="<?php echo esc_attr( $value['custom-selector'] ); ?>" />
							</div>
						<?php endif; ?>

						<div class="xts-typography-font-family-container xts-col xts-col-6">
							<input type="hidden" class="xts-typography-google-input" name="<?php echo esc_attr( $this->get_input_name( $index, 'google' ) ); ?>" value="<?php echo esc_attr( $google ); ?>">

							<input type="hidden" class="xts-typography-family-input" name="<?php echo esc_attr( $this->get_input_name( $index, 'font-family' ) ); ?>" value="<?php echo esc_attr( $value['font-family'] ); ?>" />

							<select class="xts-typography-family select2-container <?php echo ! $value['font-family'] ? ' xts-default-value' : ''; ?>" data-placeholder="<?php esc_attr_e( 'Font family', 'xts-theme' ); ?>" data-value="<?php echo esc_attr( $value['font-family'] ); ?>">
								<?php if ( $value['font-family'] ) : ?>
									<option>
										<?php echo esc_html( $value['font-family'] ); ?>
									</option>
								<?php else : ?>
									<option disabled selected>
										<?php esc_attr_e( 'Font family', 'xts-theme' ); ?>
									</option>
								<?php endif; ?>
							</select>
						</div>


						<div class="xts-typography-style-container xts-col xts-col-6" original-title="<?php esc_attr_e( 'Font style', 'xts-theme' ); ?>">

							<?php $style = $value['font-weight'] . $value['font-style']; ?>

							<input type="hidden" class="xts-typography-weight-input" name="<?php echo esc_attr( $this->get_input_name( $index, 'font-weight' ) ); ?>" value="<?php echo esc_attr( $value['font-weight'] ); ?>" />

							<input type="hidden" class="xts-typography-style-input" name="<?php echo esc_attr( $this->get_input_name( $index, 'font-style' ) ); ?>" />

							<select data-placeholder="<?php esc_attr_e( 'Style', 'xts-theme' ); ?>" class="xts-typography-style" original-title="<?php esc_attr_e( 'Font style', 'xts-theme' ); ?>" data-value="<?php echo esc_attr( $style ); ?>">
							</select>

						</div>

						<div class="select_wrapper xts-typography-subsets-container xts-col xts-col-6" original-title="<?php esc_attr_e( 'Font subsets', 'xts-theme' ); ?>">

							<input type="hidden" class="xts-typography-subset-input" name="<?php echo esc_attr( $this->get_input_name( $index, 'font-subset' ) ); ?>" />
							<select data-placeholder="<?php esc_attr_e( 'Subset', 'xts-theme' ); ?>" class="xts-typography-subset" original-title="<?php esc_attr_e( 'Font subset', 'xts-theme' ); ?>" data-value="<?php echo esc_attr( $value['font-subset'] ); ?>">
							</select>
						</div>

						<?php if ( $this->args['text-transform'] ) : ?>
							<div class="xts-typography-extra-container xts-col xts-col-6">
								<select class="xts-typography-transform" data-placeholder="<?php esc_attr_e( 'Text transform', 'xts-theme' ); ?>" name="<?php echo esc_attr( $this->get_input_name( $index, 'text-transform' ) ); ?>">
									<option value=""></option>
									<option value="capitalize" <?php selected( $value['text-transform'], 'capitalize' ); ?>>
										Capitalize
									</option>
									<option value="lowercase" <?php selected( $value['text-transform'], 'lowercase' ); ?>>
										Lowercase
									</option>
									<option value="uppercase" <?php selected( $value['text-transform'], 'uppercase' ); ?>>
										Uppercase
									</option>
									<option value="none" <?php selected( $value['text-transform'], 'none' ); ?>>None
									</option>
									<option value="inherit" <?php selected( $value['text-transform'], 'inherit' ); ?>>
										Inherit
									</option>
								</select>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( $this->args['font-size'] ) : ?>
					<div class="xts-typography-size-container xts-col xts-col-6 xts-typography-responsive-controls">
						<div class="xts-typography-size-point xts-typography-control-desktop">
							<label>Font size</label>
							<div class="input-append">
								<input type="number" name="<?php echo esc_attr( $this->get_input_name( $index, 'font-size' ) ); ?>" value="<?php echo esc_attr( $value['font-size'] ); ?>" /><span class="add-on">px</span>
							</div>
						</div>
						<div class="xts-typography-responsive-opener" title="Responsive controls"></div>
						<div class="xts-typography-size-point xts-typography-control-tablet <?php echo( ! empty( $value['tablet']['font-size'] ) ? 'show' : 'hide' ); ?>">
							<label>Tablet</label>
							<div class="input-append">
								<input type="number" name="<?php echo esc_attr( $this->get_input_name( $index, 'tablet', 'font-size' ) ); ?>" value="<?php echo esc_attr( $value['tablet']['font-size'] ); ?>" /><span class="add-on">px</span>
							</div>
						</div>
						<div class="xts-typography-size-point xts-typography-control-mobile <?php echo( ! empty( $value['tablet']['font-size'] ) ? 'show' : 'hide' ); ?>">
							<label>Mobile</label>
							<div class="input-append">
								<input type="number" name="<?php echo esc_attr( $this->get_input_name( $index, 'mobile', 'font-size' ) ); ?>" value="<?php echo esc_attr( $value['mobile']['font-size'] ); ?>" /><span class="add-on">px</span>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $this->args['line-height'] ) : ?>
					<div class="xts-typography-height-container xts-col xts-col-6 xts-typography-responsive-controls">
						<div class="xts-typography-height-point xts-typography-control-desktop">
							<label>Line height</label>
							<div class="input-append">
								<input type="number" step="0.01" name="<?php echo esc_attr( $this->get_input_name( $index, 'line-height' ) ); ?>" value="<?php echo esc_attr( $value['line-height'] ); ?>" /><span class="add-on">em</span>
							</div>
						</div>
						<div class="xts-typography-responsive-opener" title="Responsive controls"></div>
						<div class="xts-typography-height-point xts-typography-control-tablet <?php echo ( ! empty( $value['tablet']['line-height'] ) ) ? 'show' : 'hide'; ?>">
							<label>Tablet</label>
							<div class="input-append">
								<input type="number" step="0.01" name="<?php echo esc_attr( $this->get_input_name( $index, 'tablet', 'line-height' ) ); ?>" value="<?php echo esc_attr( $value['tablet']['line-height'] ); ?>" /><span class="add-on">em</span>
							</div>
						</div>
						<div class="xts-typography-height-point xts-typography-control-mobile <?php echo ( ! empty( $value['tablet']['line-height'] ) ) ? 'show' : 'hide'; ?>">
							<label>Mobile</label>
							<div class="input-append">
								<input type="number" step="0.01" name="<?php echo esc_attr( $this->get_input_name( $index, 'mobile', 'line-height' ) ); ?>" value="<?php echo esc_attr( $value['mobile']['line-height'] ); ?>" /><span class="add-on">em</span>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ( isset( $this->args['color'] ) && $this->args['color'] ) || ( isset( $this->args['color-hover'] ) && $this->args['color-hover'] ) || ( isset( $this->args['color-active'] ) && $this->args['color-active'] ) ) : ?>
					<div class="xts-typography-color-container xts-col">
						<div class="xts-row xts-row-spacing-20">
							<?php if ( isset( $this->args['color'] ) && $this->args['color'] ) : ?>
								<div class="xts-typography-color-default-container xts-col xts-col-6">
									<div class="xts-typography-color-point">
										<label>Color</label>
										<input type="text" placeholder="<?php __( 'Color', 'xts-theme' ); ?>" data-alpha="<?php echo isset( $this->args['alpha'] ) ? esc_attr( $this->args['alpha'] ) : 'true'; ?>" name="<?php echo esc_attr( $this->get_input_name( $index, 'color' ) ); ?>" value="<?php echo esc_attr( $value['color'] ); ?>" class="xts-typography-color" />
									</div>
								</div>
							<?php endif; ?>
							<?php if ( isset( $this->args['color-hover'] ) && $this->args['color-hover'] ) : ?>
								<div class="xts-typography-color-hover-container xts-col xts-col-6">
									<div class="xts-typography-color-point">
										<label>Color on hover</label>
										<input type="text" placeholder="<?php __( 'Color', 'xts-theme' ); ?>" data-alpha="<?php echo isset( $this->args['alpha'] ) ? esc_attr( $this->args['alpha'] ) : 'true'; ?>" name="<?php echo esc_attr( $this->get_input_name( $index, 'hover', 'color' ) ); ?>" value="<?php echo esc_attr( $value['hover']['color'] ); ?>" class="xts-typography-color-hover" />
									</div>
								</div>
							<?php endif; ?>
							<?php if ( isset( $this->args['color-active'] ) && $this->args['color-active'] ) : ?>
								<div class="xts-typography-color-active-container xts-col xts-col-6">
									<div class="xts-typography-color-point">
										<label>Active item color</label>
										<input type="text" placeholder="<?php __( 'Color', 'xts-theme' ); ?>" data-alpha="<?php echo isset( $this->args['alpha'] ) ? esc_attr( $this->args['alpha'] ) : 'true'; ?>" name="<?php echo esc_attr( $this->get_input_name( $index, 'active', 'color' ) ); ?>" value="<?php echo esc_attr( $value['active']['color'] ); ?>" class="xts-typography-color-active" />
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="xts-typography-preview-container xts-col">
					<div class="xts-typography-preview">1 2 3 4 5 6 7 8 9 0 A B C D E F G H I J K L M N O P Q R S T U V
						W X Y Z a b c d e f g h i j k l m n o p q r s t u v w x y z
					</div>
				</div>
				<?php if ( $this->_is_multiple_field() ) : ?>
					<div class="xts-typography-btn-remove-container xts-col">
						<div class="xts-typography-btn-remove xts-inline-btn xts-inline-btn-remove">Remove</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php
	}

	/**
	 * Enqueue js libs.
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker-alpha', XTS_FRAMEWORK_URL . '/assets/js-libs/wp-color-picker-alpha.js', array( 'wp-color-picker' ), XTS_VERSION, true );
		wp_enqueue_script( 'select2' );
	}

	/**
	 * Frontend enqueue.
	 *
	 * @since 1.0.0
	 */
	public function frontend_enqueue() {
		$active_presets = Presets::get_active_presets();
		$this->set_presets( $active_presets );
		$this->add_google_fonts();
	}

	/**
	 * Add google fonts.
	 *
	 * @since 1.0.0
	 */
	public function add_google_fonts() {
		if ( empty( $this->get_field_value() ) ) {
			return;
		}

		$value = $this->get_field_value();

		foreach ( $value as $i => $typography ) {
			if ( ! isset( $typography['selector'] ) && $this->_is_multiple_field() ) {
				continue;
			}

			Google_Fonts::add_google_font( $typography );
		}
	}

	/**
	 * Output field's css code based on the settings.
	 *
	 * @since 1.0.0
	 *
	 * @return string $output Generated CSS code.
	 */
	public function css_output() {
		if ( empty( $this->get_field_value() ) ) {
			return '';
		}

		$value = $this->get_field_value();

		$output = '';

		foreach ( $value as $i => $typography ) {
			if ( ! isset( $typography['selector'] ) && $this->_is_multiple_field() ) {
				continue;
			}

			if ( $this->_is_multiple_field() ) {
				$selector              = $this->_combine_selectors( $typography['selector'], 'selector', $typography['custom-selector'] );
				$selector_color        = $this->_combine_selectors( $typography['selector'], 'selector-color', $typography['custom-selector'] );
				$selector_color_hover  = $this->_combine_selectors( $typography['selector'], 'selector-color-hover', $typography['custom-selector'], 'hover' );
				$selector_color_active = $this->_combine_selectors( $typography['selector'], 'selector-color-active', $typography['custom-selector'], 'active' );
				$selector_font_family  = $this->_combine_selectors( $typography['selector'], 'selector-font-family', $typography['custom-selector'] );
				$selector_font_size    = $this->_combine_selectors( $typography['selector'], 'selector-font-size', $typography['custom-selector'] );
			} else {
				$selector              = isset( $this->args['selector'] ) ? $this->args['selector'] : '';
				$selector_color        = isset( $this->args['selector-color'] ) ? $this->args['selector-color'] : '';
				$selector_color_hover  = isset( $this->args['selector-color-hover'] ) ? $this->args['selector-color-hover'] : '';
				$selector_color_active = isset( $this->args['selector-color-active'] ) ? $this->args['selector-color-active'] : '';
				$selector_font_family  = isset( $this->args['selector-font-family'] ) ? $this->args['selector-font-family'] : '';
				$selector_font_size    = isset( $this->args['selector-font-size'] ) ? $this->args['selector-font-size'] : '';
			}

			if ( $selector ) {
				$output .= $this->_generate_css_code( $selector, $typography );
			}

			// Special selector for color.
			if ( $selector_color && ( $selector_color !== $selector && 'custom' !== $selector ) && isset( $typography['color'] ) ) {
				$output .= $this->_generate_css_code( $selector_color, array( 'color' => $typography['color'] ) );
			}

			if ( $selector_color_hover && ( $selector_color_hover !== $selector && 'custom' !== $selector ) && isset( $typography['hover'] ) ) {
				$output .= $this->_generate_css_code( $selector_color_hover, array( 'color' => $typography['hover']['color'] ) );
			}

			if ( $selector_color_active && ( $selector_color_active !== $selector && 'custom' !== $selector ) && isset( $typography['active'] ) ) {
				$output .= $this->_generate_css_code( $selector_color_active, array( 'color' => $typography['active']['color'] ) );
			}

			// Special selector for font family.
			if ( $selector_font_family && ( $selector_font_family !== $selector && 'custom' !== $selector ) && isset( $typography['font-family'] ) && isset( $typography['font-weight'] ) && isset( $typography['font-style'] ) ) {
				$output .= $this->_generate_css_code(
					$selector_font_family,
					array(
						'font-family' => $typography['font-family'],
						'font-weight' => $typography['font-weight'],
						'font-style'  => $typography['font-style'],
					)
				);
			}

			// Special selector for font size.
			if ( $selector_font_size && ( $selector_font_size !== $selector && 'custom' !== $selector ) && isset( $typography['font-size'] ) ) {
				$output .= $this->_generate_css_code(
					$selector_font_size,
					array(
						'font-size'   => $typography['font-size'],
						'line-height' => $typography['line-height'],
					)
				);
			}

			if ( $selector_font_size && isset( $typography['tablet'] ) && isset( $typography['tablet']['font-size'] ) ) {
				$css_tablet = $this->_generate_css_code(
					$selector_font_size,
					array(
						'font-size'   => $typography['tablet']['font-size'],
						'line-height' => $typography['tablet']['line-height'],
					)
				);
				$output    .= $this->_get_css_media_query( '@media (max-width: 1024px)', $css_tablet );
			}

			if ( $selector_font_size && isset( $typography['mobile'] ) && isset( $typography['mobile']['font-size'] ) ) {
				$css_mobile = $this->_generate_css_code(
					$selector_font_size,
					array(
						'font-size'   => $typography['mobile']['font-size'],
						'line-height' => $typography['mobile']['line-height'],
					)
				);
				$output    .= $this->_get_css_media_query( '@media (max-width: 767px)', $css_mobile );
			}

			Google_Fonts::add_google_font( $typography );
		}

		return $output;
	}

	/**
	 * Output CSS code inside the media query.
	 *
	 * @since 1.0.0
	 *
	 * @param string $query CSS query.
	 * @param string $css   The code.
	 *
	 * @return  string $output Generated CSS code.
	 */
	private function _get_css_media_query( $query, $css ) { // phpcs:ignore
		if ( empty( $css ) ) {
			return '';
		}

		$code  = $query . '{' . "\n";
		$code .= "\t" . $css . "\n";
		$code .= '}' . "\n\n";

		return $code;
	}

	/**
	 * Generate CSS code based on rules array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $selector CSS selector.
	 * @param array  $rules    CSS rules array.
	 *
	 * @return  string $output Generated CSS code.
	 */
	private function _generate_css_code( $selector, $rules ) { // phpcs:ignore
		$css_rules  = $this->_get_css_rule( 'font-family', $rules );
		$css_rules .= $this->_get_css_rule( 'font-weight', $rules );
		$css_rules .= $this->_get_css_rule( 'font-style', $rules );
		$css_rules .= $this->_get_css_rule( 'font-size', $rules );
		$css_rules .= $this->_get_css_rule( 'line-height', $rules );
		$css_rules .= $this->_get_css_rule( 'text-transform', $rules );
		$css_rules .= $this->_get_css_rule( 'color', $rules );

		if ( empty( $css_rules ) ) {
			return '';
		}

		$css  = $selector;
		$css .= '{' . "\n";
		$css .= "\t" . $css_rules . "\n";
		$css .= '}' . "\n\n";

		return $css;
	}

	/**
	 * Generate a single CSS rule.
	 *
	 * @since 1.0.0
	 *
	 * @param string $rule        CSS rule.
	 * @param array  $rules_array CSS rules array.
	 *
	 * @return  string $output Generated CSS code for this rule.
	 */
	private function _get_css_rule( $rule, $rules_array ) { // phpcs:ignore
		if ( ! isset( $rules_array[ $rule ] ) || empty( $rules_array[ $rule ] ) ) {
			return '';
		}

		$suffix    = '';
		$std_fonts = xts_get_config( 'standard-fonts' );

		if ( in_array( $rule, array( 'font-size' ), true ) ) {
			$suffix = 'px';
		}

		if ( in_array( $rule, array( 'font-family' ), true ) ) {
			if ( in_array( $rules_array[ $rule ], $std_fonts ) ) { // phpcs:ignore
				return $rule . ': ' . $rules_array[ $rule ] . ';';
			}

			$suffix = apply_filters( 'xts_backup_font', ', Arial, Helvetica, sans-serif' );

			return $rule . ': "' . $rules_array[ $rule ] . '"' . $suffix . ';';
		}

		return $rule . ': ' . $rules_array[ $rule ] . $suffix . ';';
	}

	/**
	 * Generate a complex selector based on selectors array, state and custom.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $selector_ids CSS rule.
	 * @param string $selector_key Selector key.
	 * @param string $custom       Custom CSS selector.
	 * @param string $state        CSS state.
	 *
	 * @return mixed $string Generated CSS selector.
	 */
	private function _combine_selectors( $selector_ids, $selector_key = '', $custom = '', $state = '' ) { // phpcs:ignore
		if ( ! is_array( $selector_ids ) ) {
			return $selector_ids;
		}

		$delimiter = ', ';
		$selector  = array();

		foreach ( $selector_ids as $i => $id ) {
			if ( ! isset( $this->args['selectors'][ $id ] ) ) {
				continue;
			}

			$current_selector = $this->args['selectors'][ $id ]['selector'];

			if ( 'custom' === $id ) {
				$current_selector = $custom;
			}

			if ( isset( $this->args['selectors'][ $id ][ $selector_key ] ) && 'custom' !== $id ) {
				$current_selector = $this->args['selectors'][ $id ][ $selector_key ];
			} elseif ( ( 'selector-color-hover' === $selector_key && 'hover' === $state ) || ( 'selector-color-active' === $selector_key && 'active' === $state ) ) {
				$current_selector .= ':' . $state;
			}

			$selector[ $i ] = $current_selector;
		}

		return implode( $delimiter, $selector );
	}
}
