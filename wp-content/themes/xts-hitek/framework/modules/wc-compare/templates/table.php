<?php
/**
 * Compare table template
 *
 * @package xts
 * @version 1.0.0
 */

use XTS\Modules\WC_Compare;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Compare.
 *
 * @var WC_Compare $compare
 * @var array $products
 */

?>

<table class="xts-compare-table">
	<?php if ( $products ) : ?>
		<?php array_unshift( $products, array() ); ?>

		<?php foreach ( $fields as $key => $value ) : ?>
			<?php
			if ( ! $compare->is_products_have_field( $key, $products ) ) {
				continue;
			}

			?>
			<tr class="xts-compare-<?php echo esc_attr( $key ); ?>">
				<?php foreach ( $products as $product_id => $product ) : ?>
					<?php if ( ! empty( $product ) ) : ?>
						<td data-title="<?php echo esc_attr( $value ); ?>">
							<?php $compare->compare_display_field( $key, $product ); ?>
						</td>
					<?php else : ?>
						<th>
							<?php if ( 'Base' !== $value ) : ?>
								<?php echo esc_html( $value ); ?>
							<?php endif; ?>
						</th>
					<?php endif; ?>

				<?php endforeach ?>
			</tr>
		<?php endforeach; ?>
	<?php else : ?>
		<h4 class="xts-empty-page xts-empty-compare">
			<?php esc_html_e( 'Compare list is empty.', 'xts-theme' ); ?>
		</h4>

		<?php if ( $empty_compare_text ) : ?>
			<p class="xts-empty-page-text">
				<?php echo wp_kses( $empty_compare_text, xts_get_allowed_html() ); ?>
			</p>
		<?php endif; ?>

		<p class="return-to-shop">
			<a class="button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<?php esc_html_e( 'Return to shop', 'xts-theme' ); ?>
			</a>
		</p>
	<?php endif; ?>
</table>
