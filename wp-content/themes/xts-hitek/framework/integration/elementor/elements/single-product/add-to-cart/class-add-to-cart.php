<?php
/**
 * Add_To_Cart map
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
class Add_To_Cart extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_single_product_add_to_cart';
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
		return esc_html__( 'Add to cart', 'xts-theme' );
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
		return 'xf-woo-el-add-to-cart';
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
		return [ 'xts-product-elements' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * @since  1.0.0
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
			'label_position',
			[
				'label'        => esc_html__( 'Swatches label position', 'xts-theme' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'without' => esc_html__( 'Without', 'xts-theme' ),
					'left'    => esc_html__( 'Left', 'xts-theme' ),
					'top'     => esc_html__( 'Top', 'xts-theme' ),
				],
				'prefix_class' => 'xts-label-position-',
				'render_type'  => 'template',
				'default'      => 'left',
			]
		);

		$this->add_control(
			'stock_status',
			[
				'label'        => esc_html__( 'Stock status', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		/**
		 * Main price settings
		 */
		$this->start_controls_section(
			'main_price_style_section',
			[
				'label' => esc_html__( 'Main price (variable product)', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'main_price_custom_typography',
				'label'    => esc_html__( 'Custom typography', 'xts-theme' ),
				'selector' => '{{WRAPPER}} .price, {{WRAPPER}} span.amount, {{WRAPPER}} del',
			]
		);

		$this->add_control(
			'main_price_custom_color',
			[
				'label'     => esc_html__( 'Custom color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price, {{WRAPPER}} span.amount, {{WRAPPER}} del' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Sale price settings
		 */
		$this->start_controls_section(
			'sale_price_style_section',
			[
				'label' => esc_html__( 'Sale price (variable product)', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_price_custom_typography',
				'label'    => esc_html__( 'Custom typography', 'xts-theme' ),
				'selector' => '{{WRAPPER}} .price del, {{WRAPPER}} del span.amount',
			]
		);

		$this->add_control(
			'sale_price_custom_color',
			[
				'label'     => esc_html__( 'Custom color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price del, {{WRAPPER}} del span.amount' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Tax text settings
		 */
		$this->start_controls_section(
			'tax_text_style_section',
			[
				'label' => esc_html__( 'Tax text (variable product)', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tax_text_custom_typography',
				'label'    => esc_html__( 'Custom typography', 'xts-theme' ),
				'selector' => '{{WRAPPER}} .woocommerce-price-suffix',
			]
		);

		$this->add_control(
			'tax_text_custom_color',
			[
				'label'     => esc_html__( 'Custom color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-price-suffix' => 'color: {{VALUE}}',
				],
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
		global $post;

		if ( ! is_singular( 'product' ) ) {
			$post = xts_get_preview_product(); // phpcs:ignore
			setup_postdata( $post );
		}

		xts_single_product_add_to_cart_template( $this->get_settings_for_display() );

		if ( ! is_singular( 'product' ) ) {
			wp_reset_postdata();
		}
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Add_To_Cart() );
