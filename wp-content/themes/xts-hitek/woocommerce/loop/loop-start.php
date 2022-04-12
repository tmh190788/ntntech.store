<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$products_per_row                = xts_get_products_per_row();
$products_per_row_tablet         = xts_get_opt( 'products_per_row_tablet' );
$products_per_row_mobile         = xts_get_opt( 'products_per_row_mobile' );
$spacing                         = xts_get_opt( 'shop_spacing' );
$masonry                         = xts_get_opt( 'shop_masonry' );
$product_loop_countdown          = xts_get_opt( 'product_loop_sale_countdown' );
$single_product_countdown        = xts_get_opt( 'single_product_sale_countdown' );
$different_sizes                 = xts_get_opt( 'shop_different_sizes' );
$animation_in_view               = xts_get_opt( 'shop_animation_in_view' );
$animation_delay                 = xts_get_opt( 'shop_animation_delay' );
$title_lines_limit               = xts_get_opt( 'product_title_lines_limit' );
$product_categories_design       = xts_get_opt( 'product_categories_design' );
$product_categories_color_scheme = xts_get_opt( 'product_categories_color_scheme' );
$product_loop_design             = xts_get_opt( 'product_loop_design' );
$shop_pagination                 = xts_get_opt( 'shop_pagination' );
$wrapper_classes                 = '';

// Wrapper classes.
$wrapper_classes .= xts_get_row_classes( $products_per_row, $products_per_row_tablet, $products_per_row_mobile, $spacing );
$wrapper_classes .= ' xts-prod-design-' . $product_loop_design;
$wrapper_classes .= ' xts-cat-design-' . $product_categories_design;
if ( 'summary' === $product_loop_design || 'summary-alt' === $product_loop_design ) {
	wp_enqueue_script( 'imagesloaded' );
	xts_enqueue_js_script( 'product-hover-summary' );
}
if ( $masonry ) {
	wp_enqueue_script( 'imagesloaded' );
	xts_enqueue_js_library( 'isotope-bundle' );
	xts_enqueue_js_script( 'masonry-layout' );
	$wrapper_classes .= ' xts-masonry-layout';
}
if ( $different_sizes ) {
	$wrapper_classes .= ' xts-different-sizes';
}
if ( $animation_in_view ) {
	xts_enqueue_js_script( 'items-animation-in-view' );
	$wrapper_classes .= ' xts-in-view-animation';
}
if ( 'default' !== $title_lines_limit ) {
	$wrapper_classes .= ' xts-title-limit-' . $title_lines_limit;
}
if ( 'inherit' !== $product_categories_color_scheme ) {
	$wrapper_classes .= ' xts-scheme-' . $product_categories_color_scheme . '-cat';
}

?>

<div id="main_loop" class="xts-products products<?php echo esc_attr( $wrapper_classes ); ?>" data-source="main_loop" data-paged="1" data-animation-delay="<?php echo esc_attr( $animation_delay ); ?>">
