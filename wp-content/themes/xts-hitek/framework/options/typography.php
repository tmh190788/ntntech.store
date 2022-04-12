<?php
/**
 * Typography options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

/**
 * Basic.
 */
Options::add_field(
	array(
		'id'                   => 'content_typography',
		'type'                 => 'typography',
		'section'              => 'basic_typography_section',
		'name'                 => esc_html__( 'Typography', 'xts-theme' ),
		'group'                => esc_html__( 'Content', 'xts-theme' ),
		'selector'             => xts_get_typography_selectors( 'content-font' ),
		'selector-font-family' => xts_get_typography_selectors( 'content-font__font-family' ),
		'selector-font-size'   => xts_get_typography_selectors( 'content-font__font-size' ),
		'selector-color'       => xts_get_typography_selectors( 'content-font__color' ),
		'default'              => xts_get_default_value( 'content_typography' ),
		'priority'             => 10,
	)
);

Options::add_field(
	array(
		'id'                   => 'title_typography',
		'type'                 => 'typography',
		'section'              => 'basic_typography_section',
		'name'                 => esc_html__( 'Typography', 'xts-theme' ),
		'group'                => esc_html__( 'Titles', 'xts-theme' ),
		'selector'             => xts_get_typography_selectors( 'title-font' ),
		'selector-font-family' => xts_get_typography_selectors( 'title-font__font-family' ),
		'selector-color'       => xts_get_typography_selectors( 'title-font__color' ),
		'font-size'            => false,
		'default'              => xts_get_default_value( 'title_typography' ),
		'priority'             => 20,
	)
);

Options::add_field(
	array(
		'id'                   => 'entities_typography',
		'type'                 => 'typography',
		'section'              => 'basic_typography_section',
		'name'                 => esc_html__( 'Typography', 'xts-theme' ),
		'group'                => esc_html__( 'Entities names', 'xts-theme' ),
		'selector'             => xts_get_typography_selectors( 'entities-title-font' ),
		'selector-font-family' => xts_get_typography_selectors( 'entities-title-font__font-family' ),
		'selector-color'       => xts_get_typography_selectors( 'entities-title-font__color' ),
		'selector-color-hover' => xts_get_typography_selectors( 'entities-title-font__color-hover' ),
		'font-size'            => false,
		'color-hover'          => true,
		'priority'             => 30,
	)
);

Options::add_field(
	array(
		'id'                    => 'header_typography',
		'type'                  => 'typography',
		'section'               => 'basic_typography_section',
		'name'                  => esc_html__( 'Typography', 'xts-theme' ),
		'group'                 => esc_html__( 'Main navigation', 'xts-theme' ),
		'selector'              => xts_get_typography_selectors( 'nav-main' ),
		'selector-font-family'  => xts_get_typography_selectors( 'nav-main__font-family' ),
		'selector-font-size'    => xts_get_typography_selectors( 'nav-main__font-size' ),
		'selector-color'        => xts_get_typography_selectors( 'nav-main__color' ),
		'selector-color-hover'  => xts_get_typography_selectors( 'nav-main__color-hover' ),
		'selector-color-active' => xts_get_typography_selectors( 'nav-main__color-active' ),
		'color-hover'           => true,
		'color-active'          => true,
		'default'               => xts_get_default_value( 'header_typography' ),
		'priority'              => 40,
	)
);


Options::add_field(
	array(
		'id'       => 'widget_title_typography',
		'type'     => 'typography',
		'section'  => 'basic_typography_section',
		'name'     => esc_html__( 'Typography', 'xts-theme' ),
		'group'    => esc_html__( 'Widget titles', 'xts-theme' ),
		'selector' => '.widget-title',
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'                   => 'alt_typography',
		'type'                 => 'typography',
		'section'              => 'basic_typography_section',
		'name'                 => esc_html__( 'Typography', 'xts-theme' ),
		'group'                => esc_html__( 'Alternative font', 'xts-theme' ),
		'selector'             => xts_get_typography_selectors( 'secondary-font' ),
		'selector-font-family' => xts_get_typography_selectors( 'alternative-font__font-family' ),
		'font-size'            => false,
		'color'                => false,
		'priority'             => 60,
	)
);

/**
 * Advanced.
 */
Options::add_field(
	array(
		'id'          => 'advanced_typography',
		'type'        => 'typography',
		'section'     => 'advanced_typography_section',
		'name'        => esc_html__( 'Advanced typography', 'xts-theme' ),
		'selectors'   => xts_get_available_options( 'advanced_typography' ),
		'color-hover' => true,
		'priority'    => 10,
	)
);

/**
 * Typekit fonts.
 */
Options::add_field(
	array(
		'id'          => 'typekit_id',
		'type'        => 'text_input',
		'name'        => esc_html__( 'Your ID', 'xts-theme' ),
		'description' => 'Enter your <a target="_blank" href="https://typekit.com/account/kits">Typekit Kit ID</a>.',
		'section'     => 'typekit_fonts_section',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'typekit_fonts',
		'type'        => 'text_input',
		'name'        => esc_html__( 'Font face', 'xts-theme' ),
		'description' => esc_html__( 'Example: futura-pt, lato, arial, poppins', 'xts-theme' ),
		'section'     => 'typekit_fonts_section',
		'priority'    => 20,
	)
);

/**
 * Custom fonts
 */
Options::add_field(
	array(
		'id'       => 'custom_fonts',
		'name'     => esc_html__( 'Upload custom fonts', 'xts-theme' ),
		'type'     => 'custom_fonts',
		'fonts'    => array( 'woff', 'woff2' ),
		'section'  => 'custom_fonts_section',
		'priority' => 10,
	)
);
