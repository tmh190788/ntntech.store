<?php
/**
 * Team member map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Team_Member extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_team_member';
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
		return esc_html__( 'Team member', 'xts-theme' );
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
		return 'xf-el-team-member';
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
		 * Image settings
		 */
		$this->start_controls_section(
			'image_section',
			[
				'label' => esc_html__( 'Image', 'xts-theme' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Choose image', 'xts-theme' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => xts_get_elementor_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'medium',
				'separator' => 'none',
			]
		);

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

		$this->add_control(
			'name',
			[
				'label'   => esc_html__( 'Name', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Norman Clifton',
			]
		);

		$this->add_control(
			'position',
			[
				'label'   => esc_html__( 'Position', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Corporation CEO',
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'xts-theme' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.',
			]
		);

		$this->end_controls_section();

		/**
		 * Social links settings
		 */
		$this->start_controls_section(
			'social_content_section',
			[
				'label' => esc_html__( 'Social links', 'xts-theme' ),
			]
		);

		$this->add_control(
			'social_icons_switcher',
			[
				'label'        => esc_html__( 'Social links', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'social_icon',
			[
				'label'       => esc_html__( 'Icon', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => [
					'facebook'   => esc_html__( 'Facebook', 'xts-theme' ),
					'twitter'    => esc_html__( 'Twitter', 'xts-theme' ),
					'email'      => esc_html__( 'Email', 'xts-theme' ),
					'behance'    => esc_html__( 'Behance', 'xts-theme' ),
					'dribbble'   => esc_html__( 'Dribbble', 'xts-theme' ),
					'flickr'     => esc_html__( 'Flickr', 'xts-theme' ),
					'github'     => esc_html__( 'Github', 'xts-theme' ),
					'instagram'  => esc_html__( 'Instagram', 'xts-theme' ),
					'linkedin'   => esc_html__( 'LinkedIn', 'xts-theme' ),
					'ok'         => esc_html__( 'Odnoklassniki', 'xts-theme' ),
					'pinterest'  => esc_html__( 'Pinterest', 'xts-theme' ),
					'snapchat'   => esc_html__( 'Snapchat', 'xts-theme' ),
					'soundcloud' => esc_html__( 'SoundCloud', 'xts-theme' ),
					'spotify'    => esc_html__( 'Spotify', 'xts-theme' ),
					'telegram'   => esc_html__( 'Telegram', 'xts-theme' ),
					'tumblr'     => esc_html__( 'Tumblr', 'xts-theme' ),
					'vimeo'      => esc_html__( 'Vimeo', 'xts-theme' ),
					'vk'         => esc_html__( 'VK', 'xts-theme' ),
					'whatsapp'   => esc_html__( 'WhatsApp', 'xts-theme' ),
					'youtube'    => esc_html__( 'Youtube', 'xts-theme' ),
					'viber'      => esc_html__( 'Viber', 'xts-theme' ),
					'tiktok'     => esc_html__( 'TikTok', 'xts-theme' ),
				],
				'default'     => 'facebook',
			]
		);

		$repeater->add_control(
			'social_link',
			[
				'label'   => esc_html__( 'Link', 'xts-theme' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '#',
			]
		);

		$this->add_control(
			'social_icon_list',
			[
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'default'   => [
					[
						'social_icon' => 'facebook',
						'social_link' => '#',
					],
					[
						'social_icon' => 'twitter',
						'social_link' => '#',
					],
					[
						'social_icon' => 'linkedin',
						'social_link' => '#',
					],
					[
						'social_icon' => 'instagram',
						'social_link' => '#',
					],
					[
						'social_icon' => 'github',
						'social_link' => '#',
					],
				],
				'condition' => [
					'social_icons_switcher' => [ 'yes' ],
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

		$this->add_control(
			'design',
			[
				'label'   => esc_html__( 'Design', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'xts-theme' ),
					'mask'    => esc_html__( 'Mask', 'xts-theme' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'text_align',
			[
				'label'   => esc_html__( 'Alignment', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
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
				'default' => 'center',
			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label'   => esc_html__( 'Color scheme', 'xts-theme' ),
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

		/**
		 * Background color settings
		 */
		xts_get_background_map(
			$this,
			[
				'key'                  => 'team_member',
				'normal_selector'      => '{{WRAPPER}} .xts-member',
				'normal_default_color' => 'rgba(255,255,255,0.5)',
				'switcher_condition'   => [
					'design' => [ 'default' ],
				],
				'tabs_condition'       => [
					'design' => [ 'default' ],
				],
			]
		);

		xts_get_background_map(
			$this,
			[
				'key'                => 'team_member_hovered',
				'normal_selector'    => '{{WRAPPER}} .xts-member.xts-design-mask .xts-member-content',
				'switcher_condition' => [
					'design' => [ 'mask' ],
				],
				'tabs_condition'     => [
					'design' => [ 'mask' ],
				],
			]
		);

		/**
		 * Shadow settings
		 */
		xts_get_shadow_map(
			$this,
			[
				'key'             => 'team_member',
				'normal_selector' => '{{WRAPPER}} .xts-member',
				'hover_selector'  => '{{WRAPPER}} .xts-member:hover',
				'divider'         => 'no',
			]
		);

		/**
		 * Padding settings
		 */
		$this->add_control(
			'padding_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'member_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .xts-member.xts-design-default, {{WRAPPER}} .xts-member.xts-design-mask .xts-member-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		/**
		 * Border radius settings
		 */
		$this->add_control(
			'border_radius_divider',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .xts-member' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Name settings
		 */
		$this->start_controls_section(
			'name_style_section',
			[
				'label' => esc_html__( 'Name', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-member-name',
				'key'               => 'name',
				'text_size_default' => 'm',
			]
		);

		$this->end_controls_section();

		/**
		 * Position settings
		 */
		$this->start_controls_section(
			'position_style_section',
			[
				'label' => esc_html__( 'Position', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector'          => '{{WRAPPER}} .xts-member-position',
				'key'               => 'position',
				'text_size_default' => 's',
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
				'selector' => '{{WRAPPER}} .xts-member-description',
				'key'      => 'description',
			]
		);

		$this->end_controls_section();

		/**
		 * Social links settings
		 */
		$this->start_controls_section(
			'social_style_section',
			[
				'label' => esc_html__( 'Social links', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		xts_get_social_buttons_style_buttons_map( $this );

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
		xts_team_member_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Team_Member() );

