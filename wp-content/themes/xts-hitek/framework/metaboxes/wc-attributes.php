<?php
/**
 * Attributes metaboxes
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Metaboxes;

if ( ! xts_is_woocommerce_installed() ) {
	return;
}

foreach ( wc_get_attribute_taxonomies() as $attribute ) {
	Metaboxes::add_metabox(
		array(
			'id'         => 'xts_attributes_metabox_' . $attribute->attribute_name,
			'title'      => esc_html__( 'Attributes metabox', 'xts-theme' ),
			'object'     => 'term',
			'taxonomies' => array( 'pa_' . $attribute->attribute_name ),
		)
	);
}

$brand_attribute = xts_get_opt( 'brands_attribute' );

$brand_metaboxes = Metaboxes::add_metabox(
	array(
		'id'         => 'xts_brand_metaboxes',
		'title'      => esc_html__( 'Brand metaboxes', 'xts-theme' ),
		'object'     => 'term',
		'taxonomies' => array( $brand_attribute ),
	)
);

$brand_metaboxes->add_section(
	array(
		'id'       => 'general',
		'name'     => esc_html__( 'General', 'xts-theme' ),
		'priority' => 10,
		'icon'     => 'xf-general',
	)
);

$brand_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Single product tab content', 'xts-theme' ),
		'id'       => 'brand_single_product_tab_content',
		'type'     => 'textarea',
		'wysiwyg'  => false,
		'section'  => 'general',
		'priority' => 10,
	)
);
