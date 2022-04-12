<?php
/**
 * Wishlist template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_wishlist_template' ) ) {
	/**
	 * Wishlist template
	 *
	 * @since 1.0.0
	 */
	function xts_wishlist_template() {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		echo do_shortcode( '[xts_wishlist]' );
	}
}
