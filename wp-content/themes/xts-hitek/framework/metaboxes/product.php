<?php
/**
 * Product metaboxes
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Metaboxes;

/**
 * Product.
 */
$product_metaboxes = Metaboxes::add_metabox(
	array(
		'id'         => 'xts_product_metaboxes',
		'title'      => esc_html__( 'Product metaboxes', 'xts-theme' ),
		'post_types' => array( 'product' ),
	)
);

$product_metaboxes->add_section(
	array(
		'id'       => 'general_section',
		'name'     => esc_html__( 'General', 'xts-theme' ),
		'priority' => 10,
		'icon'     => 'xf-general',
	)
);

$product_metaboxes->add_section(
	array(
		'id'       => 'sidebar_section',
		'name'     => esc_html__( 'Sidebar', 'xts-theme' ),
		'priority' => 20,
		'icon'     => 'xf-side-bar',
	)
);

$product_metaboxes->add_section(
	array(
		'id'       => 'additional_tab_section',
		'name'     => esc_html__( 'Additional tab', 'xts-theme' ),
		'priority' => 30,
		'icon'     => 'xf-additional-tab',
	)
);

/**
 * General.
 */
$product_metaboxes->add_field(
	array(
		'id'          => 'product_label_new',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Add "New" label', 'xts-theme' ),
		'description' => esc_html__( 'You can add "New" label to this product.', 'xts-theme' ),
		'section'     => 'general_section',
		'default'     => '0',
		'priority'    => 10,
	)
);

