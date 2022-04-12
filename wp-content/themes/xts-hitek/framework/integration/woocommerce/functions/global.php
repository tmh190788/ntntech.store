<?php
/**
 * Woocommerce config file
 *
 * @package xts
 */

use XTS\Elementor\Controls\Autocomplete;
use XTS\Elementor\Controls\Buttons;
use Elementor\Plugin;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_product_price_slider_script' ) ) {
	/**
	 * Enqueue script.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_name Template_name.
	 */
	function xts_product_price_slider_script( $template_name ) {
		if ( 'content-widget-price-filter.php' === $template_name ) {
			xts_enqueue_js_script( 'wc-price-slider' );
		}
	}

	add_action( 'woocommerce_before_template_part', 'xts_product_price_slider_script', 10 );
}

if ( ! function_exists( 'xts_product_categories_widget_script' ) ) {
	/**
	 * Enqueue script.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data Data.
	 *
	 * @return string
	 */
	function xts_product_categories_widget_script( $data ) {
		if ( xts_get_opt( 'product_categories_widget_accordion' ) ) {
			xts_enqueue_js_script( 'product-categories-widget-accordion' );
		}

		return $data;
	}

	add_action( 'woocommerce_product_categories_widget_args', 'xts_product_categories_widget_script', 10 );
	add_action( 'woocommerce_product_categories_widget_dropdown_args', 'xts_product_categories_widget_script', 10 );
}

if ( ! function_exists( 'xts_woocommerce_hooks' ) ) {
	/**
	 * Play with woocommerce hooks
	 *
	 * @since 1.0.0
	 */
	function xts_woocommerce_hooks() {
		/**
		 * Disable single search redirect.
		 */
		add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );

		/**
		 * Single product photoswipe button.
		 */
		if ( xts_get_opt( 'single_product_main_gallery_photoswipe_btn' ) && 'photoswipe' !== xts_get_opt( 'single_product_main_gallery_click_action' ) ) {
			add_action( 'xts_single_product_main_gallery_action_buttons', 'xts_single_product_photoswipe_btn', 10 );
		}

		/**
		 * Disable related products option
		 */
		if ( ! xts_get_opt( 'single_product_related' ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		}

		/**
		 * Product attributes table
		 */
		if ( xts_get_opt( 'single_product_attributes_table' ) ) {
			add_action( 'woocommerce_single_product_summary', 'xts_single_product_attributes_table', 21 );
			add_filter( 'woocommerce_product_tabs', 'xts_single_product_remove_additional_information_tab', 98 );
		}
		/**
		 * Remove product excerpt
		 */
		if ( ! xts_get_opt( 'single_product_short_description' ) ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		}
		/**
		 * Cart page move totals
		 */
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );

		/**
		 * Disable wooCommerce stylesheets
		 */
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

		/**
		 * Unhook the WooCommerce wrappers
		 */
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

		/**
		 * Unhook the sale flash on single product
		 */
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
		add_action( 'xts_before_single_product_main_gallery', 'woocommerce_show_product_sale_flash', 10 );

		/**
		 * Unhook breadcrumb
		 */
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

		/**
		 * Remove default product thumbnail function
		 */
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

		/**
		 * Remove category content link
		 */
		remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
		remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );

		/**
		 * Remove default category thumbnail function
		 */
		remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );

		/**
		 * Disable product tabs title option
		 */
		if ( xts_get_opt( 'single_product_hide_tabs_titles' ) ) {
			add_filter( 'woocommerce_product_description_heading', '__return_false', 20 );
			add_filter( 'woocommerce_product_additional_information_heading', '__return_false', 20 );
		}

		/**
		 * Remove default empty cart text
		 */
		remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );

		/**
		 * Move notices on login page
		 */
		remove_action( 'woocommerce_before_customer_login_form', 'woocommerce_output_all_notices', 10 );

		if ( xts_get_opt( 'shop_filters_area' ) ) {
			if ( ( 'widgets' === xts_get_opt( 'shop_filters_area_content_type' ) && ! is_active_sidebar( 'filters-area-widget-sidebar' ) ) || ( 'html_block' === xts_get_opt( 'shop_filters_area_content_type' ) && ! xts_get_opt( 'shop_filters_area_html_block' ) ) ) {
				add_action( 'xts_before_filters_area_content', 'xts_empty_shop_filters_area_text', 20 );
			}

			// Add 'filters button'.
			add_action( 'woocommerce_before_shop_loop', 'xts_shop_filters_area_button', 40 );
		}

		/**
		 * Move notices on shop page.
		 */
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
		add_action( 'xts_before_products_loop_head', 'woocommerce_output_all_notices' );

		/**
		 * Remove rating from grid.
		 */
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

		if ( ! xts_get_opt( 'shop_woocommerce_catalog_ordering' ) ) {
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		}

		/**
		 * Remove default item link.
		 */
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	}

	add_action( 'init', 'xts_woocommerce_hooks', 1000 );
}

