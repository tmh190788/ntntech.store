<?php
/**
 * Quick view button template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-quick-view-btn xts-action-btn <?php echo esc_attr( $classes ); ?>">
	<a href="<?php echo esc_url( get_the_permalink( $id ) ); ?>" data-id="<?php echo esc_attr( $id ); ?>">
		<?php esc_html_e( 'Quick view', 'xts-theme' ); ?>
	</a>
</div>
