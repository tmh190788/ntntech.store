<?php
/**
 * General framework options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

Options::add_field(
	array(
		'id'          => 'page_comments',
		'name'        => esc_html__( 'Сomments on page', 'xts-theme' ),
		'description' => esc_html__( 'Enable/disable WordPress comments functionality for regular pages.', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'miscellaneous_section',
		'default'     => '1',
		'priority'    => 10,
	)
);

/**
 * Maintenance maintenance_mode (30 40).
 */

Options::add_field(
	array(
		'id'          => 'scroll_to_top',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Scroll to top button', 'xts-theme' ),
		'description' => esc_html__( 'This button moves you to the top of the page when you click it.', 'xts-theme' ),
		'section'     => 'miscellaneous_section',
		'default'     => '1',
		'priority'    => 50,
	)
);

/**
 * Menu overlay menu_overlay (60).
 */

Options::add_field(
	array(
		'id'           => 'custom_404_page',
		'name'         => esc_html__( 'Custom 404 page', 'xts-theme' ),
		'description'  => esc_html__( 'You can make your custom 404 page', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'miscellaneous_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_pages_array(),
		'priority'     => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'allow_upload_svg',
		'name'        => esc_html__( 'Allow SVG upload', 'xts-theme' ),
		'description' => wp_kses( __( 'Allow SVG uploads as well as SVG format for custom fonts. We suggest you to use <a href="https://ru.wordpress.org/plugins/safe-svg/">this plugin</a> to be sure that all uploaded content is safe. If you will install this plugin, you can disable this option.', 'xts-theme' ), xts_get_allowed_html() ),
		'type'        => 'switcher',
		'section'     => 'miscellaneous_section',
		'default'     => '0',
		'priority'    => 80,
	)
);

/**
 * White label (90-120).
 */
