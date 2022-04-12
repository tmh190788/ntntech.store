<?php
/**
 * Blog options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

/**
 * Sidebar.
 */
Options::add_field(
	array(
		'id'          => 'blog_sidebar_position',
		'name'        => esc_html__( 'Position', 'xts-theme' ),
		'description' => esc_html__( 'Select main content and sidebar alignment.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
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
		'id'          => 'blog_sidebar_size',
		'name'        => esc_html__( 'Size', 'xts-theme' ),
		'description' => esc_html__( 'You can set different sizes for your pages sidebar', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
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
		'id'          => 'blog_sidebar_sticky',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Sticky sidebar', 'xts-theme' ),
		'description' => esc_html__( 'Make your sidebar stuck while you are scrolling the page content.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'section'     => 'blog_archive_section',
		'default'     => '0',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_offcanvas_sidebar_desktop',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Off canvas sidebar for desktop', 'xts-theme' ),
		'description' => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'section'     => 'blog_archive_section',
		'class'       => 'xts-col-6',
		'default'     => '0',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_offcanvas_sidebar_mobile',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Off canvas sidebar for mobile devices', 'xts-theme' ),
		'description' => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'section'     => 'blog_archive_section',
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
		'id'                  => 'blog_columns',
		'name'                => esc_html__( 'Columns', 'xts-theme' ),
		'group'               => esc_html__( 'Layout', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'blog_archive_section',
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
		),
		'default'             => xts_get_default_value( 'blog_columns' ),
		'priority'            => 60,
	)
);

Options::add_field(
	array(
		'id'                  => 'blog_columns_tablet',
		'name'                => esc_html__( 'Columns (tablet)', 'xts-theme' ),
		'group'               => esc_html__( 'Layout', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'blog_archive_section',
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
		),
		'priority'            => 70,
	)
);

Options::add_field(
	array(
		'id'                  => 'blog_columns_mobile',
		'name'                => esc_html__( 'Columns (mobile)', 'xts-theme' ),
		'group'               => esc_html__( 'Layout', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'blog_archive_section',
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
		),
		'priority'            => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_spacing',
		'name'        => esc_html__( 'Space between posts', 'xts-theme' ),
		'description' => esc_html__( 'You can set different spacing between posts on blog page.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => xts_get_available_options( 'items_gap' ),
		'default'     => 10,
		'priority'    => 90,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_masonry',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Masonry layout', 'xts-theme' ),
		'description' => esc_html__( 'Masonry library works by placing elements in optimal position based on available vertical space, sort of like a mason fitting stones in a wall.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'blog_archive_section',
		'default'     => '0',
		'priority'    => 100,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_different_sizes',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Different sizes', 'xts-theme' ),
		'description' => esc_html__( 'Double the size of particular elements in the grid by their position indexes.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'blog_archive_section',
		'default'     => '0',
		'priority'    => 110,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_different_sizes_position',
		'name'        => esc_html__( 'Wide items position indexes', 'xts-theme' ),
		'description' => esc_html__( 'Set order numbers for items that you want to increase in size. For example: 2,5,8,9', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'blog_archive_section',
		'type'        => 'text_input',
		'default'     => '2,5,8,9',
		'priority'    => 120,
		'requires'    => array(
			array(
				'key'     => 'blog_different_sizes',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'blog_animation_in_view',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Animation in view', 'xts-theme' ),
		'description' => esc_html__( 'Add appearance animation effect to all the elmenets in the grid.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'blog_archive_section',
		'priority'    => 130,
		'default'     => '0',
	)
);

Options::add_field(
	array(
		'id'       => 'blog_animation',
		'name'     => esc_html__( 'Animations', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'type'     => 'select',
		'section'  => 'blog_archive_section',
		'select2'  => true,
		'options'  => xts_get_animations_array( 'default' ),
		'default'  => 'short-in-up',
		'class'    => 'xts-col-4',
		'requires' => array(
			array(
				'key'     => 'blog_animation_in_view',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority' => 140,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_animation_duration',
		'name'     => esc_html__( 'Animation duration', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'type'     => 'select',
		'section'  => 'blog_archive_section',
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
				'key'     => 'blog_animation_in_view',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority' => 150,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_animation_delay',
		'name'     => esc_html__( 'Animation delay', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'type'     => 'text_input',
		'section'  => 'blog_archive_section',
		'default'  => 100,
		'class'    => 'xts-col-4',
		'requires' => array(
			array(
				'key'     => 'blog_animation_in_view',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority' => 160,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_pagination',
		'name'        => esc_html__( 'Pagination', 'xts-theme' ),
		'description' => esc_html__( 'Choose a type for the pagination on your blog page.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
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
		'priority'    => 170,
	)
);

/**
 * Post option.
 */
if ( count( xts_get_available_options( 'blog_design' ) ) > 1 ) {
	Options::add_field(
		array(
			'id'          => 'blog_design',
			'name'        => esc_html__( 'Design', 'xts-theme' ),
			'description' => esc_html__( 'You can use different design for your blog styled for the theme.', 'xts-theme' ),
			'group'       => esc_html__( 'Post options', 'xts-theme' ),
			'type'        => 'select',
			'section'     => 'blog_archive_section',
			'options'     => xts_get_available_options( 'blog_design' ),
			'default'     => 'default',
			'priority'    => 180,
		)
	);
}
/**
 * Chess order blog_post_chess_order (181).
 */

/**
 * Blog post logo blog_post_logo (182).
 */

/**
 * Back and white blog_post_black_white (183).
 */

Options::add_field(
	array(
		'id'       => 'blog_image_size',
		'name'     => esc_html__( 'Image size', 'xts-theme' ),
		'group'    => esc_html__( 'Post options', 'xts-theme' ),
		'type'     => 'select',
		'section'  => 'blog_archive_section',
		'options'  => xts_get_all_image_sizes_names(),
		'default'  => 'large',
		'priority' => 190,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_image_size_custom',
		'name'     => esc_html__( 'Image size custom', 'xts-theme' ),
		'group'    => esc_html__( 'Post options', 'xts-theme' ),
		'type'     => 'image_dimensions',
		'section'  => 'blog_archive_section',
		'requires' => array(
			array(
				'key'     => 'blog_image_size',
				'compare' => 'equals',
				'value'   => 'custom',
			),
		),
		'priority' => 200,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_excerpt',
		'name'        => esc_html__( 'Posts excerpt', 'xts-theme' ),
		'description' => esc_html__( 'If you will set this option to "Excerpt" then you are able to set custom excerpt for each post or it will be cutted from the post content. If you choose "Full content" then all content will be shown, or you can also add "Read more button" while editing the post and by doing this cut your excerpt length as you need.', 'xts-theme' ),
		'group'       => esc_html__( 'Post options', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => array(
			'excerpt' => array(
				'name'  => esc_html__( 'Excerpt', 'xts-theme' ),
				'value' => 'excerpt',
			),
			'full'    => array(
				'name'  => esc_html__( 'Full content', 'xts-theme' ),
				'value' => 'full',
			),
		),
		'default'     => 'full',
		'priority'    => 210,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_excerpt_words_or_letters',
		'name'        => esc_html__( 'Excerpt length by words or letters', 'xts-theme' ),
		'description' => esc_html__( 'Limit your excerpt text for posts by words or letters.', 'xts-theme' ),
		'group'       => esc_html__( 'Post options', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'class'       => 'xts-col-6',
		'default'     => 'letters',
		'options'     => array(
			'letters' => array(
				'name'  => esc_html__( 'Letters', 'xts-theme' ),
				'value' => 'letters',
			),
			'words'   => array(
				'name'  => esc_html__( 'Words', 'xts-theme' ),
				'value' => 'words',
			),
		),
		'requires'    => array(
			array(
				'key'     => 'blog_excerpt',
				'compare' => 'equals',
				'value'   => 'excerpt',
			),
		),
		'priority'    => 220,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_excerpt_length',
		'name'        => esc_html__( 'Excerpt length', 'xts-theme' ),
		'description' => esc_html__( 'Number of words or letters that will be displayed for each post if you use "Excerpt" mode and don\'t set custom excerpt for each post.', 'xts-theme' ),
		'group'       => esc_html__( 'Post options', 'xts-theme' ),
		'section'     => 'blog_archive_section',
		'class'       => 'xts-col-6',
		'type'        => 'text_input',
		'requires'    => array(
			array(
				'key'     => 'blog_excerpt',
				'compare' => 'equals',
				'value'   => 'excerpt',
			),
		),
		'default'     => xts_get_default_value( 'blog_excerpt_length' ),
		'priority'    => 230,
	)
);

/**
 * Shadow blog_post_shadow (231).
 */

Options::add_field(
	array(
		'id'       => 'blog_title_visibility',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Title for posts', 'xts-theme' ),
		'group'    => esc_html__( 'Post options', 'xts-theme' ),
		'section'  => 'blog_archive_section',
		'class'    => 'xts-col-6',
		'default'  => '1',
		'priority' => 240,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_meta_visibility',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Meta information', 'xts-theme' ),
		'group'    => esc_html__( 'Post options', 'xts-theme' ),
		'section'  => 'blog_archive_section',
		'class'    => 'xts-col-6',
		'default'  => '1',
		'priority' => 250,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_text_visibility',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Post text', 'xts-theme' ),
		'group'    => esc_html__( 'Post options', 'xts-theme' ),
		'section'  => 'blog_archive_section',
		'class'    => 'xts-col-6',
		'default'  => '1',
		'priority' => 260,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_categories_visibility',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Categories', 'xts-theme' ),
		'group'    => esc_html__( 'Post options', 'xts-theme' ),
		'section'  => 'blog_archive_section',
		'class'    => 'xts-col-6',
		'default'  => '1',
		'priority' => 270,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_theme_post_formats',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Theme post formats', 'xts-theme' ),
		'description' => esc_html__( 'Use our custom fields for post formats audio, image, quote, gallery etc. Disable this option to display all post formats in a default view from WordPress.', 'xts-theme' ),
		'group'       => esc_html__( 'Post options', 'xts-theme' ),
		'section'     => 'blog_archive_section',
		'class'       => 'xts-col-6',
		'priority'    => 280,
		'default'     => '0',
	)
);

/**
 * Single post.
 */
Options::add_field(
	array(
		'id'          => 'blog_single_design',
		'name'        => esc_html__( 'Design', 'xts-theme' ),
		'description' => esc_html__( 'You can use different design for your single post page.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'type'        => 'select',
		'section'     => 'single_post_section',
		'options'     => xts_get_available_options( 'blog_single_design' ),
		'default'     => 'default',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_single_parallax_scroll',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Scale and parallax effect on scroll', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'section'  => 'single_post_section',
		'requires' => array(
			array(
				'key'     => 'blog_single_design',
				'compare' => 'equals',
				'value'   => 'page-title',
			),
		),
		'default'  => '1',
		'priority' => 20,
	)
);

Options::add_field(
	array(
		'id'           => 'blog_single_header',
		'name'         => esc_html__( 'Custom header', 'xts-theme' ),
		'description'  => esc_html__( 'You can use different header for your single post page.', 'xts-theme' ),
		'group'        => esc_html__( 'Layout', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'single_post_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_headers_array(),
		'priority'     => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_single_content_boxed',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Content layout boxed', 'xts-theme' ),
		'description' => esc_html__( 'Limit your post content width to improve the readability.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'single_post_section',
		'default'     => '0',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_single_content_width',
		'name'     => esc_html__( 'Content width', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'type'     => 'range',
		'section'  => 'single_post_section',
		'default'  => 700,
		'min'      => 300,
		'max'      => 1920,
		'step'     => 5,
		'requires' => array(
			array(
				'key'     => 'blog_single_content_boxed',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_single_image_size',
		'name'     => esc_html__( 'Image size', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'type'     => 'select',
		'section'  => 'single_post_section',
		'options'  => xts_get_all_image_sizes_names(),
		'default'  => 'large',
		'priority' => 60,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_single_image_size_custom',
		'name'     => esc_html__( 'Image size custom', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'type'     => 'image_dimensions',
		'section'  => 'single_post_section',
		'requires' => array(
			array(
				'key'     => 'blog_single_image_size',
				'compare' => 'equals',
				'value'   => 'custom',
			),
		),
		'priority' => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_single_page_title_design',
		'name'        => esc_html__( 'Design', 'xts-theme' ),
		'description' => esc_html__( 'Select page title section design or disable it completely on single post page.', 'xts-theme' ),
		'group'       => esc_html__( 'Page title', 'xts-theme' ),
		'section'     => 'single_post_section',
		'type'        => 'buttons',
		'options'     => xts_get_available_options( 'page_title_design' ),
		'default'     => xts_get_opt( 'page_title_design', 'default' ),
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_single_share_buttons',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Share buttons', 'xts-theme' ),
		'description' => esc_html__( 'Display share icons on single post page.', 'xts-theme' ),
		'group'       => esc_html__( 'Elements', 'xts-theme' ),
		'section'     => 'single_post_section',
		'class'       => 'xts-col-6',
		'default'     => '1',
		'priority'    => 90,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_single_navigation',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Navigation', 'xts-theme' ),
		'description' => esc_html__( 'Next and previous posts links on single post page.', 'xts-theme' ),
		'group'       => esc_html__( 'Elements', 'xts-theme' ),
		'section'     => 'single_post_section',
		'class'       => 'xts-col-6',
		'default'     => '1',
		'priority'    => 100,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_single_author_bio',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Author bio', 'xts-theme' ),
		'description' => esc_html__( 'Display information about the post author.', 'xts-theme' ),
		'group'       => esc_html__( 'Elements', 'xts-theme' ),
		'section'     => 'single_post_section',
		'class'       => 'xts-col-6',
		'default'     => '1',
		'priority'    => 110,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_single_related_posts',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Related posts', 'xts-theme' ),
		'description' => esc_html__( 'Show related posts on single post page (by tags).', 'xts-theme' ),
		'group'       => esc_html__( 'Related posts options', 'xts-theme' ),
		'section'     => 'single_post_section',
		'default'     => xts_get_default_value( '1' ),
		'priority'    => 120,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_single_related_posts_count',
		'name'        => esc_html__( 'Post count', 'xts-theme' ),
		'description' => esc_html__( 'The total number of related posts to display.', 'xts-theme' ),
		'group'       => esc_html__( 'Related posts options', 'xts-theme' ),
		'type'        => 'text_input',
		'section'     => 'single_post_section',
		'default'     => 5,
		'priority'    => 130,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_single_related_posts_per_row',
		'name'        => esc_html__( 'Columns', 'xts-theme' ),
		'description' => esc_html__( 'How many posts you want to show per row.', 'xts-theme' ),
		'group'       => esc_html__( 'Related posts options', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_post_section',
		'options'     => array(
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
				'value' => 6,
			),
		),
		'default'     => xts_get_default_value( 'blog_single_related_posts_per_row' ),
		'priority'    => 140,
	)
);
