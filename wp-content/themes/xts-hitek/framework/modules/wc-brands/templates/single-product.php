<?php
/**
 * Single product brands template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-single-product-brands">
	<span class="xts-label">
		<?php echo esc_html( _n( 'Brand:', 'Brands:', count( $brands ), 'xts-theme' ) ); ?>
	</span>

	<?php foreach ( $brands as $brand ) : ?>
		<?php
		$image       = get_term_meta( $brand->term_id, '_xts_attribute_image', true );
		$filter_name = 'filter_' . sanitize_title( str_replace( 'pa_', '', $attribute ) );

		if ( is_object( $taxonomy ) && $taxonomy->public ) {
			$attribute_link = get_term_link( $brand->term_id, $brand->taxonomy );
		} else {
			$attribute_link = add_query_arg( $filter_name, $brand->slug, $link );
		}
		?>

		<?php if ( isset( $image['id'] ) && $image['id'] ) : ?>
			<a href="<?php echo esc_url( $attribute_link ); ?>">
				<?php echo wp_get_attachment_image( $image['id'], 'full' ); ?>
			</a>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
