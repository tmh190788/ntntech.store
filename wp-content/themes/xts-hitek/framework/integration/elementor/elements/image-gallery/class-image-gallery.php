<?php
/**
 * Image gallery carousel map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
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
class Image_Gallery extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_image_gallery';
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
		return esc_html__( 'Image gallery', 'xts-theme' );
	}

	/**
	 * Get script depend
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Scripts array.
	 */
	public function get_script_depends() {
		if ( xts_elementor_is_edit_mode() || xts_elementor_is_preview_mode() ) {
			return [ 'xts-photoswipe', 'xts-isotope' ];
		} else {
			return [];
		}
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
		return 'xf-el-image-gallery';
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
			'gallery',
			[
				'label'   => esc_html__( 'Images', 'xts-theme' ),
				'type'    => Controls_Manager::GALLERY,
				'default' => [],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'gallery',
				'default'   => 'large',
				'separator' => 'none',
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
			'general_section',
			[
				'label' => esc_html__( 'General', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hover_effect',
			[
				'label'   => esc_html__( 'Hover effect', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => xts_get_available_options( 'image_gallery_element_hover_effect_elementor' ),
				'default' => 'none',
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
			'image_horizontal_position',
			[
				'label'   => esc_html__( 'Image horizontal position', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/image-gallery/horizontal-position/left.svg',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/image-gallery/horizontal-position/center.svg',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/image-gallery/horizontal-position/right.svg',
					],
				],
				'default' => 'center',
			]
		);

		$this->add_control(
			'image_vertical_position',
			[
				'label'   => esc_html__( 'Image vertical position', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'start'  => [
						'title' => esc_html__( 'Top', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/image-gallery/vertical-position/top.svg',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/image-gallery/vertical-position/middle.svg',
					],
					'end'    => [
						'title' => esc_html__( 'Bottom', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/image-gallery/vertical-position/bottom.svg',
					],
				],
				'default' => 'start',
			]
		);

		$this->add_control(
			'view_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
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
			'masonry',
			[
				'label'        => esc_html__( 'Masonry grid', 'xts-theme' ),
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

		$this->add_control(
			'gallery_thumbs',
			[
				'label'        => esc_html__( 'Gallery thumbnails', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
				'condition'    => [
					'view' => [ 'carousel' ],
				],
			]
		);

		$this->add_control(
			'gallery_thumbs_image_size',
			[
				'label'     => esc_html__( 'Image size', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'woocommerce_thumbnail',
				'options'   => xts_get_all_image_sizes_names( 'elementor' ),
				'condition' => [
					'gallery_thumbs' => [ 'yes' ],
				],
			]
		);

		$this->add_control(
			'gallery_thumbs_image_size_custom',
			[
				'label'       => esc_html__( 'Image dimension', 'xts-theme' ),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.', 'xts-theme' ),
				'condition'   => [
					'gallery_thumbs'            => [ 'yes' ],
					'gallery_thumbs_image_size' => [ 'custom' ],
				],
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$this,
			[
				'key'             => 'image',
				'normal_selector' => '{{WRAPPER}} .xts-image',
				'divider'         => 'no',
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
					'{{WRAPPER}} .xts-image, {{WRAPPER}} .xts-gallery-thumbs img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

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
				'arrows_horizontal_position' => true,
				'center_mode_opacity'        => true,
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

		$this->add_control(
			'click_action',
			[
				'label'   => esc_html__( 'On click action', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'lightbox'    => esc_html__( 'Lightbox', 'xts-theme' ),
					'custom_link' => esc_html__( 'Custom link', 'xts-theme' ),
					'nothing'     => esc_html__( 'Nothing', 'xts-theme' ),
				],
				'toggle'  => false,
				'default' => 'lightbox',
			]
		);

		$this->add_control(
			'custom_links',
			[
				'label'       => esc_html__( 'Custom links', 'xts-theme' ),
				'description' => esc_html__( 'Enter links for each slide (Note: divide links with linebreaks (Enter)).', 'xts-theme' ),
				'type'        => Controls_Manager::TEXTAREA,
				'condition'   => [
					'click_action' => [ 'custom_link' ],
				],
			]
		);

		$this->add_control(
			'caption',
			[
				'label'   => esc_html__( 'Caption', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'lightbox'    => esc_html__( 'Lightbox', 'xts-theme' ),
					'on-image'    => esc_html__( 'On image', 'xts-theme' ),
					'under-image' => esc_html__( 'Under image', 'xts-theme' ),
					'disabled'    => esc_html__( 'Disabled', 'xts-theme' ),
				],
				'default' => 'lightbox',
			]
		);

		$this->add_control(
			'global_lightbox',
			[
				'label'        => esc_html__( 'Global lightbox', 'xts-theme' ),
				'description'  => esc_html__( 'Enable this option if you want to make all your galleries lightboxes on the page work as one gallery.', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
				'condition'    => [
					'click_action' => [ 'lightbox' ],
				],
			]
		);

		$this->add_control(
			'lightbox_gallery',
			[
				'label'        => esc_html__( 'Show thumbnails in lightbox', 'xts-theme' ),
				'description'  => esc_html__( 'Display thumbnails navigation when you open the images lightbox.', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
				'condition'    => [
					'click_action' => [ 'lightbox' ],
				],
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
		xts_image_gallery_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Image_Gallery() );
