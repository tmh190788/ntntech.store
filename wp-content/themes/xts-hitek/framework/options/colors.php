<?php
/**
 * Colors options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

/**
 * Colors.
 */
Options::add_field(
	array(
		'id'                    => 'primary_color',
		'name'                  => esc_html__( 'Primary color', 'xts-theme' ),
		'description'           => esc_html__( 'Pick a color for all primary elements like buttons, titles, etc.', 'xts-theme' ),
		'type'                  => 'color',
		'section'               => 'general_colors_section',
		'selector'              => xts_get_typography_selectors( 'primary-color' ),
		'selector_bg'           => xts_get_typography_selectors( 'primary-background' ),
		'selector_border'       => xts_get_typography_selectors( 'primary-border-color' ),
		'selector_darken_hover' => xts_get_typography_selectors( 'primary-button-background' ),
		'auto_hover_selector'   => true,
		'default'               => xts_get_default_value( 'primary_color' ),
		'priority'              => 10,
	)
);

Options::add_field(
	array(
		'id'                    => 'secondary_color',
		'name'                  => esc_html__( 'Secondary color', 'xts-theme' ),
		'description'           => esc_html__( 'Color for secondary elements on the website.', 'xts-theme' ),
		'type'                  => 'color',
		'section'               => 'general_colors_section',
		'selector'              => xts_get_typography_selectors( 'secondary-color' ),
		'selector_bg'           => xts_get_typography_selectors( 'secondary-background' ),
		'selector_border'       => xts_get_typography_selectors( 'secondary-border-color' ),
		'selector_darken_hover' => xts_get_typography_selectors( 'secondary-button-background' ),
		'auto_hover_selector'   => true,
		'priority'              => 20,
	)
);

Options::add_field(
	array(
		'id'             => 'links_color',
		'name'           => esc_html__( 'Links color', 'xts-theme' ),
		'description'    => esc_html__( 'Set the color for links on your pages, posts and products content.', 'xts-theme' ),
		'type'           => 'color',
		'section'        => 'general_colors_section',
		'selector'       => xts_get_typography_selectors( 'link-color' ),
		'selector_hover' => xts_get_typography_selectors( 'link-color-hover' ),
		'default'        => xts_get_default_value( 'links_color' ),
		'priority'       => 30,
	)
);

/**
 * Pages bg colors.
 */
Options::add_field(
	array(
		'id'       => 'all_pages_bg',
		'name'     => esc_html__( 'ALL pages', 'xts-theme' ),
		'group'    => esc_html__( 'General', 'xts-theme' ),
		'type'     => 'background',
		'default'  => array(
			'idle' => '#FF0000',
		),
		'section'  => 'pages_bg_colors_section',
		'selector' => '.xts-site-wrapper',
		'priority' => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'body_bg',
		'name'     => esc_html__( 'Body', 'xts-theme' ),
		'group'    => esc_html__( 'General', 'xts-theme' ),
		'type'     => 'background',
		'section'  => 'pages_bg_colors_section',
		'selector' => 'body',
		'priority' => 20,
		'requires' => array(
			array(
				'key'     => 'site_layout',
				'compare' => 'equals',
				'value'   => 'boxed',
			),
		),
	)
);

Options::add_field(
	array(
		'id'       => 'home_page_bg',
		'name'     => esc_html__( 'Home page', 'xts-theme' ),
		'group'    => esc_html__( 'General', 'xts-theme' ),
		'type'     => 'background',
		'section'  => 'pages_bg_colors_section',
		'selector' => '.home .xts-site-wrapper',
		'priority' => 30,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_bg',
		'name'     => esc_html__( 'Blog archive', 'xts-theme' ),
		'group'    => esc_html__( 'Blog', 'xts-theme' ),
		'type'     => 'background',
		'default'  => array(
			'idle' => '#FF0000',
		),
		'section'  => 'pages_bg_colors_section',
		'selector' => '.blog .xts-site-wrapper',
		'priority' => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_single_bg',
		'name'     => esc_html__( 'Single post', 'xts-theme' ),
		'group'    => esc_html__( 'Blog', 'xts-theme' ),
		'type'     => 'background',
		'default'  => array(
			'idle' => '#FF0000',
		),
		'section'  => 'pages_bg_colors_section',
		'selector' => '.single-post .xts-site-wrapper',
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'       => 'portfolio_bg',
		'name'     => esc_html__( 'Portfolio archive', 'xts-theme' ),
		'group'    => esc_html__( 'Portfolio', 'xts-theme' ),
		'type'     => 'background',
		'default'  => array(
			'idle' => '#FF0000',
		),
		'section'  => 'pages_bg_colors_section',
		'selector' => '.post-type-archive-xts-portfolio .xts-site-wrapper, .page-template-xts-portfolio .xts-site-wrapper',
		'priority' => 60,
	)
);

