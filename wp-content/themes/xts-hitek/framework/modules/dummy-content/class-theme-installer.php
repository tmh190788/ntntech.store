<?php
/**
 * Theme Installer class.
 *
 * @package xts
 */

namespace XTS\Modules\Dummy_Content;

use XTS\Framework\AJAX_Response;
use XTS\Api_Client;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Theme installer main class.
 *
 * @since 1.0.0
 */
class Theme_Installer {
	/**
	 * Current version to import.
	 *
	 * @var string
	 */
	private $_version;

	/**
	 * API client object.
	 *
	 * @var object
	 */
	private $api;

	/**
	 * Initial setup for actions and hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->api = new Api_Client();

		add_action( 'wp_ajax_xts_install_theme', array( $this, 'install_theme' ) );
	}

	/**
	 * Install theme AJAX action.
	 *
	 * @since 1.0.0
	 */
	public function install_theme() {
		check_ajax_referer( 'xts-install-theme', 'security' );

		if ( ! isset( $_GET['theme'] ) ) {
			AJAX_Response::send_fail_msg( 'Theme slug is missed.' );
		}

		$theme      = sanitize_text_field( wp_unslash( $_GET['theme'] ) );
		$theme_list = xts_get_config( 'theme-list' );

		if ( ! isset( $theme_list[ $theme ] ) ) {
			AJAX_Response::send_fail_msg( 'Wrong theme slug.' );
		}

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		/* translators: %s: Theme name and version. */
		$title = sprintf( esc_html__( 'Installing Theme: %s', 'xts-theme' ), $theme );
		$nonce = 'install-theme_' . $theme;
		$url   = 'update.php?action=install-theme&theme=' . urlencode( $theme );
		$type  = 'web'; // Install theme type, From Web or an Upload.

		$api = new \stdClass();

		$api->slug          = $theme;
		$api->name          = $theme;
		$api->version       = '1.0';
		$api->download_link = $this->api->get_url( 'download', array( 'theme' => $theme ) );

		$skin     = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Theme_Upgrader( $skin );
		$result   = $upgrader->install( $api->download_link );

		$status = array();

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$status['debug'] = $skin->get_upgrade_messages();
		}

		if ( is_wp_error( $result ) ) {
			AJAX_Response::send_fail_msg( $result->get_error_message() );
		} elseif ( is_wp_error( $skin->result ) ) {
			AJAX_Response::send_fail_msg( $skin->result->get_error_message() );
		} elseif ( $skin->get_errors()->has_errors() ) {
			AJAX_Response::send_fail_msg( $skin->get_error_messages() );
		}

		$status['themeName'] = wp_get_theme( $theme )->get( 'Name' );

		if ( current_user_can( 'switch_themes' ) ) {
			if ( is_multisite() ) {
				$status['activateUrl'] = add_query_arg(
					array(
						'action'   => 'enable',
						'_wpnonce' => wp_create_nonce( 'enable-theme_' . $theme ),
						'theme'    => $theme,
					),
					network_admin_url( 'themes.php' )
				);
			} else {
				$status['activateUrl'] = add_query_arg(
					array(
						'action'     => 'activate',
						'_wpnonce'   => wp_create_nonce( 'switch-theme_' . $theme ),
						'stylesheet' => $theme,
					),
					admin_url( 'themes.php' )
				);
			}
		}

		$status['status'] = 'success';

		AJAX_Response::send_response( $status );
	}
}

new Theme_Installer();
