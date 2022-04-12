<?php
/**
 * Price message template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" class="xts-login-to-price-msg<?php echo esc_attr( $classes ); ?>">
	<?php esc_html_e( 'Login to see price', 'xts-theme' ); ?>
</a>
