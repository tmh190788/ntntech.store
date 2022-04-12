<?php
/**
 * Infobox element template
 *
 * @package xts
 */

$params['button_text']           = '';
$params['icon_size']             = '';
$params['icon_shape']            = '';
$params['icon_type']             = 'image';
$params['extra_wrapper_classes'] = 'xts-header-infobox';
$params['header_builder']        = 'yes';

if ( isset( $params['infobox_link'] ) ) {
	$params['infobox_link'] = array(
		'url'         => $params['infobox_link'],
		'is_external' => 'off',
	);
}

if ( isset( $params['image_gap'] ) && $params['image_gap'] ) {
	$params['extra_wrapper_classes'] .= ' xts-icon-gap-' . $params['image_gap'];
}

if ( 'custom' === $params['image_size'] ) {
	$params['image_custom_dimension'] = array(
		'width'  => $params['image_width'],
		'height' => $params['image_height'],
	);
}

xts_infobox_template( $params ); // phpcs:ignore
