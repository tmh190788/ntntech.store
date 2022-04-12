<?php
/**
 * Swatches helper functions.
 *
 * @since 1.0
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! function_exists( 'xts_grid_brands_template' ) ) {
	/**
	 * Grid brands.
	 *
	 * @since 1.0
	 */
	function xts_grid_brands_template() {
		$brands = Modules::get( 'wc-brands' );
		$brands->product_loop_links();
	}
}
