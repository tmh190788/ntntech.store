<?php
/**
 * Product brands
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
 * Product brands
 *
 * @since 1.0.0
 */
class WC_Brands extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ) );

		add_action( 'init', array( $this, 'hooks' ) );

		$this->define_constants();
		$this->include_files();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_filter( 'woocommerce_product_tabs', array( $this, 'tab' ) );

		add_action( 'woocommerce_single_product_summary', array( $this, 'single_product_brands' ), 1 );
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		if ( ! defined( 'XTS_BRANDS_DIR' ) ) {
			define( 'XTS_BRANDS_DIR', XTS_FRAMEWORK_ABSPATH . '/modules/wc-brands/' );
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
			$path = XTS_BRANDS_DIR . $file . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Single product brands template
	 *
	 * @since 1.0.0
	 */
	public function single_product_brands() {
		if ( ! xts_get_opt( 'single_product_brands' ) ) {
			return;
		}

		global $product;

		$attribute = xts_get_opt( 'brands_attribute' );

		if ( ! $attribute ) {
			return;
		}

		$attributes = $product->get_attributes();
		$brands     = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
		$taxonomy   = get_taxonomy( $attribute );

		if ( ! isset( $attributes[ $attribute ] ) || ! $attributes[ $attribute ] || ! $brands ) {
			return;
		}

		if ( xts_is_shop_on_front() ) {
			$link = home_url();
		} else {
			$link = get_post_type_archive_link( 'product' );
		}

		xts_get_template(
			'single-product.php',
			array(
				'brands'    => $brands,
				'link'      => $link,
				'taxonomy'  => $taxonomy,
				'attribute' => $attribute,
			),
			'wc-brands'
		);
	}

	/**
	 * Product loop brands template
	 *
	 * @since 1.0.0
	 */
	public function product_loop_links() {
		if ( ! xts_get_loop_prop( 'product_brands' ) ) {
			return;
		}

		global $product;

		$brand_option = xts_get_opt( 'brands_attribute' );
		$brands       = wc_get_product_terms( $product->get_id(), $brand_option, array( 'fields' => 'all' ) );
		$taxonomy     = get_taxonomy( $brand_option );

		if ( ! $brands ) {
			return;
		}

		if ( xts_is_shop_on_front() ) {
			$link = home_url();
		} else {
			$link = get_post_type_archive_link( 'product' );
		}

		xts_get_template(
			'product-loop-links.php',
			array(
				'brands'       => $brands,
				'link'         => $link,
				'taxonomy'     => $taxonomy,
				'brand_option' => $brand_option,
			),
			'wc-brands'
		);
	}

	/**
	 * Brand tab
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Tabs array.
	 * @return array
	 */
	public function tab( $tabs ) {
		if ( ! xts_get_opt( 'brand_tab' ) ) {
			return $tabs;
		}

		global $product;

		$brand_info = wc_get_product_terms( $product->get_id(), xts_get_opt( 'brands_attribute' ), array( 'fields' => 'all' ) );

		if ( ! isset( $brand_info[0] ) ) {
			return $tabs;
		}

		if ( get_term_meta( $brand_info[0]->term_id, '_xts_brand_single_product_tab_content', true ) ) {
			$tabs['xts_brand_tab'] = array(
				'title'    => esc_html__( 'About brand', 'xts-theme' ),
				'priority' => 50,
				'callback' => array( $this, 'tab_content' ),
			);
		}

		return $tabs;
	}

	/**
	 * Brand tab content
	 *
	 * @since 1.0.0
	 */
	public function tab_content() {
		global $product;

		$attribute = xts_get_opt( 'brands_attribute' );

		if ( ! $attribute ) {
			return;
		}

		$attributes = $product->get_attributes();

		if ( ! isset( $attributes[ $attribute ] ) || ! $attributes[ $attribute ] ) {
			return;
		}

		$brands = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'slugs' ) );

		if ( ! $brands ) {
			return;
		}

		xts_get_template(
			'tab-content.php',
			array(
				'brands'    => $brands,
				'attribute' => $attribute,
			),
			'wc-brands'
		);
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'brands_section',
				'name'     => esc_html__( 'Brands', 'xts-theme' ),
				'parent'   => 'shop_section',
				'priority' => 40,
				'icon'     => 'xf-shop',
			)
		);

		Options::add_field(
			array(
				'id'           => 'brands_attribute',
				'name'         => esc_html__( 'Brands attribute', 'xts-theme' ),
				'description'  => esc_html__( 'You need to select which attribute will be used for product brands.', 'xts-theme' ),
				'type'         => 'select',
				'section'      => 'brands_section',
				'empty_option' => true,
				'select2'      => true,
				'options'      => $this->get_product_attributes_array(),
				'priority'     => 10,
			)
		);

		Options::add_field(
			array(
				'id'          => 'product_loop_brands',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Show brand on the products archive', 'xts-theme' ),
				'description' => esc_html__( 'Show product brand name next to the product title on the shop page.', 'xts-theme' ),
				'section'     => 'brands_section',
				'default'     => '0',
				'priority'    => 20,
			)
		);

		Options::add_field(
			array(
				'id'          => 'single_product_brands',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Show brand on the single product page', 'xts-theme' ),
				'description' => esc_html__( 'You can disable/enable product\'s brand image on the single page.', 'xts-theme' ),
				'section'     => 'brands_section',
				'default'     => '1',
				'priority'    => 30,
			)
		);

		Options::add_field(
			array(
				'id'          => 'brand_tab',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Show tab with brand information', 'xts-theme' ),
				'description' => esc_html__( 'If enabled you will see additional tab with brand description on the single product page. Text will be taken from "Description" field for each brand (attribute term).', 'xts-theme' ),
				'section'     => 'brands_section',
				'default'     => '1',
				'priority'    => 40,
			)
		);
	}

	/**
	 * Get attribute taxonomies
	 *
	 * @since 1.0.0
	 */
	public function get_product_attributes_array() {
		$attributes = array();

		if ( ! xts_is_woocommerce_installed() ) {
			return $attributes;
		}

		foreach ( wc_get_attribute_taxonomies() as $attribute ) {
			$attributes[ 'pa_' . $attribute->attribute_name ] = array(
				'name'  => $attribute->attribute_label,
				'value' => 'pa_' . $attribute->attribute_name,
			);
		}

		return $attributes;
	}
}
