<?php
/**
 * Frontend class that initiallize current header for the page and generates its structure HTML + CSS
 *
 * @package xts
 */

namespace XTS\Header_Builder;

use XTS\Styles_Storage;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Frontend class that initialize current header for the page and generates its structure HTML + CSS
 */
class Frontend {
	/**
	 * Main header builder class object.
	 *
	 * @var object
	 */
	public $builder = null;

	/**
	 * Elements classes map.
	 *
	 * @var array
	 */
	private $element_classes = array();

	/**
	 * Current header structure array.
	 *
	 * @var array
	 */
	private $structure = array();

	/**
	 * Current header object.
	 *
	 * @var object
	 */
	public $header = null;

	/**
	 * Set up all properties
	 *
	 * @var Styles_Storage
	 */
	public $storage;

	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 *
	 * @param object $builder Main header builder class object.
	 */
	public function __construct( $builder ) {
		$this->builder = $builder;

		$this->hooks();
	}

	/**
	 * Register action hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'wp_print_styles', array( $this, 'get_elements' ), 200 );
		add_action( 'wp', array( $this, 'print_header_styles' ) );
	}

	/**
	 * Load elements classes list.
	 *
	 * @since 1.0.0
	 */
	public function get_elements() {
		$id              = $this->get_current_id();
		$this->header    = $this->builder->factory->get_header( $id );
		$this->structure = $this->header->get_structure();

		$this->element_classes = $this->builder->elements->get_all();
	}

	/**
	 * Load elements classes list.
	 *
	 * @since 1.0.0
	 */
	public function print_header_styles() {
		$id            = $this->get_current_id();
		$this->header  = $this->builder->factory->get_header( $id );
		$styles        = new Styles();
		$this->storage = new Styles_Storage( $this->get_current_id(), 'option', '', false );

		if ( ! $this->storage->is_css_exists() ) {
			$this->storage->write( $styles->get_all_css( $this->header->get_structure(), $this->header->get_options() ), true );
		}

		$this->storage->print_styles();
	}

	/**
	 * Get current header ID based on global options and page metabox.
	 *
	 * @since 1.0.0
	 */
	public function get_current_id() {
		$id                      = $this->builder->manager->get_default_header();
		$page_id                 = xts_get_page_id();
		$default_header          = xts_get_opt( 'default_header' );
		$custom_post_header      = xts_get_opt( 'blog_single_header' );
		$custom_portfolio_header = xts_get_opt( 'portfolio_single_header' );
		$custom_product_header   = xts_get_opt( 'single_product_header' );
		$custom                  = get_post_meta( $page_id, '_xts_page_custom_header', true );

		if ( $default_header ) {
			$id = $default_header;
		}

		if ( ! empty( $custom_post_header ) && 'none' !== $custom_post_header && is_singular( 'post' ) ) {
			$id = $custom_post_header;
		}

		if ( ! empty( $custom_portfolio_header ) && 'none' !== $custom_portfolio_header && is_singular( 'xts-portfolio' ) ) {
			$id = $custom_portfolio_header;
		}

		if ( ! empty( $custom_product_header ) && 'none' !== $custom_product_header && is_singular( 'product' ) ) {
			$id = $custom_product_header;
		}

		if ( ! empty( $custom ) && 'none' !== $custom ) {
			$id = $custom;
		}

		return $id;
	}

	/**
	 * Generate the header based on its structure.
	 *
	 * @since 1.0.0
	 */
	public function generate_header() {
		$this->render_element( $this->structure );
		do_action( 'xts_after_generate_header' );
	}

	/**
	 * Render particular structure element.
	 *
	 * @since 1.0.0
	 *
	 * @param array $el Elements parameters.
	 */
	private function render_element( $el ) {
		$children = '';
		$type     = $el['type'];

		if ( ! isset( $el['params'] ) ) {
			$el['params'] = array();
		}

		if ( isset( $el['content'] ) && is_array( $el['content'] ) ) {
			ob_start();

			foreach ( $el['content'] as $element ) {
				$this->render_element( $element );
			}

			$children = ob_get_clean();
		}

		if ( 'row' === $type && $this->is_empty_row( $el ) || 'column' === $type && $this->is_empty_column( $el ) ) {
			$children = false;
		}

		if ( isset( $this->element_classes[ $type ] ) ) {
			$obj = $this->element_classes[ $type ];
			$obj->render( $el, $children );
		}
	}

	/**
	 * Check if row contains some elements.
	 *
	 * @since 1.0.0
	 *
	 * @param array $el Elements parameters.
	 *
	 * @return bool
	 */
	private function is_empty_row( $el ) {
		$is_empty = true;

		foreach ( $el['content'] as $key => $column ) {
			if ( ! $this->is_empty_column( $column ) ) {
				$is_empty = false;
			}
		}

		return $is_empty;
	}

	/**
	 * Check if column contains some elements.
	 *
	 * @since 1.0.0
	 *
	 * @param array $el Elements parameters.
	 *
	 * @return bool
	 */
	private function is_empty_column( $el ) {
		return empty( $el['content'] );
	}
}
