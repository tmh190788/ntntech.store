<?php
/**
 * Login to see product price
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
 * Login to see product price
 *
 * @since 1.0.0
 */
class WC_Login_To_See_Price extends Module {

	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'hooks' ) );
		add_action( 'init', array( $this, 'add_options' ) );
	}

	/**
	 * Hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		if ( is_user_logged_in() || ! xts_get_opt( 'login_to_see_price' ) ) {
			return;
		}

		add_filter( 'woocommerce_get_price_html', array( $this, 'price_message' ) );
		add_filter( 'woocommerce_loop_add_to_cart_link', '__return_false' );

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	}

	/**
	 * Price message
	 *
	 * @since 1.0.0
	 */
	public function price_message() {
		$classes    = '';
		$settings   = xts_get_header_settings();
		$login_side = isset( $settings['my-account'] ) && isset( $settings['my-account']['login_form'] ) && $settings['my-account']['login_form'];

		$classes .= $login_side ? ' xts-opener' : '';

		return xts_get_template_html(
			'price-message.php',
			array(
				'classes' => $classes,
			),
			'wc-login-to-see-price'
		);
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'login_to_see_price',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Login to see add to cart and price', 'xts-theme' ),
				'description' => esc_html__( 'You can restrict shopping functions only for logged in customers.', 'xts-theme' ),
				'section'     => 'general_shop_section',
				'default'     => '0',
				'priority'    => 50,
			)
		);
	}
}
