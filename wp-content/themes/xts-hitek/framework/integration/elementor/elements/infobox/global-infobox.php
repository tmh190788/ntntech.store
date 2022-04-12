<?php
/**
 * Infobox global map file
 *
 * @package xts
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_infobox_content_title_map' ) ) {
	/**
	 * Get infobox title map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_infobox_content_title_map( $element ) {
		$element->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Infobox title, click to edit.',
			]
		);
	}
}

if ( ! function_exists( 'xts_get_infobox_style_title_map' ) ) {
	/**
	 * Get infobox title map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_infobox_style_title_map( $element ) {
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
				'selector'          => '{{WRAPPER}} .xts-box-title',
				'hover_selector'    => '{{WRAPPER}} .xts-infobox:hover .xts-box-title',
				'key'               => 'title',
				'text_size_default' => 'm',
			)
		);
	}
}

if ( ! function_exists( 'xts_get_infobox_content_subtitle_map' ) ) {
	/**
	 * Get infobox subtitle map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_infobox_content_subtitle_map( $element ) {
		$element->add_control(
			'subtitle',
			[
				'label'   => esc_html__( 'Subtitle', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Infobox subtitle text',
			]
		);
	}
}

if ( ! function_exists( 'xts_get_infobox_style_subtitle_map' ) ) {
	/**
	 * Get infobox subtitle map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_infobox_style_subtitle_map( $element ) {
		xts_get_typography_map(
			$element,
			array(
				'selector'          => '{{WRAPPER}} .xts-box-subtitle',
				'key'               => 'subtitle',
				'text_size_default' => 's',
			)
		);
	}
}

if ( ! function_exists( 'xts_get_infobox_content_description_map' ) ) {
	/**
	 * Get infobox description map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_infobox_content_description_map( $element ) {
		$element->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'xts-theme' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.',
			]
		);
	}
}

if ( ! function_exists( 'xts_get_infobox_content_general_map' ) ) {
	/**
	 * Get infobox general  map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_infobox_content_general_map( $element ) {
		$element->add_control(
			'infobox_link',
			[
				'label'   => esc_html__( 'Infobox link', 'xts-theme' ),
				'type'    => Controls_Manager::URL,
				'default' => [
					'url'         => '',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);
	}
}

if ( ! function_exists( 'xts_get_infobox_style_description_map' ) ) {
	/**
	 * Get infobox description map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_infobox_style_description_map( $element ) {
		xts_get_typography_map(
			$element,
			array(
				'selector' => '{{WRAPPER}} .xts-box-desc',
				'key'      => 'description',
			)
		);
	}
}

if ( ! function_exists( 'xts_get_infobox_content_icon_map' ) ) {
	/**
	 * Get infobox icon map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_infobox_content_icon_map( $element ) {
		$element->add_control(
			'icon_type',
			[
				'label'       => esc_html__( 'Icon type', 'xts-theme' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => [
					'icon'  => [
						'title' => esc_html__( 'Icon', 'xts-theme' ),
						'icon'  => 'fa fa-info',
					],
					'text'  => [
						'title' => esc_html__( 'Text', 'xts-theme' ),
						'icon'  => 'fa fa-text-width',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'xts-theme' ),
						'icon'  => 'fa fa-image',
					],
				],
				'toggle'      => false,
				'default'     => 'icon',
			]
		);

		$element->add_control(
			'icon',
			[
				'label'     => esc_html__( 'Icon', 'xts-theme' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'icon_type' => [ 'icon' ],
				],
			]
		);

		$element->add_control(
			'text_icon',
			[
				'label'     => esc_html__( 'Text icon', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '1.',
				'condition' => [
					'icon_type' => [ 'text' ],
				],
			]
		);

		$element->add_control(
			'image',
			[
				'label'     => esc_html__( 'Choose image', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => xts_get_elementor_placeholder_image_src(),
				],
				'condition' => [
					'icon_type' => [ 'image' ],
				],
			]
		);

		$element->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => [
					'icon_type' => [ 'image' ],
				],
			]
		);

		$element->add_control(
			'icon_size',
			[
				'label'     => esc_html__( 'Icon size', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					's'      => esc_html__( 'Small', 'xts-theme' ),
					'm'      => esc_html__( 'Medium', 'xts-theme' ),
					'l'      => esc_html__( 'Large', 'xts-theme' ),
					'custom' => esc_html__( 'Custom', 'xts-theme' ),
				],
				'default'   => 'm',
				'condition' => [
					'icon_type!' => [ 'image' ],
				],
			]
		);

		$element->add_responsive_control(
			'custom_icon_size',
			[
				'label'      => esc_html__( 'Custom icon size', 'xts-theme' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 30,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .xts-box-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'icon_type!' => [ 'image' ],
					'icon_size'  => [ 'custom' ],
				],
			]
		);
	}
}

if ( ! function_exists( 'xts_get_infobox_style_icon_map' ) ) {
	/**
	 * Get infobox icon map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_infobox_style_icon_map( $element ) {
		$element->add_control(
			'icon_position',
			[
				'label'   => esc_html__( 'Icon position', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'side' => array(
						'title' => esc_html__( 'Side', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/infobox/icon-position/side.svg',
						'style' => 'col-2',
					),
					'top'  => array(
						'title' => esc_html__( 'Top', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/infobox/icon-position/top.svg',
					),
				],
				'default' => 'top',
			]
		);

		$element->add_control(
			'icon_vertical_position',
			[
				'label'     => esc_html__( 'Vertical align', 'xts-theme' ),
				'type'      => 'xts_buttons',
				'options'   => [
					'start'  => array(
						'title' => esc_html__( 'Start', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/infobox/icon-vertical-position/start.svg',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/infobox/icon-vertical-position/center.svg',
					),
					'end'    => array(
						'title' => esc_html__( 'End', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/infobox/icon-vertical-position/end.svg',
					),
				],
				'condition' => [
					'icon_position' => [ 'side' ],
				],
				'default'   => 'start',
			]
		);

		$element->add_control(
			'icon_position_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		/**
		 * Icon color
		 */
		xts_get_color_map(
			$element,
			array(
				'key'              => 'icon',
				'normal_selectors' => array(
					'{{WRAPPER}} .xts-box-icon'    => 'color: {{VALUE}}',
					'{{WRAPPER}} .xts-infobox svg' => 'fill: {{VALUE}}',
				),
				'hover_selectors'  => array(
					'{{WRAPPER}} .xts-infobox:hover svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .xts-infobox:hover .xts-box-icon' => 'color: {{VALUE}}',
				),
			)
		);

		/**
		 * Border icon color
		 */
		xts_get_border_color_map(
			$element,
			array(
				'key'              => 'icon',
				'normal_selectors' => array(
					'{{WRAPPER}} .xts-box-icon' => 'border-color: {{VALUE}}',
				),
				'hover_selectors'  => array(
					'{{WRAPPER}} .xts-infobox:hover .xts-box-icon' => 'border-color: {{VALUE}}',
				),
			)
		);

		/**
		 * Background icon color.
		 */
		xts_get_background_color_map(
			$element,
			array(
				'key'                  => 'icon',
				'normal_selectors'     => array(
					'{{WRAPPER}} .xts-box-icon' => 'background-color: {{VALUE}}',
				),
				'normal_default_color' => '#f9f9f9',
				'hover_selectors'      => array(
					'{{WRAPPER}} .xts-infobox:hover .xts-box-icon' => 'background-color: {{VALUE}}',
				),
			)
		);

		/**
		 * Icon shape
		 */
		$element->add_control(
			'icon_shape',
			[
				'label'      => esc_html__( 'Icon shape', 'xts-theme' ),
				'type'       => 'xts_buttons',
				'options'    => [
					'square'  => array(
						'title' => esc_html__( 'Rectangle', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/infobox/icon-shape/rectangle.svg',
					),
					'rounded' => array(
						'title' => esc_html__( 'Rounded', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/infobox/icon-shape/rounded.svg',
					),
					'round'   => array(
						'title' => esc_html__( 'Round', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/infobox/icon-shape/round.svg',
					),
				],
				'default'    => 'square',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'icon_border_color_switcher',
							'operator' => '===',
							'value'    => 'yes',
						],
						[
							'name'     => 'icon_background_color_switcher',
							'operator' => '===',
							'value'    => 'yes',
						],
					],
				],
			]
		);

	}
}

