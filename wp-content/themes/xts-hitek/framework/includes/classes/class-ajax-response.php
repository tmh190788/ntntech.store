<?php
/**
 * Ajax response class.
 *
 * @package xts
 */

namespace XTS\Framework;

use XTS\Singleton;

/**
 * Ajax response class.
 *
 * @since 1.0.0
 */
class AJAX_Response extends Singleton {

	/**
	 * Response data.
	 *
	 * @var array
	 */
	public static $response = array();

	/**
	 * Initialization.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		self::$response = array(
			'status'  => 'fail',
			'message' => '',
		);
	}

	/**
	 * Send response array.
	 *
	 * @param array   $array Data.
	 * @param boolean $header Is send header.
	 *
	 * @since 1.0.0
	 */
	public static function send_response( $array = array(), $header = false ) {
		if ( $header ) {
			@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) ); // phpcs:ignore
		}

		if ( empty( $array ) && ! empty( self::$response ) ) {
			echo wp_json_encode( self::$response );
		} elseif ( ! empty( $array ) ) {
			echo wp_json_encode( $array );
		} else {
			echo wp_json_encode( array( 'message' => 'empty response' ) );
		}

		die();
	}

	/**
	 * Add message
	 *
	 * @param string $msg Message text.
	 *
	 * @since 1.0.0
	 */
	public static function add_msg( $msg ) {
		self::$response['status'] = 'success';
		if ( isset( self::$response['message'] ) ) {
			self::$response['message'] .= $msg . '<br>';
		}
	}

	/**
	 * Send Success message.
	 *
	 * @param string $msg Message text.
	 *
	 * @since 1.0.0
	 */
	public static function send_success_msg( $msg ) {
		self::send_msg( 'success', $msg );
	}

	/**
	 * Send fail message immediately.
	 *
	 * @param string $msg Message text.
	 *
	 * @since 1.0.0
	 */
	public static function send_fail_msg( $msg ) {
		self::send_msg( 'fail', $msg );
	}

	/**
	 * Send message of different type (status).
	 *
	 * @param string $status Response statues.
	 * @param string $message Message text.
	 *
	 * @since 1.0.0
	 */
	public static function send_msg( $status, $message ) {
		self::$response = array(
			'status'  => $status,
			'message' => $message,
		);

		self::send_response();
	}
}
