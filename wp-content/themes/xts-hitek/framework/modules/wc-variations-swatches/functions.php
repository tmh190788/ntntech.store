<?php
/**
 * Swatches helper functions.
 *
 * @since 1.0
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! function_exists( 'xts_grid_swatches_template' ) ) {
	/**
	 * Grid swatches.
	 *
	 * @since 1.0
	 */
	function xts_grid_swatches_template() {
		$swatches = Modules::get( 'wc-variations-swatches' );
		$swatches->grid_swatches_template();
	}
}

if ( ! function_exists( 'xts_grid_variations_template' ) ) {
	/**
	 * Grid _variations.
	 *
	 * @since 1.0
	 */
	function xts_grid_variations_template() {
		$swatches = Modules::get( 'wc-variations-swatches' );
		$swatches->grid_variations_template();
	}
}
