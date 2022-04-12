<?php
/**
 * Product notices function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_notices_template' ) ) {
	/**
	 * Product notices template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_notices_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			'button_align' => '',
		);

		extract( wp_parse_args( $element_args, $default_args ) ); // phpcs:ignore

		if ( is_null( WC()->session ) ) {
			return;
		}

		wc_print_notices();
	}
}

