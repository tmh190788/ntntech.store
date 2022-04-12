<?php
/**
 * AJAX Search timer map
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
class Search extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_ajax_search';
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
		return esc_html__( 'AJAX Search', 'xts-theme' );
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
			return [ 'xts-autocomplete' ];
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
		return 'xf-el-ajax-search';
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
			'count',
			[
				'label'      => esc_html__( 'Number of items to show', 'xts-theme' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 3,
				],
				'size_units' => '',
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 25,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'post_type',
			[
				'label'   => esc_html__( 'Search post type', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'product'       => esc_html__( 'Product', 'xts-theme' ),
					'post'          => esc_html__( 'Post', 'xts-theme' ),
					'xts-portfolio' => esc_html__( 'Portfolio', 'xts-theme' ),
				],
				'default' => 'product',
			]
		);

		$this->add_control(
			'ajax',
			[
				'label'        => esc_html__( 'AJAX search', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'thumbnail',
			[
				'label'        => esc_html__( 'Show thumbnail', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'price',
			[
				'label'        => esc_html__( 'Show price', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_type' => 'product',
				],
			]
		);

		$this->add_control(
			'categories',
			[
				'label'        => esc_html__( 'Show categories', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_type' => 'product',
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
			'search_style',
			[
				'label'   => esc_html__( 'Search style', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => xts_get_available_options( 'search_style_elementor' ),
				'default' => 'default',
			]
		);

		$this->add_control(
			'form_color_scheme',
			[
				'label'   => esc_html__( 'Form color scheme', 'xts-theme' ),
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
		xts_ajax_search_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Search() );
