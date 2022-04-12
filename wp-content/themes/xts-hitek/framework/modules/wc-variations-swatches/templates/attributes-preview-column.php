<?php
/**
 * Product attributes preview column content template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<?php if ( isset( $image['id'] ) && $image['id'] ) : ?>
	<div class="xts-attribute-preview">
		<?php echo wp_get_attachment_image( $image['id'] ); ?>
	</div>
<?php elseif ( $color ) : ?>
	<div class="xts-attribute-preview" style="background-color:<?php echo esc_attr( $color['idle'] ); ?>;"></div>
<?php endif; ?>
