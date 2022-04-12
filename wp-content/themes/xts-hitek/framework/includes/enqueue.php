<?php
/**
 * Enqueue functions.
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_framework_enqueue_styles' ) ) {
	/**
	 * Enqueue styles.
	 *
	 * @since 1.0.0
	 */
	function xts_framework_enqueue_styles() {
		$minified  = xts_get_opt( 'minified_css' ) ? '.min' : '';
		$is_rtl    = is_rtl() ? '-rtl' : '';
		$style_url = XTS_THEME_URL . '/style' . $is_rtl . $minified . '.css';

		if ( xts_is_elementor_installed() ) {
			Elementor\Plugin::$instance->frontend->enqueue_styles();
		}

		if ( xts_is_elementor_installed() && xts_get_opt( 'elementor_optimized_css' ) && ! xts_elementor_is_edit_mode() && ! xts_elementor_is_preview_mode() ) {
			wp_deregister_style( 'elementor-frontend' );
			wp_dequeue_style( 'elementor-frontend' );

			wp_enqueue_style( 'elementor-frontend', XTS_THEME_URL . '/css/elementor-optimized' . $is_rtl . $minified . '.css', array(), XTS_VERSION );
		}

		if ( xts_get_opt( 'always_use_elementor_font_awesome' ) ) {
			wp_enqueue_style( 'elementor-icons-fa-solid' );
			wp_enqueue_style( 'elementor-icons-fa-brands' );
			wp_enqueue_style( 'elementor-icons-fa-regular' );
		}

		wp_enqueue_style( 'xts-style', $style_url, array(), XTS_VERSION );

		// Load typekit fonts.
		$typekit_id = xts_get_opt( 'typekit_id' );

		if ( $typekit_id ) {
			wp_enqueue_style( 'xts-typekit', 'https://use.typekit.net/' . esc_attr( $typekit_id ) . '.css', array(), XTS_VERSION );
		}

		if ( xts_is_elementor_installed() && ( xts_elementor_is_edit_mode() || xts_elementor_is_preview_mode() || is_singular( 'xts-html-block' ) ) ) {
			wp_enqueue_style( 'xts-elementor-editor-frontend-style', XTS_FRAMEWORK_URL . '/integration/elementor/assets/css/editor.css', array(), XTS_VERSION );
			wp_enqueue_style( 'xts-admin-frontend-style', XTS_FRAMEWORK_URL . '/assets/css/style-frontend.css', array(), XTS_VERSION );
		}

		if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
			wp_enqueue_style( 'xts-admin-frontend-style', XTS_FRAMEWORK_URL . '/assets/css/style-frontend.css', array(), XTS_VERSION );
		}

		if ( ! xts_is_elementor_installed() ) {
			wp_enqueue_style( 'xts-swiper-library', XTS_THEME_URL . '/css/swiper' . $minified . '.css', array(), XTS_VERSION );
		}
	}

	add_action( 'wp_enqueue_scripts', 'xts_framework_enqueue_styles', 20 );
}

if ( ! function_exists( 'xts_dequeue_styles' ) ) {
	/**
	 * Dequeue styles.
	 *
	 * @since 1.0.0
	 */
	function xts_dequeue_styles() {
		$dequeue_styles = explode( ',', xts_get_opt( 'dequeue_styles' ) );

		if ( is_array( $dequeue_styles ) ) {
			foreach ( $dequeue_styles as $style ) {
				wp_deregister_style( trim( $style ) );
				wp_dequeue_style( trim( $style ) );
			}
		}

		if ( ! xts_get_opt( 'gutenberg_css' ) ) {
			wp_deregister_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library' );

			wp_deregister_style( 'wc-block-style' );
			wp_dequeue_style( 'wc-block-style' );
		}

		wp_deregister_style( 'contact-form-7' );
		wp_dequeue_style( 'contact-form-7' );
		wp_deregister_style( 'contact-form-7-rtl' );
		wp_dequeue_style( 'contact-form-7-rtl' );
	}

	add_action( 'wp_enqueue_scripts', 'xts_dequeue_styles', 2000 );
}

