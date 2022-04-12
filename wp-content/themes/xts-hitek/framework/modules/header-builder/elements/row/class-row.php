<?php
/**
 * Basic structure element - row
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Basic structure element - row
 */
class Row extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'row';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'row',
			'title'           => esc_html__( 'Row', 'xts-theme' ),
			'text'            => esc_html__( 'Row', 'xts-theme' ),
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => false,
			'drag_target_for' => array(),
			'drag_source'     => '',
			'removable'       => false,
			'addable'         => false,
			'it_works'        => 'row',
			'class'           => '',
			'content'         => array(),
			'params'          => array(
				'flex_layout'            => array(
					'id'          => 'flex_layout',
					'title'       => esc_html__( 'Row flex layout', 'xts-theme' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => 'stretch-center',
					'options'     => array(
						'stretch-center' => array(
							'label' => esc_html__( 'Flexible middle column', 'xts-theme' ),
							'value' => 'stretch-center',
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/row/stretch-center.svg',
						),
						'equal-sides'    => array(
							'label' => esc_html__( 'Equal right and left columns', 'xts-theme' ),
							'value' => 'equal-sides',
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/row/equal-sides.svg',
						),
					),
					'description' => wp_kses( __( 'Determine the "flex layout" for this row. More information about both options read in our <a href="#" target="_blank">documentation here</a>.', 'xts-theme' ), 'default' ),
				),
				'height'                 => array(
					'id'          => 'height',
					'title'       => esc_html__( 'Row height', 'xts-theme' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'from'        => 0,
					'to'          => 200,
					'value'       => 50,
					'units'       => 'px',
					'description' => esc_html__( 'Determine the header height value in pixels.', 'xts-theme' ),
				),
				'mobile_height'          => array(
					'id'          => 'mobile_height',
					'title'       => esc_html__( 'Row height on mobile devices', 'xts-theme' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'from'        => 0,
					'to'          => 200,
					'value'       => 40,
					'units'       => 'px',
					'description' => esc_html__( 'Determine the header height for mobile devices value in pixels.', 'xts-theme' ),
				),
				'align_dropdowns_bottom' => array(
					'id'          => 'align_dropdowns_bottom',
					'title'       => esc_html__( 'Align dropdowns below the row', 'xts-theme' ),
					'type'        => 'switcher',
					'onText'      => esc_html__( 'Yes', 'xts-theme' ),
					'offText'     => esc_html__( 'No', 'xts-theme' ),
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => false,
					'description' => esc_html__( 'You can align all elements dropdowns (menu, account, cart etc.) below the elements itself or below the row.', 'xts-theme' ),
				),
				'hide_desktop'           => array(
					'id'          => 'hide_desktop',
					'title'       => esc_html__( 'Hide on desktop', 'xts-theme' ),
					'type'        => 'switcher',
					'onText'      => esc_html__( 'Yes', 'xts-theme' ),
					'offText'     => esc_html__( 'No', 'xts-theme' ),
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => false,
					'description' => esc_html__( 'Disable this row for desktop devices completely.', 'xts-theme' ),
				),
				'hide_mobile'            => array(
					'id'          => 'hide_mobile',
					'title'       => esc_html__( 'Hide on mobile', 'xts-theme' ),
					'type'        => 'switcher',
					'onText'      => esc_html__( 'Yes', 'xts-theme' ),
					'offText'     => esc_html__( 'No', 'xts-theme' ),
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => false,
					'description' => esc_html__( 'Disable this row for mobile devices completely.', 'xts-theme' ),
				),
				'sticky'                 => array(
					'id'          => 'sticky',
					'title'       => esc_html__( 'Sticky row', 'xts-theme' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => false,
					'description' => esc_html__( 'Make this row sticky on scroll.', 'xts-theme' ),
				),
				'sticky_height'          => array(
					'id'          => 'sticky_height',
					'title'       => esc_html__( 'Row height on sticky header', 'xts-theme' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'from'        => 0,
					'to'          => 200,
					'value'       => 60,
					'units'       => 'px',
					'description' => esc_html__( 'Determine the header height for sticky header value in pixels.', 'xts-theme' ),
					'requires'    => array(
						'sticky' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'color_scheme'           => array(
					'id'          => 'color_scheme',
					'title'       => esc_html__( 'Text color scheme', 'xts-theme' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Colors', 'xts-theme' ),
					'value'       => 'dark',
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
					'description' => esc_html__( 'Select different text color scheme depending on your background.', 'xts-theme' ),
				),
				'shadow'                 => array(
					'id'          => 'shadow',
					'title'       => esc_html__( 'Shadow', 'xts-theme' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'Colors', 'xts-theme' ),
					'value'       => false,
					'description' => esc_html__( 'Add shadow to the header section.', 'xts-theme' ),
				),
				'background'             => array(
					'id'          => 'background',
					'title'       => esc_html__( 'Background settings', 'xts-theme' ),
					'type'        => 'bg',
					'tab'         => esc_html__( 'Colors', 'xts-theme' ),
					'value'       => '',
					'description' => '',
				),
				'border'                 => array(
					'id'              => 'border',
					'title'           => esc_html__( 'Border', 'xts-theme' ),
					'type'            => 'border',
					'sides'           => array( 'top', 'bottom', 'left', 'right' ),
					'tab'             => esc_html__( 'Colors', 'xts-theme' ),
					'colorpicker_top' => true,
					'container'       => true,
					'value'           => '',
				),
			),
		);
	}
}
