<?php
/**
 * Instagram class.
 *
 * @package xts
 */

namespace XTS\Widget;

use XTS\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Instagram widget
 */
class Instagram extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( '[XTemos] Instagram', 'xts-theme' ),
			'description' => esc_html__( 'Instagram photos', 'xts-theme' ),
			'slug'        => 'xts-widget-instagram',
			'fields'      => array(
				array(
					'id'      => 'title',
					'type'    => 'text',
					'name'    => esc_html__( 'Title', 'xts-theme' ),
					'default' => 'Instagram',
				),

				array(
					'id'      => 'source',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Source', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'API', 'xts-theme' ) => 'api',
						esc_html__( 'Custom images', 'xts-theme' ) => 'custom_images',
					),
					'default' => 'custom_images',
				),

				array(
					'id'      => 'custom_images_size',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Image size', 'xts-theme' ),
					'fields'  => xts_get_all_image_sizes_names( 'widget' ),
					'default' => 'thumbnail',
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

				/**
				 * Custom images settings
				 */
				array(
					'id'   => 'custom_images',
					'type' => 'attach_images',
					'name' => esc_html__( 'Custom images', 'xts-theme' ),
				),

				array(
					'id'   => 'custom_images_link',
					'type' => 'text',
					'name' => esc_html__( 'Link to profile', 'xts-theme' ),
				),

				/**
				 * API settings
				 */
				array(
					'id'   => 'api_title',
					'type' => 'title',
					'name' => esc_html__( 'API', 'xts-theme' ),
				),

				array(
					'id'      => 'api_images_per_page',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Images per page', 'xts-theme' ),
					'fields'  => array(
						'9'  => '9',
						'12' => '12',
						'11' => '11',
						'10' => '10',
						'8'  => '8',
						'7'  => '7',
						'6'  => '6',
						'5'  => '5',
						'4'  => '4',
						'3'  => '3',
						'2'  => '2',
						'1'  => '1',
					),
					'default' => '9',
				),

				/**
				 * Other settings
				 */
				array(
					'id'   => 'other_title',
					'type' => 'title',
					'name' => esc_html__( 'Other setting', 'xts-theme' ),
				),

				array(
					'id'      => 'link_is_external',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Open in new window', 'xts-theme' ),
					'default' => false,
				),

				array(
					'id'      => 'link_nofollow',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Add nofollow', 'xts-theme' ),
					'default' => false,
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

		$ids           = explode( ',', $instance['custom_images'] );
		$custom_images = array();

		foreach ( $ids as $id ) {
			$custom_images[] = array(
				'id' => $id,
			);
		}

		$default_args = array(
			'title'               => '',
			'api_images_per_page' => 9,
			'source'              => 'ajax',
			'link_is_external'    => false,
			'link_nofollow'       => false,

			// Custom images.
			'custom_images_size'  => 'thumbnail',
			'custom_images_link'  => '',
		);

		$instance = wp_parse_args( $instance, $default_args );

		$element_args = array(
			'source'              => $instance['source'],
			'link_is_external'    => $instance['link_is_external'] ? 'yes' : 'no',
			'link_nofollow'       => $instance['link_nofollow'] ? 'yes' : 'no',
			'api_images_per_page' => array(
				'size' => $instance['api_images_per_page'],
			),

			// Custom images.
			'custom_images'       => $custom_images,
			'custom_images_size'  => $instance['custom_images_size'],
			'custom_images_link'  => $instance['custom_images_link'],

			'show_meta'           => 'no',
			'columns'             => array( 'size' => 3 ),
			'columns_tablet'      => array( 'size' => 3 ),
			'columns_mobile'      => array( 'size' => 3 ),
			'spacing'             => '10',
		);

		if ( isset( $instance['width'] ) && $instance['width'] && isset( $instance['height'] ) && $instance['height'] ) {
			$element_args['custom_images_custom_dimension'] = array(
				'width'  => $instance['width'],
				'height' => $instance['height'],
			);
		}

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo wp_kses( $args['before_title'], 'xts_widget' ) . $instance['title'] . wp_kses( $args['after_title'], 'xts_widget' ); // phpcs:ignore
		}

		xts_instagram_template( $element_args );

		echo wp_kses( $args['after_widget'], 'xts_widget' );
	}
}
