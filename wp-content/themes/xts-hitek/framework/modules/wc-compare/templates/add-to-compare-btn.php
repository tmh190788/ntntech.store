<?php
/**
 * Add to compare button template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-compare-btn xts-action-btn <?php echo esc_attr( $classes ); ?>">
	<a href="<?php echo esc_url( $url ); ?>" data-added-text="<?php esc_attr_e( 'Compare products', 'xts-theme' ); ?>" data-id="<?php echo esc_attr( $product->get_id() ); ?>">
		<?php esc_html_e( 'Compare', 'xts-theme' ); ?>
	</a>
</div>