$product_metaboxes->add_field(
	array(
		'id'           => 'single_product_custom_template',
		'name'         => esc_html__( 'Custom template for product', 'xts-theme' ),
		'description'  => esc_html__( 'You can build custom template for products with Elementor.', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'general_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_product_templates_array(),
		'priority'     => 20,
	)
);

$product_metaboxes->add_field(
	array(
		'id'          => 'single_product_video_url',
		'type'        => 'text_input',
		'name'        => esc_html__( 'Product video URL', 'xts-theme' ),
		'description' => esc_html__( 'Example: https://youtu.be/LXb3EKWsInQ', 'xts-theme' ),
		'section'     => 'general_section',
		'default'     => '',
		'priority'    => 30,
	)
);

$product_metaboxes->add_field(
	array(
		'id'          => 'single_product_360_view',
		'type'        => 'upload_list',
		'name'        => esc_html__( 'Product 360 view gallery', 'xts-theme' ),
		'description' => esc_html__( 'Upload a set of images that demonstrate the product from different angles of view. The recommended number of images 20+.', 'xts-theme' ),
		'section'     => 'general_section',
		'default'     => '',
		'priority'    => 40,
	)
);

/**
 * Size guide single_product_disable_size_guide (50 60).
 */

/**
 * Swatches attribute swatches_attribute (70).
 */

$product_metaboxes->add_field(
	array(
		'id'              => 'single_product_bg',
		'name'            => esc_html__( 'Background for page', 'xts-theme' ),
		'type'            => 'background',
		'default'         => array(),
		'section'         => 'general_section',
		'selector'        => 'body.single-product .xts-site-content, body.single-xts-template .xts-site-content',
		'option_override' => 'single_product_bg',
		'priority'        => 80,
	)
);

/**
 * Sidebar.
 */
$product_metaboxes->add_field(
	array(
		'id'              => 'single_product_sidebar_position',
		'name'            => esc_html__( 'Sidebar position', 'xts-theme' ),
		'description'     => esc_html__( 'Select main content and sidebar alignment.', 'xts-theme' ),
		'type'            => 'buttons',
		'section'         => 'sidebar_section',
		'option_override' => 'single_product_sidebar_position',
		'options'         => array(
			'inherit'  => array(
				'name'  => esc_html__( 'Inherit', 'xts-theme' ),
				'value' => 'inherit',
			),
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
		'default'         => 'inherit',
		'priority'        => 10,
	)
);

$product_metaboxes->add_field(
	array(
		'id'              => 'single_product_sidebar_size',
		'name'            => esc_html__( 'Size', 'xts-theme' ),
		'description'     => esc_html__( 'You can set different sizes for your pages sidebar', 'xts-theme' ),
		'type'            => 'buttons',
		'section'         => 'sidebar_section',
		'option_override' => 'single_product_sidebar_size',
		'options'         => array(
			'inherit' => array(
				'name'  => esc_html__( 'Inherit', 'xts-theme' ),
				'value' => 'inherit',
			),
			'small'   => array(
				'name'  => esc_html__( 'Small', 'xts-theme' ),
				'value' => 'small',
			),
			'medium'  => array(
				'name'  => esc_html__( 'Medium', 'xts-theme' ),
				'value' => 'medium',
			),
			'large'   => array(
				'name'  => esc_html__( 'Large', 'xts-theme' ),
				'value' => 'large',
			),
		),
		'default'         => 'inherit',
		'priority'        => 20,
	)
);

$product_metaboxes->add_field(
	array(
		'id'              => 'single_product_sidebar_sticky',
		'type'            => 'switcher',
		'name'            => esc_html__( 'Sticky sidebar', 'xts-theme' ),
		'description'     => esc_html__( 'Make your sidebar stuck while you are scrolling the page content.', 'xts-theme' ),
		'section'         => 'sidebar_section',
		'option_override' => 'single_product_sidebar_sticky',
		'default'         => '0',
		'priority'        => 30,
	)
);

$product_metaboxes->add_field(
	array(
		'id'              => 'single_product_offcanvas_sidebar_desktop',
		'type'            => 'switcher',
		'name'            => esc_html__( 'Off canvas sidebar for desktop', 'xts-theme' ),
		'description'     => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
		'section'         => 'sidebar_section',
		'option_override' => 'single_product_offcanvas_sidebar_desktop',
		'class'           => 'xts-col-6',
		'default'         => '0',
		'priority'        => 40,
	)
);

$product_metaboxes->add_field(
	array(
		'id'              => 'single_product_offcanvas_sidebar_mobile',
		'type'            => 'switcher',
		'name'            => esc_html__( 'Off canvas sidebar for mobile devices', 'xts-theme' ),
		'description'     => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
		'section'         => 'sidebar_section',
		'option_override' => 'single_product_offcanvas_sidebar_mobile',
		'class'           => 'xts-col-6',
		'default'         => '1',
		'priority'        => 50,
	)
);

/**
 * Additional tab.
 */
$product_metaboxes->add_field(
	array(
		'id'          => 'single_product_custom_additional_tab_title',
		'name'        => esc_html__( 'Title', 'xts-theme' ),
		'description' => esc_html__( 'Leave empty to disable custom tab', 'xts-theme' ),
		'section'     => 'additional_tab_section',
		'type'        => 'text_input',
		'default'     => '',
		'priority'    => 10,
	)
);

$product_metaboxes->add_field(
	array(
		'id'          => 'single_product_custom_additional_tab_content_type',
		'name'        => esc_html__( 'Content type', 'xts-theme' ),
		'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'additional_tab_section',
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

$product_metaboxes->add_field(
	array(
		'id'          => 'single_product_custom_additional_tab_text',
		'name'        => esc_html__( 'Text', 'xts-theme' ),
		'description' => esc_html__( 'You can use any text or HTML here.', 'xts-theme' ),
		'section'     => 'additional_tab_section',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'default'     => '',
		'requires'    => array(
			array(
				'key'     => 'single_product_custom_additional_tab_content_type',
				'compare' => 'equals',
				'value'   => 'text',
			),
		),
		'priority'    => 30,
	)
);

$product_metaboxes->add_field(
	array(
		'id'           => 'single_product_custom_additional_tab_html_block',
		'name'         => esc_html__( 'HTML Block', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
		'type'         => 'select',
		'section'      => 'additional_tab_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'requires'     => array(
			array(
				'key'     => 'single_product_custom_additional_tab_content_type',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'class'        => 'xts-html-block-links',
		'priority'     => 40,
	)
);
