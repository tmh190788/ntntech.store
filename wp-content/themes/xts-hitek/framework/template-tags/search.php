<?php
/**
 * Search template functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_add_attributes_to_default_menu_walker_link' ) ) {
	/**
	 * Add attributes to default menu walker link.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $atts The HTML attributes applied to the list item's `<a>` element, empty strings are ignored.
	 * @param WP_Term $category Term data object.
	 *
	 * @return array
	 */
	function xts_add_attributes_to_default_menu_walker_link( $atts, $category ) {
		$atts['data-val'] = $category->slug;
		return $atts;
	}

	add_action( 'category_list_link_attributes', 'xts_add_attributes_to_default_menu_walker_link', 2, 10 );
}


if ( ! function_exists( 'xts_search_full_screen' ) ) {
	/**
	 * Search full screen
	 *
	 * @since 1.0.0
	 */
	function xts_search_full_screen() {
		if ( ! xts_is_full_screen_search() ) {
			return;
		}

		$settings = xts_get_header_settings();

		$wrapper_classes = '';

		if ( isset( $settings['search']['color_scheme'] ) && 'light' === $settings['search']['color_scheme'] ) {
			$wrapper_classes .= 'xts-scheme-light';
		}

		xts_search_form(
			array(
				'type'            => 'full-screen',
				'post_type'       => $settings['search']['post_type'],
				'ajax'            => $settings['search']['ajax'],
				'count'           => isset( $settings['search']['ajax_result_count'] ) && $settings['search']['ajax_result_count'] ? $settings['search']['ajax_result_count'] : 40,
				'search_style'    => 'icon-alt',
				'wrapper_classes' => $wrapper_classes,
			)
		);
	}

	add_action( 'xts_after_site_wrapper', 'xts_search_full_screen', 10 );
}

if ( ! function_exists( 'xts_search_form' ) ) {
	/**
	 * Search form
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Config array.
	 */
	function xts_search_form( $args = array() ) {
		$default_args = array(
			'ajax'                  => false,
			'post_type'             => xts_is_woocommerce_installed() ? 'product' : 'post',
			'type'                  => 'form',
			'thumbnail'             => true,
			'price'                 => true,
			'categories_dropdown'   => 'no',
			'categories_on_results' => xts_get_opt( 'show_post_categories_on_ajax' ) ? 'yes' : 'no',
			'count'                 => 20,
			'icon_type'             => '',
			'search_style'          => '',
			'custom_icon'           => '',
			'wrapper_classes'       => '',
			'dropdown_classes'      => '',
			'form_classes'          => '',
			'location'              => '',
		);

		$args = wp_parse_args( $args, $default_args );

		$button_classes   = '';
		$form_classes     = '';
		$wrapper_classes  = '';
		$dropdown_classes = '';
		$data             = '';

		// Wrapper classes.
		$wrapper_classes .= ' xts-search-' . $args['type'];
		if ( 'dropdown' === $args['type'] ) {
			$wrapper_classes .= ' xts-dropdown xts-dropdown-search';
			$form_classes    .= ' xts-dropdown-inner';
		}
		if ( $args['wrapper_classes'] ) {
			$wrapper_classes .= ' ' . $args['wrapper_classes'];
		}
		if ( $args['dropdown_classes'] ) {
			$dropdown_classes .= ' ' . $args['dropdown_classes'];
		}

		// Button classes.
		if ( 'custom' === $args['icon_type'] ) {
			$button_classes .= ' xts-icon-custom';
		}

		// Form classes.
		if ( $args['search_style'] ) {
			$form_classes .= ' xts-style-' . $args['search_style'];
		}
		if ( $args['form_classes'] ) {
			$form_classes .= ' ' . $args['form_classes'];
		}
		if ( 'yes' === $args['categories_dropdown'] ) {
			$form_classes .= ' xts-with-cats';
		}

		$ajax_args = array(
			'thumbnail'             => $args['thumbnail'],
			'price'                 => $args['price'],
			'post_type'             => $args['post_type'],
			'count'                 => $args['count'],
			'categories_on_results' => $args['categories_on_results'],
			'sku'                   => xts_get_opt( 'show_product_sku_on_ajax' ) ? 'yes' : 'no',
			'symbols_count'         => apply_filters( 'xts_ajax_search_symbols_count', 3 ),
		);

		if ( $args['ajax'] ) {
			xts_enqueue_js_library( 'autocomplete' );
			xts_enqueue_js_script( 'ajax-search' );

			$form_classes .= ' xts-ajax-search';
			foreach ( $ajax_args as $key => $value ) {
				$data .= ' data-' . $key . '="' . $value . '"';
			}
		}

		switch ( $args['post_type'] ) {
			case 'product':
				$placeholder = esc_html__( 'Search for products', 'xts-theme' );
				$description = esc_html__( 'Start typing to see products you are looking for.', 'xts-theme' );
				break;

			case 'xts-portfolio':
				$placeholder = esc_html__( 'Search for projects', 'xts-theme' );
				$description = esc_html__( 'Start typing to see projects you are looking for.', 'xts-theme' );
				break;

			default:
				$placeholder = esc_html__( 'Search for posts', 'xts-theme' );
				$description = esc_html__( 'Start typing to see posts you are looking for.', 'xts-theme' );
				break;
		}

		xts_get_template(
			'search.php',
			array(
				'wrapper_classes'  => $wrapper_classes,
				'dropdown_classes' => $dropdown_classes,
				'form_classes'     => $form_classes,
				'data'             => $data,
				'placeholder'      => $placeholder,
				'button_classes'   => $button_classes,
				'description'      => $description,
				'args'             => $args,
			),
			'',
			'templates'
		);
	}
}
