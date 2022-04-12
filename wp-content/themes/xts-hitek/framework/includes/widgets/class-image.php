<?php
/**
 * Image widget class.
 *
 * @package xts
 */

namespace XTS\Widget;

use XTS\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Image widget class.
 */
class Image extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( '[XTemos] Image or SVG', 'xts-theme' ),
			'description' => esc_html__( 'Display a single image or SVG graphic.', 'xts-theme' ),
			'slug'        => 'xts-widget-image',
			'fields'      => array(
				array(
					'id'   => 'title',
					'type' => 'text',
					'name' => esc_html__( 'Title', 'xts-theme' ),
				),

				array(
					'id'   => 'link',
					'type' => 'text',
					'name' => esc_html__( 'Link', 'xts-theme' ),
				),

				array(
					'id'      => 'is_external',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Open in new window', 'xts-theme' ),
					'default' => false,
				),

				array(
					'id'      => 'nofollow',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Add nofollow', 'xts-theme' ),
					'default' => false,
				),

				array(
					'id'   => 'image',
					'type' => 'attach_image',
					'name' => esc_html__( 'Image', 'xts-theme' ),
				),

				array(
					'id'      => 'image_size',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Image size', 'xts-theme' ),
					'fields'  => xts_get_all_image_sizes_names( 'widget' ),
					'default' => 'large',
				),

				array(
					'id'      => 'align',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Alignment', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Inherit', 'xts-theme' ) => 'inherit',
						esc_html__( 'Left', 'xts-theme' )  => 'left',
						esc_html__( 'Center', 'xts-theme' ) => 'center',
						esc_html__( 'Right', 'xts-theme' ) => 'right',
					),
					'default' => 'center',
				),

				array(
					'id'          => 'width',
					'type'        => 'number',
					'name'        => esc_html__( 'Width', 'xts-theme' ),
					'description' => esc_html__( 'Only for custom image size', 'xts-theme' ),
				),

				array(
					'id'          => 'height',
					'type'        => 'number',
					'name'        => esc_html__( 'Height', 'xts-theme' ),
					'description' => esc_html__( 'Only for custom image size', 'xts-theme' ),
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
			'title'       => '',
			'align'       => 'center',
			'link'        => '',
			'is_external' => false,
			'nofollow'    => false,
			'image_size'  => 'large',
			'width'       => '',
			'height'      => '',
		);

		$instance = wp_parse_args( $instance, $default_args );

		$element_args = array(
			'click_action'           => 'custom_link',
			'align'                  => $instance['align'],
			'custom_link'            => array(
				'url'         => $instance['link'],
				'is_external' => $instance['is_external'] ? 'on' : 'off',
				'nofollow'    => $instance['nofollow'] ? 'on' : 'off',
			),
			'image_size'             => $instance['image_size'],
			'image_custom_dimension' => array(
				'width'  => $instance['width'],
				'height' => $instance['height'],
			),
			'image'                  => array(
				'id' => $instance['image'],
			),
		);

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo wp_kses( $args['before_title'], 'xts_widget' ) . $instance['title'] . wp_kses( $args['after_title'], 'xts_widget' ); // phpcs:ignore
		}

		xts_image_template( $element_args );

		echo wp_kses( $args['after_widget'], 'xts_widget' );
	}
}
