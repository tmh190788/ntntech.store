<?php
/**
 * Theme functions
 *
 * @package xts
 */

use Elementor\Controls_Manager;
use XTS\Options\Metaboxes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_remove_play_button_border_controls' ) ) {
	/**
	 * Remove border controls.
	 *
	 * @since 1.0.0
	 */
	function xts_remove_play_button_border_controls() {
		remove_action( 'elementor/element/xts_video/style_play_button_section/before_section_end', 'xts_add_video_play_button_border_controls', 10 ); // Does not remove core filters.
	}

	add_action( 'init', 'xts_remove_play_button_border_controls', 50 );
}

if ( ! function_exists( 'xts_hitek_hooks' ) ) {
	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	function xts_hitek_hooks() {
		remove_action( 'xts_shop_tools_left_area', 'xts_current_shop_breadcrumbs' );
		remove_action( 'xts_shop_page_title', 'xts_shop_page_title', 10 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		add_action( 'xts_shop_tools_left_area', 'xts_shop_page_title', 10 );
	}

	add_action( 'wp', 'xts_hitek_hooks', 500 );
}

if ( ! function_exists( 'xts_custom_content_after_page_title' ) ) {
	/**
	 * Add content after page title.
	 *
	 * @since 1.0.0
	 */
	function xts_custom_content_after_page_title() {
		if ( ! xts_is_shop_archive() ) {
			return;
		}

		?>
		<div class="container">
			<div class="xts-shop-head-nav row row-spacing-0">
				<div class="col xts-shop-tools">
					<?php xts_current_shop_breadcrumbs(); ?>
				</div>

				<div class="col-auto xts-shop-tools">
					<?php woocommerce_result_count(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	add_action( 'xts_before_site_content_container', 'xts_custom_content_after_page_title', 20 );
}
