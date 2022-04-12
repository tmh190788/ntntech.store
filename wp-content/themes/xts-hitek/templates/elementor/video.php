<?php
/**
 * Video template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use Elementor\Embed;

if ( ! function_exists( 'xts_video_widget_template' ) ) {
	/**
	 * Video template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_video_widget_template( $element_args ) {
		$default_args = array(
			// General.
			'video_type'             => 'youtube',
			'video_youtube_url'      => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
			'video_vimeo_url'        => 'https://vimeo.com/235215203',
			'video_hosted_url'       => '',

			// Options.
			'video_autoplay'         => 'no',
			'video_mute'             => 'no',
			'video_loop'             => 'no',
			'video_controls'         => 'yes',
			'video_action_button'    => 'overlay',

			// Image overlay.
			'video_image_overlay'    => array(),
			'video_overlay_lightbox' => 'no',

			// Button.
			'button_text'            => 'Play video',
			'play_button_label'      => '',
			'play_button_align'      => 'left',

			// General style.
			'video_size'             => 'custom',
			'video_aspect_ratio'     => '16-9',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$image_url       = '';
		$video_url       = '';
		$play_classes    = '';
		$wrapper_classes = '';

		xts_enqueue_js_script( 'video-element' );

		// Wrapper classes.
		$wrapper_classes .= ' xts-action-' . $element_args['video_action_button'];
		if ( 'play' === $element_args['video_action_button'] ) {
			xts_enqueue_js_library( 'magnific' );
			xts_enqueue_js_script( 'video-element-popup' );
			$wrapper_classes .= ' xts-textalign-' . $element_args['play_button_align'];
		}
		if ( 'aspect_ratio' === $element_args['video_size'] ) {
			$wrapper_classes .= ' xts-ar-' . $element_args['video_aspect_ratio'];
		}

		// Play classes.
		if ( 'yes' === $element_args['video_overlay_lightbox'] ) {
			xts_enqueue_js_library( 'magnific' );
			xts_enqueue_js_script( 'video-element-popup' );
			$play_classes .= ' xts-el-video-lightbox';
		}

		if ( 'hosted' === $element_args['video_type'] ) {
			$play_classes .= ' xts-el-video-hosted';
		}

		// Image settings.
		if ( 'overlay' === $element_args['video_action_button'] ) {
			if ( $element_args['video_image_overlay']['id'] ) {
				$image_url = xts_get_image_url( $element_args['video_image_overlay']['id'], 'video_image_overlay', $element_args );
			} elseif ( $element_args['video_image_overlay']['url'] ) {
				$image_url = $element_args['video_image_overlay']['url'];
			}
		}

		// Video settings.
		$primary_color = xts_get_opt( 'primary_color' );
		$video_params  = array(
			'loop'     => 'yes' === $element_args['video_loop'] ? 1 : 0,
			'mute'     => 'yes' === $element_args['video_mute'] ? 1 : 0,
			'controls' => 'yes' === $element_args['video_controls'] ? 1 : 0,
			'autoplay' => 'yes' === $element_args['video_autoplay'] && 'without' === $element_args['video_action_button'],
		);

		if ( 'youtube' === $element_args['video_type'] ) {
			$video_url                          = $element_args['video_youtube_url'];
			$element_args['button_link']['url'] = $element_args['video_youtube_url'];

			if ( 'yes' === $element_args['video_loop'] ) {
				$video_properties = Embed::get_video_properties( $element_args['video_youtube_url'] );

				$video_params['playlist'] = $video_properties['video_id'];
			}
		} elseif ( 'vimeo' === $element_args['video_type'] ) {
			$video_url                          = $element_args['video_vimeo_url'];
			$video_params['color']              = str_replace( '#', '', $primary_color['idle'] );
			$element_args['button_link']['url'] = $element_args['video_vimeo_url'];
		} elseif ( 'hosted' === $element_args['video_type'] ) {
			$element_args['button_link']['url'] = $element_args['video_hosted_url']['url'];
		}

		if ( 'hosted' === $element_args['video_type'] ) {
			$video_tag_id                         = uniqid();
			$video_html                           = '';
			$element_args['button_link']['url']   = '#' . $video_tag_id;
			$element_args['button_extra_classes'] = $play_classes;

			if ( 'yes' === $element_args['video_overlay_lightbox'] || 'button' === $element_args['video_action_button'] || 'play' === $element_args['video_action_button'] ) {
				$video_html .= '<div class="xts-popup-video mfp-with-anim" id="' . $video_tag_id . '">';
			}

			$video_html .= xts_get_hosted_video( $element_args );

			if ( 'yes' === $element_args['video_overlay_lightbox'] || 'button' === $element_args['video_action_button'] || 'play' === $element_args['video_action_button'] ) {
				$video_html .= '</div>';
			}
		} else {
			$classes = '';
			if ( 'yes' === $element_args['video_overlay_lightbox'] || 'button' === $element_args['video_action_button'] || 'play' === $element_args['video_action_button'] ) {
				$classes .= ' mfp-with-anim';
			}

			$video_html = Embed::get_embed_html(
				$video_url,
				$video_params,
				array(
					'lazy_load' => 1,
				),
				array(
					'allow'  => 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture',
					'width'  => '100%',
					'height' => '100%',
					'class'  => $classes,
				)
			);
		}

		// Button settings.
		if ( 'button' === $element_args['video_action_button'] ) {
			if ( isset( $element_args['button_extra_classes'] ) ) {
				$element_args['button_extra_classes'] .= ' xts-el-video-btn';
			} else {
				$element_args['button_extra_classes'] = ' xts-el-video-btn';
			}
			xts_enqueue_js_library( 'magnific' );
			xts_enqueue_js_script( 'video-element-popup' );
		}

		xts_get_template(
			'video-item.php',
			array(
				'wrapper_classes' => $wrapper_classes,
				'element_args'    => $element_args,
				'play_classes'    => $play_classes,
				'video_html'      => $video_html,
				'image_url'       => $image_url,
			),
			'',
			'templates/elementor'
		);
	}
}
