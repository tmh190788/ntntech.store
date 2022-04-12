<?php
/**
 * Woocommerce catalog mode.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * Woocommerce catalog mode.
 *
 * @since 1.0.0
 */
class WC_Catalog_Mode extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'wp', array( $this, 'hooks' ), 1000 );
		add_action( 'wp', array( $this, 'pages_redirect' ) );
		add_action( 'init', array( $this, 'add_options' ) );
	}

	/**
	 * Hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		if ( ! xts_get_opt( 'catalog_mode' ) ) {
			return;
		}

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	}

	/**
	 * Redirect form cart or checkout to home
	 *
	 * @since 1.0.0
	 */
	public function pages_redirect() {
		if ( ! xts_get_opt( 'catalog_mode' ) || ! xts_is_woocommerce_installed() ) {
			return;
		}

		$cart     = is_page( wc_get_page_id( 'cart' ) );
		$checkout = is_page( wc_get_page_id( 'checkout' ) );

		wp_reset_postdata();

		if ( $cart || $checkout ) {
			wp_safe_redirect( home_url() );
			exit;
		}
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'catalog_mode',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Catalog mode', 'xts-theme' ),
				'description' => esc_html__( 'You can hide all "Add to cart" buttons, cart widget, cart and checkout pages. This will allow you to showcase your products as an online catalog without ability to make a purchase.', 'xts-theme' ),
				'section'     => 'general_shop_section',
				'default'     => '0',
				'priority'    => 40,
			)
		);
	}
}
