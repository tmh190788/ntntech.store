<?php
/**
 * Import the dummy content functions.
 *
 * @package xts
 */

namespace XTS\Modules;

use XTS\Framework\Dashboard;
use XTS\Framework\Module;
use XTS\Framework\Plugin_Activation;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Import the dummy content functions class.
 *
 * @since 1.0.0
 */
class Dummy_Content extends Module {
	/**
	 * Init the module.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->hooks();
		$this->include_files();
	}

	/**
	 * Set up all actions.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'init', array( $this, 'switch_theme' ), 100 );
	}

	/**
	 * Include module files.
	 *
	 * @since 1.0.0
	 */
	public function include_files() {
		xts_get_file( 'framework/modules/dummy-content/class-import' );
	}

	/**
	 * Custom switch theme function.
	 *
	 * @since 1.0.0
	 */
	public function switch_theme() {
		if ( isset( $_GET['page'] ) && 'xts_import' === $_GET['page'] && isset( $_GET['action'] ) && 'activate' === $_GET['action'] ) {
			check_admin_referer( 'switch-theme_' . $_GET['stylesheet'] ); // phpcs:ignore
			$theme = wp_get_theme( $_GET['stylesheet'] ); // phpcs:ignore

			if ( ! $theme->exists() || ! $theme->is_allowed() ) {
				wp_die( '<h1>' . esc_html__( 'Something went wrong.', 'xts-theme' ) . '</h1><p>' . esc_html__( 'The requested theme does not exist.', 'xts-theme' ) . '</p>', 403 );
			}

			switch_theme( $theme->get_stylesheet() );

			wp_safe_redirect( admin_url( 'admin.php?page=xts_import&activated=true' ) );

			exit;
		}
	}

	/**
	 * Register JS scripts.
	 *
	 * @since 1.0.0
	 */
	public function scripts() {
		wp_register_script( 'xts-import', XTS_FRAMEWORK_URL . '/assets/js/import.js', array(), XTS_VERSION, true );
	}

	/**
	 * Is data imported.
	 *
	 * @since 1.0.0
	 */
	public function is_data_imported() {
		return get_option( 'xts_imported_data' );
	}

	/**
	 * Get theme name.
	 *
	 * @since 1.0.0
	 */
	public function get_imported_data_theme_name() {
		$data       = get_option( 'xts_imported_data' );
		$theme_name = '';

		if ( isset( $data['theme_name'] ) ) {
			$theme_name = ucfirst( $data['theme_name'] );
		}

		return $theme_name;
	}

