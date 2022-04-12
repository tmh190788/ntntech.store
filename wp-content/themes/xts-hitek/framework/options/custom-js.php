<?php
/**
 * Custom JS options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

Options::add_field(
	array(
		'id'       => 'js_global',
		'name'     => esc_html__( 'Global JS', 'xts-theme' ),
		'type'     => 'editor',
		'language' => 'javascript',
		'section'  => 'custom_js_section',
		'priority' => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'js_document_ready',
		'name'        => esc_html__( 'On document ready', 'xts-theme' ),
		'description' => esc_html__( 'Will be executed on $(document).ready()', 'xts-theme' ),
		'type'        => 'editor',
		'language'    => 'javascript',
		'section'     => 'custom_js_section',
		'priority'    => 20,
	)
);
