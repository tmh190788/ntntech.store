<?php
/**
 * Default header builder settings
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$header_settings = array(
	'overlap'          => array(
		'id'          => 'overlap',
		'title'       => 'Overlap',
		'type'        => 'switcher',
		'tab'         => 'General',
		'value'       => false,
		'description' => 'Make the header overlap the content.',
	),

	'boxed'            => array(
		'id'          => 'boxed',
		'title'       => 'Boxed',
		'type'        => 'switcher',
		'tab'         => 'General',
		'value'       => false,
		'description' => 'The header will be boxed instead of full width',
		'requires'    => array(
			'overlap' => array(
				'comparison' => 'equal',
				'value'      => true,
			),
		),
	),

	'background_hover' => array(
		'id'          => 'background_hover',
		'title'       => esc_html__( 'Background on hover', 'xts-theme' ),
		'description' => esc_html__( 'Overlap header with transparent background will have a white background on mouse over.', 'xts-theme' ),
		'type'        => 'switcher',
		'tab'         => 'General',
		'value'       => false,
		'requires'    => array(
			'overlap' => array(
				'comparison' => 'equal',
				'value'      => true,
			),
		),
	),

	'full_width'       => array(
		'id'          => 'full_width',
		'title'       => 'Stretch content',
		'description' => esc_html__( 'Make the header content wider then your website container.', 'xts-theme' ),
		'type'        => 'switcher',
		'tab'         => 'General',
		'value'       => false,
	),

	'sticky_effect'    => array(
		'id'          => 'sticky_effect',
		'title'       => 'Sticky effect',
		'type'        => 'selector',
		'tab'         => 'Sticky header',
		'value'       => 'stick',
		'options'     => array(
			'stick' => array(
				'value' => 'stick',
				'label' => 'Stick on scroll',
			),
			'slide' => array(
				'value' => 'slide',
				'label' => 'Slide after scrolled down',
			),
		),
		'description' => 'You can choose between two types of sticky header effects.',
	),

	'sticky_clone'     => array(
		'id'          => 'sticky_clone',
		'title'       => 'Sticky header clone',
		'type'        => 'switcher',
		'tab'         => 'Sticky header',
		'value'       => false,
		'requires'    => array(
			'sticky_effect' => array(
				'comparison' => 'equal',
				'value'      => 'slide',
			),
		),
		'description' => 'Sticky header will clone elements from the header (logo, menu, search and shopping cart widget) and show them in one line.',
	),

	'sticky_height'    => array(
		'id'          => 'sticky_height',
		'title'       => 'Sticky header height',
		'type'        => 'slider',
		'tab'         => 'Sticky header',
		'from'        => 0,
		'to'          => 200,
		'value'       => 50,
		'units'       => 'px',
		'description' => 'Determine header height for sticky header value in pixels.',
		'requires'    => array(
			'sticky_clone'  => array(
				'comparison' => 'equal',
				'value'      => true,
			),
			'sticky_effect' => array(
				'comparison' => 'equal',
				'value'      => 'slide',
			),
		),
	),

	'sticky_shadow'    => array(
		'id'          => 'sticky_shadow',
		'title'       => 'Sticky header shadow',
		'type'        => 'switcher',
		'tab'         => 'Sticky header',
		'value'       => true,
		'description' => 'Add a shadow for the header when it is sticked.',
	),

	'hide_on_scroll'   => array(
		'id'          => 'hide_on_scroll',
		'title'       => esc_html__( 'Hide when scrolling down', 'xts-theme' ),
		'description' => esc_html__( 'Hides the sticky header when you scroll the page down. And shows only when you scroll top.', 'xts-theme' ),
		'type'        => 'switcher',
		'tab'         => 'Sticky header',
		'value'       => false,
	),
);

return apply_filters( 'xts_default_header_settings', $header_settings );
