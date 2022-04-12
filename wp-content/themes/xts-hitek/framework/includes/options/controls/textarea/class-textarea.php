<?php
/**
 * Textarea with text or HTML control.
 *
 * @package xts
 */

namespace XTS\Options\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Field;

/**
 * Textarea field control.
 */
class Textarea extends Field {
	/**
	 * Displays the field control HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function render_control() {
		wp_enqueue_editor();
		$uniqid  = uniqid();
		$classes = $this->args['wysiwyg'] ? 'wysiwyg' : 'plain';

		?>
		<?php if ( $this->args['wysiwyg'] ) : ?>
			<div class="xts-wysiwyg-buttons xts-btns-set">
				<button class="xts-set-item xts-btn xts-set-btn xts-btns-set-active" data-id="<?php echo esc_attr( 'xts-' . $uniqid ); ?>" data-mode="visual">
					<?php esc_html_e( 'Visual', 'xts-theme' ); ?>
				</button>

				<button class="xts-set-item xts-btn xts-set-btn" data-id="<?php echo esc_attr( 'xts-' . $uniqid ); ?>" data-mode="text">
					<?php esc_html_e( 'Text', 'xts-theme' ); ?>
				</button>
			</div>
		<?php endif; ?>

		<textarea placeholder="<?php echo esc_attr( $this->_get_placeholder() ); ?>" class="xts-textarea-<?php echo esc_attr( $classes ); ?>" name="<?php echo esc_attr( $this->get_input_name() ); ?>" id="<?php echo esc_attr( 'xts-' . $uniqid ); ?>"><?php echo esc_textarea( $this->get_field_value() ); ?></textarea>
		<?php
	}

	/**
	 * Get textarea's placeholder text from arguments.
	 *
	 * @since 1.0.0
	 */
	private function _get_placeholder() {
		return isset( $this->args['placeholder'] ) ? $this->args['placeholder'] : '';
	}
}


