<?php
/**
 * Menu anchor template function.
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_menu_anchor_template' ) ) {
	/**
	 * Menu anchor template.
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_menu_anchor_template( $element_args ) {
		$default_args = array(
			'anchor' => 'anchor1',
			'offset' => '150',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		?>
		<div class="xts-menu-anchor" data-id="<?php echo esc_attr( $element_args['anchor'] ); ?>" data-offset="<?php echo esc_attr( $element_args['offset'] ); ?>"></div>
		<?php
	}
}
