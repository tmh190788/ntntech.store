<?php
/**
 * Hotspots map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Hotspots extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_hotspots';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Hotspots', 'xts-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'xf-el-hotspots';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		/**
		 * Content tab
		 */

		/**
		 * Image settings
		 */
		$this->start_controls_section(
			'image_content_section',
			[
				'label' => esc_html__( 'Image', 'xts-theme' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Choose image', 'xts-theme' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => xts_get_elementor_placeholder_image_src( 'banner' ),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'large',
				'separator' => 'none',
			]
		);

		$this->end_controls_section();

		/**
		 * Hotspots settings
		 */
		$this->start_controls_section(
			'hotspots_content_section',
			[
				'label' => esc_html__( 'Hotspots', 'xts-theme' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'hotspot_tabs' );

		$repeater->start_controls_tab(
			'content_tab',
			[
				'label' => esc_html__( 'Content', 'xts-theme' ),
			]
		);

		$repeater->add_control(
			'content_type',
			[
				'label'   => esc_html__( 'Type', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'text'    => esc_html__( 'Text', 'xts-theme' ),
					'product' => esc_html__( 'Product', 'xts-theme' ),
				],
				'default' => 'text',
			]
		);

		/**
		 * Text settings
		 */
		$repeater->add_control(
			'title',
			[
				'label'     => esc_html__( 'Title', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Title, click to edit.',
				'condition' => [
					'content_type' => [ 'text' ],
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'     => esc_html__( 'Choose image', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => xts_get_elementor_placeholder_image_src( 'banner' ),
				],
				'condition' => [
					'content_type' => [ 'text' ],
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'large',
				'separator' => 'none',
				'condition' => [
					'content_type' => [ 'text' ],
				],
			]
		);

		$repeater->add_control(
			'link_text',
			[
				'label'     => esc_html__( 'Link text', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Button',
				'condition' => [
					'content_type' => [ 'text' ],
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'     => esc_html__( 'Link', 'xts-theme' ),
				'type'      => Controls_Manager::URL,
				'default'   => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
				'condition' => [
					'content_type' => [ 'text' ],
				],
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'     => esc_html__( 'Description', 'xts-theme' ),
				'type'      => Controls_Manager::TEXTAREA,
				'condition' => [
					'content_type' => [ 'text' ],
				],
			]
		);

		/**
		 * Product settings
		 */
		$repeater->add_control(
			'product',
			[
				'label'       => esc_html__( 'Select product', 'xts-theme' ),
				'type'        => 'xts_autocomplete',
				'search'      => 'xts_get_posts_by_query',
				'render'      => 'xts_get_posts_title_by_id',
				'post_type'   => 'product',
				'multiple'    => false,
				'label_block' => true,
				'condition'   => [
					'content_type' => [ 'product' ],
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'position_tab',
			[
				'label' => esc_html__( 'Position', 'xts-theme' ),
			]
		);

		$repeater->add_responsive_control(
			'hotspot_position_horizontal',
			[
				'label'     => esc_html__( 'Horizontal position (%)', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 50,
				],
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.xts-spot' => 'left: {{SIZE}}%;',
				],
			]
		);

		$repeater->add_responsive_control(
			'hotspot_position_vertical',
			[
				'label'     => esc_html__( 'Vertical position (%)', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 50,
				],
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.xts-spot' => 'top: {{SIZE}}%;',
				],
			]
		);

		$repeater->add_control(
			'content_position',
			[
				'label'   => esc_html__( 'Content position', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'left'   => esc_html__( 'Left', 'xts-theme' ),
					'right'  => esc_html__( 'Right', 'xts-theme' ),
					'top'    => esc_html__( 'Top', 'xts-theme' ),
					'bottom' => esc_html__( 'Bottom', 'xts-theme' ),
				],
				'default' => 'left',
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		/**
		 * Repeater settings
		 */
		$this->add_control(
			'hotspots_repeater',
			[
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
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

		$this->add_control(
			'trigger',
			[
				'label'   => esc_html__( 'Trigger', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => xts_get_available_options( 'hotspot_element_trigger_elementor' ),
				'default' => 'hover',
			]
		);

		$this->add_control(
			'text_align',
			[
				'label'   => esc_html__( 'Text alignment', 'xts-theme' ),
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

		$this->end_controls_section();

		/**
		 * Title settings
		 */
		$this->start_controls_section(
			'title_style_section',
			[
				'label' => esc_html__( 'Title', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			array(
				'selector'          => '{{WRAPPER}} .xts-spot-title',
				'key'               => 'title',
				'text_size_default' => 'm',
			)
		);

		$this->end_controls_section();

		/**
		 * Description settings
		 */
		$this->start_controls_section(
			'description_style_section',
			[
				'label' => esc_html__( 'Description', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			array(
				'selector' => '{{WRAPPER}} .xts-title',
				'key'      => 'description',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		xts_hotspots_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Hotspots() );
