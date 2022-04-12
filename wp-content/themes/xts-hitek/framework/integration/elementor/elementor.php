<?php
/**
 * Elementor config file
 *
 * @package xts
 */

use Elementor\Icons_Manager;
use XTS\Elementor\Controls\Autocomplete;
use XTS\Elementor\Controls\Buttons;
use Elementor\Plugin;
use XTS\Elementor\Controls\Google_Json;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager Theme manager.
	 *
	 * @return void
	 */
	function xts_elementor_register_elementor_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_location(
			'header',
			[
				'is_core'         => false,
				'public'          => false,
				'label'           => esc_html__( 'Header', 'xts-theme' ),
				'edit_in_content' => false,
			]
		);

		$elementor_theme_manager->register_location(
			'footer',
			[
				'is_core'         => false,
				'public'          => false,
				'label'           => esc_html__( 'Footer', 'xts-theme' ),
				'edit_in_content' => false,
			]
		);
	}

	add_action( 'elementor/theme/register_locations', 'xts_elementor_register_elementor_locations' );
}

if ( ! function_exists( 'xts_elementor_exclude_custom_post_types' ) ) {
	/**
	 * Filters the list of post type objects used by Elementor.
	 *
	 * @since 2.8.0
	 *
	 * @param array $post_types List of post type objects used by Elementor.
	 *
	 * @return array
	 */
	function xts_elementor_exclude_custom_post_types( $post_types ) {
		unset( $post_types['xts-size-guide'] );
		unset( $post_types['xts-sidebar'] );

		return $post_types;
	}

	add_filter( 'elementor/settings/controls/checkbox_list_cpt/post_type_objects', 'xts_elementor_exclude_custom_post_types' );
}

if ( ! function_exists( 'xts_elementor_get_render_icon' ) ) {
	/**
	 * Render Icon
	 *
	 * @since 1.0.0
	 *
	 * @param array  $icon       Icon Type, Icon value.
	 * @param array  $attributes Icon HTML Attributes.
	 * @param string $tag        Icon HTML tag, defaults to <i>.
	 *
	 * @return mixed|string
	 */
	function xts_elementor_get_render_icon( $icon, $attributes = [], $tag = 'i' ) {
		ob_start();
		Icons_Manager::render_icon( $icon, $attributes, $tag );
		return ob_get_clean();
	}
}

if ( ! function_exists( 'xts_elementor_enqueue_editor_styles' ) ) {
	/**
	 * Enqueue elementor editor custom styles
	 *
	 * @since 1.0.0
	 */
	function xts_elementor_enqueue_editor_styles() {
		wp_enqueue_style( 'xts-elementor-editor-style', XTS_FRAMEWORK_URL . '/integration/elementor/assets/css/editor.css', array( 'elementor-editor' ), XTS_VERSION );
		wp_enqueue_style( 'xts-admin-frontend-style', XTS_FRAMEWORK_URL . '/assets/css/style-frontend.css', array(), XTS_VERSION );
	}

	add_action( 'elementor/editor/before_enqueue_styles', 'xts_elementor_enqueue_editor_styles' );
}

if ( ! function_exists( 'xts_elementor_enqueue_editor_scripts' ) ) {
	/**
	 * Enqueue elementor editor custom scripts
	 *
	 * @since 1.0.0
	 */
	function xts_elementor_enqueue_editor_scripts() {
		wp_enqueue_script( 'xts-elementor-editor-scripts', XTS_FRAMEWORK_URL . '/integration/elementor/assets/js/functions.js', array( 'jquery' ), XTS_VERSION, false );
	}

	add_action( 'elementor/editor/before_enqueue_scripts', 'xts_elementor_enqueue_editor_scripts' );
}

