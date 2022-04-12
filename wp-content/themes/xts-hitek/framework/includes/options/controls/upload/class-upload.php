<?php
/**
 * Upload media library control.
 *
 * @package xts
 */

namespace XTS\Options\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Field;

/**
 * Upload button.
 */
class Upload extends Field {

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

		?>
		<div class="xts-upload-preview">
			<?php if ( isset( $image_url ) && $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_html_e( 'preview', 'xts-theme' ); ?>">
			<?php endif ?>
		</div>

		<div class="xts-upload-btns">
			<button class="xts-btn xts-upload-btn">
				<?php esc_html_e( 'Upload', 'xts-theme' ); ?>
			</button>

			<button class="xts-btn xts-btn-disable xts-btn-remove<?php echo ( isset( $image_url ) && $image_url ) ? ' xts-active' : ''; ?>">
				<?php esc_html_e( 'Remove', 'xts-theme' ); ?>
			</button>

			<input type="hidden" class="xts-upload-input-url" name="<?php echo esc_attr( $this->get_input_name( 'url' ) ); ?>" value="<?php echo esc_attr( $image_url_value ); ?>" />
			<input type="hidden" class="xts-upload-input-id" name="<?php echo esc_attr( $this->get_input_name( 'id' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'id' ) ); ?>" />
		</div>
		<?php
	}

	/**
	 * Enqueue color picker lib.
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		wp_enqueue_media();
	}
}


