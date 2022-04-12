<?php
/**
 * Shape map
 *
 * @package xts
 */

namespace XTS\Elementor;

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
class Shape extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_shape';
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
		return esc_html__( 'Shape', 'xts-theme' );
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
		return 'xf-el-shape';
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

		$this->add_responsive_control(
			'shape_width',
			[
				'label'     => esc_html__( 'Width', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 50,
						'max' => 1500,
					],
				],
				'default'   => [
					'size' => 150,
				],
				'selectors' => [
					'{{WRAPPER}} .xts-shape' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'shape_height',
			[
				'label'     => esc_html__( 'Height', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 50,
						'max' => 1500,
					],
				],
				'default'   => [
					'size' => 150,
				],
				'selectors' => [
					'{{WRAPPER}} .xts-shape' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'shape_rotate',
			[
				'label'     => esc_html__( 'Rotate', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 360,
					],
				],
				'default'   => [
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .xts-shape' => 'transform: rotate({{SIZE}}deg);',
				],
			]
		);

		/**
		 * Background settings
		 */
		xts_get_background_map(
			$this,
			[
				'key'                  => 'shape',
				'normal_selector'      => '{{WRAPPER}} .xts-shape',
				'switcher_default'     => 'yes',
				'normal_default_color' => '#c9c9c9',
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$this,
			[
				'key'             => 'shape',
				'normal_selector' => '{{WRAPPER}} .xts-shape',
			]
		);

		/**
		 * Border settings
		 */
		xts_get_border_map(
			$this,
			[
				'key'             => 'shape',
				'normal_selector' => '{{WRAPPER}} .xts-shape',
				'divider'         => 'no',
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
		xts_shape_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Shape() );
