<?php
/**
 * Shop tools template.
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>

<div class="xts-shop-head row">
	<div class="xts-shop-tools col-fill">
		<?php do_action( 'xts_shop_tools_left_area' ); ?>
	</div>

	<div class="xts-shop-tools col-auto">
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop.
		 *
		 * @hooked woocommerce_output_all_notices - 10
		 * @hooked woocommerce_result_count - 20
		 * @hooked woocommerce_catalog_ordering - 30
		 */
		do_action( 'woocommerce_before_shop_loop' );
		?>
	</div>
</div>
