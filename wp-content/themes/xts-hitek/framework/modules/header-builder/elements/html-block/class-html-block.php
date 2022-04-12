<?php
/**
 * HTML Block element
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * HTML Block element
 */
class HTML_Block extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'html-block';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {

		$options = $this->get_options();
		$first   = reset( $options );

		$this->args = array(
			'type'            => 'HTMLBlock',
			'title'           => esc_html__( 'HTML Block', 'xts-theme' ),
			'text'            => esc_html__( 'Elementor builder', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/html-block.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(
				'block_id' => array(
					'id'          => 'block_id',
					'title'       => esc_html__( 'HTML Block', 'xts-theme' ),
					'type'        => 'select',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => isset( $first['value'] ) ? $first['value'] : '',
					'options'     => $options,
					'description' => esc_html__( 'Choose which HTML Block to display in the header.', 'xts-theme' ),
				),
			),
		);
	}

	/**
	 * Get HTML Blocks options array.
	 *
	 * @since 1.0.0
	 */
	private function get_options() {
		$array        = array();
		$args         = array(
			'posts_per_page' => 250,
			'post_type'      => 'xts-html-block',
		);
		$blocks_posts = get_posts( $args );
		foreach ( $blocks_posts as $post ) {
			setup_postdata( $post );
			$array[ $post->ID ] = array(
				'label' => $post->post_title,
				'value' => $post->ID,
			);
		}
		wp_reset_postdata();
		return $array;
	}

}

