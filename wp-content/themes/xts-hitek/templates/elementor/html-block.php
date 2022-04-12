<?php
/**
 * Html block template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_html_block_template' ) ) {
	/**
	 * Html block template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_html_block_template( $element_args ) {
		$default_args = array(
			'html_block_id' => '0',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		if ( ! $element_args['html_block_id'] ) {
			?>
				<div class="xts-notification xts-color-info">
					<?php esc_html_e( 'You need to select an HTML Block from the dropdown to display its content here.', 'xts-theme' ); ?>
				</div>
			<?php
			return;
		}

		echo xts_get_html_block_content( $element_args['html_block_id'] ); // phpcs:ignore
	}
}
