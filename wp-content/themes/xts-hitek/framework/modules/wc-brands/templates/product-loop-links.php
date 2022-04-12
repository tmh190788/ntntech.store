<?php
/**
 * Product loop brands template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-product-brands xts-product-meta">
	<?php foreach ( $brands as $brand ) : ?>
		<?php
		$filter_name = 'filter_' . sanitize_title( str_replace( 'pa_', '', $brand_option ) );

		if ( is_object( $taxonomy ) && $taxonomy->public ) {
			$attribute_link = get_term_link( $brand->term_id, $brand->taxonomy );
		} else {
			$attribute_link = add_query_arg( $filter_name, $brand->slug, $link );
		}

		$sep = ', ';

		if ( end( $brands ) === $brand ) {
			$sep = '';
		}

		?>

		<a href="<?php echo esc_url( $attribute_link ); ?>">
			<?php echo esc_html( $brand->name ); ?>
		</a>

		<?php echo esc_html( $sep ); ?>

	<?php endforeach; ?>
</div>
