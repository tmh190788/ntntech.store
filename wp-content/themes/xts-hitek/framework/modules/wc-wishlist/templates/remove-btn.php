<?php
/**
 * Remove from wishlist button template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-remove-wishlist-btn xts-action-btn xts-style-inline" data-key="<?php echo esc_attr( wp_create_nonce( 'xts-wishlist-remove' ) ); ?>" data-product-id="<?php echo esc_attr( get_the_ID() ); ?>">
	<a href="#">
		<?php esc_html_e( 'Remove', 'xts-theme' ); ?>
	</a>
</div>