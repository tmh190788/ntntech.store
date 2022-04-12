<?php
/**
 * Title map
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
class Title extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_title';
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
		return esc_html__( 'Title', 'xts-theme' );
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
		return 'xf-el-title';
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
			'subtitle',
			[
				'label'   => esc_html__( 'Subtitle', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Section subtitle text example',
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Section title example. Click to edit.',
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'xts-theme' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => 'Short introduction text after the title. Click here to edit.',
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
			'design',
			[
				'label'       => esc_html__( 'Design', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => xts_get_available_options( 'title_element_design_elementor' ),
				'default'     => 'default',
			]
		);

		$this->add_control(
			'image',
			[
				'label'     => esc_html__( 'Choose image', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'design' => [ 'image' ],
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
					'design' => [ 'image' ],
				],
			]
		);

		$this->add_control(
			'image_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'style'     => 'thick',
				'condition' => [
					'design' => [ 'image' ],
				],
			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label'   => esc_html__( 'Color scheme', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'inherit' => [
						'title' => esc_html__( 'Inherit', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/inherit.svg',
					],
					'dark'    => [
						'title' => esc_html__( 'Dark', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/dark.svg',
					],
					'light'   => [
						'title' => esc_html__( 'Light', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/light.svg',
					],
				],
				'default' => 'inherit',
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label'          => esc_html__( 'Text width', 'xts-theme' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units'     => [ 'px', '%' ],
				'range'          => [
					'%'  => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors'      => [
						'{{WRAPPER}} .xts-section-subtitle-text, {{WRAPPER}} .xts-section-title-text, {{WRAPPER}} .xts-section-desc-text' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Subtitle settings
		 */
		$this->start_controls_section(
			'subtitle_section',
			[
				'label' => esc_html__( 'Subtitle', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-section-subtitle',
				'key'               => 'subtitle',
				'text_size_default' => 's',
			]
		);

		$this->end_controls_section();

		/**
		 * Title settings
		 */
		$this->start_controls_section(
			'title_section',
			[
				'label' => esc_html__( 'Title', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tag',
			[
				'label'   => esc_html__( 'Tag', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => esc_html__( 'h1', 'xts-theme' ),
					'h2'   => esc_html__( 'h2', 'xts-theme' ),
					'h3'   => esc_html__( 'h3', 'xts-theme' ),
					'h4'   => esc_html__( 'h4', 'xts-theme' ),
					'h5'   => esc_html__( 'h5', 'xts-theme' ),
					'h6'   => esc_html__( 'h6', 'xts-theme' ),
					'p'    => esc_html__( 'p', 'xts-theme' ),
					'div'  => esc_html__( 'div', 'xts-theme' ),
					'span' => esc_html__( 'span', 'xts-theme' ),
				],
				'default' => 'h4',
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-section-title',
				'key'               => 'title',
				'text_size_default' => 'l',
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

		xts_get_typography_map(
			$this,
			[
				'selector' => '{{WRAPPER}} .xts-section-desc',
				'key'      => 'description',
			]
		);

		$this->end_controls_section();

		/**
		 * Extra settings
		 */
		$this->start_controls_section(
			'extra_section',
			[
				'label' => esc_html__( 'Extra', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		/**
		 * Animations
		 */
		xts_get_animation_map(
			$this,
			[
				'type'      => 'items',
				'key'       => '_items',
				'condition' => [
					'animation_in_view' => [ 'yes' ],
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
		xts_title_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Title() );
