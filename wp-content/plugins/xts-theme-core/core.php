<?php
/**
 * Plugin Name: XTemos theme core
 * Plugin URI: https://xtemos.com
 * Description: Enable it to load framework from the plugin
 * Version: 1.1.1
 * Author: XTemos
 * Author URI: https://xtemos.com
 * Text Domain: xts-theme
 * Domain Path: /languages/
 *
 * @package xts
 */

define( 'XTS_CORE_VERSION', '1.1.1' );
define( 'XTS_CORE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

if ( ! function_exists( 'xts_core_init_plugin' ) ) {
	/**
	 * Init core plugin.
	 */
	function xts_core_init_plugin() {
		if ( ! defined( 'XTS_THEME_SLUG' ) ) {
			return;
		}

		require_once XTS_CORE_PLUGIN_PATH . '/hooks.php';
		require_once XTS_CORE_PLUGIN_PATH . '/shortcodes.php';
		require_once XTS_CORE_PLUGIN_PATH . '/post-type.php';
		require_once XTS_CORE_PLUGIN_PATH . '/functions.php';
		require_once XTS_CORE_PLUGIN_PATH . '/class-twitter-api.php';
		require_once XTS_CORE_PLUGIN_PATH . '/wc-social-authentication/class-wc-social-authentication.php';
	}

	add_action( 'init', 'xts_core_init_plugin', 5 );
}

if ( ! function_exists( 'xts_include_social_auth_dir' ) ) {
	/**
	 * Include social auth dir.
	 *
	 * @return string
	 */
	function xts_include_social_auth_dir() {
		return XTS_CORE_PLUGIN_PATH . '/wc-social-authentication/';
	}

	add_filter( 'xts_social_auth_dir', 'xts_include_social_auth_dir' );
}

require_once XTS_CORE_PLUGIN_PATH . '/widgets.php';

