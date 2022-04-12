<?php
/**
 * Images picker template
 *
 * @package xts
 * @version 1.0.0
 */

use XTS\WC_Additional_Variation_Images\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Variables.
 *
 * @var Admin $admin Admin class.
 * @var object $variation Variations.
 */

?>

<div class="xts-avi-wrapper">
	<h4>
		<?php esc_html_e( 'Additional variation images', 'xts-theme' ); ?>
	</h4>

	<div class="xts-avi-list xts-upload-preview">
		<?php foreach ( $admin->get_attachments_data( $variation ) as $attachment ) : ?>
			<div class="xts-avi-image" data-attachment_id="<?php echo esc_attr( $attachment['id'] ); ?>">
				<img src="<?php echo esc_attr( $attachment['url'][0] ); ?>" width="<?php echo esc_attr( $attachment['url'][1] ); ?>" height="<?php echo esc_attr( $attachment['url'][2] ); ?>" alt="<?php esc_attr_e( 'variation image', 'xts-theme' ); ?>">
				<a href="#" class="xts-avi-remove-image xts-remove">
					<span class="dashicons dashicons-dismiss"></span>
				</a>
			</div>
		<?php endforeach; ?>
	</div>

	<input type="hidden" class="xts-variation-gallery-ids" name="xts_additional_variation_images[<?php echo esc_attr( $variation->ID ); ?>]" value="<?php echo esc_attr( implode( ',', $admin->get_attachments( $variation ) ) ); ?>">

	<a href="#" class="button xts-avi-add-image">
		<?php esc_html_e( 'Add image', 'xts-theme' ); ?>
	</a>
</div>
