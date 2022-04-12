<?php
/**
 * Price plan switcher map
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
class Price_Plan_Switcher extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_price_plan_switcher';
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
		return esc_html__( 'Price plan switcher', 'xts-theme' );
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
		return 'xf-el-price-plan-switcher';
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
		 * General settings
		 */
		$this->start_controls_section(
			'general_content_section',
			[
				'label' => esc_html__( 'General', 'xts-theme' ),
			]
		);

		$this->start_controls_tabs( 'price_plan_pricing_tabs' );

		$this->start_controls_tab(
			'pricing_price_1_tab',
			[
				'label' => esc_html__( 'Price 1', 'xts-theme' ),
			]
		);

		$this->add_control(
			'price_1',
			[
				'label'   => esc_html__( 'Price 1 text', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Month',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pricing_price_2_tab',
			[
				'label' => esc_html__( 'Price 2', 'xts-theme' ),
			]
		);

		$this->add_control(
			'price_2',
			[
				'label'   => esc_html__( 'Price 2 text', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Year',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pricing_price_3_tab',
			[
				'label' => esc_html__( 'Price 3', 'xts-theme' ),
			]
		);

		$this->add_control(
			'price_3',
			[
				'label'   => esc_html__( 'Price 3 text', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Lifetime',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
			'text_align',
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
				'default' => 'center',
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'   => esc_html__( 'Default', 'xts-theme' ),
					'underline' => esc_html__( 'Underline', 'xts-theme' ),
				],
				'default' => 'default',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_custom_typography',
				'label'    => esc_html__( 'Custom typography', 'xts-theme' ),
				'selector' => '{{WRAPPER}} .xts-nav-link',
			]
		);

		/**
		 * Color
		 */
		xts_get_color_map(
			$this,
			[
				'key'              => 'pp_switcher',
				'normal_selectors' => [
					'{{WRAPPER}} .xts-nav-link' => 'color: {{VALUE}}',
				],
				'hover_selectors'  => [
					'{{WRAPPER}} .xts-nav-link:hover' => 'color: {{VALUE}}',
				],
				'active_selectors' => [
					'{{WRAPPER}} .xts-nav-pp-switcher li.xts-active .xts-nav-link' => 'color: {{VALUE}}',
				],
			]
		);

		/**
		 * Background color
		 */
		xts_get_background_color_map(
			$this,
			[
				'key'              => 'pp_switcher',
				'normal_selectors' => [
					'{{WRAPPER}} .xts-nav-link' => 'background-color: {{VALUE}}',
				],
				'hover_selectors'  => [
					'{{WRAPPER}} .xts-nav-link:hover' => 'background-color: {{VALUE}}',
				],
				'active_selectors' => [
					'{{WRAPPER}} .xts-nav-pp-switcher li.xts-active .xts-nav-link' => 'background-color: {{VALUE}}',
				],
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$this,
			[
				'key'             => 'pp_switcher',
				'normal_selector' => '{{WRAPPER}} .xts-nav-link',
				'hover_selector'  => '{{WRAPPER}} .xts-nav-link:hover',
				'active_selector' => '{{WRAPPER}} .xts-nav-pp-switcher li.xts-active .xts-nav-link',
			]
		);

		/**
		 * Border settings
		 */
		xts_get_border_map(
			$this,
			[
				'key'             => 'pp_switcher',
				'normal_selector' => '{{WRAPPER}} .xts-nav-link',
				'hover_selector'  => '{{WRAPPER}} .xts-nav-link:hover',
				'active_selector' => '{{WRAPPER}} .xts-nav-pp-switcher li.xts-active .xts-nav-link',
				'divider'         => 'no',
			]
		);

		/**
		 * Spacing settings
		 */
		$this->add_control(
			'spacing_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'text_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-nav-pp-switcher li:not(:last-child)' => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		/**
		 * Padding settings
		 */
		$this->add_control(
			'padding_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'pp_switcher_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '5',
					'bottom'   => '5',
					'left'     => '10',
					'right'    => '10',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .xts-nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .xts-nav-pp-switcher:not([class*="xts-with-"])' => 'margin-right: -{{RIGHT}}{{UNIT}}; margin-left: -{{LEFT}}{{UNIT}};',
				],
			]
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
		xts_price_plan_switcher_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Price_Plan_Switcher() );
