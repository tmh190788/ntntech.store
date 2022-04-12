<?php
/**
 * Basic structure element - categories
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
 * Basic structure element - cart class
 */
class Categories extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'categories';
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
			'type'            => 'categories',
			'title'           => esc_html__( 'Categories', 'xts-theme' ),
			'text'            => esc_html__( 'Categories dropdown', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/categories.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				'menu_id'               => array(
					'id'          => 'menu_id',
					'title'       => esc_html__( 'Choose menu', 'xts-theme' ),
					'type'        => 'select',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => isset( $first['value'] ) ? $first['value'] : '',
					'options'     => $options,
					'description' => esc_html__( 'Choose which menu to display in the header as a categories dropdown.', 'xts-theme' ),
				),

				'more_cat_button'       => array(
					'id'          => 'more_cat_button',
					'title'       => esc_html__( 'Limit categories', 'xts-theme' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => false,
					'description' => esc_html__( 'Display a certain number of categories and "show more" button', 'xts-theme' ),
				),
				'more_cat_button_count' => array(
					'id'          => 'more_cat_button_count',
					'title'       => esc_html__( 'Number of categories', 'xts-theme' ),
					'description' => esc_html__( 'Specify the number of categories to be shown initially', 'xts-theme' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'from'        => 1,
					'to'          => 100,
					'value'       => 5,
					'units'       => '',
					'requires'    => array(
						'more_cat_button' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),

				'style'                 => array(
					'id'      => 'style',
					'title'   => esc_html__( 'Title style', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'text',
					'options' => array(
						'text'      => array(
							'value' => 'text',
							'label' => esc_html__( 'Only text', 'xts-theme' ),
						),
						'icon-text' => array(
							'value' => 'icon-text',
							'label' => esc_html__( 'Icon with text', 'xts-theme' ),
						),
					),
				),

				'icon_style'            => array(
					'id'       => 'icon_style',
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
							'comparison' => 'equal',
							'value'      => 'icon-text',
						),
					),
				),

				'custom_icon'           => array(
					'id'          => 'custom_icon',
					'title'       => esc_html__( 'Custom icon', 'xts-theme' ),
					'type'        => 'image',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => '',
					'description' => '',
					'requires'    => array(
						'icon_style' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
				),

				'color_scheme'          => array(
					'id'          => 'color_scheme',
					'title'       => esc_html__( 'Color scheme', 'xts-theme' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Title', 'xts-theme' ),
					'value'       => 'light',
					'options'     => array(
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
					'description' => esc_html__( 'Select different text color scheme depending on your header background.', 'xts-theme' ),
				),

				'background_color'      => array(
					'id'      => 'background_color',
					'title'   => esc_html__( 'Background color', 'xts-theme' ),
					'type'    => 'select',
					'tab'     => esc_html__( 'Title', 'xts-theme' ),
					'value'   => 'primary',
					'options' => array(
						'primary'   => array(
							'value' => 'primary',
							'label' => esc_html__( 'Primary', 'xts-theme' ),
						),
						'secondary' => array(
							'value' => 'secondary',
							'label' => esc_html__( 'Secondary', 'xts-theme' ),
						),
						'custom'    => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'xts-theme' ),
						),
					),
				),

				'background'            => array(
					'id'          => 'background',
					'title'       => esc_html__( 'Background settings', 'xts-theme' ),
					'type'        => 'bg',
					'tab'         => esc_html__( 'Title', 'xts-theme' ),
					'value'       => '',
					'requires'    => array(
						'background_color' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
					'description' => '',
				),

				'border'                => array(
					'id'              => 'border',
					'title'           => esc_html__( 'Border', 'xts-theme' ),
					'type'            => 'border',
					'tab'             => esc_html__( 'Title', 'xts-theme' ),
					'colorpicker_top' => true,
					'container'       => false,
					'value'           => '',
				),
			),
		);
	}
}
