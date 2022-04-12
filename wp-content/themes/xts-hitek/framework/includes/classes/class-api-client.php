<?php
/**
 * API client to communicate with xtemos space api server.
 *
 * @package xts
 */

namespace XTS;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * API client class to communicate with xtemos space api server.
 *
 * @since 1.0.0
 */
class Api_Client {
	/**
	 * API Url.
	 *
	 * @var string
	 */
	private $url;
	/**
	 * License token.
	 *
	 * @var string
	 */
	private $token;

	/**
	 * Construct the API class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->token = xts_get_token();
		$this->url   = XTS_API_URL;
	}

	/**
	 * Activate theme.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key License key.
	 * @param string $dev Is dev domain.
	 *
	 * @return array|mixed|object|WP_Error|null
	 */
	public function activate( $key, $dev ) {
		return $this->post(
			'activate',
			array(
				'key'    => $key,
				'domain' => get_site_url(),
				'theme'  => XTS_THEME_SLUG,
				'dev'    => $dev,
			)
		);
	}

	/**
	 * Deactivate theme.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		$response = $this->get(
			'deactivate',
			array(
				'token' => $this->token,
			)
		);

		return isset( $response['success'] ) && true === $response['success'];
	}

	/**
	 * Refresh the license information.
	 *
	 * @since 1.0.0
	 */
	public function refresh() {
		return $this->get(
			'refresh',
			array(
				'token' => $this->token,
			)
		);
	}

	/**
	 * Optimize image by file url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file File URL.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function optimize( $file ) {
		return $this->post(
			'optimize',
			array(
				'file' => $file,
			)
		);
	}

	/**
	 * Perform a get request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint Endpoint.
	 * @param array  $params   Parameters array.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get( $endpoint, $params = array() ) {
		return $this->request( 'get', $endpoint, $params );
	}

	/**
	 * Perform a post request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint Endpoint.
	 * @param array  $params   Parameters array.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function post( $endpoint, $params = array() ) {
		return $this->request( 'post', $endpoint, $params );
	}

	/**
	 * Perform a get/post request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $method   Method Get or Post.
	 * @param string $endpoint Endpoint.
	 * @param array  $params   Parameters array.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function request( $method = 'get', $endpoint = '', $params = array() ) {
		switch ( $method ) {
			case 'get':
				$url = $this->get_url( $endpoint, $params );

				$response = wp_remote_get( $url );

				if ( is_wp_error( $response ) ) {
					$response = array(
						'success' => false,
						'error'   => 'wp_remote_get WP error',
						'code'    => 401,
					);
				} elseif ( isset( $response['response'] ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
					$response            = json_decode( $response['body'], true );
					$response['success'] = true;
				} else {
					$response = array(
						'success' => false,
						'error'   => 'error_txt',
						'code'    => 401,
					);
				}

				break;
			case 'post':
				$url = $this->add_token( $this->url . $endpoint );

				if ( isset( $params['file'] ) ) {
					if ( class_exists( '\CURLFile' ) ) {
						$file = new \CURLFile( $params['file'] );
					} else {
						$file = '@' . $params['file'];
					}

					unset( $params['file'] );

					$params['file'] = $file;
				}

				$response = wp_remote_post(
					$url,
					array(
						'headers'     => array( 'Content-Type' => 'application/json; charset=utf-8' ),
						'body'        => wp_json_encode( $params ),
						'method'      => 'POST',
						'data_format' => 'body',
					)
				);

				$response = json_decode( $response['body'], true );

				if ( ! isset( $response['success'] ) ) {
					$response = array(
						'success' => false,
						'error'   => $response['message'],
						'code'    => $response['code'],
					);
				}

				break;
		}

		return $response;
	}

	/**
	 * Get API url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint Endpoint.
	 * @param array  $params   Parameters array.
	 *
	 * @return string
	 */
	public function get_url( $endpoint, $params = array() ) {
		return add_query_arg( $params, $this->add_token( $this->url . $endpoint ) );
	}

	/**
	 * Add license token parameter to the URL
	 *
	 * @since 1.0.0
	 *
	 * @param string $url API request url.
	 *
	 * @return string
	 */
	private function add_token( $url ) {
		if ( ! $this->token ) {
			return $url;
		}

		return $url . '?token=' . $this->token;
	}
}
