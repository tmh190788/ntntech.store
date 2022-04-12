<?php
/**
 * Theme list
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_theme_list',
	array(
		'hitek' => array(
			'name'        => 'Hitek',
			'slug'        => 'hitek',
			'version'     => '1.1.0',
			'woocommerce' => true,
			'categories'  => 'ecommerce,multipurpose',
		),
		
	)
);