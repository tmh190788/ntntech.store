<?php
/**
 * Wishlist element template
 *
 * @package xts
 */

if ( ! xts_is_woocommerce_installed() || ! xts_get_opt( 'wishlist' ) || ( xts_get_opt( 'wishlist_logged' ) && ! is_user_logged_in() ) ) {
	return;
}

$wrapper_classes = '';
$icon_classes    = '';
$icon_style      = $params['icon_style'];
$design          = $params['design'];
$style           = $params['style'];

$icon_classes .= ' xts-icon-' . $icon_style;

$wrapper_classes .= ' xts-style-' . $style;
$wrapper_classes .= ' xts-design-' . $design;

xts_enqueue_js_script( 'product-wishlist' );

?>

<div class="xts-header-wishlist xts-header-el<?php echo esc_attr( $wrapper_classes ); ?>">
	<a href="<?php echo esc_url( xts_get_whishlist_page_url() ); ?>">
		<span class="xts-header-el-icon<?php echo esc_attr( $icon_classes ); ?>">
			<?php if ( 'custom' === $icon_style ) : ?>
				<?php echo xts_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore ?>
			<?php endif; ?>

			<?php if ( 'count' === $design || 'count-text' === $design || 'count-alt' === $design || 'round-bordered' === $design || 'round' === $design ) : ?>
				<span class="xts-wishlist-count">
					<?php echo esc_html( xts_get_wishlist_count() ); ?>
				</span>
			<?php endif; ?>
		</span>

		<span class="xts-header-el-label">
			<?php esc_html_e( 'Wishlist', 'xts-theme' ); ?>
		</span>
	</a>
</div>
