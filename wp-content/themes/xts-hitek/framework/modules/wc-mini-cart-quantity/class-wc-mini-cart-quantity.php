<?php
/**
 * Mini cart quantity.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use WC_AJAX;
use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * Mini cart quantity.
 *
 * @since 1.0.0
 */
class WC_Mini_Cart_Quantity extends Module {
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
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'wp_ajax_xts_update_mini_cart_item', array( $this, 'update_cart_item' ) );
		add_action( 'wp_ajax_nopriv_xts_update_mini_cart_item', array( $this, 'update_cart_item' ) );
		add_action( 'xts_mini_cart_after_formatted_cart_item_data', array( $this, 'quantity' ), 10, 2 );
	}

	/**
	 * Quantity.
	 *
	 * @since 1.0.0
	 *
	 * @param object $product   Product.
	 * @param array  $cart_item Cart item data.
	 */
	public function quantity( $product, $cart_item ) {
		if ( ! $product->is_sold_individually() && $product->is_purchasable() && xts_get_opt( 'mini_cart_quantity' ) ) {
			woocommerce_quantity_input(
				array(
					'input_value' => $cart_item['quantity'],
					'min_value'   => 1,
					'max_value'   => $product->backorders_allowed() ? '' : $product->get_stock_quantity(),
				),
				$product
			);
		}
	}

	/**
	 * Update cart item.
	 *
	 * @since 1.0.0
	 */
	public function update_cart_item() {
		if ( ( isset( $_GET['item_id'] ) && $_GET['item_id'] ) && ( isset( $_GET['qty'] ) && $_GET['qty'] ) ) { // phpcs:ignore
			global $woocommerce;
			$woocommerce->cart->set_quantity( $_GET['item_id'], $_GET['qty'] ); // phpcs:ignore
		}

		WC_AJAX::get_refreshed_fragments();
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'       => 'mini_cart_quantity',
				'name'     => esc_html__( 'Quantity input on shopping cart widget', 'xts-theme' ),
				'type'     => 'switcher',
				'section'  => 'general_shop_section',
				'default'  => '1',
				'priority' => 90,
			)
		);
	}
}
