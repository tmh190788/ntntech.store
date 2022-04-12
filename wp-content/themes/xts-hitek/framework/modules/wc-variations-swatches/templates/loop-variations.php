<?php
/**
 * Grid variations template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-product-variations" data-variations_count="<?php echo esc_attr( $count_variations ); ?>">
	<?php
	wc_get_template(
		'single-product/add-to-cart/variable.php',
		array(
			'available_variations' => $get_variations ? $product->get_available_variations() : false,
			'attributes'           => $attributes,
			'selected_attributes'  => $product->get_default_attributes(),
			'quick_shop'           => 'yes',
		)
	);
	?>
</div>
