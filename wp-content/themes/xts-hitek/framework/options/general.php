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

/**
 * Layout.
 */
Options::add_field(
	array(
		'id'           => 'default_header',
		'name'         => esc_html__( 'Header', 'xts-theme' ),
		'description'  => esc_html__( 'Set your default header for all pages from the list of all headers created with our Header Builder.', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'general_layout_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_headers_array(),
		'priority'     => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'site_layout',
		'name'        => esc_html__( 'Container style', 'xts-theme' ),
		'description' => esc_html__( 'You can make your content wrapper boxed', 'xts-theme' ),
		'group'       => esc_html__( 'Site container', 'xts-theme' ),
		'type'        => 'select',
		'section'     => 'general_layout_section',
		'options'     => array(
			'default' => array(
				'name'  => esc_html__( 'Default', 'xts-theme' ),
				'value' => 'default',
			),
			'boxed'   => array(
				'name'  => esc_html__( 'Boxed', 'xts-theme' ),
				'value' => 'boxed',
			),
		),
		'default'     => 'default',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'site_width',
		'name'        => esc_html__( 'Container width', 'xts-theme' ),
		'description' => esc_html__( 'Specify your custom website container width in pixels.', 'xts-theme' ),
		'group'       => esc_html__( 'Site container', 'xts-theme' ),
		'type'        => 'range',
		'section'     => 'general_layout_section',
		'min'         => 1025,
		'max'         => 1920,
		'step'        => 5,
		'default'     => xts_get_default_value( 'site_width' ),
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'negative_gap',
		'name'        => esc_html__( 'Negative gap', 'xts-theme' ),
		'description' => esc_html__( 'Add a negative margin to each Elementor section to align the content with your website container.', 'xts-theme' ),
		'group'       => esc_html__( 'Site container', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'general_layout_section',
		'options'     => array(
			'enabled'  => array(
				'name'  => esc_html__( 'Enabled', 'xts-theme' ),
				'value' => 'enabled',
			),
			'disabled' => array(
				'name'  => esc_html__( 'Disabled', 'xts-theme' ),
				'value' => 'disabled',
			),
		),
		'default'     => 'enabled',
		'priority'    => 31,
	)
);

Options::add_field(
	array(
		'id'          => 'sidebar_position',
		'name'        => esc_html__( 'Position', 'xts-theme' ),
		'description' => esc_html__( 'Select main content and sidebar alignment.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'general_layout_section',
		'options'     => array(
			'disabled' => array(
				'name'  => esc_html__( 'Disabled', 'xts-theme' ),
				'value' => 'disabled',
			),
			'left'     => array(
				'name'  => esc_html__( 'Left', 'xts-theme' ),
				'value' => 'left',
			),
			'right'    => array(
				'name'  => esc_html__( 'Right', 'xts-theme' ),
				'value' => 'right',
			),
		),
		'default'     => 'right',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'sidebar_size',
		'name'        => esc_html__( 'Size', 'xts-theme' ),
		'description' => esc_html__( 'You can set different sizes for your pages sidebar', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'general_layout_section',
		'options'     => array(
			'small'  => array(
				'name'  => esc_html__( 'Small', 'xts-theme' ),
				'value' => 'small',
			),
			'medium' => array(
				'name'  => esc_html__( 'Medium', 'xts-theme' ),
				'value' => 'medium',
			),
			'large'  => array(
				'name'  => esc_html__( 'Large', 'xts-theme' ),
				'value' => 'large',
			),
		),
		'default'     => 'medium',
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'sidebar_sticky',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Sticky sidebar', 'xts-theme' ),
		'description' => esc_html__( 'Make your sidebar stuck while you are scrolling the page content.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'section'     => 'general_layout_section',
		'default'     => '0',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'sidebar_sticky_offset',
		'name'        => esc_html__( 'Sticky sidebar offset', 'xts-theme' ),
		'description' => esc_html__( 'Set the offset for sticky sidebar globally in pixels.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'type'        => 'range',
		'section'     => 'general_layout_section',
		'min'         => 0,
		'max'         => 300,
		'step'        => 5,
		'default'     => 150,
		'priority'    => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'offcanvas_sidebar_desktop',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Off canvas sidebar for desktop', 'xts-theme' ),
		'description' => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'class'       => 'xts-col-6',
		'section'     => 'general_layout_section',
		'default'     => '0',
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'offcanvas_sidebar_mobile',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Off canvas sidebar for mobile devices', 'xts-theme' ),
		'description' => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'class'       => 'xts-col-6',
		'section'     => 'general_layout_section',
		'default'     => '1',
		'priority'    => 90,
	)
);

/**
 * Search.
 */
Options::add_field(
	array(
		'id'          => 'search_posts_results',
		'name'        => esc_html__( 'Display results from blog', 'xts-theme' ),
		'description' => esc_html__( 'Display blog posts as a part of the search results.', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'search_section',
		'default'     => false,
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'search_posts_results_column',
		'name'     => esc_html__( 'Number of columns for blog results', 'xts-theme' ),
		'type'     => 'range',
		'section'  => 'search_section',
		'default'  => 2,
		'min'      => 2,
		'step'     => 1,
		'max'      => 6,
		'requires' => array(
			array(
				'key'     => 'search_posts_results',
				'compare' => 'equals',
				'value'   => true,
			),
		),
		'priority' => 20,
	)
);

Options::add_field(
	array(
		'id'       => 'show_post_categories_on_ajax',
		'name'     => esc_html__( 'Show portfolio or post categories names on AJAX results', 'xts-theme' ),
		'type'     => 'switcher',
		'section'  => 'search_section',
		'default'  => '0',
		'priority' => 30,
	)
);

Options::add_field(
	array(
		'id'       => 'shop_search_by_sku',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Search by product SKU', 'xts-theme' ),
		'section'  => 'search_section',
		'default'  => '1',
		'priority' => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'show_product_sku_on_ajax',
		'name'     => esc_html__( 'Show SKU on AJAX results', 'xts-theme' ),
		'type'     => 'switcher',
		'section'  => 'search_section',
		'default'  => '0',
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'relevanssi_search',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Use Relevanssi for AJAX search', 'xts-theme' ),
		'description' => esc_html__( 'Install Relevanssi plugin and enable its support to improve search algorithm.', 'xts-theme' ),
		'section'     => 'search_section',
		'default'     => '1',
		'priority'    => 50,
	)
);

/**
 * Promo popup.
 */
Options::add_field(
	array(
		'id'          => 'promo_popup',
		'name'        => esc_html__( 'Promo popup', 'xts-theme' ),
		'description' => esc_html__( 'Show promo popup to users when they enter the site.', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'promo_popup_section',
		'default'     => '0',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'promo_popup_content_type',
		'name'        => esc_html__( 'Content type', 'xts-theme' ),
		'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
		'group'       => esc_html__( 'Content', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'promo_popup_section',
		'options'     => array(
			'text'       => array(
				'name'  => esc_html__( 'Text', 'xts-theme' ),
				'value' => 'text',
			),
			'html_block' => array(
				'name'  => esc_html__( 'HTML Block', 'xts-theme' ),
				'value' => 'html_block',
			),
		),
		'default'     => 'text',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'promo_popup_text',
		'name'        => esc_html__( 'Text', 'xts-theme' ),
		'description' => esc_html__( 'You can use any text or HTML here.', 'xts-theme' ),
		'group'       => esc_html__( 'Content', 'xts-theme' ),
		'section'     => 'promo_popup_section',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'requires'    => array(
			array(
				'key'     => 'promo_popup_content_type',
				'compare' => 'equals',
				'value'   => 'text',
			),
		),
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'           => 'promo_popup_html_block',
		'name'         => esc_html__( 'HTML Block', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
		'group'        => esc_html__( 'Content', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'promo_popup_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'requires'     => array(
			array(
				'key'     => 'promo_popup_content_type',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'class'        => 'xts-html-block-links',
		'priority'     => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'promo_popup_width',
		'name'        => esc_html__( 'Width', 'xts-theme' ),
		'description' => esc_html__( 'Set width of the promo popup in pixels.', 'xts-theme' ),
		'group'       => esc_html__( 'Style', 'xts-theme' ),
		'type'        => 'range',
		'section'     => 'promo_popup_section',
		'min'         => 400,
		'max'         => 1000,
		'step'        => 5,
		'default'     => 800,
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'promo_popup_bg',
		'name'        => esc_html__( 'Background', 'xts-theme' ),
		'description' => esc_html__( 'Set background image or color for promo popup.', 'xts-theme' ),
		'group'       => esc_html__( 'Style', 'xts-theme' ),
		'type'        => 'background',
		'section'     => 'promo_popup_section',
		'selector'    => '.xts-promo-popup',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'promo_popup_color_scheme',
		'name'        => esc_html__( 'Color scheme', 'xts-theme' ),
		'description' => esc_html__( 'You can set different text colors depending on its background. May be light or dark.', 'xts-theme' ),
		'group'       => esc_html__( 'Style', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'promo_popup_section',
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
		'default'     => 'inherit',
		'priority'    => 61,
		'class'       => 'xts-color-scheme-picker',
	)
);

Options::add_field(
	array(
		'id'          => 'promo_popup_hide_mobile',
		'name'        => esc_html__( 'Hide for mobile devices', 'xts-theme' ),
		'description' => esc_html__( 'You can disable this option for mobile devices completely.', 'xts-theme' ),
		'group'       => esc_html__( 'Settings', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'promo_popup_section',
		'default'     => '1',
		'priority'    => 70,
	)
);

Options::add_field(
	array(
		'id'       => 'promo_popup_show_after',
		'name'     => esc_html__( 'Show after', 'xts-theme' ),
		'group'    => esc_html__( 'Settings', 'xts-theme' ),
		'type'     => 'buttons',
		'section'  => 'promo_popup_section',
		'options'  => array(
			'some-time'   => array(
				'name'  => esc_html__( 'Some time', 'xts-theme' ),
				'value' => 'default',
			),
			'user-scroll' => array(
				'name'  => esc_html__( 'User scroll', 'xts-theme' ),
				'value' => 'dark',
			),
		),
		'default'  => 'some-time',
		'priority' => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'promo_popup_delay',
		'name'        => esc_html__( 'Delay', 'xts-theme' ),
		'description' => esc_html__( 'Show popup after some time (in milliseconds).', 'xts-theme' ),
		'group'       => esc_html__( 'Settings', 'xts-theme' ),
		'section'     => 'promo_popup_section',
		'type'        => 'text_input',
		'requires'    => array(
			array(
				'key'     => 'promo_popup_show_after',
				'compare' => 'equals',
				'value'   => 'some-time',
			),
		),
		'default'     => 2000,
		'priority'    => 90,
	)
);

Options::add_field(
	array(
		'id'          => 'promo_popup_user_scroll',
		'name'        => esc_html__( 'Show after user scroll down the page', 'xts-theme' ),
		'description' => esc_html__( 'Set the number of pixels users have to scroll down before popup opens.', 'xts-theme' ),
		'group'       => esc_html__( 'Settings', 'xts-theme' ),
		'type'        => 'range',
		'section'     => 'promo_popup_section',
		'min'         => 100,
		'max'         => 5000,
		'step'        => 5,
		'requires'    => array(
			array(
				'key'     => 'promo_popup_show_after',
				'compare' => 'equals',
				'value'   => 'user-scroll',
			),
		),
		'default'     => 1000,
		'priority'    => 100,
	)
);

Options::add_field(
	array(
		'id'          => 'promo_popup_page_visited',
		'name'        => esc_html__( 'Show after number of pages visited', 'xts-theme' ),
		'description' => esc_html__( 'You can choose how much pages user should change before popup will be shown.', 'xts-theme' ),
		'group'       => esc_html__( 'Settings', 'xts-theme' ),
		'type'        => 'range',
		'section'     => 'promo_popup_section',
		'min'         => 0,
		'max'         => 10,
		'step'        => 1,
		'default'     => 0,
		'priority'    => 110,
	)
);


Options::add_field(
	array(
		'id'          => 'promo_popup_version',
		'name'        => esc_html__( 'Version', 'xts-theme' ),
		'description' => esc_html__( 'If you change your promo popup you can increase its version to show the popup to all visitors again.', 'xts-theme' ),
		'group'       => esc_html__( 'Settings', 'xts-theme' ),
		'section'     => 'promo_popup_section',
		'type'        => 'text_input',
		'default'     => 1,
		'priority'    => 120,
	)
);

/**
 * Cookies info.
 */
Options::add_field(
	array(
		'id'          => 'cookies',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Cookies info', 'xts-theme' ),
		'description' => esc_html__( 'Under EU privacy regulations, websites must make it clear to visitors what information about them is being stored. This specifically includes cookies. Turn on this option and user will see info box at the bottom of the page that your web-site is using cookies.', 'xts-theme' ),
		'section'     => 'cookies_section',
		'default'     => '0',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'cookies_title',
		'name'     => esc_html__( 'Title', 'xts-theme' ),
		'section'  => 'cookies_section',
		'type'     => 'text_input',
		'default'  => 'Cookies',
		'priority' => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'cookies_content',
		'name'        => esc_html__( 'Content', 'xts-theme' ),
		'description' => esc_html__( 'Place here some information about cookies usage that will be shown in the popup.', 'xts-theme' ),
		'section'     => 'cookies_section',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'default'     => 'We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies.',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'           => 'cookies_policy_page',
		'name'         => esc_html__( 'Page with details', 'xts-theme' ),
		'description'  => esc_html__( 'Choose page that will contain detailed information about your Privacy Policy.', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'cookies_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_pages_array(),
		'priority'     => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'cookies_version',
		'name'        => esc_html__( 'Version', 'xts-theme' ),
		'description' => esc_html__( 'If you change your cookie policy information you can increase their version to show the popup to all visitors again.', 'xts-theme' ),
		'type'        => 'text_input',
		'section'     => 'cookies_section',
		'default'     => '1',
		'priority'    => 50,
	)
);
