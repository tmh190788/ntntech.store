<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product-cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * XTS Settings
 */
$design                   = xts_get_opt( 'product_categories_design' );
$animation                = xts_get_opt( 'shop_animation' );
$animation_duration       = xts_get_opt( 'shop_animation_duration' );
$animation_in_view        = xts_get_opt( 'shop_animation_in_view' );
$different_sizes          = xts_get_opt( 'shop_different_sizes' );
$different_sizes_position = explode( ',', xts_get_opt( 'shop_different_sizes_position' ) );
$column_classes           = '';
$category_classes         = '';

// Template_args.
$template_args = array(
	'category' => $category,
);

// Category classes.
$category_classes .= ' xts-cat';

// Increase loop count.
xts_set_loop_prop( 'woocommerce_loop', xts_get_loop_prop( 'woocommerce_loop' ) + 1 );
$woocommerce_loop = xts_get_loop_prop( 'woocommerce_loop' );

// Different sizes.
if ( in_array( $woocommerce_loop, $different_sizes_position ) && $different_sizes ) { // phpcs:ignore
	$column_classes .= ' xts-wide';
}

// Animations.
if ( $animation && $animation_in_view ) {
	$column_classes .= ' xts-animation-' . $animation;
	$column_classes .= ' xts-animation-' . $animation_duration;
}

// Sub categories.
if ( 'subcat' === $design ) {
	$sub_categories = get_terms(
		'product_cat',
		array(
			'fields'       => 'all',
			'parent'       => $category->term_id,
			'hierarchical' => true,
			'hide_empty'   => true,
		)
	);

	if ( $sub_categories ) {
		$category_classes               .= ' xts-with-subcat';
		$template_args['sub_categories'] = $sub_categories;
	}
}

?>

<div class="xts-col<?php echo esc_attr( $column_classes ); ?>" data-loop="<?php echo esc_attr( $woocommerce_loop ); ?>">
	<div <?php wc_product_cat_class( $category_classes, $category ); ?>>
		<?php
		wc_get_template(
			'content-product-cat-' . $design . '.php',
			$template_args
		);
		?>
	</div>
</div>
