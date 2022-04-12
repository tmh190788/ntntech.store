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

if ( ! function_exists( 'xts_get_button_content_general_map' ) ) {
	/**
	 * Get button map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_button_content_general_map( $element, $custom_args = array() ) {
		$default_args = array(
			'link'          => true,
			'smooth_scroll' => false,
			'product'       => false,
			'text'          => 'Read more',
		);

		$args = wp_parse_args( $custom_args, $default_args );

		if ( $args['product'] ) {
			$element->add_control(
				'button_link_type',
				[
					'label'   => esc_html__( 'Link type', 'xts-theme' ),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'link'    => esc_html__( 'Link', 'xts-theme' ),
						'product' => esc_html__( 'Product', 'xts-theme' ),
					],
					'default' => 'link',
				]
			);
		}

		$element->add_control(
			'button_text',
			[
				'label'   => esc_html__( 'Text', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $args['text'],
			]
		);

		if ( $args['product'] ) {
			$element->add_control(
				'button_product_id_1',
				[
					'label'       => esc_html__( 'Select product 1', 'xts-theme' ),
					'type'        => 'xts_autocomplete',
					'search'      => 'xts_get_posts_by_query',
					'render'      => 'xts_get_posts_title_by_id',
					'post_type'   => 'product',
					'multiple'    => false,
					'label_block' => true,
					'condition'   => [
						'button_link_type' => [ 'product' ],
					],
				]
			);

			$element->add_control(
				'button_product_id_2',
				[
					'label'       => esc_html__( 'Select product 2', 'xts-theme' ),
					'type'        => 'xts_autocomplete',
					'search'      => 'xts_get_posts_by_query',
					'render'      => 'xts_get_posts_title_by_id',
					'post_type'   => 'product',
					'multiple'    => false,
					'label_block' => true,
					'condition'   => [
						'button_link_type' => [ 'product' ],
					],
				]
			);

			$element->add_control(
				'button_product_id_3',
				[
					'label'       => esc_html__( 'Select product 3', 'xts-theme' ),
					'description' => esc_html__( 'You can specify the product for three different states and then use our "Price plan switcher" element to allow customers to switch between these products.', 'xts-theme' ),
					'type'        => 'xts_autocomplete',
					'search'      => 'xts_get_posts_by_query',
					'render'      => 'xts_get_posts_title_by_id',
					'post_type'   => 'product',
					'multiple'    => false,
					'label_block' => true,
					'condition'   => [
						'button_link_type' => [ 'product' ],
					],
				]
			);
		}

		if ( $args['link'] ) {
			$button_link = [
				'label'   => esc_html__( 'Link', 'xts-theme' ),
				'type'    => Controls_Manager::URL,
				'default' => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			];

			if ( $args['product'] ) {
				$button_link['condition'] = [
					'button_link_type' => [ 'link' ],
				];
			}

			$element->add_control(
				'button_link',
				$button_link
			);
		}

		if ( $args['smooth_scroll'] ) {
			$element->add_control(
				'button_smooth_scroll',
				[
					'label'        => esc_html__( 'Smooth scroll', 'xts-theme' ),
					'description'  => esc_html__(
						'When you turn on this option you need to specify this button link with a hash symbol. For example #section-id
Then you need to have a section with an ID of "section-id" and this button click will smoothly scroll the page to that section.',
						'xts-theme'
					),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
					'label_off'    => esc_html__( 'No', 'xts-theme' ),
					'return_value' => 'yes',
					'separator'    => 'before',
				]
			);

			$element->add_control(
				'button_smooth_scroll_time',
				[
					'label'     => esc_html__( 'Smooth scroll time (ms)', 'xts-theme' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 100,
					'condition' => [
						'button_smooth_scroll' => [ 'yes' ],
					],
				]
			);

			$element->add_control(
				'button_smooth_scroll_offset',
				[
					'label'     => esc_html__( 'Smooth scroll offset (px)', 'xts-theme' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 100,
					'condition' => [
						'button_smooth_scroll' => [ 'yes' ],
					],
				]
			);
		}
	}
}

if ( ! function_exists( 'xts_get_button_style_general_map' ) ) {
	/**
	 * Get button map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_button_style_general_map( $element, $custom_args = array() ) {
		$default_args = array(
			'size'  => 'm',
			'color' => 'primary',
			'style' => 'default',
			'shape' => xts_get_default_value( 'button_element_shape' ),
			'align' => 'left',
		);

		$args = wp_parse_args( $custom_args, $default_args );

		if ( $args['align'] ) {
			$element->add_control(
				'button_align',
				[
					'label'   => esc_html__( 'Alignment', 'xts-theme' ),
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
					'default' => $args['align'],
				]
			);
		}

		$element->add_control(
			'button_full_width',
			[
				'label'        => esc_html__( 'Full width', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'button_size',
			[
				'label'   => esc_html__( 'Size', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'xs' => esc_html__( 'Extra small', 'xts-theme' ),
					's'  => esc_html__( 'Small', 'xts-theme' ),
					'm'  => esc_html__( 'Medium', 'xts-theme' ),
					'l'  => esc_html__( 'Large', 'xts-theme' ),
					'xl' => esc_html__( 'Extra large', 'xts-theme' ),
				],
				'default' => $args['size'],
			]
		);

		$element->add_control(
			'button_color',
			[
				'label'   => esc_html__( 'Color', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'   => esc_html__( 'Default', 'xts-theme' ),
					'primary'   => esc_html__( 'Primary', 'xts-theme' ),
					'secondary' => esc_html__( 'Secondary', 'xts-theme' ),
					'white'     => esc_html__( 'White', 'xts-theme' ),
					'custom'    => esc_html__( 'Custom', 'xts-theme' ),
				],
				'default' => $args['color'],
			]
		);

		$element->start_controls_tabs(
			'button_tabs_style',
			[
				'condition' => [
					'button_color' => 'custom',
				],
			]
		);

		$element->start_controls_tab(
			'button_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'xts-theme' ),
			]
		);

		$element->add_control(
			'button_text_color',
			[
				'label'     => esc_html__( 'Text color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .xts-button' => 'color: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'button_bg_color',
			[
				'label'     => esc_html__( 'Background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'button_style!' => [ 'bordered', 'link', 'link-2', 'link-3' ],
				],
			]
		);

		$element->add_control(
			'button_border_color',
			[
				'label'     => esc_html__( 'Border color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-button' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_style' => [ 'bordered', 'link' ],
				],
			]
		);

		$element->add_control(
			'button_border_color_link_2',
			[
				'label'     => esc_html__( 'Border color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-button:after' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_style' => [ 'link-2' ],
				],
			]
		);

		$element->end_controls_tab();

		$element->start_controls_tab(
			'button_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'xts-theme' ),
			]
		);

		$element->add_control(
			'button_text_hover_color',
			[
				'label'     => esc_html__( 'Text color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .xts-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'button_bg_hover_color',
			[
				'label'     => esc_html__( 'Background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-button:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'button_style!' => [ 'link', 'link-2', 'link-3' ],
				],
			]
		);

		$element->add_control(
			'button_border_hover_color',
			[
				'label'     => esc_html__( 'Border color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_style' => [ 'bordered', 'link' ],
				],
			]
		);

		$element->add_control(
			'button_border_hover_color_link_2',
			[
				'label'     => esc_html__( 'Border color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-button:hover:after' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_style' => [ 'link-2' ],
				],
			]
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

		$element->add_control(
			'button_custom_color_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'style'     => 'thick',
				'condition' => [
					'button_color' => [ 'custom' ],
				],
			]
		);

		$element->add_control(
			'button_style',
			[
				'label'   => esc_html__( 'Style', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => xts_get_available_options( 'button_style_elementor' ),
				'default' => $args['style'],
			]
		);

		$element->add_control(
			'button_shape',
			[
				'label'     => esc_html__( 'Shape', 'xts-theme' ),
				'type'      => 'xts_buttons',
				'options'   => [
					'rectangle' => array(
						'title' => esc_html__( 'Rectangle', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/button/shape/rectangle.svg',
					),
					'rounded'   => array(
						'title' => esc_html__( 'Rounded', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/button/shape/rounded.svg',
					),
					'round'     => array(
						'title' => esc_html__( 'Round', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/button/shape/round.svg',
					),
				],
				'default'   => $args['shape'],
				'condition' => [
					'button_style!' => [ 'link', 'link-2', 'link-3' ],
				],
			]
		);

		/**
		 * Shadow settings
		 */
		if ( xts_theme_supports( 'buttons-shadow' ) ) {
			xts_get_shadow_map(
				$element,
				[
					'key'                => 'button',
					'normal_selector'    => '{{WRAPPER}} .xts-button',
					'hover_selector'     => '{{WRAPPER}} .xts-button:hover',
					'switcher_condition' => [
						'button_style!' => [ 'link', 'bordered' ],
					],
					'tabs_condition'     => [
						'button_style!' => [ 'link' ],
					],
					'divider'            => 'no',
				]
			);
		}
	}
}


