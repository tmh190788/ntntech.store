<?php
/**
 * Main Theme class
 *
 * @package xts
 */

namespace XTS;

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Main Theme class
 *
 * Also includes files with theme functions
 * template tags, 3d party plugins etc.
 *
 * @since 1.0.0
 */
class Theme extends Singleton {
	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->include_files();
	}

	/**
	 * Include basic files.
	 *
	 * @since 1.0.0
	 */
	public function include_files() {
		xts_get_file( 'theme/theme-functions' );

		Modules::register( 'wc-sticky-categories-navigation' );
		Modules::register( 'menu-overlay' );
	}
}

Theme::get_instance();
