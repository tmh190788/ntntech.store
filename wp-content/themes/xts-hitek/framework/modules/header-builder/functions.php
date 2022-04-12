<?php
/**
 * Header builder functions
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! function_exists( 'xts_get_header' ) ) {
	/**
	 * Returns the current header instance (on frontend).
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	function xts_get_header() {
		return Modules::get( 'header-builder' )->frontend->header;
	}
}

if ( ! function_exists( 'xts_get_header_settings' ) ) {
	/**
	 * Get header settings and key elements params (search, cart widget, menu)
	 *
	 * @since 1.0.0
	 * @return array
	 */
	function xts_get_header_settings() {
		// Fix yoast php error.
		if ( ! is_object( xts_get_header() ) ) {
			return array();
		}

		return xts_get_header()->get_options();
	}
}

if ( ! function_exists( 'xts_generate_header' ) ) {
	/**
	 * Generate current header HTML structure
	 *
	 * @since 1.0.0
	 */
	function xts_generate_header() {
		if ( ! xts_needs_header() ) {
			return;
		}

		Modules::get( 'header-builder' )->frontend->generate_header();
	}

	add_action( 'xts_header', 'xts_generate_header' );
}

if ( ! function_exists( 'xts_get_header_builder' ) ) {
	/**
	 * Get main builder class instance
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	function xts_get_header_builder() {
		return Modules::get( 'header-builder' )->frontend->builder;
	}
}

if ( ! function_exists( 'xts_is_full_screen_search' ) ) {
	/**
	 * Is full screen search enabled
	 *
	 * @since 1.0.0
	 * @return string
	 */
	function xts_is_full_screen_search() {
		$settings = xts_get_header_settings();

		return isset( $settings['search'] ) && 'full-screen' === $settings['search']['display'];
	}
}

if ( ! function_exists( 'xts_get_header_settings' ) ) {
	/**
	 * Get header settings and key elements params (search, cart widget, menu)
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_header_settings() {
		// Fix yoast php error.
		if ( ! is_object( xts_get_header() ) ) {
			return array();
		}

		return xts_get_header()->get_options();
	}
}

if ( ! function_exists( 'xts_get_custom_icon' ) ) {
	/**
	 * Get custom icon for header elements
	 *
	 * @since 1.0.0
	 *
	 * @param array $params Parameters.
	 *
	 * @return string
	 */
	function xts_get_custom_icon( $params ) {
		$custom_icon_url = $custom_icon_width = $custom_icon_height = ''; // phpcs:ignore

		if ( isset( $params['url'] ) ) {
			$custom_icon_url = $params['url'];
		}

		if ( isset( $params['width'] ) && ! empty( $params['width'] ) ) {
			$custom_icon_width = $params['width'];
		}

		if ( isset( $params['height'] ) && ! empty( $params['height'] ) ) {
			$custom_icon_height = $params['height'];
		}

		if ( ! empty( $custom_icon_url ) ) {
			return '<img class="xts-custom-img" src="' . esc_url( $custom_icon_url ) . '" alt="' . esc_attr__( 'custom-icon', 'xts-theme' ) . '" width="' . esc_attr( $custom_icon_width ) . '" height="' . esc_attr( $custom_icon_height ) . '">';
		}

		return '';
	}
}

if ( ! function_exists( 'xts_set_default_header' ) ) {
	/**
	 * Setup default header from theme settings
	 *
	 * @since 1.0.0
	 */
	function xts_set_default_header() {
		if ( ! isset( $_GET['settings-updated'] ) || isset( $_GET['preset'] ) ) { // phpcs:ignore
			return;
		}

		$theme_settings_header_id = xts_get_opt( 'default_header' );

		if ( $theme_settings_header_id ) {
			update_option( 'xts_main_header', $theme_settings_header_id );
		}
	}

	add_filter( 'init', 'xts_set_default_header', 1000 );
}

if ( ! function_exists( 'xts_get_header_classes' ) ) {
	/**
	 * Function to get header classes
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function xts_get_header_classes() {
		$settings       = xts_get_header_settings();
		$header_classes = array();

		if ( $settings['overlap'] ) {
			$header_classes[] = 'xts-overlap-on';
		}

		if ( $settings['overlap'] && $settings['boxed'] ) {
			$header_classes[] = 'xts-design-boxed';
		}

		if ( $settings['overlap'] && $settings['background_hover'] ) {
			$header_classes[] = 'xts-hover-bg';
		}

		if ( $settings['full_width'] ) {
			$header_classes[] = 'xts-full-width';
		}

		if ( $settings['sticky_shadow'] ) {
			$header_classes[] = 'xts-with-shadow';
		}

		if ( $settings['sticky_effect'] ) {
			$header_classes[] = 'xts-scroll-' . $settings['sticky_effect'];
		}

		if ( $settings['sticky_clone'] && 'slide' === $settings['sticky_effect'] ) {
			xts_enqueue_js_script( 'header-builder' );
			$header_classes[] = 'xts-sticky-clone';
		} else {
			$header_classes[] = 'xts-sticky-real';
		}

		if ( $settings['hide_on_scroll'] ) {
			$header_classes[] = 'xts-scroll-hide';
		}

		return implode( ' ', $header_classes );
	}
}