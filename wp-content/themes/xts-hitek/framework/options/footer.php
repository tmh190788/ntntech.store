<?php
/**
 * Footer options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

/**
 * Footer.
 */
Options::add_field(
	array(
		'id'       => 'footer',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Footer', 'xts-theme' ),
		'section'  => 'footer_section',
		'default'  => '1',
		'priority' => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'footer_content_type',
		'name'        => esc_html__( 'Content type', 'xts-theme' ),
		'description' => esc_html__( 'You can display content as widgets added via Appearance -> Widgets or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
		'group'       => esc_html__( 'Content', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'footer_section',
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
		'priority'    => 20,
	)
);


Options::add_field(
	array(
		'id'           => 'footer_html_block',
		'name'         => esc_html__( 'HTML Block', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
		'group'        => esc_html__( 'Content', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'footer_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'requires'     => array(
			array(
				'key'     => 'footer_content_type',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'class'        => 'xts-html-block-links',
		'priority'     => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'footer_layout',
		'name'        => esc_html__( 'Layout', 'xts-theme' ),
		'description' => esc_html__( 'Choose your footer layout. Depending on columns number you will have different number of widget areas for footer in Appearance->Widgets', 'xts-theme' ),
		'group'       => esc_html__( 'Content', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'footer_section',
		'default'     => 1,
		'options'     => xts_get_available_options( 'footer_layout' ),
		'requires'    => array(
			array(
				'key'     => 'footer_content_type',
				'compare' => 'equals',
				'value'   => 'widgets',
			),
		),
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'footer_bg',
		'name'        => esc_html__( 'Background', 'xts-theme' ),
		'description' => esc_html__( 'You can set your footer section background color or upload some graphic.', 'xts-theme' ),
		'group'       => esc_html__( 'Style', 'xts-theme' ),
		'type'        => 'background',
		'section'     => 'footer_section',
		'selector'    => '.xts-footer',
		'default'     => xts_get_default_value( 'footer_bg' ),
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'footer_color_scheme',
		'name'        => esc_html__( 'Color scheme', 'xts-theme' ),
		'description' => esc_html__( 'You can set different text colors depending on its background. May be light or dark.', 'xts-theme' ),
		'group'       => esc_html__( 'Style', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'footer_section',
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
		'default'     => xts_get_default_value( 'dark' ),
		'priority'    => 60,
		'class'       => 'xts-color-scheme-picker',
	)
);

Options::add_field(
	array(
		'id'          => 'footer_widgets_collapse',
		'section'     => 'footer_section',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Collapse widgets on mobile', 'xts-theme' ),
		'description' => esc_html__( 'Widgets added to the footer will be collapsed by default and opened when you click on their titles.', 'xts-theme' ),
		'group'       => esc_html__( 'Style', 'xts-theme' ),
		'default'     => '0',
		'priority'    => 70,
	)
);

/**
 * Copyrights.
 */
Options::add_field(
	array(
		'id'          => 'copyrights',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Copyrights', 'xts-theme' ),
		'description' => esc_html__( 'Turn on/off a section with your copyrights under the footer.', 'xts-theme' ),
		'section'     => 'copyrights_section',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'copyrights_layout',
		'name'        => esc_html__( 'Layout', 'xts-theme' ),
		'description' => esc_html__( 'Set different copyrights section layout.', 'xts-theme' ),
		'type'        => 'select',
		'section'     => 'copyrights_section',
		'options'     => array(
			'two_columns' => array(
				'name'  => esc_html__( 'Two columns', 'xts-theme' ),
				'value' => 'two_columns',
			),
			'centered'    => array(
				'name'  => esc_html__( 'Single column', 'xts-theme' ),
				'value' => 'centered',
			),
		),
		'default'     => xts_get_default_value( 'copyrights_layout' ),
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'copyrights_content_type',
		'name'        => esc_html__( 'Content type', 'xts-theme' ),
		'description' => esc_html__( 'You can display content as widgets added via Appearance -> Widgets or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'copyrights_section',
		'options'     => array(
			'widgets' => array(
				'name'  => esc_html__( 'Widgets', 'xts-theme' ),
				'value' => 'widgets',
			),
			'text'    => array(
				'name'  => esc_html__( 'Text', 'xts-theme' ),
				'value' => 'text',
			),
		),
		'default'     => 'text',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'copyrights_left_text',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'name'        => esc_html__( 'Text left', 'xts-theme' ),
		'description' => esc_html__( 'You can use any HTML, shortcodes, or place an HTML Block built with Elementor builder there like [html_block id="258"]', 'xts-theme' ),
		'section'     => 'copyrights_section',
		'requires'    => array(
			array(
				'key'     => 'copyrights_content_type',
				'compare' => 'equals',
				'value'   => 'text',
			),
		),
		'default'     => '© 2011-' . date( 'Y' ) . ' &#8226; ' . get_option( 'blogname' ) . ' &#8226; Theme designed and coded by <a href="' . esc_url( XTS_SPACE_URL ) . '" target="_blank"><strong>Xtemos Studio</strong></a>.',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'copyrights_right_text',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'name'        => esc_html__( 'Text right', 'xts-theme' ),
		'description' => esc_html__( 'You can use any HTML, shortcodes, or place an HTML Block built with Elementor builder there like [html_block id="258"]', 'xts-theme' ),
		'section'     => 'copyrights_section',
		'requires'    => array(
			array(
				'key'     => 'copyrights_content_type',
				'compare' => 'equals',
				'value'   => 'text',
			),
			array(
				'key'     => 'copyrights_layout',
				'compare' => 'equals',
				'value'   => 'two_columns',
			),
		),
		'default'     => '',
		'priority'    => 50,
	)
);

/**
 * Prefooter.
 */
Options::add_field(
	array(
		'id'           => 'prefooter_html_block',
		'name'         => esc_html__( 'HTML Block', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a><br>Prefooter is a section where you can put any custom content and it will be displayed globally. You need to create an HTML Block with Elementor and select it from the dropdown.',
		'type'         => 'select',
		'section'      => 'prefooter_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'class'        => 'xts-html-block-links',
		'priority'     => 10,
	)
);
