<?php
/**
 * Banner global map file
 *
 * @package xts
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_banner_style_general_map' ) ) {
	/**
	 * Get banner general style map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_banner_style_general_map( $element, $custom_args = array() ) {
		$default_args = [
			'banner_hover_options' => xts_get_available_options( 'banner_element_hover_effect_elementor' ),
		];

		$args = wp_parse_args( $custom_args, $default_args );

		$element->add_control(
			'banner_style',
			[
				'label'   => esc_html__( 'Style', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => xts_get_available_options( 'banner_design_elementor' ),
				'default' => 'default',
			]
		);

		do_action( 'xts_banner_style_general_after_banner_style', $element );

		$element->add_control(
			'banner_hover',
			[
				'label'   => esc_html__( 'Hover effect', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $args['banner_hover_options'],
				'default' => 'none',
			]
		);

		$element->add_control(
			'color_scheme',
			[
				'label'   => esc_html__( 'Color scheme', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
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
				'default' => 'light',
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$element,
			[
				'key'             => 'banner',
				'normal_selector' => '{{WRAPPER}} .xts-iimage',
				'hover_selector'  => '{{WRAPPER}} .xts-iimage:hover',
				'divider'         => 'no',
			]
		);

		/**
		 * Padding settings
		 */
		$element->add_control(
			'padding_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$element->add_responsive_control(
			'banner_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .xts-iimage-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		/**
		 * Border radius settings
		 */
		$element->add_control(
			'border_radius_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$element->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .xts-iimage' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}
}

if ( ! function_exists( 'xts_get_banner_layout_map' ) ) {
	/**
	 * Get banner layout map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param string $repeater Repeater current item.
	 */
	function xts_get_banner_layout_map( $element, $repeater = '' ) {
		$element->add_control(
			'content_align',
			[
				'label'   => esc_html__( 'Content alignment', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/left.svg',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/center.svg',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/right.svg',
					],
				],
				'default' => 'center',
			]
		);

		$element->add_responsive_control(
			'content_width',
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
					'{{WRAPPER}}' . $repeater . ' .xts-iimage-content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->add_control(
			'content_horizontal_position',
			[
				'label'   => esc_html__( 'Content horizontal position', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'start'  => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/banner/horizontal-position/left.svg',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/banner/horizontal-position/center.svg',
					],
					'end'    => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/banner/horizontal-position/right.svg',
					],
				],
				'default' => 'center',
			]
		);

		$element->add_control(
			'content_vertical_position',
			[
				'label'   => esc_html__( 'Content vertical position', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'start'  => [
						'title' => esc_html__( 'Top', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/banner/vertical-position/top.svg',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/banner/vertical-position/middle.svg',
					],
					'end'    => [
						'title' => esc_html__( 'Bottom', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/banner/vertical-position/bottom.svg',
					],
				],
				'default' => 'center',
			]
		);
	}
}