Options::add_field(
	array(
		'id'       => 'portfolio_single_bg',
		'name'     => esc_html__( 'Single project', 'xts-theme' ),
		'group'    => esc_html__( 'Portfolio', 'xts-theme' ),
		'type'     => 'background',
		'default'  => array(
			'idle' => '#FF0000',
		),
		'section'  => 'pages_bg_colors_section',
		'selector' => '.single-xts-portfolio .xts-site-wrapper',
		'priority' => 70,
	)
);

Options::add_field(
	array(
		'id'       => 'shop_bg',
		'name'     => esc_html__( 'Shop archive', 'xts-theme' ),
		'group'    => esc_html__( 'Shop', 'xts-theme' ),
		'type'     => 'background',
		'default'  => array(),
		'section'  => 'pages_bg_colors_section',
		'selector' => '.xts-shop-archive .xts-site-content',
		'priority' => 80,
	)
);

Options::add_field(
	array(
		'id'       => 'single_product_bg',
		'name'     => esc_html__( 'Single product', 'xts-theme' ),
		'group'    => esc_html__( 'Shop', 'xts-theme' ),
		'type'     => 'background',
		'default'  => array(),
		'section'  => 'pages_bg_colors_section',
		'selector' => '.single-product .xts-site-content',
		'priority' => 90,
	)
);

/**
 * Buttons.
 */
Options::add_field(
	array(
		'id'                  => 'default_button_bg_color',
		'name'                => esc_html__( 'Background color', 'xts-theme' ),
		'group'               => esc_html__( 'Regular buttons', 'xts-theme' ),
		'type'                => 'color',
		'default'             => array(),
		'section'             => 'buttons_colors_section',
		'selector_bg'         => xts_get_typography_selectors( 'regular-button' ),
		'selector_bg_hover'   => xts_get_typography_selectors( 'regular-button' ),
		'auto_hover_selector' => true,
		'priority'            => 10,
	)
);

Options::add_field(
	array(
		'id'                  => 'default_button_text_color',
		'name'                => esc_html__( 'Text color', 'xts-theme' ),
		'group'               => esc_html__( 'Regular buttons', 'xts-theme' ),
		'type'                => 'color',
		'default'             => array(),
		'section'             => 'buttons_colors_section',
		'selector'            => xts_get_typography_selectors( 'regular-button' ),
		'selector_hover'      => xts_get_typography_selectors( 'regular-button' ),
		'auto_hover_selector' => true,
		'priority'            => 20,
	)
);

Options::add_field(
	array(
		'id'                  => 'accent_button_bg_color',
		'name'                => esc_html__( 'Background color', 'xts-theme' ),
		'group'               => esc_html__( 'Accent buttons', 'xts-theme' ),
		'type'                => 'color',
		'default'             => array(),
		'section'             => 'buttons_colors_section',
		'selector_bg'         => xts_get_typography_selectors( 'accent-button' ),
		'selector_bg_hover'   => xts_get_typography_selectors( 'accent-button' ),
		'auto_hover_selector' => true,
		'priority'            => 30,
	)
);

Options::add_field(
	array(
		'id'                  => 'accent_button_text_color',
		'name'                => esc_html__( 'Text color', 'xts-theme' ),
		'group'               => esc_html__( 'Accent buttons', 'xts-theme' ),
		'type'                => 'color',
		'default'             => array(),
		'section'             => 'buttons_colors_section',
		'selector'            => xts_get_typography_selectors( 'accent-button' ),
		'selector_hover'      => xts_get_typography_selectors( 'accent-button' ),
		'auto_hover_selector' => true,
		'priority'            => 40,
	)
);

Options::add_field(
	array(
		'id'                  => 'shop_button_bg_color',
		'name'                => esc_html__( 'Background color', 'xts-theme' ),
		'group'               => esc_html__( 'Shop buttons', 'xts-theme' ),
		'type'                => 'color',
		'default'             => array(),
		'section'             => 'buttons_colors_section',
		'selector_bg'         => xts_get_typography_selectors( 'shop-button' ),
		'selector_bg_hover'   => xts_get_typography_selectors( 'shop-button' ),
		'auto_hover_selector' => true,
		'priority'            => 50,
	)
);

Options::add_field(
	array(
		'id'                  => 'shop_button_text_color',
		'name'                => esc_html__( 'Text color', 'xts-theme' ),
		'group'               => esc_html__( 'Shop buttons', 'xts-theme' ),
		'type'                => 'color',
		'default'             => array(),
		'section'             => 'buttons_colors_section',
		'selector'            => xts_get_typography_selectors( 'shop-button' ),
		'selector_hover'      => xts_get_typography_selectors( 'shop-button' ),
		'auto_hover_selector' => true,
		'priority'            => 60,
	)
);
