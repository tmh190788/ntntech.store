<?php
/**
 * My account links template.
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-account-links xts-row xts-row-2 xts-row-md-3">
	<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
		<div class="xts-col xts-link-<?php echo esc_attr( $endpoint ); ?>">
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</a>
		</div>
	<?php endforeach; ?>
</div>