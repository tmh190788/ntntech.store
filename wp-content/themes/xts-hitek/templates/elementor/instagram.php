<?php
/**
 * Instagram template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Modules;

if ( ! function_exists( 'xts_instagram_template' ) ) {
	/**
	 * Instagram template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @return void
	 */
	function xts_instagram_template( $element_args ) {
		$default_args = array(
			// Content.
			'source'                       => 'ajax',
			'description'                  => '',
			'link_is_external'             => 'no',
			'link_nofollow'                => 'no',
			'custom_images_size'           => 'thumbnail',

			// API.
			'api_images_per_page'          => array( 'size' => 10 ),

			// Custom images.
			'custom_images'                => array(),
			'custom_images_link'           => '',

			// Layout.
			'view'                         => 'grid',
			'columns'                      => array( 'size' => 3 ),
			'columns_tablet'               => array( 'size' => '' ),
			'columns_mobile'               => array( 'size' => '' ),
			'spacing'                      => xts_get_default_value( 'items_gap' ),
			'show_meta'                    => 'yes',
			'different_images'             => 'no',
			'different_images_position'    => '2,5,8,9',

			// Carousel.
			'carousel_items'               => array( 'size' => 3 ),
			'carousel_items_tablet'        => array( 'size' => '' ),
			'carousel_items_mobile'        => array( 'size' => '' ),
			'carousel_spacing'             => xts_get_default_value( 'items_gap' ),

			// Extra.
			'lazy_loading'                 => 'no',
			'animation_in_view'            => 'no',
			'xts_animation_items'          => '',
			'xts_animation_duration_items' => 'normal',
			'xts_animation_delay_items'    => '',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes           = '';
		$images_wrapper_classes    = '';
		$carousel_attrs            = '';
		$different_images_position = explode( ',', $element_args['different_images_position'] );

		// Wrapper classes.
		$wrapper_classes .= ' xts-autoplay-animations-off';

		if ( 'carousel' === $element_args['view'] ) {
			$images_wrapper_classes .= xts_get_carousel_classes( $element_args );
			$images_wrapper_classes .= xts_get_row_classes( $element_args['carousel_items']['size'], $element_args['carousel_items_tablet']['size'], $element_args['carousel_items_mobile']['size'], $element_args['carousel_spacing'] );

			$carousel_attrs .= xts_get_carousel_atts( $element_args );
		} else {
			$images_wrapper_classes .= xts_get_row_classes( $element_args['columns']['size'], $element_args['columns_tablet']['size'], $element_args['columns_mobile']['size'], $element_args['spacing'] );
		}

		if ( 'yes' === $element_args['animation_in_view'] ) {
			xts_enqueue_js_script( 'items-animation-in-view' );
			$wrapper_classes .= ' xts-in-view-animation';
		}

		if ( 'yes' === $element_args['different_images'] ) {
			wp_enqueue_script( 'imagesloaded' );
			xts_enqueue_js_library( 'isotope-bundle' );
			xts_enqueue_js_script( 'masonry-layout' );
			$images_wrapper_classes .= ' xts-masonry-layout';
			$images_wrapper_classes .= ' xts-different-images';
		}

		// Lazy loading.
		$lazy_module = Modules::get( 'lazy-loading' );
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		}

		// Image.
		$element_args['custom_images_custom_dimension'] = isset( $element_args['custom_images_custom_dimension']['width'] ) && $element_args['custom_images_custom_dimension']['width'] ? $element_args['custom_images_custom_dimension'] : array(
			'width'  => 128,
			'height' => 128,
		);

		$images = xts_get_instagram_images_data( $element_args );

		if ( is_wp_error( $images ) ) {
			?>
				<div class="xts-notification xts-color-info">
					<?php echo esc_html( $images->get_error_message() ); ?>
				</div>
			<?php
			return;
		}

		?>
			<div class="xts-insta<?php echo esc_attr( $wrapper_classes ); ?>" data-animation-delay="<?php echo esc_attr( $element_args['xts_animation_delay_items'] ); ?>">
				<?php if ( $element_args['description'] ) : ?>
					<div class="xts-insta-content xts-fill">
						<div class="xts-insta-desc xts-reset-all-last xts-reset-mb-10">
							<?php echo do_shortcode( $element_args['description'] ); ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="xts-insta-items<?php echo esc_attr( $images_wrapper_classes ); ?>" <?php echo wp_kses( $carousel_attrs, true ); ?>>
					<?php foreach ( $images as $key => $image_data ) : ?>
						<?php
						$column_classes = '';

						if ( in_array( $key + 1, $different_images_position ) && 'yes' === $element_args['different_images'] ) { // phpcs:ignore
							$column_classes = ' xts-wide';
						}

						// Animations.
						if ( 'yes' === $element_args['animation_in_view'] && $element_args['xts_animation_items'] ) {
							$column_classes .= ' xts-animation-' . $element_args['xts_animation_items'];
							$column_classes .= ' xts-animation-' . $element_args['xts_animation_duration_items'];
						}

						// Link.
						$link_attrs = xts_get_link_attrs(
							array(
								'class'       => 'xts-insta-link xts-fill',
								'url'         => $image_data['link_url'],
								'is_external' => 'yes' === $element_args['link_is_external'] ? 'on' : 'off',
								'nofollow'    => 'yes' === $element_args['link_nofollow'] ? 'on' : 'off',
							)
						);

						// Image.
						if ( isset( $image_data['image_id'] ) && $image_data['image_id'] ) {
							$image = xts_get_image_html(
								array(
									'image_size' => $element_args['custom_images_size'],
									'image_custom_dimension' => $element_args['custom_images_custom_dimension'],
									'image'      => array(
										'id' => $image_data['image_id'],
									),
								),
								'image'
							);
						} else {
							$image = apply_filters( 'xts_image', '<img src="' . esc_url( $image_data['image_url'] ) . '">' );
						}

						?>
							<div class="xts-col<?php echo esc_attr( $column_classes ); ?>">
								<div class="xts-insta-item">
									<?php if ( $image_data['link_url'] ) : ?>
										<a <?php echo wp_kses( $link_attrs, true ); ?>></a>
									<?php endif; ?>

									<?php echo wp_kses( $image, 'xts_media' ); ?>

									<?php if ( 'yes' === $element_args['show_meta'] ) : ?>
										<div class="xts-insta-meta xts-fill">
											<span class="xts-insta-likes">
												<?php echo esc_html( xts_get_pretty_number( $image_data['likes_count'] ) ); ?>
											</span>

											<span class="xts-insta-comments">
												<?php echo esc_html( xts_get_pretty_number( $image_data['comments_count'] ) ); ?>
											</span>
										</div>
									<?php endif; ?>
								</div>
							</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php

		// Lazy loading.
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init();
		}
	}
}
if ( ! function_exists( 'xts_insert_image_from_url' ) ) {
	/**
	 * Insert image from url.
	 *
	 * @param string $url Image url.
	 *
	 * @return int|WP_Error
	 */
	function xts_insert_image_from_url( $url ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $url, $matches );
		$img_name  = wp_basename( $matches[0] );

		if ( ! xts_get_image_id_by_slug( $img_name ) ) {
			add_filter( 'intermediate_image_sizes', 'xts_get_instagram_insert_image_sizes', 10 );
			$upload = media_sideload_image( $url, 0, $img_name, 'id' );
			remove_action( 'intermediate_image_sizes', 'xts_get_instagram_insert_image_sizes', 10 );

			if ( is_wp_error( $upload ) ) {
				return $upload->get_error_message();
			}

			return $upload;
		} else {
			return xts_get_image_id_by_slug( $img_name );
		}
	}
}

