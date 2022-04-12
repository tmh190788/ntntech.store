<?php
/**
 * Banner carousel map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Plugin;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Banner_Carousel extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_banner_carousel';
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
		return esc_html__( 'Banner carousel', 'xts-theme' );
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
		return 'xf-el-banner-carousel';
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
		 * Banners settings
		 */
		$this->start_controls_section(
			'banners_content',
			[
				'label' => esc_html__( 'Banners', 'xts-theme' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'banner_tabs' );

		$repeater->start_controls_tab(
			'link_tab',
			[
				'label' => esc_html__( 'Link', 'xts-theme' ),
			]
		);

		xts_get_banner_content_general_map( $repeater );

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'image_tab',
			[
				'label' => esc_html__( 'Image', 'xts-theme' ),
			]
		);

		xts_get_banner_content_image_map( $repeater, ' {{CURRENT_ITEM}}' );

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'text_tab',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
			]
		);

		xts_get_banner_content_subtitle_map( $repeater );

		xts_get_banner_content_title_map( $repeater );

		xts_get_banner_content_description_map( $repeater );

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'button_tab',
			[
				'label' => esc_html__( 'Button', 'xts-theme' ),
			]
		);

		xts_get_button_content_general_map( $repeater );

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'layout_tab',
			[
				'label' => esc_html__( 'Layout', 'xts-theme' ),
			]
		);

		xts_get_banner_layout_map( $repeater, ' {{CURRENT_ITEM}}' );

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		/**
		 * Repeater settings
		 */
		$this->add_control(
			'content_repeater',
			[
				'type'        => Controls_Manager::REPEATER,
				'title_field' => '{{{ title }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title'    => 'Banner title, click to edit.',
						'subtitle' => 'Banner subtitle text',
					],
					[
						'title'    => 'Banner title, click to edit.',
						'subtitle' => 'Banner subtitle text',
					],
					[
						'title'    => 'Banner title, click to edit.',
						'subtitle' => 'Banner subtitle text',
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

		xts_get_banner_style_general_map(
			$this,
			[
				'banner_hover_options' => xts_get_available_options( 'banner_carousel_element_hover_effect_elementor' ),
			]
		);

		$this->end_controls_section();

		/**
		 * Carousel settings
		 */

		$this->start_controls_section(
			'carousel_section',
			[
				'label' => esc_html__( 'Carousel settings', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_carousel_map(
			$this,
			[
				'arrows_horizontal_position' => true,
				'items'                      => 2,
				'items_tablet'               => 2,
				'items_mobile'               => 1,
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
		xts_banner_carousel_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Banner_Carousel() );
