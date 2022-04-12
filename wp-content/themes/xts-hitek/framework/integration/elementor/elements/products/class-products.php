<?php
/**
 * Products map
 *
 * @package xts
 */

namespace XTS\Elementor;

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
class Products extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_products';
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
		return esc_html__( 'Products', 'xts-theme' );
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
		return 'xf-el-products';
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
		 * General settings
		 */
		$this->start_controls_section(
			'general_content_section',
			[
				'label' => esc_html__( 'General', 'xts-theme' ),
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
			'product_source_heading',
			[
				'label'     => esc_html__( 'Product source', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
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

		$this->add_control(
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

		$this->add_control(
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
			$this,
			array(
				'exclude_search' => 'xts_get_posts_by_query',
				'exclude_render' => 'xts_get_posts_title_by_id',
				'post_type'      => 'product',
				'query_type'     => 'yes',
			)
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
				'default' => 'woocommerce_thumbnail',
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
			'visibility_section',
			[
				'label' => esc_html__( 'Visibility', 'xts-theme' ),
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
		xts_products_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Products() );
