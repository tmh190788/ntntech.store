<?php
/**
 * Root element. Required for the structure only. Can hold one element only.
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
 * Root element. Required for the structure only. Can hold one element only.
 */
class Root extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'root';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'root',
			'title'           => esc_html__( 'Root', 'xts-theme' ),
			'text'            => esc_html__( 'Root', 'xts-theme' ),
			'editable'        => false,
			'container'       => false,
			'edit_on_create'  => false,
			'drag_target_for' => array(),
			'drag_source'     => '',
			'removable'       => false,
			'addable'         => false,
			'class'           => '',
			'it_works'        => 'root',
			'content'         => array(),
		);
	}

}

