<?php
/**
 * Product archive.
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

/**
 * Product archive.
 */
Options::add_field(
	array(
		'id'           => 'shop_page_content',
		'name'         => esc_html__( 'Shop page extra content', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a><br>Select an HTML Block built with Elementor that will be displayed above the shop page content on all shop archive pages including categories and tags pages.',
		'type'         => 'select',
		'section'      => 'product_archive_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'class'        => 'xts-html-block-links',
		'priority'     => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'ajax_shop',
		'type'        => 'switcher',
		'name'        => esc_html__( 'AJAX shop', 'xts-theme' ),
		'description' => esc_html__( 'Load all shop related pages (categories, filters, tags, etc.) with AJAX without page reloading.', 'xts-theme' ),
		'section'     => 'product_archive_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'ajax_shop_scroll',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Scroll to top after AJAX', 'xts-theme' ),
		'description' => esc_html__( 'Disable - Enable scroll to top after AJAX.', 'xts-theme' ),
		'section'     => 'product_archive_section',
		'default'     => '1',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'yoast_shop_breadcrumbs',
		'name'        => esc_html__( 'Yoast breadcrumbs for shop', 'xts-theme' ),
		'description' => esc_html__( 'Requires Yoast SEO plugin to be installed. Replaces our theme\'s breadcrumbs for shop and single product with custom that come with the plugin. You need to enable and configure it in Dashboard -> SEO -> Search Appearance -> Breadcrumbs.', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'product_archive_section',
		'default'     => '0',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_woocommerce_catalog_ordering',
		'name'        => esc_html__( 'Sort by dropdown', 'xts-theme' ),
		'description' => esc_html__( 'Show or hide default WooCommerce "sort by" widget on the shop page.', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'product_archive_section',
		'default'     => '1',
		'priority'    => 50,
	)
);

/**
 * Shop tools search shop_tools_search (60).
 */

Options::add_field(
	array(
		'id'          => 'category_description_position',
		'name'        => esc_html__( 'Category description position', 'xts-theme' ),
		'description' => esc_html__( 'You can change default products category description position and move it below the products.', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'product_archive_section',
		'options'     => array(
			'before' => array(
				'name'  => esc_html__( 'Before products grid', 'xts-theme' ),
				'value' => 'before',
			),
			'after'  => array(
				'name'  => esc_html__( 'After products grid', 'xts-theme' ),
				'value' => 'after',
			),
		),
		'default'     => 'before',
		'priority'    => 70,
	)
);

/**
 * Sidebar.
 */
Options::add_field(
	array(
		'id'          => 'shop_sidebar_position',
		'name'        => esc_html__( 'Position', 'xts-theme' ),
		'description' => esc_html__( 'Select main content and sidebar alignment.', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'product_archive_sidebar_section',
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
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_sidebar_size',
		'name'        => esc_html__( 'Size', 'xts-theme' ),
		'description' => esc_html__( 'You can set different sizes for your pages sidebar', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'product_archive_sidebar_section',
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
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_sidebar_sticky',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Sticky sidebar', 'xts-theme' ),
		'description' => esc_html__( 'Make your sidebar stuck while you are scrolling the page content.', 'xts-theme' ),
		'section'     => 'product_archive_sidebar_section',
		'default'     => '0',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_offcanvas_sidebar_desktop',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Off canvas sidebar for desktop', 'xts-theme' ),
		'description' => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
		'section'     => 'product_archive_sidebar_section',
		'class'       => 'xts-col-6',
		'default'     => '0',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_offcanvas_sidebar_mobile',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Off canvas sidebar for mobile devices', 'xts-theme' ),
		'description' => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
		'section'     => 'product_archive_sidebar_section',
		'class'       => 'xts-col-6',
		'default'     => '1',
		'priority'    => 50,
	)
);

/**
 * Layout.
 */
Options::add_field(
	array(
		'id'                  => 'products_per_row',
		'name'                => esc_html__( 'Products per row', 'xts-theme' ),
		'description'         => esc_html__( 'How many products should be shown per row?', 'xts-theme' ),
		'group'               => esc_html__( 'Grid', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'product_archive_layout_section',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'desktop_only'        => true,
		'options'             => array(
			1 => array(
				'name'  => 1,
				'value' => 1,
			),
			2 => array(
				'name'  => 2,
				'value' => 2,
			),
			3 => array(
				'name'  => 3,
				'value' => 3,
			),
			4 => array(
				'name'  => 4,
				'value' => 4,
			),
			5 => array(
				'name'  => 5,
				'value' => 5,
			),
			6 => array(
				'name'  => 6,
				'value' => 64,
			),
		),
		'default'             => 4,
		'priority'            => 10,
	)
);

Options::add_field(
	array(
		'id'                  => 'products_per_row_tablet',
		'name'                => esc_html__( 'Products per row (tablet)', 'xts-theme' ),
		'description'         => esc_html__( 'How many products should be shown per row?', 'xts-theme' ),
		'group'               => esc_html__( 'Grid', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'product_archive_layout_section',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'tablet_only'         => true,
		'options'             => array(
			1 => array(
				'name'  => 1,
				'value' => 1,
			),
			2 => array(
				'name'  => 2,
				'value' => 2,
			),
			3 => array(
				'name'  => 3,
				'value' => 3,
			),
			4 => array(
				'name'  => 4,
				'value' => 4,
			),
			5 => array(
				'name'  => 5,
				'value' => 5,
			),
			6 => array(
				'name'  => 6,
				'value' => 64,
			),
		),
		'priority'            => 20,
	)
);

Options::add_field(
	array(
		'id'                  => 'products_per_row_mobile',
		'name'                => esc_html__( 'Products per row (mobile)', 'xts-theme' ),
		'description'         => esc_html__( 'How many products should be shown per row?', 'xts-theme' ),
		'group'               => esc_html__( 'Grid', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'product_archive_layout_section',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'mobile_only'         => true,
		'options'             => array(
			1 => array(
				'name'  => 1,
				'value' => 1,
			),
			2 => array(
				'name'  => 2,
				'value' => 2,
			),
			3 => array(
				'name'  => 3,
				'value' => 3,
			),
			4 => array(
				'name'  => 4,
				'value' => 4,
			),
			5 => array(
				'name'  => 5,
				'value' => 5,
			),
			6 => array(
				'name'  => 6,
				'value' => 64,
			),
		),
		'priority'            => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_spacing',
		'name'        => esc_html__( 'Space between products', 'xts-theme' ),
		'description' => esc_html__( 'You can set different spacing between posts on shop page.', 'xts-theme' ),
		'group'       => esc_html__( 'Grid', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'product_archive_layout_section',
		'options'     => xts_get_available_options( 'items_gap' ),
		'default'     => 10,
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_masonry',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Masonry layout', 'xts-theme' ),
		'description' => esc_html__( 'Masonry library works by placing elements in optimal position based on available vertical space, sort of like a mason fitting stones in a wall.', 'xts-theme' ),
		'group'       => esc_html__( 'Grid', 'xts-theme' ),
		'section'     => 'product_archive_layout_section',
		'default'     => '0',
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_different_sizes',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Different sizes', 'xts-theme' ),
		'description' => esc_html__( 'Double the size of particular elements in the grid by their position indexes.', 'xts-theme' ),
		'group'       => esc_html__( 'Grid', 'xts-theme' ),
		'section'     => 'product_archive_layout_section',
		'default'     => '0',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_different_sizes_position',
		'name'        => esc_html__( 'Wide items position indexes', 'xts-theme' ),
		'description' => esc_html__( 'Set order numbers for items that you want to increase in size. For example: 2,5,8,9', 'xts-theme' ),
		'group'       => esc_html__( 'Grid', 'xts-theme' ),
		'section'     => 'product_archive_layout_section',
		'type'        => 'text_input',
		'default'     => '2,5,8,9',
		'requires'    => array(
			array(
				'key'     => 'shop_different_sizes',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority'    => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'products_per_row_variations',
		'type'        => 'select',
		'name'        => esc_html__( 'Products per row variations', 'xts-theme' ),
		'description' => esc_html__( 'What columns users may select to be displayed on the product page.', 'xts-theme' ),
		'group'       => esc_html__( 'Grid', 'xts-theme' ),
		'section'     => 'product_archive_layout_section',
		'multiple'    => true,
		'select2'     => true,
		'options'     => array(
			'2' => array(
				'name'  => '2',
				'value' => '2',
			),
			'3' => array(
				'name'  => '3',
				'value' => '3',
			),
			'4' => array(
				'name'  => '4',
				'value' => '4',
			),
			'5' => array(
				'name'  => '5',
				'value' => '5',
			),
			'6' => array(
				'name'  => '6',
				'value' => '6',
			),
		),
		'default'     => array(
			'2',
			'3',
			'4',
		),
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'       => 'shop_animation_in_view',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Animation in view', 'xts-theme' ),
		'group'    => esc_html__( 'Grid', 'xts-theme' ),
		'section'  => 'product_archive_layout_section',
		'default'  => '0',
		'priority' => 90,
	)
);

Options::add_field(
	array(
		'id'       => 'shop_animation',
		'name'     => esc_html__( 'Animations', 'xts-theme' ),
		'group'    => esc_html__( 'Grid', 'xts-theme' ),
		'type'     => 'select',
		'section'  => 'product_archive_layout_section',
		'select2'  => true,
		'options'  => xts_get_animations_array( 'default' ),
		'default'  => 'short-in-up',
		'class'    => 'xts-col-4',
		'requires' => array(
			array(
				'key'     => 'shop_animation_in_view',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority' => 100,
	)
);

Options::add_field(
	array(
		'id'       => 'shop_animation_duration',
		'name'     => esc_html__( 'Animation duration', 'xts-theme' ),
		'group'    => esc_html__( 'Grid', 'xts-theme' ),
		'type'     => 'select',
		'section'  => 'product_archive_layout_section',
		'options'  => array(
			'slow'   => array(
				'name'  => esc_html__( 'Slow', 'xts-theme' ),
				'value' => 'slow',
			),
			'normal' => array(
				'name'  => esc_html__( 'Normal', 'xts-theme' ),
				'value' => 'normal',
			),
			'fast'   => array(
				'name'  => esc_html__( 'Fast', 'xts-theme' ),
				'value' => 'fast',
			),
		),
		'default'  => 'fast',
		'class'    => 'xts-col-4',
		'requires' => array(
			array(
				'key'     => 'shop_animation_in_view',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority' => 110,
	)
);

Options::add_field(
	array(
		'id'       => 'shop_animation_delay',
		'name'     => esc_html__( 'Animation delay', 'xts-theme' ),
		'group'    => esc_html__( 'Grid', 'xts-theme' ),
		'type'     => 'text_input',
		'section'  => 'product_archive_layout_section',
		'default'  => 100,
		'class'    => 'xts-col-4',
		'requires' => array(
			array(
				'key'     => 'shop_animation_in_view',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority' => 120,
	)
);

Options::add_field(
	array(
		'id'          => 'products_per_page',
		'name'        => esc_html__( 'Products per page', 'xts-theme' ),
		'description' => esc_html__( 'How many products should be shown per page?', 'xts-theme' ),
		'group'       => esc_html__( 'Pages', 'xts-theme' ),
		'section'     => 'product_archive_layout_section',
		'type'        => 'text_input',
		'default'     => '12',
		'priority'    => 130,
	)
);

Options::add_field(
	array(
		'id'          => 'products_per_page_variations',
		'name'        => esc_html__( 'Products per page variations', 'xts-theme' ),
		'description' => esc_html__( 'For ex.: 10,20,30,-1. Use -1 to show all products on the page', 'xts-theme' ),
		'group'       => esc_html__( 'Pages', 'xts-theme' ),
		'section'     => 'product_archive_layout_section',
		'type'        => 'text_input',
		'default'     => '12,20,30,-1',
		'priority'    => 140,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_pagination',
		'name'        => esc_html__( 'Pagination', 'xts-theme' ),
		'description' => esc_html__( 'Choose a type for the pagination on your shop page.', 'xts-theme' ),
		'group'       => esc_html__( 'Pages', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'product_archive_layout_section',
		'options'     => array(
			'links'     => array(
				'name'  => esc_html__( 'Pagination links', 'xts-theme' ),
				'value' => 'links',
			),
			'load_more' => array(
				'name'  => esc_html__( 'Load more button', 'xts-theme' ),
				'value' => 'load_more',
			),
			'infinite'  => array(
				'name'  => esc_html__( 'Infinite scrolling', 'xts-theme' ),
				'value' => 'infinite',
			),
		),
		'default'     => 'links',
		'priority'    => 150,
	)
);

/**
 * Product options.
 */
Options::add_field(
	array(
		'id'       => 'product_loop_design',
		'name'     => esc_html__( 'Design', 'xts-theme' ),
		'type'     => 'select',
		'section'  => 'product_archive_product_options_section',
		'options'  => xts_get_available_options( 'product_loop_design' ),
		'default'  => 'summary',
		'priority' => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'product_loop_image_size',
		'name'        => esc_html__( 'Image size', 'xts-theme' ),
		'description' => esc_html__( 'Leave "Woocommerce Thumbnail" to load images sizes as set in Appearance -> Customize -> WooCommerce -> Product images. Or specify custom images size for the shop page.', 'xts-theme' ),
		'type'        => 'select',
		'section'     => 'product_archive_product_options_section',
		'options'     => xts_get_all_image_sizes_names(),
		'default'     => 'woocommerce_thumbnail',
		'priority'    => 11,
	)
);

Options::add_field(
	array(
		'id'       => 'product_loop_image_size_custom',
		'name'     => esc_html__( 'Image size custom', 'xts-theme' ),
		'type'     => 'image_dimensions',
		'section'  => 'product_archive_product_options_section',
		'requires' => array(
			array(
				'key'     => 'product_loop_image_size',
				'compare' => 'equals',
				'value'   => 'custom',
			),
		),
		'priority' => 12,
	)
);

Options::add_field(
	array(
		'id'       => 'product_loop_design_summary_hover_content',
		'name'     => esc_html__( 'Hover content', 'xts-theme' ),
		'type'     => 'buttons',
		'section'  => 'product_archive_product_options_section',
		'options'  => array(
			'excerpt'         => array(
				'name'  => esc_html__( 'Excerpt', 'xts-theme' ),
				'value' => 'excerpt',
			),
			'additional_info' => array(
				'name'  => esc_html__( 'Additional information', 'xts-theme' ),
				'value' => 'additional_info',
			),
			'without'         => array(
				'name'  => esc_html__( 'Without', 'xts-theme' ),
				'value' => 'without',
			),
		),
		'default'  => 'excerpt',
		'priority' => 20,
		'requires' => array(
			array(
				'key'     => 'product_loop_design',
				'compare' => 'equals',
				'value'   => array( 'summary', 'summary-alt', 'summary-alt-2' ),
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'product_title_lines_limit',
		'name'        => esc_html__( 'Product title lines limit', 'xts-theme' ),
		'description' => esc_html__( 'Limit product titles size with CSS. Useful when product blocks height are different and titles are too long.', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'product_archive_product_options_section',
		'options'     => array(
			'one'     => array(
				'name'  => esc_html__( 'One line', 'xts-theme' ),
				'value' => 'one',
			),
			'two'     => array(
				'name'  => esc_html__( 'Two line', 'xts-theme' ),
				'value' => 'two',
			),
			'default' => array(
				'name'  => esc_html__( 'Default', 'xts-theme' ),
				'value' => 'default',
			),
		),
		'default'     => 'default',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'product_loop_categories',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Show product categories', 'xts-theme' ),
		'section'  => 'product_archive_product_options_section',
		'default'  => '0',
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'       => 'product_loop_attributes',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Show product attributes', 'xts-theme' ),
		'section'  => 'product_archive_product_options_section',
		'default'  => '0',
		'priority' => 55,
	)
);

/**
 * Stock progress bar product_loop_stock_progress_bar (60).
 */

/**
 * Countdown timer product_loop_sale_countdown (70).
 */

Options::add_field(
	array(
		'id'       => 'product_loop_rating',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Show product rating', 'xts-theme' ),
		'section'  => 'product_archive_product_options_section',
		'default'  => '1',
		'priority' => 80,
	)
);

/**
 * Product loop quantity product_loop_quantity (80).
 */

/**
 * Widgets.
 */
Options::add_field(
	array(
		'id'          => 'product_categories_widget_accordion',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Accordion function for categories widget', 'xts-theme' ),
		'description' => esc_html__( 'Turn it on to enable accordion JS for the WooCommerce Product Categories widget. Useful if you have a lot of categories and subcategories.', 'xts-theme' ),
		'section'     => 'widgets_section',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'product_layered_nav_widgets_scroll',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Scroll for filters widgets', 'xts-theme' ),
		'description' => esc_html__( 'You can limit your Layered Navigation widgets by height and enable nice scroll for them. Useful if you have a lot of product colors/sizes or other attributes for filters.', 'xts-theme' ),
		'section'     => 'widgets_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'product_layered_nav_widgets_height',
		'name'        => esc_html__( 'Height for filters widgets', 'xts-theme' ),
		'description' => esc_html__( 'Set widgets height in pixels.', 'xts-theme' ),
		'type'        => 'range',
		'section'     => 'widgets_section',
		'default'     => 280,
		'min'         => 100,
		'max'         => 800,
		'step'        => 10,
		'requires'    => array(
			array(
				'key'     => 'product_layered_nav_widgets_scroll',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority'    => 30,
	)
);

/**
 * Page title.
 */
Options::add_field(
	array(
		'id'          => 'product_archive_page_title',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Page title block', 'xts-theme' ),
		'description' => esc_html__( 'Show page title for shop page, product categories or tags.', 'xts-theme' ),
		'section'     => 'product_archive_page_title_section',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'page_title_shop_title',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Title', 'xts-theme' ),
		'description' => esc_html__( 'Display title text for the shop page and categories, tags, brands etc.', 'xts-theme' ),
		'section'     => 'product_archive_page_title_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'page_title_shop_categories',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Categories menu', 'xts-theme' ),
		'description' => esc_html__( 'This categories menu is generated automatically based on all categories in the shop. You are not able to manage this menu as other WordPress menus.', 'xts-theme' ),
		'section'     => 'product_archive_page_title_section',
		'default'     => '1',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'       => 'page_title_shop_categories_products_count',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Show products count for each category', 'xts-theme' ),
		'section'  => 'product_archive_page_title_section',
		'requires' => array(
			array(
				'key'     => 'page_title_shop_categories',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'default'  => '1',
		'priority' => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'page_title_shop_categories_ancestors',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Show current category ancestors', 'xts-theme' ),
		'description' => esc_html__( 'If you visit category Man, for example, only man\'s subcategories will be shown in the page title like T-shirts, Coats, Shoes etc.', 'xts-theme' ),
		'section'     => 'product_archive_page_title_section',
		'requires'    => array(
			array(
				'key'     => 'page_title_shop_categories',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'default'     => '0',
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'page_title_shop_categories_neighbors',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Show category neighbors if there is no children', 'xts-theme' ),
		'description' => esc_html__( 'If the category you visit doesn\'t contain any subcategories, the page title menu will display this category\'s neighbors categories.', 'xts-theme' ),
		'section'     => 'product_archive_page_title_section',
		'requires'    => array(
			array(
				'key'     => 'page_title_shop_categories_ancestors',
				'compare' => 'equals',
				'value'   => '1',
			),
			array(
				'key'     => 'page_title_shop_categories',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'default'     => '0',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'page_title_shop_category_all_link_icon',
		'name'        => esc_html__( 'All products link icon', 'xts-theme' ),
		'description' => esc_html__( 'All products image (icon) for categories navigation on the shop page.', 'xts-theme' ),
		'type'        => 'upload',
		'section'     => 'product_archive_page_title_section',
		'requires'    => array(
			array(
				'key'     => 'page_title_shop_categories',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority'    => 70,
	)
);

Options::add_field(
	array(
		'id'       => 'page_title_shop_hide_empty_categories',
		'name'     => esc_html__( 'Hide empty categories', 'xts-theme' ),
		'type'     => 'switcher',
		'section'  => 'product_archive_page_title_section',
		'requires' => array(
			array(
				'key'     => 'page_title_shop_categories',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'default'  => '0',
		'priority' => 80,
	)
);

Options::add_field(
	array(
		'id'           => 'page_title_shop_categories_exclude',
		'type'         => 'select',
		'section'      => 'product_archive_page_title_section',
		'name'         => esc_html__( 'Exclude categories', 'xts-theme' ),
		'select2'      => true,
		'empty_option' => true,
		'multiple'     => true,
		'autocomplete' => array(
			'type'   => 'term',
			'value'  => 'product_cat',
			'search' => 'xts_get_taxonomies_by_query_autocomplete',
			'render' => 'xts_get_taxonomies_by_ids_autocomplete',
		),
		'requires'     => array(
			array(
				'key'     => 'page_title_shop_categories',
				'compare' => 'equals',
				'value'   => '1',
			),
			array(
				'key'     => 'page_title_shop_categories_ancestors',
				'compare' => 'not_equals',
				'value'   => '1',
			),
		),
		'priority'     => 90,
	)
);

/**
 * WC Page title text page_title_shop_text (100).
 */


/**
 * Filters area.
 */
Options::add_field(
	array(
		'id'          => 'shop_filters_area',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Shop filters', 'xts-theme' ),
		'description' => esc_html__( 'Enable shop filters widget\'s area above the products.', 'xts-theme' ),
		'section'     => 'product_archive_filters_area_section',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_filters_area_always_open',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Shop filters area always opened', 'xts-theme' ),
		'description' => esc_html__( 'If you enable this option the shop filters will be always opened on the shop page.', 'xts-theme' ),
		'section'     => 'product_archive_filters_area_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_filters_area_stop_close',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Stop close filters after click', 'xts-theme' ),
		'description' => esc_html__( 'This option will prevent filters area from closing when you click on certain filter links.', 'xts-theme' ),
		'section'     => 'product_archive_filters_area_section',
		'requires'    => array(
			array(
				'key'     => 'shop_filters_area_always_open',
				'compare' => 'equals',
				'value'   => '0',
			),
		),
		'default'     => '0',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_filters_area_content_type',
		'name'        => esc_html__( 'Content type', 'xts-theme' ),
		'description' => esc_html__( 'You can display content as widgets added via Appearance -> Widgets or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'product_archive_filters_area_section',
		'options'     => array(
			'widgets'    => array(
				'name'  => esc_html__( 'Widgets', 'xts-theme' ),
				'value' => 'widgets',
			),
			'html_block' => array(
				'name'  => esc_html__( 'HTML Block', 'xts-theme' ),
				'value' => 'html_block',
			),
		),
		'default'     => 'widgets',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'           => 'shop_filters_area_html_block',
		'name'         => esc_html__( 'Html block', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
		'type'         => 'select',
		'section'      => 'product_archive_filters_area_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'requires'     => array(
			array(
				'key'     => 'shop_filters_area_content_type',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'class'        => 'xts-html-block-links',
		'priority'     => 50,
	)
);

/**
 * Categories.
 */
Options::add_field(
	array(
		'id'       => 'product_categories_design',
		'name'     => esc_html__( 'Design', 'xts-theme' ),
		'type'     => 'select',
		'section'  => 'product_archive_categories_options_section',
		'options'  => xts_get_available_options( 'product_categories_design' ),
		'default'  => 'default',
		'priority' => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'product_categories_color_scheme',
		'name'     => esc_html__( 'Color scheme', 'xts-theme' ),
		'type'     => 'buttons',
		'section'  => 'product_archive_categories_options_section',
		'options'  => array(
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
		'default'  => 'inherit',
		'priority' => 20,
		'class'    => 'xts-color-scheme-picker',
	)
);

Options::add_field(
	array(
		'id'       => 'categories_product_count',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Show product count on category', 'xts-theme' ),
		'section'  => 'product_archive_categories_options_section',
		'default'  => '1',
		'priority' => 30,
	)
);
