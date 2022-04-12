<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

/**
 * XTS Settings
 */
$design                   = xts_get_opt( 'product_loop_design' );
$animation                = xts_get_opt( 'shop_animation' );
$animation_duration       = xts_get_opt( 'shop_animation_duration' );
$animation_in_view        = xts_get_opt( 'shop_animation_in_view' );
$different_sizes          = xts_get_opt( 'shop_different_sizes' );
$different_sizes_position = explode( ',', xts_get_opt( 'shop_different_sizes_position' ) );
$column_classes           = '';

// Increase loop count.
xts_set_loop_prop( 'woocommerce_loop', xts_get_loop_prop( 'woocommerce_loop' ) + 1 );
$woocommerce_loop = xts_get_loop_prop( 'woocommerce_loop' );

// Different sizes.
if ( in_array( $woocommerce_loop, $different_sizes_position ) && $different_sizes ) { // phpcs:ignore
	$column_classes .= ' xts-wide';
}
// Animations.
if ( $animation && $animation_in_view ) {
	$column_classes .= ' xts-animation-' . $animation;
	$column_classes .= ' xts-animation-' . $animation_duration;
}

?>

<div class="xts-col<?php echo esc_attr( $column_classes ); ?>" data-loop="<?php echo esc_attr( $woocommerce_loop ); ?>">
	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
	<?php do_action( 'xts_before_shop_loop_product' ); ?>

	<div <?php wc_product_class( 'xts-product', $product ); ?> data-id="<?php echo esc_attr( $product->get_id() ); ?>">
		<?php wc_get_template_part( 'content', 'product-' . $design ); ?>

	</div>
</div>
