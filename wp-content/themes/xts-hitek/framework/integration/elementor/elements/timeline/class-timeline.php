<?php
/**
 * Timeline map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Timeline extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_timeline';
	}

	/**
	 * Get widget title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Timeline', 'xts-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'xf-el-timeline';
	}

	/**
	 * Get widget categories.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'xts-elements' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		/**
		 * Content tab
		 */

		/**
		 * Items settings
		 */
		$this->start_controls_section(
			'items_content_section',
			[
				'label' => esc_html__( 'Items', 'xts-theme' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'items_type',
			[
				'label'   => esc_html__( 'Items type', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'items'      => esc_html__( 'Timeline items', 'xts-theme' ),
					'breakpoint' => esc_html__( 'Breakpoint', 'xts-theme' ),
				],
				'default' => 'items',
			]
		);

		$repeater->add_responsive_control(
			'items_reverse',
			[
				'label'        => esc_html__( 'Items reverse', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
				'devices'      => [
					'desktop',
					'mobile',
				],
				'condition'    => [
					'items_type' => 'items',
				],
			]
		);

		$repeater->add_control(
			'breakpoint_text',
			[
				'label'     => esc_html__( 'Breakpoint text', 'xts-theme' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => 'Breakpoint text',
				'condition' => [
					'items_type' => 'breakpoint',
				],
			]
		);

		$repeater->start_controls_tabs(
			'item_tabs',
			[
				'condition' => [
					'items_type' => 'items',
				],
			]
		);

		$repeater->start_controls_tab(
			'first_tab_content',
			[
				'label' => esc_html__( 'First item', 'xts-theme' ),
			]
		);

		$this->get_item_content_map( $repeater, 'first' );

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'second_tab_content',
			[
				'label' => esc_html__( 'Second item', 'xts-theme' ),
			]
		);

		$this->get_item_content_map( $repeater, 'second' );

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'timeline_items',
			[
				'type'        => Controls_Manager::REPEATER,
				'title_field' => '{{{ items_type }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'first_title'        => 'Title example. Click to edit.',
						'first_subtitle'     => 'Subtitle text example',
						'first_description'  => 'Inceptos diam proin justo in nibh enim quam.',

						'second_title'       => 'Title example. Click to edit.',
						'second_subtitle'    => 'Subtitle text example',
						'second_description' => 'Inceptos diam proin justo in nibh enim quam.',
					],
					[
						'items_type'      => 'breakpoint',
						'breakpoint_text' => 'New era from 2020',
					],
					[
						'first_title'        => 'Title example. Click to edit.',
						'first_subtitle'     => 'Subtitle text example',
						'first_description'  => 'Inceptos diam proin justo in nibh enim quam.',

						'second_title'       => '',
						'second_subtitle'    => '',
						'second_description' => '',
					],
					[
						'first_title'        => '',
						'first_subtitle'     => '',
						'first_description'  => '',

						'second_title'       => 'Title example. Click to edit.',
						'second_subtitle'    => 'Subtitle text example',
						'second_description' => 'Inceptos diam proin justo in nibh enim quam.',
					],
					[
						'items_type'      => 'breakpoint',
						'breakpoint_text' => 'Starting from 1940',
					],
					[
						'first_title'        => 'Title example. Click to edit.',
						'first_subtitle'     => 'Subtitle text example',
						'first_description'  => 'Inceptos diam proin justo in nibh enim quam.',

						'second_title'       => 'Title example. Click to edit.',
						'second_subtitle'    => 'Subtitle text example',
						'second_description' => 'Inceptos diam proin justo in nibh enim quam.',
					],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style tab
		 */

		/**
		 * General settings
		 */
		$this->start_controls_section(
			'general_style_section',
			[
				'label' => esc_html__( 'General', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'general_style_tabs' );

		$this->start_controls_tab(
			'first_general_style_tab',
			[
				'label' => esc_html__( 'First item', 'xts-theme' ),
			]
		);

		$this->get_item_general_style_map(
			$this,
			[
				'key'             => 'first',
				'normal_selector' => '{{WRAPPER}} .xts-timeline-item-first',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'second_general_style_tab',
			[
				'label' => esc_html__( 'Second item', 'xts-theme' ),
			]
		);

		$this->get_item_general_style_map(
			$this,
			[
				'key'             => 'second',
				'normal_selector' => '{{WRAPPER}} .xts-timeline-item-second',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Subtitle settings
		 */
		$this->start_controls_section(
			'subtitle_section',
			[
				'label' => esc_html__( 'Subtitle', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'subtitle_style_tabs' );

		$this->start_controls_tab(
			'first_subtitle_style_tab',
			[
				'label' => esc_html__( 'First item', 'xts-theme' ),
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-timeline-item-first .xts-timeline-subtitle',
				'key'               => 'first_subtitle',
				'text_size_default' => 's',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'second_subtitle_style_tab',
			[
				'label' => esc_html__( 'Second item', 'xts-theme' ),
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-timeline-item-second .xts-timeline-subtitle',
				'key'               => 'second_subtitle',
				'text_size_default' => 's',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Title settings
		 */
		$this->start_controls_section(
			'title_section',
			[
				'label' => esc_html__( 'Title', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'title_style_tabs' );

		$this->start_controls_tab(
			'first_title_style_tab',
			[
				'label' => esc_html__( 'First item', 'xts-theme' ),
			]
		);

		$this->add_control(
			'first_title_tag',
			[
				'label'   => esc_html__( 'Tag', 'xts-theme' ),
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
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-timeline-item-first .xts-timeline-title',
				'key'               => 'first_title',
				'text_size_default' => 'l',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'second_title_style_tab',
			[
				'label' => esc_html__( 'Second item', 'xts-theme' ),
			]
		);

		$this->add_control(
			'second_title_tag',
			[
				'label'   => esc_html__( 'Tag', 'xts-theme' ),
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
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-timeline-item-second .xts-timeline-title',
				'key'               => 'second_title',
				'text_size_default' => 'l',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Description settings
		 */
		$this->start_controls_section(
			'description_section',
			[
				'label' => esc_html__( 'Description', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'description_style_tabs' );

		$this->start_controls_tab(
			'first_description_style_tab',
			[
				'label' => esc_html__( 'First item', 'xts-theme' ),
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector' => '{{WRAPPER}} .xts-timeline-item-first .xts-timeline-desc',
				'key'      => 'first_description',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'second_description_style_tab',
			[
				'label' => esc_html__( 'Second item', 'xts-theme' ),
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector' => '{{WRAPPER}} .xts-timeline-item-second .xts-timeline-desc',
				'key'      => 'second_description',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Line settings
		 */
		$this->start_controls_section(
			'line_style_section',
			[
				'label' => esc_html__( 'Line', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'line_style',
			[
				'label'     => esc_html__( 'Style', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => esc_html__( 'Solid', 'xts-theme' ),
					'dashed' => esc_html__( 'Dashed', 'xts-theme' ),
					'dotted' => esc_html__( 'Dotted', 'xts-theme' ),
				],
				'default'   => 'solid',
				'selectors' => [
					'{{WRAPPER}} .xts-timeline-line:after' => 'border-left-style: {{VALUE}};',
					'{{WRAPPER}} .xts-timeline-item:after' => 'border-top-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'line_background_color',
			[
				'label'     => esc_html__( 'Background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-timeline-line:after, {{WRAPPER}} .xts-timeline-item-first:after, {{WRAPPER}} .xts-timeline-item-second:after' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'line_width',
			[
				'label'     => esc_html__( 'Width', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-timeline-line:after' => 'border-left-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .xts-timeline-item-first:after, {{WRAPPER}} .xts-timeline-item-second:after' => 'border-top-width: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Dot settings
		 */
		$this->start_controls_section(
			'dot_style_section',
			[
				'label' => esc_html__( 'Dot', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'dot_background_color',
			[
				'label'     => esc_html__( 'Background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-timeline-dot' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dot_size',
			[
				'label'     => esc_html__( 'Size', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-timeline-dot' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'dot_border',
				'label'          => esc_html__( 'Border', 'xts-theme' ),
				'fields_options' => [
					'border' => [
						'default' => '',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#bbb',
					],
				],
				'selector'       => '{{WRAPPER}} .xts-timeline-dot',
			]
		);

		/**
		 * Border radius settings
		 */
		$this->add_control(
			'dot_border_radius_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'dot_border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .xts-timeline-dot' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Breakpoint settings
		 */
		$this->start_controls_section(
			'breakpoint_style_section',
			[
				'label' => esc_html__( 'Breakpoint', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'breakpoint_tabs' );

		$this->start_controls_tab(
			'breakpoint_text_tab',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'              => '{{WRAPPER}} .xts-timeline-breakpoint-title',
				'key'                   => 'breakpoint',
				'text_size_default'     => 's',
				'color_presets_default' => 'white',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'breakpoint_style_tab',
			[
				'label' => esc_html__( 'Style', 'xts-theme' ),
			]
		);

		/**
		 * Background color settings
		 */
		xts_get_background_map(
			$this,
			[
				'normal_selector'      => '{{WRAPPER}} .xts-timeline-breakpoint-title',
				'key'                  => 'breakpoint',
				'switcher_default'     => 'yes',
				'normal_default_color' => '#333',
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$this,
			[
				'key'             => 'breakpoint',
				'normal_selector' => '{{WRAPPER}} .xts-timeline-breakpoint-title',
			]
		);

		/**
		 * Padding settings
		 */
		$this->add_control(
			'breakpoint_padding_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'breakpoint_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .xts-timeline-breakpoint-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		/**
		 * Border radius settings
		 */
		$this->add_control(
			'breakpoint_border_radius_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'breakpoint_border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .xts-timeline-breakpoint-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get item content map.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param object $element Element object.
	 * @param string $key     Unique key.
	 */
	public function get_item_content_map( $element, $key ) {
		$element->add_control(
			$key . '_content_type',
			[
				'label'       => esc_html__( 'Content type', 'xts-theme' ),
				'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'text'       => esc_html__( 'Text', 'xts-theme' ),
					'html_block' => esc_html__( 'HTML Block', 'xts-theme' ),
				],
				'default'     => 'text',
			]
		);

		$element->add_control(
			$key . '_content_type_hr',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$element->add_control(
			$key . '_html_block_id',
			[
				'label'       => esc_html__( 'HTML Block', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'description' => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
				'options'     => xts_get_html_blocks_array( 'elementor' ),
				'default'     => '0',
				'classes'     => 'xts-html-block-links',
				'condition'   => [
					$key . '_content_type' => [ 'html_block' ],
				],
			]
		);

		$element->add_control(
			$key . '_subtitle',
			[
				'label'     => esc_html__( 'Subtitle', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Subtitle text example',
				'condition' => [
					$key . '_content_type' => [ 'text' ],
				],
			]
		);

		$element->add_control(
			$key . '_title',
			[
				'label'     => esc_html__( 'Title', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Title example. Click to edit.',
				'condition' => [
					$key . '_content_type' => [ 'text' ],
				],
			]
		);

		$element->add_control(
			$key . '_description',
			[
				'label'     => esc_html__( 'Description', 'xts-theme' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => 'Inceptos diam proin justo in nibh enim quam.',
				'condition' => [
					$key . '_content_type' => [ 'text' ],
				],
			]
		);

		$element->add_control(
			$key . '_image',
			[
				'label'     => esc_html__( 'Choose image', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => xts_get_elementor_placeholder_image_src(),
				],
				'condition' => [
					$key . '_content_type' => [ 'text' ],
				],
			]
		);

		$element->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => $key . '_image',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => [
					$key . '_content_type' => [ 'text' ],
				],
			]
		);
	}

	/**
	 * Get item style map.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param object $element     Element object.
	 * @param array  $custom_args Custom args.
	 */
	public function get_item_general_style_map( $element, $custom_args = [] ) {
		$default_args = [
			'key'             => '',
			'normal_selector' => false,
		];

		$args = wp_parse_args( $custom_args, $default_args );

		$element->add_responsive_control(
			$args['key'] . '_item_text_align',
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
				'default' => 'first' === $args['key'] ? 'right' : 'left',
			]
		);

		$element->add_control(
			$args['key'] . '_color_scheme',
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
				'default' => 'inherit',
			]
		);

		/**
		 * Background color settings
		 */
		xts_get_background_map(
			$element,
			[
				'key'             => $args['key'] . '_timeline',
				'normal_selector' => $args['normal_selector'],
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$element,
			[
				'key'             => $args['key'] . '_timeline',
				'normal_selector' => $args['normal_selector'],
				'divider'         => 'no',
			]
		);

		/**
		 * Padding settings
		 */
		$element->add_control(
			$args['key'] . '_padding_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$element->add_responsive_control(
			$args['key'] . '_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$args['normal_selector'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		/**
		 * Border radius settings
		 */
		$element->add_control(
			$args['key'] . '_border_radius_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$element->add_responsive_control(
			$args['key'] . '_border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$args['normal_selector'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		xts_timeline_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Timeline() );
