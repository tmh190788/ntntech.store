<?php
/**
 * Infobox global map file
 *
 * @package xts
 */

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_social_buttons_style_buttons_map' ) ) {
	/**
	 * Get social buttons style map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_social_buttons_style_buttons_map( $element ) {
		$element->add_control(
			'size',
			[
				'label'   => esc_html__( 'Size', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					's' => esc_html__( 'Small', 'xts-theme' ),
					'm' => esc_html__( 'Medium', 'xts-theme' ),
					'l' => esc_html__( 'Large', 'xts-theme' ),
				],
				'default' => 'm',
			]
		);

		$element->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => xts_get_available_options( 'social_buttons_style_elementor' ),
				'default' => 'default',
			]
		);

		$element->add_control(
			'shape',
			[
				'label'     => esc_html__( 'Shape', 'xts-theme' ),
				'type'      => 'xts_buttons',
				'options'   => [
					'round'   => array(
						'title' => esc_html__( 'Round', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/social-buttons/shape/round.svg',
					),
					'rounded' => array(
						'title' => esc_html__( 'Rounded', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/social-buttons/shape/rounded.svg',
					),
					'square'  => array(
						'title' => esc_html__( 'Square', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/social-buttons/shape/square.svg',
					),
				],
				'condition' => [
					'style!' => [ 'default', 'with-text' ],
				],
				'default'   => 'round',
			]
		);
	}
}
