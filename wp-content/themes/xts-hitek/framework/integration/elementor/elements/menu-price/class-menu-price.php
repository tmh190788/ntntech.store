<?php
/**
 * Menu price map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an Menu_Price content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Menu_Price extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_menu_price';
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
		return esc_html__( 'Menu price', 'xts-theme' );
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
		return 'xf-el-menu-price';
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

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'menu_price_tabs' );

		$repeater->start_controls_tab(
			'text_tab',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Seared chicken',
			]
		);

		$repeater->add_control(
			'price',
			[
				'label'   => esc_html__( 'Price', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '$38.00',
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'xts-theme' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.',
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'xts-theme' ),
				'type'  => Controls_Manager::URL,
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'image_tab',
			[
				'label' => esc_html__( 'Image', 'xts-theme' ),
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Choose image', 'xts-theme' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => xts_get_elementor_placeholder_image_src(),
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'thumbnail',
				'separator' => 'none',
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'menu_price_items',
			[
				'type'        => Controls_Manager::REPEATER,
				'title_field' => '{{{ name }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title'       => 'Crispy pork',
						'price'       => '$34.00',
						'description' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.',
					],
					[
						'title'       => 'Pan seared red snapper',
						'price'       => '$25.00',
						'description' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.',
					],
					[
						'title'       => 'Roast halibut',
						'price'       => '$30.00',
						'description' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.',
					],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style tab
		 */

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
				'selector'          => '{{WRAPPER}} .xts-menu-price-title',
				'key'               => 'title',
				'text_size_default' => 'm',
			]
		);

		$this->end_controls_section();

		/**
		 * Price settings
		 */
		$this->start_controls_section(
			'price_style_section',
			[
				'label' => esc_html__( 'Price', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-menu-price-amount',
				'key'               => 'price',
				'text_size_default' => 's',
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
				'selector' => '{{WRAPPER}} .xts-menu-price-desc',
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
	 * @since  1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		xts_menu_price_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Menu_Price() );
