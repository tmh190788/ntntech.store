<?php
/**
 * Mobile menu burger icon
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
 * Mobile menu burger icon class
 */
class Burger extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'burger';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$options = xts_get_menus_array( 'header_builder' );
		$first   = reset( $options );

		$this->args = array(
			'type'            => 'burger',
			'title'           => esc_html__( 'Mobile menu', 'xts-theme' ),
			'text'            => esc_html__( 'Mobile burger icon', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/mobile-menu.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				'menu_id'      => array(
					'id'          => 'menu_id',
					'title'       => esc_html__( 'Choose menu', 'xts-theme' ),
					'type'        => 'select',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => isset( $first['value'] ) ? $first['value'] : '',
					'options'     => $options,
					'description' => esc_html__( 'Choose which menu to display.', 'xts-theme' ),
				),

				'style'        => array(
					'id'          => 'style',
					'title'       => esc_html__( 'Style', 'xts-theme' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => 'icon',
					'options'     => array(
						'icon'      => array(
							'value' => 'icon',
							'label' => esc_html__( 'Icon only', 'xts-theme' ),
						),
						'icon-text' => array(
							'value' => 'icon-text',
							'label' => esc_html__( 'Icon with text', 'xts-theme' ),
						),
						'text'      => array(
							'value' => 'text',
							'label' => esc_html__( 'Only text', 'xts-theme' ),
						),
					),
					'description' => esc_html__( 'You can change the burger icon style.', 'xts-theme' ),
				),

				'icon_type'    => array(
					'id'       => 'icon_type',
					'title'    => esc_html__( 'Icon', 'xts-theme' ),
					'type'     => 'selector',
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'value'    => 'default',
					'options'  => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/burger-menu.svg',
						),
						'custom'  => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/custom-icon.svg',
						),
					),
					'requires' => array(
						'style' => array(
							'comparison' => 'not_equal',
							'value'      => 'text',
						),
					),
				),

				'custom_icon'  => array(
					'id'          => 'custom_icon',
					'title'       => esc_html__( 'Custom icon', 'xts-theme' ),
					'type'        => 'image',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => '',
					'description' => '',
					'requires'    => array(
						'icon_type' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
						'style'     => array(
							'comparison' => 'not_equal',
							'value'      => 'text',
						),
					),
				),

				'position'     => array(
					'id'          => 'position',
					'type'        => 'selector',
					'title'       => esc_html__( 'Position', 'xts-theme' ),
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => 'left',
					'options'     => array(
						'left'  => array(
							'value' => 'left',
							'label' => esc_html__( 'Left', 'xts-theme' ),
						),
						'right' => array(
							'value' => 'right',
							'label' => esc_html__( 'Right', 'xts-theme' ),
						),
					),
					'description' => esc_html__( 'Position of the mobile menu sidebar.', 'xts-theme' ),
				),

				'color_scheme' => array(
					'id'      => 'color_scheme',
					'type'    => 'selector',
					'title'   => esc_html__( 'Color scheme', 'xts-theme' ),
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

				'search_form'  => array(
					'id'    => 'search_form',
					'type'  => 'switcher',
					'title' => esc_html__( 'Show search form', 'xts-theme' ),
					'tab'   => esc_html__( 'General', 'xts-theme' ),
					'value' => true,
				),
			),
		);
	}

}
