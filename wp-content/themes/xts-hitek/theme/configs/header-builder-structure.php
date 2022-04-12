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
					'value' => true,
					'type'  => 'switcher',
				),
				'hide_mobile'            => array(
					'id'    => 'hide_mobile',
					'value' => true,
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
					'value' => array(
						'width-bottom' => '1',
						'color'        => array(
							'r' => 237,
							'g' => 237,
							'b' => 237,
							'a' => 1,
						),
						'sides'        => array(
							0 => 'top',
							1 => 'bottom',
							2 => 'left',
							3 => 'right',
						),
					),
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
					'content' => array(
						0 => array(
							'id'     => 'tp51ql2mjo5gb1kh8dam',
							'type'   => 'logo',
							'params' => array(
								'image'        => array(
									'id'    => 'image',
									'value' => array(
										'id'     => '',
										'url'    => '',
										'width'  => 0,
										'height' => 0,
									),
									'type'  => 'image',
								),
								'width'        => array(
									'id'    => 'width',
									'value' => 150,
									'type'  => 'slider',
								),
								'sticky_image' => array(
									'id'    => 'sticky_image',
									'value' => '',
									'type'  => 'image',
								),
								'sticky_width' => array(
									'id'    => 'sticky_width',
									'value' => 150,
									'type'  => 'slider',
								),
								'logo_notice'  => array(
									'id'    => 'logo_notice',
									'value' => '',
									'type'  => 'notice',
								),
							),
						),
						1 => array(
							'id'     => 'xo16iptqvsv12sxhqrer',
							'type'   => 'space',
							'params' => array(
								'direction' => array(
									'id'    => 'direction',
									'value' => 'h',
									'type'  => 'selector',
								),
								'width'     => array(
									'id'    => 'width',
									'value' => 30,
									'type'  => 'slider',
								),
								'css_class' => array(
									'id'    => 'css_class',
									'value' => '',
									'type'  => 'text',
								),
							),
						),
					),
				),
				1 => array(
					'id'      => 'column9',
					'type'    => 'column',
					'content' => array(
						0 => array(
							'id'     => 'sanrjxmqio1n8pn0mvkr',
							'type'   => 'search',
							'params' => array(
								'display'             => array(
									'id'    => 'display',
									'value' => 'form',
									'type'  => 'selector',
								),
								'search_style'        => array(
									'id'    => 'search_style',
									'value' => 'icon-alt-2',
									'type'  => 'selector',
								),
								'form_color_scheme'   => array(
									'id'    => 'form_color_scheme',
									'value' => 'inherit',
									'type'  => 'selector',
								),
								'icon_style'          => array(
									'id'    => 'icon_style',
									'value' => 'icon',
									'type'  => 'selector',
								),
								'categories_dropdown' => array(
									'id'    => 'categories_dropdown',
									'value' => true,
									'type'  => 'switcher',
								),
								'icon_type'           => array(
									'id'    => 'icon_type',
									'value' => 'default',
									'type'  => 'selector',
								),
								'custom_icon'         => array(
									'id'    => 'custom_icon',
									'value' => '',
									'type'  => 'image',
								),
								'ajax'                => array(
									'id'    => 'ajax',
									'value' => true,
									'type'  => 'switcher',
								),
								'ajax_result_count'   => array(
									'id'    => 'ajax_result_count',
									'value' => 20,
									'type'  => 'slider',
								),
								'post_type'           => array(
									'id'    => 'post_type',
									'value' => 'post',
									'type'  => 'selector',
								),
								'color_scheme'        => array(
									'id'    => 'color_scheme',
									'value' => 'dark',
									'type'  => 'selector',
								),
							),
						),
					),
				),
				2 => array(
					'id'      => 'column10',
					'type'    => 'column',
					'content' => array(
						0 => array(
							'id'     => 'bjws5ni4k4hpitg2nfav',
							'type'   => 'mainmenu',
							'params' => array(
								'menu_style'       => array(
									'id'    => 'menu_style',
									'value' => 'default',
									'type'  => 'selector',
								),
								'menu_full_height' => array(
									'id'    => 'menu_full_height',
									'value' => false,
									'type'  => 'switcher',
								),
								'menu_align'       => array(
									'id'    => 'menu_align',
									'value' => 'left',
									'type'  => 'selector',
								),
								'menu_items_gap'   => array(
									'id'    => 'menu_items_gap',
									'value' => 's',
									'type'  => 'selector',
								),
							),
						),
					),
				),
				3 => array(
					'id'      => 'column_mobile2',
					'type'    => 'column',
					'content' => array(
						0 => array(
							'id'     => 'vmip7kyqrlcwnqhc6w0q',
							'type'   => 'logo',
							'params' => array(
								'image'        => array(
									'id'    => 'image',
									'value' => array(
										'id'     => '',
										'url'    => '',
										'width'  => 0,
										'height' => 0,
									),
									'type'  => 'image',
								),
								'width'        => array(
									'id'    => 'width',
									'value' => 110,
									'type'  => 'slider',
								),
								'sticky_image' => array(
									'id'    => 'sticky_image',
									'value' => '',
									'type'  => 'image',
								),
								'sticky_width' => array(
									'id'    => 'sticky_width',
									'value' => 150,
									'type'  => 'slider',
								),
								'logo_notice'  => array(
									'id'    => 'logo_notice',
									'value' => '',
									'type'  => 'notice',
								),
							),
						),
					),
				),
				4 => array(
					'id'      => 'column_mobile3',
					'type'    => 'column',
					'content' => array(),
				),
				5 => array(
					'id'      => 'column_mobile4',
					'type'    => 'column',
					'content' => array(
						0 => array(
							'id'     => '8seo0n6gxwjrv19ni884',
							'type'   => 'burger',
							'params' => array(
								'menu_id'      => array(
									'id'    => 'menu_id',
									'value' => 'main-navigation',
									'type'  => 'select',
								),
								'style'        => array(
									'id'    => 'style',
									'value' => 'icon-text',
									'type'  => 'selector',
								),
								'icon_type'    => array(
									'id'    => 'icon_type',
									'value' => 'default',
									'type'  => 'selector',
								),
								'custom_icon'  => array(
									'id'    => 'custom_icon',
									'value' => '',
									'type'  => 'image',
								),
								'position'     => array(
									'id'    => 'position',
									'value' => 'right',
									'type'  => 'selector',
								),
								'color_scheme' => array(
									'id'    => 'color_scheme',
									'value' => 'inherit',
									'type'  => 'selector',
								),
								'search_form'  => array(
									'id'    => 'search_form',
									'value' => true,
									'type'  => 'switcher',
								),
							),
						),
					),
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
					'value' => 100,
					'type'  => 'slider',
				),
				'mobile_height'          => array(
					'id'    => 'mobile_height',
					'value' => 60,
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
					'value' => true,
					'type'  => 'switcher',
				),
				'sticky_height'          => array(
					'id'    => 'sticky_height',
					'value' => 89,
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
					'value' => array(
						'width-bottom' => '1',
						'color'        => array(
							'r' => 237,
							'g' => 237,
							'b' => 237,
							'a' => 1,
						),
						'applyFor'     => 'fullwidth',
						'sides'        => array(
							0 => 'top',
							1 => 'bottom',
							2 => 'left',
							3 => 'right',
						),
					),
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
					'value' => true,
					'type'  => 'switcher',
				),
				'hide_mobile'            => array(
					'id'    => 'hide_mobile',
					'value' => true,
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
