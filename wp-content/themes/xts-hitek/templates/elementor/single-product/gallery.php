<?php
/**
 * Product gallery function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_gallery_template' ) ) {
	/**
	 * Product gallery template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_gallery_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			'thumbnails_gallery_position'   => 'bottom',
			'main_gallery_click_action'     => 'zoom',
			'main_gallery_photoswipe_btn'   => '1',
			'main_gallery_lightbox_gallery' => '1',
			'thumbnails_gallery_count'      => array( 'size' => 4 ),
			'source'                        => 'elementor',
			'badges'                        => '1',
		);

		$args = wp_parse_args( $element_args, $default_args );

		if ( $args['main_gallery_photoswipe_btn'] && 'photoswipe' !== $args['main_gallery_click_action'] ) {
			add_action( 'xts_single_product_main_gallery_action_buttons', 'xts_single_product_photoswipe_btn', 10 );
		} else {
			remove_action( 'xts_single_product_main_gallery_action_buttons', 'xts_single_product_photoswipe_btn', 10 );
		}

		if ( ! $args['badges'] ) {
			remove_action( 'woocommerce_sale_flash', 'xts_product_labels', 100 );
			add_filter( 'woocommerce_sale_flash', '__return_false', 100 );
		}

		if ( 'zoom' === $args['main_gallery_click_action'] ) {
			wp_enqueue_script( 'zoom' );
		} else {
			wp_dequeue_script( 'zoom' );
		}

		wc_get_template( 'single-product/product-image.php', $args );

		if ( ! $args['badges'] ) {
			add_filter( 'woocommerce_sale_flash', 'xts_product_labels', 100 );
		}
	}
}

