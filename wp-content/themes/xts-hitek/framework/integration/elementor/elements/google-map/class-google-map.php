<?php
/**
 * Google map map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Image_Size;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Google_Map extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_google_map';
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
		return esc_html__( 'Google map', 'xts-theme' );
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
			return [ 'google.map.api', 'xts-maplace' ];
		} else {
			return [ 'google.map.api' ];
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
		return 'xf-el-google-map';
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
		 * Location settings
		 */
		$this->start_controls_section(
			'location_content_section',
			[
				'label' => esc_html__( 'Location', 'xts-theme' ),
			]
		);

		$this->add_control(
			'latitude',
			[
				'label'   => esc_html__( 'Latitude', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 45.9,
			]
		);

		$this->add_control(
			'longitude',
			[
				'label'       => esc_html__( 'Longitude', 'xts-theme' ),
				'description' => 'Read an instruction about how to get the coordinates <a href="https://support.google.com/maps/answer/18539?co=GENIE.Platform%3DDesktop&hl=en" target="_blank">here</a>.',
				'type'        => Controls_Manager::TEXT,
				'default'     => 10.9,
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
			'text_content_type',
			[
				'label'       => esc_html__( 'Content type', 'xts-theme' ),
				'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'text'       => esc_html__( 'Text', 'xts-theme' ),
					'html_block' => esc_html__( 'HTML Block', 'xts-theme' ),
				],
				'default'     => 'text',
			]
		);

		$this->add_control(
			'text',
			[
				'label'     => esc_html__( 'Text', 'xts-theme' ),
				'type'      => Controls_Manager::WYSIWYG,
				'condition' => [
					'text_content_type' => [ 'text' ],
				],
			]
		);

		$this->add_control(
			'html_block_id',
			[
				'label'       => esc_html__( 'HTML Block', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => xts_get_html_blocks_array( 'elementor' ),
				'description' => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
				'default'     => '0',
				'classes'     => 'xts-html-block-links',
				'condition'   => [
					'text_content_type' => [ 'html_block' ],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Marker settings
		 */
		$this->start_controls_section(
			'marker_content_section',
			[
				'label' => esc_html__( 'Marker', 'xts-theme' ),
			]
		);

		$this->add_control(
			'marker_icon',
			[
				'label'   => esc_html__( 'Marker icon', 'xts-theme' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'marker_icon',
				'default'   => 'large',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'marker_title',
			[
				'label' => esc_html__( 'Title', 'xts-theme' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'marker_text',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
				'type'  => Controls_Manager::WYSIWYG,
			]
		);

		$this->end_controls_section();

		/**
		 * Lazy loading settings
		 */
		$this->start_controls_section(
			'lazy_loading_content_section',
			[
				'label' => esc_html__( 'Lazy loading', 'xts-theme' ),
			]
		);

		$this->add_control(
			'lazy_type',
			[
				'label'   => esc_html__( 'Event', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'page_load' => [
						'title' => esc_html__( 'On page load', 'xts-theme' ),
					],
					'scroll'    => [
						'title' => esc_html__( 'On scroll', 'xts-theme' ),
					],
					'button'    => [
						'title' => esc_html__( 'On button click', 'xts-theme' ),
					],
				],
				'default' => 'page_load',
			]
		);

		$this->add_control(
			'lazy_placeholder',
			[
				'label'     => esc_html__( 'Choose placeholder', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => xts_get_elementor_placeholder_image_src( 'map' ),
				],
				'condition' => [
					'lazy_type' => [ 'scroll', 'button' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'lazy_placeholder',
				'default'   => 'large',
				'separator' => 'none',
				'condition' => [
					'lazy_type' => [ 'scroll', 'button' ],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style tab
		 */

		/**
		 * Style settings
		 */
		$this->start_controls_section(
			'general_style_section',
			[
				'label' => esc_html__( 'General', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'zoom',
			[
				'label'      => esc_html__( 'Zoom', 'xts-theme' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => '',
				'default'    => [
					'size' => 15,
				],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 19,
						'step' => 1,
					],
				],
			]
		);

		$this->add_responsive_control(
			'map_height',
			[
				'label'     => esc_html__( 'Map height', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 400,
				],
				'range'     => [
					'px' => [
						'min'  => 100,
						'max'  => 2000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-map' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'mouse_zoom',
			[
				'label'        => esc_html__( 'Zoom with mouse wheel', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'default_ui',
			[
				'label'        => esc_html__( 'Disable default map UI', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'json_style',
			[
				'label'       => esc_html__( 'Styles (JSON)', 'xts-theme' ),
				'type'        => 'xts_google_json',
				'description' => 'Styled maps allow you to customize the presentation of the standard Google base maps, changing the visual display of such elements as roads, parks, and built-up areas.<br> You can find more Google Maps styles on the website: <a target="_blank" href="https://snazzymaps.com/">Snazzy Maps</a><br> Just copy JSON code and paste it here.',
			]
		);

		$this->end_controls_section();

		/**
		 * Text settings
		 */
		$this->start_controls_section(
			'text_style_section',
			[
				'label' => esc_html__( 'Text', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'text_width',
			[
				'label'          => esc_html__( 'Width', 'xts-theme' ),
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
					'{{WRAPPER}} .xts-map-content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_horizontal_position',
			[
				'label'   => esc_html__( 'Horizontal position', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'start'  => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/google-map/horizontal-position/left.svg',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/google-map/horizontal-position/center.svg',
					],
					'end'    => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/google-map/horizontal-position/right.svg',
					],
				],
				'default' => 'start',
			]
		);

		$this->add_control(
			'text_vertical_position',
			[
				'label'   => esc_html__( 'Vertical position', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'start'  => [
						'title' => esc_html__( 'Top', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/google-map/vertical-position/top.svg',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/google-map/vertical-position/middle.svg',
					],
					'end'    => [
						'title' => esc_html__( 'Bottom', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/google-map/vertical-position/bottom.svg',
					],
				],
				'default' => 'start',
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
		xts_google_map_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Google_Map() );