if ( ! function_exists( 'xts_wc_hide_outdated_templates_notice' ) ) {
	/**
	 * Hide woocommerce outdated templates notice.
	 *
	 * @param bool   $show Show or hide the notice.
	 * @param string $notice The slug of the notice.
	 *
	 * @return bool
	 */
	function xts_wc_hide_outdated_templates_notice( $show, $notice ) {
		if ( 'template_files' === $notice ) {
			return false;
		}

		return $show;
	}

	add_filter( 'woocommerce_show_admin_notice', 'xts_wc_hide_outdated_templates_notice', 2, 10 );
}

if ( ! function_exists( 'xts_get_widget_column_numbers' ) ) {
	/**
	 * Get widget column numbers
	 *
	 * @since 1.0.0
	 *
	 * @param string $sidebar_id Sidebar id.
	 *
	 * @return string
	 */
	function xts_get_widget_column_numbers( $sidebar_id = 'filters-area' ) {
		global $_wp_sidebars_widgets;

		if ( ! $_wp_sidebars_widgets ) {
			$_wp_sidebars_widgets = get_option( 'sidebars_widgets', array() ); // phpcs:ignore
		}

		$sidebars_widgets_count = $_wp_sidebars_widgets;
		$column                 = 4;

		if ( isset( $sidebars_widgets_count[ $sidebar_id ] ) || 'filters-area' === $sidebar_id ) {
			$count        = isset( $sidebars_widgets_count[ $sidebar_id ] ) ? count( $sidebars_widgets_count[ $sidebar_id ] ) : 0;
			$widget_count = apply_filters( 'widgets_count_' . $sidebar_id, $count );

			if ( $widget_count < 4 && 0 !== $widget_count ) {
				$column = $widget_count;
			}
		}

		return $column;
	}
}

if ( ! function_exists( 'xts_wc_get_cart_data' ) ) {
	/**
	 * Get a refreshed cart fragment, including the mini cart HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fragments Attachment ID.
	 *
	 * @return array
	 */
	function xts_wc_get_cart_data( $fragments ) {
		ob_start();
		xts_wc_cart_count();
		$count = ob_get_clean();

		ob_start();
		xts_wc_cart_subtotal();
		$subtotal = ob_get_clean();

		$fragments['span.xts-cart-count']    = $count;
		$fragments['span.xts-cart-subtotal'] = $subtotal;

		return $fragments;
	}

	add_filter( 'woocommerce_add_to_cart_fragments', 'xts_wc_get_cart_data', 100 );
}

if ( ! function_exists( 'xts_product_attributes_labels_update' ) ) {
	/**
	 * Attribute update.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $attribute_id       Added attribute ID.
	 * @param array   $attribute          Attribute data.
	 * @param string  $old_attribute_name Attribute old name.
	 */
	function xts_product_attributes_labels_update( $attribute_id, $attribute, $old_attribute_name ) {
		if ( isset( $_POST['attribute_show_on_product'] ) ) { // phpcs:ignore
			update_option( 'xts_pa_' . $attribute['attribute_name'] . '_show_on_product', sanitize_text_field( wp_unslash( $_POST['attribute_show_on_product'] ) ) ); // phpcs:ignore
		} else {
			delete_option( 'xts_pa_' . $attribute['attribute_name'] . '_show_on_product' ); // phpcs:ignore
		}
	}

	add_action( 'woocommerce_attribute_updated', 'xts_product_attributes_labels_update', 10, 3 );
}

if ( ! function_exists( 'xts_product_attributes_labels_add' ) ) {
	/**
	 * Attribute add.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $attribute_id Added attribute ID.
	 * @param array   $attribute    Attribute data.
	 */
	function xts_product_attributes_labels_add( $attribute_id, $attribute ) {
		if ( isset( $_POST['attribute_show_on_product'] ) ) { // phpcs:ignore
			add_option( 'xts_pa_' . $attribute['attribute_name'] . '_show_on_product', sanitize_text_field( wp_unslash( $_POST['attribute_show_on_product'] ) ) ); // phpcs:ignore
		}
	}

	add_action( 'woocommerce_attribute_added', 'xts_product_attributes_labels_add', 10, 2 );
}
