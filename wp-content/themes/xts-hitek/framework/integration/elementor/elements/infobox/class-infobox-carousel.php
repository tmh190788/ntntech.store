<?php
/**
 * Infobox carousel map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Infobox_Carousel extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_infobox_carousel';
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
		return esc_html__( 'Infobox carousel', 'xts-theme' );
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
		return 'xf-el-infobox-carusel';
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
		 * Infobox settings
		 */
		$this->start_controls_section(
			'infoboxes_content',
			[
				'label' => esc_html__( 'Infoboxes', 'xts-theme' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'infobox_tabs' );

		$repeater->start_controls_tab(
			'link_tab',
			[
				'label' => esc_html__( 'Link', 'xts-theme' ),
			]
		);

		xts_get_infobox_content_general_map( $repeater );

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'icon_tab',
			[
				'label' => esc_html__( 'Icon', 'xts-theme' ),
			]
		);

		xts_get_infobox_content_icon_map( $repeater );

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'text_tab',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
			]
		);

		xts_get_infobox_content_subtitle_map( $repeater );

		xts_get_infobox_content_title_map( $repeater );

		xts_get_infobox_content_description_map( $repeater );

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'button_tab',
			[
				'label' => esc_html__( 'Button', 'xts-theme' ),
			]
		);

		xts_get_button_content_general_map( $repeater );

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
						'icon'        => [
							'value'   => 'fas fa-store',
							'library' => 'fa-solid',
						],
						'title'       => 'Infobox title, click to edit.',
						'subtitle'    => 'Infobox subtitle text',
						'description' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.',
					],
					[
						'icon'        => [
							'value'   => 'fas fa-user',
							'library' => 'fa-solid',
						],
						'title'       => 'Infobox title, click to edit.',
						'subtitle'    => 'Infobox subtitle text',
						'description' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.',
					],
					[
						'icon'        => [
							'value'   => 'fas fa-shield-alt',
							'library' => 'fa-solid',
						],
						'title'       => 'Infobox title, click to edit.',
						'subtitle'    => 'Infobox subtitle text',
						'description' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.',
					],
					[
						'icon'        => [
							'value'   => 'fas fa-sync-alt',
							'library' => 'fa-solid',
						],
						'title'       => 'Infobox title, click to edit.',
						'subtitle'    => 'Infobox subtitle text',
						'description' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.',
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
			'general_section',
			[
				'label' => esc_html__( 'General', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_infobox_style_general_map( $this );

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
				'items_mobile' => 1,
			]
		);

		$this->end_controls_section();

		/**
		 * Icon settings
		 */
		$this->start_controls_section(
			'icon_section',
			[
				'label' => esc_html__( 'Icon', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_infobox_style_icon_map( $this );

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

		xts_get_infobox_style_subtitle_map( $this );

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

		xts_get_infobox_style_title_map( $this );

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

		xts_get_infobox_style_description_map( $this );

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
	 * @since  1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		xts_infobox_carousel_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Infobox_Carousel() );
