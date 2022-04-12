<?php
/**
 * Quick view
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Framework\Modules;
use XTS\Framework\Options;

/**
 * Size guide
 *
 * @since 1.0.0
 */
class WC_Quick_View extends Module {

	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ) );
		add_action( 'wp_ajax_xts_quick_view', array( $this, 'quick_view_action' ) );
		add_action( 'wp_ajax_nopriv_xts_quick_view', array( $this, 'quick_view_action' ) );

		$this->define_constants();
		$this->include_files();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		if ( ! defined( 'XTS_QUICK_VIEW_DIR' ) ) {
			define( 'XTS_QUICK_VIEW_DIR', XTS_FRAMEWORK_ABSPATH . '/modules/wc-quick-view/' );
		}
	}

	/**
	 * Include main files.
	 *
	 * @since 1.0.0
	 */
	private function include_files() {
		$files = array(
			'functions',
		);

		foreach ( $files as $file ) {
			$path = XTS_QUICK_VIEW_DIR . $file . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Show quick view button
	 *
	 * @since 1.0
	 *
	 * @param string $classes Extra classes.
	 */
	public function quick_view_btn( $classes = '' ) {
		global $product;

		if ( ! xts_get_opt( 'quick_view' ) ) {
			return;
		}

		$id = $product->get_id();

		wp_enqueue_script( 'zoom' );
		wp_enqueue_script( 'wc-add-to-cart-variation' );

		xts_enqueue_js_library( 'magnific' );
		xts_enqueue_js_library( 'tooltip' );

		xts_enqueue_js_script( 'product-quick-view' );
		xts_enqueue_js_script( 'single-product-gallery' );
		xts_enqueue_js_script( 'tooltip' );
		xts_enqueue_js_script( 'variations-swatches' );

		if ( xts_get_opt( 'single_product_ajax_add_to_cart' ) ) {
			xts_enqueue_js_script( 'single-product-ajax-add-to-cart' );
		}

		xts_get_template(
			'button.php',
			array(
				'classes' => $classes,
				'id'      => $id,
			),
			'wc-quick-view'
		);
	}

	/**
	 * Show quick view product title
	 *
	 * @since 1.0.0
	 */
	public function quick_view_product_title() {
		xts_get_template(
			'product-title.php',
			array(),
			'wc-quick-view'
		);
	}

	/**
	 * Quick view AJAX action
	 *
	 * @since 1.0.0
	 */
	public function quick_view_action() {
		if ( ( isset( $_GET['id'] ) && ! $_GET['id'] ) || ! xts_is_woocommerce_installed() ) { // phpcs:ignore
			return;
		}

		global $post;

		$size_guide_module = Modules::get( 'wc-size-guide' );
		$products          = get_posts(
			array(
				'post__in'  => array( sanitize_text_field( wp_unslash( $_GET['id'] ) ) ), // phpcs:ignore
				'post_type' => 'product',
			)
		);

		xts_set_loop_prop( 'is_quick_view', true );

		foreach ( $products as $post ) { // phpcs:ignore
			setup_postdata( $post );

			// Remove size guide button.
			remove_action( 'woocommerce_single_product_summary', array( $size_guide_module, 'size_guide_single_btn' ), 35 );

			// Remove single product wrapper.
			remove_action( 'woocommerce_before_single_product', 'xts_single_product_wrapper_start', 100 );
			remove_action( 'woocommerce_after_single_product', 'xts_single_product_wrapper_end', 100 );

			// Remove before and after add to cart button text.
			remove_action( 'woocommerce_single_product_summary', 'xts_before_add_to_cart_content', 25 );
			remove_action( 'woocommerce_single_product_summary', 'xts_after_add_to_cart_content', 31 );

			// Remove notices.
			remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );

			// Disable add to cart button for catalog mode.
			if ( xts_get_opt( 'catalog_mode' ) ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			}

			// Change title template.
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'quick_view_product_title' ), 5 );

			get_template_part( 'woocommerce/content', 'quick-view' );
		}

		wp_reset_postdata();

		die();
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'quick_view_section',
				'name'     => esc_html__( 'Quick view', 'xts-theme' ),
				'parent'   => 'shop_section',
				'icon'     => 'xf-shop',
				'priority' => 50,
			)
		);

		Options::add_field(
			array(
				'id'          => 'quick_view',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Quick view', 'xts-theme' ),
				'description' => esc_html__( 'Adds a special button on the product grid to display a popup with product information loaded with AJAX.', 'xts-theme' ),
				'section'     => 'quick_view_section',
				'default'     => '1',
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'          => 'quick_view_width',
				'type'        => 'range',
				'name'        => esc_html__( 'Quick view width', 'xts-theme' ),
				'description' => esc_html__( 'Set the popup quick view size in pixels.', 'xts-theme' ),
				'section'     => 'quick_view_section',
				'default'     => 920,
				'min'         => 400,
				'max'         => 1200,
				'step'        => 10,
				'priority'    => 30,
			)
		);
	}
}