if ( ! function_exists( 'xts_get_infobox_style_general_map' ) ) {
	/**
	 * Get infobox general map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_infobox_style_general_map( $element ) {
		$element->add_control(
			'content_align',
			[
				'label'   => esc_html__( 'Content alignment', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'left'   => array(
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/infobox/align/left.svg',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/infobox/align/center.svg',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/infobox/align/right.svg',
					),
				],
				'default' => 'center',
			]
		);

		$element->add_control(
			'content_align_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		/**
		 * Color scheme settings
		 */
		xts_get_color_scheme_map(
			$element,
			[
				'key' => 'infobox',
			]
		);

		/**
		 * Background color settings
		 */
		xts_get_background_map(
			$element,
			[
				'key'             => 'infobox',
				'normal_selector' => '{{WRAPPER}} .xts-infobox',
				'hover_selector'  => '{{WRAPPER}} .xts-infobox .xts-box-overlay',
			]
		);

		/**
		 * Border settings
		 */
		xts_get_border_map(
			$element,
			[
				'key'             => 'infobox',
				'normal_selector' => '{{WRAPPER}} .xts-infobox',
				'hover_selector'  => '{{WRAPPER}} .xts-infobox:hover',
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$element,
			[
				'key'             => 'infobox',
				'normal_selector' => '{{WRAPPER}} .xts-infobox',
				'hover_selector'  => '{{WRAPPER}} .xts-infobox:hover',
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
			'box_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .xts-infobox' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}
}
