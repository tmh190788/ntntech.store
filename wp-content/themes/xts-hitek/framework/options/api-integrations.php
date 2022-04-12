<?php
/**
 * General framework options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

/**
 * Google map API.
 */
Options::add_field(
	array(
		'id'          => 'google_map_api_key',
		'type'        => 'text_input',
		'name'        => esc_html__( 'Google map API key', 'xts-theme' ),
		'description' => 'Obtain API key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">here</a> to use our Google Map Elementor element.',
		'section'     => 'google_map_api_section',
		'priority'    => 10,
	)
);

/**
 * Instagram.
 */
Options::add_field(
	array(
		'id'          => 'instagram_api',
		'type'        => 'instagram_api',
		'name'        => esc_html__( 'Connect instagram account', 'xts-theme' ),
		'description' => 'Follow our <a href="' . esc_url( XTS_DOCS_URL ) . 'how-to-set-up-the-instagram-api" target="_blank">documentation</a> guide about how to prepare your account and connect to the website.',
		'section'     => 'instagram_api_section',
		'priority'    => 20,
	)
);

/**
 * Social authentication social_authentication_section (30).
 */
