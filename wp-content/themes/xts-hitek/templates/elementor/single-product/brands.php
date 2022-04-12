<?php
/**
 * Product brands function
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_brands_template' ) ) {
	/**
	 * Product brands template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_brands_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array();

		$args = wp_parse_args( $element_args, $default_args );

		$brands_module = Modules::get( 'wc-brands' );

		$brands_module->single_product_brands();
	}
}

