<?php
/**
 * Plugin activation class.
 *
 * @package xts
 */

namespace XTS\Framework;

use XTS\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Dashboard class
 *
 * @since 1.0.0
 */
class Plugin_Activation extends Singleton {
	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_filter( 'tgmpa_load', array( $this, 'tgmpa_load' ), 10, 1 );
		add_action( 'wp_ajax_xts_deactivate_plugin', array( $this, 'ajax_deactivate_plugin' ) );
		add_action( 'wp_ajax_xts_check_plugins', array( $this, 'ajax_check_plugin' ) );
	}

	/**
	 * Deactivate plugin.
	 *
	 * @since 1.0.0
	 */
	public function ajax_deactivate_plugin() {
		$plugins = $this->get_plugins();
		$tgmpa   = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );

		if ( ! $plugins ) {
			wp_send_json(
				array(
					'message' => esc_html__( 'Plugins list is empty.', 'xts-theme' ),
					'status'  => 'error',
				)
			);
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json(
				array(
					'message' => esc_html__( 'You not have access.', 'xts-theme' ),
					'status'  => 'error',
				)
			);
		}

		if ( is_multisite() && is_plugin_active_for_network( $plugins[ $_POST['xts_plugin'] ]['file_path'] ) ) { // phpcs:ignore
			wp_send_json(
				array(
					'message' => esc_html__( 'You cannot deactivate the plugin on a multisite.', 'xts-theme' ),
					'status'  => 'error',
				)
			);
		}

		if ( isset( $_POST['xts_plugin'] ) && $tgmpa->is_active_plugin( $_POST['xts_plugin'] ) ) { // phpcs:ignore
			deactivate_plugins( $plugins[ $_POST['xts_plugin'] ]['file_path'] ); // phpcs:ignore
		}

		wp_send_json(
			array(
				'data'   => $plugins[ $_POST['xts_plugin'] ]['status'], // phpcs:ignore
				'status' => 'success',
			)
		);
	}

	/**
	 * Check plugin.
	 *
	 * @since 1.0.0
	 */
	public function ajax_check_plugin() {
		$plugins = $this->get_plugins();

		if ( ! $plugins ) {
			wp_send_json(
				array(
					'message' => esc_html__( 'Plugins list is empty.', 'xts-theme' ),
					'status'  => 'error',
				)
			);
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json(
				array(
					'message' => esc_html__( 'You not have access.', 'xts-theme' ),
					'status'  => 'error',
				)
			);
		}

		wp_send_json(
			array(
				'data'   => array(
					'status'           => $plugins[ $_POST['xts_plugin'] ]['status'], // phpcs:ignore
					'version'          => $plugins[ $_POST['xts_plugin'] ]['version'], // phpcs:ignore
					'required_plugins' => count( $this->get_required_plugins_to_activate() ) > 0 ? 'has_required' : 'no',
					'is_all_activated' => $this->is_all_activated() ? 'yes' : 'no',
				),
				'status' => 'success',
			)
		);
	}

	/**
	 * Load TGM.
	 *
	 * @since 1.0.0
	 */
	public function tgmpa_load() {
		return is_admin() || current_user_can( 'install_themes' );
	}

	/**
	 * Get plugins list array.
	 *
	 * @since 1.0.0
	 */
	public function get_plugins() {
		$tgmpa             = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		$tgmpa_plugins     = $tgmpa->plugins;
		$installed_plugins = get_plugins();

		$plugins = array();

		foreach ( $tgmpa_plugins as $slug => $plugin ) {
			$plugins[ $slug ]                   = $plugin;
			$plugins[ $slug ]['activate_url']   = $this->get_action_url( $slug, 'activate' );
			$plugins[ $slug ]['update_url']     = $this->get_action_url( $slug, 'update' );
			$plugins[ $slug ]['deactivate_url'] = '';

			if ( isset( $installed_plugins[ $plugin['file_path'] ]['Version'] ) ) {
				$plugins[ $slug ]['version'] = $installed_plugins[ $plugin['file_path'] ]['Version'];
			}

			if ( ! $tgmpa->is_plugin_installed( $slug ) ) {
				$plugins[ $slug ]['status'] = 'install';
			} else {
				if ( $tgmpa->does_plugin_have_update( $slug ) ) {
					$plugins[ $slug ]['status'] = 'update';
				} elseif ( $tgmpa->can_plugin_activate( $slug ) ) {
					$plugins[ $slug ]['status'] = 'activate';
				} elseif ( $tgmpa->does_plugin_require_update( $slug ) ) {
					$plugins[ $slug ]['status'] = 'require_update';
				} else {
					$plugins[ $slug ]['status'] = 'deactivate';
				}
			}
		}

		$order = array(
			'xts-theme-core',
			'elementor',
			'woocommerce',
			'revslider',
			'contact-form-7',
			'mailchimp-for-wp',
		);

		if ( xts_is_build_for_space() ) {
			$order = array_flip( $order );
			unset( $order['xts-theme-core'] );
			$order = array_flip( $order );
		}

		if ( ! isset( $plugins['woocommerce'] ) ) {
			$order = array_flip( $order );
			unset( $order['woocommerce'] );
			$order = array_flip( $order );
		}

		return array_replace( array_flip( $order ), $plugins );
	}

	/**
	 * Get required plugins to activate.
	 *
	 * @since 1.0.0
	 */
	public function get_required_plugins_to_activate() {
		$plugins = $this->get_plugins();
		$tgmpa   = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		$output  = array();

		foreach ( $plugins as $slug => $plugin ) {
			if ( ! $tgmpa->is_active_plugin( $slug ) && $tgmpa->can_plugin_activate( $slug ) && $plugin['required'] ) {
				$output[] = $plugin;
			}
		}

		return $output;
	}

	/**
	 * Is all plugins activated.
	 *
	 * @since 1.0.0
	 */
	public function is_all_activated() {
		$plugins = $this->get_plugins();
		$tgmpa   = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		$output  = array();

		foreach ( $plugins as $slug => $plugin ) {
			if ( ! $tgmpa->is_active_plugin( $slug ) && $tgmpa->can_plugin_activate( $slug ) ) {
				$output[] = $plugin;
			}
		}

		return count( $output ) === 0;
	}

	/**
	 * Get required plugins to activate.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug   Slug.
	 * @param string $status Status.
	 *
	 * @return string
	 */
	public function get_action_url( $slug, $status ) {
		return wp_nonce_url(
			add_query_arg(
				array(
					'plugin'           => rawurlencode( $slug ),
					'tgmpa-' . $status => $status . '-plugin',
				),
				admin_url( 'themes.php?page=tgmpa-install-plugins' )
			),
			'tgmpa-' . $status,
			'tgmpa-nonce'
		);
	}

	/**
	 * Get required plugins to activate.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Status.
	 *
	 * @return string
	 */
	public function get_action_text( $status ) {
		$text = esc_html__( 'Deactivate', 'xts-theme' );

		if ( 'install' === $status ) {
			$text = esc_html__( 'Install', 'xts-theme' );
		} elseif ( 'update' === $status ) {
			$text = esc_html__( 'Update', 'xts-theme' );
		} elseif ( 'activate' === $status ) {
			$text = esc_html__( 'Activate', 'xts-theme' );
		}

		return $text;
	}

	/**
	 * Template.
	 *
	 * @since 1.0.0
	 */
	public function plugins_template() {
		$plugins   = $this->get_plugins();
		$dashboard = Dashboard::get_instance();
		$classes   = $this->is_all_activated() ? 'xts-all-active' : '';

		?>
		<div class="xts-row">
			<div class="xts-col">
				<div class="xts-dashboard-box xts-plugins xts-tooltips-top xts-tooltips-light <?php echo esc_attr( $classes ); ?>">
					<div class="xts-dashboard-box-header">
						<div class="xts-dashboard-box-header-inner">
							<h3>
								<?php esc_html_e( 'Plugins activation', 'xts-theme' ); ?>
							</h3>

							<p>
								<?php esc_html_e( 'Install and activate plugins for your site', 'xts-theme' ); ?>
							</p>
						</div>
					</div>

					<div class="xts-dashboard-box-content">
						<script>
							var xtsPluginsData = <?php echo wp_json_encode( $plugins ); ?>
						</script>

						<div class="xts-plugin-response"></div>

						<div class="xts-plugin-activation xts-row xts-row-spacing-20">
							<?php foreach ( $plugins as $slug => $plugin ) : ?>
								<div class="xts-plugin-wrapper xts-col xts-col-3">
									<div class="xts-plugin">

										<div class="xts-plugin-content">
											<div class="xts-plugin-header">
												<div>
													<?php if ( $plugin['required'] ) : ?>
														<div class="xts-plugin-label xts-plugin-required">
															<?php esc_html_e( 'Required', 'xts-theme' ); ?>
													</div>
													<?php endif; ?>
												</div>

												<div class="xts-plugin-version">
													<?php echo esc_html( $plugin['version'] ); ?>
												</div>
											</div>
											<div class="xts-plugin-image">
												<img src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/plugins/' . $plugin['slug'] . '.png' ); ?>" alt="<?php esc_attr_e( 'preview', 'xts-theme' ); ?>">
											</div>
											<h4 class="xts-plugin-name">
												<span>
													<?php echo esc_html( $plugin['name'] ); ?>
													<span class="xts-plugin-info">
														<span class="xts-tooltip"><?php echo esc_html( $plugin['tooltip'] ); ?></span>
													</span>
												</span>
											</h4>
										</div>

										<div class="xts-plugin-footer">
											<?php if ( is_multisite() && is_plugin_active_for_network( $plugin['file_path'] ) ) : ?>
												<?php esc_html_e( 'Plugin activated globally.', 'xts-theme' ); ?>
											<?php elseif ( 'require_update' !== $plugin['status'] ) : ?>
												<a class="xts-btn xts-ajax-plugin xts-size-s xts-<?php echo esc_html( $plugin['status'] ); ?>-now"
													href="<?php echo esc_url( $this->get_action_url( $slug, $plugin['status'] ) ); ?>"
													data-plugin="<?php echo esc_attr( $slug ); ?>"
													data-action="<?php echo esc_attr( $plugin['status'] ); ?>">
													<span><?php echo esc_html( $this->get_action_text( $plugin['status'] ) ); ?></span>
												</a>
											<?php else : ?>
												<?php esc_html_e( 'Required Update not Available', 'xts-theme' ); ?>
											<?php endif; ?>
										</div>

									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="xts-dashboard-box-footer xts-setup-wizard-footer">
						<?php if ( $dashboard->is_setup() ) : ?>
							<?php $dashboard->setup_back_link( 'xts_child_theme' ); ?>
						<?php endif; ?>
						<div class="xts-dashboard-box-footer-actions">
							<a href="#" class="xts-btn xts-btn-success xts-size-l xts-ajax-all-plugins">
								<?php esc_html_e( 'Activate all plugins', 'xts-theme' ); ?>
							</a>
							<?php if ( $dashboard->is_setup() ) : ?>
								<?php $dashboard->setup_next_link( 'xts_import', count( $this->get_required_plugins_to_activate() ) > 0 ); ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

Plugin_Activation::get_instance();