if ( ! function_exists( 'xts_get_image_id_by_slug' ) ) {
	/**
	 * Default images sizes.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_instagram_insert_image_sizes() {
		return array( 'thumbnail' );
	}
}

if ( ! function_exists( 'xts_get_image_id_by_slug' ) ) {
	/**
	 * Get image id by slug
	 *
	 * @param string $slug Image slug.
	 *
	 * @return int
	 */
	function xts_get_image_id_by_slug( $slug ) {
		$args = array(
			'post_type'      => 'attachment',
			'name'           => sanitize_title( $slug ),
			'posts_per_page' => 1,
		);

		$post = get_posts( $args );

		if ( ! $post ) {
			return '';
		}

		return $post[0]->ID;
	}
}

if ( ! function_exists( 'xts_get_instagram_images_data' ) ) {
	/**
	 * Gets a photo by instagram username.
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Element args.
	 *
	 * @return array|WP_Error
	 */
	function xts_get_instagram_images_data( $element_args ) {
		if ( ! xts_is_core_module_exists() ) {
			return array();
		}

		if ( 'api' === $element_args['source'] ) {
			$transient_name = xts_get_instagram_transient_name( $element_args );
			$images_data    = get_transient( $transient_name );
			$cache_time     = apply_filters( 'xts_instagram_cache_time', HOUR_IN_SECONDS * 2 );

			if ( ! $images_data ) {
				$images_data = xts_get_api_instagram_images_data( $element_args['api_images_per_page']['size'] );

				if ( $images_data ) {
					$images_data = xts_compress( maybe_serialize( $images_data ) );
					set_transient( $transient_name, $images_data, $cache_time );
				}
			}

			if ( $images_data ) {
				return maybe_unserialize( xts_decompress( $images_data ) );
			}
		}

		return xts_get_custom_instagram_images_data( $element_args );
	}
}