if ( ! function_exists( 'xts_is_combined_needed' ) ) {
	/**
	 * Is combined needed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Combined key.
	 *
	 * @return bool
	 */
	function xts_is_combined_needed( $key ) {
		return xts_get_opt( $key ) || ( xts_is_elementor_installed() && ( xts_elementor_is_edit_mode() || xts_elementor_is_preview_mode() ) );
	}
}

if ( ! function_exists( 'xts_register_libraries_scripts' ) ) {
	/**
	 * Register libraries scripts.
	 *
	 * @since 1.0.0
	 */
	function xts_register_libraries_scripts() {
		$config   = xts_get_config( 'framework-js-libraries' );
		$minified = xts_get_opt( 'minified_js' ) ? '.min' : '';

		if ( xts_is_combined_needed( 'libraries_combined_js' ) ) {
			return;
		}

		foreach ( $config as $key => $libraries ) {
			foreach ( $libraries as $library ) {
				$src = XTS_THEME_URL . $library['file'] . $minified . '.js';

				$dependency = 'device' === $library['name'] ? array( 'jquery' ) : array();

				wp_register_script( 'xts-' . $library['name'] . '-library', $src, $dependency, XTS_VERSION, $library['in_footer'] );
			}
		}
	}

	add_action( 'wp_enqueue_scripts', 'xts_register_libraries_scripts', 10 );
}

if ( ! function_exists( 'xts_register_scripts' ) ) {
	/**
	 * Register scripts.
	 *
	 * @since 1.0.0
	 */
	function xts_register_scripts() {
		$config   = xts_get_js_scripts();
		$minified = xts_get_opt( 'minified_js' ) ? '.min' : '';

		if ( xts_is_combined_needed( 'scripts_combined_js' ) ) {
			return;
		}

		foreach ( $config as $key => $scripts ) {
			foreach ( $scripts as $script ) {
				$src = XTS_THEME_URL . $script['file'] . $minified . '.js';

				wp_register_script( 'xts-' . $script['name'], $src, array(), XTS_VERSION, $script['in_footer'] );
			}
		}
	}

	add_action( 'wp_enqueue_scripts', 'xts_register_scripts', 20 );
}

