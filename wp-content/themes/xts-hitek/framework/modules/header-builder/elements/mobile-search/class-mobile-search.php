<?php
/**
 * Search icon for mobile devices
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
 * Search icon for mobile devices
 */
class Mobile_Search extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'mobile-search';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'mobilesearch',
			'title'           => esc_html__( 'Search', 'xts-theme' ),
			'text'            => esc_html__( 'Search form', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/search.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'mobile'          => true,
			'params'          => array(

				'style'       => array(
					'id'          => 'style',
					'title'       => esc_html__( 'Style', 'xts-theme' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => 'icon',
					'options'     => xts_get_available_options( 'search_icon_style_header_builder' ),
					'description' => esc_html__( 'You can change the search icon style.', 'xts-theme' ),
				),

				'icon_type'   => array(
					'id'      => 'icon_type',
					'title'   => esc_html__( 'Icon type', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'default',
					'options' => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/search/icon/default.svg',
						),
						'custom'  => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/custom-icon.svg',
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
						'icon_type' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
				),
			),
		);
	}

}
