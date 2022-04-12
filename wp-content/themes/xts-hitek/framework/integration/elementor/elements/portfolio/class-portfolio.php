<?php
/**
 * Portfolio map
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
class Portfolio extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_portfolio';
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
		return esc_html__( 'Portfolio', 'xts-theme' );
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
			return [ 'xts-isotope', 'xts-photoswipe' ];
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
		return 'xf-el-portfolio';
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
			'items_per_page',
			[
				'label'      => esc_html__( 'Items per page', 'xts-theme' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 6,
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
			'taxonomies',
			[
				'label'       => esc_html__( 'Categories', 'xts-theme' ),
				'type'        => 'xts_autocomplete',
				'search'      => 'xts_get_taxonomies_by_query',
				'render'      => 'xts_get_taxonomies_title_by_id',
				'taxonomy'    => 'xts-portfolio-cat',
				'multiple'    => true,
				'label_block' => true,
			]
		);

		$this->add_control(
			'include',
			[
				'label'       => esc_html__( 'Include only', 'xts-theme' ),
				'type'        => 'xts_autocomplete',
				'search'      => 'xts_get_posts_by_query',
				'render'      => 'xts_get_posts_title_by_id',
				'post_type'   => 'xts-portfolio',
				'multiple'    => true,
				'label_block' => true,
			]
		);

		xts_get_posts_query_map(
			$this,
			array(
				'exclude_search'  => 'xts_get_posts_by_query',
				'exclude_render'  => 'xts_get_posts_title_by_id',
				'post_type'       => 'xts-portfolio',
				'orderby_options' => [
					'id'             => esc_html__( 'ID', 'xts-theme' ),
					'date'           => esc_html__( 'Date', 'xts-theme' ),
					'title'          => esc_html__( 'Title', 'xts-theme' ),
					'rand'           => esc_html__( 'Random', 'xts-theme' ),
					'menu_order'     => esc_html__( 'Menu order', 'xts-theme' ),
					'author'         => esc_html__( 'Author', 'xts-theme' ),
					'modified'       => esc_html__( 'Last modified date', 'xts-theme' ),
					'meta_value'     => esc_html__( 'Meta value', 'xts-theme' ), // phpcs:ignore
					'meta_value_num' => esc_html__( 'Meta value number', 'xts-theme' ),
					'post__in'       => esc_html__( 'Include order', 'xts-theme' ),
				],
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
				'options' => xts_get_available_options( 'portfolio_design_elementor' ),
				'default' => 'inherit',
			]
		);

		$this->add_control(
			'image_size',
			[
				'label'   => esc_html__( 'Image size', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium',
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

		/**
		 * Distortion effect Portfolio_Distortion_Effect.
		 */

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
			'pagination',
			[
				'label'     => esc_html__( 'Pagination', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'without'   => esc_html__( 'Without', 'xts-theme' ),
					'load_more' => esc_html__( 'Load more button', 'xts-theme' ),
					'infinite'  => esc_html__( 'Infinite scrolling', 'xts-theme' ),
				],
				'default'   => 'without',
				'condition' => [
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
			'filters_type',
			[
				'label'   => esc_html__( 'Categories filters', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'without' => esc_html__( 'Without', 'xts-theme' ),
					'links'   => esc_html__( 'Categories links', 'xts-theme' ),
					'masonry' => esc_html__( 'Masonry', 'xts-theme' ),
				],
				'default' => 'without',
			]
		);

		$this->add_control(
			'masonry',
			[
				'label'        => esc_html__( 'Masonry grid', 'xts-theme' ),
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
			'different_images',
			[
				'label'        => esc_html__( 'Different images', 'xts-theme' ),
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
			'different_images_position',
			[
				'label'     => esc_html__( 'Different images position', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '2,5,8,9',
				'condition' => [
					'different_images' => [ '1' ],
					'view'             => [ 'grid' ],
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

		xts_get_carousel_map( $this );

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
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		xts_portfolio_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Portfolio() );
