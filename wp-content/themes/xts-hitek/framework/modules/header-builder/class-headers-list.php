<?php
/**
 * Manage headers lists in the database. CRUD
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Manage headers lists in the database. CRUD
 */
class Headers_List {

	/**
	 * Set default header ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Header ID.
	 */
	public function set_default( $id ) {
		update_option( 'xts_main_header', $id );
	}

	/**
	 * Get default header ID.
	 *
	 * @since 1.0.0
	 */
	public function get_default() {
		$id = get_option( 'xts_main_header' );

		if ( ! $id ) {
			$id = XTS_HB_DEFAULT_ID;
		}

		return $id;
	}

	/**
	 * Get all headers.
	 *
	 * @since 1.0.0
	 */
	public function get_all() {
		$default_id = XTS_HB_DEFAULT_ID;

		$list = array(
			$default_id => array(
				'id'   => XTS_HB_DEFAULT_ID,
				'name' => XTS_HB_DEFAULT_NAME,
			),
		);

		$header = get_option( 'xts_' . $default_id );

		if ( isset( $header['name'] ) ) {
			$list[ $default_id ]['name'] = $header['name'];
		}

		$saved_headers = get_option( 'xts_saved_headers' );

		if ( ! empty( $saved_headers ) && is_array( $saved_headers ) ) {
			$list = array_merge( $list, $saved_headers );
		}

		return $list;
	}

	/**
	 * Add header by ID and name.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $id   Header ID.
	 * @param bool $name Header name.
	 *
	 * @return array
	 */
	public function add_header( $id = false, $name = false ) {
		$list = $this->get_all();

		$list[ $id ] = array(
			'id'   => $id,
			'name' => $name,
		);

		update_option( 'xts_saved_headers', $list );

		return $list;
	}

	/**
	 * Remove header by ID.
	 *
	 * @since 1.0.0`
	 *
	 * @param string $id Header ID.
	 *
	 * @return array
	 */
	public function remove( $id ) {
		$list = $this->get_all();
		if ( isset( $list[ $id ] ) ) {
			unset( $list[ $id ] );
		}

		update_option( 'xts_saved_headers', $list );

		return $list;
	}

	/**
	 * Get header examples config.
	 *
	 * @since 1.0.0
	 */
	public function get_examples() {
		return xts_get_config( 'header-examples' );
	}
}
