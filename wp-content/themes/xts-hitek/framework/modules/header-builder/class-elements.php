<?php
/**
 * Include all elements classes and create their objects. AJAX handlers.
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\AJAX_Response;

/**
 * Include all elements classes and create their objects. AJAX handlers.
 */
class Elements {

	/**
	 * Elements classes data.
	 *
	 * @var array
	 */
	public $elements_data = array(
		'my-account'     => array(
			'class_name'   => 'XTS\Header_Builder\My_Account',
			'element_name' => 'my-account',
		),
		'button'         => array(
			'class_name'   => 'XTS\Header_Builder\Button',
			'element_name' => 'button',
		),
		'cart'           => array(
			'class_name'   => 'XTS\Header_Builder\Cart',
			'element_name' => 'cart',
		),
		'categories'     => array(
			'class_name'   => 'XTS\Header_Builder\Categories',
			'element_name' => 'categories',
		),
		'column'         => array(
			'class_name'   => 'XTS\Header_Builder\Column',
			'element_name' => 'column',
		),
		'compare'        => array(
			'class_name'   => 'XTS\Header_Builder\Compare',
			'element_name' => 'compare',
		),
		'divider'        => array(
			'class_name'   => 'XTS\Header_Builder\Divider',
			'element_name' => 'divider',
		),
		'html-block'     => array(
			'class_name'   => 'XTS\Header_Builder\HTML_Block',
			'element_name' => 'HTMLBlock',
		),
		'infobox'        => array(
			'class_name'   => 'XTS\Header_Builder\Infobox',
			'element_name' => 'infobox',
		),
		'logo'           => array(
			'class_name'   => 'XTS\Header_Builder\Logo',
			'element_name' => 'logo',
		),
		'burger'         => array(
			'class_name'   => 'XTS\Header_Builder\Burger',
			'element_name' => 'burger',
		),
		'main-menu'      => array(
			'class_name'   => 'XTS\Header_Builder\Main_Menu',
			'element_name' => 'mainmenu',
		),
		'menu'           => array(
			'class_name'   => 'XTS\Header_Builder\Menu',
			'element_name' => 'menu',
		),
		'mobile-search'  => array(
			'class_name'   => 'XTS\Header_Builder\Mobile_Search',
			'element_name' => 'mobilesearch',
		),
		'root'           => array(
			'class_name'   => 'XTS\Header_Builder\Root',
			'element_name' => 'root',
		),
		'row'            => array(
			'class_name'   => 'XTS\Header_Builder\Row',
			'element_name' => 'row',
		),
		'search'         => array(
			'class_name'   => 'XTS\Header_Builder\Search',
			'element_name' => 'search',
		),
		'social-buttons' => array(
			'class_name'   => 'XTS\Header_Builder\Social_Buttons',
			'element_name' => 'social_buttons',
		),
		'space'          => array(
			'class_name'   => 'XTS\Header_Builder\Space',
			'element_name' => 'space',
		),
		'text'           => array(
			'class_name'   => 'XTS\Header_Builder\Text',
			'element_name' => 'text',
		),
		'wishlist'       => array(
			'class_name'   => 'XTS\Header_Builder\Wishlist',
			'element_name' => 'wishlist',
		),
	);

	/**
	 * Elements classes.
	 *
	 * @var array
	 */
	public $elements = array();

	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Register action hooks.
	 *
	 * @since 1.0.0
	 */
	private function hooks() {
		add_action( 'init', array( $this, 'ajax_actions' ) );
		add_action( 'init', array( $this, 'include_files' ) );
	}

	/**
	 * Register AJAX actions hooks.
	 *
	 * @since 1.0.0
	 */
	public function ajax_actions() {
		add_action( 'wp_ajax_xts_get_builder_elements', array( $this, 'get_elements_ajax' ) );
		add_action( 'wp_ajax_xts_get_builder_element', array( $this, 'get_element_ajax' ) );
	}

	/**
	 * Include files.
	 *
	 * @since 1.0.0
	 */
	public function include_files() {
		foreach ( $this->elements_data as $dir_name => $data ) {
			$path = XTS_HB_TEMPLATES . $dir_name . '/class-' . $dir_name . '.php';

			if ( file_exists( $path ) ) {
				require_once $path;
				$this->elements[ $data['element_name'] ] = new $data['class_name']();
			}
		}
	}
	/**
	 * Elements getter.
	 *
	 * @since 1.0.0
	 */
	public function get_all() {
		return $this->elements;
	}

	/**
	 * Get elements data with AJAX>
	 *
	 * @since 1.0.0
	 */
	public function get_elements_ajax() {
		$elements = array();

		foreach ( $this->elements as $class ) {
			$args = $class->get_args();
			if ( $args['addable'] ) {
				$elements[] = $class->get_args();
			}
		}

		AJAX_Response::send_response( $elements );
	}

	/**
	 * Get particular element by its name.
	 *
	 * @since 1.0.0
	 */
	public function get_element_ajax() {
		$el = trim( $_REQUEST['element'] ); // phpcs:ignore

		$el = $this->elements[ $el ];

		AJAX_Response::send_response( $el->get_args() );
	}
}
