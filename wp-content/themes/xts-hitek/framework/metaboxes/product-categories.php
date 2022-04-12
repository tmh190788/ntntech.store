<?php
/**
 * Product categories metaboxes
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Metaboxes;

$product_categories_metaboxes = Metaboxes::add_metabox(
	array(
		'id'         => 'xts_product_categories_metabox',
		'title'      => esc_html__( 'Categories metabox', 'xts-theme' ),
		'object'     => 'term',
		'taxonomies' => array( 'product_cat' ),
	)
);

$product_categories_metaboxes->add_section(
	array(
		'id'       => 'general',
		'name'     => esc_html__( 'General', 'xts-theme' ),
		'priority' => 10,
		'icon'     => 'xf-general',
	)
);

$product_categories_metaboxes->add_field(
	array(
		'id'       => 'page_title_bg_image',
		'name'     => esc_html__( 'Page title background image', 'xts-theme' ),
		'type'     => 'upload',
		'section'  => 'general',
		'priority' => 10,
	)
);

$product_categories_metaboxes->add_field(
	array(
		'id'       => 'page_title_bg_color',
		'name'     => esc_html__( 'Page title background color', 'xts-theme' ),
		'section'  => 'general',
		'type'     => 'color',
		'priority' => 20,
	)
);

$product_categories_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Image (icon) for categories navigation on the shop page', 'xts-theme' ),
		'id'       => 'page_title_shop_category_icon',
		'type'     => 'upload',
		'section'  => 'general',
		'priority' => 30,
	)
);

$product_categories_metaboxes->add_field(
	array(
		'id'           => 'product_categories_product_custom_template',
		'name'         => esc_html__( 'Custom template for products', 'xts-theme' ),
		'description'  => esc_html__( 'Apply a special template for single products that belong to this particular category.', 'xts-theme' ),
		'type'         => 'select',
		'section'      => 'general',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_product_templates_array(),
		'priority'     => 40,
	)
);

