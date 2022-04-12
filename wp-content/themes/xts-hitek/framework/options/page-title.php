<?php
/**
 * Page title options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

Options::add_field(
	array(
		'id'          => 'page_title_design',
		'name'        => esc_html__( 'Design', 'xts-theme' ),
		'description' => esc_html__( 'Select page title section design or disable it completely on all pages.', 'xts-theme' ),
		'group'       => esc_html__( 'Style', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'page_title_section',
		'options'     => xts_get_available_options( 'page_title_design' ),
		'default'     => 'default',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'page_title_size',
		'name'        => esc_html__( 'Size', 'xts-theme' ),
		'description' => esc_html__( 'You can set different sizes for your pages titles.', 'xts-theme' ),
		'group'       => esc_html__( 'Style', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'page_title_section',
		'options'     => array(
			'xs'  => array(
				'name'  => esc_html__( 'XS', 'xts-theme' ),
				'value' => 'xs',
			),
			's'   => array(
				'name'  => esc_html__( 'S', 'xts-theme' ),
				'value' => 's',
			),
			'm'   => array(
				'name'  => esc_html__( 'M', 'xts-theme' ),
				'value' => 'm',
			),
			'l'   => array(
				'name'  => esc_html__( 'L', 'xts-theme' ),
				'value' => 'l',
			),
			'xl'  => array(
				'name'  => esc_html__( 'XL', 'xts-theme' ),
				'value' => 'xl',
			),
			'xxl' => array(
				'name'  => esc_html__( 'XXL', 'xts-theme' ),
				'value' => 'xxl',
			),
		),
		'default'     => 'm',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'                  => 'page_title_bg',
		'name'                => esc_html__( 'Background', 'xts-theme' ),
		'description'         => esc_html__( 'Set background image or color, that will be used as a default for all page titles, shop page and blog.', 'xts-theme' ),
		'group'               => esc_html__( 'Style', 'xts-theme' ),
		'type'                => 'background',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'desktop_only'        => true,
		'section'             => 'page_title_section',
		'selector'            => '.xts-page-title-overlay',
		'priority'            => 30,
	)
);

Options::add_field(
	array(
		'name'                => esc_html__( 'Background (tablet)', 'xts-theme' ),
		'description'         => esc_html__( 'Set background image or color, that will be used as a default for all page titles, shop page and blog.', 'xts-theme' ),
		'group'               => esc_html__( 'Style', 'xts-theme' ),
		'id'                  => 'page_title_bg_tablet',
		'type'                => 'background',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'tablet_only'         => true,
		'section'             => 'page_title_section',
		'selector'            => '.xts-page-title-overlay',
		'priority'            => 31,
	)
);

Options::add_field(
	array(
		'name'                => esc_html__( 'Background (mobile)', 'xts-theme' ),
		'description'         => esc_html__( 'Set background image or color, that will be used as a default for all page titles, shop page and blog.', 'xts-theme' ),
		'group'               => esc_html__( 'Style', 'xts-theme' ),
		'id'                  => 'page_title_bg_mobile',
		'type'                => 'background',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'mobile_only'         => true,
		'section'             => 'page_title_section',
		'selector'            => '.xts-page-title-overlay',
		'priority'            => 32,
	)
);

Options::add_field(
	array(
		'id'          => 'page_title_color_scheme',
		'name'        => esc_html__( 'Color scheme', 'xts-theme' ),
		'description' => esc_html__( 'You can set different colors depending on it\'s background. May be light or dark.', 'xts-theme' ),
		'group'       => esc_html__( 'Style', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'page_title_section',
		'options'     => array(
			'inherit' => array(
				'name'  => esc_html__( 'Inherit', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/options/color/inherit.svg',
				'value' => 'inherit',
			),
			'dark'    => array(
				'name'  => esc_html__( 'Dark', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/options/color/dark.svg',
				'value' => 'dark',
			),
			'light'   => array(
				'name'  => esc_html__( 'Light', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/options/color/light.svg',
				'value' => 'light',
			),
		),
		'default'     => 'dark',
		'priority'    => 40,
		'class'       => 'xts-color-scheme-picker'
	)
);

Options::add_field(
	array(
		'id'          => 'page_title_breadcrumbs',
		'name'        => esc_html__( 'Show breadcrumbs', 'xts-theme' ),
		'description' => esc_html__( 'Displays a full chain of links to the current page.', 'xts-theme' ),
		'group'       => esc_html__( 'SEO', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'page_title_section',
		'default'     => '1',
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'yoast_pages_breadcrumbs',
		'name'        => esc_html__( 'Yoast breadcrumbs for pages', 'xts-theme' ),
		'description' => esc_html__( 'Requires Yoast SEO plugin to be installed. Replaces our theme\'s breadcrumbs for pages and blog with custom that come with the plugin. You need to enable and configure it in Dashboard -> SEO -> Search Appearance -> Breadcrumbs.', 'xts-theme' ),
		'group'       => esc_html__( 'SEO', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'page_title_section',
		'default'     => '0',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'page_title_tag',
		'name'        => esc_html__( 'Title tag', 'xts-theme' ),
		'description' => esc_html__( 'Choose which HTML tag to use to keep the page title text.', 'xts-theme' ),
		'group'       => esc_html__( 'SEO', 'xts-theme' ),
		'type'        => 'select',
		'section'     => 'page_title_section',
		'default'     => 'default',
		'options'     => array(
			'default' => array(
				'name'  => esc_html__( 'Default', 'xts-theme' ),
				'value' => 'default',
			),
			'h1'      => array(
				'name'  => 'h1',
				'value' => 'h1',
			),
			'h2'      => array(
				'name'  => 'h2',
				'value' => 'h2',
			),
			'h3'      => array(
				'name'  => 'h3',
				'value' => 'h3',
			),
			'h4'      => array(
				'name'  => 'h4',
				'value' => 'h4',
			),
			'h5'      => array(
				'name'  => 'h5',
				'value' => 'h5',
			),
			'h6'      => array(
				'name'  => 'h6',
				'value' => 'h6',
			),
			'p'       => array(
				'name'  => 'p',
				'value' => 'p',
			),
			'div'     => array(
				'name'  => 'div',
				'value' => 'div',
			),
			'span'    => array(
				'name'  => 'span',
				'value' => 'span',
			),
		),
		'priority'    => 70,
	)
);
