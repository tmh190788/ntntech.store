<?php
/**
 * Activation part class
 *
 * @package xts
 */

namespace XTS\Framework;

use XTS\Api_Client;
use XTS\Singleton;

/**
 * Activation class
 *
 * @since 1.0.0
 */
class Activation extends Singleton {
	/**
	 * Notices object.
	 *
	 * @var object
	 */
	private $notices = null;

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->notices = new Notices();
		add_action( 'init', array( $this, 'activate_license' ) );
	}

	/**
	 * Activation page content
	 */
	public function form() {
		$dashboard = Dashboard::get_instance();
		$data      = xts_get_license_data();
		?>
			<div class="xts-row">
				<div class="xts-col xts-col-8">
					<div class="xts-dashboard-box xts-activation <?php echo xts_is_activated_license() ? 'xts-activated' : ''; ?>">
						<div class="xts-dashboard-box-header">
							<div class="xts-dashboard-box-header-inner">
								<h3>
									<?php esc_html_e( 'Activation form', 'xts-theme' ); ?>
								</h3>

								<?php if ( xts_is_activated_license() ) : ?>
									<p>
										<?php esc_html_e( 'Your theme licence is activated', 'xts-theme' ); ?>
									</p>
								<?php else : ?>
									<p>
										<?php esc_html_e( 'Fill the form with your license key', 'xts-theme' ); ?>
									</p>
								<?php endif; ?>

							</div>
							<?php $dashboard->activation_status(); ?>
						</div>

						<div class="xts-dashboard-box-content">
							<?php $this->notices->show_msgs(); ?>

							<form action="#" method="post" class="xts-activation-form">

								<?php if ( xts_is_activated_license() ) : ?>
									<div class="xts-license-wrapper ">
										<div class="xts-row xts-row-spacing-30">
											<div class="xts-col xts-col-12">
												<div class="xts-activation-field">
													<?php esc_html_e( 'License key:', 'xts-theme' ); ?>
												</div>
												<div class="xts-activation-key">
													<?php echo esc_html( $this->string_to_secret( xts_get_license_key() ) ); ?>
												</div>
											</div>
											<?php if ( isset( $data['status'] ) && 'envato' !== $data['type'] ) : ?>
												<div class="xts-col xts-col-12">
													<div class="xts-activation-field">
														<?php esc_html_e( 'Status:', 'xts-theme' ); ?>
													</div>
													<div class="<?php echo 'expired' === $data['status'] || 'pending cancellation' === $data['status'] ? 'xts-status-warning' : 'xts-status-success'; ?>">
														<?php echo esc_html( $data['status'] ); ?>
													</div>
												</div>
											<?php endif; ?>
											<div class="xts-col xts-col-12">
												<div class="xts-activation-field">
													<?php esc_html_e( 'License type:', 'xts-theme' ); ?>
												</div>
												<div>
													<?php echo esc_html( $data['type'] ); ?>
												</div>
											</div>
											<?php if ( isset( $data['next_payment_date'] ) && $data['next_payment_date'] ) : ?>
												<div class="xts-col xts-col-12">
													<div class="xts-activation-field">
														<?php esc_html_e( 'Next payment date:', 'xts-theme' ); ?>
													</div>
													<div>
														<?php echo esc_html( date( 'd M Y', strtotime( $data['next_payment_date'] ) ) ); ?>
													</div>
												</div>
											<?php endif; ?>
											<?php if ( isset( $data['end_date'] ) && $data['end_date'] ) : ?>
												<div class="xts-col xts-col-12">
													<div class="xts-activation-field">
														<?php esc_html_e( 'End date:', 'xts-theme' ); ?>
													</div>
													<div><?php echo esc_html( date( 'd M Y', strtotime( $data['end_date'] ) ) ); ?></div>
												</div>
											<?php endif; ?>
										</div>
									</div>
								<?php else : ?>
									<div class="xts-activation-input">
										<input id="xts-license-key" placeholder="Enter your license key" type="text" name="xts-license-key">
										<?php $dashboard->activation_status(); ?>
									</div>
									<div class="xts-dev-domain">
										<input id="xts-dev-domain-label" type="checkbox" name="xts-dev-domain" <?php checked( isset( $_REQUEST['xts-dev-domain'] ) && $_REQUEST['xts-dev-domain'], '1' ); // phpcs:ignore ?> value="1">
										<label for="xts-dev-domain-label">
											<?php esc_html_e( 'Development domain', 'xts-theme' ); ?>
										</label>
									</div>
									<?php if ( ! xts_is_build_for_space() ) : ?>
										<div class="xts-dev-domain">
											<input id="agree_stored" type="checkbox" name="agree_stored" required>
											<label for="agree_stored">
												<?php esc_html_e( 'I agree that my purchase code and user data will be stored by xtemos.com', 'xts-theme' ); ?>
											</label>
										</div>
									<?php endif; ?>
								<?php endif; ?>

								<div class="xts-activation-actions">
									<?php wp_nonce_field( 'xts_license_activation', 'xts_license_activation_nonce' ); ?>

									<?php if ( xts_is_activated_license() ) : ?>
										<?php if ( $data['is_expired'] && 'envato' !== $data['type'] ) : ?>
											<button type="submit" class="xts-btn xts-btn-refresh xts-size-l" name="xts-license-refresh">
												<?php esc_html_e( 'Refresh', 'xts-theme' ); ?>
											</button>
										<?php endif; ?>
										<button type="submit" class="xts-btn xts-btn-disable xts-size-l" name="xts-license-deactivate">
											<?php esc_html_e( 'Deactivate', 'xts-theme' ); ?>
										</button>
									<?php else : ?>
										<button type="submit" class="xts-btn xts-btn-primary xts-btn-shadow xts-size-l" name="xts-license-activate">
											<?php esc_html_e( 'Activate', 'xts-theme' ); ?>
										</button>
									<?php endif; ?>
								</div>
							</form>

							<?php if ( ! xts_is_activated_license() ) : ?>
								<div class="xts-activation-infoboxes-wrap xts-row xts-row-spacing-20">
									<div class="xts-col xts-col-6">
										<a  href="<?php echo esc_url( xts_get_link_by_key( 'activation_find_license_key' ) ); ?>" class="xts-activation-infobox" target="_blank">
											<div class="xf-sw-license"></div>
											<h4><?php esc_html_e( 'Find license key', 'xts-theme' ); ?></h4>
											<p><?php esc_html_e( 'Where can I find my license key?', 'xts-theme' ); ?></p>
										</a>
									</div>
									<div class="xts-col xts-col-6">
										<a href="<?php echo esc_url( xts_get_link_by_key( 'activation_purchase' ) ); ?>" class="xts-activation-infobox" target="_blank">
											<div class="xf-sw-purchase"></div>
											<h4><?php esc_html_e( 'Purchase license', 'xts-theme' ); ?></h4>
											<p><?php esc_html_e( 'I don\'t have a license key where can I get it?', 'xts-theme' ); ?></p>
										</a>
									</div>
								</div>
							<?php endif; ?>
						</div>

						<?php if ( $dashboard->is_setup() ) : ?>
							<div class="xts-dashboard-box-footer xts-setup-wizard-footer">
								<?php $dashboard->setup_back_link( 'xts_dashboard' ); ?>
								<?php $dashboard->setup_next_link( 'xts_child_theme' ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( ! $dashboard->is_setup() && xts_is_build_for_space() ) : ?>
					<div class="xts-col xts-col-4">
						<div class="xts-dashboard-video">
							<div class="xts-info">
								<span><?php esc_html_e( 'Go to your account on our website to obtain the license key.', 'xts-theme' ); ?></span>
								<a href="<?php echo esc_url( xts_get_link_by_key( 'activation_go_to_account' ) ); ?>" class="xts-inline-btn" target="_blank">
									<?php esc_html_e( 'Go to account', 'xts-theme' ); ?>
								</a>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php
	}

	/**
	 * Activation action.
	 *
	 * @since 1.0
	 *
	 * @param null $string $string String.
	 *
	 * @return string
	 */
	private function string_to_secret( $string = null ) {
		if ( ! $string ) {
			return null;
		}

		$length        = strlen( $string );
		$visible_count = (int) round( $length / 7 );
		$hidden_count  = $length - ( $visible_count * 2 );

		return substr( $string, 0, $visible_count ) . str_repeat( '*', $hidden_count ) . substr( $string, ( $visible_count * -1 ), $visible_count );
	}

	/**
	 * Activation action.
	 *
	 * @since 1.0
	 */
	public function activate_license() {
		if ( ! isset( $_POST['xts_license_activation_nonce'] ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( $_POST['xts_license_activation_nonce'], 'xts_license_activation' ) ) { // phpcs:ignore
			$this->notices->add_error( 'Security error. Please, try again.' );
			return false;
		}

		if ( isset( $_POST['xts-license-refresh'] ) ) {
			$this->refresh();
			return false;
		}

		if ( isset( $_POST['xts-license-deactivate'] ) ) {
			$this->deactivate();
			$this->notices->add_success( 'Theme is successfully deactivated!' );
			return false;
		}

		if ( ! isset( $_POST['xts-license-key'] ) ) {
			$this->notices->add_error( 'Wrong or empty license key.' );
			return false;
		}

		$key = sanitize_text_field( $_POST['xts-license-key'] ); // phpcs:ignore
		$dev = (int) ( isset( $_POST['xts-dev-domain'] ) && $_POST['xts-dev-domain'] ); // phpcs:ignore

		$this->api = new Api_Client();

		$response = $this->api->activate( $key, $dev );

		if ( isset( $response['success'] ) && true === $response['success'] && $response['token'] ) {
			$this->notices->add_success( 'Theme is successfully activated!' );
			$this->update_license_data( $key, $response['token'], $response['license_data'], $dev );
		} elseif ( isset( $response['errors'] ) ) {
			$this->notices->add_error( $response['errors'] );
		} elseif ( isset( $response['code'] ) && 'rest_forbidden' === $response['code'] ) {
			$this->notices->add_error( 'This license key is not valid or already expired.' );
		} else {
			$this->notices->add_error( 'API call error.' );
		}

		return false;
	}

	/**
	 * Activation action.
	 *
	 * @param string $key License key.
	 * @param string $token  License token.
	 * @param array  $license_data  License data array.
	 * @param bool   $dev  Is dev domain.
	 *
	 * @since 1.0
	 */
	private function update_license_data( $key, $token, $license_data, $dev ) {
		if ( 'all-themes' === $license_data['type'] ) {
			update_option( 'xts_all_themes_license', XTS_THEME_SLUG );
		}

		update_option( 'xts_' . XTS_THEME_SLUG . '_token', $token );
		update_option( 'xts_' . XTS_THEME_SLUG . '_license_data', $license_data );
		update_option( 'xts_' . XTS_THEME_SLUG . '_license_key', $key );
		update_option( 'xts_' . XTS_THEME_SLUG . '_license_active', true );
		update_option( 'xts_' . XTS_THEME_SLUG . '_dev_domain', $dev );
	}

	/**
	 * Deactivation action.
	 *
	 * @since 1.0
	 */
	private function deactivate() {
		$this->api = new Api_Client();

		$this->api->deactivate();

		$slug = get_option( 'xts_all_themes_license' ) ? get_option( 'xts_all_themes_license' ) : XTS_THEME_SLUG;

		delete_option( 'xts_' . $slug . '_token' );
		delete_option( 'xts_' . $slug . '_license_data' );
		delete_option( 'xts_' . $slug . '_license_key' );
		delete_option( 'xts_' . $slug . '_license_active' );
		delete_option( 'xts_' . $slug . '_dev_domain' );
		delete_option( 'xts_all_themes_license' );
	}

	/**
	 * Refresh the system information.
	 *
	 * @since 1.0
	 */
	private function refresh() {
		$this->api = new Api_Client();

		$response = $this->api->refresh();

		if ( isset( $response['success'] ) && true === $response['success'] && $response['license_data'] ) {
			update_option( 'xts_' . XTS_THEME_SLUG . '_license_data', $response['license_data'] );
			$this->notices->add_success( 'Your license information is refreshed.' );
		} elseif ( isset( $response['errors'] ) ) {
			$this->notices->add_error( $response['errors'] );
		} else {
			$this->notices->add_error( 'API call error.' );
		}
	}
}
