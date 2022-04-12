<?php
/**
 * AJAX search template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}


if ( ! function_exists( 'xts_ajax_search_template' ) ) {
	/**
	 * AJAX search template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_ajax_search_template( $element_args ) {
		$default_args = array(
			'count'             => array(
				'size' => 3,
			),
			'post_type'         => 'post',
			'ajax'              => 'no',
			'thumbnail'         => 'no',
			'price'             => 'no',
			'categories'        => 'no',
			'form_color_scheme' => 'inherit',
			'search_style'      => 'default',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes = '';

		if ( 'inherit' !== $element_args['form_color_scheme'] ) {
			$wrapper_classes .= ' xts-scheme-' . $element_args['form_color_scheme'] . '-form';
		}

		xts_search_form(
			array(
				'count'               => $element_args['count']['size'],
				'post_type'           => $element_args['post_type'],
				'categories_dropdown' => $element_args['categories'],
				'search_style'        => $element_args['search_style'],
				'wrapper_classes'     => $wrapper_classes,
				'ajax'                => 'yes' === $element_args['ajax'],
				'thumbnail'           => 'yes' === $element_args['thumbnail'],
				'price'               => 'yes' === $element_args['price'],
			)
		);
	}
}
