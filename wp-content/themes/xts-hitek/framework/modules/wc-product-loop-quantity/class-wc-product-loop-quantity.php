<?php
/**
 * Product loop quantity.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use Elementor\Controls_Manager;
use WC_AJAX;
use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * Product loop quantity.
 *
 * @since 1.0.0
 */
class WC_Product_Loop_Quantity extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ) );
		add_filter( 'elementor/element/xts_products/visibility_section/before_section_end', array( $this, 'add_element_options' ), 5, 2 );
		add_filter( 'elementor/element/xts_product_tabs/visibility_section/before_section_end', array( $this, 'add_element_options' ), 5, 2 );
		add_action( 'init', array( $this, 'hooks' ) );
	}

	/**
	 * Hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		$this->define_constants();
		$this->include_files();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		if ( ! defined( 'XTS_WC_PRODUCT_LOOP_QUANTITY_DIR' ) ) {
			define( 'XTS_WC_PRODUCT_LOOP_QUANTITY_DIR', XTS_FRAMEWORK_ABSPATH . '/modules/wc-product-loop-quantity/' );
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
			$path = XTS_WC_PRODUCT_LOOP_QUANTITY_DIR . $file . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Quantity.
	 *
	 * @since 1.0.0
	 *
	 * @param object $product Product.
	 */
	public function quantity( $product ) {
		if ( ! $product->is_sold_individually() && 'variable' !== $product->get_type() && $product->is_purchasable() && $product->is_in_stock() ) {
			xts_enqueue_js_script( 'product-loop-quantity' );
			woocommerce_quantity_input(
				array(
					'min_value' => 1,
					'max_value' => $product->backorders_allowed() ? '' : $product->get_stock_quantity(),
				)
			);
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
				'id'       => 'product_loop_quantity',
				'name'     => esc_html__( 'Quantity input on product', 'xts-theme' ),
				'type'     => 'switcher',
				'section'  => 'product_archive_product_options_section',
				'requires' => array(
					array(
						'key'     => 'product_loop_design',
						'compare' => 'equals',
						'value'   => xts_get_default_value( 'product_loop_quantity_product_design_condition' ),
					),
				),
				'default'  => '0',
				'priority' => 80,
			)
		);
	}

	/**
	 * Add element options.
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	public function add_element_options( $element ) {
		$element->add_control(
			'quantity',
			[
				'label'        => esc_html__( 'Quantity input on product', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '0',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
				'condition'    => [
					'design' => xts_get_default_value( 'product_loop_quantity_product_design_condition' ),
				],
			]
		);
	}
}
