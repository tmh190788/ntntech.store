<?php
/**
 * Single product page options
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
		'id'          => 'single_product_sidebar_position',
		'name'        => esc_html__( 'Position', 'xts-theme' ),
		'description' => esc_html__( 'Select main content and sidebar alignment.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'general_single_product_section',
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
		'id'          => 'single_product_sidebar_size',
		'name'        => esc_html__( 'Size', 'xts-theme' ),
		'description' => esc_html__( 'You can set different sizes for your pages sidebar', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'general_single_product_section',
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
		'id'          => 'single_product_sidebar_sticky',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Sticky sidebar', 'xts-theme' ),
		'description' => esc_html__( 'Make your sidebar stuck while you are scrolling the page content.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'section'     => 'general_single_product_section',
		'default'     => '0',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_offcanvas_sidebar_desktop',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Off canvas sidebar for desktop', 'xts-theme' ),
		'description' => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'section'     => 'general_single_product_section',
		'class'       => 'xts-col-6',
		'default'     => '0',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_offcanvas_sidebar_mobile',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Off canvas sidebar for mobile devices', 'xts-theme' ),
		'description' => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
		'group'       => esc_html__( 'Sidebar', 'xts-theme' ),
		'section'     => 'general_single_product_section',
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
		'id'          => 'single_product_page_title',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Page title', 'xts-theme' ),
		'description' => esc_html__( 'Show page title block on the single product page.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'general_single_product_section',
		'default'     => '0',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_sticky',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Sticky product', 'xts-theme' ),
		'description' => esc_html__( 'If you turn on this option, the section with description will be sticky when you scroll the page. In case when the description is higher than images, the images section will be fixed instead.', 'xts-theme' ),
		'group'       => esc_html__( 'Layout', 'xts-theme' ),
		'section'     => 'general_single_product_section',
		'default'     => '0',
		'priority'    => 70,
	)
);

Options::add_field(
	array(
		'id'           => 'single_product_header',
		'name'         => esc_html__( 'Custom header', 'xts-theme' ),
		'description'  => esc_html__( 'You can use different header for your single product page.', 'xts-theme' ),
		'group'        => esc_html__( 'Layout', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'general_single_product_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_headers_array(),
		'priority'     => 80,
	)
);

/**
 * Image.
 */

/**
 * Main image.
 */
