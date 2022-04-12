<?php
/**
 * Woocommerce my account template functions file
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_wc_my_account_wrapper_start' ) ) {
	/**
	 * My account wrapper start
	 *
	 * @since 1.0.0
	 */
	function xts_wc_my_account_wrapper_start() {
		?>
			<div class="xts-my-account-wrapper">
		<?php
	}

	add_action( 'woocommerce_account_navigation', 'xts_wc_my_account_wrapper_start', 1 );
}

if ( ! function_exists( 'xts_wc_my_account_wrapper_end' ) ) {
	/**
	 * My account wrapper end
	 *
	 * @since 1.0.0
	 */
	function xts_wc_my_account_wrapper_end() {
		?>
			</div>
		<?php
	}

	add_action( 'woocommerce_account_content', 'xts_wc_my_account_wrapper_end', 500 );
}

if ( ! function_exists( 'xts_wc_my_account_sidebar_wrapper_start' ) ) {
	/**
	 * Before my account navigation.
	 *
	 * @since 1.0.0
	 */
	function xts_wc_my_account_sidebar_wrapper_start() {
		?>
		<div class="xts-my-account-sidebar">
		<h3 class="woocommerce-MyAccount-title entry-title">
			<?php echo get_the_title(); // phpcs:ignore ?>
		</h3>
		<?php
	}

	add_action( 'woocommerce_account_navigation', 'xts_wc_my_account_sidebar_wrapper_start', 5 );
}

if ( ! function_exists( 'xts_wc_my_account_sidebar_wrapper_end' ) ) {
	/**
	 * After my account navigation.
	 *
	 * @since 1.0.0
	 */
	function xts_wc_my_account_sidebar_wrapper_end() {
		$sidebar_name = 'my-account-widget-sidebar';

		?>
		<?php if ( is_active_sidebar( $sidebar_name ) ) : ?>
			<aside class="xts-sidebar">
				<div class="xts-sidebar-inner">
					<div class="widget-area">
						<?php dynamic_sidebar( $sidebar_name ); ?>
					</div>
				</div>
			</aside>
		<?php endif; ?>
		</div><!-- .xts-my-account-sidebar -->
		<?php
	}

	add_action( 'woocommerce_account_navigation', 'xts_wc_my_account_sidebar_wrapper_end', 30 );
}

if ( ! function_exists( 'xts_wc_my_account_widget_template' ) ) {
	/**
	 * Display cart widget side
	 *
	 * @since 1.0.0
	 */
	function xts_wc_my_account_widget_template() {
		$wrapper_classes = '';
		$settings        = xts_get_header_settings();
		$page_id         = xts_get_page_id() ? xts_get_page_id() : get_option( 'woocommerce_myaccount_page_id' );
		$redirect_url    = apply_filters( 'xts_my_account_side_login_form_redirect', get_permalink( $page_id ) );
		$position        = isset( $settings['my-account']['position'] ) ? $settings['my-account']['position'] : '';
		$color_scheme    = isset( $settings['my-account']['color_scheme'] ) ? $settings['my-account']['color_scheme'] : '';

		if ( ! xts_is_woocommerce_installed() || is_user_logged_in() || ! isset( $settings['my-account'] ) || ( isset( $settings['my-account'] ) && isset( $settings['my-account']['login_form'] ) && ! $settings['my-account']['login_form'] ) ) {
			return;
		}

		if ( 'top' === $settings['my-account']['widget_type'] ) {
			$position = 'top';
		}

		$wrapper_classes .= ' xts-side-' . $position;
		if ( 'dark' !== $color_scheme && $color_scheme ) {
			$wrapper_classes .= ' xts-scheme-' . $color_scheme;
		}

		xts_enqueue_js_script( 'offcanvas-my-account' );

		?>
		<div class="xts-login-side xts-side-hidden<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php if ( 'top' === $position ) : ?>
				<?php xts_wc_my_account_top_template(); ?>
			<?php else : ?>
				<?php xts_wc_my_account_side_template( $redirect_url ); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	add_action( 'xts_after_site_wrapper', 'xts_wc_my_account_widget_template', 70 );
}

if ( ! function_exists( 'xts_wc_my_account_side_template' ) ) {
	/**
	 * Default template my account side.
	 *
	 * @param string $redirect_url Redirect url.
	 */
	function xts_wc_my_account_side_template( $redirect_url ) {
		?>
		<div class="xts-heading-with-btn">
			<span class="title xts-fontsize-m">
				<?php esc_html_e( 'Sign in', 'xts-theme' ); ?>
			</span>

			<div class="xts-close-button xts-action-btn xts-style-inline">
				<a href="#" ><?php esc_html_e( 'Close', 'xts-theme' ); ?></a>
			</div>
		</div>

		<?php woocommerce_output_all_notices(); ?>

		<?php
		woocommerce_login_form(
			array(
				'redirect' => $redirect_url,
				'action'   => $redirect_url,
			)
		);
		?>

		<div class="xts-create-account-msg">
			<p>
				<?php esc_html_e( 'No account yet?', 'xts-theme' ); ?>
			</p>

			<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="xts-button xts-size-s xts-color-primary">
				<?php esc_html_e( 'Create an Account', 'xts-theme' ); ?>
			</a>
		</div>
		<?php
	}
}

if ( ! function_exists( 'xts_wc_my_account_top_template' ) ) {
	/**
	 * Top template my account side.
	 */
	function xts_wc_my_account_top_template() {
		?>
		<div class="xts-close-button xts-action-btn xts-cross-btn xts-style-icon">
			<a href="#"></a>
		</div>
		<div class="container">
			<?php wc_get_template( 'myaccount/form-login.php' ); ?>
		</div>
		<?php
	}
}
