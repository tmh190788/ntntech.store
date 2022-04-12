<?php
/**
 * Database storage.
 *
 * @package xts
 */

namespace XTS\WC_Wishlist;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\WC_Wishlist\Storage;

/**
 * Database storage.
 *
 * @since 1.0.0
 */
class DB_Storage implements Storage {

	/**
	 * Wishlist id.
	 *
	 * @var int
	 */
	private $wishlist_id = 0;

	/**
	 * User id.
	 *
	 * @var int
	 */
	private $user_id = 0;

	/**
	 * Transient name.
	 *
	 * @var string
	 */
	private $cache_name = '';

	/**
	 * Set cookie name in the constructor.
	 *
	 * @since 1.0
	 *
	 * @param integer $wishlist_id Wishlist id.
	 * @param integer $user_id User id.
	 *
	 * @return void
	 */
	public function __construct( $wishlist_id, $user_id ) {
		$this->wishlist_id = $wishlist_id;
		$this->user_id     = $user_id;
		$this->cache_name  = 'xts_wishlist_' . $this->wishlist_id;
	}

	/**
	 * Add product to the wishlist.
	 *
	 * @since 1.0
	 *
	 * @param integer $product_id Product id.
	 *
	 * @return boolean
	 */
	public function add( $product_id ) {
		global $wpdb;

		if ( $this->is_product_exists( $product_id ) ) {
			return false;
		}

		if ( ! $this->wishlist_id ) {
			return false;
		}

		delete_user_meta( $this->user_id, $this->cache_name );

		return $wpdb->insert( // phpcs:ignore
			$wpdb->xts_products_table,
			array(
				'product_id'  => $product_id,
				'wishlist_id' => $this->wishlist_id,
			),
			array(
				'%d',
				'%d',
			)
		);
	}

	/**
	 * Remove product from the wishlist.
	 *
	 * @since 1.0
	 *
	 * @param integer $product_id Product id.
	 *
	 * @return boolean
	 */
	public function remove( $product_id ) {
		global $wpdb;

		if ( ! $this->is_product_exists( $product_id ) ) {
			return false;
		}

		delete_user_meta( $this->user_id, $this->cache_name );

		return $wpdb->delete( // phpcs:ignore
			$wpdb->xts_products_table,
			array(
				'product_id'  => $product_id,
				'wishlist_id' => $this->wishlist_id,
			),
			array( '%d', '%d' )
		);
	}

	/**
	 * Get all products.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_all() {
		global $wpdb;

		if ( ! $this->wishlist_id ) {
			return array();
		}

		$cache = get_user_meta( $this->user_id, $this->cache_name, true );

		if ( empty( $cache ) || $cache['expires'] < time() ) {

			$products = $wpdb->get_results( // phpcs:ignore
				$wpdb->prepare(
					"
						SELECT *
						FROM $wpdb->xts_products_table
						WHERE wishlist_id = %d
					",
					$this->wishlist_id
				),
				ARRAY_A
			);

			$cache = array(
				'expires'  => time() + WEEK_IN_SECONDS,
				'products' => $products,
			);

			update_user_meta( $this->user_id, $this->cache_name, $cache );
		}

		return $cache['products'];
	}

	/**
	 * Is product in compare.
	 *
	 * @since 1.0
	 *
	 * @param integer $product_id Product id.
	 *
	 * @return boolean
	 */
	public function is_product_exists( $product_id ) {
		global $wpdb;

		$id = $wpdb->get_var( // phpcs:ignore
			$wpdb->prepare(
				"
				SELECT ID
				FROM $wpdb->xts_products_table
				WHERE wishlist_id = %d
				AND product_id = %d
			",
				$this->wishlist_id,
				$product_id
			)
		);

		return ! is_null( $id );
	}

}
