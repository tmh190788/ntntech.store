<?php
/**
 * Stock progress bar
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
 * Stock progress bar
 *
 * @since 1.0.0
 */
class WC_Stock_Progress_Bar extends Module {

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
		$this->define_constants();
		$this->include_files();

		if ( ! $this->is_progress_bar_enabled() ) {
			return;
		}

		add_action( 'woocommerce_process_product_meta_simple', array( $this, 'save_total_quantity' ) );
		add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_total_quantity' ) );
		add_action( 'woocommerce_process_product_meta_grouped', array( $this, 'save_total_quantity' ) );
		add_action( 'woocommerce_process_product_meta_external', array( $this, 'save_total_quantity' ) );
		add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'quantity_input_template' ) );

		if ( xts_get_opt( 'single_product_stock_progress_bar' ) ) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'stock_progress_bar_template' ), 20 );
		}
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		if ( ! defined( 'XTS_PROGRESS_BAR_DIR' ) ) {
			define( 'XTS_PROGRESS_BAR_DIR', XTS_FRAMEWORK_ABSPATH . '/modules/wc-stock-progress-bar/' );
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
			$path = XTS_PROGRESS_BAR_DIR . $file . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Progress bar template
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Extra classes.
	 */
	public function stock_progress_bar_template( $classes = '' ) {
		$data = $this->get_progress_bar_data();

		if ( ! $data ) {
			return;
		}

		xts_get_template(
			'progress-bar.php',
			array(
				'extra_classes' => $classes,
				'data'          => $data,
			),
			'wc-stock-progress-bar'
		);
	}

	/**
	 * Get progress bar data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_progress_bar_data() {
		$product_id    = get_the_ID();
		$total_stock   = get_post_meta( $product_id, 'xts_total_stock_quantity', true );
		$current_stock = get_post_meta( $product_id, '_stock', true );

		if ( ! $total_stock || $current_stock <= 0 ) {
			return array();
		}

		$total_sold = $total_stock > $current_stock ? $total_stock - $current_stock : 0;
		$percentage = $total_sold > 0 ? round( $total_sold / $total_stock * 100 ) : 0;

		return array(
			'total_sold'    => $total_sold,
			'percentage'    => $percentage,
			'current_stock' => $current_stock,
		);
	}

	/**
	 * Save stock quantity
	 *
	 * @since 1.0.0
	 *
	 * @param integer $product_id Product id.
	 */
	public function save_total_quantity( $product_id ) {
		$quantity = isset( $_POST['xts_total_stock_quantity'] ) && $_POST['xts_total_stock_quantity'] ? sanitize_text_field( wp_unslash( $_POST['xts_total_stock_quantity'] ) ) : ''; // phpcs:ignore

		update_post_meta( $product_id, 'xts_total_stock_quantity', $quantity );
	}

	/**
	 * Input template
	 *
	 * @since 1.0.0
	 */
	public function quantity_input_template() {
		xts_get_template(
			'quantity-input.php',
			array(),
			'wc-stock-progress-bar'
		);
	}

	/**
	 * Is progress bar enabled
	 *
	 * @since 1.0.0
	 */
	public function is_progress_bar_enabled() {
		return xts_get_opt( 'single_product_stock_progress_bar' ) || xts_get_opt( 'product_loop_stock_progress_bar' );
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'single_product_stock_progress_bar',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Stock progress bar', 'xts-theme' ),
				'description' => 'Display a number of sold and in stock products as a progress bar. <a href="' . esc_url( XTS_DOCS_URL ) . 'products-stock-progress-bar" target="_blank">Documentation.</a>',
				'section'     => 'single_product_elements_section',
				'class'       => 'xts-col-6',
				'default'     => '0',
				'priority'    => 40,
			)
		);

		Options::add_field(
			array(
				'id'          => 'product_loop_stock_progress_bar',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Stock progress bar', 'xts-theme' ),
				'description' => 'Display a number of sold and in stock products as a progress bar. <a href="' . esc_url( XTS_DOCS_URL ) . 'products-stock-progress-bar" target="_blank">Documentation.</a>',
				'section'     => 'product_archive_product_options_section',
				'default'     => '0',
				'priority'    => 60,
			)
		);
	}
}
