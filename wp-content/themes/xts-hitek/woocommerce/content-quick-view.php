<?php
/**
 * Quick view template
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

global $post;

$product = wc_get_product( $post->ID );

// Ensure visibility.
if ( ! $product || ! $product->is_visible() ) {
	return;
}

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore

	return;
}

/**
 * XTS Settings
 */
xts_set_loop_prop( 'is_quick_view', true );

?>

<?php do_action( 'woocommerce_before_single_product' ); ?>

<div id="product-<?php the_ID(); ?>" <?php post_class( 'xts-quick-view-product' ); ?>>

	<div class="row">
		<div class="col-lg-6 col-md-6 col-12 xts-quick-view-product-gallery">
			<?php wc_get_template( 'single-product/product-image.php' ); ?>
		</div>

		<div class="col-lg-6 col-md-6 col-12 xts-scroll">
			<div class="summary entry-summary xts-single-product-summary xts-scroll-content">
				<?php
				/**
				 * Hook: woocommerce_single_product_summary.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 * @hooked WC_Structured_Data::generate_product_data() - 60
				 */
				do_action( 'woocommerce_single_product_summary' );
				?>
			</div>
		</div><!-- .summary -->
	</div>

</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