if ( ! function_exists( 'xts_add_elementor_widget_categories' ) ) {
	/**
	 * Add theme widget categories
	 *
	 * @since 1.0.0
	 */
	function xts_add_elementor_widget_categories() {
		Plugin::instance()->elements_manager->add_category(
			'xts-elements',
			array(
				'title' => esc_html__( '[XTemos] Elements', 'xts-theme' ),
				'icon'  => 'fab fa-plug',
			)
		);

		Plugin::instance()->elements_manager->add_category(
			'xts-product-elements',
			array(
				'title' => esc_html__( '[XTemos] Product Elements', 'xts-theme' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	xts_add_elementor_widget_categories();
}

if ( ! function_exists( 'xts_register_elementor_controls' ) ) {
	/**
	 * Registering New Controls
	 *
	 * @since 1.0.0
	 */
	function xts_register_elementor_controls() {
		$controls_manager = Plugin::$instance->controls_manager;
		$controls_manager->register_control( 'xts_autocomplete', new Autocomplete() );
		$controls_manager->register_control( 'xts_buttons', new Buttons() );
		$controls_manager->register_control( 'xts_google_json', new Google_Json() );
	}

	add_action( 'elementor/controls/controls_registered', 'xts_register_elementor_controls' );
}

if ( ! function_exists( 'xts_get_posts_by_query' ) ) {
	/**
	 * Get post by search
	 *
	 * @since 1.0.0
	 */
	function xts_get_posts_by_query() {
		$search_string = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : ''; // phpcs:ignore
		$post_type     = isset( $_POST['post_type'] ) ? $_POST['post_type'] : 'post'; // phpcs:ignore
		$results       = array();

		$query = new WP_Query(
			array(
				's'              => $search_string,
				'post_type'      => $post_type,
				'posts_per_page' => - 1,
			)
		);

		if ( ! isset( $query->posts ) ) {
			return;
		}

		foreach ( $query->posts as $post ) {
			$results[] = array(
				'id'   => $post->ID,
				'text' => $post->post_title,
			);
		}

		wp_send_json( $results );
	}

	add_action( 'wp_ajax_xts_get_posts_by_query', 'xts_get_posts_by_query' );
	add_action( 'wp_ajax_nopriv_xts_get_posts_by_query', 'xts_get_posts_by_query' );
}

if ( ! function_exists( 'xts_get_posts_title_by_id' ) ) {
	/**
	 * Get post title by ID
	 *
	 * @since 1.0.0
	 */
	function xts_get_posts_title_by_id() {
		$ids       = isset( $_POST['id'] ) ? $_POST['id'] : array(); // phpcs:ignore
		$post_type = isset( $_POST['post_type'] ) ? $_POST['post_type'] : 'post'; // phpcs:ignore
		$results   = array();

		$query = new WP_Query(
			array(
				'post_type'      => $post_type,
				'post__in'       => $ids,
				'posts_per_page' => - 1,
				'orderby'        => 'post__in',
			)
		);

		if ( ! isset( $query->posts ) ) {
			return;
		}

		foreach ( $query->posts as $post ) {
			$results[ $post->ID ] = $post->post_title;
		}

		wp_send_json( $results );
	}

	add_action( 'wp_ajax_xts_get_posts_title_by_id', 'xts_get_posts_title_by_id' );
	add_action( 'wp_ajax_nopriv_xts_get_posts_title_by_id', 'xts_get_posts_title_by_id' );
}

if ( ! function_exists( 'xts_get_taxonomies_title_by_id' ) ) {
	/**
	 * Get taxonomies title by id
	 *
	 * @since 1.0.0
	 */
	function xts_get_taxonomies_title_by_id() {
		$ids     = isset( $_POST['id'] ) ? $_POST['id'] : array(); // phpcs:ignore
		$results = array();

		$args = array(
			'include' => $ids,
		);

		$terms = get_terms( $args );

		if ( is_array( $terms ) && $terms ) {
			foreach ( $terms as $term ) {
				if ( is_object( $term ) ) {
					$results[ $term->term_id ] = $term->name . ' (' . $term->taxonomy . ')';
				}
			}
		}

		wp_send_json( $results );
	}

	add_action( 'wp_ajax_xts_get_taxonomies_title_by_id', 'xts_get_taxonomies_title_by_id' );
	add_action( 'wp_ajax_nopriv_xts_get_taxonomies_title_by_id', 'xts_get_taxonomies_title_by_id' );
}

if ( ! function_exists( 'xts_get_taxonomies_by_query' ) ) {
	/**
	 * Get taxonomies by search
	 *
	 * @since 1.0.0
	 */
	function xts_get_taxonomies_by_query() {
		$search_string = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : ''; // phpcs:ignore
		$taxonomy      = isset( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : ''; // phpcs:ignore
		$results       = array();

		$args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'search'     => $search_string,
		);

		$terms = get_terms( $args );

		if ( is_array( $terms ) && $terms ) {
			foreach ( $terms as $term ) {
				if ( is_object( $term ) ) {
					$results[] = array(
						'id'   => $term->term_id,
						'text' => $term->name . ' (' . $term->taxonomy . ')',
					);
				}
			}
		}

		wp_send_json( $results );
	}

	add_action( 'wp_ajax_xts_get_taxonomies_by_query', 'xts_get_taxonomies_by_query' );
	add_action( 'wp_ajax_nopriv_xts_get_taxonomies_by_query', 'xts_get_taxonomies_by_query' );
}

if ( ! function_exists( 'xts_hide_elements_from_editor' ) ) {
	/**
	 * Hide some elements from editor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Settings.
	 *
	 * @return array
	 */
	function xts_hide_elements_from_editor( $settings ) {

		if ( 'xts-template' !== get_post_type() ) {
			$settings['widgets']['xts_single_product_add_to_cart']['show_in_panel']           = false;
			$settings['widgets']['xts_single_product_badges']['show_in_panel']                = false;
			$settings['widgets']['xts_single_product_breadcrumb']['show_in_panel']            = false;
			$settings['widgets']['xts_single_product_excerpt']['show_in_panel']               = false;
			$settings['widgets']['xts_single_product_gallery']['show_in_panel']               = false;
			$settings['widgets']['xts_single_product_hook']['show_in_panel']                  = false;
			$settings['widgets']['xts_single_product_meta']['show_in_panel']                  = false;
			$settings['widgets']['xts_single_product_notices']['show_in_panel']               = false;
			$settings['widgets']['xts_single_product_price']['show_in_panel']                 = false;
			$settings['widgets']['xts_single_product_rating']['show_in_panel']                = false;
			$settings['widgets']['xts_single_product_tabs']['show_in_panel']                  = false;
			$settings['widgets']['xts_single_product_title']['show_in_panel']                 = false;
			$settings['widgets']['xts_single_product_nav']['show_in_panel']                   = false;
			$settings['widgets']['xts_single_product_countdown']['show_in_panel']             = false;
			$settings['widgets']['xts_single_product_stock_progress_bar']['show_in_panel']    = false;
			$settings['widgets']['xts_single_product_additional_info_table']['show_in_panel'] = false;
			$settings['widgets']['xts_single_product_reviews']['show_in_panel']               = false;
			$settings['widgets']['xts_single_product_description']['show_in_panel']           = false;
			$settings['widgets']['xts_single_product_action_buttons']['show_in_panel']        = false;
			$settings['widgets']['xts_single_product_action_buttons']['show_in_panel']        = false;
			$settings['widgets']['xts_single_product_brands']['show_in_panel']                = false;
		}

		$settings['widgets']['wp-widget-xts-widget-instagram']['show_in_panel']      = false;
		$settings['widgets']['wp-widget-xts-widget-ajax-search']['show_in_panel']    = false;
		$settings['widgets']['wp-widget-xts-widget-twitter']['show_in_panel']        = false;
		$settings['widgets']['wp-widget-xts-widget-mega-menu']['show_in_panel']      = false;
		$settings['widgets']['wp-widget-xts-widget-social-buttons']['show_in_panel'] = false;
		$settings['widgets']['wp-widget-xts-widget-image']['show_in_panel']          = false;
		$settings['widgets']['wp-widget-xts-widget-html-block']['show_in_panel']     = false;

		return $settings;
	}

	add_filter( 'elementor/document/config', 'xts_hide_elements_from_editor' );
}

if ( ! function_exists( 'xts_add_custom_font_group' ) ) {
	/**
	 * Add custom font group to font control
	 *
	 * @since 1.0.0
	 *
	 * @param array $font_groups Default font groups.
	 *
	 * @return array
	 */
	function xts_add_custom_font_group( $font_groups ) {
		$font_groups = array( 'xts_fonts' => esc_html__( 'Theme fonts', 'xts-theme' ) ) + $font_groups;

		return $font_groups;
	}

	add_filter( 'elementor/fonts/groups', 'xts_add_custom_font_group' );
}

if ( ! function_exists( 'xts_add_custom_fonts_to_theme_group' ) ) {
	/**
	 * Add custom fonts to theme group
	 *
	 * @since 1.0.0
	 *
	 * @param array $additional_fonts Additional fonts.
	 *
	 * @return array
	 */
	function xts_add_custom_fonts_to_theme_group( $additional_fonts ) {
		$theme_fonts  = array();
		$content_font = xts_get_opt( 'content_typography' );
		$title_font   = xts_get_opt( 'title_typography' );
		$alt_font     = xts_get_opt( 'alt_typography' );

		if ( isset( $content_font[0] ) && isset( $content_font[0]['font-family'] ) && $content_font[0]['font-family'] ) {
			$theme_fonts[ $content_font[0]['font-family'] ] = 'xts_fonts';
		}

		if ( isset( $title_font[0] ) && isset( $title_font[0]['font-family'] ) && $title_font[0]['font-family'] ) {
			$theme_fonts[ $title_font[0]['font-family'] ] = 'xts_fonts';
		}

		if ( isset( $alt_font[0] ) && isset( $alt_font[0]['font-family'] ) && $alt_font[0]['font-family'] ) {
			$theme_fonts[ $alt_font[0]['font-family'] ] = 'xts_fonts';
		}

		$additional_fonts = $theme_fonts + $additional_fonts;

		return $additional_fonts;
	}

	add_filter( 'elementor/fonts/additional_fonts', 'xts_add_custom_fonts_to_theme_group' );
}
