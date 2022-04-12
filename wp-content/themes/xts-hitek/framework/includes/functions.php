<?php
/**
 * Framework functions.
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use Elementor\Utils;
use XTS\Framework\AJAX_Response;

if ( ! function_exists( 'xts_custom_admin_menu_order' ) ) {
	/**
	 * Filters the order of administration menu items.
	 *
	 * @since 1.0.0
	 *
	 * @param array $menu_order An ordered array of menu items.
	 *
	 * @return array
	 */
	function xts_custom_admin_menu_order( $menu_order ) {
		$custom_menu_order = array(
			'xts_dashboard',
			'xtemos_options',
			'edit.php?post_type=xts-portfolio',
			'edit.php?post_type=xts-html-block',
			'edit.php?post_type=xts-sidebar',
			'edit.php?post_type=xts-slide',
			'edit.php?post_type=xts-template',
			'edit.php?post_type=xts-size-guide',
			'xts-menu-separator',
		);

		foreach ( $menu_order as $index => $item ) {
			if ( in_array( $item, $custom_menu_order ) ) { // phpcs:ignore
				unset( $menu_order[ $index ] );
			}

			if ( 'xts_dashboard' === $item ) {
				$menu_order[] = 'xts-menu-separator';
				$menu_order[] = $item;
				$menu_order[] = 'xtemos_options';
				$menu_order[] = 'edit.php?post_type=xts-portfolio';
				$menu_order[] = 'edit.php?post_type=xts-html-block';
				$menu_order[] = 'edit.php?post_type=xts-sidebar';
				$menu_order[] = 'edit.php?post_type=xts-slide';
				$menu_order[] = 'edit.php?post_type=xts-template';
				$menu_order[] = 'edit.php?post_type=xts-size-guide';
			} elseif ( ! in_array( $item, array( 'xts-menu-separator' ), true ) ) {
				$menu_order[] = $item;
			}
		}

		return $menu_order;
	}

	add_filter( 'menu_order', 'xts_custom_admin_menu_order', 1000000 );
	add_filter( 'custom_menu_order', '__return_true' );
}

if ( ! function_exists( 'xts_scpo_single_posts_navigation_fix' ) ) {
	/**
	 * Fix for single post navigation with Simple Custom Post Order plugin.
	 *
	 * @param WP_Query $wp_query The WP_Query instance (passed by reference).
	 */
	function xts_scpo_single_posts_navigation_fix( $wp_query ) {
		unset( $wp_query->query['suppress_filters'] );
	}
}

