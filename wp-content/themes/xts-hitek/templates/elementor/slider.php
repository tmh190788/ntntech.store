<?php
/**
 * Slider template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_slider_template' ) ) {
	/**
	 * Slider template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_slider_template( $element_args ) {
		$default_args = array(
			'slider' => '',
		);

		$args = wp_parse_args( $element_args, $default_args );

		$slider_term = get_term_by( 'slug', $args['slider'], 'xts_slider' );

		if ( is_wp_error( $slider_term ) || ! $slider_term ) {
			return;
		}

		xts_enqueue_js_library( 'flickity' );
		xts_enqueue_js_script( 'slider-element' );

		$slider_id = 'xts-slider-' . $slider_term->term_id;
		$animation = get_term_meta( $slider_term->term_id, '_xts_animation', true );

		if ( 'fade' === $animation ) {
			xts_enqueue_js_library( 'flickity-fade' );
		}

		$slides = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'xts-slide',
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
				'tax_query'   => array( // phpcs:ignore
					array(
						'taxonomy' => 'xts_slider',
						'field'    => 'id',
						'terms'    => $slider_term->term_id,
					),
				),
			)
		);

		if ( is_wp_error( $slides ) || ! $slides ) {
			return;
		}

		$slider_atts = array(
			'carousel_spacing'      => 0,
			'carousel_items'        => array( 'size' => 1 ),
			'carousel_items_tablet' => array( 'size' => 1 ),
			'carousel_items_mobile' => array( 'size' => 1 ),
			'autoplay'              => get_term_meta( $slider_term->term_id, '_xts_autoplay', true ) ? 'yes' : 'no',
			'autoplay_speed'        => array( 'size' => get_term_meta( $slider_term->term_id, '_xts_autoplay_speed', true ) ),
			'infinite_loop'         => 'yes',
			'center_mode'           => 'no',
			'auto_height'           => 'yes',
			'arrows'                => 'disabled' === get_term_meta( $slider_term->term_id, '_xts_arrows_style', true ) ? 'no' : 'yes',
			'dots'                  => 'disabled' === get_term_meta( $slider_term->term_id, '_xts_dots_style', true ) ? 'no' : 'yes',
		);

		?>
			<?php xts_get_slider_css( $slider_term->term_id, $slider_id, $slides ); ?>
			<div id="<?php echo esc_attr( $slider_id ); ?>" class="xts-slider<?php echo esc_attr( xts_get_slider_classes( $slider_term->term_id ) ); ?>" <?php echo xts_get_carousel_atts( $slider_atts ); // phpcs:ignore ?>>
				<?php foreach ( $slides as $key => $slide ) : ?>
					<?php
					$slide_id      = 'xts-slide-' . $slide->ID;
					$mask_classes  = '';
					$slide_classes = '';
					$autoplay      = 0;

					if ( 0 === $key || 1 === $key ) {
						$slide_classes .= ' xts-loaded';
						$autoplay       = 1;
					}

					// Video settings.
					$video_mp4     = get_post_meta( $slide->ID, '_xts_slide_video_mp4', true );
					$video_webm    = get_post_meta( $slide->ID, '_xts_slide_video_webm', true );
					$video_ogg     = get_post_meta( $slide->ID, '_xts_slide_video_ogg', true );
					$video_youtube = get_post_meta( $slide->ID, '_xts_slide_video_youtube', true );
					$video_vimeo   = get_post_meta( $slide->ID, '_xts_slide_video_vimeo', true );

					// Overlay mask.
					$overlay_mask         = get_post_meta( $slide->ID, '_xts_overlay_mask', true );
					$dotted_overlay_style = get_post_meta( $slide->ID, '_xts_dotted_overlay_style', true );
					$mask_classes        .= ' xts-style-' . $overlay_mask;
					if ( 'dotted' === $overlay_mask ) {
						$mask_classes .= ' xts-dotted-' . $dotted_overlay_style;
					}

					// Distortion.
					$slide_attrs = '';
					if ( 'distortion' === $animation ) {
						xts_enqueue_js_script( 'slider-distortion' );
						$background_desktop = get_post_meta( $slide->ID, '_xts_background', true );

						if ( isset( $background_desktop['id'] ) ) {
							$slide_attrs = 'data-image-url="' . wp_get_attachment_image_url( $background_desktop['id'], 'full' ) . '"';
						}
					}

					?>

					<div id="<?php echo esc_attr( $slide_id ); ?>" class="xts-slide<?php echo esc_attr( $slide_classes ); ?>" <?php echo wp_kses( $slide_attrs, true ); ?>>
						<div class="container xts-slide-container<?php echo esc_attr( xts_get_slide_classes( $slide->ID ) ); ?>">
							<div class="xts-slide-content">
								<?php if ( xts_is_elementor_installed() && Elementor\Plugin::$instance->db->is_built_with_elementor( $slide->ID ) ) : ?>
									<?php echo xts_elementor_get_content( $slide->ID ); // phpcs:ignore ?>
								<?php else : ?>
									<?php echo do_shortcode( wpautop( $slide->post_content ) ); ?>
								<?php endif; ?>
							</div>
						</div>

						<div class="xts-slide-bg xts-video-resize xts-fill">
							<?php if ( ( isset( $video_mp4['id'] ) && $video_mp4['id'] ) || ( isset( $video_webm['id'] ) && $video_webm['id'] ) || ( isset( $video_ogg['id'] ) && $video_ogg['id'] ) ) : ?>
								<?php
								$attrs = array( 'preload', 'playsinline', 'muted', 'loop' );

								if ( $autoplay ) {
									$attrs[] = 'autoplay';
								}

								xts_video_template(
									array(
										'classes'    => 'xts-slide-video-html5',
										'attrs'      => $attrs,
										'video_mp4'  => $video_mp4,
										'video_webm' => $video_webm,
										'video_ogg'  => $video_ogg,
									)
								);
								?>
							<?php elseif ( $video_youtube ) : ?>
								<?php
								wp_enqueue_script( 'xts-youtube-player', 'https://www.youtube.com/player_api', array(), XTS_VERSION, true );

								echo Elementor\Embed::get_embed_html( // phpcs:ignore
									$video_youtube,
									array(
										'enablejsapi' => 1,
										'autoplay'    => $autoplay,
										'controls'    => 0,
										'showinfo'    => 0,
										'loop'        => 1,
										'mute'        => 1,
										'rel'         => 0,
									),
									array(
										'lazy_load' => 0,
									),
									array(
										'class'  => 'xts-slide-video-youtube',
										'allow'  => 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture',
										'width'  => '100%',
										'height' => '100%',
									)
								);
								?>
							<?php elseif ( $video_vimeo ) : ?>
								<?php
								xts_enqueue_js_library( 'vimeo-player' );

								echo Elementor\Embed::get_embed_html( // phpcs:ignore
									$video_vimeo,
									array(
										'api'        => 1,
										'muted'      => 1,
										'background' => 1,
										'autoplay'   => $autoplay,
										'loop'       => 1,
									),
									array(
										'lazy_load' => 0,
									),
									array(
										'class'  => 'xts-slide-video-vimeo',
										'allow'  => 'autoplay',
										'width'  => '100%',
										'height' => '100%',
									)
								);
								?>
							<?php endif; ?>
						</div>
						<?php if ( 'without' !== $overlay_mask ) : ?>
							<div class="xts-slide-overlay xts-fill<?php echo esc_attr( $mask_classes ); ?>"></div>
						<?php endif; ?>
					</div>

				<?php endforeach; ?>
			</div>
		<?php
	}
}

if ( ! function_exists( 'xts_get_slider_css' ) ) {
	/**
	 * Get slider CSS
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id Slider id.
	 * @param string  $el_id Slider element id.
	 * @param array   $slides Array of slides.
	 */
	function xts_get_slider_css( $id, $el_id, $slides ) {
		$height        = get_term_meta( $id, '_xts_height', true );
		$height_tablet = get_term_meta( $id, '_xts_height_tablet', true );
		$height_mobile = get_term_meta( $id, '_xts_height_mobile', true );
		$full_height   = get_term_meta( $id, '_xts_full_height', true );

		echo '<style type="text/css">';
		?>

		<?php if ( ! $full_height ) : ?>
			#<?php echo esc_attr( $el_id ); ?> .xts-slide {
				<?php xts_maybe_set_css_rule( 'min-height', $height, '', 'px' ); ?>
			}

			@media (min-width: 1025px) {
				.browser-Internet #<?php echo esc_attr( $el_id ); ?> .xts-slide {
					<?php xts_maybe_set_css_rule( 'height', $height, '', 'px' ); ?>
				}
			}

			@media (max-width: 1024px) {
				#<?php echo esc_attr( $el_id ); ?> .xts-slide {
					<?php xts_maybe_set_css_rule( 'min-height', $height_tablet, '', 'px' ); ?>
				}
			}

			@media (max-width: 767px) {
				#<?php echo esc_attr( $el_id ); ?> .xts-slide {
					<?php xts_maybe_set_css_rule( 'min-height', $height_mobile, '', 'px' ); ?>
				}
			}
		<?php endif; ?>

		<?php
		foreach ( $slides as $slide ) {
			$width        = get_post_meta( $slide->ID, '_xts_content_width', true );
			$width_tablet = get_post_meta( $slide->ID, '_xts_content_width_tablet', true );
			$width_mobile = get_post_meta( $slide->ID, '_xts_content_width_mobile', true );
			$full_width   = get_post_meta( $slide->ID, '_xts_content_full_width', true );

			$background_desktop = get_post_meta( $slide->ID, '_xts_background', true );
			$background_tablet  = get_post_meta( $slide->ID, '_xts_background_tablet', true );
			$background_mobile  = get_post_meta( $slide->ID, '_xts_background_mobile', true );

			// Overlay mask.
			$overlay_mask       = get_post_meta( $slide->ID, '_xts_overlay_mask', true );
			$overlay_mask_color = get_post_meta( $slide->ID, '_xts_overlay_color', true );

			?>
			<?php if ( $background_desktop ) : ?>
				#xts-slide-<?php echo esc_attr( $slide->ID ); ?>.xts-loaded .xts-slide-bg {
					<?php xts_maybe_set_css_rule( 'background', $background_desktop ); ?>
				}

				#xts-slide-<?php echo esc_attr( $slide->ID ); ?> .xts-slide-bg {
					<?php xts_maybe_set_css_rule( 'background_color', $background_desktop ); ?>
				}
			<?php endif; ?>

			<?php if ( 'color' === $overlay_mask ) : ?>
				#xts-slide-<?php echo esc_attr( $slide->ID ); ?> .xts-slide-overlay {
					<?php xts_maybe_set_css_rule( 'background-color', $overlay_mask_color ); ?>
				}
			<?php endif; ?>

			<?php if ( ! $full_width ) : ?>
				#xts-slide-<?php echo esc_attr( $slide->ID ); ?> .xts-slide-content {
					<?php xts_maybe_set_css_rule( 'max-width', $width, '', 'px' ); ?>
				}
			<?php endif; ?>

			@media (max-width: 1024px) {
				<?php if ( $background_tablet ) : ?>
					#xts-slide-<?php echo esc_attr( $slide->ID ); ?>.xts-loaded .xts-slide-bg {
						<?php xts_maybe_set_css_rule( 'background', $background_tablet ); ?>
					}
					#xts-slide-<?php echo esc_attr( $slide->ID ); ?> .xts-slide-bg {
						<?php xts_maybe_set_css_rule( 'background_color', $background_tablet ); ?>
					}
				<?php endif; ?>

				<?php if ( ! $full_width ) : ?>
					#xts-slide-<?php echo esc_attr( $slide->ID ); ?> .xts-slide-content {
						<?php xts_maybe_set_css_rule( 'max-width', $width_tablet, '', 'px' ); ?>
					}
				<?php endif; ?>
			}

			@media (max-width: 767px) {
				<?php if ( $background_mobile ) : ?>
					#xts-slide-<?php echo esc_attr( $slide->ID ); ?>.xts-loaded .xts-slide-bg {
						<?php xts_maybe_set_css_rule( 'background', $background_mobile ); ?>
					}
					#xts-slide-<?php echo esc_attr( $slide->ID ); ?> .xts-slide-bg {
						<?php xts_maybe_set_css_rule( 'background_color', $background_mobile ); ?>
					}
				<?php endif; ?>

				<?php if ( ! $full_width ) : ?>
					#xts-slide-<?php echo esc_attr( $slide->ID ); ?> .xts-slide-content {
						<?php xts_maybe_set_css_rule( 'max-width', $width_mobile, '', 'px' ); ?>
					}
				<?php endif; ?>
			}

			<?php
		}
		echo '</style>';
	}
}

