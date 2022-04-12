<?php
/**
 * Progress bar helper functions.
 *
 * @since   1.0
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! function_exists( 'xts_stock_progress_bar_template' ) ) {
	/**
	 * Progress bar template.
	 *
	 * @since 1.0
	 *
	 * @param string $classes Custom classes.
	 */
	function xts_stock_progress_bar_template( $classes = '' ) {
		$progress_bar = Modules::get( 'wc-stock-progress-bar' );
		$progress_bar->stock_progress_bar_template( $classes );
	}
}
