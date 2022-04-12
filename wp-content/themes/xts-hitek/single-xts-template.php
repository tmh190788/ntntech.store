<?php
/**
 * The template for displaying all xts templates.
 *
 * @package xts
 */

get_header();

$product = xts_get_preview_product();

?>

<?php if ( 'sidebar-left' === xts_get_page_layout() ) : ?>
	<?php
	/**
	 * Hook: woocommerce_sidebar.
	 *
	 * @hooked woocommerce_get_sidebar - 10
	 */
	do_action( 'woocommerce_sidebar' );
	?>
<?php endif; ?>

<?php
/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );
?>

<div id="product-<?php echo esc_attr( $product->ID ); ?>" <?php wc_product_class( '', $product->ID ); ?>>
	<?php while ( have_posts() ) : ?>

		<?php the_post(); ?>

		<?php the_content(); ?>

	<?php endwhile; ?>
</div>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );
?>

<?php if ( 'sidebar-right' === xts_get_page_layout() ) : ?>
	<?php
	/**
	 * Hook: woocommerce_sidebar.
	 *
	 * @hooked woocommerce_get_sidebar - 10
	 */
	do_action( 'woocommerce_sidebar' );
	?>
<?php endif; ?>

<?php get_footer(); ?>
