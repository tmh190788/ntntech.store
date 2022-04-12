<?php
/**
 * Basic structure element - compare
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
class Compare extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'compare';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'compare',
			'title'           => esc_html__( 'Compare', 'xts-theme' ),
			'text'            => esc_html__( 'Compare icon', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/compare.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				'style'       => array(
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

				'design'      => array(
					'id'       => 'design',
					'type'     => 'selector',
					'title'    => esc_html__( 'Icon design', 'xts-theme' ),
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'options'  => xts_get_available_options( 'compare_design_header_builder' ),
					'requires' => array(
						'style' => array(
							'comparison' => 'not_equal',
							'value'      => 'text',
						),
					),
					'value'    => 'default',
				),

				'icon_style'  => array(
					'id'       => 'icon_style',
					'title'    => esc_html__( 'Icon style', 'xts-theme' ),
					'type'     => 'selector',
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'value'    => 'default',
					'options'  => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/compare/icon/default.svg',
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

				'custom_icon' => array(
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
						'style'      => array(
							'comparison' => 'not_equal',
							'value'      => 'text',
						),
					),
				),
			),
		);
	}
}
