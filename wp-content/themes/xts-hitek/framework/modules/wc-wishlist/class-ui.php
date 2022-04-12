<?php
/**
 * Wishlist UI.
 *
 * @package xts
 */

namespace XTS\WC_Wishlist;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Singleton;

/**
 * Wishlist UI.
 *
 * @since 1.0.0
 */
class Ui extends Singleton {
	/**
	 * Wishlist object.
	 *
	 * @var null
	 */
	private $wishlist = null;
	/**
	 * Can user edit this wishlist or just view it.
	 *
	 * @var boolean
	 */
	private $editable = true;

	/**
	 * Initialize action.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		add_action( 'init', array( $this, 'hooks' ), 100 ); // wp changed to init because elementor not init shortcode on backend.
		add_action( 'wp', array( $this, 'hooks' ), 100 );
	}

	/**
	 * Register hooks and actions.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		if ( ! xts_get_opt( 'wishlist' ) ) {
			return;
		}

		add_action( 'woocommerce_single_product_summary', array( $this, 'add_to_wishlist_single_btn' ), 33 );

		add_filter( 'woocommerce_account_menu_items', array( $this, 'account_navigation' ), 15 );

		add_filter( 'woocommerce_get_endpoint_url', array( $this, 'account_navigation_url' ), 15, 2 );

		add_filter( 'woocommerce_account_menu_item_classes', array( $this, 'account_navigation_classes' ), 15, 2 );

		$wishlist_id = get_query_var( 'wishlist_id' );

		// Display public wishlist or personal.
		if ( $wishlist_id && (int) $wishlist_id > 0 ) {
			$this->editable = false;
			$this->wishlist = new Wishlist( $wishlist_id, false, true );
		} else {
			$this->wishlist = new Wishlist();
		}
	}

	/**
	 * Wishlist page shortcode output.
	 *
	 * @since 1.0.0
	 */
	public function get_wishlist() {
		return $this->wishlist;
	}

	/**
	 * Wishlist page shortcode output.
	 *
	 * @since 1.0.0
	 */
	public function wishlist_page() {
		xts_get_template(
			'page.php',
			array(
				'wishlist_ui' => $this,
			),
			'wc-wishlist'
		);
	}

	/**
	 * Content of the wishlist page with products.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $wishlist Wishlist object.
	 *
	 * @return string
	 */
	public function wishlist_page_content( $wishlist = false ) {
		if ( ! $wishlist ) {
			$wishlist = $this->get_wishlist();
		}

		$wishlist_empty_text = xts_get_opt( 'wishlist_empty_text' );
		$products            = $wishlist->get_all();
		$url                 = xts_get_whishlist_page_url();
		$id                  = get_query_var( 'wishlist_id' );
		$wrapper_classes     = '';

		if ( $id && $id > 0 ) {
			$url .= $id . '/';
		}

		if ( '' !== get_option( 'permalink_structure' ) ) {
			$base = user_trailingslashit( $url . 'page/%#%' );
		} else {
			$base = add_query_arg( 'page', '%#%', $url );
		}

		$ids = array_map(
			function ( $item ) {
				return $item['product_id'];
			},
			$products
		);

		$args = array(
			'include'        => $ids,
			'items_per_page' => array( 'size' => xts_get_opt( 'products_per_page' ) ),
			'pagination'     => '',
			'columns'        => array( 'size' => xts_get_opt( 'wishlist_products_per_row', 3 ) ),
			'columns_tablet' => array( 'size' => xts_get_opt( 'wishlist_products_per_row_tablet' ) ),
			'columns_mobile' => array( 'size' => xts_get_opt( 'wishlist_products_per_row_mobile' ) ),
			'spacing'        => xts_get_opt( 'shop_spacing' ),
			'product_source' => 'list_of_products',
		);

		if ( ! $this->is_editable() ) {
			$wrapper_classes .= ' xts-wishlist-preview';
		}

		xts_enqueue_js_script( 'product-wishlist' );

		return xts_get_template_html(
			'page-content.php',
			array(
				'wishlist_ui'         => $this,
				'wishlist'            => $wishlist,
				'wrapper_classes'     => $wrapper_classes,
				'products'            => $products,
				'url'                 => $url,
				'args'                => $args,
				'base'                => $base,
				'wishlist_empty_text' => $wishlist_empty_text,
			),
			'wc-wishlist'
		);
	}

	/**
	 * Remove button HTML.
	 *
	 * @since 1.0.0
	 */
	public function remove_btn() {
		xts_get_template( 'remove-btn.php', array(), 'wc-wishlist' );
	}

	/**
	 * Add to wishlist button on single product.
	 *
	 * @since 1.0.0
	 */
	public function add_to_wishlist_single_btn() {
		$this->add_to_wishlist_btn( 'xts-style-inline' );
	}

	/**
	 * Add to wishlist button.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Extra classes.
	 */
	public function add_to_wishlist_btn( $classes = '' ) {
		if ( ! xts_get_opt( 'wishlist' ) || ( xts_get_opt( 'wishlist_logged' ) && ! is_user_logged_in() ) ) {
			return;
		}

		xts_enqueue_js_library( 'tooltip' );
		xts_enqueue_js_script( 'tooltip' );
		xts_enqueue_js_script( 'product-wishlist' );

		xts_get_template(
			'add-btn.php',
			array(
				'classes' => $classes,
			),
			'wc-wishlist'
		);
	}

	/**
	 * Add wishlist title to account menu.
	 *
	 * @since 1.0.0
	 *
	 * @param array $items Menu items.
	 *
	 * @return array
	 */
	public function account_navigation( $items ) {
		unset( $items['customer-logout'] );

		if ( xts_get_opt( 'wishlist_page' ) && xts_get_opt( 'wishlist' ) ) {
			$items['wishlist'] = get_the_title( xts_get_opt( 'wishlist_page' ) );
		}

		$items['customer-logout'] = esc_html__( 'Logout', 'xts-theme' );

		return $items;
	}

	/**
	 * Add URL to wishlist item in the menu.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url      Item url.
	 * @param string $endpoint Endpoint name.
	 *
	 * @return string
	 */
	public function account_navigation_url( $url, $endpoint ) {
		if ( 'wishlist' === $endpoint ) {
			$url = xts_get_whishlist_page_url();
		}

		return $url;
	}

	/**
	 * Add active class to wishlist item in the menu.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $classes  Item classes.
	 * @param string $endpoint Endpoint name.
	 *
	 * @return array
	 */
	public function account_navigation_classes( $classes, $endpoint ) {
		if ( ( 'wishlist' === $endpoint ) && ( get_the_ID() === (int) xts_get_opt( 'wishlist_page' ) ) ) {
			$classes[] = 'is-active';
		} elseif ( get_the_ID() === (int) xts_get_opt( 'wishlist_page' ) ) {
			$key = array_search( 'is-active', $classes ); // phpcs:ignore
			if ( false !== $key ) {
				unset( $classes[ $key ] );
			}
		}

		return $classes;
	}

	/**
	 * Can user edit this wishlist or just view it.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_editable() {
		return $this->editable;
	}
}
