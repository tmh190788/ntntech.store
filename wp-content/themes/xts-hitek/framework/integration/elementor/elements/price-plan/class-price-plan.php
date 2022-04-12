<?php
/**
 * Price plan map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Price_Plan extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_price_plan';
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
		return esc_html__( 'Price plan', 'xts-theme' );
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
		return 'xf-el-price-plan';
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
		 * Header settings
		 */
		$this->start_controls_section(
			'header_content_section',
			[
				'label' => esc_html__( 'Header', 'xts-theme' ),
			]
		);

		$this->start_controls_tabs( 'price_plan_header_tabs' );

		$this->start_controls_tab(
			'text_tab',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Base plan',
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Most popular plan',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_tab',
			[
				'label' => esc_html__( 'Icon', 'xts-theme' ),
			]
		);

		$this->add_control(
			'icon_type',
			[
				'label'       => esc_html__( 'Icon type', 'xts-theme' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => [
					'icon'  => [
						'title' => esc_html__( 'Icon', 'xts-theme' ),
						'icon'  => 'fa fa-info',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'xts-theme' ),
						'icon'  => 'fa fa-image',
					],
				],
				'toggle'      => false,
				'default'     => 'icon',
			]
		);

		$this->add_control(
			'icon',
			[
				'label'     => esc_html__( 'Icon', 'xts-theme' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'icon_type' => [ 'icon' ],
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label'     => esc_html__( 'Choose image', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => xts_get_elementor_placeholder_image_src(),
				],
				'condition' => [
					'icon_type' => [ 'image' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => [
					'icon_type' => [ 'image' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'label_tab',
			[
				'label' => esc_html__( 'Label', 'xts-theme' ),
			]
		);

		$this->add_control(
			'label_text',
			[
				'label'   => esc_html__( 'Label text', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Popular',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Pricing settings
		 */
		$this->start_controls_section(
			'pricing_section',
			[
				'label' => esc_html__( 'Pricing', 'xts-theme' ),
			]
		);

		$this->add_control(
			'currency_symbol',
			[
				'label'   => esc_html__( 'Currency symbol', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '$',
			]
		);

		$this->start_controls_tabs( 'price_plan_pricing_tabs' );

		$this->start_controls_tab(
			'pricing_price_1_tab',
			[
				'label' => esc_html__( 'Price 1', 'xts-theme' ),
			]
		);

		$this->add_control(
			'price_1',
			[
				'label'   => esc_html__( 'Price 1', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '39',
			]
		);

		$this->add_control(
			'fraction_1',
			[
				'label'   => esc_html__( 'Fraction 1', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '.99',
			]
		);

		$this->add_control(
			'title_1',
			[
				'label'       => esc_html__( 'Subtitle 1', 'xts-theme' ),
				'description' => esc_html__( 'You can specify the configuration for three different states and then use our "Price plan switcher" element to allow customers to switch between these three options.', 'xts-theme' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'per month',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pricing_price_2_tab',
			[
				'label' => esc_html__( 'Price 2', 'xts-theme' ),
			]
		);

		$this->add_control(
			'price_2',
			[
				'label'   => esc_html__( 'Price 2', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '139',
			]
		);

		$this->add_control(
			'fraction_2',
			[
				'label'   => esc_html__( 'Fraction 2', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '.99',
			]
		);

		$this->add_control(
			'title_2',
			[
				'label'       => esc_html__( 'Subtitle 2', 'xts-theme' ),
				'description' => esc_html__( 'You can specify the configuration for three different states and then use our "Price plan switcher" element to allow customers to switch between these three options.', 'xts-theme' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'per year',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pricing_price_3_tab',
			[
				'label' => esc_html__( 'Price 3', 'xts-theme' ),
			]
		);

		$this->add_control(
			'price_3',
			[
				'label'   => esc_html__( 'Price 3', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '239',
			]
		);

		$this->add_control(
			'fraction_3',
			[
				'label'   => esc_html__( 'Fraction 3', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '.99',
			]
		);

		$this->add_control(
			'title_3',
			[
				'label'       => esc_html__( 'Subtitle 3', 'xts-theme' ),
				'description' => esc_html__( 'You can specify the configuration for three different states and then use our "Price plan switcher" element to allow customers to switch between these three options.', 'xts-theme' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'lifetime',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Features settings
		 */
		$this->start_controls_section(
			'features_section',
			[
				'label' => esc_html__( 'Features', 'xts-theme' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'price_plan_tabs' );

		$repeater->start_controls_tab(
			'text_tab',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
			]
		);

		$repeater->add_control(
			'item_text',
			[
				'label'   => esc_html__( 'Text', 'xts-theme' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Plan advantage',
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'icon_tab',
			[
				'label' => esc_html__( 'Icon', 'xts-theme' ),
			]
		);

		$repeater->add_control(
			'item_icon',
			[
				'label' => esc_html__( 'Icon', 'xts-theme' ),
				'type'  => Controls_Manager::ICONS,
			]
		);

		$repeater->add_control(
			'item_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} i' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'features_list',
			[
				'type'        => Controls_Manager::REPEATER,
				'title_field' => '{{{ item_text }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'item_text' => 'Plan advantage #1',
						'item_icon' => 'fa fa-check',
					],
					[
						'item_text' => 'Plan advantage #2',
						'item_icon' => 'fa fa-check',
					],
					[
						'item_text' => 'Plan advantage #3',
						'item_icon' => 'fa fa-check',
					],
				],
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
				'product' => true,
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
				'default' => 'center',
			]
		);

		$this->add_control(
			'featured_item',
			[
				'label'        => esc_html__( 'Featured item', 'xts-theme' ),
				'description'  => esc_html__( 'Mark this price plan as a featured item to highlight between other plans.', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		/**
		 * Color scheme
		 */
		xts_get_color_scheme_map(
			$this,
			[
				'key' => 'price_plan',
			]
		);

		/**
		 * Background color
		 */
		xts_get_background_map(
			$this,
			[
				'key'             => 'price_plan',
				'normal_selector' => '{{WRAPPER}} .xts-price-plan',
				'hover_selector'  => '{{WRAPPER}} .xts-plan-overlay',
			]
		);

		/**
		 * Border settings
		 */
		xts_get_border_map(
			$this,
			[
				'key'             => 'price_plan',
				'normal_selector' => '{{WRAPPER}} .xts-price-plan',
				'hover_selector'  => '{{WRAPPER}} .xts-price-plan:hover',
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$this,
			[
				'key'              => 'price_plan',
				'normal_selector'  => '{{WRAPPER}} .xts-price-plan',
				'hover_selector'   => '{{WRAPPER}} .xts-price-plan:hover',
				'switcher_default' => 'yes',
				'divider'          => 'no',
			]
		);

		/**
		 * Padding settings
		 */
		$this->add_control(
			'padding_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'member_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .xts-price-plan' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'icon_size',
			[
				'label'     => esc_html__( 'Icon size', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					's' => esc_html__( 'Small', 'xts-theme' ),
					'm' => esc_html__( 'Medium', 'xts-theme' ),
					'l' => esc_html__( 'Large', 'xts-theme' ),
				],
				'default'   => 'm',
				'condition' => [
					'icon_type' => [ 'icon' ],
				],
			]
		);

		xts_get_color_map(
			$this,
			[
				'key'              => 'icon',
				'normal_selectors' => [
					'{{WRAPPER}} .xts-plan-icon'      => 'color: {{VALUE}}',
					'{{WRAPPER}} .xts-price-plan svg' => 'fill: {{VALUE}}',
				],
				'hover_selectors'  => [
					'{{WRAPPER}} .xts-price-plan:hover .xts-plan-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .xts-price-plan:hover svg' => 'fill: {{VALUE}}',
				],
				'divider'          => 'no',
			]
		);

		$this->end_controls_section();

		/**
		 * Header settings
		 */
		$this->start_controls_section(
			'header_style_section',
			[
				'label' => esc_html__( 'Header', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'header_tabs' );

		$this->start_controls_tab(
			'title_style_tab',
			[
				'label' => esc_html__( 'Title', 'xts-theme' ),
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-plan-title',
				'key'               => 'title',
				'text_size_default' => 'm',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'description_style_tab',
			[
				'label' => esc_html__( 'Description', 'xts-theme' ),
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector' => '{{WRAPPER}} .xts-plan-desc',
				'key'      => 'description',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'label_style_tab',
			[
				'label' => esc_html__( 'Label', 'xts-theme' ),
			]
		);

		$this->add_control(
			'label_align',
			[
				'label'   => esc_html__( 'Label alignment', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'left'  => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/price-plan/label-align/left.svg',
						'style' => 'col-2',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/price-plan/label-align/right.svg',
					],
				],
				'default' => 'right',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'   => esc_html__( 'Label background color', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'primary'   => esc_html__( 'Primary Color', 'xts-theme' ),
					'secondary' => esc_html__( 'Secondary', 'xts-theme' ),
				],
				'default' => 'primary',
			]
		);

		$this->add_control(
			'custom_label_color_switcher',
			[
				'label'        => esc_html__( 'Custom label color', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'custom_label_bg_color',
			[
				'label'     => esc_html__( 'Label background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-plan-label' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'custom_label_color_switcher' => [ 'yes' ],
				],
			]
		);

		$this->add_control(
			'custom_label_color',
			[
				'label'     => esc_html__( 'Label text color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-plan-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'custom_label_color_switcher' => [ 'yes' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Pricing settings
		 */
		$this->start_controls_section(
			'pricing_style_section',
			[
				'label' => esc_html__( 'Pricing', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'pricing_tabs' );

		$this->start_controls_tab(
			'amount_tab',
			[
				'label' => esc_html__( 'Amount', 'xts-theme' ),
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'              => '{{WRAPPER}} .xts-plan-pricing',
				'key'                   => 'pricing',
				'text_size_default'     => 'l',
				'color_presets_default' => 'primary',
				'text_size_options'     => [
					's' => esc_html__( 'Small', 'xts-theme' ),
					'm' => esc_html__( 'Medium', 'xts-theme' ),
					'l' => esc_html__( 'Large', 'xts-theme' ),
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'subtitle_tab',
			[
				'label' => esc_html__( 'Subtitle', 'xts-theme' ),
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-plan-pricing-subtitle',
				'key'               => 'subtitle',
				'text_size_default' => 'm',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Features text settings
		 */
		$this->start_controls_section(
			'features_text_style_section',
			[
				'label' => esc_html__( 'Features text', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector' => '{{WRAPPER}} .xts-plan-feature',
				'key'      => 'features_text',
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

		xts_get_button_style_general_map(
			$this,
			[
				'align' => '',
			]
		);

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
		xts_price_plan_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Price_Plan() );
