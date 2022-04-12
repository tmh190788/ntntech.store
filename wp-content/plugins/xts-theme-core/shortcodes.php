<?php
/**
 * Add shortcodes for template.
 *
 * @package xts.
 */

use XTS\Framework\Modules;
use XTS\WC_Wishlist\Ui;

if ( ! function_exists( 'xts_add_shortcodes' ) ) {
	/**
	 * Xts add shortcodes.
	 */
	function xts_add_shortcodes() {
		add_shortcode( 'xts_html_block', 'xts_html_block_shortcode' );
		add_shortcode( 'xts_social_buttons', 'xts_social_buttons_shortcode' );
		add_shortcode( 'xts_wishlist', array( Ui::get_instance(), 'wishlist_page' ) );
		add_shortcode( 'xts_compare', array( Modules::get( 'wc-compare' ), 'shortcode' ) );
	}

	add_action( 'wp', 'xts_add_shortcodes' );
}
