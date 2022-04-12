<?php
/**
 * Wishlist page template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<?php if ( xts_get_opt( 'wishlist_logged' ) && ! is_user_logged_in() ) : ?>
	<div class="woocommerce-notices-wrapper">
		<div class="woocommerce-info" role="alert">
			<?php esc_html_e( 'Wishlist is available only for logged in visitors.', 'xts-theme' ); ?>
			<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>">
				<?php esc_html_e( 'Sign in', 'xts-theme' ); ?>
			</a>
		</div>
	</div>
	<?php return; ?>
<?php endif; ?>

<?php if ( is_user_logged_in() && $wishlist_ui->is_editable() ) : ?>
	<?php do_action( 'woocommerce_account_navigation' ); ?>
<?php endif; ?>

<div class="<?php echo ( is_user_logged_in() && $wishlist_ui->is_editable() ) ? 'woocommerce-MyAccount-content' : ''; ?>">
	<?php echo apply_filters( 'xts_wishlist_page_output', $wishlist_ui->wishlist_page_content() ); ?>
</div>
