<?php
/**
 * Product hook function
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_hook_template' ) ) {
	/**
	 * Product hook template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_hook_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			'hook'          => '0',
			'clean_actions' => 'no',
		);

		extract( wp_parse_args( $element_args, $default_args ) ); // phpcs:ignore

		if ( 'yes' === $clean_actions ) {
			switch ( $hook ) {
				case 'woocommerce_before_single_product_summary':
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
					remove_action( 'woocommerce_before_single_product_summary', 'xts_single_product_breadcrumbs', 10 );
					break;
				case 'woocommerce_single_product_summary':
					$countdown_module    = Modules::get( 'wc-product-countdown' );
					$progress_bar_module = Modules::get( 'wc-stock-progress-bar' );
					$compare_module      = Modules::get( 'wc-compare' );
					$brands_module       = Modules::get( 'wc-brands' );

					remove_action( 'woocommerce_single_product_summary', array( $brands_module, 'single_product_brands' ), 1 );
					remove_action( 'woocommerce_single_product_summary', array( $progress_bar_module, 'stock_progress_bar_template' ), 20 );
					remove_action( 'woocommerce_single_product_summary', array( $countdown_module, 'product_single_countdown' ), 15 );
					remove_action( 'woocommerce_single_product_summary', array( $compare_module, 'add_to_compare_single_btn' ), 34 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
					remove_action( 'woocommerce_single_product_summary', 'xts_single_product_share_buttons', 45 );
					break;
				case 'woocommerce_after_single_product_summary':
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
					break;
				case 'woocommerce_before_single_product':
					remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
					break;
				case 'woocommerce_after_single_product':
					break;
			}
		}

		do_action( $hook );
	}
}

