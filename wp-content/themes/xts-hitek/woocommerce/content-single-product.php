<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore
	return;
}

/**
 * XTS Settings
 */
$wrapper_classes = 'xts-single-product';
$is_quick_view   = xts_get_loop_prop( 'is_quick_view' );

if ( ! $is_quick_view && xts_get_opt( 'single_product_sticky' ) ) {
	wp_enqueue_script( 'imagesloaded' );
	xts_enqueue_js_library( 'sticky-kit' );
	xts_enqueue_js_script( 'single-product-sticky' );
	$wrapper_classes .= ' xts-product-sticky';
}

?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( $wrapper_classes, $product ); ?>>

	<div class="row">
		<?php
		/**
		 * Hook: woocommerce_before_single_product_summary.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
		?>

		<div class="<?php echo esc_attr( xts_get_single_product_summary_classes() ); ?>">
			<div class="summary entry-summary xts-single-product-summary">
				<?php
				/**
				 * Hook: woocommerce_single_product_summary.
				 *
				 * @hooked WC_Brands::single_product_brands - 1
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked WC_Product_Countdown::product_single_countdown - 15
				 * @hooked WC_Stock_Progress_Bar::woocommerce_template_single_excerpt - 20
				 * @hooked stock_progress_bar_template - 20
				 * @hooked xts_single_product_attributes_table - 21
				 * @hooked xts_before_add_to_cart_content - 25
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked xts_after_add_to_cart_content - 31
				 * @hooked xts_single_product_action_buttons_wrapper_start - 32
				 * @hooked WC_Wishlist::add_to_wishlist_single_btn - 33
				 * @hooked WC_Compare::add_to_compare_single_btn - 34
				 * @hooked WC_Size_Guide::size_guide_btn - 35
				 * @hooked xts_single_product_action_buttons_wrapper_end - 36
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked xts_single_product_share_buttons - 45
				 * @hooked single_product_brands - 46
				 * @hooked woocommerce_template_single_sharing - 50
				 * @hooked WC_Structured_Data::generate_product_data() - 60
				 */
				do_action( 'woocommerce_single_product_summary' );
				?>
			</div>
		</div>
	</div>

	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
