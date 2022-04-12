<?php
/**
 * Compare field base template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-compare-remove xts-cross-btn xts-action-btn xts-style-inline">
	<a href="#" data-id="<?php echo esc_attr( $product['id'] ); ?>"><?php esc_html_e( 'Remove', 'xts-theme' ); ?></a>
</div>

<a href="<?php echo esc_url( get_permalink( $product['id'] ) ); ?>" class="xts-compare-image">
	<?php echo wp_kses( $product['base']['image'], 'xts_media' ); ?>
</a>

<a class="xts-entities-title" href="<?php echo esc_url( get_permalink( $product['id'] ) ); ?>">
	<?php echo esc_html( $product['base']['title'] ); ?>
</a>

<?php echo apply_filters( 'xts_compare_rating_html', $product['id'] ); ?>['base']['rating'] ); // phpcs:ignore XSS ok. ?>

<div class="price">
	<?php echo apply_filters( 'xts_compare_price_html', $product['base']['price'] ); // phpcs:ignore XSS ok. ?>
</div>

<?php if ( ! xts_get_opt( 'catalog_mode' ) ) : ?>
	<?php echo apply_filters( 'xts_compare_add_to_cart_html', $product['base']['add_to_cart'] ); // phpcs:ignore XSS ok. ?>
<?php endif; ?>
