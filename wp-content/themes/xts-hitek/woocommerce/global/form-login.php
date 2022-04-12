<?php
/**
 * Login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( is_user_logged_in() ) {
	return;
}

$wrapper_classes = '';

if ( $hidden ) {
	$wrapper_classes .= ' xts-checkout-login';
}

if ( ! isset( $action ) ) {
	$action = ''; // phpcs:ignore
}

$message = $message ? wpautop( wptexturize( $message ) ) : '';
$hidden  = $hidden ? 'display:none;' : '';

?>
<form class="woocommerce-form woocommerce-form-login login<?php echo esc_attr( $wrapper_classes ); ?>" method="post" style="<?php echo esc_attr( $hidden ); ?>" action="<?php echo esc_url( $action ); ?>">

	<?php do_action( 'woocommerce_login_form_start' ); ?>

	<?php echo wpautop( wptexturize( $message ) ); // @codingStandardsIgnoreLine ?>

	<p class="form-row form-row-wide">
		<label for="username"><?php esc_html_e( 'Username or email', 'woocommerce' ); ?>
			&nbsp;<span class="required">*</span></label>
		<input type="text" class="input-text" name="username" id="username" autocomplete="username" />
	</p>
	<p class="form-row form-row-wide">
		<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>
			&nbsp;<span class="required">*</span></label>
		<input class="input-text" type="password" name="password" id="password" autocomplete="current-password" />
	</p>
	<div class="clear"></div>

	<?php do_action( 'woocommerce_login_form' ); ?>

	<p class="form-row xts-login-extra">
		<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
			<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
			<span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
		</label>

		<a class="lost-password" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
	</p>

	<p class="form-row xts-login-submit">
		<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
		<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ); ?>" />
		<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>"><?php esc_html_e( 'Login', 'woocommerce' ); ?></button>
	</p>

	<?php do_action( 'woocommerce_login_form_end' ); ?>

</form>
