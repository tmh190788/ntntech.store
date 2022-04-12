<?php
/**
 * Add to wishlist button template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-wishlist-btn xts-action-btn <?php echo esc_attr( $classes ); ?>">
	<a href="<?php echo esc_url( xts_get_whishlist_page_url() ); ?>" data-key="<?php echo esc_attr( wp_create_nonce( 'xts-wishlist-add' ) ); ?>" data-product-id="<?php echo esc_attr( get_the_ID() ); ?>" data-added-text="<?php esc_html_e( 'Browse Wishlist', 'xts-theme' ); ?>">
		<?php esc_html_e( 'Add to wishlist', 'xts-theme' ); ?>
	</a>
</div>
