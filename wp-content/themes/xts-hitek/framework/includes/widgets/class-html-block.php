<?php
/**
 * Widget_Search class.
 *
 * @package xts
 */

namespace XTS\Widget;

use XTS\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * AJAX search widget
 */
class Html_Block extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( '[XTemos] HTML Block', 'xts-theme' ),
			'description' => esc_html__( 'Display HTML Block', 'xts-theme' ),
			'slug'        => 'xts-widget-html-block',
			'fields'      => array(
				array(
					'id'      => 'id',
					'type'    => 'dropdown',
					'heading' => esc_html__( 'Select block', 'xts-theme' ),
					'value'   => xts_get_html_blocks_array( 'widget' ),
					'default' => '',
				),
			),
		);

		$this->create_widget( $args );
	}

	/**
	 * Output widget.
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		echo wp_kses( $args['before_widget'], 'xts_widget' );

		$default_args = array(
			'id' => '',
		);

		$instance = wp_parse_args( $instance, $default_args );

		echo xts_get_html_block_content( $instance['id'] ); // phpcs:ignore

		echo wp_kses( $args['after_widget'], 'xts_widget' );
	}
}
