<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$element_args = array(
	'product_source'        => 'upsells',
	'pagination'            => '',
	'design'                => xts_get_opt( 'product_loop_design' ),
	'items_per_page'        => array( 'size' => xts_get_opt( 'single_product_related_count' ) ),
	'view'                  => xts_get_opt( 'single_product_related_view' ),
	'columns'               => array( 'size' => xts_get_opt( 'single_product_related_per_row' ) ),
	'columns_tablet'        => array( 'size' => xts_get_opt( 'single_product_related_per_row_tablet' ) ),
	'columns_mobile'        => array( 'size' => xts_get_opt( 'single_product_related_per_row_mobile' ) ),
	'carousel_items'        => array( 'size' => xts_get_opt( 'single_product_related_per_row' ) ),
	'carousel_items_tablet' => array( 'size' => xts_get_opt( 'single_product_related_per_row_tablet' ) ),
	'carousel_items_mobile' => array( 'size' => xts_get_opt( 'single_product_related_per_row_mobile' ) ),
	'image_size'            => xts_get_opt( 'product_loop_image_size' ),
	'image_custom'          => xts_get_opt( 'product_loop_image_size_custom' ),
);

?>

<?php if ( $upsells ) : ?>
	<section class="up-sells upsells products">

		<h2>
			<?php esc_html_e( 'You may also like&hellip;', 'woocommerce' ); ?>
		</h2>

		<?php xts_products_template( $element_args ); ?>
	</section>
<?php endif; ?>
