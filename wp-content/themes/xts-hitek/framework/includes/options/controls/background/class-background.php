<?php
/**
 * Set element background options and generate css.
 *
 * @package xts
 */

namespace XTS\Options\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Field;

/**
 * Background properties control.
 */
class Background extends Field {
	/**
	 * Displays the field control HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function render_control() {
		$value           = $this->get_field_value();
		$image_url       = $this->get_field_value( 'url' );
		$image_url_value = '';

		if ( isset( $value['id'] ) && $value['id'] ) {
			$image_url = wp_get_attachment_image_url( $value['id'] );
		}

		if ( $image_url ) {
			$uploads         = wp_upload_dir();
			$image_url_value = explode( $uploads['baseurl'], $image_url )[1];
		}

		?>
			<div class="xts-bg-source">
				<div class="xts-bg-color">
					<input type="text" data-alpha="<?php echo isset( $this->args['alpha'] ) ? esc_attr( $this->args['alpha'] ) : 'true'; ?>" name="<?php echo esc_attr( $this->get_input_name( 'color' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'color' ) ); ?>" />
				</div>

				<div class="xts-bg-image">
					<div class="xts-upload-preview">
						<?php if ( isset( $image_url ) && $image_url ) : ?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'preview', 'xts-theme' ); ?>">
						<?php endif ?>
					</div>
				</div>

				<div class="xts-upload-btns">
					<button class="xts-btn xts-upload-btn">
						<?php esc_html_e( 'Upload', 'xts-theme' ); ?>
					</button>

					<button class="xts-btn xts-btn-disable xts-btn-remove<?php echo ( isset( $value['url'] ) && ! empty( $value['url'] ) ) ? ' xts-active' : ''; ?>">
						<?php esc_html_e( 'Remove', 'xts-theme' ); ?>
					</button>

					<input type="hidden" class="xts-upload-input-url" name="<?php echo esc_attr( $this->get_input_name( 'url' ) ); ?>" value="<?php echo esc_attr( $image_url_value ); ?>" />
					<input type="hidden" class="xts-upload-input-id" name="<?php echo esc_attr( $this->get_input_name( 'id' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'id' ) ); ?>" />
				</div>
			</div>

			<div class="xts-bg-image-options<?php echo ( isset( $value['url'] ) && ! empty( $value['url'] ) ) ? ' xts-active' : ''; ?>">
				<select class="xts-bg-repeat" data-placeholder="<?php esc_attr_e( 'Background repeat', 'xts-theme' ); ?>" name="<?php echo esc_attr( $this->get_input_name( 'repeat' ) ); ?>">
					<option value=""></option>
					<option value="no-repeat" <?php selected( $this->get_field_value( 'repeat' ), 'no-repeat' ); ?>>No Repeat</option>
					<option value="repeat" <?php selected( $this->get_field_value( 'repeat' ), 'repeat' ); ?>>Repeat</option>
					<option value="repeat-x" <?php selected( $this->get_field_value( 'repeat' ), 'repeat-x' ); ?>>Repeat Horizontally</option>
					<option value="repeat-y" <?php selected( $this->get_field_value( 'repeat' ), 'repeat-y' ); ?>>Repeat Vertically</option>
					<option value="inherit" <?php selected( $this->get_field_value( 'repeat' ), 'inherit' ); ?>>Inherit</option>
				</select>
				<select class="xts-bg-size" data-placeholder="<?php esc_attr_e( 'Background size', 'xts-theme' ); ?>" name="<?php echo esc_attr( $this->get_input_name( 'size' ) ); ?>">
					<option value=""></option>
					<option value="cover" <?php selected( $this->get_field_value( 'size' ), 'cover' ); ?>>Cover</option>
					<option value="contain" <?php selected( $this->get_field_value( 'size' ), 'contain' ); ?>>Contain</option>
					<option value="inherit" <?php selected( $this->get_field_value( 'size' ), 'inherit' ); ?>>Inherit</option>
				</select>
				<select class="xts-bg-attachment" data-placeholder="<?php esc_attr_e( 'Background attachment', 'xts-theme' ); ?>" name="<?php echo esc_attr( $this->get_input_name( 'attachment' ) ); ?>">
					<option value=""></option>
					<option value="fixed" <?php selected( $this->get_field_value( 'attachment' ), 'fixed' ); ?>>Fixed</option>
					<option value="scroll" <?php selected( $this->get_field_value( 'attachment' ), 'scroll' ); ?>>Scroll</option>
					<option value="inherit" <?php selected( $this->get_field_value( 'attachment' ), 'inherit' ); ?>>Inherit</option>
				</select>
				<select class="xts-bg-position" data-placeholder="<?php esc_attr_e( 'Background position', 'xts-theme' ); ?>" name="<?php echo esc_attr( $this->get_input_name( 'position' ) ); ?>">
					<option value=""></option>
					<option value="left top" <?php selected( $this->get_field_value( 'position' ), 'left top' ); ?>>
						<?php esc_html_e( 'Left Top', 'xts-theme' ); ?>
					</option>
					<option value="left center" <?php selected( $this->get_field_value( 'position' ), 'left center' ); ?>>
						<?php esc_html_e( 'Left Center', 'xts-theme' ); ?>
					</option>
					<option value="left bottom" <?php selected( $this->get_field_value( 'position' ), 'left bottom' ); ?>>
						<?php esc_html_e( 'Left Bottom', 'xts-theme' ); ?>
					</option>
					<option value="center top" <?php selected( $this->get_field_value( 'position' ), 'center top' ); ?>>
						<?php esc_html_e( 'Center Top', 'xts-theme' ); ?>
					</option>
					<option value="center center" <?php selected( $this->get_field_value( 'position' ), 'center center' ); ?>>
						<?php esc_html_e( 'Center Center', 'xts-theme' ); ?>
					</option>
					<option value="center bottom" <?php selected( $this->get_field_value( 'position' ), 'center bottom' ); ?>>
						<?php esc_html_e( 'Center Bottom', 'xts-theme' ); ?>
					</option>
					<option value="right top" <?php selected( $this->get_field_value( 'position' ), 'right top' ); ?>>
						<?php esc_html_e( 'Right Top', 'xts-theme' ); ?>
					</option>
					<option value="right center" <?php selected( $this->get_field_value( 'position' ), 'right center' ); ?>>
						<?php esc_html_e( 'Right Center', 'xts-theme' ); ?>
					</option>
					<option value="right bottom" <?php selected( $this->get_field_value( 'position' ), 'right bottom' ); ?>>
						<?php esc_html_e( 'Right Bottom', 'xts-theme' ); ?>
					</option>
					<option value="custom" <?php selected( $this->get_field_value( 'position' ), 'custom' ); ?>>
						<?php esc_html_e( 'Custom', 'xts-theme' ); ?>
					</option>
				</select>

				<div class="xts-bg-position-custom<?php echo ( isset( $value['position'] ) && ! empty( $value['position'] ) && 'custom' === $value['position'] ) ? ' xts-active' : ''; ?>">
					<div class="xts-position-x">
						<input type="text" placeholder="Horizontal [X]" name="<?php echo esc_attr( $this->get_input_name( 'position_x' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'position_x' ) ); ?>">
					</div>
					<div class="xts-position-y">
						<input type="text" placeholder="Vertical [Y]" name="<?php echo esc_attr( $this->get_input_name( 'position_y' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'position_y' ) ); ?>">
					</div>
				</div>

			</div>

			<div class="xts-bg-preview"></div>

			<input type="hidden" class="xts-css-output" name="<?php echo esc_attr( $this->get_input_name( 'css_output' ) ); ?>" value="1">
		<?php
	}

	/**
	 * Enqueue color picker lib.
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker-alpha', XTS_FRAMEWORK_URL . '/assets/js-libs/wp-color-picker-alpha.js', array( 'wp-color-picker' ), XTS_VERSION, true );
	}

	/**
	 * Generate CSS.
	 *
	 * @param array $value Value.
	 *
	 * @return string
	 */
	public function generate_css( $value ) {
		$output = '{' . "\n";
		if ( isset( $value['color'] ) && ! empty( $value['color'] ) ) {
			$output .= "\t" . 'background-color:' . $value['color'] . ';' . "\n";
		}
		if ( isset( $value['id'] ) && ! empty( $value['id'] ) ) {
			$output .= "\t" . 'background-image: url(' . wp_get_attachment_image_url( $value['id'], 'full' ) . ');' . "\n";
		}
		if ( isset( $value['repeat'] ) && ! empty( $value['repeat'] ) ) {
			$output .= "\t" . 'background-repeat:' . $value['repeat'] . ';' . "\n";
		}
		if ( isset( $value['size'] ) && ! empty( $value['size'] ) ) {
			$output .= "\t" . 'background-size:' . $value['size'] . ';' . "\n";
		}
		if ( isset( $value['attachment'] ) && ! empty( $value['attachment'] ) ) {
			$output .= "\t" . 'background-attachment:' . $value['attachment'] . ';' . "\n";
		}
		if ( isset( $value['position'] ) && ! empty( $value['position'] ) ) {
			if ( 'custom' === $value['position'] && isset( $value['position_x'] ) && isset( $value['position_y'] ) ) {
				$output .= "\t" . 'background-position:' . $value['position_x'] . ' ' . $value['position_y'] . ';' . "\n";
			} else {
				$output .= "\t" . 'background-position:' . $value['position'] . ';' . "\n";
			}
		}

		$output .= '}' . "\n\n";

		return $output;
	}

