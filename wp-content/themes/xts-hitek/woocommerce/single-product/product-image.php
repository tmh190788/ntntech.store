<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$gallery_image_ids = $product->get_gallery_image_ids();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
		'row',
	)
);

/**
 * XTS Settings
 */
xts_enqueue_js_script( 'single-product-gallery' );

$is_quick_view       = xts_get_loop_prop( 'is_quick_view' );
$source              = isset( $source ) ? $source : 'default';
$click_action        = isset( $main_gallery_click_action ) ? $main_gallery_click_action : xts_get_opt( 'single_product_main_gallery_click_action' );
$click_action        = $is_quick_view ? 'without' : $click_action;
$thumbnails_position = isset( $thumbnails_gallery_position ) ? $thumbnails_gallery_position : xts_get_opt( 'single_product_thumbnails_gallery_position' );
$lightbox_gallery    = isset( $main_gallery_lightbox_gallery ) ? $main_gallery_lightbox_gallery : xts_get_opt( 'single_product_main_gallery_lightbox_gallery' );
$thumbnails_count    = isset( $thumbnails_gallery_count ) ? $thumbnails_gallery_count['size'] : xts_get_opt( 'single_product_thumbnails_gallery_count' );

$main_gallery_wrapper_classes       = '';
$main_gallery_classes               = '';
$main_gallery_attrs                 = '';
$thumbnails_gallery_wrapper_classes = '';
$thumbnails_gallery_classes         = '';
$thumbnails_gallery_attrs           = '';

if ( $is_quick_view ) {
	$thumbnails_position = 'without';
}

// Wrapper classes.
$wrapper_classes[] = ' xts-style-' . $thumbnails_position;
if ( 'side' === $thumbnails_position && $gallery_image_ids ) {
	$wrapper_classes[] = ' row-spacing-10';
}

// Main gallery classes.
if ( 'side' === $thumbnails_position && $gallery_image_ids ) {
	$main_gallery_wrapper_classes .= ' col-lg-9 order-lg-last';
} else {
	$main_gallery_wrapper_classes .= ' col-lg-12';
}

$main_gallery_classes .= ' xts-action-' . $click_action;

if ( 'photoswipe' === $click_action ) {
	xts_enqueue_js_library( 'photoswipe-bundle' );
	xts_enqueue_js_script( 'single-product-gallery-photoswipe' );
}

if ( 'zoom' === $click_action ) {
	wp_enqueue_script( 'zoom' );
	xts_enqueue_js_script( 'single-product-gallery-zoom' );
}

if ( 'grid-1' === $thumbnails_position ) {
	$main_gallery_classes .= xts_get_row_classes( 1, 1, 1, 10 );
} elseif ( 'grid-2' === $thumbnails_position || 'grid-comb' === $thumbnails_position ) {
	$main_gallery_classes .= xts_get_row_classes( 2, 1, 1, 10 );
} elseif ( 'bottom' === $thumbnails_position || 'side' === $thumbnails_position || 'without' === $thumbnails_position ) {
	$main_gallery_classes .= xts_get_row_classes( 1, 1, 1, 10 );
	$main_gallery_classes .= xts_get_carousel_classes(
		array(
			'arrows_horizontal_position' => 'inside',
			'arrows_vertical_position'   => 'sides',
			'arrows_design'              => 'default',
			'source'                     => 'single_product',
		)
	);
}

if ( $lightbox_gallery ) {
	$main_gallery_classes .= ' xts-lightbox-gallery';
}

// Thumbnails gallery classes.
$thumbnails_gallery_id = uniqid();
if ( 'side' === $thumbnails_position ) {
	$thumbnails_gallery_wrapper_classes .= ' col-lg-3 order-lg-first';
	$thumbnails_gallery_classes         .= xts_get_row_classes( $thumbnails_count, $thumbnails_count, $thumbnails_count, 10 );
	$thumbnails_gallery_classes         .= xts_get_carousel_classes(
		array(
			'arrows_horizontal_position' => 'disabled',
			'arrows_vertical_position'   => 'disabled',
			'arrows_design'              => 'disabled',
			'source'                     => 'single_product',
		)
	);
} else {
	$thumbnails_gallery_wrapper_classes .= ' col-lg-12';
	$thumbnails_gallery_classes         .= xts_get_row_classes( $thumbnails_count, $thumbnails_count, $thumbnails_count, 10 );
	$thumbnails_gallery_classes         .= xts_get_carousel_classes(
		array(
			'arrows_horizontal_position' => 'inside',
			'arrows_vertical_position'   => 'sides',
			'arrows_design'              => 'default',
			'source'                     => 'single_product',
		)
	);
}

// Add main image to gallery images.
if ( $post_thumbnail_id ) {
	array_unshift( $gallery_image_ids, $post_thumbnail_id );
}

?>
<?php if ( ! $is_quick_view && 'default' === $source ) : ?>
	<div class="<?php echo esc_attr( xts_get_single_product_main_gallery_classes() ); ?>">
<?php endif; ?>
	<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">

		<figure class="woocommerce-product-gallery__wrapper<?php echo esc_attr( $main_gallery_wrapper_classes ); ?>">

			<div class="xts-single-product-images-wrapper">

				<?php do_action( 'xts_before_single_product_main_gallery' ); ?>

				<div class="xts-single-product-images<?php echo esc_attr( $main_gallery_classes ); ?>" data-controls-id="<?php echo esc_attr( uniqid() ); ?>">
					<?php if ( $gallery_image_ids ) : ?>
						<?php echo xts_wc_get_gallery_html( $gallery_image_ids, true ); // phpcs:ignore ?>
					<?php else : ?>
						<div class="xts-col woocommerce-product-gallery__image">
							<div class="xts-col-inner">
								<img src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ); ?>" alt="<?php esc_attr_e( 'Awaiting product image', 'woocommerce' ); ?>">
							</div>
						</div>
					<?php endif; ?>
				</div>

				<div class="xts-single-product-image-actions">
					<?php do_action( 'xts_single_product_main_gallery_action_buttons' ); ?>
				</div>

				<?php do_action( 'xts_after_single_product_main_gallery' ); ?>

			</div>

		</figure>

		<?php if ( $gallery_image_ids && count( $gallery_image_ids ) > 1 && ( 'side' === $thumbnails_position || 'bottom' === $thumbnails_position ) ) : ?>
			<div class="<?php echo esc_attr( $thumbnails_gallery_wrapper_classes ); ?>">
				<div class="xts-single-product-thumb-wrapper">
					<div class="xts-single-product-thumb<?php echo esc_attr( $thumbnails_gallery_classes ); ?>" data-thumb-count="<?php echo esc_attr( $thumbnails_count ); ?>" data-controls-id="<?php echo esc_attr( $thumbnails_gallery_id ); ?>">
						<?php do_action( 'woocommerce_product_thumbnails' ); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php if ( ! $is_quick_view && 'default' === $source ) : ?>
	</div>
<?php endif; ?>
