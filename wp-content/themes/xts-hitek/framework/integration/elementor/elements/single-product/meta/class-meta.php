<?php
/**
 * Meta map
 *
 * @package xts
 */

namespace XTS\Elementor\Single_Product_Builder;

use Elementor\Group_Control_Typography;
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
class Meta extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_single_product_meta';
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
		return esc_html__( 'Meta', 'xts-theme' );
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
		return 'xf-woo-el-meta';
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
			'text_align',
			[
				'label'        => esc_html__( 'Text alignment', 'xts-theme' ),
				'type'         => 'xts_buttons',
				'options'      => [
					'left'    => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/left.svg',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/center.svg',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/right.svg',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/justify.svg',
					],
				],
				'prefix_class' => 'xts-textalign-',
				'render_type'  => 'template',
				'default'      => 'left',
			]
		);

		$this->add_control(
			'style',
			[
				'label'        => esc_html__( 'Style', 'xts-theme' ),
				'type'         => 'xts_buttons',
				'options'      => [
					'list'   => [
						'title' => esc_html__( 'List', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/single-product/meta/style/list.svg',
						'style' => 'col-2',
					],
					'inline' => [
						'title' => esc_html__( 'Inline', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/single-product/meta/style/inline.svg',
					],
				],
				'prefix_class' => 'xts-meta-style-',
				'render_type'  => 'template',
				'default'      => 'default',
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

		xts_single_product_meta_template( $this->get_settings_for_display() );

		if ( ! is_singular( 'product' ) ) {
			wp_reset_postdata();
		}
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Meta() );