if ( ! function_exists( 'xts_get_instagram_transient_name' ) ) {
	/**
	 * Get transient instagram name.
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Element args.
	 *
	 * @return string
	 */
	function xts_get_instagram_transient_name( $element_args ) {
		$source   = $element_args['source'];
		$per_page = $element_args['api_images_per_page']['size'];
		$name     = get_option( 'xts_instagram_account_id' );

		return 'xts-insta-' . sanitize_title_with_dashes( strtolower( $name ) ) . '-' . $per_page . '-' . $source;
	}
}

if ( ! function_exists( 'xts_get_api_instagram_images_data' ) ) {
	/**
	 * Get a photo from instagram API.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $images_per_page Images per page.
	 *
	 * @return array|WP_Error
	 */
	function xts_get_api_instagram_images_data( $images_per_page ) {
		$instagram_account_id   = get_option( 'xts_instagram_account_id' );
		$instagram_access_token = get_option( 'xts_instagram_access_token' );

		if ( ! $instagram_access_token || ! $instagram_account_id ) {
			return new WP_Error( 'no_token', esc_html__( 'You need connect your Instagram account in Theme settings -> General -> Connect instagram account', 'xts-theme' ) );
		}

		$images_data         = wp_remote_get( 'https://graph.facebook.com/v5.0/' . $instagram_account_id . '/media?access_token=' . $instagram_access_token . '&fields=timestamp,caption,media_type,media_url,thumbnail_url,like_count,comments_count,permalink' );
		$images_data_decoded = json_decode( $images_data['body'] );

		if ( is_object( $images_data_decoded ) ) {
			if ( property_exists( $images_data_decoded, 'error' ) ) {
				return new WP_Error( 'no_images', $images_data_decoded->error->message );
			}
		} else {
			return new WP_Error( 'no_images', esc_html__( 'Instagram API did not return any images.', 'xts-theme' ) );
		}

		$images_data_output = array();

		$images_data = array_slice( $images_data_decoded->data, 0, $images_per_page );

		foreach ( $images_data as $image_data ) {
			$caption = esc_html__( 'Instagram Image', 'xts-theme' );

			if ( isset( $image_data->caption ) ) {
				$caption = $image_data->caption;
			}

			if ( 'VIDEO' === $image_data->media_type ) {
				$image_url = $image_data->thumbnail_url;
			} else {
				$image_url = $image_data->media_url;
			}

			$images_data_output[] = array(
				'caption'        => $caption,
				'link_url'       => preg_replace( '/^https:/i', '', $image_data->permalink ),
				'image_url'      => preg_replace( '/^https:/i', '', $image_url ),
				'image_id'       => xts_insert_image_from_url( $image_url ),
				'comments_count' => $image_data->comments_count,
				'likes_count'    => $image_data->like_count,
				'media_type'     => $image_data->media_type,
			);
		}

		return $images_data_output;
	}
}

if ( ! function_exists( 'xts_get_custom_instagram_images_data' ) ) {
	/**
	 * Get custom instagram images.
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Element args.
	 *
	 * @return array|WP_Error
	 */
	function xts_get_custom_instagram_images_data( $element_args ) {
		$images = array();

		foreach ( $element_args['custom_images'] as $image ) {
			$image_url = xts_get_image_url(
				$image['id'],
				'image',
				array(
					'image_size'             => $element_args['custom_images_size'],
					'image_custom_dimension' => $element_args['custom_images_custom_dimension'],
				)
			);

			if ( ! $image_url ) {
				continue;
			}

			$images[] = array(
				'image_id'       => $image['id'],
				'image_url'      => $image_url,
				'link_url'       => $element_args['custom_images_link'],
				'likes_count'    => wp_rand( apply_filters( 'xts_instagram_likes_min_count', 1000 ), apply_filters( 'xts_instagram_likes_max_count', 10000 ) ),
				'comments_count' => wp_rand( apply_filters( 'xts_instagram_comments_min_count', 0 ), apply_filters( 'xts_instagram_comments_min_count', 1000 ) ),
			);
		}

		if ( ! $images ) {
			return new WP_Error( 'no_images', esc_html__( 'You need to upload your images manually to the element if you want to load them from your website. Otherwise you will need to connect your real Instagram account via API.', 'xts-theme' ) );
		}

		return $images;
	}
}
