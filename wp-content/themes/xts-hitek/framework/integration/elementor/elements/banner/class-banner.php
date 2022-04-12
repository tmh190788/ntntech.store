<?php
/**
 * Banner map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Plugin;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable description into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Banner extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_banner';
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
		return esc_html__( 'Banner', 'xts-theme' );
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
		return 'xf-el-banner';
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

		xts_get_banner_content_general_map( $this );

		$this->end_controls_section();

		/**
		 * Image settings
		 */
		$this->start_controls_section(
			'image_section',
			[
				'label' => esc_html__( 'Image', 'xts-theme' ),
			]
		);

		xts_get_banner_content_image_map( $this );

		$this->end_controls_section();

		/**
		 * Text settings
		 */
		$this->start_controls_section(
			'text_section',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
			]
		);

		xts_get_banner_content_subtitle_map( $this );

		xts_get_banner_content_title_map( $this );

		xts_get_banner_content_description_map( $this );

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

		xts_get_button_content_general_map( $this );

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

		xts_get_banner_style_general_map( $this );

		$this->end_controls_section();

		/**
		 * Layout settings
		 */
		$this->start_controls_section(
			'layout_style_section',
			[
				'label' => esc_html__( 'Layout', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_banner_layout_map( $this );

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

		xts_get_banner_style_subtitle_map( $this );

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

		xts_get_banner_style_title_map( $this );

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

		xts_get_banner_style_description_map( $this );

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
				'size'  => 's',
				'align' => '',
			]
		);

		xts_get_button_style_icon_map( $this );

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
		xts_banner_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Banner() );
