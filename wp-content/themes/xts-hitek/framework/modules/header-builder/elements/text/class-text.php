<?php
/**
 * Simple texteara
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
 * Simple textarea.
 */
class Text extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'text';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'text',
			'title'           => esc_html__( 'Text/HTML', 'xts-theme' ),
			'text'            => esc_html__( 'Plain text/HTML', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/text-html.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				'content'      => array(
					'id'          => 'content',
					'title'       => esc_html__( 'Text/HTML content', 'xts-theme' ),
					'type'        => 'editor',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => '',
					'description' => esc_html__( 'Place your text or HTML code with WordPress shortcodes.', 'xts-theme' ),
				),

				'inline'       => array(
					'id'          => 'inline',
					'title'       => esc_html__( 'Display inline', 'xts-theme' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => false,
					'description' => esc_html__( 'The width of the element will depend on its content', 'xts-theme' ),
				),

				'color_scheme' => array(
					'id'      => 'color_scheme',
					'title'   => esc_html__( 'Color scheme', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'Styles', 'xts-theme' ),
					'value'   => 'inherit',
					'options' => array(
						'inherit' => array(
							'value' => 'inherit',
							'label' => esc_html__( 'Inherit', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/color/inherit.svg',
						),
						'dark'    => array(
							'value' => 'dark',
							'label' => esc_html__( 'Dark', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/color/dark.svg',
						),
						'light'   => array(
							'value' => 'light',
							'label' => esc_html__( 'Light', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/color/light.svg',
						),
					),
				),

				'text_color'   => array(
					'id'    => 'text_color',
					'title' => esc_html__( 'Color', 'xts-theme' ),
					'tab'   => esc_html__( 'Styles', 'xts-theme' ),
					'type'  => 'color',
					'value' => '',
				),

				'font_size'    => array(
					'id'      => 'font_size',
					'title'   => esc_html__( 'Font size', 'xts-theme' ),
					'type'    => 'select',
					'tab'     => esc_html__( 'Styles', 'xts-theme' ),
					'value'   => 'default',
					'options' => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
						),
						'xs'      => array(
							'value' => 'xs',
							'label' => esc_html__( 'Extra small', 'xts-theme' ),
						),
						's'       => array(
							'value' => 's',
							'label' => esc_html__( 'Small', 'xts-theme' ),
						),
						'm'       => array(
							'value' => 'm',
							'label' => esc_html__( 'Medium', 'xts-theme' ),
						),
						'l'       => array(
							'value' => 'l',
							'label' => esc_html__( 'Large', 'xts-theme' ),
						),
					),
				),

				'css_class'    => array(
					'id'          => 'css_class',
					'title'       => esc_html__( 'Additional CSS class', 'xts-theme' ),
					'type'        => 'text',
					'tab'         => esc_html__( 'Styles', 'xts-theme' ),
					'value'       => '',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'xts-theme' ),
				),
			),
		);
	}

}

