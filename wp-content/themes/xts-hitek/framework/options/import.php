<?php
/**
 * Import options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

Options::add_field(
	array(
		'id'          => 'import_export',
		'name'        => esc_html__( 'Import/export', 'xts-theme' ),
		'description' => esc_html__( 'You can copy the content from the previously exported file with all theme settings data. Then click on "Import" button to replace your current settings with values from the file.', 'xts-theme' ),
		'type'        => 'import',
		'section'     => 'import_export_section',
		'priority'    => 10,
	)
);
