<?php
/**
 * 360 view template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_360_view_template' ) ) {
	/**
	 * 360 view template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_360_view_template( $element_args ) {
		$default_args = array(
			'images'      => array(),
			'images_size' => 'large',
			'navigation'  => 'yes',
		);

		if ( ! $element_args['images'] ) {
			?>
				<div class="xts-notification xts-color-info">
					<?php esc_html_e( 'You need to upload a set of product images that show your item from different angles of view.', 'xts-theme' ); ?>
				</div>
			<?php
			return;
		}

		xts_enqueue_js_library( 'threesixty' );
		xts_enqueue_js_script( 'threesixty' );

		$element_args = wp_parse_args( $element_args, $default_args );

		$image_data = wp_get_attachment_image_src( $element_args['images'][0]['id'], $element_args['images_size'] );

		$args = array(
			'frames_count' => count( $element_args['images'] ),
			'images'       => array(),
			'width'        => $image_data[1],
			'height'       => $image_data[2],
			'navigation'   => $element_args['navigation'],
		);

		foreach ( $element_args['images'] as $key => $image ) {
			$args['images'][] = xts_get_image_url( $image['id'], 'images', $element_args );
		}

		?>
			<div class="xts-360-view" data-args='<?php echo wp_json_encode( $args ); ?>'>

				<ul class="xts-360-images"></ul>
				<div class="xts-360-progress">
					<span>0%</span>
				</div>
			</div>
		<?php
	}
}
