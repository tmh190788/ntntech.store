<?php
/**
 * Working hours map
 *
 * @package xts
 */

namespace XTS\Elementor;

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
class Working_Hours extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_working_hours';
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
		return esc_html__( 'Working hours', 'xts-theme' );
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
		return 'xf-el-working-hours';
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
		$this->start_controls_section(
			'general_content_section',
			[
				'label' => esc_html__( 'General', 'xts-theme' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'xts-theme' ),
				'type'  => Controls_Manager::ICONS,
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Monday',
			]
		);

		$repeater->add_control(
			'time',
			[
				'label'   => esc_html__( 'Time', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '8:00 – 19:30',
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'xts-theme' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '',
			]
		);

		$this->add_control(
			'items',
			[
				'type'        => Controls_Manager::REPEATER,
				'title_field' => '{{{ title }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title' => 'Monday',
						'time'  => '8:00 – 19:30',
					],
					[
						'title' => 'Tuesday',
						'time'  => '8:00 – 19:30',
					],
					[
						'title' => 'Wednesday',
						'time'  => '8:00 – 19:30',
					],
					[
						'title' => 'Thursday',
						'time'  => '8:00 – 19:30',
					],
					[
						'title' => 'Friday',
						'time'  => '8:00 – 19:30',
					],
					[
						'title' => 'Saturday / Sunday',
						'time'  => 'Closed',
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
			'style',
			[
				'label'   => esc_html__( 'Style', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'  => esc_html__( 'Default', 'xts-theme' ),
					'bordered' => esc_html__( 'Bordered', 'xts-theme' ),
				],
				'default' => 'default',
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
					'{{WRAPPER}} .xts-work-hours .xts-work-item' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'style' => [ 'bordered', 'vertical-border' ],
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => esc_html__( 'Border color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-work-hours .xts-work-item' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'style' => [ 'bordered' ],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Icon settings
		 */
		$this->start_controls_section(
			'icon_style_section',
			[
				'label' => esc_html__( 'Icon', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'              => '{{WRAPPER}} .xts-work-icon',
				'key'                   => 'icon',
				'typography'            => 'no',
				'color_presets_default' => 'primary',
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
			[
				'selector'          => '{{WRAPPER}} .xts-work-title',
				'key'               => 'title',
				'text_size_default' => 's',
			]
		);

		$this->end_controls_section();

		/**
		 * Time settings
		 */
		$this->start_controls_section(
			'time_style_section',
			[
				'label' => esc_html__( 'Time', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'              => '{{WRAPPER}} .xts-work-time',
				'key'                   => 'time',
				'text_size_default'     => 's',
				'color_presets_default' => 'primary',
			]
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
			[
				'selector' => '{{WRAPPER}} .xts-work-desc',
				'key'      => 'description',
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
		xts_working_hours_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Working_Hours() );
