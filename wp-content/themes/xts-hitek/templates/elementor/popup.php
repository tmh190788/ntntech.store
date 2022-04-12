<?php
/**
 * Popup template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}


if ( ! function_exists( 'xts_popup_template' ) ) {
	/**
	 * Popup template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_popup_template( $element_args ) {
		$default_args = array(
			// Content.
			'html_block_id'     => '0',

			// Style.
			'width'             => '800',
			'bg_color'          => '',
			'image'             => '',
			'image_bg_position' => 'center-center',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		if ( ! $element_args['html_block_id'] ) {
			?>
				<div class="xts-notification xts-color-info">
					<?php esc_html_e( 'You need to select some HTML Block to be displayed in the popup to make this element work correctly.', 'xts-theme' ); ?>
				</div>
			<?php
			return;
		}

		$uniqid    = 'xts-popup-' . uniqid();
		$image_url = '';

		$element_args['button_extra_classes'] = 'xts-popup-opener';
		$element_args['button_link']          = array(
			'url' => '#' . $uniqid,
		);

		// Image settings.
		if ( $element_args['image']['id'] ) {
			$image_url = xts_get_image_url( $element_args['image']['id'], 'image', $element_args );
		} elseif ( $element_args['image']['url'] ) {
			$image_url = $element_args['image']['url'];
		}

		xts_enqueue_js_library( 'magnific' );
		xts_enqueue_js_script( 'popup-element' );

		?>

		<div class="xts-popup">
			<style>
				#<?php echo esc_attr( $uniqid ); ?>{
					max-width:<?php echo esc_attr( $element_args['width']['size'] ); ?>px;

					<?php if ( $image_url ) : ?>
						background-image: url(<?php echo esc_url( $image_url ); ?>);
						background-position: <?php echo esc_attr( str_replace( '-', ' ', $element_args['image_bg_position'] ) ); ?>;
					<?php endif; ?>

					<?php if ( $element_args['bg_color'] ) : ?>
						background-color: <?php echo esc_url( $element_args['bg_color'] ); ?>;
					<?php endif; ?>
				}
			</style>
			<?php if ( $element_args['html_block_id'] ) : ?>
				<div id="<?php echo esc_attr( $uniqid ); ?>" class="xts-popup-content mfp-hide mfp-with-anim">
					<div class="xts-popup-inner">
						<?php echo xts_get_html_block_content( $element_args['html_block_id'] ); // phpcs:ignore ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $element_args['button_text'] ) : ?>
				<?php xts_button_template( $element_args ); ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
