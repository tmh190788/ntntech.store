<?php
/**
 * Popup map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Popup extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_popup';
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
		return esc_html__( 'Popup', 'xts-theme' );
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
		return 'xf-el-popup';
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
			'html_block_id',
			[
				'label'       => esc_html__( 'Content', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'description' => 'You can use any elements and build your popup with Elementor and HTML Blocks. Select an HTML Block from the list to be displayed in the popup.<br><a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
				'options'     => xts_get_html_blocks_array( 'elementor' ),
				'default'     => '0',
				'classes'     => 'xts-html-block-links',
			]
		);

		$this->end_controls_section();

		/**
		 * Button settings
		 */
		$this->start_controls_section(
			'button_content_section',
			[
				'label' => esc_html__( 'Button', 'xts-theme' ),
			]
		);

		xts_get_button_content_general_map(
			$this,
			[
				'link' => false,
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

		$this->add_responsive_control(
			'width',
			[
				'label'   => esc_html__( 'Width', 'xts-theme' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 800,
				],
				'range'   => [
					'px' => [
						'min'  => 100,
						'max'  => 2000,
						'step' => 5,
					],
				],
			]
		);

		$this->add_control(
			'bg_color',
			[
				'label' => esc_html__( 'Background color', 'xts-theme' ),
				'type'  => Controls_Manager::COLOR,
			]
		);

		$this->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose background image', 'xts-theme' ),
				'type'  => Controls_Manager::MEDIA,
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

		$this->add_control(
			'image_bg_position',
			[
				'label'   => esc_html__( 'Background position', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'left-center'   => esc_html__( 'Left', 'xts-theme' ),
					'right-center'  => esc_html__( 'Right', 'xts-theme' ),
					'center-center' => esc_html__( 'Center', 'xts-theme' ),
				],
				'default' => 'center-center',
			]
		);

		$this->end_controls_section();

		/**
		 * Button settings
		 */
		$this->start_controls_section(
			'button_style_section',
			[
				'label' => esc_html__( 'Button', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_button_style_general_map( $this );

		xts_get_button_style_icon_map( $this );

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
		xts_popup_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Popup() );

