<?php
/**
 * Wishlist helper functions.
 *
 * @since 1.0
 *
 * @package xts
 */

use XTS\WC_Wishlist\Ui;

if ( ! function_exists( 'xts_get_whishlist_page_url' ) ) {
	/**
	 * Get wishlist page url.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	function xts_get_whishlist_page_url() {
		$page_id = xts_get_opt( 'wishlist_page' );

		if ( defined( 'ICL_SITEPRESS_VERSION' ) && function_exists( 'wpml_object_id_filter' ) ) {
			$page_id = wpml_object_id_filter( $page_id, 'page', true );
		}

		return get_permalink( $page_id );
	}
}

if ( ! function_exists( 'xts_get_wishlist_count' ) ) {
	/**
	 * Get wishlist count.
	 *
	 * @since 1.0
	 *
	 * @return integer
	 */
	function xts_get_wishlist_count() {
		$count = 0;
		$ui    = Ui::get_instance();

		if ( $ui->get_wishlist() ) {
			$count = $ui->get_wishlist()->get_count();
		}

		return $count;
	}
}

if ( ! function_exists( 'xts_add_wishlist_button' ) ) {
	/**
	 * Add to wishlist button.
	 *
	 * @since 1.0
	 *
	 * @param string $classes Additional classes.
	 */
	function xts_add_wishlist_button( $classes ) {
		$ui = Ui::get_instance();
		$ui->add_to_wishlist_btn( $classes );
	}
}
