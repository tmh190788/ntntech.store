<?php // phpcs:disable

namespace XTS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Widgets Helper Class
 */
class Widget_Base extends \WP_Widget {
	/**
	 * Create Widget
	 *
	 * Creates a new widget and sets it's labels, description, fields and options
	 *
	 * @access   public
	 * @since    1.0
	 *
	 * @param array
	 *
	 * @return   void
	 */
	function create_widget( $args ) {
		// settings some defaults
		$defaults = array(
			'label'       => '',
			'description' => '',
			'fields'      => array(),
			'options'     => array(),
			'slug'        => '',
		);

		// parse and merge args with defaults
		$args = wp_parse_args( $args, $defaults );

		// extract each arg to its own variable
		extract( $args, EXTR_SKIP );

		// set the widget vars
		$this->slug   = ( $slug ) ? $slug : sanitize_title( $label );
		$this->fields = $fields;

		// check options
		$this->options = array(
			'classname'   => $this->slug,
			'description' => $description,
		);
		if ( ! empty( $options ) ) {
			$this->options = array_merge( $this->options, $options );
		}

		// call WP_Widget to create the widget
		parent::__construct( $this->slug, $label, $this->options );
	}

	/**
	 * Form
	 *
	 * Creates the settings form.
	 *
	 * @access   private
	 * @since    1.0
	 *
	 * @param array
	 *
	 * @return   void
	 */
	function form( $instance ) {
		$this->instance = $instance;
		$form           = $this->create_fields();

		echo apply_filters( 'xts_widget_base_form_output', $form );
	}

	/**
	 * Update Fields
	 *
	 * @access   private
	 * @since    1.0
	 *
	 * @param array
	 * @param array
	 *
	 * @return   array
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$this->before_update_fields();

		foreach ( $this->fields as $key ) {
			$slug = ( isset( $key['id'] ) ) ? $key['id'] : $key['param_name'];

			if ( isset( $key['validate'] ) ) {
				if ( false === $this->validate( $key['validate'], $new_instance[ $slug ] ) ) {
					return $instance;
				}
			}

			if ( isset( $key['filter'] ) ) {
				$instance[ $slug ] = $this->filter( $key['filter'], $new_instance[ $slug ] );
			} else {
				if ( 'checkbox' === $key['type'] ) {
					$instance[ $slug ] = isset( $new_instance[ $slug ] ) ? $new_instance[ $slug ] : 0;
				} else {
					if ( is_array( $new_instance[ $slug ] ) ) {
						$instance[ $slug ] = $new_instance[ $slug ];
					} else {
						$instance[ $slug ] = strip_tags( $new_instance[ $slug ] );
					}
				}
			}
		}

		return $this->after_validate_fields( $instance );
	}

	/**
	 * Before Validate Fields
	 *
	 * Allows to hook code on the update.
	 *
	 * @access   public
	 * @since    1.6
	 *
	 * @param string
	 *
	 * @return   string
	 */
	function before_update_fields() {
		return;
	}

	/**
	 * After Validate Fields
	 *
	 * Allows to modify the output after validating the fields.
	 *
	 * @access   public
	 * @since    1.6
	 *
	 * @param array
	 *
	 * @return   array
	 */
	function after_validate_fields( $instance = '' ) {
		return $instance;
	}

