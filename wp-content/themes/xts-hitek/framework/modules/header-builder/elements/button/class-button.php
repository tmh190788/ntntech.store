<?php
/**
 * Button element map
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
 * Button class
 */
class Button extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'button';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'button',
			'title'           => esc_html__( 'Button', 'xts-theme' ),
			'text'            => esc_html__( 'Button with link', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/button.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				'button_text'  => array(
					'id'    => 'button_text',
					'title' => esc_html__( 'Text', 'xts-theme' ),
					'type'  => 'text',
					'tab'   => esc_html__( 'General', 'xts-theme' ),
					'value' => 'Read more',
				),

				'button_link'  => array(
					'id'    => 'button_link',
					'title' => esc_html__( 'Link', 'xts-theme' ),
					'type'  => 'text',
					'tab'   => esc_html__( 'General', 'xts-theme' ),
					'value' => '#',
				),

				'button_size'  => array(
					'id'      => 'button_size',
					'title'   => esc_html__( 'Size', 'xts-theme' ),
					'type'    => 'select',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'm',
					'options' => array(
						'xs' => array(
							'value' => 'xs',
							'label' => esc_html__( 'Extra small', 'xts-theme' ),
						),
						's'  => array(
							'value' => 's',
							'label' => esc_html__( 'Small', 'xts-theme' ),
						),
						'm'  => array(
							'value' => 'm',
							'label' => esc_html__( 'Medium', 'xts-theme' ),
						),
						'l'  => array(
							'value' => 'l',
							'label' => esc_html__( 'Large', 'xts-theme' ),
						),
						'xl' => array(
							'value' => 'xl',
							'label' => esc_html__( 'Extra large', 'xts-theme' ),
						),
					),
				),

				'button_color' => array(
					'id'      => 'button_color',
					'title'   => esc_html__( 'Color', 'xts-theme' ),
					'type'    => 'select',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'primary',
					'options' => array(
						'default'   => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
						),
						'primary'   => array(
							'value' => 'primary',
							'label' => esc_html__( 'Primary', 'xts-theme' ),
						),
						'secondary' => array(
							'value' => 'secondary',
							'label' => esc_html__( 'Secondary', 'xts-theme' ),
						),
						'white'     => array(
							'value' => 'white',
							'label' => esc_html__( 'White', 'xts-theme' ),
						),
					),
				),

				'button_style' => array(
					'id'      => 'button_style',
					'title'   => esc_html__( 'Style', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'default',
					'options' => xts_get_available_options( 'button_style_header_builder' ),
				),

				'button_shape' => array(
					'id'      => 'button_shape',
					'title'   => esc_html__( 'Shape', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'rounded',
					'options' => array(
						'rectangle' => array(
							'value' => 'rectangle',
							'label' => esc_html__( 'Rectangle', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/button/shape/rectangle.svg',
						),
						'rounded'   => array(
							'value' => 'rounded',
							'label' => esc_html__( 'Rounded', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/button/shape/rounded.svg',
						),
						'round'     => array(
							'value' => 'round',
							'label' => esc_html__( 'Round', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/button/shape/round.svg',
						),
					),
				),

			),
		);
	}

}
