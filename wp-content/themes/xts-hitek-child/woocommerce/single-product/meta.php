<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
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

global $product;

$sku = $product->get_sku() ? $product->get_sku() : esc_html__( 'N/A', 'woocommerce' );
$terms = get_the_terms( $product->get_id(), 'product_cat' );
$is_stock_list = false;
$stock_list_url = '';
if (!empty($terms)) {
	foreach($terms as $term) {
		$term_id = $term->term_id;
		if (get_field( 'stocklist_page',  'product_cat_' . $term_id) == 1) {
			$is_stock_list = true;
			$stock_list_url = get_category_link( $term_id );
			break;
		}
	}
}
?>

<?php
	if ($is_stock_list) :
?>
<div class="stocklist-wrapper">
	<div class="stocklist-info product_meta">
		<div><span class="xts-label">Model: </span><?= get_field('model') ?></div>
		<div><span class="xts-label">Maker: </span><a href="<?=$stock_list_url?>?keyword=<?=get_field('maker')?>&filter=maker"><?= get_field('maker') ?></a></div>
		<div><span class="xts-label">Description: </span><a href="<?=$stock_list_url?>?keyword=<?=get_field('description')?>&filter=description"><?= get_field('description') ?></a></div>
	</div>
	<div class="stocklist-buttons row">
		<?php if (!empty(get_field('download_catalog'))) : ?>
			<div class="col-md-4 col-12">
				<a href="<?= get_field('download_catalog') ?>" target="_blank" class="stocklist-btn download"><i class="fas fa-file-pdf"></i> Download Catalog</a>
			</div>
		<?php endif; ?>
		<?php if (!empty(get_field('detail_product_series'))) : ?>
			<div class="col-md-4 col-12">
				<a href="<?= get_field('detail_product_series') ?>" target="_blank" class="stocklist-btn detail"><i class="fas fa-file-alt"></i> Detail Product Series</a>
			</div>
		<?php endif; ?>
		<div class="col-md-4 col-12">
			<a href="#" class="stocklist-btn request"><i class="fas fa-envelope"></i> Request A Quote</a>
		</div>
	</div>
</div>

<?php
	endif;
?>
<?php
	if (!$is_stock_list && !empty(get_field('catalog'))) :
?>
<div class="stocklist-buttons row no-stocklist">
		<div class="col-md-6">
			<a href="<?= get_field('catalog') ?>" target="_blank" class="stocklist-btn download"><i class="fas fa-file-pdf"></i> Download Catalog</a>
		</div>
</div>
<?php
	endif;
?>

<div class="product_meta <?=$is_stock_list ? 'with-stocklist' : ''?>">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
		<span class="sku_wrapper">
			<span class="xts-label">
				<?php esc_html_e( 'SKU:', 'woocommerce' ); ?>
			</span>
			<span class="sku">
				<?php echo esc_html( $sku ); ?>
			</span>
		</span>
	<?php endif; ?>

	<?php echo wc_get_product_category_list( $product->get_id(), '<span class="seperator">, </span>', '<span class="posted_in"><span class="xts-label">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' </span>', '</span>' ); ?>

	<?php echo wc_get_product_tag_list( $product->get_id(), '<span class="seperator">, </span>', '<span class="tagged_as"><span class="xts-label">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' </span>', '</span>' ); ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>