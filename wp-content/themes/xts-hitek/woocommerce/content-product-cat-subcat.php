<?php
/**
 * Sub categories product categories hover template
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

	<div class="xts-cat-content xts-scroll xts-fill">
		<div class="xts-cat-header">
			<?php
			/**
			 * Hook: woocommerce_shop_loop_subcategory_title
			 *
			 * @hooked woocommerce_template_loop_category_title - 10
			 */
			do_action( 'woocommerce_shop_loop_subcategory_title', $category );
			?>
		</div>

		<?php if ( isset( $sub_categories ) ) : ?>
			<div class="xts-cat-footer xts-scroll-content">
				<ul class="xts-cat-sub-menu xts-sub-menu">
					<?php foreach ( $sub_categories as $sub_category ) : // phpcs:ignore ?>
						<li>
							<a href="<?php echo esc_url( get_term_link( $sub_category->term_id ) ); ?>">
								<?php echo esc_html( $sub_category->name ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

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
