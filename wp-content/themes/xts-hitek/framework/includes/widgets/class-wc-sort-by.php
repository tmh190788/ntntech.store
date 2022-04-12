<?php
/**
 * Sort by class.
 *
 * @package xts
 */

namespace XTS\Widget;

use XTS\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Sort by widget
 */
class WC_Sort_By extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( '[XTemos] Sort by', 'xts-theme' ),
			'description' => esc_html__( 'Sort products by name, price, popularity etc.', 'xts-theme' ),
			'slug'        => 'xts-widget-sort-by',
			'fields'      => array(
				array(
					'id'      => 'title',
					'type'    => 'text',
					'name'    => esc_html__( 'Title', 'xts-theme' ),
					'default' => 'Sort by',
				),

				array(
					'id'      => 'style',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Style', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Dropdown', 'xts-theme' ) => 'dropdown',
						esc_html__( 'List', 'xts-theme' ) => 'list',
					),
					'default' => 'dropdown',
				),
			),
		);

		$this->create_widget( $args );
	}

	/**
	 * Output widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		$default_args = array(
			'title' => 'Sort by',
			'style' => 'dropdown',
		);

		$instance = wp_parse_args( $instance, $default_args );

		if ( ! woocommerce_products_will_display() ) {
			return;
		}

		$orderby                 = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) ); // phpcs:ignore
		$show_default_orderby = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		$options              = apply_filters(
			'woocommerce_catalog_orderby',
			array(
				'menu_order' => esc_html__( 'Default', 'xts-theme' ),
				'popularity' => esc_html__( 'Popularity', 'xts-theme' ),
				'rating'     => esc_html__( 'Average rating', 'xts-theme' ),
				'date'       => esc_html__( 'Newness', 'xts-theme' ),
				'price'      => esc_html__( 'Price: low to high', 'xts-theme' ),
				'price-desc' => esc_html__( 'Price: high to low', 'xts-theme' ),
			)
		);

		if ( ! $show_default_orderby ) {
			unset( $options['menu_order'] );
		}

		if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
			unset( $options['rating'] );
		}

		echo wp_kses( $args['before_widget'], 'xts_widget' );

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo wp_kses( $args['before_title'], 'xts_widget' ) . $instance['title'] . wp_kses( $args['after_title'], 'xts_widget' ); // phpcs:ignore
		}

		wc_get_template(
			'loop/orderby.php',
			array(
				'catalog_orderby_options' => $options,
				'orderby'                 => $orderby,
				'show_default_orderby'    => $show_default_orderby,
				'style'                   => $instance['style'],
			)
		);

		echo wp_kses( $args['after_widget'], 'xts_widget' );
	}
}
