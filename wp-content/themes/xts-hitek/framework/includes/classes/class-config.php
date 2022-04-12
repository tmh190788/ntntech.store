<?php
/**
 * Config class.
 *
 * @package xts
 */

namespace XTS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Config class.
 *
 * @since 1.0.0
 */
class Config extends Singleton {
	/**
	 * Config.
	 *
	 * @var object
	 */
	private $config = array();

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {}

	/**
	 * Get config file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Config name.
	 * @param string $from Where search config.
	 *
	 * @return mixed
	 */
	public function get_config( $name, $from = 'framework' ) {
		if ( isset( $this->config[ $name ] ) ) {
			return $this->config[ $name ];
		}

		$path = xts_get_theme_root_path( $from . '/configs/' . $name );

		if ( file_exists( $path ) ) {
			$this->config[ $name ] = include_once $path;
			return $this->config[ $name ];
		}

		return false;
	}
}
