<?php
/**
 * Theme features class
 *
 * @since 1.0.0
 * @package xts
 */

namespace XTS;

/**
 * Theme features class
 *
 * @since 1.0.0
 */
class Theme_Features extends Singleton {
	/**
	 * Theme features
	 *
	 * @var array
	 */
	public static $features = array();

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
	}

	/**
	 * Add this theme support.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Feature key.
	 */
	public static function add( $key ) {
		self::$features[ $key ] = $key;
	}

	/**
	 * Remove this theme support.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Feature key.
	 */
	public static function remove( $key ) {
		unset( self::$features[ $key ] );
	}

	/**
	 * Does this theme support.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Feature key.
	 * @return bool
	 */
	public static function supports( $key ) {
		return isset( self::$features[ $key ] );
	}
}

Theme_Features::get_instance();