if ( ! function_exists( 'xts_maybe_set_css_rule' ) ) {
	/**
	 * Set CSS rule
	 *
	 * @since 1.0.0
	 *
	 * @param string $rule CSS rule.
	 * @param mixed  $value CSS value.
	 * @param string $before Before value.
	 * @param string $after After value.
	 */
	function xts_maybe_set_css_rule( $rule, $value = '', $before = '', $after = '' ) {
		if ( in_array( $rule, array( 'width', 'height', 'max-width', 'max-height', 'min-height' ), true ) ) {
			echo esc_html( $rule . ':' . $before . $value . $after . ';' );
			return;
		}

		if ( in_array( $rule, array( 'background-color' ), true ) && is_array( $value ) ) {
			echo esc_html( $rule . ':' . $before . $value['idle'] . $after . ';' );
			return;
		}

		if ( in_array( $rule, array( 'background_color' ), true ) && ( empty( $before ) || empty( $after ) ) ) {
			if ( isset( $value['color'] ) && ! empty( $value['color'] ) ) {
				echo esc_html( 'background-color:' . $value['color'] . ';' );
			}
			return;
		}

		if ( in_array( $rule, array( 'background' ), true ) && ( empty( $before ) || empty( $after ) ) ) {
			$output = '';

			if ( isset( $value['id'] ) && ! empty( $value['id'] ) ) {
				$output .= 'background-image: url(' . wp_get_attachment_image_url( $value['id'], 'full' ) . ');';
			}
			if ( isset( $value['size'] ) && ! empty( $value['size'] ) ) {
				$output .= 'background-size:' . $value['size'] . ';';
			}
			if ( isset( $value['position'] ) && ! empty( $value['position'] ) ) {
				if ( 'custom' === $value['position'] && isset( $value['position_x'] ) && isset( $value['position_y'] ) ) {
					$output .= 'background-position:' . $value['position_x'] . ' ' . $value['position_y'] . ';';
				} else {
					$output .= 'background-position:' . $value['position'] . ';';
				}
			}

			echo esc_html( $output );
			return;
		}
	}
}

