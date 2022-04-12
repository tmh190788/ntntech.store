<?php
/**
 * JS scripts.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return array(
	'menu-overlay' => array(
		array(
			'title'     => esc_html__( 'Menu overlay script', 'xts-theme' ),
			'name'      => 'menu-overlay-method',
			'file'      => '/js/scripts/menuOverlay',
			'in_footer' => true,
		),
	),
);
