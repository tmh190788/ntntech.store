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
use XTS\Options\Metaboxes;

/**
 * Product brands
 *
 * @since 1.0.0
 */
class WC_Brands_Background extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_metaboxes' ) );
	}

	/**
	 * Add metaboxes
	 *
	 * @since 1.0.0
	 */
	public function add_metaboxes() {
		$brand_attribute = xts_get_opt( 'brands_attribute' );

		$metaboxes = Metaboxes::add_metabox(
			array(
				'id'         => 'xts_brand_background_metaboxes',
				'title'      => esc_html__( 'Brand background metaboxes', 'xts-theme' ),
				'object'     => 'term',
				'taxonomies' => array( $brand_attribute ),
			)
		);

		$metaboxes->add_section(
			array(
				'id'       => 'general',
				'name'     => esc_html__( 'General', 'xts-theme' ),
				'priority' => 10,
				'icon'     => 'xf-general',
			)
		);

		$metaboxes->add_field(
			array(
				'name'     => esc_html__( 'Background image', 'xts-theme' ),
				'id'       => 'brand_background_image',
				'type'     => 'upload',
				'section'  => 'general',
				'priority' => 10,
			)
		);

		$metaboxes->add_field(
			array(
				'name'     => esc_html__( 'Country', 'xts-theme' ),
				'id'       => 'brand_background_country',
				'type'     => 'text_input',
				'section'  => 'general',
				'priority' => 20,
			)
		);
	}
}
