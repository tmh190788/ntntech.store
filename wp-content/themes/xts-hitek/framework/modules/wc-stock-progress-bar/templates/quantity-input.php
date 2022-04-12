<?php
/**
 * Input template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="options_group">
	<?php
	woocommerce_wp_text_input(
		array(
			'id'          => 'xts_total_stock_quantity',
			'label'       => esc_html__( 'Initial number in stock', 'xts-theme' ),
			'desc_tip'    => 'true',
			'description' => esc_html__( 'Required for stock progress bar option', 'xts-theme' ),
			'type'        => 'text',
		)
	);
	?>
</div>
