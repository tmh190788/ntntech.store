<?php
/**
 * Additional pages.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_additional_pages',
	array(
		'about-me-1' => array(
			'title' => 'About me 1',
			'slug'  => 'about-me-1',
		),
		'about-us-1' => array(
			'title' => 'About us 1',
			'slug'  => 'about-us-1',
		),
		'contact-us-1' => array(
			'title' => 'Contact us 1',
			'slug'  => 'contact-us-1',
		),
		'contact-us-2' => array(
			'title' => 'Contact us 2',
			'slug'  => 'contact-us-2',
		),
		'contact-us-3' => array(
			'title' => 'Contact us 3',
			'slug'  => 'contact-us-3',
		),
		'contact-us-4' => array(
			'title' => 'Contact us 4',
			'slug'  => 'contact-us-4',
		),
		'contact-us-5' => array(
			'title' => 'Contact us 5',
			'slug'  => 'contact-us-5',
		),
		'faq-1' => array(
			'title' => 'FAQ 1',
			'slug'  => 'faq-1',
		),
		'faq-2' => array(
			'title' => 'FAQ 2',
			'slug'  => 'faq-2',
		),
		
	)
);