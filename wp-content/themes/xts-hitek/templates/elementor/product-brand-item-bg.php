<?php
/**
 * Brands item template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$background_image = get_term_meta( $brand->term_id, '_xts_brand_background_image', true );
$country          = get_term_meta( $brand->term_id, '_xts_brand_background_country', true );

?>

<div class="xts-brand-item">
	<a class="xts-brand-link xts-fill" title="<?php echo esc_attr( $brand->name ); ?>"  href="<?php echo esc_url( $attribute_link ); ?>"></a>

	<div class="xts-brand-bg">
		<?php if ( isset( $background_image['id'] ) ) : ?>
			<?php echo wp_get_attachment_image( $background_image['id'], 'medium' ); ?>
		<?php endif; ?>
	</div>

	<div class="xts-brand-content">
		<?php if ( isset( $image['id'] ) && $image['id'] ) : ?>
			<div class="xts-brand-logo">
				<?php echo wp_get_attachment_image( $image['id'], 'medium' ); ?>
			</div>
		<?php endif; ?>

		<div class="xts-brand-desc">
			<h4 class="xts-brand-name xts-entities-title">
				<?php echo esc_html( $brand->name ); ?>
			</h4>

			<div class="xts-brand-country">
				<?php echo esc_html( $country ); ?>
			</div>
		</div>
	</div>
</div>