	/**
	 * Output field's css code based on the settings..
	 *
	 * @since 1.0.0
	 *
	 * @return string $output Generated CSS code.
	 */
	public function css_output() {
		if ( ! isset( $this->args['selector'] ) || empty( $this->args['selector'] ) || ( ! $this->get_field_value( 'css_output' ) && 'metabox' !== $this->_type ) || ( ! $this->get_field_value( 'color' ) && ! $this->get_field_value( 'url' ) && ! $this->get_field_value( 'repeat' ) && ! $this->get_field_value( 'size' ) && ! $this->get_field_value( 'attachment' ) && ! $this->get_field_value( 'position' ) ) ) {
			return '';
		}

		$value  = $this->get_field_value();
		$output = '';

		if ( isset( $this->args['desktop_only'] ) || ( ! isset( $this->args['desktop_only'] ) && ! isset( $this->args['responsive'] ) ) ) {
			$output .= $this->args['selector'];
			$output .= $this->generate_css( $value );
		} elseif ( isset( $this->args['tablet_only'] ) ) {
			$output .= '@media (max-width: 1024px) {' . "\n";
			$output .= $this->args['selector'];
			$output .= $this->generate_css( $value );
			$output .= '}' . "\n\n";
		} elseif ( isset( $this->args['mobile_only'] ) ) {
			$output .= '@media (max-width: 767px) {' . "\n";
			$output .= $this->args['selector'];
			$output .= $this->generate_css( $value );
			$output .= '}' . "\n\n";
		}

		return $output;
	}
}


