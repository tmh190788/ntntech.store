<?php
/**
 * Custom CSS options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

Options::add_field(
	array(
		'id'       => 'css_global',
		'name'     => esc_html__( 'Global CSS', 'xts-theme' ),
		'type'     => 'editor',
		'language' => 'css',
		'section'  => 'custom_css_section',
		'priority' => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'css_desktop',
		'name'     => esc_html__( 'Desktop CSS', 'xts-theme' ),
		'type'     => 'editor',
		'language' => 'css',
		'section'  => 'custom_css_section',
		'priority' => 20,

	)
);

Options::add_field(
	array(
		'id'       => 'css_tablet',
		'name'     => esc_html__( 'Tablet CSS', 'xts-theme' ),
		'type'     => 'editor',
		'language' => 'css',
		'section'  => 'custom_css_section',
		'priority' => 30,

	)
);

Options::add_field(
	array(
		'id'       => 'css_mobile',
		'name'     => esc_html__( 'Mobile CSS', 'xts-theme' ),
		'type'     => 'editor',
		'language' => 'css',
		'section'  => 'custom_css_section',
		'priority' => 40,

	)
);
