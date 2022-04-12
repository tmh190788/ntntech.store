<?php
/**
 * Abstract class for all elements used in the builder. This class is used both on backend and
 * on the frontend.
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Abstract class for all elements used in the builder.
 */
abstract class Element {

	/**
	 * Element arguments.
	 *
	 * @var object
	 */
	public $args = array();

	/**
	 * Template file name.
	 *
	 * @var object
	 */
	public $template_name;

	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->map();
	}

	/**
	 * Element arguments getter.
	 *
	 * @since 1.0.0
	 */
	public function get_args() {
		return $this->args;
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {}

	/**
	 * Render element's template file.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $el       Element.
	 * @param string $children Child elements.
	 */
	public function render( $el, $children = '' ) {
		$args = $this->parse_args( $el );

		extract( $args ); // phpcs:ignore

		$path = '/templates/header/' . $this->template_name . '.php';

		$located = '';

		if ( file_exists( get_stylesheet_directory() . $path ) ) {
			$located = get_stylesheet_directory() . $path;
		} elseif ( file_exists( get_template_directory() . $path ) ) {
			$located = get_template_directory() . $path;
		}

		if ( file_exists( $located ) ) {
			require $located;
		}
	}

	/**
	 * Parse element arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param array $el Element.
	 *
	 * @return array
	 */
	private function parse_args( $el ) {
		$a = array();

		foreach ( $el['params'] as $arg ) {
			$a[ $arg['id'] ] = $arg['value'];
		}

		unset( $el['content'] );

		$el['params'] = $a;

		return $el;
	}

	/**
	 * Does this element has a background.
	 *
	 * @since 1.0.0
	 *
	 * @param array $params Parameters data.
	 *
	 * @return bool
	 */
	public function has_background( $params ) {
		return( isset( $params['background'] ) && ( isset( $params['background']['background-color'] ) || isset( $params['background']['background-image'] ) ) );
	}
}
