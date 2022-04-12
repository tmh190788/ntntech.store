<?php
/**
 * Extra menu list map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Image_Size;
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
class Extra_Menu_List extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_extra_menu_list';
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
		return esc_html__( 'Extra menu list', 'xts-theme' );
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
		return 'xf-el-extra-menu-list';
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
			'general_section',
			[
				'label' => esc_html__( 'General', 'xts-theme' ),
			]
		);

		$this->start_controls_tabs( 'extra_menu_tabs' );

		$this->start_controls_tab(
			'link_tab',
			[
				'label' => esc_html__( 'Link', 'xts-theme' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Menu parent item',
			]
		);

		$this->add_control(
			'link',
			[
				'label'   => esc_html__( 'Link', 'xts-theme' ),
				'type'    => Controls_Manager::URL,
				'default' => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'label_tab',
			[
				'label' => esc_html__( 'Label', 'xts-theme' ),
			]
		);

		$this->add_control(
			'label',
			[
				'label'   => esc_html__( 'Label text (optional)', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'   => esc_html__( 'Label color', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'primary'   => esc_html__( 'Primary Color', 'xts-theme' ),
					'secondary' => esc_html__( 'Secondary', 'xts-theme' ),
					'red'       => esc_html__( 'Red', 'xts-theme' ),
					'green'     => esc_html__( 'Green', 'xts-theme' ),
					'blue'      => esc_html__( 'Blue', 'xts-theme' ),
					'orange'    => esc_html__( 'Orange', 'xts-theme' ),
					'grey'      => esc_html__( 'Grey', 'xts-theme' ),
					'white'     => esc_html__( 'White', 'xts-theme' ),
					'black'     => esc_html__( 'Black', 'xts-theme' ),
				],
				'default' => 'primary',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_tab',
			[
				'label' => esc_html__( 'Image', 'xts-theme' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose File', 'xts-theme' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'thumbnail',
				'separator' => 'none',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'extra_menu_tabs' );

		$repeater->start_controls_tab(
			'link_tab',
			[
				'label' => esc_html__( 'Link', 'xts-theme' ),
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Menu child item',
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'   => esc_html__( 'Link', 'xts-theme' ),
				'type'    => Controls_Manager::URL,
				'default' => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'label_tab',
			[
				'label' => esc_html__( 'Label', 'xts-theme' ),
			]
		);

		$repeater->add_control(
			'label',
			[
				'label'   => esc_html__( 'Label text (optional)', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$repeater->add_control(
			'label_color',
			[
				'label'   => esc_html__( 'Label color', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'primary'   => esc_html__( 'Primary Color', 'xts-theme' ),
					'secondary' => esc_html__( 'Secondary', 'xts-theme' ),
					'red'       => esc_html__( 'Red', 'xts-theme' ),
					'green'     => esc_html__( 'Green', 'xts-theme' ),
					'blue'      => esc_html__( 'Blue', 'xts-theme' ),
					'orange'    => esc_html__( 'Orange', 'xts-theme' ),
					'grey'      => esc_html__( 'Grey', 'xts-theme' ),
					'white'     => esc_html__( 'White', 'xts-theme' ),
					'black'     => esc_html__( 'Black', 'xts-theme' ),
				],
				'default' => 'primary',
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'image_tab',
			[
				'label' => esc_html__( 'Image', 'xts-theme' ),
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose File', 'xts-theme' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'thumbnail',
				'separator' => 'none',
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'menu_items_repeater',
			[
				'type'        => Controls_Manager::REPEATER,
				'label'       => esc_html__( 'List items', 'xts-theme' ),
				'separator'   => 'before',
				'title_field' => '{{{ title }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title'       => 'Menu child item 1',
						'label'       => '',
						'label_color' => 'primary',
						'button_link' => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
					[
						'title'       => 'Menu child item  2',
						'label'       => 'New',
						'label_color' => 'green',
						'button_link' => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
					[
						'title'       => 'Menu child item 3',
						'label'       => '',
						'label_color' => 'primary',
						'button_link' => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
					[
						'title'       => 'Menu child item 4',
						'label'       => '',
						'label_color' => 'primary',
						'button_link' => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
					[
						'title'       => 'Menu child item 5',
						'label'       => 'Hot',
						'label_color' => 'red',
						'button_link' => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
					[
						'title'       => 'Menu child item 6',
						'label'       => '',
						'label_color' => 'primary',
						'button_link' => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
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
		xts_extra_menu_list_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Extra_Menu_List() );
