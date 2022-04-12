<?php
/**
 * Additional information table function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_additional_info_table_template' ) ) {
	/**
	 * Additional information table template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_additional_info_table_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			'button_align' => '',
		);

		extract( wp_parse_args( $element_args, $default_args ) ); // phpcs:ignore

		xts_single_product_attributes_table();
	}
}

