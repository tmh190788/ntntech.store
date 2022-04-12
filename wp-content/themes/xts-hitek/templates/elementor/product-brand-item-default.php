<?php
/**
 * Brands item template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>

<div class="xts-brand-item">
	<a href="<?php echo esc_url( $attribute_link ); ?>" title="<?php echo esc_attr( $brand->name ); ?>">
		<?php if ( isset( $image['id'] ) && $image['id'] ) : ?>
			<?php echo wp_get_attachment_image( $image['id'], 'full' ); ?>
		<?php else : ?>
			<span class="xts-brand-name">
				<?php echo esc_html( $brand->name ); ?>
			</span>
		<?php endif; ?>
	</a>
</div>
