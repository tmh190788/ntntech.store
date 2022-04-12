<?php
/**
 * Tabs map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;

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
		return 'xts_tabs';
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
		return esc_html__( 'Tabs', 'xts-theme' );
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
		return 'xf-el-tabs';
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

		$repeater->start_controls_tabs( 'content_tabs' );

		$repeater->start_controls_tab(
			'text_tab',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
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
			'item_title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Tab title example',
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

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'icon_tab',
			[
				'label' => esc_html__( 'Icon', 'xts-theme' ),
			]
		);

		$repeater->add_control(
			'icon_type',
			[
				'label'       => esc_html__( 'Type', 'xts-theme' ),
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

		$repeater->add_control(
			'icon',
			[
				'label'     => esc_html__( 'Icon', 'xts-theme' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'icon_type' => [ 'icon' ],
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'     => esc_html__( 'Choose image', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => [
					'icon_type' => [ 'image' ],
				],
			]
		);

		$repeater->add_group_control(
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

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'tabs_items',
			[
				'type'        => Controls_Manager::REPEATER,
				'title_field' => '{{{ item_title }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'item_title' => 'Tab title example #1',
						'item_desc'  => '<i>Click here to change this text</i>. Ac non ac hac ullamcorper rhoncus velit maecenas convallis torquent elit accumsan eu est pulvinar pretium congue a vestibulum suspendisse scelerisque condimentum parturient quam.Aliquet faucibus condimentum amet nam a nascetur suspendisse habitant a mollis senectus suscipit a vestibulum primis molestie parturient aptent nisi aenean.A scelerisque quam consectetur condimentum risus lobortis cum dignissim mi fusce primis rhoncus a rhoncus bibendum parturient condimentum odio a justo a et mollis pulvinar venenatis metus sodales elementum.Parturient ullamcorper natoque mi sagittis a nibh nisi a suspendisse a.',
					],
					[
						'item_title' => 'Tab title example #2',
						'item_desc'  => '<i>Click here to change this text</i>. Ac non ac hac ullamcorper rhoncus velit maecenas convallis torquent elit accumsan eu est pulvinar pretium congue a vestibulum suspendisse scelerisque condimentum parturient quam.Aliquet faucibus condimentum amet nam a nascetur suspendisse habitant a mollis senectus suscipit a vestibulum primis molestie parturient aptent nisi aenean.A scelerisque quam consectetur condimentum risus lobortis cum dignissim mi fusce primis rhoncus a rhoncus bibendum parturient condimentum odio a justo a et mollis pulvinar venenatis metus sodales elementum.Parturient ullamcorper natoque mi sagittis a nibh nisi a suspendisse a.',
					],
					[
						'item_title' => 'Tab title example #3',
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
		 * Tabs title settings
		 */
		$this->start_controls_section(
			'title_style_section',
			[
				'label' => esc_html__( 'Tabs title', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_align',
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

		$this->add_control(
			'icon_position',
			[
				'label'   => esc_html__( 'Icon position', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'left'  => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/tabs/icon-position/left.svg',
					],
					'top'   => [
						'title' => esc_html__( 'Top', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/tabs/icon-position/top.svg',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/tabs/icon-position/right.svg',
					],
				],
				'default' => 'left',
			]
		);

		$this->add_control(
			'title_style',
			[
				'label'   => esc_html__( 'Style', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'   => esc_html__( 'Default', 'xts-theme' ),
					'underline' => esc_html__( 'Underline', 'xts-theme' ),
				],
				'default' => 'default',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_custom_typography',
				'label'    => esc_html__( 'Custom typography', 'xts-theme' ),
				'selector' => '{{WRAPPER}} .xts-nav-text',
			]
		);

		xts_get_color_map(
			$this,
			[
				'key'              => 'title',
				'normal_selectors' => [
					'{{WRAPPER}} .xts-nav-link' => 'color: {{VALUE}}',
				],
				'hover_selectors'  => [
					'{{WRAPPER}} .xts-nav-link:hover' => 'color: {{VALUE}}',
				],
				'active_selectors' => [
					'{{WRAPPER}} .xts-nav-tabs li.xts-active .xts-nav-link' => 'color: {{VALUE}}',
				],
			]
		);

		xts_get_background_color_map(
			$this,
			[
				'key'              => 'title',
				'normal_selectors' => [
					'{{WRAPPER}} .xts-nav-link' => 'background-color: {{VALUE}}',
				],
				'hover_selectors'  => [
					'{{WRAPPER}} .xts-nav-link:hover' => 'background-color: {{VALUE}}',
				],
				'active_selectors' => [
					'{{WRAPPER}} .xts-nav-tabs li.xts-active .xts-nav-link' => 'background-color: {{VALUE}}',
				],
				'divider'          => 'no',
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$this,
			[
				'key'             => 'title',
				'normal_selector' => '{{WRAPPER}} .xts-nav-link',
				'hover_selector'  => '{{WRAPPER}} .xts-nav-link:hover',
				'active_selector' => '{{WRAPPER}} .xts-nav-tabs li.xts-active .xts-nav-link',
			]
		);

		/**
		 * Border settings
		 */
		xts_get_border_map(
			$this,
			[
				'key'             => 'title',
				'normal_selector' => '{{WRAPPER}} .xts-nav-link',
				'hover_selector'  => '{{WRAPPER}} .xts-nav-link:hover',
				'active_selector' => '{{WRAPPER}} .xts-nav-tabs li.xts-active .xts-nav-link',
				'divider'         => 'no',
			]
		);

		/**
		 * Spacing settings
		 */
		$this->add_control(
			'title_spacing_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => esc_html__( 'Horizontal spacing', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-nav-tabs li:not(:last-child)' => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_vertical_spacing',
			[
				'label'     => esc_html__( 'Vertical spacing', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .xts-nav-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '5',
					'bottom'   => '5',
					'left'     => '10',
					'right'    => '10',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .xts-nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .xts-nav-tabs:not([class*="xts-with-"])' => 'margin-left: -{{LEFT}}{{UNIT}}; margin-right: -{{RIGHT}}{{UNIT}};',
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

		$this->add_control(
			'description_animations',
			[
				'label'   => esc_html__( 'Animations', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'fade-in'        => esc_html__( 'Fade in', 'xts-theme' ),
					'fade-in-bottom' => esc_html__( 'Fade in bottom', 'xts-theme' ),
					'fade-in-right'  => esc_html__( 'Fade in right', 'xts-theme' ),
				],
				'default' => 'fade-in',
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'     => '{{WRAPPER}} .xts-tab-desc',
				'key'          => 'description',
				'size_presets' => 'no',
			]
		);

		xts_get_background_color_map(
			$this,
			[
				'key'              => 'description',
				'normal_selectors' => [
					'{{WRAPPER}} .xts-tab-content-wrapper' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .xts-tab-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		xts_tabs_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Tabs() );
