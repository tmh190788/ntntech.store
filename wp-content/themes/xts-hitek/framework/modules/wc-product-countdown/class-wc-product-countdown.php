<?php
/**
 * Product sale countdown
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
class WC_Product_Countdown extends Module {
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
		if ( xts_get_opt( 'single_product_sale_countdown' ) ) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'product_single_countdown' ), 15 );
		}

		$this->define_constants();
		$this->include_files();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		if ( ! defined( 'XTS_SALE_COUNTDOWN_DIR' ) ) {
			define( 'XTS_SALE_COUNTDOWN_DIR', XTS_FRAMEWORK_ABSPATH . '/modules/wc-product-countdown/' );
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
			$path = XTS_SALE_COUNTDOWN_DIR . $file . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Product countdown for single product
	 *
	 * @since 1.0.0
	 */
	public function product_single_countdown() {
		$this->product_countdown( 'xts-with-shadow' );
	}

	/**
	 * Product countdown
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Extra classes.
	 */
	public function product_countdown( $classes = '' ) {
		global $product;

		$sale_date_end   = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );
		$sale_date_start = get_post_meta( $product->get_id(), '_sale_price_dates_from', true );

		if ( $product->is_type( 'variable' ) ) {
			$cache                = apply_filters( 'xts_countdown_variable_cache', true );
			$transient_name       = 'xts_countdown_variable_cache_' . $product->get_id();
			$available_variations = array();

			if ( $cache ) {
				$available_variations = get_transient( $transient_name );
			}

			if ( ! $available_variations ) {
				$available_variations = $product->get_available_variations();
				if ( $cache ) {
					set_transient( $transient_name, $available_variations, apply_filters( 'xts_countdown_variable_cache_time', WEEK_IN_SECONDS ) );
				}
			}

			if ( $available_variations ) {
				$sale_date_end   = get_post_meta( $available_variations[0]['variation_id'], '_sale_price_dates_to', true );
				$sale_date_start = get_post_meta( $available_variations[0]['variation_id'], '_sale_price_dates_from', true );
			}
		}

		$current_date = strtotime( date( 'Y-m-d H:i:s' ) );

		if ( $sale_date_end < $current_date || $current_date < $sale_date_start ) {
			return;
		}

		xts_countdown_timer_template(
			array(
				'extra_classes' => $classes,
				'date'          => date( 'Y-m-d H:i:s', $sale_date_end ),
				'align'         => 'left',
			)
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
				'id'          => 'single_product_sale_countdown',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Countdown timer', 'xts-theme' ),
				'description' => 'Show timer for products that have scheduled date for the sale price. <a href="' . esc_url( XTS_DOCS_URL ) . 'product-sale-countdown" target="_blank">Documentation.</a>',
				'section'     => 'single_product_elements_section',
				'class'       => 'xts-col-6',
				'default'     => '0',
				'priority'    => 50,
			)
		);

		Options::add_field(
			array(
				'id'          => 'product_loop_sale_countdown',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Countdown timer', 'xts-theme' ),
				'description' => 'Show timer for products that have scheduled date for the sale price. <a href="' . esc_url( XTS_DOCS_URL ) . 'product-sale-countdown" target="_blank">Documentation.</a>',
				'section'     => 'product_archive_product_options_section',
				'default'     => '0',
				'priority'    => 70,
			)
		);
	}
}
