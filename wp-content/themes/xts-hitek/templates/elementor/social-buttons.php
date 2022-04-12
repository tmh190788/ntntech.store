<?php
/**
 * Social buttons template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_social_buttons_template' ) ) {
	/**
	 * Social buttons template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_social_buttons_template( $element_args ) {
		if ( xts_is_core_module_exists() ) {
			xts_core_social_buttons_template( $element_args );
		}
	}
}

if ( ! function_exists( 'xts_social_buttons_shortcode' ) ) {
	/**
	 * Social buttons shortcode
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @return false|string
	 */
	function xts_social_buttons_shortcode( $element_args ) {
		ob_start();

		xts_social_buttons_template( $element_args );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'xts_get_social_buttons_link_attrs' ) ) {
	/**
	 * Social buttons link attributes
	 *
	 * @since 1.0.0
	 *
	 * @param string $type          Social link type.
	 * @param string $social        Social name.
	 * @param bool   $share_link    Share link.
	 * @param string $extra_classes Extra classes.
	 *
	 * @return string
	 */
	function xts_get_social_buttons_link_attrs( $type, $social, $share_link = false, $extra_classes = '' ) {
		$attrs = '';

		if ( $extra_classes ) {
			$extra_classes = ' ' . $extra_classes;
		}

		$attrs .= ' target="_blank"';
		$attrs .= ' class="xts-social-' . esc_attr( $social . $extra_classes ) . '"';

		if ( 'follow' === $type && 'email' !== $social ) {
			$attrs .= ' href="' . esc_url( xts_get_opt( $social . '_link' ) ) . '"';
		} elseif ( ( 'share' === $type && $share_link ) || 'email' === $social ) {
			$attrs .= ' href="' . $share_link . '"';
		}

		return $attrs;
	}
}
