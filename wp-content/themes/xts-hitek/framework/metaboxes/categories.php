<?php
/**
 * Categories metaboxes
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Metaboxes;

Metaboxes::add_metabox(
	array(
		'id'         => 'xts_categories_metabox',
		'title'      => esc_html__( 'Categories metabox', 'xts-theme' ),
		'object'     => 'term',
		'taxonomies' => array( 'category', 'xts-portfolio-cat' ),
	)
);
