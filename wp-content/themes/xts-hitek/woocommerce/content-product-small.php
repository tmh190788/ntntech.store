<?php
/**
 * Default product hover template
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>

<?php do_action( 'xts_before_shop_loop_thumbnail' ); ?>

<div class="xts-product-thumb">
	<a href="<?php echo esc_url( get_permalink() ); ?>" class="xts-product-link xts-fill"></a>

	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );
	?>
</div>

<div class="xts-product-content">
	<?php
	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );
	?>

	<?php xts_grid_categories_template(); ?>
	<?php xts_grid_attribute_template(); ?>
	<?php xts_grid_brands_template(); ?>

	<?php if ( xts_get_loop_prop( 'product_rating' ) ) : ?>
		<?php woocommerce_template_loop_rating(); ?>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );
	?>
</div>
