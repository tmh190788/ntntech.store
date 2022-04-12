<?php
/**
 * Logo image with uploader
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
 * Logo image with uploader class
 */
class Logo extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'logo';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'logo',
			'title'           => esc_html__( 'Logo', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/logo.svg',
			'text'            => esc_html__( 'Website logo', 'xts-theme' ),
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(
				'image'        => array(
					'id'          => 'image',
					'title'       => esc_html__( 'Logo image', 'xts-theme' ),
					'type'        => 'image',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => '',
					'description' => '',
				),

				'width'        => array(
					'id'          => 'width',
					'title'       => esc_html__( 'Logo width', 'xts-theme' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'from'        => 10,
					'to'          => 500,
					'value'       => 150,
					'units'       => 'px',
					'description' => esc_html__( 'Determine the logo image width in pixels.', 'xts-theme' ),
				),

				'sticky_image' => array(
					'id'          => 'sticky_image',
					'title'       => esc_html__( 'Logo image for sticky header', 'xts-theme' ),
					'description' => esc_html__( 'Use this option only in case when you need different logo images for your header and for the sticky header.', 'xts-theme' ),
					'type'        => 'image',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => '',
				),

				'sticky_width' => array(
					'id'          => 'sticky_width',
					'title'       => esc_html__( 'Sticky header logo width', 'xts-theme' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'from'        => 10,
					'to'          => 500,
					'value'       => 150,
					'units'       => 'px',
					'description' => esc_html__( 'Determine the logo on the sticky header image width in pixels.', 'xts-theme' ),
				),

				'logo_notice'  => array(
					'id'    => 'logo_notice',
					'type'  => 'notice',
					'value' => '',
					'style' => 'info',
					'tab'   => esc_html__( 'General', 'xts-theme' ),
					'text'  => esc_html__( 'NOTE: the logo element is different for desktop and mobile devices. That means that you need to upload your images for desktop and mobile devices separately. But you can use the same image for both elements if you do not want to make them look different.', 'xts-theme' ),
				),
			),
		);
	}
}
