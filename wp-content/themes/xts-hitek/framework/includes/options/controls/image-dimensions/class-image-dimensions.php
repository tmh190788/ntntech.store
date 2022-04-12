<?php
/**
 * Image dimensions control.
 *
 * @package xts
 */

namespace XTS\Options\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Field;

/**
 * Input type text field control.
 */
class Image_Dimensions extends Field {
	/**
	 * Displays the field control HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function render_control() {
		?>
			<div class="xts-image-dimensions-field">
				<label>
					<?php esc_html_e( 'Width', 'xts-theme' ); ?>
				</label>
				<div class="input-append">
					<input type="number" name="<?php echo esc_attr( $this->get_input_name( 'width' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'width' ) ); ?>">
					<span class="add-on">px</span>
				</div>
			</div>
			<div class="xts-image-dimensions-separator">x</div>
			<div class="xts-image-dimensions-field">
				<label>
					<?php esc_html_e( 'Height', 'xts-theme' ); ?>
				</label>
				<div class="input-append">
					<input type="number" name="<?php echo esc_attr( $this->get_input_name( 'height' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'height' ) ); ?>">
					<span class="add-on">px</span>
				</div>
			</div>
		<?php
	}
}


