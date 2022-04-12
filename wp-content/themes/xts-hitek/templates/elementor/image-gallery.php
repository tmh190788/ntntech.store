<?php
/**
 * Image gallery function
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_image_gallery_template' ) ) {
	/**
	 * Image gallery template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_image_gallery_template( $element_args ) {
		global $xts_image_gallery_index;

		$default_args = array(
			// General.
			'gallery'                          => array(),

			// General.
			'hover_effect'                     => 'none',

			// Layout.
			'image_shadow_switcher'            => 'no',
			'image_horizontal_position'        => 'start',
			'image_vertical_position'          => 'start',
			'view'                             => 'grid',
			'columns'                          => array( 'size' => 3 ),
			'columns_tablet'                   => array( 'size' => '' ),
			'columns_mobile'                   => array( 'size' => '' ),
			'spacing'                          => xts_get_default_value( 'items_gap' ),
			'masonry'                          => 'no',
			'different_images'                 => 'no',
			'different_images_position'        => '2,5,8,9',
			'gallery_thumbs'                   => 'no',
			'gallery_thumbs_image_size'        => 'thumbnail',
			'gallery_thumbs_image_size_custom' => '',

			// Carousel.
			'carousel_items'                   => array( 'size' => 3 ),
			'carousel_items_tablet'            => array( 'size' => '' ),
			'carousel_items_mobile'            => array( 'size' => '' ),
			'center_mode'                      => 'no',
			'center_mode_opacity'              => 'no',
			'carousel_spacing'                 => xts_get_default_value( 'items_gap' ),

			// Extra.
			'extra_wrapper_classes'            => '',
			'caption'                          => 'lightbox',
			'click_action'                     => 'lightbox',
			'custom_links'                     => '',
			'global_lightbox'                  => 'no',
			'lightbox_gallery'                 => 'yes',
			'animation_in_view'                => 'no',
			'xts_animation_items'              => '',
			'xts_animation_duration_items'     => 'normal',
			'xts_animation_delay_items'        => '',
			'lazy_loading'                     => 'no',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes           = '';
		$carousel_attrs            = '';
		$custom_links              = array();
		$different_images_position = explode( ',', $element_args['different_images_position'] );

		if ( ! $element_args['gallery'] ) {
			?>
			<?php if ( 'xts-images-comments-lightbox' !== $element_args['extra_wrapper_classes'] ) : ?>
				<div class="xts-notification xts-color-info">
					<?php esc_html_e( 'You need to upload images to the element first to display the gallery.', 'xts-theme' ); ?>
				</div>
			<?php endif; ?>
			<?php
			return;
		}

		if ( 'carousel' === $element_args['view'] ) {
			$wrapper_classes .= xts_get_carousel_classes( $element_args );
			$wrapper_classes .= xts_get_row_classes( $element_args['carousel_items']['size'], $element_args['carousel_items_tablet']['size'], $element_args['carousel_items_mobile']['size'], $element_args['carousel_spacing'] );
			$carousel_attrs  .= xts_get_carousel_atts( $element_args );
		} else {
			$wrapper_classes .= xts_get_row_classes( $element_args['columns']['size'], $element_args['columns_tablet']['size'], $element_args['columns_mobile']['size'], $element_args['spacing'] );
		}

		$wrapper_classes .= ' xts-textalign-' . $element_args['image_horizontal_position'];
		$wrapper_classes .= ' xts-items-' . $element_args['image_vertical_position'];
		if ( 'lightbox' === $element_args['click_action'] ) {
			xts_enqueue_js_library( 'photoswipe-bundle' );
			xts_enqueue_js_script( 'image-gallery-element' );
			$wrapper_classes .= ' xts-photoswipe-images';

			if ( 'yes' === $element_args['global_lightbox'] && 'xts-images-comments-lightbox' !== $element_args['extra_wrapper_classes'] ) {
				$wrapper_classes .= ' xts-images-global-lightbox';
			}

			if ( 'yes' === $element_args['lightbox_gallery'] ) {
				$wrapper_classes .= ' xts-lightbox-gallery';
			}
		}
		if ( $element_args['extra_wrapper_classes'] ) {
			$wrapper_classes .= ' ' . $element_args['extra_wrapper_classes'];
		}
		if ( 'yes' === $element_args['animation_in_view'] ) {
			xts_enqueue_js_script( 'items-animation-in-view' );
			$wrapper_classes .= ' xts-in-view-animation';
		}
		if ( 'disabled' !== $element_args['caption'] ) {
			$wrapper_classes .= ' xts-caption-' . $element_args['caption'];
		}
		if ( 'yes' === $element_args['masonry'] ) {
			wp_enqueue_script( 'imagesloaded' );
			xts_enqueue_js_library( 'isotope-bundle' );
			xts_enqueue_js_script( 'masonry-layout' );
			$wrapper_classes .= ' xts-masonry-layout';
		}
		if ( 'yes' === $element_args['different_images'] ) {
			$wrapper_classes .= ' xts-different-images';
		}
		if ( 'yes' === $element_args['gallery_thumbs'] ) {
			$wrapper_classes .= ' xts-with-thumbs';
		}
		if ( 'none' !== $element_args['hover_effect'] ) {
			$wrapper_classes .= ' xts-image-hover-' . $element_args['hover_effect'];
		}
		if ( 'yes' === $element_args['image_shadow_switcher'] ) {
			$wrapper_classes .= ' xts-with-shadow';
		}
		$wrapper_classes .= ' xts-autoplay-animations-off';

		// Custom links.
		if ( 'custom_link' === $element_args['click_action'] && ! xts_elementor_is_edit_mode() ) {
			$custom_links = explode( "\n", $element_args['custom_links'] );
		}

		// Lazy loading.
		$lazy_module = Modules::get( 'lazy-loading' );
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		}

		?>
			<div class="xts-image-gallery<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo wp_kses( $carousel_attrs, true ); ?> data-animation-delay="<?php echo esc_attr( $element_args['xts_animation_delay_items'] ); ?>">
				<?php foreach ( $element_args['gallery'] as $key => $image ) : ?>
					<?php
					$image_default_args = array(
						// Content.
						'id' => '',
					);

					$image = wp_parse_args( $image, $image_default_args );

					$column_classes = '';
					$link_attrs     = '';
					$image_attrs    = '';
					$caption_text   = wp_get_attachment_caption( $image['id'] );

					if ( 'lightbox' === $element_args['click_action'] && ! xts_elementor_is_edit_mode() ) {
						$image_data = wp_get_attachment_image_src( $image['id'], 'full' );

						if ( ! $xts_image_gallery_index ) {
							$xts_image_gallery_index = 0;
						}

						$index = $key;

						if ( 'yes' === $element_args['global_lightbox'] ) {
							$index = $xts_image_gallery_index;
						}

						if ( $image_data ) {
							$link_attrs = xts_get_link_attrs(
								array(
									'url'   => $image_data[0],
									'class' => 'xts-image-inner',
									'data'  => 'data-width="' . esc_attr( $image_data[1] ) . '" data-height="' . esc_attr( $image_data[2] ) . '" data-index="' . esc_attr( $index ) . '" data-elementor-open-lightbox="no"',
								)
							);
						}
					}

					if ( 'custom_link' === $element_args['click_action'] && isset( $custom_links[ $key ] ) && ! xts_elementor_is_edit_mode() ) {
						$link_attrs = xts_get_link_attrs(
							array(
								'class' => 'xts-image-inner',
								'url'   => $custom_links[ $key ],
							)
						);
					}

					if ( 'lightbox' === $element_args['caption'] ) {
						$image_attrs .= ' title="' . esc_attr( $caption_text ) . '"';
					}

					// Image alt.
					$image_alt = get_post_meta( $image['id'], '_wp_attachment_image_alt', true );

					if ( $image_alt ) {
						$image_attrs .= ' alt="' . esc_attr( $image_alt ) . '"';
					} else {
						$image_attrs .= ' alt="' . esc_attr( get_the_title( $image['id'] ) ) . '"';
					}

					if ( in_array( $key + 1, $different_images_position, false ) && 'yes' === $element_args['different_images'] ) { // phpcs:ignore
						$column_classes .= ' xts-wide';
					}

					// Animations.
					if ( 'yes' === $element_args['animation_in_view'] && $element_args['xts_animation_items'] ) {
						$column_classes .= ' xts-animation-' . $element_args['xts_animation_items'];
						$column_classes .= ' xts-animation-' . $element_args['xts_animation_duration_items'];
					}

					// Image url.
					$image_url = xts_get_image_url( $image['id'], 'gallery', $element_args );

					if ( ! $image_url ) {
						continue;
					}

					?>
					<div class="xts-col<?php echo esc_attr( $column_classes ); ?>">
						<?php if ( 'yes' === $element_args['center_mode'] && 'yes' === $element_args['center_mode_opacity'] ) : ?>
							<div class="xts-image-wrapper">
						<?php endif; ?>
							<figure class="xts-image">
								<?php if ( 'nothing' !== $element_args['click_action'] && $link_attrs ) : ?>
									<a <?php echo wp_kses( $link_attrs, true ); ?>>
								<?php else : ?>
									<div class="xts-image-inner">
								<?php endif ?>

									<?php echo apply_filters( 'xts_image', '<img src="' . esc_url( $image_url ) . '" ' . $image_attrs . '>' ); // phpcs:ignore ?>

								<?php if ( 'nothing' !== $element_args['click_action'] && $link_attrs ) : ?>
									</a>
								<?php else : ?>
									</div>
								<?php endif ?>

								<?php if ( 'on-image' === $element_args['caption'] && $caption_text ) : ?>
									<figcaption class="xts-image-caption">
										<?php echo esc_html( $caption_text ); ?>
									</figcaption>
								<?php endif ?>
							</figure>

							<?php if ( 'under-image' === $element_args['caption'] && $caption_text ) : ?>
								<div class="xts-image-caption">
									<?php echo esc_html( $caption_text ); ?>
								</div>
							<?php endif ?>
						<?php if ( 'yes' === $element_args['center_mode'] && 'yes' === $element_args['center_mode_opacity'] ) : ?>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( 'lightbox' === $element_args['click_action'] && 'yes' === $element_args['global_lightbox'] ) : ?>
						<?php $xts_image_gallery_index++; ?>
					<?php endif; ?>

				<?php endforeach; ?>
			</div>

			<?php if ( 'yes' === $element_args['gallery_thumbs'] ) : ?>
				<?php
				$thumbs_slider_attrs = array(
					'arrows'                => 'yes',
					'dots'                  => 'no',
					'carousel_items'        => array( 'size' => 5 ),
					'carousel_items_tablet' => array( 'size' => 3 ),
					'carousel_items_mobile' => array( 'size' => 3 ),
				);

				$wrapper_classes  = '';
				$wrapper_classes .= xts_get_row_classes( 5, 3, 3 );

				if ( xts_get_opt( 'disable_carousel_mobile_devices' ) ) {
					$wrapper_classes .= ' xts-disable-md';
				}

				?>

				<div class="xts-gallery-thumbs xts-lib-swiper xts-carousel <?php echo esc_attr( $wrapper_classes ); ?>" <?php echo xts_get_carousel_atts( $thumbs_slider_attrs ); // phpcs:ignore ?>>
					<?php foreach ( $element_args['gallery'] as $key => $image ) : ?>
						<div class="xts-col">
							<div class="xts-gallery-thumb">
								<?php echo apply_filters( 'xts_image', '<img src="' . esc_url( xts_get_image_url( $image['id'], 'gallery_thumbs_image', $element_args ) ) . '">' ); // phpcs:ignore ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php

		// Lazy loading.
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init();
		}
	}
}
