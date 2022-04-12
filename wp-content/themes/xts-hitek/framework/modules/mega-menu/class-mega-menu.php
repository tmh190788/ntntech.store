<?php
/**
 * Mega menu base class.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;

/**
 * Define constants, load classes and initialize everything.
 */
class Mega_Menu extends Module {

	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->include_files();

		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'filter_walker' ) );
		add_action( 'wp_update_nav_menu_item', array( $this, 'custom_fields_save' ), 10, 3 );
	}

	/**
	 * Include main files.
	 *
	 * @since 1.0.0
	 */
	private function include_files() {
		xts_get_file( 'framework/modules/mega-menu/class-walker' );
	}

	/**
	 * Use custom walker for nav menu edit.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function filter_walker() {
		require_once XTS_FRAMEWORK_ABSPATH . 'modules/mega-menu/class-edit-walker.php';
		return 'XTS\Module\Mega_Menu\Edit_Walker';
	}

	/**
	 * Use custom walker for nav menu edit.
	 *
	 * @since 1.0.0
	 *
	 * @param string $menu_id Name of used walker class.
	 * @param string $menu_item_db_id Name of used walker class.
	 * @param string $args Name of used walker class.
	 */
	public function custom_fields_save( $menu_id, $menu_item_db_id, $args ) {
		$fields       = array( 'design', 'width', 'event', 'label', 'label-text', 'block', 'dropdown-ajax', 'colorscheme', 'opanchor', 'image' );
		$fields_count = count( $fields );

		for ( $i = 0; $i < $fields_count; $i++ ) {
			$key = 'menu-item-' . $fields[ $i ];
			if ( isset( $_REQUEST[ $key ] ) && isset( $_REQUEST[ $key ][ $menu_item_db_id ] ) && ! empty( $_REQUEST[ $key ] ) && is_array( $_REQUEST[ $key ] ) ) { // phpcs:ignore
				$custom_value = $_REQUEST[ $key ][ $menu_item_db_id ]; // phpcs:ignore
				update_post_meta( $menu_item_db_id, '_menu_item_' . $fields[ $i ], $custom_value );
			}
		}
	}
}