if ( ! function_exists( 'xts_enqueue_base_scripts' ) ) {
	/**
	 * Enqueue base scripts.
	 *
	 * @since 1.0.0
	 */
	function xts_enqueue_base_scripts() {
		$minified = xts_get_opt( 'minified_js' ) ? '.min' : '';

		// General.
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
		if ( xts_is_elementor_installed() ) {
			Elementor\Plugin::$instance->frontend->enqueue_scripts();
		}

		// Libraries.
		if ( xts_is_combined_needed( 'libraries_combined_js' ) ) {
			wp_enqueue_script( 'xts-libraries', XTS_THEME_URL . '/js/combine-libraries' . $minified . '.js', array( 'jquery' ), XTS_VERSION, true );
		} else {
			xts_enqueue_js_library( 'device' );

			if ( ( xts_get_opt( 'ajax_shop' ) && xts_is_shop_archive() ) ) {
				xts_enqueue_js_library( 'pjax' );
			}

			if ( ( xts_get_opt( 'ajax_portfolio' ) && xts_is_portfolio_archive() ) ) {
				xts_enqueue_js_library( 'pjax' );
			}

			if ( ! xts_is_woocommerce_installed() ) {
				xts_enqueue_js_library( 'cookie' );
			}

			$config = xts_get_config( 'framework-js-libraries' );
			foreach ( $config as $key => $libraries ) {
				foreach ( $libraries as $library ) {
					if ( 'always' === xts_get_opt( $library['name'] . '_library' ) ) {
						xts_enqueue_js_library( $library['name'] );
					}
				}
			}
		}

		if ( ! xts_is_elementor_installed() ) {
			wp_enqueue_script( 'xts-swiper-library', XTS_THEME_URL . '/js/swiper' . $minified . '.js', array(), XTS_VERSION, true );
		}

		if ( 'always' === xts_get_opt( 'swiper_library' ) ) {
			wp_enqueue_script( 'swiper' );
		}

		if ( 'always' === xts_get_opt( 'waypoints_library' ) ) {
			wp_enqueue_script( 'elementor-waypoints' );
		}

		// Scripts.
		if ( xts_is_combined_needed( 'scripts_combined_js' ) ) {
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'xts-scripts', XTS_THEME_URL . '/js/scripts/combine-scripts' . $minified . '.js', array(), XTS_VERSION, true );
		} else {
			xts_enqueue_js_script( 'scripts' );
			xts_enqueue_js_script( 'hide-notices' );

			if ( ( xts_get_opt( 'ajax_shop' ) && xts_is_shop_archive() ) ) {
				xts_enqueue_js_script( 'ajax-shop' );
			}

			if ( ( xts_get_opt( 'ajax_portfolio' ) && xts_is_portfolio_archive() ) ) {
				xts_enqueue_js_script( 'ajax-portfolio' );
			}

			if ( xts_get_opt( 'menu_overlay' ) ) {
				xts_enqueue_js_script( 'menu-overlay' );
			}

			$scripts_always = xts_get_opt( 'scripts_always_use' );
			if ( is_array( $scripts_always ) ) {
				foreach ( $scripts_always as $script ) {
					xts_enqueue_js_script( $script );
				}
			}
		}

		wp_add_inline_script( 'xts-scripts', xts_get_custom_js() );
		wp_localize_script( 'xts-scripts', 'xts_settings', xts_get_localized_string_array() );

		// Sticky sidebar.
		if (
			( ( is_singular( 'post' ) || xts_is_blog_archive() ) && xts_get_opt( 'blog_sidebar_sticky' ) ) ||
			( xts_is_shop_archive() && xts_get_opt( 'shop_sidebar_sticky' ) ) ||
			( ( is_singular( 'product' ) || is_singular( 'xts-template' ) ) && xts_get_opt( 'single_product_sidebar_sticky' ) ) ||
			( xts_get_opt( 'sidebar_sticky' ) )
		) {
			xts_enqueue_js_library( 'sticky-kit' );
			xts_enqueue_js_script( 'sticky-sidebar' );
		}
	}

	add_action( 'wp_enqueue_scripts', 'xts_enqueue_base_scripts', 20 );
}

if ( ! function_exists( 'xts_enqueue_js_script' ) ) {
	/**
	 * Enqueue js script.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key        Script name.
	 * @param string $responsive Responsive key.
	 */
	function xts_enqueue_js_script( $key, $responsive = '' ) {
		$config          = xts_get_js_scripts();
		$scripts_not_use = xts_get_opt( 'scripts_not_use' );

		if ( ! isset( $config[ $key ] ) || xts_is_combined_needed( 'scripts_combined_js' ) ) {
			return;
		}

		foreach ( $config[ $key ] as $data ) {
			if ( ( 'only_mobile' === $responsive && ! wp_is_mobile() ) || ( 'only_desktop' === $responsive && wp_is_mobile() ) || ( is_array( $scripts_not_use ) && in_array( $data['name'], $scripts_not_use ) ) ) { // phpcs:ignore
				continue;
			}

			wp_enqueue_script( 'xts-' . $data['name'] );
		}
	}
}

if ( ! function_exists( 'xts_enqueue_js_library' ) ) {
	/**
	 * Enqueue js library.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key        Script name.
	 * @param string $responsive Responsive key.
	 */
	function xts_enqueue_js_library( $key, $responsive = '' ) {
		$config = xts_get_config( 'framework-js-libraries' );

		if ( ! isset( $config[ $key ] ) || xts_is_combined_needed( 'libraries_combined_js' ) ) {
			return;
		}

		foreach ( $config[ $key ] as $data ) {
			if ( ( 'only_mobile' === $responsive && ! wp_is_mobile() ) || ( 'only_desktop' === $responsive && wp_is_mobile() ) || 'not_use' === xts_get_opt( $data['name'] . '_library' ) ) {
				continue;
			}

			wp_enqueue_script( 'xts-' . $data['name'] . '-library' );
		}
	}
}

