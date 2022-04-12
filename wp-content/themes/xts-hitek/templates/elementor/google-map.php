<?php
/**
 * Google map template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_google_map_template' ) ) {
	/**
	 * Google map template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_google_map_template( $element_args ) {
		$default_args = array(
			// General.
			'latitude'                 => 45.9,
			'longitude'                => 10.9,

			// Map.
			'zoom'                     => array( 'size' => 15 ),
			'mouse_zoom'               => 'no',
			'default_ui'               => 'no',
			'json_style'               => '',

			// Text.
			'text_content_type'        => 'text',
			'text'                     => '',
			'html_block_id'            => '0',
			'text_horizontal_position' => 'start',
			'text_vertical_position'   => 'start',

			// Marker.
			'marker_icon'              => array( 'url' => XTS_ASSETS_IMAGES_URL . '/elementor/google-map/default-marker.png' ),
			'marker_title'             => '',
			'marker_text'              => '',

			// Lazy load.
			'lazy_type'                => 'page_load',
			'lazy_placeholder'         => '',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$uniqid               = uniqid();
		$wrapper_classes      = '';
		$text_wrapper_classes = '';
		$text_inner_classes   = '';
		$placeholder_url      = '';

		// Text wrapper classes.
		$text_wrapper_classes .= ' xts-items-' . $element_args['text_vertical_position'];
		$text_wrapper_classes .= ' xts-justify-' . $element_args['text_horizontal_position'];
		if ( xts_elementor_is_edit_mode() ) {
			$text_inner_classes .= ' elementor-inline-editing';
		}

		// Wrapper classes.
		if ( 'page_load' !== $element_args['lazy_type'] ) {
			$wrapper_classes .= ' xts-lazy';
		}

		// Image settings.
		if ( isset( $element_args['lazy_placeholder']['id'] ) && $element_args['lazy_placeholder']['id'] ) {
			$placeholder_url = xts_get_image_url( $element_args['lazy_placeholder']['id'], 'lazy_placeholder', $element_args );
		} elseif ( isset( $element_args['lazy_placeholder']['url'] ) && $element_args['lazy_placeholder']['url'] ) {
			$placeholder_url = $element_args['lazy_placeholder']['url'];
		}

		// Marker settings.
		if ( isset( $element_args['marker_icon']['id'] ) && $element_args['marker_icon']['id'] ) {
			$marker_url = xts_get_image_url( $element_args['marker_icon']['id'], 'marker_icon', $element_args );
		} else {
			$marker_url = XTS_ASSETS_IMAGES_URL . '/elementor/google-map/default-marker.png';
		}

		$placeholder_attrs = $placeholder_url ? 'style="background-image: url(' . esc_url( $placeholder_url ) . ');"' : '';

		$compress_json_style   = xts_is_core_module_exists() ? xts_compress( $element_args['json_style'] ) : '';
		$decompress_json_style = xts_is_core_module_exists() ? xts_decompress( $element_args['json_style'], true ) : '';

		// Map settings.
		$map_args = array(
			'latitude'           => $element_args['latitude'] ? $element_args['latitude'] : 45.9,
			'longitude'          => $element_args['longitude'] ? $element_args['longitude'] : 10.9,
			'zoom'               => $element_args['zoom']['size'] ? $element_args['zoom']['size'] : 15,
			'mouse_zoom'         => $element_args['mouse_zoom'] ? $element_args['mouse_zoom'] : 'no',
			'json_style'         => $decompress_json_style ? $element_args['json_style'] : $compress_json_style,
			'marker_icon'        => $marker_url,
			'marker_text_needed' => $element_args['marker_text'] || $element_args['marker_title'] ? 'yes' : 'no',
			'marker_text'        => '<h3>' . $element_args['marker_title'] . '</h3>' . $element_args['marker_text'],
			'selector'           => 'xts-map-id-' . $uniqid,
			'default_ui'         => 'yes' === $element_args['default_ui'],
			'lazy_type'          => $element_args['lazy_type'],
		);

		$minified = xts_get_opt( 'minified_js' ) ? '.min' : '';
		wp_enqueue_script( 'xts-google-map-api', 'https://maps.google.com/maps/api/js?libraries=geometry&v=3.44&key=' . xts_get_opt( 'google_map_api_key' ), array(), XTS_VERSION, true );
		wp_enqueue_script( 'xts-maplace', XTS_THEME_URL . '/js/maplace' . $minified . '.js', array( 'xts-google-map-api' ), XTS_VERSION, true );

		xts_enqueue_js_script( 'google-map-element' );

		?>
			<div class="xts-map<?php echo esc_attr( $wrapper_classes ); ?>" data-map-args='<?php echo wp_json_encode( $map_args ); ?>'>
				<div id="xts-map-id-<?php echo esc_attr( $uniqid ); ?>" class="xts-map-layout xts-fill"></div>

				<?php if ( $element_args['text'] || $element_args['html_block_id'] ) : ?>
					<div class="xts-map-container container xts-fill<?php echo esc_attr( $text_wrapper_classes ); ?>">
						<div class="xts-map-content xts-opened">
							<div class="xts-map-close"></div>

							<?php if ( 'text' === $element_args['text_content_type'] && $element_args['text'] ) : ?>
								<div class="xts-map-desc xts-reset-all-last xts-reset-mb-10<?php echo esc_attr( $text_inner_classes ); ?>" data-elementor-setting-key="text">
									<?php echo do_shortcode( $element_args['text'] ); ?>
								</div>
							<?php elseif ( 'html_block' === $element_args['text_content_type'] && $element_args['html_block_id'] ) : ?>
								<div class="xts-map-desc">
									<?php echo xts_get_html_block_content( $element_args['html_block_id'] ); // phpcs:ignore ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( 'page_load' !== $element_args['lazy_type'] && $placeholder_url ) : ?>
					<div class="xts-map-placeholder xts-fill" <?php echo wp_kses( $placeholder_attrs, true ); ?>>
						<?php if ( 'button' === $element_args['lazy_type'] ) : ?>
							<a href="#" class="xts-map-button xts-button xts-color-white">
								<?php esc_attr_e( 'Show map', 'xts-theme' ); ?>
							</a>
						<?php endif ?>
					</div>
				<?php endif ?>
			</div>
		<?php
	}
}
