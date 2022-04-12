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
class Search extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$post_types = array(
			esc_html__( 'Post', 'xts-theme' )      => 'post',
			esc_html__( 'Portfolio', 'xts-theme' ) => 'xts_portfolio',
		);

		if ( xts_is_woocommerce_installed() ) {
			$post_types[ esc_html__( 'Product', 'xts-theme' ) ] = 'product';
		}

		$args = array(
			'label'       => esc_html__( '[XTemos] AJAX Search', 'xts-theme' ),
			'description' => esc_html__( 'Search form with AJAX', 'xts-theme' ),
			'slug'        => 'xts-widget-ajax-search',
			'fields'      => array(
				array(
					'id'      => 'title',
					'type'    => 'text',
					'name'    => esc_html__( 'Title', 'xts-theme' ),
					'default' => 'Search',
				),

				array(
					'id'   => 'count',
					'type' => 'number',
					'name' => esc_html__( 'Number of items to show', 'xts-theme' ),
				),

				array(
					'id'      => 'post_type',
					'type'    => 'dropdown',
					'default' => 'post',
					'name'    => esc_html__( 'Search post type', 'xts-theme' ),
					'fields'  => $post_types,
				),

				array(
					'id'      => 'search_style',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Style', 'xts-theme' ),
					'fields'  => xts_get_available_options( 'search_style_widget' ),
					'default' => 'default',
				),

				array(
					'id'      => 'form_color_scheme',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Form color scheme', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Inherit', 'xts-theme' ) => 'inherit',
						esc_html__( 'Dark', 'xts-theme' )  => 'dark',
						esc_html__( 'Light', 'xts-theme' ) => 'light',
					),
					'default' => 'inherit',
				),

				array(
					'id'   => 'thumbnail',
					'type' => 'checkbox',
					'name' => esc_html__( 'Show thumbnail', 'xts-theme' ),
				),
			),
		);

		if ( xts_is_woocommerce_installed() ) {
			$args['fields'][] = array(
				'id'   => 'price',
				'type' => 'checkbox',
				'name' => esc_html__( 'Show price', 'xts-theme' ),
			);

			$args['fields'][] = array(
				'id'   => 'categories',
				'type' => 'checkbox',
				'name' => esc_html__( 'Show categories', 'xts-theme' ),
			);
		}

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
			'title'             => '',
			'count'             => 3,
			'thumbnail'         => false,
			'price'             => false,
			'post_type'         => 'post',
			'categories'        => false,
			'search_style'      => 'default',
			'form_color_scheme' => 'inherit',
		);

		$instance = wp_parse_args( $instance, $default_args );

		$wrapper_classes = '';

		if ( 'inherit' !== $instance['form_color_scheme'] ) {
			$wrapper_classes .= ' xts-scheme-' . $instance['form_color_scheme'] . '-form';
		}

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo wp_kses( $args['before_title'], 'xts_widget' ) . $instance['title'] . wp_kses( $args['after_title'], 'xts_widget' ); // phpcs:ignore
		}

		xts_search_form(
			array(
				'ajax'                => true,
				'count'               => $instance['count'],
				'thumbnail'           => $instance['thumbnail'] ? 'yes' : 'no',
				'categories_dropdown' => $instance['categories'] ? 'yes' : 'no',
				'post_type'           => $instance['post_type'],
				'price'               => $instance['price'] ? 'yes' : 'no',
				'search_style'        => $instance['search_style'],
				'wrapper_classes'     => $wrapper_classes,
			)
		);

		echo wp_kses( $args['after_widget'], 'xts_widget' );
	}
}
