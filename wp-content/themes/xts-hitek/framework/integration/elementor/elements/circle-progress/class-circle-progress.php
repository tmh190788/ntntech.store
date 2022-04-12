<?php
/**
 * Circle progress map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Circle_Progress extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_circle_progress';
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
		return esc_html__( 'Circle progress', 'xts-theme' );
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
		return 'xf-el-circle-progress';
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

		$this->add_control(
			'circle_size',
			[
				'label'       => esc_html__( 'Circle size', 'xts-theme' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px' ],
				'default'     => [
					'unit' => 'px',
					'size' => 185,
				],
				'range'       => [
					'px' => [
						'min' => 100,
						'max' => 600,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .xts-circle-progress' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'stroke_width',
			[
				'label'       => esc_html__( 'Stroke width', 'xts-theme' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px' ],
				'default'     => [
					'unit' => 'px',
					'size' => 7,
				],
				'range'       => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .xts-circle-meter, {{WRAPPER}} .xts-circle-meter-value' => 'stroke-width: {{SIZE}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'duration',
			[
				'label'   => esc_html__( 'Animation duration', 'xts-theme' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1000,
				'min'     => 100,
				'step'    => 100,
			]
		);

		$this->end_controls_section();

		/**
		 * Value settings
		 */
		$this->start_controls_section(
			'values_content_section',
			[
				'label' => esc_html__( 'Value', 'xts-theme' ),
			]
		);

		$this->add_control(
			'value_position',
			[
				'label'   => esc_html__( 'Position', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inside',
				'options' => [
					'inside'  => esc_html__( 'Inside', 'xts-theme' ),
					'outside' => esc_html__( 'Outside', 'xts-theme' ),
				],
			]
		);

		$this->add_control(
			'value_type',
			[
				'label'   => esc_html__( 'Progress type', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'percent',
				'options' => [
					'percent'  => esc_html__( 'Percent', 'xts-theme' ),
					'absolute' => esc_html__( 'Absolute', 'xts-theme' ),
				],
			]
		);

		$this->add_control(
			'suffix',
			[
				'label'   => esc_html__( 'Number suffix', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '%',
			]
		);

		$this->add_control(
			'percent_value',
			[
				'label'      => esc_html__( 'Current percent', 'xts-theme' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default'    => [
					'unit' => '%',
					'size' => 50,
				],
				'range'      => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition'  => [
					'value_type' => 'percent',
				],
			]
		);

		$this->add_control(
			'absolute_value_current',
			[
				'label'     => esc_html__( 'Current value', 'xts-theme' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 50,
				'condition' => [
					'value_type' => 'absolute',
				],
			]
		);

		$this->add_control(
			'absolute_value_max',
			[
				'label'     => esc_html__( 'Max value', 'xts-theme' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 200,
				'condition' => [
					'value_type' => 'absolute',
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
			'text_align',
			[
				'label'        => esc_html__( 'Text alignment', 'xts-theme' ),
				'type'         => 'xts_buttons',
				'options'      => [
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
				'prefix_class' => 'xts-textalign-',
				'render_type'  => 'template',
				'default'      => 'left',
			]
		);

		$this->add_control(
			'circle_background_color',
			[
				'label'     => esc_html__( 'Circle background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-circle-meter' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'circle_color',
			[
				'label'     => esc_html__( 'Circle color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-circle-meter' => 'stroke: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'circle_value_color',
			[
				'label'     => esc_html__( 'Circle value color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-circle-meter-value' => 'stroke: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'line_endings',
			[
				'label'     => esc_html__( 'Progress line endings', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'butt',
				'options'   => [
					'butt'  => esc_html__( 'Flat', 'xts-theme' ),
					'round' => esc_html__( 'Rounded', 'xts-theme' ),
				],
				'selectors' => [
					'{{WRAPPER}} .xts-circle-meter-value' => 'stroke-linecap: {{VALUE}}',
				],
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$this,
			[
				'key'             => 'circle_progress',
				'normal_selector' => '{{WRAPPER}} .xts-circle-bar',
			]
		);

		$this->end_controls_section();

		/**
		 * Value settings
		 */
		$this->start_controls_section(
			'value_style_section',
			[
				'label' => esc_html__( 'Value', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-circle-value',
				'key'               => 'value',
				'text_size_default' => 'l',
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
		xts_circle_progress_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Circle_Progress() );
