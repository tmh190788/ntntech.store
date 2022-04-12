<?php
/**
 * Countdown timer template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}


if ( ! function_exists( 'xts_countdown_timer_template' ) ) {
	/**
	 * Countdown timer template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_countdown_timer_template( $element_args ) {
		$default_args = array(
			'date'                      => '2019-12-12',
			'size'                      => 'm',
			'align'                     => 'left',
			'color'                     => 'default',
			'bg_color'                  => 'default',
			'countdown_shadow_switcher' => 'no',
			'extra_classes'             => '',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes = '';

		$wrapper_classes .= ' xts-textalign-' . $element_args['align'];
		$wrapper_classes .= ' xts-size-' . $element_args['size'];
		if ( 'yes' === $element_args['countdown_shadow_switcher'] ) {
			$wrapper_classes .= ' xts-with-shadow';
		}
		if ( 'default' !== $element_args['color'] ) {
			$wrapper_classes .= ' xts-textcolor-' . $element_args['color'];
		}
		if ( 'default' !== $element_args['bg_color'] ) {
			$wrapper_classes .= ' xts-bg-color-' . $element_args['bg_color'];
		}
		if ( $element_args['extra_classes'] ) {
			$wrapper_classes .= ' ' . $element_args['extra_classes'];
		}

		$timezone = apply_filters( 'xts_wp_timezone_countdown_timer', false ) ? get_option( 'timezone_string' ) : 'GMT';

		xts_enqueue_js_library( 'countdown-bundle' );
		xts_enqueue_js_script( 'countdown-timer-element' );

		?>
			<div class="xts-countdown-timer<?php echo esc_attr( $wrapper_classes ); ?>" data-end-date="<?php echo esc_attr( $element_args['date'] ); ?>" data-timezone="<?php echo esc_attr( $timezone ); ?>"></div>
		<?php

	}
}
