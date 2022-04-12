<?php
/**
 * Countdown timer map
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
class Countdown_Timer extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_countdown_timer';
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
		return esc_html__( 'Countdown timer', 'xts-theme' );
	}

	/**
	 * Get script depend
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array Scripts array.
	 */
	public function get_script_depends() {
		if ( xts_elementor_is_edit_mode() || xts_elementor_is_preview_mode() ) {
			return [ 'xts-countdown' ];
		} else {
			return [];
		}
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
		return 'xf-el-countdown-timer';
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
			'date',
			[
				'label'   => esc_html__( 'Date', 'xts-theme' ),
				'type'    => Controls_Manager::DATE_TIME,
				'default' => date( 'Y-m-d', strtotime( ' +2 months' ) ),
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
			'align',
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
				'default' => 'left',
			]
		);

		$this->add_control(
			'size',
			[
				'label'   => esc_html__( 'Size', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					's' => esc_html__( 'Small', 'xts-theme' ),
					'm' => esc_html__( 'Medium', 'xts-theme' ),
					'l' => esc_html__( 'Large', 'xts-theme' ),
				],
				'default' => 'm',
			]
		);

		$this->add_control(
			'color',
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
				'default' => 'default',
			]
		);

		$this->add_control(
			'custom_color',
			[
				'label'     => esc_html__( 'Custom color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-countdown-item' => 'color: {{VALUE}}',
				],
				'condition' => [
					'color' => [ 'custom' ],
				],
			]
		);

		$this->add_control(
			'bg_color',
			[
				'label'   => esc_html__( 'Background color', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'   => esc_html__( 'Transparent', 'xts-theme' ),
					'primary'   => esc_html__( 'Primary', 'xts-theme' ),
					'secondary' => esc_html__( 'Secondary', 'xts-theme' ),
					'custom'    => esc_html__( 'Custom', 'xts-theme' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'custom_bg_color',
			[
				'label'     => esc_html__( 'Background custom color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-countdown-item' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'bg_color' => [ 'custom' ],
				],
			]
		);

		$this->add_responsive_control(
			'spacing',
			[
				'label'     => esc_html__( 'Spacing', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-countdown-item'  => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .xts-countdown-timer' => 'margin-right: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/2);',
				],
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$this,
			[
				'key'             => 'countdown',
				'normal_selector' => '{{WRAPPER}} .xts-countdown-item',
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
	 * @since  1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		xts_countdown_timer_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Countdown_Timer() );

