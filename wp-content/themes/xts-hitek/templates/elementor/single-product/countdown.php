<?php
/**
 * Product countdown function
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_countdown_template' ) ) {
	/**
	 * Product countdown template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_countdown_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			'button_align' => '',
		);

		extract( wp_parse_args( $element_args, $default_args ) ); // phpcs:ignore

		$countdown_module = Modules::get( 'wc-product-countdown' );
		$countdown_module->product_single_countdown();
	}
}