	/**
	 * Validate
	 *
	 * @access   private
	 * @since    1.0
	 *
	 * @param string
	 * @param string
	 *
	 * @return   boolean
	 */
	function validate( $rules, $value ) {
		$rules = explode( '|', $rules );

		if ( empty( $rules ) || count( $rules ) < 1 ) {
			return true;
		}

		foreach ( $rules as $rule ) {
			if ( false === $this->do_validation( $rule, $value ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Filter
	 *
	 * @access   private
	 * @since    1.0
	 *
	 * @param string
	 * @param string
	 *
	 * @return   void
	 */
	function filter( $filters, $value ) {
		$filters = explode( '|', $filters );

		if ( empty( $filters ) || count( $filters ) < 1 ) {
			return $value;
		}

		foreach ( $filters as $filter ) {
			$value = $this->do_filter( $filter, $value );
		}

		return $value;
	}

	/**
	 * Do Validation Rule
	 *
	 * @access   private
	 * @since    1.0
	 *
	 * @param string
	 * @param string
	 *
	 * @return   boolean
	 */
	function do_validation( $rule, $value = '' ) {
		switch ( $rule ) {
			case 'alpha':
				return ctype_alpha( $value );
				break;

			case 'alpha_numeric':
				return ctype_alnum( $value );
				break;

			case 'alpha_dash':
				return preg_match( '/^[a-z0-9-_]+$/', $value );
				break;

			case 'numeric':
				return ctype_digit( $value );
				break;

			case 'integer':
				return (bool) preg_match( '/^[\-+]?[0-9]+$/', $value );
				break;

			case 'boolean':
				return is_bool( $value );
				break;

			case 'email':
				return is_email( $value );
				break;

			case 'decimal':
				return (bool) preg_match( '/^[\-+]?[0-9]+\.[0-9]+$/', $value );
				break;

			case 'natural':
				return (bool) preg_match( '/^[0-9]+$/', $value );

				return;

			case 'natural_not_zero':
				if ( ! preg_match( '/^[0-9]+$/', $value ) ) {
					return false;
				}
				if ( $value == 0 ) {
					return false;
				}

				return true;

				return;

			default:
				if ( method_exists( $this, $rule ) ) {
					return $this->$rule( $value );
				} else {
					return false;
				}
				break;
		}
	}

	/**
	 * Do Filter
	 *
	 * @access   private
	 * @since    1.0
	 *
	 * @param string
	 * @param string
	 *
	 * @return   boolean
	 */
	function do_filter( $filter, $value = '' ) {
		switch ( $filter ) {
			case 'strip_tags':
				return strip_tags( $value );
				break;

			case 'wp_strip_all_tags':
				return wp_strip_all_tags( $value );
				break;

			case 'esc_attr':
				return esc_attr( $value );
				break;

			case 'esc_url':
				return esc_url( $value );
				break;

			case 'esc_textarea':
				return esc_textarea( $value );
				break;

			default:
				if ( method_exists( $this, $filter ) ) {
					return $this->$filter( $value );
				} else {
					return $value;
				}
				break;
		}
	}

	/**
	 * Create Fields
	 *
	 * Creates each field defined.
	 *
	 * @access   private
	 * @since    1.0
	 *
	 * @param string
	 *
	 * @return   string
	 */
	function create_fields( $out = '' ) {
		$out = $this->before_create_fields( $out );

		if ( ! empty( $this->fields ) ) {
			foreach ( $this->fields as $key ) {
				if ( empty( $key ) ) {
					continue;
				}
				$out .= $this->create_field( $key );
			}
		}

		$out = $this->after_create_fields( $out );

		return $out;
	}

	/**
	 * Before Create Fields
	 *
	 * Allows to modify code before creating the fields.
	 *
	 * @access   public
	 * @since    1.0
	 *
	 * @param string
	 *
	 * @return   string
	 */
	function before_create_fields( $out = '' ) {
		return $out;
	}

	/**
	 * After Create Fields
	 *
	 * Allows to modify code after creating the fields.
	 *
	 * @access   public
	 * @since    1.0
	 *
	 * @param string
	 *
	 * @return   string
	 */
	function after_create_fields( $out = '' ) {
		return $out;
	}

	/**
	 * Create Fields
	 *
	 * @access   private
	 * @since    1.0
	 *
	 * @param string
	 * @param string
	 *
	 * @return   string
	 */
	function create_field( $key, $out = '' ) {
		/* Set Defaults */
		$key['std'] = isset( $key['std'] ) ? $key['std'] : '';
		$key['std'] = isset( $key['default'] ) ? $key['default'] : '';

		$slug        = ( isset( $key['id'] ) ) ? $key['id'] : $key['param_name'];
		$heading     = isset( $key['heading'] ) ? $key['heading'] : '';
		$key['name'] = ( isset( $key['name'] ) ) ? $key['name'] : $heading;

		if ( isset( $key['skip_in'] ) && $key['skip_in'] == 'widget' ) {
			return;
		}
		if ( isset( $key['value'] ) ) {
			$key['fields'] = $key['value'];
		}

		if ( isset( $key['true_state'] ) && isset( $key['false_state'] ) ) {
			$key['fields'] = array(
				$key['true_state']  => $key['true_state'],
				$key['false_state'] => $key['false_state'],
			);
		}

		if ( ! isset( $key['class'] ) ) {
			$key['class'] = 'widefat';
		}

		if ( isset( $key['description'] ) ) {
			$key['desc'] = $key['description'];
		}

		if ( isset( $this->instance[ $slug ] ) ) {
			$key['value'] = empty( $this->instance[ $slug ] ) ? '' : $this->instance[ $slug ];
		} else {
			unset( $key['value'] );
		}

		/* Set field id and name  */
		$key['_id']   = $this->get_field_id( $slug );
		$key['_name'] = $this->get_field_name( $slug );

		/* Set field type */
		if ( ! isset( $key['type'] ) ) {
			$key['type'] = 'text';
		}

		/* Prefix method */
		$field_method = 'create_field_' . str_replace( '-', '_', $key['type'] );

		/* Check for <p> Class */
		$p = ( isset( $key['class-p'] ) ) ? '<p class="' . $key['class-p'] . '">' : '<p>';

		/* Run method */
		if ( method_exists( $this, $field_method ) ) {
			return $p . $this->$field_method( $key ) . '</p>';
		}
	}

	/**
	 * select2 field
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param array
	 *
	 * @return   string
	 */

	function create_field_select2( $key, $out = '' ) {
		$value = isset( $key['value'] ) && $key['value'] ? $key['value'] : $key['std'];

		if ( ! is_array( $value ) ) {
			$value = explode( ',', $value );
		}

		ob_start();

		?>
		<?php echo wp_kses( $this->create_field_label( $key['name'], $key['_id'] ), true ); ?>

		<select name="<?php echo esc_attr( $key['_name'] ); ?>[]" id="<?php echo esc_attr( $key['_id'] ); ?>" class="xts-select xts-select2" multiple>
			<?php foreach ( $key['fields'] as $field => $option ) : ?>
				<?php
				$selected = false;

				if ( is_array( $value ) && in_array( $option, $value, false ) ) {
					$selected = true;
				}

				?>

				<option value="<?php echo esc_attr( $option ); ?>" <?php selected( true, $selected ); ?>>
					<?php echo esc_html( $field ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php

		return ob_get_clean();
	}

	/**
	 * dropdown field
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param array
	 *
	 * @return   string
	 */
	function create_field_dropdown( $key, $out = '' ) {
		$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

		$out .= '<select id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" ';

		if ( isset( $key['class'] ) ) {
			$out .= 'class="' . esc_attr( $key['class'] ) . '" ';
		}

		$out .= '> ';

		$selected = isset( $key['value'] ) ? $key['value'] : $key['std'];

		foreach ( $key['fields'] as $field => $option ) {
			$out .= '<option value="' . esc_attr( $option ) . '" ';

			if ( esc_attr( $selected ) == $option ) {
				$out .= ' selected="selected" ';
			}

			$out .= '> ' . esc_html( $field ) . '</option>';
		}

		$out .= ' </select> ';

		if ( isset( $key['desc'] ) ) {
			$out .= '<br/><small class="description">' . esc_html( $key['desc'] ) . '</small>';
		}

		return $out;
	}

	/**
	 * Attach image field
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param array
	 *
	 * @return   string
	 */
	function create_field_attach_image( $key, $out = '' ) {
		$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

		$url = '';

		if ( isset( $value ) ) {
			$url = wp_get_attachment_url( $value );
		}

		ob_start();

		$remove_btn_classes = $url ? ' xts-active' : '';

		?>
		<div class="xts-upload-control">
			<h4>
				<?php echo wp_kses( $this->create_field_label( $key['name'], $key['_id'] ), true ); ?>
			</h4>

			<div class="xts-upload-preview">
				<?php if ( $url ) : ?>
					<img src="<?php echo esc_url( $url ); ?>">
				<?php endif; ?>
			</div>

			<div class="xts-upload-btns">
				<button class="xts-btn xts-upload-btn">
					<?php esc_html_e( 'Upload', 'xts-theme' ); ?>
				</button>

				<button class="xts-btn xts-btn-disable xts-btn-remove<?php echo esc_attr( $remove_btn_classes ); ?>">
					<?php esc_html_e( 'Remove', 'xts-theme' ); ?>
				</button>

				<input type="hidden" class="xts-upload-input-id" name="<?php echo esc_attr( $key['_name'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
			</div>

			<?php if ( isset( $key['desc'] ) ) : ?>
				<small class="description">
					<?php echo esc_html( $key['desc'] ); ?>
				</small>
			<?php endif; ?>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Attach images field
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param array
	 *
	 * @return   string
	 */
	function create_field_attach_images( $key, $out = '' ) {
		$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

		$ids = explode( ',', $value );

		ob_start();

		?>
		<div class="xts-upload_list-control">
			<h4>
				<?php echo wp_kses( $this->create_field_label( $key['name'], $key['_id'] ), true ); ?>
			</h4>

			<div class="xts-upload-preview">
				<?php foreach ( $ids as $id ) : ?>
					<?php if ( $id ) : ?>
						<div data-attachment_id="<?php echo esc_attr( $id ); ?>">
							<?php echo wp_get_attachment_image( $id, 'thumbnail' ); // phpcs:ignore ?>
							<a href="#" class="xts-remove">
								<span class="dashicons dashicons-dismiss"></span>
							</a>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>

			<div class="xts-upload-btns">
				<button class="xts-btn xts-upload-btn">
					<?php esc_html_e( 'Upload', 'xts-theme' ); ?>
				</button>

				<button class="xts-btn xts-btn-remove xts-btn-disable">
					<?php esc_html_e( 'Clear all', 'xts-theme' ); ?>
				</button>

				<input type="hidden" class="xts-upload-input-id" name="<?php echo esc_attr( $key['_name'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
			</div>

			<?php if ( isset( $key['desc'] ) ) : ?>
				<small class="description">
					<?php echo esc_html( $key['desc'] ); ?>
				</small>
			<?php endif; ?>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Field Text
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param array
	 *
	 * @return   string
	 */
	function create_field_text( $key, $out = '' ) {
		$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

		$out .= '<input type="text" ';

		if ( isset( $key['class'] ) ) {
			$out .= 'class="' . esc_attr( $key['class'] ) . '" ';
		}

		$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

		$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" value="' . esc_attr( $value ) . '" ';

		if ( isset( $key['size'] ) ) {
			$out .= 'size="' . esc_attr( $key['size'] ) . '" ';
		}

		$out .= ' />';

		if ( isset( $key['desc'] ) ) {
			$out .= '<br/><small class="description">' . esc_html( $key['desc'] ) . '</small>';
		}

		return $out;
	}

	/**
	 * Field Textarea
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param array
	 *
	 * @return   string
	 */
	function create_field_textarea( $key, $out = '' ) {
		$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

		$out .= '<textarea ';

		if ( isset( $key['class'] ) ) {
			$out .= 'class="' . esc_attr( $key['class'] ) . '" ';
		}

		if ( isset( $key['rows'] ) ) {
			$out .= 'rows="' . esc_attr( $key['rows'] ) . '" ';
		}

		if ( isset( $key['cols'] ) ) {
			$out .= 'cols="' . esc_attr( $key['cols'] ) . '" ';
		}

		$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

		$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '">' . esc_html( $value );

		$out .= '</textarea>';

		if ( isset( $key['desc'] ) ) {
			$out .= '<br/><small class="description">' . esc_html( $key['desc'] ) . '</small>';
		}

		return $out;
	}

	/**
	 * Field Checkbox
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param array
	 *
	 * @return   string
	 */
	function create_field_checkbox( $key, $out = '' ) {
		$out .= $this->create_field_label( $key['name'], $key['_id'] );

		$out .= ' <input type="checkbox" ';

		if ( isset( $key['class'] ) ) {
			$out .= 'class="' . esc_attr( $key['class'] ) . '" ';
		}

		$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" value="1" ';

		if ( ( isset( $key['value'] ) && $key['value'] == 1 ) or ( ! isset( $key['value'] ) && $key['std'] == 1 ) ) {
			$out .= ' checked="checked" ';
		}

		$out .= ' /> ';

		if ( isset( $key['desc'] ) ) {
			$out .= '<br/><small class="description">' . esc_html( $key['desc'] ) . '</small>';
		}

		return $out;
	}

	/**
	 * Field Select
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param array
	 *
	 * @return   string
	 */
	function create_field_select( $key, $out = '' ) {
		$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

		$out .= '<select id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" ';

		if ( isset( $key['class'] ) ) {
			$out .= 'class="' . esc_attr( $key['class'] ) . '" ';
		}

		$out .= '> ';

		$selected = isset( $key['value'] ) ? $key['value'] : $key['std'];

		foreach ( $key['fields'] as $field => $option ) {
			$out .= '<option value="' . esc_attr( $option['value'] ) . '" ';

			if ( esc_attr( $selected ) == $option['value'] ) {
				$out .= ' selected="selected" ';
			}

			$out .= '> ' . esc_html( $option['name'] ) . '</option>';
		}

		$out .= ' </select> ';

		if ( isset( $key['desc'] ) ) {
			$out .= '<br/><small class="description">' . esc_html( $key['desc'] ) . '</small>';
		}

		return $out;
	}

	/**
	 * Field Select with Options Group
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param array
	 *
	 * @return   string
	 */

	function create_field_select_group( $key, $out = '' ) {
		$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

		$out .= '<select id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" ';

		if ( isset( $key['class'] ) ) {
			$out .= 'class="' . esc_attr( $key['class'] ) . '" ';
		}

		$out .= '> ';

		$selected = isset( $key['value'] ) ? $key['value'] : $key['std'];

		foreach ( $key['fields'] as $group => $fields ) {
			$out .= '<optgroup label="' . $group . '">';

			foreach ( $fields as $field => $option ) {
				$out .= '<option value="' . esc_attr( $option['value'] ) . '" ';

				if ( esc_attr( $selected ) == $option['value'] ) {
					$out .= ' selected="selected" ';
				}

				$out .= '> ' . esc_html( $option['name'] ) . '</option>';
			}

			$out .= '</optgroup>';
		}

		$out .= '</select>';

		if ( isset( $key['desc'] ) ) {
			$out .= '<br/><small class="description">' . esc_html( $key['desc'] ) . '</small>';
		}

		return $out;
	}

	/**
	 * Field Number
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param array
	 *
	 * @return   string
	 */
	function create_field_number( $key, $out = '' ) {
		$out .= $this->create_field_label( $key['name'], $key['_id'] ) . '<br/>';

		$out .= '<input type="number" ';

		if ( isset( $key['class'] ) ) {
			$out .= 'class="' . esc_attr( $key['class'] ) . '" ';
		}

		$value = isset( $key['value'] ) ? $key['value'] : $key['std'];

		$out .= 'id="' . esc_attr( $key['_id'] ) . '" name="' . esc_attr( $key['_name'] ) . '" value="' . esc_attr( $value ) . '" ';

		if ( isset( $key['size'] ) ) {
			$out .= 'size="' . esc_attr( $key['size'] ) . '" ';
		}

		$out .= ' />';

		if ( isset( $key['desc'] ) ) {
			$out .= '<br/><small class="description">' . esc_html( $key['desc'] ) . '</small>';
		}

		return $out;
	}

	/**
	 * Field title
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param array
	 *
	 * @return   string
	 */
	function create_field_title( $key, $out = '' ) {
		ob_start();

		?>
		<h4>
			<?php echo esc_html( $key['name'] ); ?>
		</h4>

		<?php if ( isset( $key['desc'] ) ) : ?>
			<small class="description">
				<?php echo esc_html( $key['desc'] ); ?>
			</small>
		<?php endif; ?>
		<?php

		return ob_get_clean();
	}

	/**
	 * Field Label
	 *
	 * @access   private
	 * @since    1.5
	 *
	 * @param string
	 * @param string
	 *
	 * @return   string
	 */

	function create_field_label( $name = '', $id = '' ) {
		return '<label for="' . esc_attr( $id ) . '">' . esc_html( $name ) . ':</label>';
	}
}
