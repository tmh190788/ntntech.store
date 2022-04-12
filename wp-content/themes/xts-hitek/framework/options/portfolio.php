<?php
/**
 * Portfolio options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

/**
 * Portfolio.
 */
Options::add_field(
	array(
		'id'          => 'portfolio',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Portfolio post type', 'xts-theme' ),
		'description' => esc_html__( 'You can enable/disable custom post type that used for portfolio functionality.', 'xts-theme' ),
		'section'     => 'portfolio_section',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'           => 'portfolio_page',
		'name'         => esc_html__( 'Portfolio page', 'xts-theme' ),
		'description'  => esc_html__( 'You need to create an empty page and select from the dropdown. It will be used as a root page for your portfolio archives.', 'xts-theme' ),
		'section'      => 'portfolio_section',
		'type'         => 'select',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_pages_array(),
		'priority'     => 20,
	)
);

/**
 * Portfolio archive.
 */
Options::add_field(
	array(
		'id'          => 'portfolio_sidebar_position',
		'name'        => esc_html__( 'Position', 'xts-theme' ),
		'description' => esc_html__( 'Select main content and sidebar alignment.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'portfolio_archive_section',
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
		'id'          => 'portfolio_sidebar_size',
		'name'        => esc_html__( 'Size', 'xts-theme' ),
		'description' => esc_html__( 'You can set different sizes for your projects sidebar', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'portfolio_archive_section',
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

/**
 * Layout.
 */
Options::add_field(
	array(
		'id'          => 'portfolio_full_width',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Portfolio full width', 'xts-theme' ),
		'description' => esc_html__( 'Makes container 100% width of the page.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'portfolio_archive_section',
		'priority'    => 30,
		'default'     => '0',
	)
);

Options::add_field(
	array(
		'id'                  => 'portfolio_columns',
		'name'                => esc_html__( 'Columns', 'xts-theme' ),
		'description'         => esc_html__( 'How many projects you want to show per row.', 'xts-theme' ),
		'group'               => esc_html__( 'Layout', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'portfolio_archive_section',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'desktop_only'        => true,
		'options'             => array(
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
		'default'             => 3,
		'priority'            => 40,
	)
);

Options::add_field(
	array(
		'id'                  => 'portfolio_columns_tablet',
		'name'                => esc_html__( 'Columns (tablet)', 'xts-theme' ),
		'description'         => esc_html__( 'How many projects you want to show per row.', 'xts-theme' ),
		'group'               => esc_html__( 'Layout', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'portfolio_archive_section',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'tablet_only'         => true,
		'options'             => array(
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
		'priority'            => 50,
	)
);

Options::add_field(
	array(
		'id'                  => 'portfolio_columns_mobile',
		'name'                => esc_html__( 'Columns (mobile)', 'xts-theme' ),
		'description'         => esc_html__( 'How many projects you want to show per row.', 'xts-theme' ),
		'group'               => esc_html__( 'Layout', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'portfolio_archive_section',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'mobile_only'         => true,
		'options'             => array(
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
		'priority'            => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'portfolio_spacing',
		'name'        => esc_html__( 'Space between projects', 'xts-theme' ),
		'description' => esc_html__( 'You can set different spacing between blocks on portfolio page.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'portfolio_archive_section',
		'options'     => xts_get_available_options( 'items_gap' ),
		'default'     => 10,
		'priority'    => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'portfolio_per_page',
		'name'        => esc_html__( 'Items per page', 'xts-theme' ),
		'description' => esc_html__( 'Number of portfolio projects that will be displayed on one page.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'portfolio_archive_section',
		'type'        => 'text_input',
		'default'     => 12,
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'portfolio_masonry',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Masonry', 'xts-theme' ),
		'description' => esc_html__( 'Masonry library works by placing elements in optimal position based on available vertical space, sort of like a mason fitting stones in a wall.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'portfolio_archive_section',
		'default'     => '0',
		'priority'    => 90,
	)
);

Options::add_field(
	array(
		'id'          => 'portfolio_different_images',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Different images', 'xts-theme' ),
		'description' => esc_html__( 'Double the size of particular elements in the grid by their position indexes.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'portfolio_archive_section',
		'default'     => '0',
		'priority'    => 100,
	)
);

Options::add_field(
	array(
		'id'          => 'portfolio_different_images_position',
		'name'        => esc_html__( 'Wide items position indexes', 'xts-theme' ),
		'description' => esc_html__( 'Set order numbers for items that you want to increase in size. For example: 2,5,8,9', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'portfolio_archive_section',
		'type'        => 'text_input',
		'default'     => '2,5,8,9',
		'priority'    => 110,
		'requires'    => array(
			array(
				'key'     => 'portfolio_different_images',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
	)
);

Options::add_field(
	array(
		'id'       => 'portfolio_animation_in_view',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Animation in view', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'section'  => 'portfolio_archive_section',
		'priority' => 120,
		'default'  => '0',
	)
);

Options::add_field(
	array(
		'id'       => 'portfolio_animation',
		'name'     => esc_html__( 'Animations', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'type'     => 'select',
		'section'  => 'portfolio_archive_section',
		'options'  => xts_get_animations_array( 'default' ),
		'default'  => 'short-in-up',
		'class'    => 'xts-col-4',
		'requires' => array(
			array(
				'key'     => 'portfolio_animation_in_view',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority' => 130,
	)
);

Options::add_field(
	array(
		'id'       => 'portfolio_animation_duration',
		'name'     => esc_html__( 'Animation duration', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'type'     => 'select',
		'section'  => 'portfolio_archive_section',
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
				'key'     => 'portfolio_animation_in_view',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority' => 140,
	)
);

Options::add_field(
	array(
		'id'       => 'portfolio_animation_delay',
		'name'     => esc_html__( 'Animation delay', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'type'     => 'text_input',
		'section'  => 'portfolio_archive_section',
		'default'  => 100,
		'class'    => 'xts-col-4',
		'requires' => array(
			array(
				'key'     => 'portfolio_animation_in_view',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority' => 150,
	)
);

Options::add_field(
	array(
		'id'          => 'portfolio_pagination',
		'name'        => esc_html__( 'Pagination', 'xts-theme' ),
		'description' => esc_html__( 'Choose a type for the pagination on your portfolio page.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'portfolio_archive_section',
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
		'priority'    => 160,
	)
);

/**
 * Project options.
 */
Options::add_field(
	array(
		'id'          => 'portfolio_design',
		'name'        => esc_html__( 'Design', 'xts-theme' ),
		'description' => esc_html__( 'You can use different styles for your projects.', 'xts-theme' ),
		'group'       => esc_html__( 'Project options', 'xts-theme' ),
		'type'        => 'select',
		'section'     => 'portfolio_archive_section',
		'options'     => xts_get_available_options( 'portfolio_design' ),
		'default'     => xts_get_default_value( 'portfolio_design' ),
		'priority'    => 170,
	)
);

/**
 * Distortion effect Portfolio_Distortion_Effect (180).
 */

Options::add_field(
	array(
		'id'       => 'portfolio_image_size',
		'name'     => esc_html__( 'Image size', 'xts-theme' ),
		'group'    => esc_html__( 'Project options', 'xts-theme' ),
		'type'     => 'select',
		'section'  => 'portfolio_archive_section',
		'options'  => xts_get_all_image_sizes_names(),
		'default'  => 'large',
		'priority' => 190,
	)
);

Options::add_field(
	array(
		'id'       => 'portfolio_image_size_custom',
		'name'     => esc_html__( 'Image size custom', 'xts-theme' ),
		'group'    => esc_html__( 'Project options', 'xts-theme' ),
		'type'     => 'image_dimensions',
		'section'  => 'portfolio_archive_section',
		'requires' => array(
			array(
				'key'     => 'portfolio_image_size',
				'compare' => 'equals',
				'value'   => 'custom',
			),
		),
		'priority' => 200,
	)
);

Options::add_field(
	array(
		'id'          => 'portfolio_filters_type',
		'type'        => 'buttons',
		'name'        => esc_html__( 'Categories filters', 'xts-theme' ),
		'description' => esc_html__( 'You can switch between links that will lead to project categories and masonry filters within one page only. Or turn off the filters completely.', 'xts-theme' ),
		'group'       => esc_html__( 'Project options', 'xts-theme' ),
		'section'     => 'portfolio_archive_section',
		'options'     => array(
			'without' => array(
				'name'  => esc_html__( 'Without', 'xts-theme' ),
				'value' => 'without',
			),
			'links'   => array(
				'name'  => esc_html__( 'Links', 'xts-theme' ),
				'value' => 'links',
			),
			'masonry' => array(
				'name'  => esc_html__( 'Masonry', 'xts-theme' ),
				'value' => 'masonry',
			),
		),
		'default'     => 'links',
		'priority'    => 210,
	)
);

Options::add_field(
	array(
		'id'          => 'ajax_portfolio',
		'type'        => 'switcher',
		'name'        => esc_html__( 'AJAX portfolio', 'xts-theme' ),
		'description' => esc_html__( 'Use AJAX functionality for portfolio categories links.', 'xts-theme' ),
		'group'       => esc_html__( 'Project options', 'xts-theme' ),
		'section'     => 'portfolio_archive_section',
		'requires'    => array(
			array(
				'key'     => 'portfolio_filters_type',
				'compare' => 'equals',
				'value'   => 'links',
			),
		),
		'default'     => '1',
		'priority'    => 220,
	)
);

/**
 * Single project.
 */
Options::add_field(
	array(
		'id'          => 'portfolio_single_design',
		'name'        => esc_html__( 'Design', 'xts-theme' ),
		'description' => esc_html__( 'You can use different design for your single project page.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'type'        => 'select',
		'section'     => 'single_project_section',
		'options'     => array(
			'default'    => array(
				'name'  => esc_html__( 'Default', 'xts-theme' ),
				'value' => 'default',
			),
			'page-title' => array(
				'name'  => esc_html__( 'Image in page title', 'xts-theme' ),
				'value' => 'page-title',
			),
		),
		'default'     => 'default',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'portfolio_single_parallax_scroll',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Scale and parallax effect on scroll', 'xts-theme' ),
		'group'    => esc_html__( 'Layout', 'xts-theme' ),
		'section'  => 'single_project_section',
		'requires' => array(
			array(
				'key'     => 'portfolio_single_design',
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
		'id'           => 'portfolio_single_header',
		'name'         => esc_html__( 'Custom header', 'xts-theme' ),
		'description'  => esc_html__( 'You can use different header for your single project page.', 'xts-theme' ),
		'group'        => esc_html__( 'Layout', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'single_project_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_headers_array(),
		'priority'     => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'portfolio_single_navigation',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Projects navigation', 'xts-theme' ),
		'description' => esc_html__( 'Next and previous projects links on single project page.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'single_project_section',
		'class'       => 'xts-col-6',
		'default'     => '1',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'portfolio_single_related',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Related projects', 'xts-theme' ),
		'description' => esc_html__( 'Show related projects carousel.', 'xts-theme' ),
		'group'       => esc_html__( 'Related projects options', 'xts-theme' ),
		'section'     => 'single_project_section',
		'default'     => '1',
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'portfolio_single_related_projects_count',
		'name'        => esc_html__( 'Projects count', 'xts-theme' ),
		'description' => esc_html__( 'The total number of related projects to display.', 'xts-theme' ),
		'group'       => esc_html__( 'Related projects options', 'xts-theme' ),
		'type'        => 'text_input',
		'section'     => 'single_project_section',
		'default'     => 5,
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'portfolio_single_related_projects_per_row',
		'name'        => esc_html__( 'Columns', 'xts-theme' ),
		'description' => esc_html__( 'How many projects you want to show per row.', 'xts-theme' ),
		'group'       => esc_html__( 'Related projects options', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_project_section',
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
		'default'     => xts_get_default_value( 'portfolio_single_related_projects_per_row' ),
		'priority'    => 70,
	)
);

