<?php
/**
 * Custom product page template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-single-product">
	<div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>
		<?php echo apply_filters( 'xts_single_product_builder_output', $content ); ?>
	</div>
</div>
