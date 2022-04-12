<?php
/**
 * Stock progress bar function
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_stock_progress_bar_template' ) ) {
	/**
	 * Stock progress bar template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_stock_progress_bar_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			'button_align' => '',
		);

		extract( wp_parse_args( $element_args, $default_args ) ); // phpcs:ignore

		$progress_bar_module = Modules::get( 'wc-stock-progress-bar' );
		$progress_bar_module->stock_progress_bar_template();
	}
}

