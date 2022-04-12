<?php
/**
 * Animated text map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Animated_Text extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_animated_text';
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
		return esc_html__( 'Animated text', 'xts-theme' );
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
		return 'xf-el-animated-text';
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
			'before_text',
			[
				'label'   => esc_html__( 'Before text', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Shop for',
			]
		);

		$this->add_control(
			'animated_text',
			[
				'label'   => esc_html__( 'Words', 'xts-theme' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Clothing' . "\n" . 'Shoes' . "\n" . 'Accessories',
			]
		);

		$this->add_control(
			'after_text',
			[
				'label'   => esc_html__( 'After text', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'online',
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
				'default' => 'left',
			]
		);

		$this->add_control(
			'animation_effect',
			[
				'label'   => esc_html__( 'Animation effect', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'typing' => esc_html__( 'Typing', 'xts-theme' ),
					'word'   => esc_html__( 'Change word', 'xts-theme' ),
				],
				'default' => 'typing',
			]
		);

		$this->add_control(
			'animation_time',
			[
				'label'     => esc_html__( 'Animation duration time (ms)', 'xts-theme' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 600,
				'condition' => [
					'animation_effect' => [ 'word' ],
				],
			]
		);

		$this->add_control(
			'character_time',
			[
				'label'     => esc_html__( 'Character type time (ms)', 'xts-theme' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 150,
				'condition' => [
					'animation_effect' => [ 'typing' ],
				],
			]
		);

		$this->add_control(
			'interval_time',
			[
				'label'   => esc_html__( 'Interval time (ms)', 'xts-theme' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2500,
			]
		);

		$this->end_controls_section();

		/**
		 * Before & After text settings
		 */
		$this->start_controls_section(
			'before_after_style_section',
			[
				'label' => esc_html__( 'Before & After text', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-anim-text-before, {{WRAPPER}} .xts-anim-text-after',
				'key'               => 'before_after',
				'text_size_default' => 'l',
			]
		);

		$this->end_controls_section();

		/**
		 * Animated text settings
		 */
		$this->start_controls_section(
			'animated_style_section',
			[
				'label' => esc_html__( 'Animated text', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'              => '{{WRAPPER}} .xts-anim-text-list',
				'key'                   => 'animated',
				'text_size_default'     => 'l',
				'color_presets_default' => 'primary',
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
		xts_animated_text_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Animated_Text() );
