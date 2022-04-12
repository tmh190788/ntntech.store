<?php
/**
 * Default product hover template
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

global $product;

$hover_content        = xts_get_opt( 'product_loop_design_summary_hover_content' );
$product_desc_classes = '';
$actions_classes      = xts_get_default_value( 'hover_product_summary_actions_classes' );
if ( 'excerpt' === $hover_content ) {
	$product_desc_classes .= ' xts-with-text';
} elseif ( 'additional_info' === $hover_content ) {
	$product_desc_classes .= ' xts-with-info';
}

?>

<?php do_action( 'xts_before_shop_loop_thumbnail' ); ?>

<div class="xts-product-bg"></div>

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

	<div class="xts-product-actions">
		<?php if ( xts_get_opt( 'product_loop_wishlist' ) ) : ?>
			<?php xts_add_wishlist_button( $actions_classes ); ?>
		<?php endif; ?>
		<?php if ( xts_get_opt( 'product_loop_compare' ) ) : ?>
			<?php xts_add_compare_button( $actions_classes ); ?>
		<?php endif; ?>
		<?php xts_quick_view_btn( $actions_classes ); ?>
	</div>

	<div class="xts-product-swatches-wrapper"><?php xts_grid_swatches_template(); ?><?php xts_grid_variations_template(); // Must be in one line. ?></div>
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

	<div class="xts-product-hide-info">
		<?php if ( 'without' !== $hover_content ) : ?>
			<div class="xts-product-desc xts-more-desc<?php echo esc_attr( $product_desc_classes ); ?>">
				<div class="xts-more-desc-inner">
					<?php if ( 'excerpt' === $hover_content ) : ?>
						<?php echo do_shortcode( get_the_excerpt() ); ?>
					<?php elseif ( 'additional_info' === $hover_content ) : ?>
						<?php wc_display_product_attributes( $product ); ?>
					<?php endif; ?>
				</div>

				<a href="#" class="xts-more-desc-btn"><span></span></a>
			</div>
		<?php endif; ?>

		<?php if ( xts_get_loop_prop( 'product_stock_progress_bar' ) ) : ?>
			<?php xts_stock_progress_bar_template( 'xts-product-spb' ); ?>
		<?php endif; ?>

		<?php if ( xts_get_loop_prop( 'product_countdown' ) ) : ?>
			<?php xts_product_loop_sale_countdown( 'xts-product-countdown' ); ?>
		<?php endif; ?>

		<?php if ( ! xts_get_opt( 'catalog_mode' ) ) : ?>
			<div class="xts-product-add-btn xts-reset-mb-10">
				<?php if ( xts_get_loop_prop( 'product_loop_quantity' ) ) : ?>
					<?php xts_product_loop_quantity( $product ); ?>
				<?php endif ?>
				<?php
				/**
				 * Hook: woocommerce_after_shop_loop_item.
				 *
				 * @hooked woocommerce_template_loop_product_link_close - 5
				 * @hooked woocommerce_template_loop_add_to_cart - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item' );
				?>
			</div>
		<?php endif; ?>
	</div>
</div>
