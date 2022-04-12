<?php
/**
 * Product tabs function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_tabs_template' ) ) {
	/**
	 * Product tabs template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_tabs_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			'disable_additional_info' => 'no',
			'disable_reviews'         => 'no',
			'disable_description'     => 'no',
		);

		$args = wp_parse_args( $element_args, $default_args );

		if ( 'yes' === $args['disable_additional_info'] ) {
			add_filter( 'woocommerce_product_tabs', 'xts_single_product_remove_additional_information_tab', 98 );
		}

		if ( 'yes' === $args['disable_reviews'] ) {
			add_filter( 'woocommerce_product_tabs', 'xts_single_product_remove_reviews_tab', 98 );
		}

		if ( 'yes' === $args['disable_description'] ) {
			add_filter( 'woocommerce_product_tabs', 'xts_single_product_remove_description_tab', 98 );
		}

		wc_get_template( 'single-product/tabs/tabs.php', $args );
	}
}

