<?php
/**
 * Mega menu map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Typography;
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
class Mega_Menu extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_mega_menu';
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
		return esc_html__( 'Mega menu', 'xts-theme' );
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
		return 'xf-el-mega-menu';
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
		 * General settings
		 */
		$this->start_controls_section(
			'general_content_section',
			[
				'label' => esc_html__( 'General', 'xts-theme' ),
			]
		);

		$this->add_control(
			'menu',
			[
				'label'       => esc_html__( 'Select menu', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => xts_get_menus_array( 'elementor' ),
				'default'     => '0',
			]
		);

		$this->add_control(
			'title',
			[
				'label'     => esc_html__( 'Title', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => [
					'design' => [ 'vertical' ],
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
			'design',
			[
				'label'   => esc_html__( 'Design', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => xts_get_available_options( 'menu_orientation_elementor' ),
				'default' => 'vertical',
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => xts_get_available_options( 'menu_style_elementor' ),
				'default' => 'default',
			]
		);

		do_action( 'xts_mega_menu_general_style_after_menu_style', $this );

		$this->add_control(
			'color_scheme',
			[
				'label'   => esc_html__( 'Color scheme', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'dark'  => [
						'title' => esc_html__( 'Dark', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/dark.svg',
					],
					'light' => [
						'title' => esc_html__( 'Light', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/light.svg',
					],
				],
				'default' => 'dark',
			]
		);

		$this->add_control(
			'bg_color',
			[
				'label'     => esc_html__( 'Background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-nav-mega' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'design' => [ 'vertical' ],
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => esc_html__( 'Border color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-nav-mega' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'design' => [ 'vertical' ],
				],
			]
		);

		$this->add_control(
			'border_width',
			[
				'label'     => esc_html__( 'Border width', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-nav-mega'   => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .xts-mega-title' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'design' => [ 'vertical' ],
				],
			]
		);

		$this->add_control(
			'align',
			[
				'label'     => esc_html__( 'Alignment', 'xts-theme' ),
				'type'      => 'xts_buttons',
				'options'   => [
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
				'condition' => [
					'design' => [ 'horizontal' ],
				],
				'default'   => 'left',
			]
		);

		$this->add_control(
			'items_gap',
			[
				'label'     => esc_html__( 'Items gap', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					's' => esc_html__( 'Small', 'xts-theme' ),
					'm' => esc_html__( 'Medium', 'xts-theme' ),
					'l' => esc_html__( 'Large', 'xts-theme' ),
				],
				'condition' => [
					'design' => [ 'horizontal' ],
				],
				'default'   => 's',
			]
		);

		$this->add_control(
			'typography_heading',
			[
				'label'     => esc_html__( 'Items typography', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		xts_get_color_map(
			$this,
			array(
				'key'              => 'items',
				'normal_selectors' => array(
					'{{WRAPPER}} .xts-nav-mega > li > a' => 'color: {{VALUE}}',
				),
				'hover_selectors'  => array(
					'{{WRAPPER}} .xts-nav-mega > li:hover > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'items_typography',
				'label'    => esc_html__( 'Custom typography', 'xts-theme' ),
				'selector' => '{{WRAPPER}} .xts-nav-mega > li > a',
			]
		);

		$this->end_controls_section();

		/**
		 * Title settings
		 */
		$this->start_controls_section(
			'title_style_section',
			[
				'label'     => esc_html__( 'Title', 'xts-theme' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'design' => [ 'vertical' ],
				],
			]
		);

		$this->add_control(
			'title_bg_color',
			[
				'label'   => esc_html__( 'Background color', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'primary'   => esc_html__( 'Primary', 'xts-theme' ),
					'secondary' => esc_html__( 'Secondary', 'xts-theme' ),
					'custom'    => esc_html__( 'Custom', 'xts-theme' ),
				],
				'default' => 'primary',
			]
		);

		$this->add_control(
			'title_custom_bg_color',
			[
				'label'     => esc_html__( 'Background custom color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-mega-title' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'bg_color' => [ 'custom' ],
				],
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'              => '{{WRAPPER}} .xts-mega-title',
				'key'                   => 'title',
				'color_presets_default' => 'white',
			]
		);

		$this->end_controls_section();
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
		xts_mega_menu_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Mega_Menu() );