	/**
	 * Import form interface.
	 *
	 * @since 1.0.0
	 */
	public function import() {
		wp_enqueue_script( 'xts-import' );
		$dashboard        = Dashboard::get_instance();
		$plugins          = Plugin_Activation::get_instance();
		$required_plugins = $plugins->get_required_plugins_to_activate();
		$class            = $this->is_data_imported() ? ' xts-imported' : '';
		$link_class       = $this->is_data_imported() || $required_plugins ? ' xts-disabled' : '';

		?>
		<?php if ( isset( $_GET['activated'] ) ) : // phpcs:ignore ?>
			<div class="xts-options-message xts-notice xts-success">
				<?php esc_html_e( 'New theme activated.', 'xts-theme' ); ?>

				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Visit site', 'xts-theme' ); ?>
				</a>
			</div>
		<?php endif; ?>

		<div class="xts-row xts-dummy-content">
			<div class="xts-col xts-col-6">
				<div class="xts-dashboard-box xts-dummy-box<?php echo esc_attr( $class ); ?>">
					<div class="xts-dashboard-box-header">
						<div class="xts-dashboard-box-header-inner">
							<h3>
								<?php esc_html_e( 'Import the dummy content', 'xts-theme' ); ?>
							</h3>

							<p>
								<?php esc_html_e( 'Use our one-click dummy content importer mechanism', 'xts-theme' ); ?>
							</p>
						</div>
						<div class="xts-dummy-status xts-label xts-disable">
							<?php esc_html_e( 'Not imported', 'xts-theme' ); ?>
						</div>
						<div class="xts-dummy-status xts-label xts-success">
							<?php esc_html_e( 'Imported', 'xts-theme' ); ?>
						</div>
					</div>

					<div class="xts-dummy-response"></div>

					<?php if ( $required_plugins ) : ?>
						<?php foreach ( $required_plugins as $plugin ) : ?>
							<div class="xts-notice xts-info">
								<?php echo esc_html( $plugin['name'] . ' is required to activate.' ); ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>

					<div class="xts-dashboard-box-content xts-row">
						<div class="xts-col xts-col-xxl-7">
							<div class="xts-dummy-preview">
								<img src="<?php echo esc_url( XTS_THEME_URL . '/theme/dummy-content/base/preview.jpg' ); ?>" alt="<?php esc_attr_e( 'preview', 'xts-theme' ); ?>">
							</div>
						</div>

						<div class="xts-col xts-col-xxl-5">
							<h4>
								<?php esc_html_e( 'What is included', 'xts-theme' ); ?>
							</h4>

							<ul class="xts-list">
								<li>
									<?php esc_html_e( 'Home page(s)', 'xts-theme' ); ?>
								</li>

								<li>
									<?php esc_html_e( 'Sample posts', 'xts-theme' ); ?>
								</li>

								<li>
									<?php esc_html_e( 'Images', 'xts-theme' ); ?>
								</li>

								<li>
									<?php esc_html_e( 'Navigation menus', 'xts-theme' ); ?>
								</li>

								<li>
									<?php esc_html_e( 'Sidebars widgets', 'xts-theme' ); ?>
								</li>
							</ul>

							<div class="xts-warning">
								<p>
									<?php esc_html_e( 'Importing the dummy content may replace some of your existing configuration, content and widgets. It is recommended to import only on a fresh WordPress installation.', 'xts-theme' ); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="xts-dashboard-box-footer">
						<div class="xts-dashboard-box-footer-actions xts-dummy-actions">
							<a href="#" id="xts-submit" class="xts-btn xts-btn-primary xts-btn-shadow xts-size-l<?php echo esc_attr( $link_class ); ?>">
								<?php esc_html_e( 'Import', 'xts-theme' ); ?>
							</a>

							<a href="#" id="xts-clear" class="xts-btn xts-btn-disable xts-size-l <?php echo ! $this->is_data_imported() ? 'xts-disabled' : ''; ?>">
								<?php echo sprintf( __( 'Clear <span>%s</span> data*', 'xts-theme' ), esc_html( $this->get_imported_data_theme_name() ) ); // phpcs:ignore ?>
							</a>

							<div class="xts-description">
								<?php esc_html_e( '* You can clear your website from previously imported dummy content', 'xts-theme' ); ?>
							</div>
						</div>

						<div class="xts-dummy-progress-bar" data-progress="0">
							<div></div>
							<span class="xts-dummy-progress-bar-count">0%</span>
						</div>
					</div>

					<?php if ( $dashboard->is_setup() ) : ?>
						<div class="xts-dashboard-box-footer xts-setup-wizard-footer">
							<?php $dashboard->setup_back_link( 'xts_plugins' ); ?>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_finish_setup&xts_setup' ) ); ?>" class="xts-inline-btn xts-next-btn xts-skip-btn xts-btn-primary xts-size-l">
								<?php echo esc_html__( 'Skip', 'xts-theme' ); ?>
							</a>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_finish_setup&xts_setup' ) ); ?>" class="xts-btn xts-btn-success xts-next-btn xts-size-l">
								<?php echo esc_html__( 'Done', 'xts-theme' ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( ! $dashboard->is_setup() ) : ?>
				<div class="xts-col xts-col-6">
					<div class="xts-dashboard-box xts-dummy-box">
						<div class="xts-dashboard-box-header">
							<div class="xts-dashboard-box-header-inner">
								<h3>
									<?php esc_html_e( 'Import additional pages', 'xts-theme' ); ?>
								</h3>

								<p>
									<?php esc_html_e( 'Basic pages and elements for any website', 'xts-theme' ); ?>
								</p>
							</div>
						</div>

						<div class="xts-dummy-response">
							<?php if ( ! $this->is_data_imported() ) : ?>
								<div class="xts-notice xts-info">
									<?php esc_attr_e( 'You need to import the base content to be able to import additional pages.', 'xts-theme' ); ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="xts-dashboard-box-content xts-row">
							<div class="xts-col xts-col-xxl-7">
								<div class="xts-dummy-preview xts-dummy-pages-preview">
									<img src="<?php echo esc_url( XTS_THEME_URL . '/theme/dummy-content/about-me-1/preview.jpg' ); ?>" alt="<?php esc_attr_e( 'preview', 'xts-theme' ); ?>">
								</div>
							</div>

							<div class="xts-col xts-col-xxl-5">
								<p>
									<select name="xts_additional_pages" class="xts-additional-pages xts-select xts-select2" data-base-url="<?php echo esc_url( XTS_THEME_URL . '/theme/dummy-content/' ); ?>">
										<option value="">
											<?php esc_html_e( 'Select', 'xts-theme' ); ?>
										</option>

										<?php if ( xts_get_config( 'additional-pages', 'theme' ) ) : ?>
											<?php foreach ( xts_get_config( 'additional-pages', 'theme' ) as $page ) : ?>
												<option value="<?php echo esc_attr( $page['slug'] ); ?>">
													<?php echo esc_html( $page['title'] ); ?>
												</option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
								</p>

								<p>
									<?php esc_html_e( 'This interface allows you to import additional basic pages like contact us, about us, FaQs, and others. It also includes pages with our basic elements like titles, infoboxes, widgets, etc.', 'xts-theme' ); ?>
								</p>

								<p class="xts-view-page-area"></p>
							</div>
						</div>
						<div class="xts-dashboard-box-footer">
							<div class="xts-dashboard-box-footer-actions xts-dummy-actions">
								<a id="xts-import-page" class="xts-btn xts-btn-primary xts-btn-shadow xts-size-l <?php echo ! $this->is_data_imported() ? 'xts-disabled' : ''; ?>">
									<?php esc_html_e( 'Import page', 'xts-theme' ); ?>
								</a>
							</div>

							<div class="xts-dummy-progress-bar" data-progress="0">
								<div></div>
								<span class="xts-dummy-progress-bar-count">0%</span>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
