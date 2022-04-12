<?php
/**
 * Instagram map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Image_Size;
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
class Instagram extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_instagram';
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
		return esc_html__( 'Instagram', 'xts-theme' );
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
		return 'xf-el-instagram';
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
			'link_is_external',
			[
				'label'        => esc_html__( 'Open in new window', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'link_nofollow',
			[
				'label'        => esc_html__( 'Add nofollow', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->end_controls_section();

		/**
		 * Images settings
		 */
		$this->start_controls_section(
			'images_content_section',
			[
				'label' => esc_html__( 'Images', 'xts-theme' ),
			]
		);

		$this->add_control(
			'source',
			[
				'label'   => esc_html__( 'Source', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'api'           => esc_html__( 'API', 'xts-theme' ),
					'custom_images' => esc_html__( 'Custom images', 'xts-theme' ),
				],
				'default' => 'custom_images',
			]
		);

		$this->add_control(
			'custom_images_size',
			[
				'label'   => esc_html__( 'Image size', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'thumbnail',
				'options' => xts_get_all_image_sizes_names( 'elementor' ),
			]
		);

		$this->add_control(
			'custom_images_custom_dimension',
			[
				'label'       => esc_html__( 'Image dimension', 'xts-theme' ),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.', 'xts-theme' ),
				'condition'   => [
					'custom_images_size' => 'custom',
				],
			]
		);

		/**
		 * Custom images settings
		 */
		$this->add_control(
			'custom_images',
			[
				'label'     => esc_html__( 'Add Images', 'xts-theme' ),
				'type'      => Controls_Manager::GALLERY,
				'default'   => [],
				'condition' => [
					'source' => [ 'custom_images' ],
				],
			]
		);

		$this->add_control(
			'custom_images_link',
			[
				'label'     => esc_html__( 'Link to profile', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'source' => [ 'custom_images' ],
				],
			]
		);

		/**
		 * API settings
		 */
		$this->add_control(
			'api_images_per_page',
			[
				'label'      => esc_html__( 'Images per page', 'xts-theme' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 9,
				],
				'size_units' => '',
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 12,
						'step' => 1,
					],
				],
				'condition'  => [
					'source' => [ 'api' ],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Text settings
		 */
		$this->start_controls_section(
			'text_content_section',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'xts-theme' ),
				'description' => esc_html__( 'Add here few words about your instagram profile.', 'xts-theme' ),
				'type'        => Controls_Manager::WYSIWYG,
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
			'text_width',
			[
				'label'          => esc_html__( 'Text width', 'xts-theme' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'unit' => 'px',
					'size' => 300,
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'size_units'     => [ 'px', '%' ],
				'range'          => [
					'%'  => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 100,
						'max' => 2000,
					],
				],
				'selectors'      => [
					'{{WRAPPER}} .xts-insta-desc' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'show_meta',
			[
				'label'        => esc_html__( 'Show likes and comments', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		/**
		 * Border radius settings
		 */
		$this->add_control(
			'border_radius_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .xts-insta-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Layout settings
		 */
		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__( 'Layout', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'view',
			[
				'label'   => esc_html__( 'View', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'grid'     => [
						'title' => esc_html__( 'Grid', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/view/grid.svg',
						'style' => 'col-2',
					],
					'carousel' => [
						'title' => esc_html__( 'Carousel', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/view/carousel.svg',
					],
				],
				'default' => 'grid',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'      => esc_html__( 'Columns', 'xts-theme' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 3,
				],
				'size_units' => '',
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				],
				'condition'  => [
					'view' => [ 'grid' ],
				],
			]
		);

		$this->add_control(
			'spacing',
			[
				'label'     => esc_html__( 'Items gap', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => xts_get_available_options( 'items_gap_elementor' ),
				'condition' => [
					'view' => [ 'grid' ],
				],
				'default'   => xts_get_default_value( 'items_gap' ),
			]
		);

		$this->add_control(
			'different_images',
			[
				'label'        => esc_html__( 'Different images', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
				'condition'    => [
					'view' => [ 'grid' ],
				],
			]
		);

		$this->add_control(
			'different_images_position',
			[
				'label'     => esc_html__( 'Different images position', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '2,5,8,9',
				'condition' => [
					'different_images' => [ 'yes' ],
					'view'             => [ 'grid' ],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Extra settings
		 */
		$this->start_controls_section(
			'extra_style_section',
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

		/**
		 * Lazy loading
		 */
		xts_get_lazy_loading_map( $this );

		$this->end_controls_section();

		/**
		 * Carousel settings
		 */
		$this->start_controls_section(
			'carousel_section',
			[
				'label'     => esc_html__( 'Carousel settings', 'xts-theme' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'view' => [ 'carousel' ],
				],
			]
		);

		xts_get_carousel_map(
			$this,
			[
				'padding' => [
					'top'    => 0,
					'bottom' => 0,
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
		xts_instagram_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Instagram() );
