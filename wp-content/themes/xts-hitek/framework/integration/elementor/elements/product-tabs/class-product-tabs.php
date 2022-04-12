<?php
/**
 * Products tabs map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Product_Tabs extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_product_tabs';
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
		return esc_html__( 'Products tabs', 'xts-theme' );
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
		return 'xf-el-products-tabs';
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
	 * Get attribute taxonomies
	 *
	 * @since 1.0.0
	 */
	public function get_product_attributes_array() {
		$attributes = [];

		if ( xts_is_woocommerce_installed() ) {
			foreach ( wc_get_attribute_taxonomies() as $attribute ) {
				$attributes[] = 'pa_' . $attribute->attribute_name;
			}
		}

		return $attributes;
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
		 * Heading settings
		 */
		$this->start_controls_section(
			'heading_content_section',
			[
				'label' => esc_html__( 'Heading', 'xts-theme' ),
			]
		);

		$this->start_controls_tabs( 'tabs_title_tabs' );

		$this->start_controls_tab(
			'text_tab',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
			]
		);

		$this->add_control(
			'tabs_subtitle',
			[
				'label'   => esc_html__( 'Subtitle', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Tabs subtitle',
			]
		);

		$this->add_control(
			'tabs_title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Tabs title',
			]
		);

		$this->add_control(
			'tabs_description',
			[
				'label'   => esc_html__( 'Description', 'xts-theme' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '',
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
			'heading_icon_type',
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

		$this->add_control(
			'heading_icon',
			[
				'label'     => esc_html__( 'Icon', 'xts-theme' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'heading_icon_type' => [ 'icon' ],
				],
			]
		);

		$this->add_control(
			'heading_icon_image',
			[
				'label'     => esc_html__( 'Choose image', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => [
					'heading_icon_type' => [ 'image' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'heading_icon_image',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => [
					'heading_icon_type' => [ 'image' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Tabs settings
		 */
		$this->start_controls_section(
			'tabs_content_section',
			[
				'label' => esc_html__( 'Tabs', 'xts-theme' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'content_tabs' );

		$repeater->start_controls_tab(
			'query_tab',
			[
				'label' => esc_html__( 'Query', 'xts-theme' ),
			]
		);

		$repeater->add_control(
			'product_source_heading',
			[
				'label' => esc_html__( 'Product source', 'xts-theme' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$repeater->add_control(
			'product_source',
			[
				'label'   => esc_html__( 'Product source', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'all_products'     => esc_html__( 'All products', 'xts-theme' ),
					'featured'         => esc_html__( 'Featured', 'xts-theme' ),
					'sale'             => esc_html__( 'Sale', 'xts-theme' ),
					'new'              => esc_html__( 'With "New" label', 'xts-theme' ),
					'bestsellers'      => esc_html__( 'Bestsellers', 'xts-theme' ),
					'top_rated'        => esc_html__( 'Top rated', 'xts-theme' ),
					'related'          => esc_html__( 'Related (Single product)', 'xts-theme' ),
					'upsells'          => esc_html__( 'Upsells (Single product)', 'xts-theme' ),
					'list_of_products' => esc_html__( 'List of products', 'xts-theme' ),
				],
				'default' => 'all_products',
			]
		);

		$repeater->add_control(
			'taxonomies',
			[
				'label'       => esc_html__( 'Taxonomies', 'xts-theme' ),
				'type'        => 'xts_autocomplete',
				'search'      => 'xts_get_taxonomies_by_query',
				'render'      => 'xts_get_taxonomies_title_by_id',
				'taxonomy'    => array_merge( [ 'product_cat', 'product_tag' ], $this->get_product_attributes_array() ),
				'multiple'    => true,
				'label_block' => true,
				'condition'   => [
					'product_source!' => [
						'list_of_products',
						'related',
						'upsells',
					],
				],
			]
		);

		$repeater->add_control(
			'include',
			[
				'label'       => esc_html__( 'Include only', 'xts-theme' ),
				'type'        => 'xts_autocomplete',
				'search'      => 'xts_get_posts_by_query',
				'render'      => 'xts_get_posts_title_by_id',
				'post_type'   => 'product',
				'multiple'    => true,
				'label_block' => true,
				'condition'   => [
					'product_source' => [ 'list_of_products' ],
				],
			]
		);

		xts_get_posts_query_map(
			$repeater,
			array(
				'exclude_search' => 'xts_get_posts_by_query',
				'exclude_render' => 'xts_get_posts_title_by_id',
				'post_type'      => 'product',
				'query_type'     => 'yes',
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'text_tab',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
			]
		);

		$repeater->add_control(
			'item_title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Tab title',
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

		/**
		 * Repeater settings
		 */
		$this->add_control(
			'tabs_items',
			[
				'type'        => Controls_Manager::REPEATER,
				'title_field' => '{{{ item_title }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'item_title' => 'Tab title 1',
					],
					[
						'item_title' => 'Tab title 2',
					],
					[
						'item_title' => 'Tab title 3',
					],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style tab
		 */

		/**
		 * Heading settings
		 */
		$this->start_controls_section(
			'heading_style_section',
			[
				'label' => esc_html__( 'Heading', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tabs_heading_design',
			[
				'label'   => esc_html__( 'Design', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => xts_get_available_options( 'product_tabs_heading_design' ),
				'default' => 'default',
			]
		);

		$this->add_control(
			'title_align',
			[
				'label'     => esc_html__( 'Alignment', 'xts-theme' ),
				'type'      => 'xts_buttons',
				'options'   => [
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
				'default'   => 'left',
				'condition' => [
					'tabs_heading_design!' => [ 'by-sides' ],
				],
			]
		);

		$this->add_responsive_control(
			'tabs_heading_spacing',
			[
				'label'     => esc_html__( 'Vertical spacing', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-tabs-header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Subtitle settings
		 */
		$this->start_controls_section(
			'subtitle_style_section',
			[
				'label' => esc_html__( 'Subtitle', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector' => '{{WRAPPER}} .xts-tabs-subtitle',
				'key'      => 'tabs_subtitle',
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

		xts_get_typography_map(
			$this,
			[
				'selector' => '{{WRAPPER}} .xts-tabs-title',
				'key'      => 'tabs_title',
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
				'selector' => '{{WRAPPER}} .xts-tabs-desc',
				'key'      => 'tabs_description',
			]
		);

		$this->end_controls_section();

		/**
		 * Title settings
		 */
		$this->start_controls_section(
			'tabs_title_style_section',
			[
				'label' => esc_html__( 'Tabs navigation', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_icon_position',
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
				'options' => xts_get_available_options( 'product_tabs_title_style' ),
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
			'title_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '5',
					'bottom'   => '5',
					'left'     => '15',
					'right'    => '15',
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
		 * Design settings
		 */
		$this->start_controls_section(
			'product_design_section',
			[
				'label' => esc_html__( 'Product design', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'design',
			[
				'label'   => esc_html__( 'Design', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => xts_get_available_options( 'product_loop_design_elementor' ),
				'default' => 'inherit',
			]
		);

		$this->add_control(
			'image_size',
			[
				'label'   => esc_html__( 'Image size', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large',
				'options' => xts_get_all_image_sizes_names( 'elementor' ),
			]
		);

		$this->add_control(
			'image_size_custom',
			[
				'label'       => esc_html__( 'Image dimension', 'xts-theme' ),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.', 'xts-theme' ),
				'condition'   => [
					'image_size' => 'custom',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Layout settings
		 */
		$this->start_controls_section(
			'product_layout_section',
			[
				'label' => esc_html__( 'Product layout', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'items_per_page',
			[
				'label'      => esc_html__( 'Items per page', 'xts-theme' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 8,
				],
				'size_units' => '',
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 50,
						'step' => 1,
					],
				],
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
					'size' => 4,
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
			'different_sizes',
			[
				'label'        => esc_html__( 'Different sizes', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '0',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
				'condition'    => [
					'view' => [ 'grid' ],
				],
			]
		);

		$this->add_control(
			'different_sizes_position',
			[
				'label'     => esc_html__( 'Different sizes position', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '2,5,8,9',
				'condition' => [
					'different_sizes' => [ '1' ],
					'view'            => [ 'grid' ],
				],
			]
		);

		$this->add_control(
			'pagination',
			[
				'label'     => esc_html__( 'Pagination', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'without'   => esc_html__( 'Without', 'xts-theme' ),
					'load_more' => esc_html__( 'Load more button', 'xts-theme' ),
					'infinite'  => esc_html__( 'Infinite scrolling', 'xts-theme' ),
					'arrows'    => esc_html__( 'Arrows', 'xts-theme' ),
				],
				'default'   => 'without',
				'condition' => [
					'view' => [ 'grid' ],
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
				'items' => 4,
			]
		);

		$this->end_controls_section();

		/**
		 * Visibility settings
		 */
		$this->start_controls_section(
			'product_visibility_section',
			[
				'label' => esc_html__( 'Product visibility', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'countdown',
			[
				'label'        => esc_html__( 'Countdown timer', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '0',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
			]
		);

		$this->add_control(
			'stock_progress_bar',
			[
				'label'        => esc_html__( 'Stock progress bar', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '0',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
			]
		);

		$this->add_control(
			'categories',
			[
				'label'        => esc_html__( 'Categories', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '0',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
			]
		);

		$this->add_control(
			'product_attributes',
			[
				'label'        => esc_html__( 'Attributes', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '0',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
			]
		);

		$this->add_control(
			'brands',
			[
				'label'        => esc_html__( 'Brands', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '0',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
			]
		);

		$this->add_control(
			'rating',
			[
				'label'        => esc_html__( 'Rating', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
			]
		);

		$this->add_control(
			'hover_image',
			[
				'label'        => esc_html__( 'Hover image', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
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
	 * @since  1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		xts_product_tabs_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Product_Tabs() );