if ( ! function_exists( 'xts_get_button_style_icon_map' ) ) {
	/**
	 * Get button map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param bool   $heading Is heading needed.
	 */
	function xts_get_button_style_icon_map( $element, $heading = true ) {
		if ( $heading ) {
			$element->add_control(
				'button_icon_heading',
				[
					'label'     => esc_html__( 'Icon', 'xts-theme' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);
		}

		$element->add_control(
			'button_icon_type',
			[
				'label'       => esc_html__( 'Type', 'xts-theme' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => [
					'icon'  => [
						'title' => esc_html__( 'Icon', 'xts-theme' ),
						'icon'  => 'fa fa-info',
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
			'button_icon',
			[
				'label'     => esc_html__( 'Icon', 'xts-theme' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'button_icon_type' => [ 'icon' ],
				],
			]
		);

		$element->add_control(
			'button_icon_image',
			[
				'label'     => esc_html__( 'Choose image', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => [
					'button_icon_type' => [ 'image' ],
				],
			]
		);

		$element->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'button_icon_image',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => [
					'button_icon_type' => [ 'image' ],
				],
			]
		);

		$element->add_control(
			'button_icon_position',
			[
				'label'   => esc_html__( 'Position', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'left'  => array(
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/button/icon-position/left.svg',
						'style' => 'col-2',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/button/icon-position/right.svg',
					),
				],
				'default' => 'right',
			]
		);

		$element->add_control(
			'button_icon_size',
			[
				'label'     => esc_html__( 'Size', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'default' => esc_html__( 'Default', 'xts-theme' ),
					's'       => esc_html__( 'Small', 'xts-theme' ),
					'm'       => esc_html__( 'Medium', 'xts-theme' ),
					'l'       => esc_html__( 'Large', 'xts-theme' ),
				],
				'default'   => 'default',
				'condition' => [
					'button_icon_type!' => [ 'image' ],
				],
			]
		);

		$element->add_control(
			'button_icon_style',
			[
				'label'     => esc_html__( 'Style', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'default' => esc_html__( 'Default', 'xts-theme' ),
					'bg'      => esc_html__( 'With background', 'xts-theme' ),
				],
				'default'   => 'default',
				'condition' => [
					'button_style!' => [ 'bordered', 'link' ],
				],
			]
		);

		$element->add_control(
			'button_icon_animation',
			[
				'label'   => esc_html__( 'Animation', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'without'    => esc_html__( 'Without', 'xts-theme' ),
					'fade-left'  => esc_html__( 'Fade from left', 'xts-theme' ),
					'fade-right' => esc_html__( 'Fade from right', 'xts-theme' ),
					'move-left'  => esc_html__( 'Move to left', 'xts-theme' ),
					'move-right' => esc_html__( 'Move to right', 'xts-theme' ),
				],
				'default' => 'without',
			]
		);
	}
}
