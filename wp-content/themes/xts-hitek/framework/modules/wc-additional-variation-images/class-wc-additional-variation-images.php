<?php
/**
 * Additional Variation Images
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\WC_Additional_Variation_Images\Admin;
use XTS\WC_Additional_Variation_Images\Functions;
use XTS\Framework\Options;

/**
 * Additional Variation Images
 *
 * @since 1.0.0
 */
class WC_Additional_Variation_Images extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->define_constants();
		$this->include_files();

		Admin::get_instance();
		Functions::get_instance();

		add_action( 'init', array( $this, 'add_options' ) );
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		if ( ! defined( 'XTS_ADDITIONAL_VARIATION_IMAGES_DIR' ) ) {
			define( 'XTS_ADDITIONAL_VARIATION_IMAGES_DIR', XTS_FRAMEWORK_ABSPATH . '/modules/wc-additional-variation-images/' );
		}
	}

	/**
	 * Include main files.
	 *
	 * @since 1.0.0
	 */
	private function include_files() {
		$files = array(
			'class-admin',
			'class-functions',
		);

		foreach ( $files as $file ) {
			$path = XTS_ADDITIONAL_VARIATION_IMAGES_DIR . $file . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
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
				'id'          => 'single_product_additional_variations_images',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Additional variations images', 'xts-theme' ),
				'description' => esc_html__( 'Add an ability to upload additional images for each variation in variable products.', 'xts-theme' ),
				'group'       => esc_html__( 'Settings', 'xts-theme' ),
				'section'     => 'single_product_image_section',
				'default'     => '1',
				'priority'    => 100,
			)
		);
	}
}
