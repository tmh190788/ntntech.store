<?php
/**
 * Frontend class that initiallize current header for the page and generates its structure HTML + CSS.
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Wrapper for our header class instance. CRUD actions.
 */
class Header_Factory {
	/**
	 * Elements class object.
	 *
	 * @var object
	 */
	private $elements = null;

	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 *
	 * @param object $elements Elements object.
	 */
	public function __construct( $elements ) {
		$this->elements = $elements;
	}

	/**
	 * Create header object based on ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Header ID.
	 *
	 * @return Header
	 */
	public function get_header( $id ) {
		return new Header( $this->elements, $id );
	}

	/**
	 * Update header structure and settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id        Header ID.
	 * @param string $name      Header name.
	 * @param array  $structure New header structure.
	 * @param array  $settings  New header settings.
	 *
	 * @return Header
	 */
	public function update_header( $id, $name, $structure, $settings ) {
		$header = new Header( $this->elements, $id );

		$header->set_name( $name );
		$header->set_structure( $structure );
		$header->set_settings( $settings );

		$header->save();

		return $header;
	}

	/**
	 * Create a new header based on id name structure and settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id        Header ID.
	 * @param string $name      Header name.
	 * @param bool   $structure New header structure.
	 * @param bool   $settings  New header settings.
	 *
	 * @return Header
	 */
	public function create_new( $id, $name, $structure = false, $settings = false ) {
		$header = new Header( $this->elements, $id, true );

		if ( $structure ) {
			$header->set_structure( $structure );
		}
		if ( $settings ) {
			$header->set_settings( $settings );
		}

		$header->set_name( $name );
		$header->save();

		return $header;
	}
}
