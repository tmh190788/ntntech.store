<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

global $product;

$post_thumbnail_id = $product->get_image_id();
$gallery_image_ids = $product->get_gallery_image_ids();

if ( $post_thumbnail_id ) {
	array_unshift( $gallery_image_ids, $post_thumbnail_id );
}

if ( $gallery_image_ids && count( $gallery_image_ids ) > 1 ) {
	echo xts_wc_get_gallery_html( $gallery_image_ids ); // phpcs:ignore
}
