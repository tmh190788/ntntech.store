<?php
/**
 * Product badges function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_product_badges_template' ) ) {
	/**
	 * Product badges template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_badges_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			'shape' => xts_get_opt( 'product_label_shape' ),
		);

		$args = wp_parse_args( $element_args, $default_args );

		xts_product_labels( $args );
	}
}

