<?php
/**
 * Product add to cart function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_add_to_cart_template' ) ) {
	/**
	 * Product add to cart template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_add_to_cart_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			'stock_status' => 'yes',
		);

		$element_args = wp_parse_args( $element_args, $default_args ); // phpcs:ignore

		if ( 'yes' !== $element_args['stock_status'] ) {
			add_filter( 'woocommerce_get_stock_html', 'xts_return_empty', 10 );
		}

		woocommerce_template_single_add_to_cart();

		if ( 'yes' !== $element_args['stock_status'] ) {
			remove_action( 'woocommerce_get_stock_html', 'xts_return_empty', 10 );
		}
	}
}

