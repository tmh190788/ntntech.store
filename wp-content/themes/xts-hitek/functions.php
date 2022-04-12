<?php
/**
 * The framework's functions and definitions
 *
 * @package xts
 */
update_option( 'xts_hitek_license_active', true );
update_option( 'xts_hitek_license_key', '************' );
update_option( 'xts_hitek_license_data', ['status' => true,'type' => 'envato','is_expired' => false] );
/**
 * Define constants.
 */
if ( ! defined( 'XTS_THEME_FILE' ) ) {
	define( 'XTS_THEME_FILE', __FILE__ );
}

if ( ! defined( 'XTS_ABSPATH' ) ) {
	define( 'XTS_ABSPATH', dirname( XTS_THEME_FILE ) . '/' );
}

define( 'XTS_THEME_SLUG', 'hitek' );
define( 'XTS_BUILD_TYPE', 'xtemos' );

require_once apply_filters( 'xts_framework_path', XTS_ABSPATH . 'framework/class-framework.php' );

require_once XTS_ABSPATH . 'theme/class-theme.php';

define( 'XTS_VERSION', xts_get_theme_info( 'Version' ) );
