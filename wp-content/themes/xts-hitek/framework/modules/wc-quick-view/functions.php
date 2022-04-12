<?php
/**
 * Quick view helper functions.
 *
 * @since 1.0
 *
 * @package xts
 */

use XTS\Modules\WC_Quick_View;

if ( ! function_exists( 'xts_quick_view_btn' ) ) {
	/**
	 * Quick view button.
	 *
	 * @since 1.0
	 *
	 * @param string $classes Additional classes.
	 */
	function xts_quick_view_btn( $classes ) {
		$quick_view = new WC_Quick_View();
		$quick_view->quick_view_btn( $classes );
	}
}