Options::add_field(
	array(
		'id'          => 'single_product_main_gallery_width',
		'name'        => esc_html__( 'Product image width', 'xts-theme' ),
		'description' => esc_html__( 'You can choose different page layout depending on the product image size you need', 'xts-theme' ),
		'group'       => esc_html__( 'Main image', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_product_image_section',
		'options'     => array(
			's' => array(
				'name'  => esc_html__( 'Small', 'xts-theme' ),
				'value' => 's',
				'image' => XTS_ASSETS_IMAGES_URL . '/options/single-product/gallery-width/small.svg',
			),
			'm' => array(
				'name'  => esc_html__( 'Medium', 'xts-theme' ),
				'value' => 'm',
				'image' => XTS_ASSETS_IMAGES_URL . '/options/single-product/gallery-width/medium.svg',
			),
			'l' => array(
				'name'  => esc_html__( 'Large', 'xts-theme' ),
				'value' => 'l',
				'image' => XTS_ASSETS_IMAGES_URL . '/options/single-product/gallery-width/large.svg',
			),
		),
		'default'     => 'm',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_main_gallery_click_action',
		'name'        => esc_html__( 'Main gallery click action', 'xts-theme' ),
		'description' => esc_html__( 'Enable/disable zoom option or switch to photoswipe popup.', 'xts-theme' ),
		'group'       => esc_html__( 'Main image', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_product_image_section',
		'options'     => array(
			'zoom'       => array(
				'name'  => esc_html__( 'Zoom', 'xts-theme' ),
				'value' => 'zoom',
			),
			'photoswipe' => array(
				'name'  => esc_html__( 'Photoswipe popup', 'xts-theme' ),
				'value' => 'photoswipe',
			),
			'without'    => array(
				'name'  => esc_html__( 'Without', 'xts-theme' ),
				'value' => 'without',
			),
		),
		'default'     => 'zoom',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_main_gallery_photoswipe_btn',
		'name'        => esc_html__( 'Show "Zoom Image" Button', 'xts-theme' ),
		'description' => esc_html__( 'Click to open image in popup and swipe to zoom', 'xts-theme' ),
		'group'       => esc_html__( 'Main image', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'single_product_image_section',
		'default'     => '1',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_main_gallery_auto_height',
		'name'        => esc_html__( 'Main carousel auto height', 'xts-theme' ),
		'description' => esc_html__( 'Useful when you have product images with different height.', 'xts-theme' ),
		'group'       => esc_html__( 'Main image', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'single_product_image_section',
		'default'     => '0',
		'priority'    => 40,
	)
);

/**
 * Thumbnails.
 */
Options::add_field(
	array(
		'id'       => 'single_product_thumbnails_gallery_position',
		'name'     => esc_html__( 'Thumbnails layout', 'xts-theme' ),
		'group'    => esc_html__( 'Thumbnails', 'xts-theme' ),
		'type'     => 'buttons',
		'section'  => 'single_product_image_section',
		'options'  => array(
			'side'      => array(
				'name'  => esc_html__( 'Side (vertical position)', 'xts-theme' ),
				'value' => 'side',
				'image' => XTS_ASSETS_IMAGES_URL . '/options/single-product/thumbnails-position/side.svg',
			),
			'bottom'    => array(
				'name'  => esc_html__( 'Bottom (horizontal carousel)', 'xts-theme' ),
				'value' => 'bottom',
				'image' => XTS_ASSETS_IMAGES_URL . '/options/single-product/thumbnails-position/bottom.svg',
			),
			'grid-1'    => array(
				'name'  => esc_html__( 'Bottom (1 columns)', 'xts-theme' ),
				'value' => 'grid-1',
				'image' => XTS_ASSETS_IMAGES_URL . '/options/single-product/thumbnails-position/grid-1.svg',
			),
			'grid-2'    => array(
				'name'  => esc_html__( 'Bottom (2 columns)', 'xts-theme' ),
				'value' => 'grid-2',
				'image' => XTS_ASSETS_IMAGES_URL . '/options/single-product/thumbnails-position/grid-2.svg',
			),
			'grid-comb' => array(
				'name'  => esc_html__( 'Combined grid', 'xts-theme' ),
				'value' => 'grid-comb',
				'image' => XTS_ASSETS_IMAGES_URL . '/options/single-product/thumbnails-position/grid-comb.svg',
			),
			'without'   => array(
				'name'  => esc_html__( 'Without', 'xts-theme' ),
				'value' => 'without',
				'image' => XTS_ASSETS_IMAGES_URL . '/options/single-product/thumbnails-position/without.svg',
			),
		),
		'default'  => 'bottom',
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'       => 'single_product_thumbnails_gallery_count',
		'type'     => 'range',
		'section'  => 'single_product_image_section',
		'name'     => esc_html__( 'Thumbnails images per slide', 'xts-theme' ),
		'group'    => esc_html__( 'Thumbnails', 'xts-theme' ),
		'min'      => 2,
		'max'      => 6,
		'step'     => 1,
		'class'    => 'xts-col-6',
		'requires' => array(
			array(
				'key'     => 'single_product_thumbnails_gallery_position',
				'compare' => 'equals',
				'value'   => array( 'side', 'bottom' ),
			),
		),
		'default'  => 4,
		'priority' => 60,
	)
);

Options::add_field(
	array(
		'id'       => 'single_product_thumbnails_gallery_image_width',
		'type'     => 'text_input',
		'section'  => 'single_product_image_section',
		'name'     => esc_html__( 'Thumbnails image width', 'xts-theme' ),
		'group'    => esc_html__( 'Thumbnails', 'xts-theme' ),
		'class'    => 'xts-col-6',
		'default'  => '200',
		'priority' => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_main_gallery_lightbox_gallery',
		'name'        => esc_html__( 'Show thumbnails in lightbox', 'xts-theme' ),
		'description' => esc_html__( 'Display thumbnails navigation when you open the images lightbox.', 'xts-theme' ),
		'group'       => esc_html__( 'Settings', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'single_product_image_section',
		'default'     => '1',
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_main_gallery_images_captions',
		'name'        => esc_html__( 'Images captions on Photo Swipe lightbox', 'xts-theme' ),
		'description' => esc_html__( 'Display caption texts below images when you open the photoswipe popup. Captions can be added to your images via the Media library.', 'xts-theme' ),
		'group'       => esc_html__( 'Settings', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'single_product_image_section',
		'default'     => '0',
		'priority'    => 90,
	)
);

/**
 * Additional variations images single_product_additional_variations_images (100).
 */

/**
 * Tabs
 */

/**
 * Settings.
 */
Options::add_field(
	array(
		'id'          => 'single_product_tabs_layout',
		'name'        => esc_html__( 'Tabs layout', 'xts-theme' ),
		'description' => esc_html__( 'Select which style for products tabs do you need.', 'xts-theme' ),
		'group'       => esc_html__( 'Settings', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_product_tabs_section',
		'options'     => array(
			'tabs'      => array(
				'name'  => esc_html__( 'Tabs', 'xts-theme' ),
				'value' => 'tabs',
			),
			'accordion' => array(
				'name'  => esc_html__( 'Accordion', 'xts-theme' ),
				'value' => 'accordion',
			),
		),
		'default'     => 'tabs',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_hide_tabs_titles',
		'name'        => esc_html__( 'Hide tabs headings', 'xts-theme' ),
		'description' => esc_html__( 'Don\'t show duplicated titles for product tabs .', 'xts-theme' ),
		'group'       => esc_html__( 'Settings', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'single_product_tabs_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

/**
 * Additional tab 1.
 */
Options::add_field(
	array(
		'id'          => 'single_product_additional_tab_title',
		'name'        => esc_html__( 'Tab title', 'xts-theme' ),
		'description' => esc_html__( 'Leave empty to disable custom tab', 'xts-theme' ),
		'group'       => esc_html__( 'Additional tab [1]', 'xts-theme' ),
		'section'     => 'single_product_tabs_section',
		'type'        => 'text_input',
		'default'     => 'Shipping & Delivery',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_additional_tab_content_type',
		'name'        => esc_html__( 'Content type', 'xts-theme' ),
		'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
		'group'       => esc_html__( 'Additional tab [1]', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_product_tabs_section',
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
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_additional_tab_text',
		'name'        => esc_html__( 'Text', 'xts-theme' ),
		'description' => esc_html__( 'You can use any text or HTML here.', 'xts-theme' ),
		'group'       => esc_html__( 'Additional tab [1]', 'xts-theme' ),
		'section'     => 'single_product_tabs_section',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'default'     => '',
		'requires'    => array(
			array(
				'key'     => 'single_product_additional_tab_content_type',
				'compare' => 'equals',
				'value'   => 'text',
			),
		),
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'           => 'single_product_additional_tab_html_block',
		'name'         => esc_html__( 'HTML Block', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
		'group'        => esc_html__( 'Additional tab [1]', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'single_product_tabs_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'requires'     => array(
			array(
				'key'     => 'single_product_additional_tab_content_type',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'class'        => 'xts-html-block-links',
		'priority'     => 60,
	)
);

/**
 * Additional tab 2.
 */
Options::add_field(
	array(
		'id'          => 'single_product_additional_tab_title_2',
		'name'        => esc_html__( 'Tab title', 'xts-theme' ),
		'description' => esc_html__( 'Leave empty to disable custom tab', 'xts-theme' ),
		'group'       => esc_html__( 'Additional tab [2]', 'xts-theme' ),
		'section'     => 'single_product_tabs_section',
		'type'        => 'text_input',
		'priority'    => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_additional_tab_content_type_2',
		'name'        => esc_html__( 'Content type', 'xts-theme' ),
		'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
		'group'       => esc_html__( 'Additional tab [2]', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_product_tabs_section',
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
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_additional_tab_text_2',
		'name'        => esc_html__( 'Text', 'xts-theme' ),
		'description' => esc_html__( 'You can use any text or HTML here.', 'xts-theme' ),
		'group'       => esc_html__( 'Additional tab [2]', 'xts-theme' ),
		'section'     => 'single_product_tabs_section',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'default'     => '',
		'requires'    => array(
			array(
				'key'     => 'single_product_additional_tab_content_type_2',
				'compare' => 'equals',
				'value'   => 'text',
			),
		),
		'priority'    => 90,
	)
);

Options::add_field(
	array(
		'id'           => 'single_product_additional_tab_html_block_2',
		'name'         => esc_html__( 'HTML Block', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
		'group'        => esc_html__( 'Additional tab [2]', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'single_product_tabs_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'requires'     => array(
			array(
				'key'     => 'single_product_additional_tab_content_type_2',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'class'        => 'xts-html-block-links',
		'priority'     => 100,
	)
);

/**
 * Additional tab 3.
 */
Options::add_field(
	array(
		'id'          => 'single_product_additional_tab_title_3',
		'name'        => esc_html__( 'Tab title', 'xts-theme' ),
		'description' => esc_html__( 'Leave empty to disable custom tab', 'xts-theme' ),
		'group'       => esc_html__( 'Additional tab [3]', 'xts-theme' ),
		'section'     => 'single_product_tabs_section',
		'type'        => 'text_input',
		'priority'    => 110,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_additional_tab_content_type_3',
		'name'        => esc_html__( 'Content type', 'xts-theme' ),
		'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
		'group'       => esc_html__( 'Additional tab [3]', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_product_tabs_section',
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
		'priority'    => 120,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_additional_tab_text_3',
		'name'        => esc_html__( 'Text', 'xts-theme' ),
		'description' => esc_html__( 'You can use any text or HTML here.', 'xts-theme' ),
		'group'       => esc_html__( 'Additional tab [3]', 'xts-theme' ),
		'section'     => 'single_product_tabs_section',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'default'     => '',
		'requires'    => array(
			array(
				'key'     => 'single_product_additional_tab_content_type_3',
				'compare' => 'equals',
				'value'   => 'text',
			),
		),
		'priority'    => 130,
	)
);

Options::add_field(
	array(
		'id'           => 'single_product_additional_tab_html_block_3',
		'name'         => esc_html__( 'HTML Block', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
		'group'        => esc_html__( 'Additional tab [3]', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'single_product_tabs_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'requires'     => array(
			array(
				'key'     => 'single_product_additional_tab_content_type_3',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'class'        => 'xts-html-block-links',
		'priority'     => 140,
	)
);

/**
 * Add to cart.
 */
Options::add_field(
	array(
		'id'          => 'single_product_ajax_add_to_cart',
		'type'        => 'switcher',
		'name'        => esc_html__( 'AJAX Add to cart', 'xts-theme' ),
		'description' => esc_html__( 'Turn on the AJAX add to cart option on the single product page. Will not work with plugins that add some custom fields to the add to cart form.', 'xts-theme' ),
		'section'     => 'single_product_add_to_cart_section',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_sticky_add_to_cart',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Sticky add to cart', 'xts-theme' ),
		'description' => esc_html__( 'Add to cart section will be displayed at the bottom of your screen when you scroll down the page.', 'xts-theme' ),
		'section'     => 'single_product_add_to_cart_section',
		'default'     => '0',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_mobile_sticky_add_to_cart',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Sticky add to cart on mobile', 'xts-theme' ),
		'description' => esc_html__( 'You can leave this option for desktop only or enable it for all devices sizes.', 'xts-theme' ),
		'section'     => 'single_product_add_to_cart_section',
		'default'     => '0',
		'requires'    => array(
			array(
				'key'     => 'single_product_sticky_add_to_cart',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_before_add_to_cart_content_type',
		'name'        => esc_html__( 'Content type', 'xts-theme' ),
		'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
		'group'       => esc_html__( 'Before add to cart', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_product_add_to_cart_section',
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
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_before_add_to_cart_text',
		'name'        => esc_html__( 'Text', 'xts-theme' ),
		'description' => esc_html__( 'You can use any text or HTML here.', 'xts-theme' ),
		'group'       => esc_html__( 'Before add to cart', 'xts-theme' ),
		'section'     => 'single_product_add_to_cart_section',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'default'     => '',
		'requires'    => array(
			array(
				'key'     => 'single_product_before_add_to_cart_content_type',
				'compare' => 'equals',
				'value'   => 'text',
			),
		),
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'           => 'single_product_before_add_to_cart_html_block',
		'name'         => esc_html__( 'HTML Block', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
		'group'        => esc_html__( 'Before add to cart', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'single_product_add_to_cart_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'requires'     => array(
			array(
				'key'     => 'single_product_before_add_to_cart_content_type',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'class'        => 'xts-html-block-links',
		'priority'     => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_after_add_to_cart_content_type',
		'name'        => esc_html__( 'Content type', 'xts-theme' ),
		'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
		'group'       => esc_html__( 'After add to cart', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_product_add_to_cart_section',
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
		'priority'    => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_after_add_to_cart_text',
		'name'        => esc_html__( 'Text', 'xts-theme' ),
		'description' => esc_html__( 'You can use any text or HTML here.', 'xts-theme' ),
		'group'       => esc_html__( 'After add to cart', 'xts-theme' ),
		'section'     => 'single_product_add_to_cart_section',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'default'     => '',
		'requires'    => array(
			array(
				'key'     => 'single_product_after_add_to_cart_content_type',
				'compare' => 'equals',
				'value'   => 'text',
			),
		),
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'           => 'single_product_after_add_to_cart_html_block',
		'name'         => esc_html__( 'HTML Block', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
		'group'        => esc_html__( 'After add to cart', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'single_product_add_to_cart_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'requires'     => array(
			array(
				'key'     => 'single_product_after_add_to_cart_content_type',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'class'        => 'xts-html-block-links',
		'priority'     => 90,
	)
);

/**
 * Elements.
 */
Options::add_field(
	array(
		'id'          => 'single_product_nav',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Products navigation', 'xts-theme' ),
		'description' => esc_html__( 'Display next/previous products navigation.', 'xts-theme' ),
		'section'     => 'single_product_elements_section',
		'class'       => 'xts-col-6',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_short_description',
		'name'        => esc_html__( 'Short description', 'xts-theme' ),
		'description' => esc_html__( 'Enable/disable short description text in the product\'s summary block.', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'single_product_elements_section',
		'class'       => 'xts-col-6',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_attributes_table',
		'name'        => esc_html__( 'Attributes table', 'xts-theme' ),
		'description' => esc_html__( 'You can display the attributes table in the short description section. "Additional information" tab will be removed if the option enabled.', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'single_product_elements_section',
		'class'       => 'xts-col-6',
		'default'     => '0',
		'priority'    => 30,
	)
);

/**
 * Progress bar single_product_stock_progress_bar (40).
 */

/**
 * Countdown timer single_product_sale_countdown (50).
 */

Options::add_field(
	array(
		'id'          => 'single_product_variations_price',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Hide the variation price', 'xts-theme' ),
		'description' => esc_html__( 'Remove duplicated price that is displayed before add to cart when you choose any variation. Works for variable products only.', 'xts-theme' ),
		'class'       => 'xts-col-6',
		'section'     => 'single_product_elements_section',
		'default'     => '1',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_share_buttons',
		'name'        => esc_html__( 'Share buttons', 'xts-theme' ),
		'description' => esc_html__( 'Display share buttons icons on the single product page.', 'xts-theme' ),
		'group'       => esc_html__( 'Share buttons', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'single_product_elements_section',
		'default'     => '1',
		'priority'    => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_share_buttons_type',
		'name'        => esc_html__( 'Share buttons type', 'xts-theme' ),
		'description' => esc_html__( 'You can switch between share and follow buttons on the single product page.', 'xts-theme' ),
		'group'       => esc_html__( 'Share buttons', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_product_elements_section',
		'options'     => array(
			'share'  => array(
				'name'  => esc_html__( 'Share', 'xts-theme' ),
				'value' => 'share',
			),
			'follow' => array(
				'name'  => esc_html__( 'Follow', 'xts-theme' ),
				'value' => 'follow',
			),
		),
		'default'     => 'share',
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_related',
		'name'        => esc_html__( 'Related products', 'xts-theme' ),
		'description' => esc_html__( 'Related Products is a section that pulls products from your store that share the same tags or categories as the current product.', 'xts-theme' ),
		'group'       => esc_html__( 'Related products options', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'single_product_elements_section',
		'default'     => '1',
		'priority'    => 90,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_related_view',
		'name'        => esc_html__( 'View', 'xts-theme' ),
		'description' => esc_html__( 'You can set different view mode for the related products. These settings will be applied for upsells products as well.', 'xts-theme' ),
		'group'       => esc_html__( 'Related products options', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'single_product_elements_section',
		'options'     => array(
			'grid'     => array(
				'name'  => esc_html__( 'Grid', 'xts-theme' ),
				'value' => 'grid',
			),
			'carousel' => array(
				'name'  => esc_html__( 'Carousel', 'xts-theme' ),
				'value' => 'carousel',
			),
		),
		'default'     => 'carousel',
		'priority'    => 100,
	)
);

Options::add_field(
	array(
		'id'          => 'single_product_related_count',
		'name'        => esc_html__( 'Product count', 'xts-theme' ),
		'description' => esc_html__( 'The total number of related products to display.', 'xts-theme' ),
		'group'       => esc_html__( 'Related products options', 'xts-theme' ),
		'type'        => 'text_input',
		'section'     => 'single_product_elements_section',
		'default'     => 8,
		'priority'    => 110,
	)
);

Options::add_field(
	array(
		'id'                  => 'single_product_related_per_row',
		'name'                => esc_html__( 'Columns', 'xts-theme' ),
		'description'         => esc_html__( 'How many products you want to show per row.', 'xts-theme' ),
		'group'               => esc_html__( 'Related products options', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'single_product_elements_section',
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
		'default'             => 4,
		'priority'            => 120,
	)
);

Options::add_field(
	array(
		'id'                  => 'single_product_related_per_row_tablet',
		'name'                => esc_html__( 'Columns (tablet)', 'xts-theme' ),
		'description'         => esc_html__( 'How many products you want to show per row.', 'xts-theme' ),
		'group'               => esc_html__( 'Related products options', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'single_product_elements_section',
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
		'priority'            => 130,
	)
);

Options::add_field(
	array(
		'id'                  => 'single_product_related_per_row_mobile',
		'name'                => esc_html__( 'Columns (mobile)', 'xts-theme' ),
		'description'         => esc_html__( 'How many products you want to show per row.', 'xts-theme' ),
		'group'               => esc_html__( 'Related products options', 'xts-theme' ),
		'type'                => 'buttons',
		'section'             => 'single_product_elements_section',
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
		'priority'            => 140,
	)
);