if ( ! function_exists( 'xts_get_taxonomies_by_ids_autocomplete' ) ) {
	/**
	 * Autocomplete by taxonomies ids.
	 *
	 * @since 1.0.0
	 *
	 * @param array $ids Posts ids.
	 *
	 * @return array
	 */
	function xts_get_taxonomies_by_ids_autocomplete( $ids ) {
		$output = array();

		if ( ! $ids ) {
			return $output;
		}

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		foreach ( $ids as $id ) {
			$term = get_term( $id );

			if ( $term && ! is_wp_error( $term ) ) {
				$output[ $term->term_id ] = array(
					'name'  => $term->name,
					'value' => $term->term_id,
				);
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'xts_get_taxonomies_by_query_autocomplete' ) ) {
	/**
	 * Autocomplete by taxonomies.
	 *
	 * @since 1.0.0
	 */
	function xts_get_taxonomies_by_query_autocomplete() {
		$output = array();

		$args = array(
			'number'   => 5,
			'taxonomy' => $_POST['value'], // phpcs:ignore
			'search'   => $_POST['params']['term'], // phpcs:ignore
		);

		$terms = get_terms( $args );

		if ( count( $terms ) > 0 ) { // phpcs:ignore
			foreach ( $terms as $term ) {
				$output[] = array(
					'id'   => $term->term_id,
					'text' => $term->name,
				);
			}
		}

		AJAX_Response::send_response( $output );
	}

	add_action( 'wp_ajax_xts_get_taxonomies_by_query_autocomplete', 'xts_get_taxonomies_by_query_autocomplete' );
	add_action( 'wp_ajax_nopriv_xts_get_taxonomies_by_query_autocomplete', 'xts_get_taxonomies_by_query_autocomplete' );
}

if ( ! function_exists( 'xts_get_posts_by_query_autocomplete' ) ) {
	/**
	 * Autocomplete by post type.
	 *
	 * @since 1.0.0
	 */
	function xts_get_posts_by_query_autocomplete() {
		$output = array();
		$args   = array(
			'posts_per_page' => 5,
			'post_type'      => $_POST['value'], // phpcs:ignore
			's'              => $_POST['params']['term'], // phpcs:ignore
		);

		$posts = get_posts( $args );

		if ( count( $posts ) > 0 ) { // phpcs:ignore
			foreach ( $posts as $post ) {
				$output[] = array(
					'id'   => $post->ID,
					'text' => $post->post_title,
				);
			}
		}

		AJAX_Response::send_response( $output );
	}

	add_action( 'wp_ajax_xts_get_posts_by_query_autocomplete', 'xts_get_posts_by_query_autocomplete' );
	add_action( 'wp_ajax_nopriv_xts_get_posts_by_query_autocomplete', 'xts_get_posts_by_query_autocomplete' );
}

if ( ! function_exists( 'xts_get_posts_by_ids_autocomplete' ) ) {
	/**
	 * Autocomplete by posts by ids.
	 *
	 * @since 1.0.0
	 *
	 * @param array $ids Posts ids.
	 *
	 * @return array
	 */
	function xts_get_posts_by_ids_autocomplete( $ids ) {
		$output = array();

		if ( ! $ids ) {
			return $output;
		}

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		foreach ( $ids as $id ) {
			$post = get_post( $id );

			if ( $post ) {
				$output[ $post->ID ] = array(
					'name'  => $post->post_title,
					'value' => $post->ID,
				);
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'xts_get_site_content_container_classes' ) ) {
	/**
	 * Get classes for site content container div
	 *
	 * @since 1.0.0
	 *
	 * @param integer $page_id Page id.
	 *
	 * @return string
	 */
	function xts_get_site_content_container_classes( $page_id ) {
		$container_classes    = 'container';
		$page_for_projects    = xts_get_opt( 'portfolio_page' );
		$portfolio_full_width = ( $page_id === $page_for_projects || is_post_type_archive( 'xts-portfolio' ) || is_tax( 'xts-portfolio-cat' ) ) && xts_get_opt( 'portfolio_full_width' );

		if ( $portfolio_full_width || xts_is_elementor_full_width() ) {
			$container_classes = 'container-fluid container-no-gutters';
		}

		return $container_classes;
	}
}

if ( ! function_exists( 'xts_get_custom_js' ) ) {
	/**
	 * Get custom JS from theme settings
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function xts_get_custom_js() {
		$custom_js = xts_get_opt( 'js_global' );
		$js_ready  = xts_get_opt( 'js_document_ready' );

		ob_start();

		if ( $custom_js || $js_ready ) {
			if ( $custom_js ) {
				echo apply_filters( 'xts_custom_js_output', $custom_js );
			}

			if ( $js_ready ) {
				echo 'jQuery(document).ready(function() {';
				echo apply_filters( 'xts_custom_js_output', $js_ready );
				echo '});';
			}
		}

		return ob_get_clean();
	}
}

if ( ! function_exists( 'xts_get_single_post_classes' ) ) {
	/**
	 * Get blog single post classes
	 *
	 * @since 1.0.0
	 *
	 * @param array $custom_classes Custom classes.
	 *
	 * @return array
	 */
	function xts_get_single_post_classes( $custom_classes = array() ) {
		$design      = xts_get_opt( 'blog_single_design' );
		$post_format = get_post_format();
		$post_id     = get_the_ID();
		$audio       = get_post_meta( $post_id, '_xts_post_audio_url', true );
		$gallery     = get_post_meta( $post_id, '_xts_post_gallery', true );
		$classes     = array();

		$classes[] = 'xts-single-post';
		$classes[] = 'xts-design-' . $design;

		if ( 'video' === $post_format && xts_post_have_video( $post_id ) ) {
			$classes[] = 'xts-has-video';
		}

		if ( 'video' === $post_format && xts_post_have_video( $post_id ) && ! has_post_thumbnail() ) {
			$classes[] = 'has-post-thumbnail';
		}

		if ( 'audio' === $post_format && $audio ) {
			$classes[] = 'xts-has-audio';
		}

		if ( 'gallery' === $post_format && $gallery ) {
			$classes[] = 'xts-has-gallery';
			$classes[] = 'has-post-thumbnail';
		}

		if ( ( 'image' === $post_format || 'quote' === $post_format || 'link' === $post_format ) && xts_get_opt( 'blog_theme_post_formats', '0' ) ) {
			$classes[] = 'xts-format-design-mask';
		}

		if ( $custom_classes ) {
			$classes = array_merge( $classes, $custom_classes );
		}

		return $classes;
	}
}

if ( ! function_exists( 'xts_get_post_classes' ) ) {
	/**
	 * Get blog post classes
	 *
	 * @since 1.0.0
	 * @return array
	 */
	function xts_get_post_classes() {
		$post_format = get_post_format();
		$post_id     = get_the_ID();
		$audio       = get_post_meta( $post_id, '_xts_post_audio_url', true );
		$gallery     = get_post_meta( $post_id, '_xts_post_gallery', true );
		$classes     = array();

		$classes[] = 'xts-post';

		if ( 'video' === $post_format && xts_post_have_video( $post_id ) ) {
			$classes[] = 'xts-video-muted';
			$classes[] = 'xts-has-video';
		}

		if ( 'video' === $post_format && xts_post_have_video( $post_id ) && ! has_post_thumbnail() ) {
			$classes[] = 'has-post-thumbnail';
		}

		if ( 'audio' === $post_format && $audio ) {
			$classes[] = 'xts-has-audio';
		}

		if ( 'gallery' === $post_format && $gallery ) {
			$classes[] = 'xts-has-gallery';
		}

		if ( 'gallery' === $post_format && $gallery && ! has_post_thumbnail() ) {
			$classes[] = 'has-post-thumbnail';
		}

		if ( ( 'image' === $post_format || 'quote' === $post_format || 'link' === $post_format ) && xts_get_opt( 'blog_theme_post_formats', '0' ) ) {
			$classes[] = 'xts-format-design-mask';
		}

		return $classes;
	}
}

if ( ! function_exists( 'xts_get_link_attrs' ) ) {
	/**
	 * Get image url
	 *
	 * @since 1.0.0
	 *
	 * @param array $link Link data array.
	 *
	 * @return string
	 */
	function xts_get_link_attrs( $link ) {
		$link_attrs = '';

		if ( isset( $link['url'] ) && $link['url'] ) {
			$link_attrs = ' href="' . esc_url( $link['url'] ) . '"';

			if ( isset( $link['is_external'] ) && 'on' === $link['is_external'] ) {
				$link_attrs .= ' target="_blank"';
			}

			if ( isset( $link['nofollow'] ) && 'on' === $link['nofollow'] ) {
				$link_attrs .= ' rel="nofollow"';
			}
		}

		if ( isset( $link['class'] ) ) {
			$link_attrs .= ' class="' . esc_attr( $link['class'] ) . '"';
		}

		if ( isset( $link['data'] ) ) {
			$link_attrs .= $link['data'];
		}

		if ( isset( $link['custom_attributes'] ) ) {
			$custom_attributes = Utils::parse_custom_attributes( $link['custom_attributes'] );
			foreach ( $custom_attributes as $key => $value ) {
				$link_attrs .= ' ' . $key . '="' . $value . '"';
			}
		}

		return $link_attrs;
	}
}

if ( ! function_exists( 'xts_body_classes' ) ) {
	/**
	 * Add classes to body
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Body classes.
	 *
	 * @return array
	 */
	function xts_body_classes( $classes ) {
		$settings = xts_get_header_settings();

		if ( isset( $settings['overlap'] ) && $settings['overlap'] ) {
			$classes[] = 'xts-header-overlap';
		}

		if ( ! is_user_logged_in() && xts_get_opt( 'login_to_see_price' ) ) {
			$classes[] = 'xts-login-see-price';
		}

		if ( xts_is_shop_archive() ) {
			$classes[] = 'xts-shop-archive';
		}

		if ( 'boxed' === xts_get_opt( 'site_layout' ) ) {
			$classes[] = 'xts-layout-boxed';
		}

		if ( xts_get_opt( 'sticky_categories_navigation_menu' ) && ! xts_elementor_is_edit_mode() && ! xts_elementor_is_preview_mode() ) {
			$classes[] = 'xts-sticky-cats-enabled';
		}

		if ( xts_get_opt( 'product_categories_widget_accordion' ) ) {
			$classes[] = 'xts-cat-accordion';
		}

		if ( xts_get_opt( 'sticky_bottom_navbar' ) ) {
			$classes[] = 'xts-sticky-navbar-enabled';
		}

		if ( (int) xts_get_opt( 'footer_html_block' ) === xts_get_page_id() && is_singular( 'xts-html-block' ) ) {
			$classes[] = 'xts-footer-html-block';
		}

		return $classes;
	}

	add_filter( 'body_class', 'xts_body_classes' );
}

if ( ! function_exists( 'xts_load_html_dropdowns_action' ) ) {
	/**
	 * Load menu dropdowns with AJAX actions
	 *
	 * @since 1.0.0
	 */
	function xts_load_html_dropdowns_action() {
		$response = array(
			'status'  => 'error',
			'message' => esc_html__( 'Can\'t load HTML Blocks with AJAX', 'xts-theme' ),
			'data'    => array(),
		);

		if ( isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) { // phpcs:ignore
			$ids = $_POST['ids']; // phpcs:ignore
			foreach ( $ids as $id ) {
				$content = xts_get_html_block_content( $id );
				if ( ! $content ) {
					continue;
				}

				$response['status']      = 'success';
				$response['message']     = 'At least one HTML Block loaded';
				$response['data'][ $id ] = $content;
			}
		}

		AJAX_Response::send_response( $response );
	}

	add_action( 'wp_ajax_xts_load_html_dropdowns', 'xts_load_html_dropdowns_action' );
	add_action( 'wp_ajax_nopriv_xts_load_html_dropdowns', 'xts_load_html_dropdowns_action' );
}

if ( ! function_exists( 'xts_clear_menu_transient' ) ) {
	/**
	 * Clear menu session storage key hash on save menu/html block.
	 *
	 * @since 1.0.0
	 */
	function xts_clear_menu_transient() {
		delete_transient( 'xts-menu-hash-time' );
	}

	add_action( 'wp_update_nav_menu_item', 'xts_clear_menu_transient', 11, 1 );
	add_action( 'save_post_xts-html-block', 'xts_clear_menu_transient', 30, 3 );
}

if ( ! function_exists( 'xts_get_default_carousel_config' ) ) {
	/**
	 * Function to get array of default carousel configuration
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_default_carousel_config() {
		return array(
			'autoplay'                   => 'no',
			'autoplay_speed'             => array( 'size' => 2000 ),
			'infinite_loop'              => 'no',
			'center_mode'                => 'no',
			'draggable'                  => 'yes',
			'auto_height'                => 'no',
			'init_on_scroll'             => 'yes',
			'dots'                       => 'no',
			'dots_color_scheme'          => 'dark',
			'arrows'                     => 'yes',
			'arrows_horizontal_position' => '',
			'arrows_color_scheme'        => xts_get_default_value( 'carousel_arrows_color_scheme' ),
			'arrows_vertical_position'   => xts_get_default_value( 'carousel_arrows_vertical_position' ),
			'arrows_design'              => 'default',
			'parent'                     => '',
			'center_mode_opacity'        => 'no',
			'library'                    => 'swiper',
			'source'                     => '',

			'carousel_items'             => array( 'size' => 3 ),
			'carousel_items_tablet'      => array( 'size' => 2 ),
			'carousel_items_mobile'      => array( 'size' => 2 ),
			'carousel_spacing'           => xts_get_default_value( 'items_gap' ),
			'controls_id'                => uniqid(),

			// Sync.
			'sync'                       => 'disabled',
			'sync_parent_id'             => '',
			'sync_child_id'              => '',
		);
	}
}

if ( ! function_exists( 'xts_get_carousel_atts' ) ) {
	/**
	 * Function to get carousel attributes
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Carousel config.
	 *
	 * @return string
	 */
	function xts_get_carousel_atts( $config = array() ) {
		$default_config = xts_get_default_carousel_config();
		$config         = wp_parse_args( $config, $default_config );

		$columns = xts_get_row_columns_numbers( $config['carousel_items']['size'] );

		$config['carousel_items']['size']        = $config['carousel_items']['size'] ? $config['carousel_items']['size'] : $columns['desktop'];
		$config['carousel_items_tablet']['size'] = $config['carousel_items_tablet']['size'] ? $config['carousel_items_tablet']['size'] : $columns['tablet'];
		$config['carousel_items_mobile']['size'] = $config['carousel_items_mobile']['size'] ? $config['carousel_items_mobile']['size'] : $columns['mobile'];

		$json         = wp_json_encode( array_intersect_key( $config, $default_config ) );
		$custom_attrs = '';

		if ( 'yes' === $config['dots'] ) {
			$custom_attrs .= 'data-xts-carousel-dots="yes"';
		}

		if ( 'disabled' !== $config['sync'] && ( $config['sync_parent_id'] || $config['sync_child_id'] ) ) {
			$id = 'parent' === $config['sync'] ? $config['sync_parent_id'] : $config['sync_child_id'];

			$custom_attrs .= ' data-sync="' . $config['sync'] . '" data-sync-id="' . $id . '"';
		}

		return ' data-xts-carousel ' . $custom_attrs . ' data-carousel-args=\'' . $json . '\'';
	}
}

if ( ! function_exists( 'xts_get_carousel_classes' ) ) {
	/**
	 * Function to get carousel attributes
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Carousel config.
	 *
	 * @return string
	 */
	function xts_get_carousel_classes( $config = array() ) {
		$default_config = xts_get_default_carousel_config();
		$config         = wp_parse_args( $config, $default_config );

		if ( 'swiper' === $config['library'] ) {
			wp_enqueue_script( 'swiper' );
			xts_enqueue_js_script( 'swiper-init' );
		}

		$classes = ' xts-carousel';

		$classes .= ' xts-lib-' . $config['library'];

		if ( 'dark' !== $config['arrows_color_scheme'] ) {
			$classes .= ' xts-arrows-' . $config['arrows_color_scheme'];
		}

		if ( 'dark' !== $config['dots_color_scheme'] ) {
			$classes .= ' xts-dots-' . $config['dots_color_scheme'];
		}

		if ( $config['arrows_horizontal_position'] && 'disabled' !== $config['arrows_horizontal_position'] ) {
			$classes .= ' xts-arrows-hpos-' . $config['arrows_horizontal_position'];
		}

		if ( 'disabled' !== $config['arrows_vertical_position'] ) {
			$classes .= ' xts-arrows-vpos-' . $config['arrows_vertical_position'];
		}

		if ( 'disabled' !== $config['arrows_design'] ) {
			$classes .= ' xts-arrows-design-' . $config['arrows_design'];
		}

		if ( 'yes' === $config['center_mode_opacity'] ) {
			$classes .= ' xts-center-mode-opacity';
		}

		if ( 'yes' === $config['init_on_scroll'] ) {
			$classes .= ' xts-init-on-scroll';
		}

		if ( xts_get_opt( 'disable_carousel_mobile_devices' ) && 'single_product' !== $config['source'] ) {
			$classes .= ' xts-disable-md';
		}

		return $classes;
	}
}

if ( ! function_exists( 'xts_get_row_classes' ) ) {
	/**
	 * Get row classes
	 *
	 * @since 1.0.0
	 *
	 * @param string  $desktop Desktop item count.
	 * @param string  $tablet  Tablet item count.
	 * @param string  $mobile  Mobile item count.
	 * @param integer $spacing Spacing.
	 *
	 * @return string
	 */
	function xts_get_row_classes( $desktop = '', $tablet = '', $mobile = '', $spacing = 10 ) {
		$columns = xts_get_row_columns_numbers( $desktop );

		$desktop = $desktop ? $desktop : $columns['desktop'];
		$tablet  = $tablet ? $tablet : $columns['tablet'];
		$mobile  = $mobile ? $mobile : $columns['mobile'];

		$sizes = array(
			array(
				'name'  => 'xts-row-lg',
				'value' => $desktop,
			),
			array(
				'name'  => 'xts-row-md',
				'value' => $tablet,
			),
			array(
				'name'  => 'xts-row',
				'value' => $mobile,
			),
		);

		$result_sizes = array();

		foreach ( $sizes as $index => $value ) {
			if ( isset( $sizes[ $index + 1 ] ) ) {
				$next = $sizes[ $index + 1 ];
			} else {
				continue;
			}

			if ( $value['value'] === $next['value'] ) {
				$result_sizes[ $next['name'] ] = $next['value'];
				unset( $result_sizes[ $value['name'] ] );
			} elseif ( $value['value'] !== $next['value'] ) {
				$result_sizes[ $value['name'] ] = $value['value'];
				$result_sizes[ $next['name'] ]  = $next['value'];
			}
		}

		$classes = ' xts-row';

		foreach ( $result_sizes as $size => $value ) {
			$classes .= ' ' . $size . '-' . $value;
		}

		$classes .= ' xts-row-spacing-' . $spacing;

		return $classes;
	}
}

if ( ! function_exists( 'xts_get_row_columns_numbers' ) ) {
	/**
	 * Get row classes
	 *
	 * @since 1.0.0
	 *
	 * @param integer $desktop Desktop item count.
	 *
	 * @return array
	 */
	function xts_get_row_columns_numbers( $desktop ) {
		$columns = array();

		$columns['desktop'] = $desktop > 0 ? $desktop : 1;
		$columns['tablet']  = $columns['desktop'] > 1 ? $columns['desktop'] - 1 : $columns['desktop'];
		$columns['mobile']  = $columns['desktop'] > 4 ? 2 : 1;

		if ( 1 === $columns['desktop'] ) {
			$columns['mobile'] = 1;
		}

		return $columns;
	}
}

if ( ! function_exists( 'xts_setup_loop' ) ) {
	/**
	 * Setup loop
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array of loop arguments.
	 */
	function xts_setup_loop( $args = array() ) {
		if ( isset( $GLOBALS['xts_loop'] ) ) {
			return;
		}

		$default_args = array(
			'blog_post_title'                 => xts_get_opt( 'blog_title_visibility' ),
			'blog_post_meta'                  => xts_get_opt( 'blog_meta_visibility' ),
			'blog_post_text'                  => xts_get_opt( 'blog_text_visibility' ),
			'blog_post_categories'            => xts_get_opt( 'blog_categories_visibility' ),
			'blog_design'                     => xts_get_opt( 'blog_design', 'default' ),
			'blog_image_size'                 => xts_get_opt( 'blog_image_size' ),
			'blog_image_size_custom'          => xts_get_opt( 'blog_image_size_custom' ),
			'blog_excerpt_length'             => xts_get_opt( 'blog_excerpt_length' ),
			'blog_post_black_white'           => xts_get_opt( 'blog_post_black_white' ),
			'blog_post_shadow'                => xts_get_opt( 'blog_post_shadow' ),
			'blog_loop'                       => 0,

			'portfolio_design'                => xts_get_opt( 'portfolio_design' ),
			'portfolio_distortion_effect'     => xts_get_opt( 'portfolio_distortion_effect' ),
			'portfolio_image_size'            => xts_get_opt( 'portfolio_image_size' ),
			'portfolio_image_custom'          => xts_get_opt( 'portfolio_image_custom' ),
			'portfolio_loop'                  => 0,

			'product_image_size'              => xts_get_opt( 'product_loop_image_size' ),
			'product_image_custom'            => xts_get_opt( 'product_loop_image_size_custom' ),
			'product_countdown'               => xts_get_opt( 'product_loop_sale_countdown' ),
			'product_stock_progress_bar'      => xts_get_opt( 'product_loop_stock_progress_bar' ),
			'product_design'                  => xts_get_opt( 'product_loop_design' ),
			'product_rating'                  => xts_get_opt( 'product_loop_rating' ),
			'product_categories'              => xts_get_opt( 'product_loop_categories' ),
			'product_attributes'              => xts_get_opt( 'product_loop_attributes' ),
			'product_brands'                  => xts_get_opt( 'product_loop_brands' ),
			'product_hover_image'             => xts_get_opt( 'product_hover_image', '1' ),
			'product_loop_quantity'           => xts_get_opt( 'product_loop_quantity' ),

			'product_categories_image_size'   => false,
			'product_categories_image_custom' => false,

			'woocommerce_loop'                => 0,
			'is_quick_view'                   => false,
		);

		$GLOBALS['xts_loop'] = wp_parse_args( $args, $default_args );
	}

	add_action( 'wp', 'xts_setup_loop', 500 );
	add_action( 'loop_start', 'xts_setup_loop', 50 );
	add_action( 'woocommerce_before_shop_loop', 'xts_setup_loop', 50 );
}

if ( ! function_exists( 'xts_set_custom_404_page' ) ) {
	/**
	 * Set custom 404 page
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Page template.
	 *
	 * @return string
	 */
	function xts_set_custom_404_page( $template ) {
		global $wp_query;

		$custom_404 = xts_get_opt( 'custom_404_page' );

		if ( ! $custom_404 ) {
			return $template;
		}

		$wp_query->query( 'page_id=' . $custom_404 );
		$wp_query->the_post();
		$template = get_page_template();
		rewind_posts();

		return $template;
	}

	add_filter( '404_template', 'xts_set_custom_404_page', 1000 );
}

if ( ! function_exists( 'xts_get_instagram_user_data' ) ) {
	/**
	 * Get instagram user data
	 *
	 * @since 1.0.0
	 *
	 * @param string $user_id User id.
	 *
	 * @return mixed
	 */
	function xts_get_instagram_user_data( $user_id ) {
		$instagram_access_token = get_option( 'xts_instagram_access_token' );

		if ( $instagram_access_token ) {
			return get_option( 'xts_instagram_account_data_' . get_option( 'xts_instagram_account_id' ) );
		}

		$remote      = wp_remote_get( 'https://www.instagram.com/' . $user_id . '/' );
		$remote_code = wp_remote_retrieve_response_code( $remote );

		if ( is_wp_error( $remote ) ) {
			return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'xts-theme' ) );
		}

		if ( 200 !== $remote_code ) {
			return new WP_Error( 'invalid_response_' . $remote_code, esc_html__( 'Instagram did not return a 200.', 'xts-theme' ) );
		}

		$shards = explode( 'window._sharedData = ', $remote['body'] );
		if ( ! isset( $shards[1] ) ) {
			if ( isset( $shards[0] ) ) {
				$error_info = json_decode( $shards[0], true );

				if ( isset( $error_info['errors'] ) ) {
					return new WP_Error( $error_info['error_type'], $error_info['errors']['error'][0] );
				}
			}

			return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'xts-theme' ) );
		}
		$json = explode( ';</script>', $shards[1] );

		return json_decode( $json[0], true );
	}
}
