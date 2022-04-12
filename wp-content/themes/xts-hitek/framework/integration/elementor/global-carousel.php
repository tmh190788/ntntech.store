<?php
/**
 * Carousel global map file
 *
 * @package xts
 */

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_carousel_map' ) ) {
	/**
	 * Get carousel map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_carousel_map( $element, $custom_args = array() ) {
		$default_args = array(
			'padding'                    => array(
				'top'    => '',
				'bottom' => '',
			),
			'items'                      => 3,
			'items_tablet'               => 2,
			'items_mobile'               => 2,
			'arrows_horizontal_position' => false,
			'center_mode_opacity'        => false,
			'spacing'                    => true,
		);

		$args = wp_parse_args( $custom_args, $default_args );

		$element->add_responsive_control(
			'carousel_items',
			[
				'label'          => esc_html__( 'Items per slide', 'xts-theme' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => $args['items'],
				],
				'tablet_default' => [
					'size' => $args['items_tablet'],
				],
				'mobile_default' => [
					'size' => $args['items_mobile'],
				],
				'size_units'     => '',
				'range'          => [
					'px' => [
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				],
			]
		);

		if ( $args['spacing'] ) {
			$element->add_control(
				'carousel_spacing',
				[
					'label'   => esc_html__( 'Items gap', 'xts-theme' ),
					'type'    => Controls_Manager::SELECT,
					'options' => xts_get_available_options( 'items_gap_elementor' ),
					'default' => xts_get_default_value( 'items_gap' ),
				]
			);
		}

		$element->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Autoplay', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'autoplay_speed',
			[
				'label'      => esc_html__( 'Autoplay interval (ms)', 'xts-theme' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => '',
				'default'    => [
					'size' => 2000,
				],
				'range'      => [
					'px' => [
						'min'  => 500,
						'max'  => 10000,
						'step' => 1,
					],
				],
				'condition'  => [
					'autoplay' => 'yes',
				],
			]
		);

		$element->add_control(
			'infinite_loop',
			[
				'label'        => esc_html__( 'Infinite loop', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'center_mode',
			[
				'label'        => esc_html__( 'Center mode', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		if ( $args['center_mode_opacity'] ) {
			$element->add_control(
				'center_mode_opacity',
				[
					'label'        => esc_html__( 'Center mode opacity', 'xts-theme' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
					'label_off'    => esc_html__( 'No', 'xts-theme' ),
					'return_value' => 'yes',
					'condition'    => [
						'center_mode' => 'yes',
					],
				]
			);
		}

		$element->add_control(
			'auto_height',
			[
				'label'        => esc_html__( 'Auto height', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'init_on_scroll',
			[
				'label'        => esc_html__( 'Init carousel on scroll', 'xts-theme' ),
				'description'  => esc_html__( 'This option allows you to init carousel script only when visitor scroll the page to the slider. Useful for performance optimization.', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'arrows_heading',
			[
				'label'     => esc_html__( 'Arrows', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$element->add_control(
			'arrows',
			[
				'label'        => esc_html__( 'Arrows', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		if ( $args['arrows_horizontal_position'] ) {
			$element->add_control(
				'arrows_horizontal_position',
				[
					'label'     => esc_html__( 'Horizontal position', 'xts-theme' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						'inside'  => esc_html__( 'Inside', 'xts-theme' ),
						'outside' => esc_html__( 'Outside', 'xts-theme' ),
					],
					'condition' => [
						'arrows' => [ 'yes' ],
					],
					'default'   => 'outside',
				]
			);
		}

		$element->add_control(
			'arrows_vertical_position',
			[
				'label'     => esc_html__( 'Vertical position', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'sides' => esc_html__( 'Sides', 'xts-theme' ),
					'top'   => esc_html__( 'Top', 'xts-theme' ),
				],
				'condition' => [
					'arrows' => [ 'yes' ],
				],
				'default'   => xts_get_default_value( 'carousel_arrows_vertical_position' ),
			]
		);

		$element->add_control(
			'arrows_color_scheme',
			[
				'label'     => esc_html__( 'Color scheme', 'xts-theme' ),
				'type'      => 'xts_buttons',
				'options'   => [
					'dark'  => [
						'title' => esc_html__( 'Dark', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/dark.svg',
					],
					'light' => [
						'title' => esc_html__( 'Light', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/light.svg',
					],
				],
				'condition' => [
					'arrows' => [ 'yes' ],
				],
				'default'   => xts_get_default_value( 'carousel_arrows_color_scheme' ),
			]
		);

		$element->add_control(
			'dots_heading',
			[
				'label'     => esc_html__( 'Dots', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$element->add_control(
			'dots',
			[
				'label'        => esc_html__( 'Dots', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'dots_color_scheme',
			[
				'label'     => esc_html__( 'Color scheme', 'xts-theme' ),
				'type'      => 'xts_buttons',
				'options'   => [
					'dark'  => [
						'title' => esc_html__( 'Dark', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/dark.svg',
					],
					'light' => [
						'title' => esc_html__( 'Light', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/light.svg',
					],
				],
				'condition' => [
					'dots' => [ 'yes' ],
				],
				'default'   => 'dark',
			]
		);

		$element->add_control(
			'sync_heading',
			[
				'label'     => esc_html__( 'Synchronization', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$element->add_control(
			'sync',
			[
				'label'   => esc_html__( 'Synchronization', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'disabled' => esc_html__( 'Disabled', 'xts-theme' ),
					'parent'   => esc_html__( 'As parent', 'xts-theme' ),
					'child'    => esc_html__( 'As child', 'xts-theme' ),
				],
				'default' => 'disabled',
			]
		);

		$element->add_control(
			'sync_parent_id',
			[
				'label'     => esc_html__( 'ID', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => uniqid(),
				'classes'   => 'xts-field-disabled',
				'condition' => [
					'sync' => [ 'parent' ],
				],
			]
		);

		$element->add_control(
			'sync_child_id',
			[
				'label'     => esc_html__( 'ID', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'sync' => [ 'child' ],
				],
			]
		);

		$element->add_control(
			'styles_heading',
			[
				'label'     => esc_html__( 'Styles', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$element->add_responsive_control(
			'padding',
			[
				'label'              => esc_html__( 'Padding', 'xts-theme' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'vertical',
				'render_type'        => 'template',
				'placeholder'        => [
					'top'    => $args['padding']['top'],
					'right'  => 'auto',
					'bottom' => $args['padding']['bottom'],
					'left'   => 'auto',
				],
				'default'            => [
					'top'    => $args['padding']['top'],
					'right'  => 'auto',
					'bottom' => $args['padding']['bottom'],
					'left'   => 'auto',
				],
				'selectors'          => [
					'{{WRAPPER}} .xts-carousel .xts-col:not(.xts-post-gallery-col)' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);
	}
}
