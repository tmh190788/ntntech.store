<?php
/**
 * Product action buttons function
 *
 * @package xts
 */

use XTS\Framework\Modules;
use XTS\WC_Wishlist\Ui;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_action_buttons_template' ) ) {
	/**
	 * Product action buttons template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_action_buttons_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			'style' => 'icon',
		);

		$classes = '';

		$args = wp_parse_args( $element_args, $default_args );

		// Init modules.
		$wishlist_module_ui = Ui::get_instance();
		$compare_module     = Modules::get( 'wc-compare' );
		$size_guide_module  = Modules::get( 'wc-size-guide' );

		// Classes.
		$classes .= 'xts-style-' . $args['style'];

		?>
		<div class="xts-single-product-actions">
			<?php $wishlist_module_ui->add_to_wishlist_btn( $classes ); ?>
			<?php $compare_module->add_to_compare_btn( $classes ); ?>
			<?php $size_guide_module->size_guide_btn( $classes ); ?>
		</div>
		<?php
	}
}

