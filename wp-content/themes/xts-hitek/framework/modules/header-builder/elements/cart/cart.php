<?php
/**
 * Cart element template
 *
 * @package xts
 */

if ( ! xts_is_woocommerce_installed() || ( ! is_user_logged_in() && xts_get_opt( 'login_to_see_price' ) ) ) {
	return;
}

$wrapper_classes  = '';
$icon_classes     = '';
$dropdown_classes = '';
$icon_style       = $params['icon_style'];
$design           = $params['design'];
$widget_type      = $params['widget_type'];
$color_scheme     = $params['color_scheme'];
$account_link     = wc_get_cart_url();

$icon_classes .= ' xts-icon-' . $icon_style;

$wrapper_classes .= ' xts-design-' . $design;
$wrapper_classes .= ' xts-style-' . $params['style'];
if ( 'dropdown' === $widget_type ) {
	$wrapper_classes .= ' xts-event-hover';
}
if ( 'side' === $widget_type || 'top' === $widget_type ) {
	$wrapper_classes .= ' xts-opener';
}

if ( 'dark' !== $color_scheme && $color_scheme ) {
	$dropdown_classes .= ' xts-scheme-' . $color_scheme;
}

if ( xts_get_opt( 'mini_cart_quantity' ) ) {
	xts_enqueue_js_script( 'mini-cart-quantity' );
	xts_enqueue_js_script( 'single-product-quantity' );
}

?>

<div class="xts-header-cart xts-header-el<?php echo esc_attr( $wrapper_classes ); ?>">
	<a href="<?php echo esc_url( $account_link ); ?>">
		<span class="xts-header-el-icon<?php echo esc_attr( $icon_classes ); ?>">
			<?php if ( 'custom' === $icon_style ) : ?>
				<?php echo xts_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore ?>
			<?php endif; ?>

			<?php if ( 'count' === $design || 'count-alt' === $design || 'count-text' === $design || 'round-bordered' === $design || 'round' === $design ) : ?>
				<?php xts_wc_cart_count(); ?>
			<?php endif; ?>
		</span>

		<span class="xts-header-el-label">
			<?php if ( 'default' === $design ) : ?>
				<?php xts_wc_cart_count(); ?>
			<?php endif; ?>

			<span class="xts-cart-divider">/</span> 
			<?php xts_wc_cart_subtotal(); ?>
		</span>
	</a>

	<?php if ( 'dropdown' === $widget_type ) : ?>
		<div class="xts-dropdown xts-dropdown-cart xts-scroll<?php echo esc_attr( $dropdown_classes ); ?>">
			<div class="xts-dropdown-inner">
				<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
			</div>
		</div>
	<?php endif; ?>
</div>
