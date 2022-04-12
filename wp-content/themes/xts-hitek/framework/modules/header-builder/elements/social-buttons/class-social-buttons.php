<?php
/**
 * Social buttons element map
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Modules;
use XTS\Header_Builder\Element;

/**
 * Social buttons class
 */
class Social_Buttons extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'social-buttons';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'social_buttons',
			'title'           => esc_html__( 'Social buttons', 'xts-theme' ),
			'text'            => esc_html__( 'Social links icons', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/social.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				'type'         => array(
					'id'      => 'type',
					'title'   => esc_html__( 'Type', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'share',
					'options' => array(
						'share'  => array(
							'value' => 'share',
							'label' => esc_html__( 'Share', 'xts-theme' ),
						),
						'follow' => array(
							'value' => 'dropdown',
							'label' => esc_html__( 'Follow', 'xts-theme' ),
						),
					),
				),

				'color_scheme' => array(
					'id'      => 'color_scheme',
					'title'   => esc_html__( 'Color scheme', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'dark',
					'options' => array(
						'dark'  => array(
							'value' => 'dark',
							'label' => esc_html__( 'Dark', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/color/dark.svg',
						),
						'light' => array(
							'value' => 'light',
							'label' => esc_html__( 'Light', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/color/light.svg',
						),
					),
				),

				'size'         => array(
					'id'      => 'size',
					'title'   => esc_html__( 'Size', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'm',
					'options' => array(
						's' => array(
							'value' => 's',
							'label' => esc_html__( 'Small', 'xts-theme' ),
						),
						'm' => array(
							'value' => 'm',
							'label' => esc_html__( 'Medium', 'xts-theme' ),
						),
						'l' => array(
							'value' => 'l',
							'label' => esc_html__( 'Large', 'xts-theme' ),
						),
					),
				),

				'style'        => array(
					'id'      => 'style',
					'title'   => esc_html__( 'Style', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'default',
					'options' => xts_get_available_options( 'social_buttons_style_header_builder' ),
				),

				'shape'        => array(
					'id'      => 'shape',
					'title'   => esc_html__( 'Shape', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'round',
					'options' => array(
						'round'   => array(
							'value' => 'round',
							'label' => esc_html__( 'Round', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/social-buttons/shape/round.svg',
						),
						'rounded' => array(
							'value' => 'rounded',
							'label' => esc_html__( 'Rounded', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/social-buttons/shape/rounded.svg',
						),
						'square'  => array(
							'value' => 'square',
							'label' => esc_html__( 'Square', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/social-buttons/shape/square.svg',
						),
					),
				),
			),
		);
	}
}
