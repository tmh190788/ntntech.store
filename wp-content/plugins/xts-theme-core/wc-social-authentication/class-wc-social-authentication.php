<?php
/**
 * Social authentication
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use Opauth;
use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * Product brands
 *
 * @since 1.0.0
 */
class WC_Social_Authentication extends Module {
	/**
	 * Available networks.
	 *
	 * @var array
	 */
	private $available_networks = array( 'facebook', 'vkontakte', 'google' );

	/**
	 * Current url.
	 *
	 * @var string
	 */
	private $current_url;

	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		add_action( 'init', array( $this, 'add_options' ) );
		add_action( 'init', array( $this, 'hooks' ) );

		if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$this->current_url = xts_get_http_protocol() . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; // phpcs:ignore
		}
		$this->define_constants();
		$this->include_files();
	}

	/**
	 * Register hooks and actions.
	 *
	 * @since 1.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'authentication' ), 20 );
		add_action( 'init', array( $this, 'authentication_callback' ), 30 );
		add_action( 'init', array( $this, 'disable_captcha' ), -10 );
		add_action( 'woocommerce_login_form_end', array( $this, 'buttons' ) );
	}

	/**
	 * Buttons template.
	 *
	 * @since 1.0.0
	 */
	public function buttons() {
		$google_app_id        = xts_get_opt( 'google_social_auth_app_id' );
		$google_app_secret    = xts_get_opt( 'google_social_auth_app_secret' );
		$facebook_app_id      = xts_get_opt( 'facebook_social_auth_app_id' );
		$facebook_app_secret  = xts_get_opt( 'facebook_social_auth_app_secret' );
		$vkontakte_app_id     = xts_get_opt( 'vkontakte_social_auth_app_id' );
		$vkontakte_app_secret = xts_get_opt( 'vkontakte_social_auth_app_secret' );

		$page_id      = xts_get_page_id() ? xts_get_page_id() : get_option( 'woocommerce_myaccount_page_id' );
		$redirect_url = apply_filters( 'xts_social_login_redirect', get_permalink( $page_id ) );

		?>
		<?php if ( ( $google_app_id && $google_app_secret ) || ( $facebook_app_id && $facebook_app_secret ) || ( $vkontakte_app_id && $vkontakte_app_secret ) ) : ?>
			<p class="xts-login-divider">
				<span><?php esc_html_e( 'Or login with', 'xts-theme' ); ?></span>
			</p>

			<ul class="xts-social-login">
				<?php if ( $facebook_app_id && $facebook_app_secret ) : ?>
					<li>
						<a href="<?php echo esc_url( add_query_arg( 'social_auth', 'facebook', $redirect_url ) ); ?>" class="xts-login-fb xts-button">
							<?php esc_html_e( 'Facebook', 'xts-theme' ); ?>
						</a>
					</li>
				<?php endif ?>

				<?php if ( $google_app_id && $google_app_secret ) : ?>
					<li>
						<a href="<?php echo esc_url( add_query_arg( 'social_auth', 'google', $redirect_url ) ); ?>" class="xts-login-goo xts-button">
							<?php esc_html_e( 'Google', 'xts-theme' ); ?>
						</a>
					</li>
				<?php endif ?>

				<?php if ( $vkontakte_app_id && $vkontakte_app_secret ) : ?>
					<li>
						<a href="<?php echo esc_url( add_query_arg( 'social_auth', 'vkontakte', $redirect_url ) ); ?>" class="xts-login-vk xts-button">
							<?php esc_html_e( 'VKontakte', 'xts-theme' ); ?>
						</a>
					</li>
				<?php endif ?>
			</ul>
		<?php endif; ?>
		<?php
	}

	/**
	 * Authentication.
	 *
	 * @since 1.0.0
	 */
	public function authentication() {
		if ( ( isset( $_GET['social_auth'] ) && ! $_GET['social_auth'] || ! isset( $_GET['social_auth'] ) ) && ( isset( $_GET['code'] ) && ! $_GET['code'] || ! isset( $_GET['code'] ) ) ) { // phpcs:ignore
			return;
		}

		$network = $this->get_current_network();

		if ( isset( $_GET['social_auth'] ) && $_GET['social_auth'] ) { // phpcs:ignore
			$network = sanitize_key( $_GET['social_auth'] ); // phpcs:ignore
		}

		if ( ! in_array( $network, $this->available_networks ) ) { // phpcs:ignore
			return;
		}

		$account_url    = $this->get_account_url();
		$security_salt  = apply_filters( 'xts_opauth_salt', '2NlBUibcszrVtNmDnxqDbwCOpLWq91eatIz6O1O' );
		$callback_param = 'int_callback';

		switch ( $network ) {
			case 'google':
				$app_id     = xts_get_opt( 'google_social_auth_app_id' );
				$app_secret = xts_get_opt( 'google_social_auth_app_secret' );

				if ( ! $app_secret || ! $app_id ) {
					return;
				}

				$strategy = array(
					'Google' => array(
						'client_id'     => $app_id,
						'client_secret' => $app_secret,
					),
				);

				$callback_param = 'oauth2callback';

				break;

			case 'vkontakte':
				$app_id     = xts_get_opt( 'vkontakte_social_auth_app_id' );
				$app_secret = xts_get_opt( 'vkontakte_social_auth_app_secret' );

				if ( ! $app_secret || ! $app_id ) {
					return;
				}

				$strategy = array(
					'VKontakte' => array(
						'app_id'     => $app_id,
						'app_secret' => $app_secret,
						'scope'      => 'email',
					),
				);
				break;

			default:
				$app_id     = xts_get_opt( 'facebook_social_auth_app_id' );
				$app_secret = xts_get_opt( 'facebook_social_auth_app_secret' );

				if ( ! $app_secret || ! $app_id ) {
					return;
				}

				$strategy = array(
					'Facebook' => array(
						'app_id'     => $app_id,
						'app_secret' => $app_secret,
						'scope'      => 'email',
					),
				);
				break;
		}

		$config = array(
			'security_salt'      => $security_salt,
			'host'               => $account_url,
			'path'               => '/',
			'callback_url'       => $account_url,
			'callback_transport' => 'get',
			'strategy_dir'       => XTS_SOCIAL_AUTH_DIR . '/opauth/',
			'Strategy'           => $strategy,
		);

		if ( isset( $_GET['code'] ) && ! $_GET['code'] || ! isset( $_GET['code'] ) ) { // phpcs:ignore
			$config['request_uri'] = '/' . $network;
		} else {
			$config['request_uri'] = '/' . $network . '/' . $callback_param . '?code=' . $_GET['code']; // phpcs:ignore
		}

		new Opauth( $config );
	}

	/**
	 * Authentication callback.
	 *
	 * @since 1.0.0
	 */
	public function authentication_callback() {
		if ( isset( $_GET['error_reason'] ) && 'user_denied' === $_GET['error_reason'] ) { // phpcs:ignore
			wp_safe_redirect( $this->get_account_url() );
			exit;
		}

		if ( ( isset( $_GET['opauth'] ) && ! $_GET['opauth'] || ! isset( $_GET['opauth'] ) ) || is_user_logged_in() ) { // phpcs:ignore
			return;
		}

		if ( ! xts_is_core_module_exists() ) {
			return;
		}

		$opauth = unserialize( xts_decompress( $_GET['opauth'] ) );

		switch ( $opauth['auth']['provider'] ) {
			case 'Facebook':
				if ( empty( $opauth['auth']['info'] ) ) {
					wc_add_notice( __( 'Can\'t login with Facebook. Please, try again later.', 'xts-theme' ), 'error' );
					return;
				}

				$email = isset( $opauth['auth']['info']['email'] ) ? $opauth['auth']['info']['email'] : '';

				if ( empty( $email ) ) {
					wc_add_notice( __( 'Facebook doesn\'t provide your email. Try to register manually.', 'xts-theme' ), 'error' );
					return;
				}

				$this->register_or_login( $email );
				break;
			case 'Google':
				if ( empty( $opauth['auth']['info'] ) ) {
					wc_add_notice( __( 'Can\'t login with Google. Please, try again later.', 'xts-theme' ), 'error' );
					return;
				}

				$email = isset( $opauth['auth']['info']['email'] ) ? $opauth['auth']['info']['email'] : '';

				if ( empty( $email ) ) {
					wc_add_notice( __( 'Google doesn\'t provide your email. Try to register manually.', 'xts-theme' ), 'error' );
					return;
				}

				$this->register_or_login( $email );
				break;
			case 'VKontakte':
				if ( empty( $opauth['auth']['info'] ) ) {
					wc_add_notice( __( 'Can\'t login with VKontakte. Please, try again later.', 'xts-theme' ), 'error' );
					return;
				}

				$email = isset( $opauth['auth']['info']['email'] ) ? $opauth['auth']['info']['email'] : '';

				if ( empty( $email ) ) {
					wc_add_notice( __( 'VK doesn\'t provide your email. Try to register manually.', 'xts-theme' ), 'error' );
					return;
				}

				$this->register_or_login( $email );
				break;

			default:
				break;
		}
	}

	/**
	 * Register or login.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email User email.
	 */
	public function register_or_login( $email ) {
		add_filter( 'pre_option_woocommerce_registration_generate_username', array( $this, 'get_yes' ), 10 );
		add_filter( 'dokan_register_nonce_check', '__return_false' );

		$password = wp_generate_password();
		$customer = wc_create_new_customer( $email, '', $password );

		$user = get_user_by( 'email', $email );

		if ( is_wp_error( $customer ) ) {
			if ( isset( $customer->errors['registration-error-email-exists'] ) ) {
				wc_set_customer_auth_cookie( $user->ID );
			}
		} else {
			wc_set_customer_auth_cookie( $customer );
			update_user_meta( $customer, 'xts_social_auth', 'yes' );
		}

		/* translators: s: user name */
		wc_add_notice( sprintf( __( 'You are now logged in as <strong>%s</strong>', 'xts-theme' ), $user->display_name ) );

		remove_action( 'pre_option_woocommerce_registration_generate_username', array( $this, 'get_yes' ), 10 );
	}

	/**
	 * Get current network.
	 *
	 * @since 1.0.0
	 */
	public function get_current_network() {
		$account_url = $this->get_account_url();

		foreach ( $this->available_networks as $network ) {
			if ( strstr( $this->current_url, trailingslashit( $account_url ) . $network ) ) {
				return $network;
			}
		}

		return false;
	}

	/**
	 * Get account url.
	 *
	 * @since 1.0.0
	 */
	public function get_account_url() {
		return untrailingslashit( wc_get_page_permalink( 'myaccount' ) );
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		if ( ! defined( 'XTS_SOCIAL_AUTH_DIR' ) ) {
			define( 'XTS_SOCIAL_AUTH_DIR', apply_filters( 'xts_social_auth_dir', XTS_FRAMEWORK_ABSPATH . '/modules/core/wc-social-authentication/' ) );
		}
	}

	/**
	 * Include main files.
	 *
	 * @since 1.0.0
	 */
	private function include_files() {
		$files = array(
			'opauth/class-opauth',
			'opauth/class-opauth-strategy',
		);

		foreach ( $files as $file ) {
			$path = XTS_SOCIAL_AUTH_DIR . $file . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Disable captcha on login.
	 *
	 * @since 1.0.0
	 */
	public function disable_captcha() {
		add_filter(
			'anr_get_option',
			function ( $option_values, $option ) {
				if ( is_array( $option_values ) && 'enabled_forms' === $option ) {
					foreach ( $option_values as $key => $value ) {
						if ( ( 'registration' === $value || 'login' === $value ) && isset( $_GET['opauth'] ) ) { // phpcs:ignore
							unset( $option_values[ $key ] );
						}
					}
				}
				return $option_values;
			},
			10000000,
			2
		);
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'social_authentication_section',
				'name'     => esc_html__( 'Social authentication', 'xts-theme' ),
				'icon'     => 'xf-social-authentefication',
				'parent'   => 'api_integrations_section',
				'priority' => 30,
			)
		);

		Options::add_field(
			array(
				'id'          => 'alt_social_auth_method',
				'name'        => esc_html__( 'Alternative login mechanism', 'xts-theme' ),
				'description' => esc_html__( 'Enable it if you are redirected to my account page without signing in after click on the social login button.', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'social_authentication_section',
				'default'     => '0',
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'       => 'facebook_social_auth_notice',
				'type'     => 'notice',
				'style'    => 'info',
				'name'     => '',
				'content'  => 'Enable login/register with Facebook on your web-site.
			To do that you need to create an APP on the Facebook <a href="https://developers.facebook.com/" target="_blank">https://developers.facebook.com/</a>.
			Then go to APP settings and copy App ID and App Secret there. You also need to insert Redirect URI like this example <strong>' . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'facebook/int_callback</strong> More information you can get in our <a href="' . esc_url( XTS_DOCS_URL ) . 'how-to-configure-facebook-login" target="_blank">documentation</a>.',
				'group'    => esc_html__( 'Facebook login', 'xts-theme' ),
				'section'  => 'social_authentication_section',
				'priority' => 20,
			)
		);

		Options::add_field(
			array(
				'id'       => 'facebook_social_auth_app_id',
				'name'     => esc_html__( 'App ID', 'xts-theme' ),
				'group'    => esc_html__( 'Facebook login', 'xts-theme' ),
				'type'     => 'text_input',
				'section'  => 'social_authentication_section',
				'class'    => 'xts-col-6',
				'priority' => 30,
			)
		);

		Options::add_field(
			array(
				'id'       => 'facebook_social_auth_app_secret',
				'name'     => esc_html__( 'App Secret', 'xts-theme' ),
				'group'    => esc_html__( 'Facebook login', 'xts-theme' ),
				'type'     => 'text_input',
				'section'  => 'social_authentication_section',
				'class'    => 'xts-col-6',
				'priority' => 40,
			)
		);

		Options::add_field(
			array(
				'id'       => 'google_social_auth_notice',
				'type'     => 'notice',
				'style'    => 'info',
				'name'     => '',
				'content'  => 'You can enable login/register with Google on your web-site.
			To do that you need to Create a Google APIs project at <a href="https://code.google.com/apis/console/" target="_blank">https://console.developers.google.com/apis/dashboard/</a>.
			Make sure to go to API Access tab and Create an OAuth 2.0 client ID. Choose Web application for Application type. Make sure that redirect URI is set to actual OAuth 2.0 callback URL, usually <strong>' . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'google/oauth2callback </strong> More information you can get in our <a href="' . esc_url( XTS_DOCS_URL ) . 'how-to-configure-google-login" target="_blank">documentation</a>.',
				'group'    => esc_html__( 'Google login', 'xts-theme' ),
				'section'  => 'social_authentication_section',
				'priority' => 50,
			)
		);

		Options::add_field(
			array(
				'id'       => 'google_social_auth_app_id',
				'name'     => esc_html__( 'App ID', 'xts-theme' ),
				'group'    => esc_html__( 'Google login', 'xts-theme' ),
				'type'     => 'text_input',
				'section'  => 'social_authentication_section',
				'class'    => 'xts-col-6',
				'priority' => 60,
			)
		);

		Options::add_field(
			array(
				'id'       => 'google_social_auth_app_secret',
				'name'     => esc_html__( 'App Secret', 'xts-theme' ),
				'group'    => esc_html__( 'Google login', 'xts-theme' ),
				'type'     => 'text_input',
				'section'  => 'social_authentication_section',
				'class'    => 'xts-col-6',
				'priority' => 70,
			)
		);

		Options::add_field(
			array(
				'id'       => 'vkontakte_social_auth_notice',
				'type'     => 'notice',
				'style'    => 'info',
				'name'     => '',
				'content'  => 'To enable login/register with vk.com you need to create an APP here <a href="https://vk.com/dev" target="_blank">https://vk.com/dev</a>.
			Then go to APP settings and copy App ID and App Secret there.
			You also need to insert Redirect URI like this example <strong>' . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'vkontakte/int_callback</strong>',
				'group'    => esc_html__( 'VK login', 'xts-theme' ),
				'section'  => 'social_authentication_section',
				'priority' => 80,
			)
		);

		Options::add_field(
			array(
				'id'       => 'vkontakte_social_auth_app_id',
				'name'     => esc_html__( 'App ID', 'xts-theme' ),
				'group'    => esc_html__( 'VK login', 'xts-theme' ),
				'type'     => 'text_input',
				'section'  => 'social_authentication_section',
				'class'    => 'xts-col-6',
				'priority' => 90,
			)
		);

		Options::add_field(
			array(
				'id'       => 'vkontakte_social_auth_app_secret',
				'name'     => esc_html__( 'App Secret', 'xts-theme' ),
				'group'    => esc_html__( 'VK login', 'xts-theme' ),
				'type'     => 'text_input',
				'section'  => 'social_authentication_section',
				'class'    => 'xts-col-6',
				'priority' => 100,
			)
		);
	}

	/**
	 * Get yes.
	 *
	 * @return string
	 */
	public function get_yes() {
		return 'yes';
	}
}

new WC_Social_Authentication();
