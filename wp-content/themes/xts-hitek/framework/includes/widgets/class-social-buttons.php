<?php
/**
 * Social buttons widget class.
 *
 * @package xts
 */

namespace XTS\Widget;

use XTS\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Social buttons widget class.
 */
class Social_Buttons extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( '[XTemos] Social buttons', 'xts-theme' ),
			'description' => esc_html__( 'Share and follow buttons element.', 'xts-theme' ),
			'slug'        => 'xts-widget-social-buttons',
			'fields'      => array(
				array(
					'id'   => 'title',
					'type' => 'text',
					'name' => esc_html__( 'Title', 'xts-theme' ),
				),

				array(
					'id'      => 'type',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Type', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Share', 'xts-theme' ) => 'share',
						esc_html__( 'Follow', 'xts-theme' ) => 'follow',
					),
					'default' => 'share',
				),

				array(
					'id'      => 'color_scheme',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Color sheme', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Dark', 'xts-theme' )  => 'dark',
						esc_html__( 'Light', 'xts-theme' ) => 'light',
					),
					'default' => 'dark',
				),

				array(
					'id'      => 'align',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Alignment', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Left', 'xts-theme' )  => 'left',
						esc_html__( 'Center', 'xts-theme' ) => 'center',
						esc_html__( 'Right', 'xts-theme' ) => 'right',
					),
					'default' => 'center',
				),

				array(
					'id'      => 'size',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Size', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Small', 'xts-theme' ) => 's',
						esc_html__( 'Medium', 'xts-theme' ) => 'm',
						esc_html__( 'Large', 'xts-theme' ) => 'l',
					),
					'default' => 'm',
				),

				array(
					'id'      => 'style',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Style', 'xts-theme' ),
					'fields'  => xts_get_available_options( 'social_buttons_style_widget' ),
					'default' => 'default',
				),

				array(
					'id'      => 'shape',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Shape', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Round', 'xts-theme' ) => 'round',
						esc_html__( 'Rounded', 'xts-theme' ) => 'rounded',
						esc_html__( 'Square', 'xts-theme' ) => 'square',
					),
					'default' => 'round',
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
			'title' => '',
		);

		$instance = wp_parse_args( $instance, $default_args );

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo wp_kses( $args['before_title'], 'xts_widget' ) . $instance['title'] . wp_kses( $args['after_title'], 'xts_widget' ); // phpcs:ignore
		}

		xts_social_buttons_template( $instance );

		echo wp_kses( $args['after_widget'], 'xts_widget' );
	}
}
