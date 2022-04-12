<?php
/**
 * Secondary menu element
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
 * Secondary menu element
 */
class Menu extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'menu';
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
			'type'            => 'menu',
			'title'           => esc_html__( 'Menu', 'xts-theme' ),
			'text'            => esc_html__( 'Secondary menu', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/menu.svg',
			'editable'        => true,
			'container'       => false,
			'drg'             => false,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'edit_on_create'  => true,
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				'menu_id'          => array(
					'id'          => 'menu_id',
					'title'       => esc_html__( 'Choose menu', 'xts-theme' ),
					'type'        => 'select',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => isset( $first['value'] ) ? $first['value'] : '',
					'options'     => $options,
					'description' => esc_html__( 'Choose which menu to display in the header.', 'xts-theme' ),
				),

				'menu_style'       => array(
					'id'      => 'menu_style',
					'title'   => esc_html__( 'Style', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'default',
					'options' => xts_get_available_options( 'menu_style_header_builder' ),
				),

				'menu_full_height' => array(
					'id'       => 'menu_full_height',
					'title'    => esc_html__( 'Full height', 'xts-theme' ),
					'type'     => 'switcher',
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'value'    => false,
					'requires' => array(
						'menu_style' => array(
							'comparison' => 'equal',
							'value'      => array( 'separated', 'underline-dot' ),
						),
					),
				),

				'menu_align'       => array(
					'id'      => 'menu_align',
					'title'   => esc_html__( 'Align', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'left',
					'options' => array(
						'left'   => array(
							'value' => 'left',
							'label' => esc_html__( 'Left', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/align/left.svg',

						),
						'center' => array(
							'value' => 'center',
							'label' => esc_html__( 'Center', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/align/center.svg',

						),
						'right'  => array(
							'value' => 'right',
							'label' => esc_html__( 'Right', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/align/right.svg',
						),
					),
				),

				'menu_items_gap'   => array(
					'id'      => 'menu_items_gap',
					'title'   => esc_html__( 'Items gap', 'xts-theme' ),
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
			),
		);
	}

}
