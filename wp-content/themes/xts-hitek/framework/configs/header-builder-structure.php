<?php
/**
 * Default header builder structure
 *
 * @package xts
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return array(
	'id'      => 'root',
	'type'    => 'root',
	'content' => array(
		0 => array(
			'id'      => 'top-bar',
			'type'    => 'row',
			'content' => array(
				0 => array(
					'id'      => 'column5',
					'type'    => 'column',
					'content' => array(),
				),
				1 => array(
					'id'      => 'column6',
					'type'    => 'column',
					'content' => array(),
				),
				2 => array(
					'id'      => 'column7',
					'type'    => 'column',
					'content' => array(),
				),
				3 => array(
					'id'      => 'column_mobile1',
					'type'    => 'column',
					'content' => array(),
				),
			),
			'params'  => array(
				'flex_layout'            => array(
					'id'    => 'flex_layout',
					'value' => 'stretch-center',
					'type'  => 'selector',
				),
				'height'                 => array(
					'id'    => 'height',
					'value' => 40,
					'type'  => 'slider',
				),
				'mobile_height'          => array(
					'id'    => 'mobile_height',
					'value' => 40,
					'type'  => 'slider',
				),
				'align_dropdowns_bottom' => array(
					'id'    => 'align_dropdowns_bottom',
					'value' => true,
					'type'  => 'switcher',
				),
				'hide_desktop'           => array(
					'id'    => 'hide_desktop',
					'value' => false,
					'type'  => 'switcher',
				),
				'hide_mobile'            => array(
					'id'    => 'hide_mobile',
					'value' => false,
					'type'  => 'switcher',
				),
				'sticky'                 => array(
					'id'    => 'sticky',
					'value' => false,
					'type'  => 'switcher',
				),
				'sticky_height'          => array(
					'id'    => 'sticky_height',
					'value' => 40,
					'type'  => 'slider',
				),
				'color_scheme'           => array(
					'id'    => 'color_scheme',
					'value' => 'dark',
					'type'  => 'selector',
				),
				'shadow'                 => array(
					'id'    => 'shadow',
					'value' => false,
					'type'  => 'switcher',
				),
				'background'             => array(
					'id'    => 'background',
					'value' => '',
					'type'  => 'bg',
				),
				'border'                 => array(
					'id'    => 'border',
					'value' => '',
					'type'  => 'border',
				),
			),
		),
		1 => array(
			'id'      => 'general-header',
			'type'    => 'row',
			'content' => array(
				0 => array(
					'id'      => 'column8',
					'type'    => 'column',
					'content' => array(),
				),
				1 => array(
					'id'      => 'column9',
					'type'    => 'column',
					'content' => array(),
				),
				2 => array(
					'id'      => 'column10',
					'type'    => 'column',
					'content' => array(),
				),
				3 => array(
					'id'      => 'column_mobile2',
					'type'    => 'column',
					'content' => array(),
				),
				4 => array(
					'id'      => 'column_mobile3',
					'type'    => 'column',
					'content' => array(),
				),
				5 => array(
					'id'      => 'column_mobile4',
					'type'    => 'column',
					'content' => array(),
				),
			),
			'params'  => array(
				'flex_layout'            => array(
					'id'    => 'flex_layout',
					'value' => 'stretch-center',
					'type'  => 'selector',
				),
				'height'                 => array(
					'id'    => 'height',
					'value' => 90,
					'type'  => 'slider',
				),
				'mobile_height'          => array(
					'id'    => 'mobile_height',
					'value' => 60,
					'type'  => 'slider',
				),
				'align_dropdowns_bottom' => array(
					'id'    => 'align_dropdowns_bottom',
					'value' => false,
					'type'  => 'switcher',
				),
				'hide_desktop'           => array(
					'id'    => 'hide_desktop',
					'value' => false,
					'type'  => 'switcher',
				),
				'hide_mobile'            => array(
					'id'    => 'hide_mobile',
					'value' => false,
					'type'  => 'switcher',
				),
				'sticky'                 => array(
					'id'    => 'sticky',
					'value' => false,
					'type'  => 'switcher',
				),
				'sticky_height'          => array(
					'id'    => 'sticky_height',
					'value' => 60,
					'type'  => 'slider',
				),
				'color_scheme'           => array(
					'id'    => 'color_scheme',
					'value' => 'dark',
					'type'  => 'selector',
				),
				'shadow'                 => array(
					'id'    => 'shadow',
					'value' => false,
					'type'  => 'switcher',
				),
				'background'             => array(
					'id'    => 'background',
					'value' => '',
					'type'  => 'bg',
				),
				'border'                 => array(
					'id'    => 'border',
					'value' => '',
					'type'  => 'border',
				),
			),
		),
		2 => array(
			'id'      => 'header-bottom',
			'type'    => 'row',
			'content' => array(
				0 => array(
					'id'      => 'column11',
					'type'    => 'column',
					'content' => array(),
				),
				1 => array(
					'id'      => 'column12',
					'type'    => 'column',
					'content' => array(),
				),
				2 => array(
					'id'      => 'column13',
					'type'    => 'column',
					'content' => array(),
				),
				3 => array(
					'id'      => 'column_mobile5',
					'type'    => 'column',
					'content' => array(),
				),
			),
			'params'  => array(
				'flex_layout'            => array(
					'id'    => 'flex_layout',
					'value' => 'stretch-center',
					'type'  => 'selector',
				),
				'height'                 => array(
					'id'    => 'height',
					'value' => 50,
					'type'  => 'slider',
				),
				'mobile_height'          => array(
					'id'    => 'mobile_height',
					'value' => 50,
					'type'  => 'slider',
				),
				'align_dropdowns_bottom' => array(
					'id'    => 'align_dropdowns_bottom',
					'value' => true,
					'type'  => 'switcher',
				),
				'hide_desktop'           => array(
					'id'    => 'hide_desktop',
					'value' => false,
					'type'  => 'switcher',
				),
				'hide_mobile'            => array(
					'id'    => 'hide_mobile',
					'value' => false,
					'type'  => 'switcher',
				),
				'sticky'                 => array(
					'id'    => 'sticky',
					'value' => false,
					'type'  => 'switcher',
				),
				'sticky_height'          => array(
					'id'    => 'sticky_height',
					'value' => 50,
					'type'  => 'slider',
				),
				'color_scheme'           => array(
					'id'    => 'color_scheme',
					'value' => 'dark',
					'type'  => 'selector',
				),
				'shadow'                 => array(
					'id'    => 'shadow',
					'value' => false,
					'type'  => 'switcher',
				),
				'background'             => array(
					'id'    => 'background',
					'value' => '',
					'type'  => 'bg',
				),
				'border'                 => array(
					'id'    => 'border',
					'value' => '',
					'type'  => 'border',
				),
			),
		),
	),
);