if ( ! function_exists( 'xts_dequeue_scripts' ) ) {
	/**
	 * Dequeue scripts.
	 *
	 * @since 1.0.0
	 */
	function xts_dequeue_scripts() {
		if ( 'zoom' !== xts_get_opt( 'single_product_main_gallery_click_action' ) ) {
			wp_dequeue_script( 'zoom' );
		}
	}

	add_action( 'wp_enqueue_scripts', 'xts_dequeue_scripts', 2000 );
}

if ( ! function_exists( 'xts_get_localized_string_array' ) ) {
	/**
	 * Get localize array
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_localized_string_array() {
		$menu_hash_transient = get_transient( 'xts-menu-hash-time' );
		if ( false === $menu_hash_transient ) {
			$menu_hash_transient = time();
			set_transient( 'xts-menu-hash-time', $menu_hash_transient );
		}

		return [
			'elementor_no_gap'                            => xts_elementor_no_gap(),
			'ajaxurl'                                     => admin_url( 'admin-ajax.php' ),
			'header_clone'                                => xts_get_config( 'header-clone-structure' ),
			'action_after_add_to_cart_cart_url'           => xts_is_woocommerce_installed() ? esc_url( wc_get_cart_url() ) : '',
			'cart_hash_key'                               => apply_filters( 'woocommerce_cart_hash_key', 'wc_cart_hash_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() ) ),
			'fragment_name'                               => apply_filters( 'woocommerce_cart_fragment_name', 'wc_fragments_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() ) ),
			'cart_redirect_after_add'                     => get_option( 'woocommerce_cart_redirect_after_add' ),
			'home_url'                                    => home_url( '/' ),
			'shop_url'                                    => xts_is_woocommerce_installed() ? esc_url( wc_get_page_permalink( 'shop' ) ) : '',
			'is_multisite'                                => is_multisite(),
			'current_blog_id'                             => get_current_blog_id(),
			'vimeo_library_url'                           => XTS_THEME_URL . '/js/vimeo-player.min.js',
			'theme_url'                                   => XTS_THEME_URL,
			'menu_storage_key'                            => apply_filters( 'xts_menu_storage_key', 'xts_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() . $menu_hash_transient ) ),
			'photoswipe_template'                         => '<div class="pswp" aria-hidden="true" role="dialog" tabindex="-1"><div class="pswp__bg"></div><div class="pswp__scroll-wrap"><div class="pswp__container"><div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div></div><div class="pswp__ui pswp__ui--hidden"><div class="pswp__top-bar"><div class="pswp__counter"></div><button class="pswp__button pswp__button--close" title="' . esc_attr__( 'Close (Esc)', 'xts-theme' ) . '"></button> <button class="pswp__button pswp__button--share" title="' . esc_attr__( 'Share', 'xts-theme' ) . '"></button> <button class="pswp__button pswp__button--fs" title="' . esc_attr__( 'Toggle fullscreen', 'xts-theme' ) . '"></button> <button class="pswp__button pswp__button--zoom" title="' . esc_attr__( 'Zoom in/out', 'xts-theme' ) . '"></button><div class="pswp__preloader"><div class="pswp__preloader__icn"><div class="pswp__preloader__cut"><div class="pswp__preloader__donut"></div></div></div></div></div><div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"><div class="pswp__share-tooltip"></div></div><button class="pswp__button pswp__button--arrow--left" title="' . esc_attr__( 'Previous (arrow left)', 'xts-theme' ) . '"></button> <button class="pswp__button pswp__button--arrow--right" title="' . esc_attr__( 'Next (arrow right)', 'xts-theme' ) . '>"></button><div class="pswp__caption"><div class="pswp__caption__center"></div></div></div><div class="xts-pswp-gallery"></div></div></div>',

			'flickity_slider_element_next_text'           => esc_html__( 'Next', 'xts-theme' ),
			'flickity_slider_element_previous_text'       => esc_html__( 'Previous', 'xts-theme' ),
			'product_categories_placeholder'              => esc_html__( 'Select a category', 'xts-theme' ),
			'product_categories_no_results'               => esc_html__( 'No matches found', 'xts-theme' ),
			'all_results'                                 => esc_html__( 'View all results', 'xts-theme' ),
			'countdown_days'                              => esc_html__( 'days', 'xts-theme' ),
			'countdown_hours'                             => esc_html__( 'hr', 'xts-theme' ),
			'countdown_mins'                              => esc_html__( 'min', 'xts-theme' ),
			'countdown_sec'                               => esc_html__( 'sc', 'xts-theme' ),
			'photoswipe_facebook'                         => esc_html__( 'Share on Facebook', 'xts-theme' ),
			'photoswipe_pinterest'                        => esc_html__( 'Pin it', 'xts-theme' ),
			'photoswipe_twitter'                          => esc_html__( 'Tweet', 'xts-theme' ),
			'photoswipe_download_image'                   => esc_html__( 'Download image', 'xts-theme' ),
			'magnific_loading'                            => esc_html__( 'Loading...', 'xts-theme' ),
			'magnific_close'                              => esc_html__( 'Close (Esc)', 'xts-theme' ),
			'action_after_add_to_cart_title'              => esc_html__( 'Product was successfully added to your cart.', 'xts-theme' ),
			'action_after_add_to_cart_continue_shopping'  => esc_html__( 'Continue shopping', 'xts-theme' ),
			'action_after_add_to_cart_view_cart'          => esc_html__( 'View cart', 'xts-theme' ),
			'google_map_style_text'                       => esc_html__( 'Custom style', 'xts-theme' ),
			'quick_shop_add_to_cart_text'                 => esc_html__( 'Add to cart', 'xts-theme' ),
			'comment_images_upload_size_text'             => sprintf( esc_html__( 'Some files are too large. Allowed file size is %s.', 'xts-theme' ), size_format( xts_get_opt( 'single_product_comment_images_upload_size' ) * MB_IN_BYTES ) ),			// phpcs:ignore
			'comment_images_count_text'                  => sprintf( esc_html__( 'You can upload up to %s images to your review.', 'xts-theme' ), xts_get_opt( 'single_product_comment_images_count' ) ), // phpcs:ignore
			'comment_images_upload_mimes_text'           => sprintf( esc_html__( 'You are allowed to upload images only in %s formats.', 'xts-theme' ), apply_filters( 'xts_comment_images_upload_mimes', 'png, jpeg' ) ), // phpcs:ignore
			'comment_images_added_count_text'            => esc_html__( 'Added %s image(s)', 'xts-theme' ), // phpcs:ignore

			'promo_popup'                                 => xts_get_opt( 'promo_popup' ) ? 'yes' : 'no',
			'promo_popup_version'                         => xts_get_opt( 'promo_popup_version' ),
			'promo_popup_delay'                           => xts_get_opt( 'promo_popup_delay' ),
			'promo_popup_show_after'                      => xts_get_opt( 'promo_popup_show_after' ),
			'promo_popup_user_scroll'                     => xts_get_opt( 'promo_popup_user_scroll' ),
			'promo_popup_page_visited'                    => xts_get_opt( 'promo_popup_page_visited' ),
			'promo_popup_hide_mobile'                     => xts_get_opt( 'promo_popup_hide_mobile' ) ? 'yes' : 'no',
			'single_product_ajax_add_to_cart'             => xts_get_opt( 'single_product_ajax_add_to_cart' ) ? 'yes' : 'no',
			'single_product_variations_price'             => xts_get_opt( 'single_product_variations_price' ) ? 'yes' : 'no',
			'single_product_main_gallery_images_captions' => xts_get_opt( 'single_product_main_gallery_images_captions' ) ? 'yes' : 'no',
			'single_product_gallery_auto_height'          => xts_get_opt( 'single_product_main_gallery_auto_height' ) ? 'yes' : 'no',
			'cookies_version'                             => xts_get_opt( 'cookies_version' ) ? xts_get_opt( 'cookies_version' ) : 1,
			'action_after_add_to_cart'                    => xts_get_opt( 'action_after_add_to_cart' ),
			'action_after_add_to_cart_timeout'            => xts_get_opt( 'action_after_add_to_cart_timeout' ) ? 'yes' : 'no',
			'action_after_add_to_cart_timeout_number'     => xts_get_opt( 'action_after_add_to_cart_timeout_number' ),
			'product_categories_widget_accordion'         => xts_get_opt( 'product_categories_widget_accordion' ) ? 'yes' : 'no',
			'header_banner_version'                       => xts_get_opt( 'header_banner_version' ) ? xts_get_opt( 'header_banner_version' ) : 1,
			'header_banner_close_button'                  => xts_get_opt( 'header_banner_close_button' ) ? 'yes' : 'no',
			'header_banner'                               => xts_get_opt( 'header_banner' ) ? 'yes' : 'no',
			'product_quick_shop'                          => xts_get_opt( 'product_quick_shop' ) ? 'yes' : 'no',
			'ajax_shop'                                   => xts_get_opt( 'ajax_shop' ) ? 'yes' : 'no',
			'ajax_portfolio'                              => xts_get_opt( 'ajax_portfolio' ) ? 'yes' : 'no',
			'ajax_shop_scroll'                            => xts_get_opt( 'ajax_shop_scroll' ) ? 'yes' : 'no',
			'shop_filters_area_stop_close'                => xts_get_opt( 'shop_filters_area_stop_close' ) ? 'yes' : 'no',
			'menu_overlay'                                => xts_get_opt( 'menu_overlay' ) ? 'yes' : 'no',
			'comment_images_upload_size'                  => xts_get_opt( 'single_product_comment_images_upload_size' ) * MB_IN_BYTES,
			'comment_images_count'                        => xts_get_opt( 'single_product_comment_images_count' ),
			'lazy_loading_offset'                         => xts_get_opt( 'lazy_loading_offset' ),
			'sticky_sidebar_offset'                       => xts_get_opt( 'sidebar_sticky_offset' ),
			'site_width'                                  => xts_get_opt( 'site_width' ),
			'disable_carousel_mobile_devices'             => xts_get_opt( 'disable_carousel_mobile_devices' ) ? 'yes' : 'no',

			'elementor_negative_gap'                      => apply_filters( 'xts_elementor_negative_gap', true ),
			'single_product_sticky_offset'                => apply_filters( 'xts_single_product_sticky_offset', 150 ),
			'quick_view_in_popup_fix'                     => apply_filters( 'xts_quick_view_in_popup_fix', false ),
			'search_input_padding'                        => apply_filters( 'xts_search_input_padding', false ) ? 'yes' : 'no',
			'pjax_timeout'                                => apply_filters( 'xts_pjax_timeout', 5000 ),
			'ajax_shop_scroll_class'                      => apply_filters( 'xts_ajax_shop_scroll_class', '.xts-site-content' ),
			'ajax_shop_scroll_offset'                     => apply_filters( 'xts_ajax_shop_scroll_offset', 100 ),
			'cookies_expires'                             => apply_filters( 'xts_cookies_expires', 30 ),
			'ajax_dropdowns_save'                         => apply_filters( 'xts_ajax_dropdowns_save', true ),
			'preloader_delay'                             => apply_filters( 'xts_preloader_delay', 300 ),
			'comment_images_upload_mimes'                 => apply_filters(
				'xts_comment_images_upload_mimes',
				array(
					'jpg|jpeg|jpe' => 'image/jpeg',
					'png'          => 'image/png',
				)
			),

			'tooltip_top_selector'                        => xts_get_default_value( 'tooltip_top_selector' ),
			'tooltip_left_selector'                       => xts_get_default_value( 'tooltip_left_selector' ),
			'menu_animation_offset'                       => xts_get_default_value( 'menu_animation_offset' ),
			'slider_distortion_effect'                    => xts_get_default_value( 'slider_distortion_effect' ),
		];
	}
}
