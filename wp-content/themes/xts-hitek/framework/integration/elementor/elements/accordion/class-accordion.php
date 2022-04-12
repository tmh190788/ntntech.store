<?php
/**
 * Accordion map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Accordion extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_accordion';
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
		return esc_html__( 'Accordion', 'xts-theme' );
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
		return 'xf-el-accordion';
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

		$repeater = new Repeater();

		$repeater->add_control(
			'item_title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Accordion title. Click here to edit',
			]
		);

		$repeater->add_control(
			'content_type',
			[
				'label'       => esc_html__( 'Content type', 'xts-theme' ),
				'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'text'       => esc_html__( 'Text', 'xts-theme' ),
					'html_block' => esc_html__( 'HTML Block', 'xts-theme' ),
				],
				'default'     => 'text',
			]
		);

		$repeater->add_control(
			'item_desc',
			[
				'label'     => esc_html__( 'Description', 'xts-theme' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => '<i>Click here to change this text</i>. Ac non ac hac ullamcorper rhoncus velit maecenas convallis torquent elit accumsan eu est pulvinar pretium congue a vestibulum suspendisse scelerisque condimentum parturient quam.Aliquet faucibus condimentum amet nam a nascetur suspendisse habitant a mollis senectus suscipit a vestibulum primis molestie parturient aptent nisi aenean.A scelerisque quam consectetur condimentum risus lobortis cum dignissim mi fusce primis rhoncus a rhoncus bibendum parturient condimentum odio a justo a et mollis pulvinar venenatis metus sodales elementum.Parturient ullamcorper natoque mi sagittis a nibh nisi a suspendisse a.',
				'condition' => [
					'content_type' => [ 'text' ],
				],
			]
		);

		$repeater->add_control(
			'html_block_id',
			[
				'label'       => esc_html__( 'HTML Block', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => xts_get_html_blocks_array( 'elementor' ),
				'description' => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
				'default'     => '0',
				'classes'     => 'xts-html-block-links',
				'condition'   => [
					'content_type' => [ 'html_block' ],
				],
			]
		);

		$this->add_control(
			'accordion_items',
			[
				'type'        => Controls_Manager::REPEATER,
				'title_field' => '{{{ item_title }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'item_title' => 'Accordion title #1. Click here to edit',
						'item_desc'  => '<i>Click here to change this text</i>. Ac non ac hac ullamcorper rhoncus velit maecenas convallis torquent elit accumsan eu est pulvinar pretium congue a vestibulum suspendisse scelerisque condimentum parturient quam.Aliquet faucibus condimentum amet nam a nascetur suspendisse habitant a mollis senectus suscipit a vestibulum primis molestie parturient aptent nisi aenean.A scelerisque quam consectetur condimentum risus lobortis cum dignissim mi fusce primis rhoncus a rhoncus bibendum parturient condimentum odio a justo a et mollis pulvinar venenatis metus sodales elementum.Parturient ullamcorper natoque mi sagittis a nibh nisi a suspendisse a.',
					],
					[
						'item_title' => 'Accordion title #2. Click here to edit',
						'item_desc'  => '<i>Click here to change this text</i>. Ac non ac hac ullamcorper rhoncus velit maecenas convallis torquent elit accumsan eu est pulvinar pretium congue a vestibulum suspendisse scelerisque condimentum parturient quam.Aliquet faucibus condimentum amet nam a nascetur suspendisse habitant a mollis senectus suscipit a vestibulum primis molestie parturient aptent nisi aenean.A scelerisque quam consectetur condimentum risus lobortis cum dignissim mi fusce primis rhoncus a rhoncus bibendum parturient condimentum odio a justo a et mollis pulvinar venenatis metus sodales elementum.Parturient ullamcorper natoque mi sagittis a nibh nisi a suspendisse a.',
					],
					[
						'item_title' => 'Accordion title #3. Click here to edit',
						'item_desc'  => '<i>Click here to change this text</i>. Ac non ac hac ullamcorper rhoncus velit maecenas convallis torquent elit accumsan eu est pulvinar pretium congue a vestibulum suspendisse scelerisque condimentum parturient quam.Aliquet faucibus condimentum amet nam a nascetur suspendisse habitant a mollis senectus suscipit a vestibulum primis molestie parturient aptent nisi aenean.A scelerisque quam consectetur condimentum risus lobortis cum dignissim mi fusce primis rhoncus a rhoncus bibendum parturient condimentum odio a justo a et mollis pulvinar venenatis metus sodales elementum.Parturient ullamcorper natoque mi sagittis a nibh nisi a suspendisse a.',
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
			'state',
			[
				'label'   => esc_html__( 'Items state', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'first'      => esc_html__( 'First opened', 'xts-theme' ),
					'all_closed' => esc_html__( 'All closed', 'xts-theme' ),
				],
				'default' => 'first',
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'         => esc_html__( 'Default', 'xts-theme' ),
					'bordered'        => esc_html__( 'Bordered', 'xts-theme' ),
					'vertical-border' => esc_html__( 'Vertical border', 'xts-theme' ),
					'shadow'          => esc_html__( 'Shadow', 'xts-theme' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'border_width',
			[
				'label'     => esc_html__( 'Border width', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-accordion.xts-style-bordered, {{WRAPPER}} .xts-accordion-item, {{WRAPPER}} .xts-accordion.xts-style-vertical-border .xts-accordion-title:before' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .xts-accordion.xts-style-vertical-border .xts-accordion-title:before' => is_rtl() ? 'margin-right: -{{SIZE}}{{UNIT}};' : 'margin-left: -{{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .xts-accordion, {{WRAPPER}} .xts-accordion-item' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'style' => [ 'bordered' ],
				],
			]
		);

		$this->add_control(
			'border_title_color',
			[
				'label'     => esc_html__( 'Title border color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-accordion-title:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'style' => [ 'vertical-border' ],
				],
			]
		);

		$this->add_control(
			'border_desc_color',
			[
				'label'     => esc_html__( 'Description border color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-accordion-item' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'style' => [ 'vertical-border' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'shadow',
				'selector'  => '{{WRAPPER}} .xts-accordion-item',
				'condition' => [
					'style' => [ 'shadow' ],
				],
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

		$this->add_control(
			'title_align',
			[
				'label'   => esc_html__( 'Alignment', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'left'  => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/left.svg',
						'style' => 'col-2',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/right.svg',
					],
				],
				'default' => 'left',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_custom_typography',
				'label'    => esc_html__( 'Custom typography', 'xts-theme' ),
				'selector' => '{{WRAPPER}} .xts-accordion-title-text',
			]
		);

		xts_get_color_map(
			$this,
			[
				'key'              => 'title',
				'normal_selectors' => [
					'{{WRAPPER}} .xts-accordion-title-text' => 'color: {{VALUE}}',
				],
				'hover_selectors'  => [
					'{{WRAPPER}} .xts-accordion-title:hover .xts-accordion-title-text' => 'color: {{VALUE}}',
				],
				'active_selectors' => [
					'{{WRAPPER}} .xts-accordion-title.xts-active .xts-accordion-title-text' => 'color: {{VALUE}}',
				],
			]
		);

		xts_get_background_color_map(
			$this,
			[
				'key'              => 'title',
				'normal_selectors' => [
					'{{WRAPPER}} .xts-accordion-title' => 'background-color: {{VALUE}}',
				],
				'hover_selectors'  => [
					'{{WRAPPER}} .xts-accordion-title:hover' => 'background-color: {{VALUE}}',
				],
				'active_selectors' => [
					'{{WRAPPER}} .xts-accordion-title.xts-active' => 'background-color: {{VALUE}}',
				],
				'divider'          => 'no',
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'bottom'   => '',
					'left'     => '',
					'right'    => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .xts-accordion-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Description settings
		 */
		$this->start_controls_section(
			'description_section',
			[
				'label' => esc_html__( 'Description', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'description_align',
			[
				'label'   => esc_html__( 'Alignment', 'xts-theme' ),
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

		xts_get_typography_map(
			$this,
			[
				'selector'     => '{{WRAPPER}} .xts-accordion-content',
				'key'          => 'description',
				'size_presets' => 'no',
			]
		);

		xts_get_background_color_map(
			$this,
			[
				'key'              => 'description',
				'normal_selectors' => [
					'{{WRAPPER}} .xts-accordion-content' => 'background-color: {{VALUE}}',
				],
				'divider'          => 'no',
			]
		);

		$this->add_responsive_control(
			'description_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'bottom'   => '',
					'left'     => '',
					'right'    => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .xts-accordion-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Icon settings
		 */
		$this->start_controls_section(
			'icon_section',
			[
				'label' => esc_html__( 'Icon', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_style',
			[
				'label'       => esc_html__( 'Style', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => [
					'arrow' => esc_html__( 'Arrow', 'xts-theme' ),
					'plus'  => esc_html__( 'Plus', 'xts-theme' ),
				],
				'default'     => 'arrow',
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'   => esc_html__( 'Position', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'left'  => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/accordion/icon-position/left.svg',
						'style' => 'col-2',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/accordion/icon-position/right.svg',
					],
				],
				'default' => 'left',
			]
		);

		/**
		 * Icon color
		 */
		xts_get_color_map(
			$this,
			[
				'key'              => 'icon',
				'divider'          => 'no',
				'normal_selectors' => [
					'{{WRAPPER}} .xts-accordion-icon' => 'color: {{VALUE}}',
				],
				'hover_selectors'  => [
					'{{WRAPPER}} .xts-accordion-title:hover .xts-accordion-icon' => 'color: {{VALUE}}',
				],
				'active_selectors' => [
					'{{WRAPPER}} .xts-accordion-title.xts-active .xts-accordion-icon' => 'color: {{VALUE}}',
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
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		xts_accordion_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Accordion() );
