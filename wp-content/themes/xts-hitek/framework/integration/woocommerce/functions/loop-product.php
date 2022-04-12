<?php
/**
 * Woocommerce loop product functions file
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_product_loop_add_to_cart_scripts' ) ) {
	/**
	 * Enqueue single product scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data Data.
	 *
	 * @return mixed
	 */
	function xts_product_loop_add_to_cart_scripts( $data ) {
		if ( 'no-action' !== xts_get_opt( 'action_after_add_to_cart' ) ) {
			xts_enqueue_js_library( 'magnific' );
			xts_enqueue_js_script( 'action-after-add-to-cart' );
		}

		return $data;
	}

	add_action( 'woocommerce_loop_add_to_cart_link', 'xts_product_loop_add_to_cart_scripts' );
}

if ( ! function_exists( 'xts_set_products_per_row_session' ) ) {
	/**
	 * Set products per page session on shop page
	 *
	 * @since 1.0.0
	 */
	function xts_set_products_per_row_session() {
		if ( isset( $_REQUEST['products_per_row'] ) ) { // phpcs:ignore
			if ( ! class_exists( 'WC_Session_Handler' ) ) {
				return;
			}

			$session = WC()->session;

			if ( is_null( $session ) ) {
				return;
			}

			$session->set( 'xts_products_per_row', intval( $_REQUEST['products_per_row'] ) ); // phpcs:ignore
		}
	}

	add_action( 'woocommerce_before_main_content', 'xts_set_products_per_row_session', 100 );
}

if ( ! function_exists( 'xts_get_products_per_row' ) ) {
	/**
	 * Get products per page on shop page
	 *
	 * @since 1.0.0
	 */
	function xts_get_products_per_row() {
		if ( ! class_exists( 'WC_Session_Handler' ) ) {
			return false;
		}

		$session = WC()->session;

		if ( is_null( $session ) ) {
			return intval( xts_get_opt( 'products_per_row' ) );
		}

		if ( isset( $_REQUEST['products_per_row'] ) && ! empty( $_REQUEST['products_per_row'] ) ) { // phpcs:ignore
			return intval( $_REQUEST['products_per_row'] ); // phpcs:ignore
		} elseif ( $session->__isset( 'xts_products_per_row' ) ) {
			$val = $session->__get( 'xts_products_per_row' );

			if ( $val ) {
				return intval( $val );
			}
		}

		return intval( xts_get_opt( 'products_per_row' ) );
	}
}

if ( ! function_exists( 'xts_set_products_per_page_session' ) ) {
	/**
	 * Set products per page session on shop page
	 *
	 * @since 1.0.0
	 */
	function xts_set_products_per_page_session() {
		if ( isset( $_REQUEST['products_per_page'] ) ) { // phpcs:ignore
			if ( ! class_exists( 'WC_Session_Handler' ) ) {
				return;
			}

			$session = WC()->session;

			if ( is_null( $session ) ) {
				return;
			}

			$session->set( 'xts_products_per_page', intval( $_REQUEST['products_per_page'] ) ); // phpcs:ignore
		}
	}

	add_action( 'woocommerce_before_main_content', 'xts_set_products_per_page_session', 100 );
}

if ( ! function_exists( 'xts_get_products_per_page' ) ) {
	/**
	 * Get products per page on shop page
	 *
	 * @since 1.0.0
	 */
	function xts_get_products_per_page() {
		if ( ! class_exists( 'WC_Session_Handler' ) ) {
			return false;
		}

		$session = WC()->session;

		if ( is_null( $session ) ) {
			return intval( xts_get_opt( 'products_per_page' ) );
		}

		if ( isset( $_REQUEST['products_per_page'] ) && ! empty( $_REQUEST['products_per_page'] ) ) { // phpcs:ignore
			return intval( $_REQUEST['products_per_page'] ); // phpcs:ignore
		} elseif ( $session->__isset( 'xts_products_per_page' ) ) {
			$val = $session->__get( 'xts_products_per_page' );

			if ( $val ) {
				return intval( $val );
			}
		}

		return intval( xts_get_opt( 'products_per_page' ) );
	}

	add_filter( 'loop_shop_per_page', 'xts_get_products_per_page' );
}
