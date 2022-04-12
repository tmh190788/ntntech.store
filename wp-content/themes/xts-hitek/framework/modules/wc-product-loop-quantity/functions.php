<?php
/**
 * Product countdown helper functions.
 *
 * @since 1.0
 *
 * @package xts
 */

use XTS\Modules\WC_Product_Loop_Quantity;

if ( ! function_exists( 'xts_product_loop_quantity' ) ) {
	/**
	 * Product loop quantity template.
	 *
	 * @since 1.0.0
	 *
	 * @param object $product Product.
	 */
	function xts_product_loop_quantity( $product ) {
		$product_loop_quantity = new WC_Product_Loop_Quantity();
		$product_loop_quantity->quantity( $product );
	}
}