if ( ! function_exists( 'xts_get_banner_content_title_map' ) ) {
	/**
	 * Get banner title content map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_banner_content_title_map( $element ) {
		$element->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Banner title, click to edit.',
			]
		);
	}
}

if ( ! function_exists( 'xts_get_banner_style_title_map' ) ) {
	/**
	 * Get banner title style map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_banner_style_title_map( $element ) {
		$element->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Title tag', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => esc_html__( 'h1', 'xts-theme' ),
					'h2'   => esc_html__( 'h2', 'xts-theme' ),
					'h3'   => esc_html__( 'h3', 'xts-theme' ),
					'h4'   => esc_html__( 'h4', 'xts-theme' ),
					'h5'   => esc_html__( 'h5', 'xts-theme' ),
					'h6'   => esc_html__( 'h6', 'xts-theme' ),
					'p'    => esc_html__( 'p', 'xts-theme' ),
					'div'  => esc_html__( 'div', 'xts-theme' ),
					'span' => esc_html__( 'span', 'xts-theme' ),
				],
				'default' => 'h4',
			]
		);

		xts_get_typography_map(
			$element,
			array(
				'selector'          => '{{WRAPPER}} .xts-iimage-title',
				'key'               => 'title',
				'text_size_default' => 'm',
			)
		);
	}
}

if ( ! function_exists( 'xts_get_banner_content_subtitle_map' ) ) {
	/**
	 * Get banner subtitle content map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_banner_content_subtitle_map( $element ) {
		$element->add_control(
			'subtitle',
			[
				'label'   => esc_html__( 'Subtitle', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Banner subtitle text',
			]
		);
	}
}

if ( ! function_exists( 'xts_get_banner_style_subtitle_map' ) ) {
	/**
	 * Get banner subtitle style map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_banner_style_subtitle_map( $element ) {
		xts_get_typography_map(
			$element,
			array(
				'selector'              => '{{WRAPPER}} .xts-iimage-subtitle',
				'key'                   => 'subtitle',
				'text_size_default'     => 's',
				'color_presets_options' => xts_get_available_options( 'banner_subtitle_color_presets_elementor' ),
			)
		);

		do_action( 'xts_banner_style_subtitle_after_typography', $element );
	}
}

if ( ! function_exists( 'xts_get_banner_content_description_map' ) ) {
	/**
	 * Get banner description content map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_banner_content_description_map( $element ) {
		$element->add_control(
			'description',
			[
				'label' => esc_html__( 'Description', 'xts-theme' ),
				'type'  => Controls_Manager::WYSIWYG,
			]
		);
	}
}

if ( ! function_exists( 'xts_get_banner_style_description_map' ) ) {
	/**
	 * Get banner description style map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_banner_style_description_map( $element ) {
		xts_get_typography_map(
			$element,
			array(
				'selector' => '{{WRAPPER}} .xts-iimage-desc',
				'key'      => 'description',
			)
		);
	}
}

if ( ! function_exists( 'xts_get_banner_content_image_map' ) ) {
	/**
	 * Get banner image content map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param string $repeater Repeater current item.
	 */
	function xts_get_banner_content_image_map( $element, $repeater = '' ) {
		$element->add_control(
			'image_type',
			[
				'label'   => esc_html__( 'Display', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'background' => esc_html__( 'As background', 'xts-theme' ),
					'image'      => esc_html__( 'As image', 'xts-theme' ),
				],
				'default' => 'image',
			]
		);

		$element->add_control(
			'image',
			[
				'label'   => esc_html__( 'Choose image', 'xts-theme' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => xts_get_elementor_placeholder_image_src( 'banner' ),
				],
			]
		);

		$element->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'large',
				'separator' => 'none',
			]
		);

		$element->add_responsive_control(
			'image_height',
			[
				'label'     => esc_html__( 'Image height', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 300,
				],
				'range'     => [
					'px' => [
						'min'  => 100,
						'max'  => 2000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' . $repeater . ' .xts-iimage img' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'image_type' => [ 'background' ],
				],
			]
		);

		$element->add_control(
			'image_bg_position',
			[
				'label'     => esc_html__( 'Background position', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'center-center' => esc_html__( 'Center', 'xts-theme' ),
					'center-top'    => esc_html__( 'Top', 'xts-theme' ),
					'center-bottom' => esc_html__( 'Bottom', 'xts-theme' ),
					'left-center'   => esc_html__( 'Left', 'xts-theme' ),
					'right-center'  => esc_html__( 'Right', 'xts-theme' ),
				],
				'default'   => 'center-center',
				'condition' => [
					'image_type' => [ 'background' ],
				],
			]
		);
	}
}

if ( ! function_exists( 'xts_get_banner_content_general_map' ) ) {
	/**
	 * Get banner general content map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_banner_content_general_map( $element ) {
		$element->add_control(
			'banner_link',
			[
				'label'   => esc_html__( 'Banner link', 'xts-theme' ),
				'type'    => Controls_Manager::URL,
				'default' => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);
	}
}