if ( ! function_exists( 'xts_get_slide_classes' ) ) {
	/**
	 * Get slide classes
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id Slide id.
	 *
	 * @return string
	 */
	function xts_get_slide_classes( $id ) {
		$classes = '';

		$v_align         = get_post_meta( $id, '_xts_vertical_align', true );
		$h_align         = get_post_meta( $id, '_xts_horizontal_align', true );
		$without_padding = get_post_meta( $id, '_xts_content_without_padding', true );

		$classes .= ' xts-items-' . $v_align;
		$classes .= ' xts-justify-' . $h_align;

		if ( $without_padding ) {
			$classes .= ' xts-without-padding';
		}

		return $classes;
	}
}

if ( ! function_exists( 'xts_get_sliders_array' ) ) {
	/**
	 * Get sliders array
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_sliders_array() {
		$sliders = get_terms(
			array(
				'taxonomy'   => 'xts_slider',
				'hide_empty' => false,
			)
		);

		$output = array(
			'0' => esc_html__( 'Select', 'xts-theme' ),
		);

		if ( is_wp_error( $sliders ) || ! $sliders ) {
			return $output;
		}

		foreach ( $sliders as $slider ) {
			$output[ $slider->slug ] = $slider->name;
		}

		return $output;
	}
}

if ( ! function_exists( 'xts_get_slider_classes' ) ) {
	/**
	 * Get slider classes
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id Slider id.
	 * @param boolean $animations_autoplay Autoplay animations.
	 *
	 * @return string
	 */
	function xts_get_slider_classes( $id, $animations_autoplay = false ) {
		$classes = '';

		$arrows_style        = get_term_meta( $id, '_xts_arrows_style', true );
		$arrows_shape        = get_term_meta( $id, '_xts_arrows_shape', true );
		$arrows_position     = get_term_meta( $id, '_xts_arrows_vertical_position', true );
		$dots_style          = get_term_meta( $id, '_xts_dots_style', true );
		$arrows_color_scheme = get_term_meta( $id, '_xts_arrows_color_scheme', true );
		$dots_color_scheme   = get_term_meta( $id, '_xts_dots_color_scheme', true );
		$stretch_slider      = get_term_meta( $id, '_xts_stretch_slider', true );
		$animation           = get_term_meta( $id, '_xts_animation', true );
		$full_height         = get_term_meta( $id, '_xts_full_height', true );

		$classes .= ' xts-anim-' . $animation;
		if ( $arrows_position ) {
			$classes .= ' xts-arrows-vpos-' . $arrows_position;
		} else {
			$classes .= ' xts-arrows-vpos-sides';
		}
		if ( 'dark' !== $arrows_style ) {
			$classes .= ' xts-arrows-style-' . $arrows_style;
		}
		if ( 'dark' !== $dots_style ) {
			$classes .= ' xts-dots-style-' . $dots_style;
		}
		if ( ! $animations_autoplay ) {
			$classes .= ' xts-autoplay-animations-off';
		}
		if ( 'bg' === $arrows_style && 'square' !== $arrows_shape ) {
			$classes .= ' xts-arrows-shape-' . $arrows_shape;
		}
		if ( 'dark' !== $arrows_color_scheme ) {
			$classes .= ' xts-arrows-' . $arrows_color_scheme;
		}
		if ( 'dark' !== $dots_color_scheme ) {
			$classes .= ' xts-dots-' . $dots_color_scheme;
		}
		if ( 'disabled' !== $stretch_slider ) {
			$classes .= ' xts-section-' . $stretch_slider;
		}
		if ( $full_height ) {
			$classes .= ' xts-full-height';
		}

		return $classes;
	}
}

