<?php
/**
 * Default product categories hover template
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>

<?php
/**
 * Hook: woocommerce_before_subcategory
 *
 * @hooked woocommerce_template_loop_category_link_open - 10
 */
do_action( 'woocommerce_before_subcategory', $category );
?>

	<div class="xts-cat-thumbnail">
		<a href="<?php echo esc_url( get_term_link( $category, 'product_cat' ) ); ?>" class="xts-cat-link xts-fill"></a>

		<?php
		/**
		 * Hook: woocommerce_before_subcategory_title
		 *
		 * @hooked woocommerce_subcategory_thumbnail - 10
		 */
		do_action( 'woocommerce_before_subcategory_title', $category );
		?>
	</div>

	<div class="xts-cat-content">
		<?php
		/**
		 * Hook: woocommerce_shop_loop_subcategory_title
		 *
		 * @hooked woocommerce_template_loop_category_title - 10
		 */
		do_action( 'woocommerce_shop_loop_subcategory_title', $category );
		?>

		<?php
		/**
		 * Hook: woocommerce_after_subcategory_title
		 */
		do_action( 'woocommerce_after_subcategory_title', $category );
		?>
	</div>

<?php
/**
 * Hook: woocommerce_after_subcategory
 *
 * @hooked woocommerce_template_loop_category_link_close - 10
 */
do_action( 'woocommerce_after_subcategory', $category );
?>