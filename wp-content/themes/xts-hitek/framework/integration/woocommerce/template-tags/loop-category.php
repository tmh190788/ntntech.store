<?php
/**
 * Woocommerce loop category functions file
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woocommerce_template_loop_category_title' ) ) {
	/**
	 * Show the subcategory title in the product loop.
	 *
	 * @param object $category Category object.
	 */
	function woocommerce_template_loop_category_title( $category ) {
		?>
			<h2 class="xts-entities-title woocommerce-loop-category__title">
				<a href="<?php echo esc_url( get_term_link( $category, 'product_cat' ) ); ?>">
					<?php echo esc_html( $category->name ); ?>
				</a>
			</h2>

			<?php if ( $category->count > 0 && xts_get_opt( 'categories_product_count' ) ) : ?>
				<div class="xts-cat-count">
					<span>
						<?php echo esc_html( $category->count ); ?>
					</span>

					<span class="xts-cat-count-text">
						<?php echo sprintf( _n( 'product', 'products', $category->count, 'xts-theme' ), $category->count ); // phpcs:ignore ?>
					</span>
				</div>
			<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'xts_subcategory_loop_thumbnail' ) ) {
	/**
	 * Get the category thumbnail for the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $category Category.
	 */
	function xts_subcategory_loop_thumbnail( $category ) {
		$thumbnail_id      = get_term_meta( $category->term_id, 'thumbnail_id', true );
		$image_size        = 'woocommerce_thumbnail';
		$custom_image_size = xts_get_loop_prop( 'product_categories_image_custom' );

		if ( xts_get_loop_prop( 'product_categories_image_size' ) ) {
			$image_size = xts_get_loop_prop( 'product_categories_image_size' );
		}

		?>
			<div class="xts-cat-image">
				<?php if ( $thumbnail_id ) : ?>
					<?php
					echo xts_get_image_html( // phpcs:ignore
						array(
							'image_size'             => $image_size,
							'image_custom_dimension' => $custom_image_size,
							'image'                  => array(
								'id' => $thumbnail_id,
							),
						),
						'image'
					);
					?>
				<?php else : ?>
					<?php echo wc_placeholder_img( 'woocommerce_thumbnail' ); // phpcs:ignore ?>
				<?php endif; ?>
			</div>
		<?php
	}

	add_action( 'woocommerce_before_subcategory_title', 'xts_subcategory_loop_thumbnail', 10 );
}
