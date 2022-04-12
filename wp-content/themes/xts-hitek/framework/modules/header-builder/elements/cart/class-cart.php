<?php
/**
 * Basic structure element - cart
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
class Cart extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'cart';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'cart',
			'title'           => esc_html__( 'Cart', 'xts-theme' ),
			'text'            => esc_html__( 'Shopping widget', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/cart.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				'widget_type'  => array(
					'id'      => 'widget_type',
					'type'    => 'selector',
					'title'   => esc_html__( 'Widget type', 'xts-theme' ),
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'options' => xts_get_available_options( 'cart_widget_type_header_builder' ),
					'value'   => 'side',
				),

				'position'     => array(
					'id'       => 'position',
					'type'     => 'selector',
					'title'    => esc_html__( 'Position', 'xts-theme' ),
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'options'  => array(
						'left'  => array(
							'value' => 'left',
							'label' => esc_html__( 'Left', 'xts-theme' ),
						),
						'right' => array(
							'value' => 'right',
							'label' => esc_html__( 'Right', 'xts-theme' ),
						),
					),
					'requires' => array(
						'widget_type' => array(
							'comparison' => 'equal',
							'value'      => 'side',
						),
					),
					'value'    => 'right',
				),

				'color_scheme' => array(
					'id'       => 'color_scheme',
					'type'     => 'selector',
					'title'    => esc_html__( 'Color scheme', 'xts-theme' ),
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'value'    => 'dark',
					'options'  => array(
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
					'requires' => array(
						'widget_type' => array(
							'comparison' => 'not_equal',
							'value'      => 'without',
						),
					),
				),

				'style'        => array(
					'id'      => 'style',
					'title'   => esc_html__( 'Style', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'icon',
					'options' => array(
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
				),

				'design'       => array(
					'id'       => 'design',
					'type'     => 'selector',
					'title'    => esc_html__( 'Icon design', 'xts-theme' ),
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'options'  => xts_get_available_options( 'cart_design_header_builder' ),
					'requires' => array(
						'style' => array(
							'comparison' => 'not_equal',
							'value'      => 'text',
						),
					),
					'value'    => 'default',
				),

				'icon_style'   => array(
					'id'       => 'icon_style',
					'type'     => 'selector',
					'title'    => esc_html__( 'Icon', 'xts-theme' ),
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'options'  => array(
						'cart'   => array(
							'value' => 'cart',
							'label' => esc_html__( 'Cart', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/icon/cart.svg',
						),
						'bag'    => array(
							'value' => 'bag',
							'label' => esc_html__( 'Bag', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/icon/bag.svg',
						),
						'custom' => array(
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
					'value'    => 'cart',
				),

				'custom_icon'  => array(
					'id'       => 'custom_icon',
					'type'     => 'image',
					'title'    => esc_html__( 'Custom icon', 'xts-theme' ),
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'requires' => array(
						'icon_style' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
						'style'      => array(
							'comparison' => 'not_equal',
							'value'      => 'text',
						),
					),
					'value'    => '',
				),
			),
		);
	}
}
