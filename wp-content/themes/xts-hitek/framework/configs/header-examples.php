<?php
/**
 * Header builder premade example layouts
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_header_examples',
	array(
		'empty'      => array(
			'name'    => 'Empty header',
			'preview' => XTS_ASSETS_IMAGES_URL . '/header-builder/header-examples/empty.svg',
		),
		'base'       => array(
			'name'    => 'Base',
			'preview' => XTS_ASSETS_IMAGES_URL . '/header-builder/header-examples/base.svg',
		),
		'double'     => array(
			'name'    => 'Double menu',
			'preview' => XTS_ASSETS_IMAGES_URL . '/header-builder/header-examples/double.svg',
		),
		'ecommerce'  => array(
			'name'    => 'eCommerce',
			'preview' => XTS_ASSETS_IMAGES_URL . '/header-builder/header-examples/ecommerce.svg',
		),
		'logo'       => array(
			'name'    => 'Logo center',
			'preview' => XTS_ASSETS_IMAGES_URL . '/header-builder/header-examples/logo.svg',
		),
		'simplified' => array(
			'name'    => 'Simplified',
			'preview' => XTS_ASSETS_IMAGES_URL . '/header-builder/header-examples/simplified.svg',
		),
	)
);
