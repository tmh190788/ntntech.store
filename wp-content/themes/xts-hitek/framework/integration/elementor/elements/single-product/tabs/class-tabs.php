<?php
/**
 * Tabs map
 *
 * @package xts
 */

namespace XTS\Elementor\Single_Product_Builder;

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
class Tabs extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_single_product_tabs';
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
		return esc_html__( 'Single product tabs', 'xts-theme' );
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
		return 'xf-woo-el-product-tabs';
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
		return [ 'xts-product-elements' ];
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

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__( 'Layout', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'tabs'      => [
						'title' => esc_html__( 'Tabs', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/single-product/tabs/layout/tabs.svg',
						'style' => 'col-2',
					],
					'accordion' => [
						'title' => esc_html__( 'Accordion', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/single-product/tabs/layout/accordion.svg',
					],
				],
				'default' => 'tabs',
			]
		);

		$this->add_control(
			'disable_additional_info',
			[
				'label'        => esc_html__( 'Disable additional info', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'disable_reviews',
			[
				'label'        => esc_html__( 'Disable reviews', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'disable_description',
			[
				'label'        => esc_html__( 'Disable description', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
				'default'      => 'no',
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
		global $post;

		if ( ! is_singular( 'product' ) ) {
			$post = xts_get_preview_product(); // phpcs:ignore
			setup_postdata( $post );
		}

		xts_single_product_tabs_template( $this->get_settings_for_display() );

		if ( ! is_singular( 'product' ) ) {
			wp_reset_postdata();
		}
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Tabs() );
