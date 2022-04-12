<?php
/**
 * Shape template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_shape_template' ) ) {
	/**
	 * Shape template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_shape_template( $element_args ) {
		$default_args = array(
			'shape_shadow_switcher' => 'no',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes = '';

		if ( 'yes' === $element_args['shape_shadow_switcher'] ) {
			$wrapper_classes .= ' xts-with-shadow';
		}

		?>
			<div class="xts-shape<?php echo esc_attr( $wrapper_classes ); ?>"></div>
		<?php
	}
}
