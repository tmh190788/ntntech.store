<?php
/**
 * Empty horizontal space element
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
 * Empty horizontal space element
 */
class Space extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'space';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'space',
			'title'           => esc_html__( 'Space', 'xts-theme' ),
			'text'            => esc_html__( 'Horizontal spacing', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/space.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(
				'direction' => array(
					'id'      => 'direction',
					'type'    => 'selector',
					'title'   => esc_html__( 'Direction', 'xts-theme' ),
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'options' => array(
						'h' => array(
							'value' => 'h',
							'label' => esc_html__( 'Horizontal', 'xts-theme' ),
						),
						'v' => array(
							'value' => 'v',
							'label' => esc_html__( 'Vertical', 'xts-theme' ),
						),
					),
					'value'   => 'h',
				),

				'width'     => array(
					'id'          => 'width',
					'title'       => esc_html__( 'Space width', 'xts-theme' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'from'        => 0,
					'to'          => 200,
					'value'       => 10,
					'units'       => 'px',
				),

				'css_class' => array(
					'id'          => 'css_class',
					'title'       => esc_html__( 'Additional CSS class', 'xts-theme' ),
					'type'        => 'text',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => '',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'xts-theme' ),
				),
			),
		);
	}
}
