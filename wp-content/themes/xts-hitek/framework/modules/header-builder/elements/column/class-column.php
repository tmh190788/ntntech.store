<?php
/**
 * Basic structure element - column
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Modules;
use XTS\Header_Builder\Element;

/**
 * Basic structure element - column class
 */
class Column extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'column';
	}
	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'column',
			'title'           => esc_html__( 'Column', 'xts-theme' ),
			'text'            => esc_html__( 'Column', 'xts-theme' ),
			'editable'        => false,
			'container'       => true,
			'edit_on_create'  => false,
			'drag_target_for' => array( 'content_element' ),
			'drag_source'     => '',
			'removable'       => false,
			'class'           => '',
			'addable'         => false,
			'it_works'        => 'column',
			'content'         => array(),
		);
	}
}
