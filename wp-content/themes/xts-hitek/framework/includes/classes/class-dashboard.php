<?php
/**
 * Dashboard class
 *
 * @package xts
 */

namespace XTS\Framework;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Singleton;

/**
 * Dashboard class
 *
 * @since 1.0.0
 */
class Dashboard extends Singleton {
	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'menu_page' ), 100 );
		add_action( 'admin_menu', array( $this, 'submenu_page' ), 130 );
		add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ) );
		add_action( 'admin_init', array( $this, 'prevent_plugins_redirect' ), 1 );
		add_action( 'wp_ajax_xts_install_child_theme', array( $this, 'install_child_theme' ) );
		add_action( 'wp_ajax_nopriv_xts_install_child_theme', array( $this, 'install_child_theme' ) );
	}

	/**
	 * Prevent plugins redirect.
	 *
	 * @since 1.0.0
	 */
	public function prevent_plugins_redirect() {
		delete_transient( 'elementor_activation_redirect' );
		add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );
	}

	/**
	 * Admin body classes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Classes.
	 *
	 * @return string
	 */
	public function admin_body_classes( $classes ) {
		if ( $this->is_setup() ) {
			$classes .= ' xts-setup-wizard';
		}

		return $classes;
	}

	/**
	 * Is setup.
	 *
	 * @since 1.0.0
	 */
	public function is_setup() {
		return isset( $_GET['xts_setup'] ); // phpcs:ignore
	}

	/**
	 * Setup next link.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $target Target.
	 * @param boolean $hide Hide.
	 * @param string  $text Text.
	 */
	public function setup_next_link( $target, $hide = false, $text = '' ) {
		$classes = '';

		if ( ! $text ) {
			$text = esc_html__( 'Next step', 'xts-theme' );
		}

		if ( $hide ) {
			$classes .= ' xts-disabled';
		}

		?>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $target . '&xts_setup' ) ); ?>" class="xts-next-btn xts-btn xts-btn-primary xts-btn-shadow xts-size-l<?php echo esc_attr( $classes ); ?>">
			<?php echo esc_html( $text ); ?>
		</a>
		<?php
	}

	/**
	 * Setup back link.
	 *
	 * @since 1.0.0
	 *
	 * @param string $target Target.
	 */
	public function setup_back_link( $target ) {
		?>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $target . '&xts_setup' ) ); ?>" class="xts-prev-btn xts-inline-btn xts-btn-gray xts-size-l">
			<?php esc_html_e( 'Previous step', 'xts-theme' ); ?>
		</a>
		<?php
	}

	/**
	 * Add menu page
	 */
	public function menu_page() {
		$text = esc_html__( 'Space Dashboard', 'xts-theme' );

		if ( xts_get_opt( 'white_label', '0' ) ) {
			$text = esc_html__( 'Theme Dashboard', 'xts-theme' );
		}

		add_menu_page(
			$text,
			$text,
			'manage_options',
			'xts_dashboard',
			array( $this, 'welcome' ),
			'',
			'55.600'
		);
	}

	/**
	 * Add sub menu pages
	 */
	public function submenu_page() {
		add_submenu_page(
			'xts_dashboard',
			esc_html__( 'Header builder', 'xts-theme' ),
			esc_html__( 'Header builder', 'xts-theme' ),
			'manage_options',
			'xts_header_builder',
			array( $this, 'builder' )
		);

		if ( ( xts_get_opt( 'white_label_dummy_content', '1' ) && xts_get_opt( 'white_label', '0' ) ) || ! xts_get_opt( 'white_label', '0' ) ) {
			add_submenu_page(
				'xts_dashboard',
				esc_html__( 'Dummy content', 'xts-theme' ),
				esc_html__( 'Dummy content', 'xts-theme' ),
				'manage_options',
				'xts_import',
				array( $this, 'import' )
			);
		}

		add_submenu_page(
			'xts_dashboard',
			esc_html__( 'Plugins', 'xts-theme' ),
			esc_html__( 'Plugins', 'xts-theme' ),
			'manage_options',
			'xts_plugins',
			array( $this, 'plugins' )
		);

		if ( ( xts_get_opt( 'white_label_license', '1' ) && xts_get_opt( 'white_label', '0' ) ) || ! xts_get_opt( 'white_label', '0' ) ) {
			add_submenu_page(
				'xts_dashboard',
				esc_html__( 'Activation', 'xts-theme' ),
				esc_html__( 'Activation', 'xts-theme' ),
				'manage_options',
				'xts_activation',
				array( $this, 'activation' )
			);
		}

		add_submenu_page(
			'xts_dashboard',
			esc_html__( 'System status', 'xts-theme' ),
			esc_html__( 'System status', 'xts-theme' ),
			'manage_options',
			'xts_system_status',
			array( $this, 'system_status' )
		);

		if ( $this->is_setup() ) {
			add_submenu_page(
				'xts_dashboard',
				esc_html__( 'Child theme', 'xts-theme' ),
				esc_html__( 'Child theme', 'xts-theme' ),
				'manage_options',
				'xts_child_theme',
				array( $this, 'child_theme' )
			);

			add_submenu_page(
				'xts_dashboard',
				esc_html__( 'Finish setup', 'xts-theme' ),
				esc_html__( 'Finish setup', 'xts-theme' ),
				'manage_options',
				'xts_finish_setup',
				array( $this, 'finish_setup' )
			);
		}

		do_action( 'xts_admin_dashboard_page' );
	}

	/**
	 * Install child theme.
	 */
	public function install_child_theme() {
		$parent_theme_name = XTS_THEME_SLUG;
		$child_theme_name  = $parent_theme_name . '-child';
		$theme_root        = get_theme_root();
		$child_theme_path  = $theme_root . '/xts-' . $child_theme_name;

		if ( ! file_exists( $child_theme_path ) ) {
			$dir = wp_mkdir_p( $child_theme_path );

			if ( ! $dir ) {
				AJAX_Response::send_response( array( 'status' => 'dir_not_exists' ) );
			}

			$child_theme_resource_folder = get_parent_theme_file_path( 'theme/' . $child_theme_name );

			copy( $child_theme_resource_folder . '/functions.php', $child_theme_path . '/functions.php' );
			copy( $child_theme_resource_folder . '/screenshot.jpg', $child_theme_path . '/screenshot.jpg' );
			copy( $child_theme_resource_folder . '/style.css', $child_theme_path . '/style.css' );

			$allowed_themes                               = get_site_option( 'allowedthemes' );
			$allowed_themes[ 'xts-' . $child_theme_name ] = true;
			update_site_option( 'allowedthemes', $allowed_themes );
		}

		if ( $parent_theme_name !== $child_theme_name ) {
			switch_theme( 'xts-' . $child_theme_name );
			AJAX_Response::send_response( array( 'status' => 'success' ) );
		}
	}

	/**
	 * Child theme template.
	 */
	public function child_theme() {
		$this->before();

		?>
		<div class="xts-row">
			<div class="xts-col xts-col-12">
			<div class="xts-dashboard-box xts-child-step <?php echo is_child_theme() ? 'xts-installed' : ''; ?>">
				<div class="xts-child-theme-response"></div>
				<div class="xts-dashboard-box-header">
					<div class="xts-dashboard-box-header-inner">
						<h3 class="xts-welcome-title">
							<?php esc_html_e( 'Child Theme', 'xts-theme' ); ?>
						</h3>
						<p>
							<?php esc_html_e( 'If you are going to make any code customizations', 'xts-theme' ); ?>
						</p>
					</div>
				</div>

				<div class="xts-dashboard-box-content">
					<div class="xts-child-image">
						<img class="xts-child-image-bg" src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/child-theme/anim-bg.svg' ); ?>" alt="<?php esc_attr_e( 'anim-bg', 'xts-theme' ); ?>">
						<img class="xts-child-image-planet" src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/child-theme/anim-planet.svg' ); ?>" alt="<?php esc_attr_e( 'anim-planet', 'xts-theme' ); ?>">
						<span class="xts-child-checkmark"></span>
					</div>
					<p class="xts-child-text">
						<?php esc_html_e( 'If you are going to make changes to the theme source code please use ChildTheme rather than modifying the main theme HTML/CSS/PHP code. This allows the parent theme to receive updates without overwriting your source code changes. Click the button below to create and activate the Child Theme.', 'xts-theme' ); ?>
					</p>
					<a href="#" class="xts-btn xts-btn-gray xts-size-l xts-disabled">
						<?php esc_html_e( 'Child theme installed', 'xts-theme' ); ?>
					</a>
					<a href="#" class="xts-btn xts-btn-success xts-size-l xts-install-child-theme">
						<span><?php esc_html_e( 'Install child theme', 'xts-theme' ); ?></span>
					</a>
				</div>

				<div class="xts-dashboard-box-footer xts-setup-wizard-footer">
					<?php $this->setup_back_link( 'xts_activation' ); ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_plugins&xts_setup' ) ); ?>" class="xts-inline-btn xts-next-btn xts-skip-btn xts-btn-primary xts-size-l">
						<?php echo esc_html__( 'Skip', 'xts-theme' ); ?>
					</a>
					<?php $this->setup_next_link( 'xts_plugins' ); ?>
				</div>
			</div>
		</div>
		<?php

		$this->after();
	}

	/**
	 * Welcome screen interface.
	 */
	public function welcome() {
		if ( $this->is_setup() ) {
			$this->setup_welcome();
			return;
		}

		$current_theme = strtolower( xts_get_theme_info( 'Name' ) );
		$classes       = xts_is_build_for_space() ? 'xts-col-4' : 'xts-col-6';
		$this->before();

		?>

			<div class="xts-row">
				<div class="xts-col xts-col-10">
					<div class="xts-welcome-title-wrap">
						<h3 class="xts-welcome-title">
							<?php if ( xts_get_opt( 'white_label', '0' ) ) : ?>
								<?php esc_html_e( 'Welcome!', 'xts-theme' ); ?>
							<?php else : ?>
								<?php esc_html_e( 'Welcome to XTemos Space!', 'xts-theme' ); ?>
							<?php endif; ?>
						</h3>
						<?php $this->activation_status(); ?>
					</div>

					<p>
						<?php esc_html_e( 'Congratulations! You have successfully installed our theme on your WordPress website. Now you can start creating your amazing website with a help of our theme. It provides you with a full control on your website layout, style, colors, typography and much more. Check our next steps guide and enjoy creating your new project. Feel free to contact us if you will have any questions and check our other products. Good luck!', 'xts-theme' ); ?>
					</p>
				</div>

				<?php if ( ! xts_get_opt( 'white_label', '0' ) ) : ?>
					<div class="xts-col xts-col-2">
						<div class="xts-theme-info">
							<div class="xts-theme-info-inner">
								<div class="xts-theme-version">
									<?php echo esc_html( 'v ' . XTS_VERSION ); ?>
								</div>

								<div class="xts-theme-name">
									<?php echo esc_html( xts_get_theme_info( 'Name' ) ); ?>
								</div>

								<div>
									<?php echo esc_html__( 'by', 'xts-theme' ); ?>
								</div>
							</div>
							<div class="xts-theme-author">
								<img src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/xtemos-logo-black.jpg' ); ?>" alt="<?php esc_attr_e( 'xtemos logo', 'xts-theme' ); ?>">
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ( xts_get_opt( 'white_label_dummy_content', '1' ) && xts_get_opt( 'white_label', '0' ) ) || ! xts_get_opt( 'white_label', '0' ) ) : ?>
					<div class="xts-col <?php echo esc_attr( $classes ); ?>">
						<div class="xts-welcome-info-box xts-dummy-content">
							<?php if ( 'finish' !== get_option( 'xts_setup_status_' . $current_theme ) && ! get_option( 'xts_imported_data' ) ) : ?>
								<h3>
									<?php esc_html_e( 'Run the setup wizard', 'xts-theme' ); ?>
								</h3>

								<p>
									<?php esc_html_e( 'An easy tool to activate our theme, required plugins and import the dummy content including pages, products, posts and settings.', 'xts-theme' ); ?>
								</p>

								<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_dashboard&xts_setup' ) ); ?>" class="xts-btn-primary xts-btn-bordered">
									<?php esc_html_e( 'Setup wizard', 'xts-theme' ); ?>
								</a>
							<?php else : ?>
								<?php if ( get_option( 'xts_imported_data' ) ) : ?>
									<div class="xts-dummy-status xts-label xts-success">
										<?php esc_html_e( 'Imported', 'xts-theme' ); ?>
									</div>
								<?php else : ?>
									<div class="xts-dummy-status xts-label xts-info">
										<?php esc_html_e( 'Not imported', 'xts-theme' ); ?>
									</div>
								<?php endif; ?>

								<h3>
									<?php esc_html_e( 'Install dummy content', 'xts-theme' ); ?>
								</h3>

								<p>
									<?php esc_html_e( 'If you don’t know what to start with, our dummy content is for you. Import sample pages, products, posts and settings.', 'xts-theme' ); ?>
								</p>

								<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_import' ) ); ?>" class="xts-btn-primary xts-btn-bordered">
									<?php esc_html_e( 'To dummy content', 'xts-theme' ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ! xts_get_opt( 'white_label', '0' ) ) : ?>
					<div class="xts-col <?php echo esc_attr( $classes ); ?>">
						<div class="xts-welcome-info-box xts-need-help">
							<h3>
								<?php esc_html_e( 'Need help?', 'xts-theme' ); ?>
							</h3>

							<ul class="xts-list">
								<li>
									<a href="<?php echo esc_url( XTS_SPACE_URL . 'knowledge-base' ); ?>">
										<?php esc_html_e( 'Documentation', 'xts-theme' ); ?>
									</a>
								</li>

								<li>
									<a href=<?php echo esc_url( XTS_SPACE_URL . 'faqs' ); ?>>
										<?php esc_html_e( 'Frequently asked questions', 'xts-theme' ); ?>
									</a>
								</li>

								<li>
									<a href=<?php echo esc_url( XTS_SPACE_URL . 'video' ); ?>>
										<?php esc_html_e( 'Video tutorials', 'xts-theme' ); ?>
									</a>
								</li>

								<li>
									<a href=<?php echo esc_url( xts_get_link_by_key( 'welcome_forum' ) ); ?>>
										<?php esc_html_e( 'Support forum', 'xts-theme' ); ?>
									</a>
								</li>
							</ul>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ! xts_get_opt( 'white_label', '0' ) && xts_is_build_for_space() ) : ?>
					<div class="xts-col <?php echo esc_attr( $classes ); ?>">
						<div class="xts-welcome-info-box xts-other-themes">
							<h3>
								<?php esc_html_e( 'Check out our other themes', 'xts-theme' ); ?>
							</h3>

							<p>
								<?php esc_html_e( 'With our premium subscription you are able to use all our themes and get new themes every month. Visit our store and you will be impressed!', 'xts-theme' ); ?>
							</p>

							<a href="<?php echo esc_url( xts_get_link_by_key( 'welcome_all_themes' ) ); ?>" class="xts-btn-success xts-btn-bordered">
								<?php esc_html_e( 'See all themes', 'xts-theme' ); ?>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ( xts_get_opt( 'white_label_license', '1' ) && xts_get_opt( 'white_label', '0' ) ) || ! xts_get_opt( 'white_label', '0' ) ) : ?>
					<div class="xts-col xts-col-12">
						<p class="xts-description">
							<?php echo wp_kses( 'Don’t forget to <a href="' . esc_url( admin_url( 'admin.php?page=xts_activation' ) ) . '">activate</a> our theme with your License key to unlock live update and other features.', xts_get_allowed_html() ); ?>
						</p>
					</div>
				<?php endif; ?>
			</div>
		<?php

		$this->after();
	}

	/**
	 * Welcome screen interface.
	 */
	public function setup_welcome() {
		$this->before();

		?>
		<div class="xts-row">
			<div class="xts-col xts-col-12">
			<div class="xts-dashboard-box xts-welcome-step">
				<div class="xts-img-layer">
					<img class="xts-img-left-corner-bg" src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/step-1-bg-left.svg' ); ?>" alt="<?php esc_attr_e( 'step-1-bg-left', 'xts-theme' ); ?>">
					<img class="xts-img-right-corner-bg" src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/step-1-bg-right.svg' ); ?>" alt="<?php esc_attr_e( 'step-1-bg-right', 'xts-theme' ); ?>">
					<img class="xts-img-small-planet" src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/step-1-planet-1.svg' ); ?>" alt="<?php esc_attr_e( 'step-1-planet-1', 'xts-theme' ); ?>">
					<img class="xts-img-large-planet" src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/step-1-planet-2.svg' ); ?>" alt="<?php esc_attr_e( 'step-1-planet-2', 'xts-theme' ); ?>">
				</div>
				<div class="xts-dashboard-box-content">
					<p class="xts-welcome-logo">
						<img src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/xt-min-logo.svg' ); ?>" alt="<?php esc_attr_e( 'logo', 'xts-theme' ); ?>">
					</p>
					<p class="xts-welcome-subtitle">
						<?php esc_html_e( 'Thanks for choosing our theme', 'xts-theme' ); ?>
					</p>

					<h3 class="xts-welcome-title">
						<?php if ( xts_is_build_for_space() ) : ?>
							<?php esc_html_e( 'Welcome to', 'xts-theme' ); ?><span class="xts-color-primary"> XTemos Space!</span>
						<?php else : ?>
							<span class="xts-color-primary"><?php echo esc_html( ucfirst( XTS_THEME_SLUG ) ); ?></span> <?php echo esc_html__( 'Setup Wizard', 'xts-theme' ); ?>
						<?php endif; ?>
					</h3>

					<p>
						<?php esc_html_e( 'Congratulations! You have successfully installed our theme on your WordPress website. This setup wizard will help you to activate our theme, all required plugins and import the dummy content just in a few clicks. You can skip it and do everything manually.', 'xts-theme' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'Click "Start wizard" and enjoy creating your new project. Feel free to contact us if you will have any questions and check our other products.', 'xts-theme' ); ?>
					</p>
					<p class="xts-signature">
						<span><?php esc_html_e( 'Good luck!', 'xts-theme' ); ?></span>
						<img src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/autograph.svg' ); ?>" alt="<?php esc_attr_e( 'autograph', 'xts-theme' ); ?>">
					</p>
				</div>

				<div class="xts-dashboard-box-footer xts-setup-wizard-footer">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_dashboard' ) ); ?>" class="xts-btn-bordered xts-btn-primary">
						<?php echo esc_html__( 'Not now', 'xts-theme' ); ?>
					</a>

					<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_activation&xts_setup' ) ); ?>" class="xts-btn xts-next-btn xts-btn-success xts-size-l">
						<?php echo esc_html__( 'Start wizard', 'xts-theme' ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php

		$this->after();
	}

	/**
	 * Builder menu page content
	 */
	public function builder() {
		$this->before();

		$this->header( esc_html__( 'Header builder', 'xts-theme' ), esc_html__( 'Customize your headers layout, colors, elements etc.', 'xts-theme' ) );

		?>
			<div class="xhb-header-builder-wrapper xts-row">
				<div id="xhb-header-builder" class="xts-col-auto">
					<div class="xts-notice xts-info">
						<?php esc_attr_e( 'The header builder cannot be loaded correctly. Probably, there is some JS conflict with some of the installed plugins or some issue with the header builder script. Check your JS console to debug this. Try to disable all external plugins and be sure that you have the latest version of the theme installed and then check again.', 'xts-theme' ); ?>
					</div>
				</div>

				<div class="xhb-header-builder-sidebar xts-col-auto">
					<?php if ( ! xts_get_opt( 'white_label', '0' ) ) : ?>
						<div class="xts-dashboard-video">
							<div class="xts-info">
								<span><?php esc_html_e( 'How to create a header with our header builder.', 'xts-theme' ); ?></span>

								<a href="https://space.xtemos.com/topic/header-builder" class="xts-inline-btn" target="_blank">
									<?php esc_html_e( 'Read tutorial', 'xts-theme' ); ?>
								</a>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php

		$this->after();
	}

	/**
	 * Import content
	 */
	public function import() {
		$this->before();

		$this->header( esc_html__( 'Dummy content', 'xts-theme' ), esc_html__( 'Import our dummy content with pages, placeholder images, posts and settings', 'xts-theme' ) );

		Modules::get( 'dummy-content' )->import();

		$this->after();
	}

	/**
	 * Support content
	 */
	public function support() {
		$this->before();

		$this->header( esc_html__( 'Support information', 'xts-theme' ), esc_html__( 'Import our dummy content with pages, placeholder images, posts and settings.', 'xts-theme' ) );

		?>
			Support information
		<?php

		$this->after();
	}

	/**
	 * Activation menu page content
	 */
	public function activation() {
		$this->before();

		$this->header( esc_html__( 'Theme activation', 'xts-theme' ), esc_html__( 'Activate your theme to unlock live autoupdates and other features', 'xts-theme' ) );

		Activation::get_instance()->form();

		$this->after();
	}

	/**
	 * Plugins menu.
	 */
	public function plugins() {
		$this->before();

		$this->header( esc_html__( 'Theme plugins', 'xts-theme' ), esc_html__( 'Install, activate, update theme-related plugins', 'xts-theme' ) );

		Plugin_Activation::get_instance()->plugins_template();

		$this->after();
	}

	/**
	 * Finish setup menu.
	 */
	public function finish_setup() {
		$theme_slug = strtolower( xts_get_theme_info( 'Name' ) );
		$this->before();

		$this->header( esc_html__( 'Finish setup', 'xts-theme' ), esc_html__( 'Import our dummy content with pages, placeholder images, posts and settings.', 'xts-theme' ) );

		update_option( 'xts_setup_status_' . $theme_slug, 'finish' );

		?>
		<div class="xts-row">
			<div class="xts-col xts-col-12">
				<div class="xts-dashboard-box xts-finish-step">
					<div class="xts-img-layer">
						<img class="xts-img-small-planet" src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/step-1-planet-1.svg' ); ?>" alt="<?php esc_attr_e( 'planet-1', 'xts-theme' ); ?>">
						<img class="xts-img-saturn-planet" src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/step-6-planet-1.svg' ); ?>" alt="<?php esc_attr_e( 'planet-2', 'xts-theme' ); ?>">
					</div>
					<div class="xts-dashboard-box-content">
						<p class="xts-welcome-logo">
							<img src="<?php echo esc_url( XTS_ASSETS_IMAGES_URL . '/wizard/xt-min-logo.svg' ); ?>" alt="<?php esc_attr_e( 'logo', 'xts-theme' ); ?>">
						</p>
						<p class="xts-welcome-subtitle">
							<?php esc_html_e( 'Theme installation complete', 'xts-theme' ); ?>
						</p>
						<h3 class="xts-welcome-title">
							<?php esc_html_e( 'Start your project with', 'xts-theme' ); ?><span class="xts-color-primary"> <?php echo esc_html( ucfirst( XTS_THEME_SLUG ) ); ?>!</span>
						</h3>
						<p>
							<?php esc_html_e( 'Congratulations! You have successfully installed our theme. Now you can start creating your amazing website with a help of our theme. It provides you with a full control on your website layout style.', 'xts-theme' ); ?>
						</p>
						<div class="xts-useful-links">
							<div class="xts-row xts-row-spacing-20">
								<div class="xts-col xts-col-6">
									<a class="xts-inline-btn xts-size-s" href="<?php echo esc_url( admin_url( 'admin.php?page=xtemos_options' ) ); ?>">
										<?php esc_html_e( 'Theme Settings', 'xts-theme' ); ?>
									</a>
								</div>
								<div class="xts-col xts-col-6">
									<a class="xts-inline-btn xts-size-s" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">
										<?php esc_html_e( 'Edit pages', 'xts-theme' ); ?>
									</a>
								</div>
								<?php if ( xts_is_woocommerce_installed() ) : ?>
									<div class="xts-col xts-col-6">
										<a class="xts-inline-btn xts-size-s" href="<?php echo esc_url( wc_admin_url( '&path=/setup-wizard' ) ); ?>">
											<?php esc_html_e( 'WooCommerce setup', 'xts-theme' ); ?>
										</a>
									</div>
								<?php endif; ?>
								<div class="xts-col xts-col-6 xts-col-fill">
									<a class="xts-inline-btn xts-size-s xts-col-fill" href="<?php echo esc_url( admin_url( 'admin.php?page=xts_header_builder' ) ); ?>">
										<?php esc_html_e( 'Header builder', 'xts-theme' ); ?>
									</a>
								</div>
							</div>
						</div>
						<a class="xts-btn xts-btn-success xts-size-l" href="<?php echo esc_url( get_home_url() ); ?>" target="_blank">
							<?php esc_html_e( 'Visit my site', 'xts-theme' ); ?>
						</a>
					</div>
			</div>
		</div>
		<?php

		$this->after();
	}

	/**
	 * System status menu page content.
	 */
	public function system_status() {
		$this->before();

		$this->header( esc_html__( 'System status', 'xts-theme' ), esc_html__( 'Information about your WordPress and basic server configuration parameters', 'xts-theme' ) );

		?>
			<div class="xts-dashboard-box xts-system-status xts-table xts-even">
				<div>
					<div>
						<?php esc_html_e( 'Theme Name', 'xts-theme' ); ?>:
					</div>
					<div>
						<?php if ( xts_get_opt( 'white_label', '0' ) ) : ?>
							<?php echo esc_html( xts_get_opt( 'white_label_theme_name' ) ); ?>
						<?php else : ?>
							<?php echo esc_html( xts_get_theme_info( 'Name' ) ); ?>
						<?php endif; ?>
					</div>
				</div>

				<div>
					<div>
						<?php esc_html_e( 'Theme Version', 'xts-theme' ); ?>:
					</div>
					<div>
						<?php echo esc_html( XTS_VERSION ); ?>
					</div>
				</div>

				<div>
					<div>
						<?php esc_html_e( 'WP Version', 'xts-theme' ); ?>:
					</div>
					<div>
						<?php echo esc_html( get_bloginfo( 'version' ) ); ?>
					</div>
				</div>

				<div>
					<div>
						<?php esc_html_e( 'WP Multisite', 'xts-theme' ); ?>:
					</div>
					<div>
						<?php echo is_multisite() ? esc_html__( 'Yes', 'xts-theme' ) : esc_html__( 'No', 'xts-theme' ); ?>
					</div>
				</div>

				<div>
					<div>
						<?php esc_html_e( 'WP Debug Mode', 'xts-theme' ); ?>:
					</div>
					<div>
						<?php echo defined( 'WP_DEBUG' ) && WP_DEBUG ? esc_html__( 'Enabled', 'xts-theme' ) : esc_html__( 'Disabled', 'xts-theme' ); ?>
					</div>
				</div>

				<div>
					<div>
						<?php esc_html_e( 'PHP Version', 'xts-theme' ); ?>:
					</div>
					<div>
						<?php if ( version_compare( PHP_VERSION, '7.2', '<' ) ) : ?>
							<div class="xts-status-error">
								<?php echo esc_html( PHP_VERSION ); ?>

								<span>
									<?php esc_html_e( 'Minimum required PHP version 7.2', 'xts-theme' ); ?>
								</span>
							</div>
						<?php else : ?>
							<?php echo esc_html( PHP_VERSION ); ?>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( function_exists( 'ini_get' ) ) : ?>
					<div>
						<div>
							<?php $post_max_size = ini_get( 'post_max_size' ); ?>

							<?php esc_html_e( 'PHP Post Max Size', 'xts-theme' ); ?>:
						</div>

						<div>
							<?php if ( wp_convert_hr_to_bytes( $post_max_size ) < 64000000 ) : ?>
								<div class="xts-status-error">
									<?php echo esc_html( $post_max_size ); ?>

									<span>
										<?php esc_html_e( 'Minimum required value 64M.', 'xts-theme' ); ?>
									</span>
								</div>
							<?php else : ?>
								<?php echo esc_html( $post_max_size ); ?>
							<?php endif; ?>
						</div>
					</div>

					<div>
						<div>
							<?php $max_execution_time = ini_get( 'max_execution_time' ); ?>
							<?php esc_html_e( 'PHP Time Limit', 'xts-theme' ); ?>:
						</div>

						<div>
							<?php if ( $max_execution_time < 180 ) : ?>
								<div class="xts-status-error">
									<?php echo esc_html( $max_execution_time ); ?>

									<span>
										<?php esc_html_e( 'Minimum required value 180.', 'xts-theme' ); ?>
									</span>
								</div>
							<?php else : ?>
								<?php echo esc_html( $max_execution_time ); ?>
							<?php endif; ?>
						</div>
					</div>

					<div>
						<div>
							<?php $max_input_vars = ini_get( 'max_input_vars' ); ?>
							<?php esc_html_e( 'PHP Max Input Vars', 'xts-theme' ); ?>:
						</div>

						<div>
							<?php if ( $max_input_vars < 10000 ) : ?>
								<div class="xts-status-error">
									<?php echo esc_html( $max_input_vars ); ?>
									<span>
										<?php esc_html_e( 'Minimum required value 10000.', 'xts-theme' ); ?>
									</span>
								</div>
							<?php else : ?>
								<?php echo esc_html( $max_input_vars ); ?>
							<?php endif; ?>
						</div>
					</div>

					<div>
						<div>
							<?php $memory_limit = ini_get( 'memory_limit' ); ?>
							<?php esc_html_e( 'PHP Memory Limit', 'xts-theme' ); ?>:
						</div>

						<div>
							<?php if ( wp_convert_hr_to_bytes( $memory_limit ) < 128000000 ) : ?>
								<div class="xts-status-error">
									<?php echo esc_html( $memory_limit ); ?>

									<span>
										<?php esc_html_e( 'Minimum required value 128M.', 'xts-theme' ); ?>
									</span>
								</div>
							<?php else : ?>
								<?php echo esc_html( $memory_limit ); ?>
							<?php endif; ?>
						</div>
					</div>

					<div>
						<div>
							<?php $upload_max_filesize = ini_get( 'upload_max_filesize' ); ?>
							<?php esc_html_e( 'PHP Upload Max Size', 'xts-theme' ); ?>:
						</div>
						<div>

							<?php if ( wp_convert_hr_to_bytes( $upload_max_filesize ) < 64000000 ) : ?>
								<div class="xts-status-error">
									<?php echo esc_html( $upload_max_filesize ); ?>

									<span>
										<?php esc_html_e( 'Minimum required value 64M.', 'xts-theme' ); ?>
									</span>
								</div>
							<?php else : ?>
								<?php echo esc_html( $upload_max_filesize ); ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<div>
					<div>
						<?php esc_html_e( 'DOMDocument', 'xts-theme' ); ?>:
					</div>
					<div>
						<?php if ( ! class_exists( 'DOMDocument' ) ) : ?>
							<div class="xts-status-error">
								<?php esc_html_e( 'No', 'xts-theme' ); ?>
							</div>
						<?php else : ?>
							<?php esc_html_e( 'Yes', 'xts-theme' ); ?>
						<?php endif; ?>
					</div>
				</div>

				<div>
					<div>
						<?php esc_html_e( 'Active Plugins', 'xts-theme' ); ?>:
					</div>
					<div>
						<?php if ( is_multisite() ) : ?>
							<?php echo esc_html( count( (array) wp_get_active_and_valid_plugins() ) + count( (array) wp_get_active_network_plugins() ) ); ?>
						<?php else : ?>
							<?php echo esc_html( count( (array) wp_get_active_and_valid_plugins() ) ); ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php
		$this->after();
	}

	/**
	 * Activation status label.
	 */
	public function activation_status() {
		?>
		<?php if ( xts_is_activated_license() ) : ?>
			<div class="xts-theme-activation-status xts-label xts-success">
				<?php esc_html_e( 'Activated', 'xts-theme' ); ?>
			</div>
		<?php else : ?>
			<div class="xts-theme-activation-status xts-label xts-disable">
				<?php esc_html_e( 'Not activated', 'xts-theme' ); ?>
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Section header.
	 *
	 * @param string $title Title.
	 * @param string $subtitle Subtitle.
	 */
	public function header( $title, $subtitle ) {
		if ( $this->is_setup() ) {
			return;
		}

		?>
		<div class="xts-dashboard-header">
			<h2>
				<?php echo esc_html( $title ); ?>
			</h2>

			<p>
				<?php echo esc_html( $subtitle ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Before content
	 */
	public function before() {
		$link_classes = '';
		$is_setup     = $this->is_setup();

		if ( $is_setup && ! ( isset( $_GET['page'] ) && 'xts_finish_setup' === $_GET['page'] ) ) { // phpcs:ignore
			$link_classes .= ' xts-disabled';
		}
		?>
		<div class="xts-dashboard-wrapper">
			<div class="xts-dashboard-tabs">
				<div class="xts-dashboard-tabs-logo">
					<?php if ( xts_get_opt( 'white_label', '0' ) ) : ?>
						<?php echo esc_html( xts_get_opt( 'white_label_theme_name' ) ); ?>
					<?php else : ?>
						<img src="<?php echo esc_url( XTS_THEME_URL . '/images/logo-admin.svg' ); ?>" alt="<?php esc_attr_e( 'logo', 'xts-theme' ); ?>">
					<?php endif; ?>
				</div>

				<ul class="xts-dashboard-tabs-nav">
					<?php if ( $this->is_setup() ) : ?>
						<?php $classes = $is_setup ? 'xts-active' : ''; ?>
						<li class="<?php echo esc_attr( $classes ); ?>">
							<a class="xf-setup-wizard" href="<?php echo esc_url( admin_url( 'admin.php?page=xts_dashboard&xts_setup' ) ); ?>">
								<?php esc_html_e( 'Setup wizard', 'xts-theme' ); ?>
							</a>
						</li>
					<?php else : ?>
						<li class="<?php echo isset( $_GET['page'] ) && ! $is_setup && 'xts_dashboard' === $_GET['page'] ? 'xts-active' : ''; // phpcs:ignore ?>">
							<a class="xf-welcome" href="<?php echo esc_url( admin_url( 'admin.php?page=xts_dashboard' ) ); ?>">
								<?php esc_html_e( 'Welcome', 'xts-theme' ); ?>
							</a>
						</li>
					<?php endif; ?>

					<li class="<?php echo isset( $_GET['page'] ) && ! $is_setup && 'xtemos_options' === $_GET['page'] ? 'xts-active' : ''; // phpcs:ignore ?>">
						<a class="xf-theme-setting<?php echo esc_attr( $link_classes ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=xtemos_options' ) ); ?>">
							<?php esc_html_e( 'Theme settings', 'xts-theme' ); ?>
						</a>
					</li>

					<li class="<?php echo isset( $_GET['page'] ) && ! $is_setup && 'xts_header_builder' === $_GET['page'] ? 'xts-active' : ''; // phpcs:ignore ?>">
						<a class="xf-header-builder<?php echo esc_attr( $link_classes ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=xts_header_builder' ) ); ?>">
							<?php esc_html_e( 'Header builder', 'xts-theme' ); ?>
						</a>
					</li>

					<?php if ( ( xts_get_opt( 'white_label_dummy_content', '1' ) && xts_get_opt( 'white_label', '0' ) ) || ! xts_get_opt( 'white_label', '0' ) ) : ?>
						<li class="<?php echo isset( $_GET['page'] ) && ! $is_setup && 'xts_import' === $_GET['page'] ? 'xts-active' : ''; // phpcs:ignore ?>">
							<a class="xf-dummy-content<?php echo esc_attr( $link_classes ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=xts_import' ) ); ?>">
								<?php esc_html_e( 'Dummy content', 'xts-theme' ); ?>
							</a>
						</li>
					<?php endif; ?>

					<li class="<?php echo isset( $_GET['page'] ) && ! $is_setup && 'xts_plugins' === $_GET['page'] ? 'xts-active' : ''; // phpcs:ignore ?>">
						<a class="xf-plugins<?php echo esc_attr( $link_classes ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=xts_plugins' ) ); ?>">
							<?php esc_html_e( 'Plugins', 'xts-theme' ); ?>
						</a>
					</li>

					<?php if ( ( xts_get_opt( 'white_label_license', '1' ) && xts_get_opt( 'white_label', '0' ) ) || ! xts_get_opt( 'white_label', '0' ) ) : ?>
						<li class="<?php echo isset( $_GET['page'] ) && ! $is_setup && 'xts_activation' === $_GET['page'] ? 'xts-active' : ''; // phpcs:ignore ?>">
							<a class="xf-activation<?php echo esc_attr( $link_classes ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=xts_activation' ) ); ?>">
								<?php esc_html_e( 'Activation', 'xts-theme' ); ?>
							</a>
						</li>
					<?php endif; ?>

					<li class="<?php echo isset( $_GET['page'] ) && ! $is_setup && 'xts_system_status' === $_GET['page'] ? 'xts-active' : ''; // phpcs:ignore ?>">
						<a class="xf-system-status<?php echo esc_attr( $link_classes ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=xts_system_status' ) ); ?>">
							<?php esc_html_e( 'System status', 'xts-theme' ); ?>
						</a>
					</li>

					<?php do_action( 'xts_admin_dashboard_nav' ); ?>
				</ul>
			</div>

			<div class="xts-dashboard-content">

			<?php if ( $this->is_setup() ) : ?>
				<?php $this->setup_menu(); ?>
			<?php endif; ?>
		<?php
	}

	/**
	 * Get menu active class.
	 *
	 * @param string $page Page slug.
	 *
	 * @return string
	 */
	public function get_menu_active_class( $page ) {
		if ( isset( $_GET['page'] ) && $_GET['page'] === $page ) { // phpcs:ignore
			return 'xts-active';
		}

		return '';
	}

	/**
	 * Setup menu.
	 */
	public function setup_menu() {
		$activation_classes  = '';
		$child_theme_classes = '';
		$plugins_classes     = '';
		$import_classes      = '';
		$finish_classes      = '';

		if ( isset( $_GET['page'] ) && $_GET['page'] === 'xts_dashboard' ) { // phpcs:ignore
			$activation_classes = 'xts-disabled';
		}

		if ( isset( $_GET['page'] ) && ( $_GET['page'] === 'xts_dashboard' || $_GET['page'] === 'xts_activation' ) ) { // phpcs:ignore
			$child_theme_classes = 'xts-disabled';
		}

		if ( isset( $_GET['page'] ) && ( $_GET['page'] === 'xts_dashboard' || $_GET['page'] === 'xts_activation' || $_GET['page'] === 'xts_child_theme' ) ) { // phpcs:ignore
			$plugins_classes = 'xts-disabled';
		}

		if ( isset( $_GET['page'] ) && ( $_GET['page'] === 'xts_dashboard' || $_GET['page'] === 'xts_activation' || $_GET['page'] === 'xts_plugins' || $_GET['page'] === 'xts_child_theme' ) ) { // phpcs:ignore
			$import_classes = 'xts-disabled';
		}

		if ( isset( $_GET['page'] ) && ( $_GET['page'] === 'xts_dashboard' || $_GET['page'] === 'xts_activation' || $_GET['page'] === 'xts_plugins' || $_GET['page'] === 'xts_import' || $_GET['page'] === 'xts_child_theme' ) ) { // phpcs:ignore
			$finish_classes = 'xts-disabled';
		}

		$activation_classes  .= ' ' . $this->get_menu_active_class( 'xts_activation' );
		$child_theme_classes .= ' ' . $this->get_menu_active_class( 'xts_child_theme' );
		$plugins_classes     .= ' ' . $this->get_menu_active_class( 'xts_plugins' );
		$import_classes      .= ' ' . $this->get_menu_active_class( 'xts_import' );
		$finish_classes      .= ' ' . $this->get_menu_active_class( 'xts_finish_setup' );

		?>
		<div class="xts-setup-wizard-menu <?php echo isset( $_GET['page'] ) && 'xts_finish_setup' === $_GET['page'] ? 'xts-finish' : ''; // phpcs:ignore ?>">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_dashboard&xts_setup' ) ); ?>" class="<?php echo esc_attr( $this->get_menu_active_class( 'xts_dashboard' ) ); ?>">
				<?php esc_html_e( 'Welcome', 'xts-theme' ); ?>
				<span class="xts-menu-dot"></span>
			</a>

			<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_activation&xts_setup' ) ); ?>" class="<?php echo esc_attr( $activation_classes ); ?>">
				<?php esc_html_e( 'Activation', 'xts-theme' ); ?>
				<span class="xts-menu-dot"></span>
			</a>

			<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_child_theme&xts_setup' ) ); ?>" class="<?php echo esc_attr( $child_theme_classes ); ?>">
				<?php esc_html_e( 'Child theme', 'xts-theme' ); ?>
				<span class="xts-menu-dot"></span>
			</a>

			<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_plugins&xts_setup' ) ); ?>" class="<?php echo esc_attr( $plugins_classes ); ?>">
				<?php esc_html_e( 'Plugins', 'xts-theme' ); ?>
				<span class="xts-menu-dot"></span>
			</a>

			<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_import&xts_setup' ) ); ?>" class="<?php echo esc_attr( $import_classes ); ?>">
				<?php esc_html_e( 'Dummy content', 'xts-theme' ); ?>
				<span class="xts-menu-dot"></span>
			</a>

			<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_finish_setup&xts_setup' ) ); ?>" class="<?php echo esc_attr( $finish_classes ); ?>">
				<?php esc_html_e( 'Done', 'xts-theme' ); ?>
				<span class="xts-menu-dot"></span>
			</a>
		</div>
		<?php
	}

	/**
	 * After content.
	 */
	public function after() {
		?>
				</div>
			</div>
		<?php
	}
}
