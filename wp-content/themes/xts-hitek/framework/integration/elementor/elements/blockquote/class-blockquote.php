<?php
/**
 * Blockquote map
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
class Blockquote extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_blockquote';
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
		return esc_html__( 'Blockquote', 'xts-theme' );
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
		return 'xf-el-blockquote';
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
			'blockquote',
			[
				'label'   => esc_html__( 'Quote', 'xts-theme' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'I am enough of an artist to draw freely upon my imagination. Imagination is more important than knowledge. Knowledge is limited. Imagination encircles the world.',
			]
		);

		$this->add_control(
			'author',
			[
				'label'   => esc_html__( 'Author', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Albert Einstein',
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
				'label'       => esc_html__( 'Style', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => [
					'default' => esc_html__( 'Default', 'xts-theme' ),
					'large'   => esc_html__( 'Large', 'xts-theme' ),
				],
				'default'     => 'default',
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
			'border_color',
			[
				'label'     => esc_html__( 'Border color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} blockquote' => 'border-color: {{VALUE}};',
				],
				'default'   => '',
			]
		);

		$this->end_controls_section();

		/**
		 * Blockquote settings
		 */
		$this->start_controls_section(
			'blockquote_section',
			[
				'label' => esc_html__( 'Quote', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector' => '{{WRAPPER}} blockquote',
				'key'      => 'blockquote',
			]
		);

		$this->end_controls_section();

		/**
		 * Author settings
		 */
		$this->start_controls_section(
			'author_section',
			[
				'label' => esc_html__( 'Author', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector' => '{{WRAPPER}} cite',
				'key'      => 'author',
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
		xts_blockquote_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Blockquote() );
