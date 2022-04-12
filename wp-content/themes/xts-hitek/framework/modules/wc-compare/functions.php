<?php
/**
 * Compare helper functions.
 *
 * @since 1.0
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! function_exists( 'xts_add_compare_button' ) ) {
	/**
	 * Add to wishlist button.
	 *
	 * @since 1.0
	 *
	 * @param string $classes Additional classes.
	 */
	function xts_add_compare_button( $classes ) {
		$compare = Modules::get( 'wc-compare' );
		$compare->add_to_compare_btn( $classes );
	}
}
