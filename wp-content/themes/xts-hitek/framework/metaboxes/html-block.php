<?php
/**
 * Register html block metaboxes.
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Metaboxes;

if ( ! function_exists( 'xts_register_html_block_metaboxes' ) ) {
	/**
	 * Register html block metaboxes.
	 *
	 * @since 1.0.0
	 */
	function xts_register_html_block_metaboxes() {
		$metabox = Metaboxes::add_metabox(
			array(
				'id'         => 'xts_html_block_metaboxes',
				'title'      => esc_html__( 'HTML Block metaboxes', 'xts-theme' ),
				'post_types' => array( 'xts-html-block' ),
			)
		);

		$metabox->add_section(
			array(
				'id'       => 'general_section',
				'name'     => esc_html__( 'General', 'xts-theme' ),
				'priority' => 10,
				'icon'     => 'xf-general',
			)
		);

		/**
		 * General.
		 */
		$metabox->add_field(
			array(
				'id'          => 'negative_gap',
				'name'        => esc_html__( 'Negative gap', 'xts-theme' ),
				'description' => esc_html__( 'Add a negative margin to each Elementor section to align the content with your website container.', 'xts-theme' ),
				'type'        => 'buttons',
				'section'     => 'general_section',
				'options'     => array(
					'inherit'  => array(
						'name'  => esc_html__( 'Inherit', 'xts-theme' ),
						'value' => 'inherit',
					),
					'enabled'  => array(
						'name'  => esc_html__( 'Enabled', 'xts-theme' ),
						'value' => 'enabled',
					),
					'disabled' => array(
						'name'  => esc_html__( 'Disabled', 'xts-theme' ),
						'value' => 'disabled',
					),
				),
				'default'     => 'inherit',
				'priority'    => 10,
			)
		);
	}

	add_action( 'init', 'xts_register_html_block_metaboxes', 100 );
}
