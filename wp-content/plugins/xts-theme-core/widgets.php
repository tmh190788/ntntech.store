<?php
/**
 * Widgets init file
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_widgets_init' ) ) {
	/**
	 * Register all widgets
	 *
	 * @since 1.0.0
	 */
	function xts_widgets_init() {
		if ( ! defined( 'XTS_THEME_SLUG' ) ) {
			return;
		}

		if ( is_blog_installed() ) {
			register_widget( 'XTS\Widget\Search' );
			register_widget( 'XTS\Widget\Html_Block' );
			register_widget( 'XTS\Widget\Instagram' );
			register_widget( 'XTS\Widget\Recent_Posts' );
			register_widget( 'XTS\Widget\Twitter' );
//			register_widget( 'XTS\Widget\Social_Counter' );
			register_widget( 'XTS\Widget\Image' );
			register_widget( 'XTS\Widget\Menu' );
			register_widget( 'XTS\Widget\Social_Buttons' );
		}

		if ( xts_is_woocommerce_installed() ) {
			register_widget( 'XTS\Widget\WC_Layered_Nav' );
			register_widget( 'XTS\Widget\WC_Price_Filter' );
			register_widget( 'XTS\Widget\WC_Sort_By' );
			register_widget( 'XTS\Widget\WC_Stock_Status' );
		}
	}

	add_action( 'widgets_init', 'xts_widgets_init' );
}
