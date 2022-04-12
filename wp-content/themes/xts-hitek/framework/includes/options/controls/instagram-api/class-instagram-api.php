<?php
/**
 * Instagram API control.
 *
 * @package xts
 */

namespace XTS\Options\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Field;

/**
 * Input type text field control.
 */
class Instagram_Api extends Field {
	/**
	 * Displays the field control HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function render_control() {
		if ( isset( $_GET['instagram_account_id'] ) && isset( $_GET['instagram_access_token'] ) ) { // phpcs:ignore
			?>
				<div class="xts-notice xts-success">
					<?php esc_html_e( 'Access token generated', 'xts-theme' ); ?>
				</div>
			<?php

			$this->connect();
		}

		if ( isset( $_GET['instagram_account_disconnect'] ) ) { // phpcs:ignore
			$this->disconnect();
		}

		$instagram_access_token = get_option( 'xts_instagram_access_token' );

		if ( $instagram_access_token ) {
			$this->show_connected_account();
		}

		?>
			<div class="xts-upload-btns">
				<a href="<?php echo esc_url( $this->get_connect_url() ); ?>" class="xts-btn xts-btn-primary">
					<?php esc_html_e( 'Connect', 'xts-theme' ); ?>
				</a>

				<?php if ( $instagram_access_token ) : ?>
					<a href="<?php echo esc_url( $this->get_return_url() . '&instagram_account_disconnect' ); ?>" class="xts-btn xts-btn-disable">
						<?php esc_html_e( 'Disconnect', 'xts-theme' ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php
	}

	/**
	 * Save instagram data.
	 */
	public function connect() {
		update_option( 'xts_instagram_account_id', $_GET['instagram_account_id'] ); // phpcs:ignore
		update_option( 'xts_instagram_access_token', $_GET['instagram_access_token'] ); // phpcs:ignore
	}

	/**
	 * Delete instagram data.
	 */
	public function disconnect() {
		$instagram_account_id = get_option( 'xts_instagram_account_id' );

		delete_option( 'xts_instagram_account_data_' . $instagram_account_id );
		delete_option( 'xts_instagram_account_id' );
		delete_option( 'xts_instagram_access_token' );
	}

	/**
	 * Get return url.
	 *
	 * @return string
	 */
	public function get_return_url() {
		return admin_url( 'admin.php' ) . '?page=xtemos_options&tab=instagram_api_section';
	}

	/**
	 * Get connect url.
	 *
	 * @return string
	 */
	public function get_connect_url() {
		$app_id        = '420748032186288';
		$base_url      = 'https://www.facebook.com/v5.0/dialog/oauth';
		$redirect_uri  = rawurlencode( 'https://xtemos.com/instagram.php' );
		$response_type = 'code';
		$scope         = 'manage_pages,instagram_basic,public_profile';
		$return_url    = rawurlencode( $this->get_return_url() );

		return $base_url . '?response_type=' . $response_type . '&client_id=' . $app_id . '&redirect_uri=' . $redirect_uri . '&scope=' . $scope . '&state=' . $return_url;
	}

	/**
	 * Show connected account.
	 */
	public function show_connected_account() {
		$instagram_account_id   = get_option( 'xts_instagram_account_id' );
		$instagram_account_data = get_option( 'xts_instagram_account_data_' . $instagram_account_id );

		if ( ! $instagram_account_data ) {
			$instagram_account_data = $this->get_connected_account_data();
		}

		if ( ! is_array( $instagram_account_data ) ) {
			return;
		}

		?>
			<div class="xts-insta-profile">
				<div class="xts-insta-pic">
					<img src="<?php echo esc_url( $instagram_account_data['image'] ); ?>" alt="<?php esc_attr_e( 'avatar', 'xts-theme' ); ?>">
				</div>

				<div class="xts-insta-name">
					<?php echo esc_html( $instagram_account_data['name'] ); ?> : <span>@<?php echo esc_html( $instagram_account_data['username'] ); ?></span>
				</div>
			</div>
		<?php
	}

	/**
	 * Get connected account data.
	 *
	 * @return array|bool
	 */
	public function get_connected_account_data() {
		$instagram_access_token = get_option( 'xts_instagram_access_token' );
		$instagram_account_id   = get_option( 'xts_instagram_account_id' );

		$account_data = wp_remote_get( 'https://graph.facebook.com/' . $instagram_account_id . '?fields=biography,id,username,website,followers_count,media_count,profile_picture_url,name&access_token=' . $instagram_access_token );
		$data_decoded = json_decode( $account_data['body'] );

		if ( is_object( $data_decoded ) && property_exists( $data_decoded, 'error' ) ) {
			echo esc_html( 'Get connected account data :' . $data_decoded->error->message );
			return false;
		}

		$data = array(
			'image'           => $data_decoded->profile_picture_url,
			'name'            => $data_decoded->name,
			'username'        => $data_decoded->username,
			'followers_count' => $data_decoded->followers_count,
		);

		update_option(
			'xts_instagram_account_data_' . $instagram_account_id,
			$data
		);

		return $data;
	}
}


