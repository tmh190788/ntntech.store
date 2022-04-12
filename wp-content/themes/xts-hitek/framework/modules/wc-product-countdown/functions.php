<?php
/**
 * Product countdown helper functions.
 *
 * @since 1.0
 *
 * @package xts
 */

use XTS\Modules\WC_Product_Countdown;

if ( ! function_exists( 'xts_product_loop_sale_countdown' ) ) {
	/**
	 * Progress bar template.
	 *
	 * @since 1.0
	 *
	 * @param string $classes Custom classes.
	 */
	function xts_product_loop_sale_countdown( $classes = '' ) {
		$progress_bar = new WC_Product_Countdown();
		$progress_bar->product_countdown( $classes );
	}
}
