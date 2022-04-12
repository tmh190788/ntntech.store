<?php
/**
 * Modules container.
 *
 * @package xts
 */

namespace XTS\Framework;

use XTS\Framework\Module;

use XTS\Singleton;

/**
 * Basic class to register and keep all the modules.
 *
 * @since 1.0.0
 */
class Modules extends Singleton {
	/**
	 * Modules array.
	 *
	 * @var array
	 */
	private static $modules = array();

	/**
	 * Register module by name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Module name (key).
	 * @param bool   $print_error Need to output errors.
	 */
	public static function register( $name, $print_error = true ) {
		if ( isset( self::$modules[ $name ] ) ) {
			return;
		}

		$file = XTS_FRAMEWORK_ABSPATH . 'modules/' . $name . '/class-' . $name . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
			$class_name             = ucwords( str_replace( '-', ' ', $name ) );
			$class_name             = str_replace( ' ', '_', $class_name );
			$class_name             = 'XTS\\Modules\\' . $class_name;
			self::$modules[ $name ] = new $class_name();
		} elseif ( $print_error ) {
			echo 'Module ' . esc_html( $name ) . ' not found.';
			die();
		}
	}

	/**
	 * Module by name is exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Module name (key).
	 *
	 * @return mixed
	 */
	public static function is_module_exists( $name ) {
		return isset( self::$modules[ $name ] );
	}

	/**
	 * Get module by name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Module name (key).
	 *
	 * @return mixed
	 */
	public static function get( $name ) {
		if ( isset( self::$modules[ $name ] ) ) {
			return self::$modules[ $name ];
		}

		echo 'Module not found - ' . esc_html( $name );

		die();
	}
}
