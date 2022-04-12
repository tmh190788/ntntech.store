<?php
/**
 * Content of the wishlist page with products
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-wishlist-content<?php echo esc_attr( $wrapper_classes ); ?>">
	<?php if ( count( $products ) > 0 ) : ?>
		<div class="xts-wishlist-head">
			<h4 class="xts-wishlist-title">
				<?php esc_html_e( 'Your products wishlist', 'xts-theme' ); ?>
			</h4>

			<?php if ( is_user_logged_in() && $wishlist_ui->is_editable() ) : ?>
				<?php
					xts_social_buttons_template(
						array(
							'page_link'  => $url . $wishlist->get_id() . '/',
							'size'       => 's',
							'label_text' => esc_html__( 'Share:', 'xts-theme' ),
						)
					);
				?>
			<?php endif; ?>
		</div>

		<?php if ( $wishlist_ui->is_editable() ) : ?>
			<?php add_action( 'xts_before_shop_loop_product', array( $wishlist_ui, 'remove_btn' ), 10 ); ?>
		<?php endif; ?>

		<?php $products = xts_products_template( $args ); ?>

		<?php remove_action( 'xts_before_shop_loop_product', array( $wishlist_ui, 'remove_btn' ), 10 ); ?>

		<?php if ( $products && $products->max_num_pages > 1 ) : ?>
			<nav class="woocommerce-pagination xts-pagination">
				<?php
				add_filter( 'get_pagenum_link', '__return_false' );

				echo paginate_links( // phpcs:ignore
					array(
						'base'      => $base,
						'add_args'  => true,
						'total'     => $products->max_num_pages,
						'prev_text' => '&larr;',
						'next_text' => '&rarr;',
						'type'      => 'list',
						'end_size'  => 3,
						'mid_size'  => 3,
					)
				);

				remove_action( 'get_pagenum_link', '__return_false' );
				?>
			</nav>
		<?php endif; ?>
	<?php else : ?>
		<h4 class="xts-empty-page xts-empty-wishlist">
			<?php esc_html_e( 'No products added to your wishlist.', 'xts-theme' ); ?>
		</h4>

		<?php if ( $wishlist_empty_text ) : ?>
			<p class="xts-empty-page-text">
				<?php echo wp_kses( $wishlist_empty_text, xts_get_allowed_html() ); ?>
			</p>
		<?php endif; ?>

		<p class="return-to-shop">
			<a class="button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<?php esc_html_e( 'Return to shop', 'xts-theme' ); ?>
			</a>
		</p>
	<?php endif; ?>
</div>
