<?php
/**
 * Video map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
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
class Video extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_video';
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
		return esc_html__( 'Video', 'xts-theme' );
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
		return 'xf-el-video';
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
		 * General settings
		 */
		$this->start_controls_section(
			'general_content_section',
			[
				'label' => esc_html__( 'General', 'xts-theme' ),
			]
		);

		$this->add_control(
			'video_type',
			[
				'label'   => esc_html__( 'Source', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'youtube' => esc_html__( 'YouTube', 'xts-theme' ),
					'vimeo'   => esc_html__( 'Vimeo', 'xts-theme' ),
					'hosted'  => esc_html__( 'Self hosted', 'xts-theme' ),
				],
				'default' => 'youtube',
			]
		);

		$this->add_control(
			'video_hosted_url',
			[
				'label'      => esc_html__( 'Choose File', 'xts-theme' ),
				'type'       => Controls_Manager::MEDIA,
				'media_type' => 'video',
				'condition'  => [
					'video_type' => 'hosted',
				],
			]
		);

		$this->add_control(
			'video_youtube_url',
			[
				'label'       => esc_html__( 'Link', 'xts-theme' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your URL', 'xts-theme' ) . ' (YouTube)',
				'condition'   => [
					'video_type' => 'youtube',
				],
				'default'     => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
			]
		);

		$this->add_control(
			'video_vimeo_url',
			[
				'label'       => esc_html__( 'Link', 'xts-theme' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your URL', 'xts-theme' ) . ' (Vimeo)',
				'condition'   => [
					'video_type' => 'vimeo',
				],
				'default'     => 'https://vimeo.com/235215203',
			]
		);

		$this->add_control(
			'video_action_button',
			[
				'label'   => esc_html__( 'Action button', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'without' => esc_html__( 'Without', 'xts-theme' ),
					'overlay' => esc_html__( 'Play button on image', 'xts-theme' ),
					'play'    => esc_html__( 'Play button', 'xts-theme' ),
					'button'  => esc_html__( 'Button', 'xts-theme' ),
				],
				'default' => 'overlay',
			]
		);

		$this->add_control(
			'video_overlay_lightbox',
			[
				'label'        => esc_html__( 'Lightbox', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'    => [
					'video_action_button' => 'overlay',
				],
				'default'      => 'no',
			]
		);

		$this->add_control(
			'play_button_label',
			[
				'label'     => esc_html__( 'Label', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'video_action_button' => [ 'overlay', 'play' ],
				],
				'default'   => '',
			]
		);

		$this->end_controls_section();

		/**
		 * Options settings
		 */
		$this->start_controls_section(
			'options_section',
			[
				'label' => esc_html__( 'Video options', 'xts-theme' ),
			]
		);

		$this->add_control(
			'video_autoplay',
			[
				'label'        => esc_html__( 'Autoplay', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'video_mute',
			[
				'label'        => esc_html__( 'Mute', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'video_loop',
			[
				'label'        => esc_html__( 'Loop', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'video_controls',
			[
				'label'        => esc_html__( 'Controls', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'video_type!' => 'vimeo',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Image overlay settings
		 */
		$this->start_controls_section(
			'image_overlay_section',
			[
				'label'     => esc_html__( 'Image overlay', 'xts-theme' ),
				'condition' => [
					'video_action_button' => 'overlay',
				],
			]
		);

		$this->add_control(
			'video_image_overlay',
			[
				'label'   => esc_html__( 'Choose Image', 'xts-theme' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => xts_get_elementor_placeholder_image_src( 'banner' ),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'video_image_overlay',
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->end_controls_section();

		/**
		 * Button settings
		 */
		$this->start_controls_section(
			'content_button_section',
			[
				'label'     => esc_html__( 'Button', 'xts-theme' ),
				'condition' => [
					'video_action_button' => 'button',
				],
			]
		);

		xts_get_button_content_general_map(
			$this,
			[
				'text' => 'Play video',
				'link' => false,
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
				'label'     => esc_html__( 'General', 'xts-theme' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'video_action_button!' => [ 'button', 'play' ],
				],
			]
		);

		$this->add_control(
			'video_size',
			[
				'label'   => esc_html__( 'Size', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'custom'       => esc_html__( 'Custom', 'xts-theme' ),
					'aspect_ratio' => esc_html__( 'Aspect ratio', 'xts-theme' ),
				],
				'default' => 'custom',
			]
		);

		$this->add_responsive_control(
			'video_height',
			[
				'label'     => esc_html__( 'Height', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 300,
				],
				'range'     => [
					'px' => [
						'min'  => 100,
						'max'  => 2000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-el-video' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'video_size' => 'custom',
				],
			]
		);

		$this->add_control(
			'video_aspect_ratio',
			[
				'label'     => esc_html__( 'Aspect Ratio', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'inherit' => esc_html__( 'Inherit from video', 'xts-theme' ),
					'16-9'    => '16:9',
					'21-9'    => '21:9',
					'4-3'     => '4:3',
					'3-2'     => '3:2',
					'1-1'     => '1:1',
					'9-16'    => '9:16',
				],
				'default'   => '16-9',
				'condition' => [
					'video_size' => 'aspect_ratio',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Button settings
		 */
		$this->start_controls_section(
			'style_button_section',
			[
				'label'     => esc_html__( 'Button', 'xts-theme' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'video_action_button' => 'button',
				],
			]
		);

		xts_get_button_style_general_map( $this );

		xts_get_button_style_icon_map( $this );

		$this->end_controls_section();

		/**
		 * Play button settings
		 */
		$this->start_controls_section(
			'style_play_button_section',
			[
				'label'     => esc_html__( 'Play button', 'xts-theme' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'video_action_button' => [ 'play', 'overlay' ],
				],
			]
		);

		$this->add_control(
			'play_button_align',
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
				'condition' => [
					'video_action_button' => 'play',
				],
				'default'   => 'left',
			]
		);

		$this->add_control(
			'play_button_label_heading',
			[
				'label'     => esc_html__( 'Label', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'play_button_label_color',
			[
				'label'     => esc_html__( 'Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-el-video .xts-el-video-play-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'play_button_label_typography',
				'label'    => esc_html__( 'Custom typography', 'xts-theme' ),
				'selector' => '{{WRAPPER}} .xts-el-video .xts-el-video-play-label',
			]
		);

		$this->add_control(
			'play_button_label_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'play_button_size',
			[
				'label'     => esc_html__( 'Size', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 40,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-el-video-play-btn' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		xts_get_color_map(
			$this,
			[
				'key'              => 'play_button_icon',
				'switcher_title'   => esc_html__( 'Icon color', 'xts-theme' ),
				'normal_selectors' => [
					'{{WRAPPER}} div.xts-el-video .xts-el-video-play-btn' => 'color: {{VALUE}}',
				],
				'hover_selectors'  => [
					'{{WRAPPER}} .xts-action-play .xts-el-video-btn:hover .xts-el-video-play-btn, {{WRAPPER}} .xts-action-overlay:hover .xts-el-video-play-btn' => 'color: {{VALUE}}',
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
	 * @since  1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		xts_video_widget_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Video() );
