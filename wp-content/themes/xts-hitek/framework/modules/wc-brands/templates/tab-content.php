<?php
/**
 * Brands tab content template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<?php foreach ( $brands as $key => $slug ) : ?>
	<?php $brand = get_term_by( 'slug', $slug, $attribute ); ?>

	<div class="xts-product-brand-description">
		<?php echo do_shortcode( get_term_meta( $brand->term_id, '_xts_brand_single_product_tab_content', true ) ); ?>
	</div>
<?php endforeach; ?>
