<?php
/**
 * Menu anchor map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Menu_Anchor extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_menu_anchor';
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
		return esc_html__( 'Navigation anchor', 'xts-theme' );
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
		return 'xf-el-menu-anchor';
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
		 * Content settings
		 */
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'xts-theme' ),
			]
		);

		$this->add_control(
			'offset',
			[
				'label'   => esc_html__( 'Offset (px)', 'xts-theme' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 150,
			]
		);

		$this->add_control(
			'anchor',
			[
				'label'       => esc_html__( 'The ID of Navigation anchor.', 'xts-theme' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Without #.', 'xts-theme' ),
				'default'     => 'anchor1',
				'label_block' => true,
			]
		);

		$this->add_control(
			'note',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Note: The ID link ONLY accepts these chars: A-Z, a-z, 0-9', 'xts-theme' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
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
		xts_menu_anchor_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Menu_Anchor() );
