<?php
/**
 * Elementor text editor custom controls
 *
 * @package xts
 */

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_add_color_scheme_to_text_element' ) ) {
	/**
	 * Add color scheme option to text element
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	function xts_add_color_scheme_to_text_element( $element ) {
		$element->add_control(
			'xts_color_scheme',
			[
				'label'        => esc_html__( 'Color scheme', 'xts-theme' ),
				'type'         => 'xts_buttons',
				'options'      => [
					'inherit' => [
						'title' => esc_html__( 'Inherit', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/inherit.svg',
					],
					'dark'    => [
						'title' => esc_html__( 'Dark', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/dark.svg',
					],
					'light'   => [
						'title' => esc_html__( 'Light', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/light.svg',
					],
				],
				'default'      => 'inherit',
				'prefix_class' => 'xts-scheme-',
				'render_type'  => 'template',
			]
		);
	}

	add_filter( 'elementor/element/text-editor/section_style/after_section_start', 'xts_add_color_scheme_to_text_element', 10, 2 );
}

if ( ! function_exists( 'xts_add_content_align_to_text_element' ) ) {
	/**
	 * Add content align option to text element
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	function xts_add_content_align_to_text_element( $element ) {
		$element->add_control(
			'xts_content_align',
			[
				'label'        => esc_html__( 'Content align', 'xts-theme' ),
				'type'         => 'xts_buttons',
				'options'      => [
					'inherit' => [
						'title' => esc_html__( 'Inherit', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/inherit.svg',
					],
					'left'    => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/left.svg',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/center.svg',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/right.svg',
					],
				],
				'default'      => 'inherit',
				'prefix_class' => 'xts-textalign-',
				'render_type'  => 'template',
			]
		);
	}

	add_filter( 'elementor/element/text-editor/section_style/before_section_end', 'xts_add_content_align_to_text_element', 10, 2 );
}

if ( ! function_exists( 'xts_add_content_width_to_text_element' ) ) {
	/**
	 * Add content width option to text element
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	function xts_add_content_width_to_text_element( $element ) {
		$element->add_responsive_control(
			'xts_content_width',
			[
				'label'          => esc_html__( 'Content width', 'xts-theme' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units'     => [ 'px', '%' ],
				'range'          => [
					'%'  => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors'      => [
					'{{WRAPPER}} .elementor-widget-container' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
	}

	add_filter( 'elementor/element/text-editor/section_style/before_section_end', 'xts_add_content_width_to_text_element', 20, 2 );
}
