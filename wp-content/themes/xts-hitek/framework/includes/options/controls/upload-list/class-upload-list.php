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
 * Upload list button.
 */
class Upload_List extends Field {

	/**
	 * Displays the field control HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function render_control() {
		$images  = $this->get_field_value();
		$classes = $images ? ' xts-active' : '';

		if ( is_array( $images ) ) {
			$images = '';
		}

		?>
			<div class="xts-upload-preview">
				<?php foreach ( explode( ',', $images ) as $image_id ) : ?>
					<?php if ( $image_id ) : ?>
						<div data-attachment_id="<?php echo esc_attr( $image_id ); ?>">
							<?php echo wp_get_attachment_image( $image_id, 'thumbnail'); // phpcs:ignore ?>

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

				<button class="xts-btn xts-btn-remove xts-btn-disable<?php echo esc_html( $classes ); ?>">
					<?php esc_html_e( 'Clear all', 'xts-theme' ); ?>
				</button>

				<input type="hidden" class="xts-upload-input-id" name="<?php echo esc_attr( $this->get_input_name() ); ?>" value="<?php echo esc_attr( $images ); ?>" />
			</div>
		<?php
	}
}


