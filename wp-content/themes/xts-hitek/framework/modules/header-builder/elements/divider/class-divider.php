<?php
/**
 * Simple vertical line
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
 * Simple vertical line
 */
class Divider extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'divider';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'divider',
			'title'           => esc_html__( 'Divider', 'xts-theme' ),
			'text'            => esc_html__( 'Simple vertical line', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/vertical-line.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				'direction'   => array(
					'id'          => 'direction',
					'type'        => 'selector',
					'title'       => esc_html__( 'Direction', 'xts-theme' ),
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'description' => esc_html__( 'The horizontal divider will break your column into two sub rows so you can build complex layouts with this option. NOTE: it will work in the general header row and will not in the top bar and bottom header builder.', 'xts-theme' ),
					'options'     => array(
						'v' => array(
							'value' => 'v',
							'label' => esc_html__( 'Vertical', 'xts-theme' ),
						),
						'h' => array(
							'value' => 'h',
							'label' => esc_html__( 'Horizontal', 'xts-theme' ),
						),
					),
					'value'       => 'v',
				),

				'full_height' => array(
					'id'          => 'full_height',
					'title'       => esc_html__( 'Full height', 'xts-theme' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => false,
					'description' => esc_html__( 'Mark this option if you want to show this divider line on the full height for this row.', 'xts-theme' ),
					'requires'    => array(
						'direction' => array(
							'comparison' => 'equal',
							'value'      => 'v',
						),
					),
				),

				'color'       => array(
					'id'    => 'color',
					'title' => esc_html__( 'Color', 'xts-theme' ),
					'tab'   => esc_html__( 'General', 'xts-theme' ),
					'type'  => 'color',
					'value' => '',
				),

				'css_class'   => array(
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
