<?php
/**
 * Child theme functions file.
 *
 * @package xts
 */

/**
 * Enqueue script and styles for child theme.
 */
function xts_child_enqueue_styles() {
	wp_enqueue_style( 'xts-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'xts-style' ), XTS_VERSION );
}
add_action( 'wp_enqueue_scripts', 'xts_child_enqueue_styles', 200 );
