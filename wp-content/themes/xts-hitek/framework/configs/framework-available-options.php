<?php
/**
 * Options for theme settings and elements.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_framework_available_options_array',
	array(
		'slider_dots_style'                              => array(
			'default'  => array(
				'name'  => esc_html__( 'Default', 'xts-theme' ),
				'value' => 'default',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/dots/default.svg',
			),
			'numbers'  => array(
				'name'  => esc_html__( 'Numbers', 'xts-theme' ),
				'value' => 'numbers',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/dots/numbers.svg',
			),
			'disabled' => array(
				'name'  => esc_html__( 'Disabled', 'xts-theme' ),
				'value' => 'disabled',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/dots/disabled.svg',
			),
		),

		'slider_arrows_style'                            => array(
			'simple'   => array(
				'name'  => esc_html__( 'Simple', 'xts-theme' ),
				'value' => 'simple',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/arrow/simple.svg',
			),
			'bg'       => array(
				'name'  => esc_html__( 'With background', 'xts-theme' ),
				'value' => 'bg',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/arrow/square.svg',
			),
			'disabled' => array(
				'name'  => esc_html__( 'Disabled', 'xts-theme' ),
				'value' => 'disabled',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/arrow/disabled.svg',
			),
		),

		'footer_layout'                                  => array(
			1  => array(
				'name'  => esc_html__( 'Single Column', 'xts-theme' ),
				'value' => 1,
				'image' => XTS_ASSETS_IMAGES_URL . '/options/footer/layout/footer-1.svg',
			),
			2  => array(
				'name'  => esc_html__( 'Two Columns', 'xts-theme' ),
				'value' => 2,
				'image' => XTS_ASSETS_IMAGES_URL . '/options/footer/layout/footer-2.svg',
			),
			3  => array(
				'name'  => esc_html__( 'Three Columns', 'xts-theme' ),
				'value' => 3,
				'image' => XTS_ASSETS_IMAGES_URL . '/options/footer/layout/footer-3.svg',
			),
			4  => array(
				'name'  => esc_html__( 'Four Columns', 'xts-theme' ),
				'value' => 4,
				'image' => XTS_ASSETS_IMAGES_URL . '/options/footer/layout/footer-4.svg',
			),
			5  => array(
				'name'  => esc_html__( 'Five Columns', 'xts-theme' ),
				'value' => 5,
				'image' => XTS_ASSETS_IMAGES_URL . '/options/footer/layout/footer-5.svg',
			),
			6  => array(
				'name'  => esc_html__( 'Six Columns', 'xts-theme' ),
				'value' => 6,
				'image' => XTS_ASSETS_IMAGES_URL . '/options/footer/layout/footer-6.svg',
			),
			13 => array(
				'name'  => esc_html__( 'Two columns + three columns', 'xts-theme' ),
				'value' => 13,
				'image' => XTS_ASSETS_IMAGES_URL . '/options/footer/layout/footer-13.svg',
			),
			16 => array(
				'name'  => esc_html__( 'Three columns + two columns', 'xts-theme' ),
				'value' => 16,
				'image' => XTS_ASSETS_IMAGES_URL . '/options/footer/layout/footer-16.svg',
			),
			14 => array(
				'name'  => esc_html__( 'Five columns with first and last wide', 'xts-theme' ),
				'value' => 14,
				'image' => XTS_ASSETS_IMAGES_URL . '/options/footer/layout/footer-14.svg',
			),
		),

		'items_gap_elementor'                            => array(
			0  => esc_html__( '0 px', 'xts-theme' ),
			2  => esc_html__( '2 px', 'xts-theme' ),
			10 => esc_html__( '10 px', 'xts-theme' ),
			20 => esc_html__( '20 px', 'xts-theme' ),
			30 => esc_html__( '30 px', 'xts-theme' ),
		),

		'items_gap'                                      => array(
			0  => array(
				'name'  => 0,
				'value' => 0,
			),
			2  => array(
				'name'  => 2,
				'value' => 2,
			),
			10 => array(
				'name'  => 10,
				'value' => 10,
			),
			20 => array(
				'name'  => 20,
				'value' => 20,
			),
			30 => array(
				'name'  => 30,
				'value' => 30,
			),
		),

		'single_product_action_buttons_style_elementor'  => array(
			'inline' => array(
				'title' => esc_html__( 'Icon with text', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/single-product/action-buttons/style/inline.svg',
				'style' => 'col-2',
			),
			'icon'   => array(
				'title' => esc_html__( 'Icon only', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/single-product/action-buttons/style/icon.svg',
			),
		),

		'cart_widget_type_header_builder'                => array(
			'side'     => array(
				'value' => 'side',
				'label' => esc_html__( 'Hidden sidebar', 'xts-theme' ),
			),
			'dropdown' => array(
				'value' => 'dropdown',
				'label' => esc_html__( 'Dropdown', 'xts-theme' ),
			),
			'without'  => array(
				'value' => 'without',
				'label' => esc_html__( 'Without widget', 'xts-theme' ),
			),
		),

		'cart_design_header_builder'                     => array(
			'default'    => array(
				'value' => 'default',
				'label' => esc_html__( 'Default', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/default.svg',
			),
			'count'      => array(
				'value' => 'count',
				'label' => esc_html__( 'Count', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/count.svg',
			),
			'count-alt'  => array(
				'value' => 'count-alt',
				'label' => esc_html__( 'Count alternative', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/count-alt.svg',
			),
			'count-text' => array(
				'value' => 'count-text',
				'label' => esc_html__( 'Count text', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/count-text.svg',
			),
		),

		'my_account_widget_type_header_builder'          => array(
			'side' => array(
				'value' => 'side',
				'label' => esc_html__( 'Hidden sidebar', 'xts-theme' ),
			),
		),

		'wishlist_design_header_builder'                 => array(
			'default'    => array(
				'value' => 'default',
				'label' => esc_html__( 'Default', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/default.svg',
			),
			'count'      => array(
				'value' => 'count',
				'label' => esc_html__( 'Count', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/count.svg',
			),
			'count-alt'  => array(
				'value' => 'count-alt',
				'label' => esc_html__( 'Count alternative', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/count-alt.svg',
			),
			'count-text' => array(
				'value' => 'count-text',
				'label' => esc_html__( 'Count text', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/count-text.svg',
			),
		),

		'compare_design_header_builder'                  => array(
			'default'    => array(
				'value' => 'default',
				'label' => esc_html__( 'Default', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/default.svg',
			),
			'count'      => array(
				'value' => 'count',
				'label' => esc_html__( 'Count', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/count.svg',
			),
			'count-alt'  => array(
				'value' => 'count-alt',
				'label' => esc_html__( 'Count alternative', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/count-alt.svg',
			),
			'count-text' => array(
				'value' => 'count-text',
				'label' => esc_html__( 'Count text', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/count-text.svg',
			),
		),

		'search_style_header_builder'                    => array(
			'default'  => array(
				'title' => esc_html__( 'Default', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/search/form/default.svg',
			),
			'icon-alt' => array(
				'title' => esc_html__( 'Alternative', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/search/form/icon-alt.svg',
			),
			'with-bg'  => array(
				'title' => esc_html__( 'With background', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/search/form/with-bg.svg',
			),
		),

		'search_style_widget'                            => array(
			esc_html__( 'Default', 'xts-theme' )         => 'default',
			esc_html__( 'Alternative', 'xts-theme' )     => 'icon-alt',
			esc_html__( 'With background', 'xts-theme' ) => 'with-bg',
		),

		'search_style_elementor'                         => array(
			'default'  => array(
				'value' => 'default',
				'label' => esc_html__( 'Default', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/search/form/default.svg',
			),
			'icon-alt' => array(
				'value' => 'icon-alt',
				'label' => esc_html__( 'Alternative', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/search/form/icon-alt.svg',
			),
			'with-bg'  => array(
				'value' => 'with-bg',
				'label' => esc_html__( 'With background', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/search/form/with-bg.svg',
			),
		),

		'blog_single_design'                             => array(
			'default'    => array(
				'name'  => esc_html__( 'Default', 'xts-theme' ),
				'value' => 'default',
			),
			'page-title' => array(
				'name'  => esc_html__( 'Image in page title', 'xts-theme' ),
				'value' => 'page-title',
			),
		),

		'slide_change_animation'                         => array(
			'slide'          => array(
				'name'  => esc_html__( 'Slide', 'xts-theme' ),
				'value' => 'slide',
			),
			'fade'           => array(
				'name'  => esc_html__( 'Fade', 'xts-theme' ),
				'value' => 'fade',
			),
			'zoom-out-short' => array(
				'name'  => esc_html__( 'Zoom out short', 'xts-theme' ),
				'value' => 'zoom-out-short',
			),
			'zoom-in-long'   => array(
				'name'  => esc_html__( 'Zoom in long', 'xts-theme' ),
				'value' => 'zoom-in-long',
			),
			'zoom-out-long'  => array(
				'name'  => esc_html__( 'Zoom out long', 'xts-theme' ),
				'value' => 'zoom-out-long',
			),
			'parallax'       => array(
				'name'  => esc_html__( 'Parallax', 'xts-theme' ),
				'value' => 'parallax',
			),
		),

		'animations'                                     => array(
			''               => esc_html__( 'None', 'xts-theme' ),
			'short-in-down'  => esc_html__( 'Short in down', 'xts-theme' ),
			'short-in-up'    => esc_html__( 'Short in up', 'xts-theme' ),
			'short-in-right' => esc_html__( 'Short in right', 'xts-theme' ),
			'short-in-left'  => esc_html__( 'Short in left', 'xts-theme' ),
			'long-in-down'   => esc_html__( 'Long in down', 'xts-theme' ),
			'long-in-up'     => esc_html__( 'Long in up', 'xts-theme' ),
			'long-in-right'  => esc_html__( 'Long in right', 'xts-theme' ),
			'long-in-left'   => esc_html__( 'Long in left', 'xts-theme' ),
			'fade-in'        => esc_html__( 'Fade in', 'xts-theme' ),
			'flip-y-right'   => esc_html__( 'Flip Y right', 'xts-theme' ),
			'flip-y-left'    => esc_html__( 'Flip Y left', 'xts-theme' ),
			'flip-x-top'     => esc_html__( 'Flip X top', 'xts-theme' ),
			'flip-x-bottom'  => esc_html__( 'Flip X bottom', 'xts-theme' ),
			'zoom-in'        => esc_html__( 'Zoom in', 'xts-theme' ),
			'rotate-Z'       => esc_html__( 'Rotate Z', 'xts-theme' ),
			'scale-bottom'   => esc_html__( 'Scale from bottom', 'xts-theme' ),
		),

		'banner_element_hover_effect_elementor'          => array(
			'none'     => esc_html__( 'None', 'xts-theme' ),
			'zoom-out' => esc_html__( 'Zoom Out', 'xts-theme' ),
			'zoom-in'  => esc_html__( 'Zoom In', 'xts-theme' ),
			'parallax' => esc_html__( 'Parallax 3D', 'xts-theme' ),
		),

		'banner_carousel_element_hover_effect_elementor' => array(
			'none'     => esc_html__( 'None', 'xts-theme' ),
			'zoom-out' => esc_html__( 'Zoom Out', 'xts-theme' ),
			'zoom-in'  => esc_html__( 'Zoom In', 'xts-theme' ),
		),

		'image_element_hover_effect_elementor'           => array(
			'none'     => esc_html__( 'None', 'xts-theme' ),
			'zoom-out' => esc_html__( 'Zoom Out', 'xts-theme' ),
			'zoom-in'  => esc_html__( 'Zoom In', 'xts-theme' ),
		),

		'image_gallery_element_hover_effect_elementor'   => array(
			'none'     => esc_html__( 'None', 'xts-theme' ),
			'zoom-out' => esc_html__( 'Zoom Out', 'xts-theme' ),
			'zoom-in'  => esc_html__( 'Zoom In', 'xts-theme' ),
		),

		'hotspot_element_trigger_elementor'              => array(
			'hover' => esc_html__( 'Hover', 'xts-theme' ),
			'click' => esc_html__( 'Click', 'xts-theme' ),
		),

		'page_title_design'                              => array(
			'without'  => array(
				'name'  => esc_html__( 'Without', 'xts-theme' ),
				'value' => 'without',
			),
			'default'  => array(
				'name'  => esc_html__( 'Default', 'xts-theme' ),
				'value' => 'default',
			),
			'centered' => array(
				'name'  => esc_html__( 'Centered', 'xts-theme' ),
				'value' => 'centered',
			),
			'by-sides' => array(
				'name'  => esc_html__( 'By sides', 'xts-theme' ),
				'value' => 'by-sides',
			),
		),

		'page_title_design_metabox'                      => array(
			'inherit'  => array(
				'name'  => esc_html__( 'Inherit', 'xts-theme' ),
				'value' => 'inherit',
			),
			'without'  => array(
				'name'  => esc_html__( 'Without', 'xts-theme' ),
				'value' => 'without',
			),
			'default'  => array(
				'name'  => esc_html__( 'Default', 'xts-theme' ),
				'value' => 'default',
			),
			'centered' => array(
				'name'  => esc_html__( 'Centered', 'xts-theme' ),
				'value' => 'centered',
			),
			'by-sides' => array(
				'name'  => esc_html__( 'By sides', 'xts-theme' ),
				'value' => 'by-sides',
			),
		),

		'product_tabs_title_style'                       => array(
			'default'   => esc_html__( 'Default', 'xts-theme' ),
			'underline' => esc_html__( 'Underline', 'xts-theme' ),
		),

		'title_element_design_elementor'                 => array(
			'default'   => esc_html__( 'Default', 'xts-theme' ),
			'simple'    => esc_html__( 'Simple', 'xts-theme' ),
			'bordered'  => esc_html__( 'Bordered', 'xts-theme' ),
			'underline' => esc_html__( 'Underline', 'xts-theme' ),
			'image'     => esc_html__( 'With image', 'xts-theme' ),
		),

		'search_icon_style_header_builder'               => array(
			'icon'      => array(
				'value' => 'icon',
				'label' => esc_html__( 'Icon only', 'xts-theme' ),
			),
			'icon-text' => array(
				'value' => 'icon-text',
				'label' => esc_html__( 'Icon with text', 'xts-theme' ),
			),
			'text'      => array(
				'value' => 'text',
				'label' => esc_html__( 'Only text', 'xts-theme' ),
			),
		),

		'blog_design'                                    => array(
			'default' => array(
				'name'  => esc_html__( 'Default', 'xts-theme' ),
				'value' => 'default',
			),
		),

		'blog_design_elementor'                          => array(
			'inherit' => esc_html__( 'Inherit', 'xts-theme' ),
			'default' => esc_html__( 'Default', 'xts-theme' ),
		),

		'product_tabs_heading_design'                    => array(
			'default'  => esc_html__( 'Default', 'xts-theme' ),
			'by-sides' => esc_html__( 'By sides', 'xts-theme' ),
		),

		'banner_design_elementor'                        => array(
			'default' => esc_html__( 'Default', 'xts-theme' ),
			'mask'    => esc_html__( 'Mask', 'xts-theme' ),
		),

		'banner_subtitle_color_presets_elementor'        => array(
			'default'   => esc_html__( 'Default', 'xts-theme' ),
			'primary'   => esc_html__( 'Primary', 'xts-theme' ),
			'secondary' => esc_html__( 'Secondary', 'xts-theme' ),
			'white'     => esc_html__( 'White', 'xts-theme' ),
			'custom'    => esc_html__( 'Custom', 'xts-theme' ),
		),

		'button_style_elementor'                         => array(
			'default'  => array(
				'title' => esc_html__( 'Default', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/button/style/default.svg',
			),
			'bordered' => array(
				'title' => esc_html__( 'Bordered', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/button/style/bordered.svg',
			),
			'link'     => array(
				'title' => esc_html__( 'Link', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/button/style/link.svg',
			),
			'3d'       => array(
				'title' => esc_html__( '3D', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/button/style/3d.svg',
			),
		),

		'button_style_header_builder'                    => array(
			'default'  => array(
				'value' => 'default',
				'label' => esc_html__( 'Default', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/button/style/default.svg',
			),
			'bordered' => array(
				'value' => 'bordered',
				'label' => esc_html__( 'Bordered', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/button/style/bordered.svg',
			),
			'link'     => array(
				'value' => 'link',
				'label' => esc_html__( 'Link', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/button/style/link.svg',
			),
			'3d'       => array(
				'value' => '3d',
				'label' => esc_html__( '3D', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/button/style/3d.svg',
			),
		),

		'product_categories_design'                      => array(
			'default' => array(
				'name'  => esc_html__( 'Default', 'xts-theme' ),
				'value' => 'default',
			),
			'mask'    => array(
				'name'  => esc_html__( 'Mask', 'xts-theme' ),
				'value' => 'mask',
			),
			'subcat'  => array(
				'name'  => esc_html__( 'Mask with sub categories', 'xts-theme' ),
				'value' => 'subcat',
			),
		),

		'product_categories_design_elementor'            => array(
			'default' => esc_html__( 'Default', 'xts-theme' ),
			'mask'    => esc_html__( 'Mask', 'xts-theme' ),
			'subcat'  => esc_html__( 'Mask with sub categories', 'xts-theme' ),
		),

		'product_brands_design_elementor'                => array(
			'default' => esc_html__( 'Default', 'xts-theme' ),
			'bg'      => esc_html__( 'With background', 'xts-theme' ),
		),

		'product_loop_design'                            => array(
			'summary' => array(
				'name'  => esc_html__( 'Summary', 'xts-theme' ),
				'value' => 'summary',
			),
			'btn'     => array(
				'name'  => esc_html__( 'Bottom button', 'xts-theme' ),
				'value' => 'btn',
			),
			'img-btn' => array(
				'name'  => esc_html__( 'Button on image', 'xts-theme' ),
				'value' => 'img-btn',
			),
			'mask'    => array(
				'name'  => esc_html__( 'Content on image', 'xts-theme' ),
				'value' => 'mask',
			),
			'icons'   => array(
				'name'  => esc_html__( 'Icons on image', 'xts-theme' ),
				'value' => 'icons',
			),
		),

		'product_loop_design_elementor'                  => array(
			'inherit' => esc_html__( 'Inherit', 'xts-theme' ),
			'summary' => esc_html__( 'Summary', 'xts-theme' ),
			'small'   => esc_html__( 'Small', 'xts-theme' ),
			'btn'     => esc_html__( 'Bottom button', 'xts-theme' ),
			'img-btn' => esc_html__( 'Button on image', 'xts-theme' ),
			'mask'    => esc_html__( 'Content on image', 'xts-theme' ),
			'icons'   => esc_html__( 'Icons on image', 'xts-theme' ),
		),

		'menu_style_header_builder'                      => array(
			'default'   => array(
				'value' => 'default',
				'label' => esc_html__( 'Default', 'xts-theme' ),
			),
			'underline' => array(
				'value' => 'underline',
				'label' => esc_html__( 'Underline', 'xts-theme' ),
			),
			'separated' => array(
				'value' => 'separated',
				'label' => esc_html__( 'Separated', 'xts-theme' ),
			),
		),

		'menu_style_widget'                              => array(
			esc_html__( 'Default', 'xts-theme' )   => 'default',
			esc_html__( 'Underline', 'xts-theme' ) => 'underline',
			esc_html__( 'Separated', 'xts-theme' ) => 'separated',
		),

		'menu_style_elementor'                           => array(
			'default'   => esc_html__( 'Default', 'xts-theme' ),
			'underline' => esc_html__( 'Underline', 'xts-theme' ),
			'separated' => esc_html__( 'Separated', 'xts-theme' ),
		),

		'menu_orientation_widget'                        => array(
			esc_html__( 'Horizontal', 'xts-theme' ) => 'horizontal',
			esc_html__( 'Vertical', 'xts-theme' )   => 'vertical',
		),

		'menu_orientation_elementor'                     => array(
			'horizontal' => esc_html__( 'Horizontal', 'xts-theme' ),
			'vertical'   => esc_html__( 'Vertical', 'xts-theme' ),
		),

		'portfolio_design_elementor'                     => array(
			'inherit'  => esc_html__( 'Inherit', 'xts-theme' ),
			'default'  => esc_html__( 'Default', 'xts-theme' ),
			'parallax' => esc_html__( 'Parallax 3D', 'xts-theme' ),
		),

		'portfolio_design'                               => array(
			'default'  => array(
				'name'  => esc_html__( 'Default', 'xts-theme' ),
				'value' => 'default',
			),
			'parallax' => array(
				'name'  => esc_html__( 'Parallax', 'xts-theme' ),
				'value' => 'parallax',
			),
		),

		'social_buttons_style_elementor'                 => array(
			'default'     => array(
				'title' => esc_html__( 'Default', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/social-buttons/style/default.svg',
			),
			'simple'      => array(
				'title' => esc_html__( 'Simple', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/social-buttons/style/simple.svg',
			),
			'colored'     => array(
				'title' => esc_html__( 'Colored', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/social-buttons/style/colored.svg',
			),
			'colored-alt' => array(
				'title' => esc_html__( 'Colored alternative', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/social-buttons/style/colored-alt.svg',
			),
			'bordered'    => array(
				'title' => esc_html__( 'Bordered', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/social-buttons/style/bordered.svg',
			),
		),

		'social_buttons_style_widget'                    => array(
			esc_html__( 'Default', 'xts-theme' )  => 'default',
			esc_html__( 'Simple', 'xts-theme' )   => 'simple',
			esc_html__( 'Colored', 'xts-theme' )  => 'colored',
			esc_html__( 'Colored alternative', 'xts-theme' ) => 'colored-alt',
			esc_html__( 'Bordered', 'xts-theme' ) => 'bordered',
		),

		'social_buttons_style_header_builder'            => array(
			'default'     => array(
				'value' => 'default',
				'label' => esc_html__( 'Default', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/social-buttons/style/default.svg',
			),
			'simple'      => array(
				'value' => 'simple',
				'label' => esc_html__( 'Simple', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/social-buttons/style/simple.svg',
			),
			'colored'     => array(
				'value' => 'colored',
				'label' => esc_html__( 'Colored', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/social-buttons/style/colored.svg',
			),
			'colored-alt' => array(
				'value' => 'colored-alt',
				'label' => esc_html__( 'Colored alternative', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/social-buttons/style/colored-alt.svg',
			),
			'bordered'    => array(
				'value' => 'bordered',
				'label' => esc_html__( 'Bordered', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/social-buttons/style/bordered.svg',
			),
		),

		'advanced_typography'                            => array(
			'header_elements'                   => array(
				'title' => esc_html__( 'Header elements', 'xts-theme' ),
			),
			'header_element_label'              => array(
				'title'                => esc_html__( 'Header elements label', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'header-elements-label' ),
				'selector-color-hover' => xts_get_typography_selectors( 'header-elements-label__color-hover' ),
			),
			'header_text_element'               => array(
				'title'    => esc_html__( 'Header element text', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'header-text-element' ),
			),
			'main_navigation'                   => array(
				'title' => esc_html__( 'Main navigations', 'xts-theme' ),
			),
			'dropdown_menu'                     => array(
				'title'    => esc_html__( 'Menu dropdowns link', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'dropdown-menu' ),
			),
			'dropdown_mega_menu_first_level'    => array(
				'title'                => esc_html__( 'Mega menu first level links', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'dropdown-mega-menu-first-level' ),
				'selector-color-hover' => xts_get_typography_selectors( 'dropdown-mega-menu-first-level__color-hover' ),
			),
			'dropdown_mega_menu_second_level'   => array(
				'title'                => esc_html__( 'Mega menu second level links', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'dropdown-mega-menu-second-level' ),
				'selector-color-hover' => xts_get_typography_selectors( 'dropdown-mega-menu-second-level__color-hover' ),
			),
			'other_navigation'                  => array(
				'title' => esc_html__( 'Other navigations', 'xts-theme' ),
			),
			'secondary_nav'                     => array(
				'title'                => esc_html__( 'Secondary navigation', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'secondary-navigation' ),
				'selector-color-hover' => xts_get_typography_selectors( 'secondary-navigation__color-hover' ),
			),
			'browse-categories-title'           => array(
				'title'    => esc_html__( '"Browse categories" title', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'browse-categories-title' ),
			),
			'categories-navigation-links'       => array(
				'title'                => esc_html__( 'Categories navigation links', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'categories-navigation-links' ),
				'selector-color-hover' => xts_get_typography_selectors( 'categories-navigation-links__color-hover' ),
			),
			'my-account-header-links'           => array(
				'title'                => esc_html__( 'My account header links', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'my-account-header-links' ),
				'selector-color-hover' => xts_get_typography_selectors( 'my-account-header-links__color-hover' ),
			),
			'mobile_menu'                       => array(
				'title' => esc_html__( 'Mobile menu', 'xts-theme' ),
			),
			'mobile-menu-first-level'           => array(
				'title'                => esc_html__( 'Mobile menu first level', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'mobile-menu-first-level' ),
				'selector-color-hover' => xts_get_typography_selectors( 'mobile-menu-first-level__color-hover' ),
			),
			'mobile-menu-second-level'          => array(
				'title'                => esc_html__( 'Mobile menu second level', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'mobile-menu-second-level' ),
				'selector-color-hover' => xts_get_typography_selectors( 'mobile-menu-second-level__color-hover' ),
			),
			'page_heading'                      => array(
				'title' => esc_html__( 'Page heading', 'xts-theme' ),
			),
			'page-title'                        => array(
				'title'    => esc_html__( 'Page title', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'page-title' ),
			),
			'breadcrumbs-links'                 => array(
				'title'                => esc_html__( 'Breadcrumbs links', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'breadcrumbs-links' ),
				'selector-color-hover' => xts_get_typography_selectors( 'breadcrumbs-links__color-hover' ),
			),
			'products_and_categories'           => array(
				'title' => esc_html__( 'Products and categories', 'xts-theme' ),
			),
			'product-archive-title'             => array(
				'title'                => esc_html__( 'Product archive title', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'product-archive-title' ),
				'selector-color-hover' => xts_get_typography_selectors( 'product-archive-title__color-hover' ),
			),
			'product-archive-price'             => array(
				'title'    => esc_html__( 'Product archive price', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'product-archive-price' ),
			),
			'product-archive-old-price'         => array(
				'title'    => esc_html__( 'Product archive old price', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'product-archive-old-price' ),
			),
			'category-title'                    => array(
				'title'                => esc_html__( 'Category title', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'category-title' ),
				'selector-color-hover' => xts_get_typography_selectors( 'category-title__color-hover' ),
			),
			'category-products-count'           => array(
				'title'    => esc_html__( 'Category products count', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'category-products-count' ),
			),
			'single_product'                    => array(
				'title' => esc_html__( 'Single Product', 'xts-theme' ),
			),
			'single-product-title'              => array(
				'title'    => esc_html__( 'Single product title', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'single-product-title' ),
			),
			'single-product-price'              => array(
				'title'    => esc_html__( 'Single product price', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'single-product-price' ),
			),
			'single-product-old-price'          => array(
				'title'    => esc_html__( 'Single product old price', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'single-product-old-price' ),
			),
			'single-product-variable-price'     => array(
				'title'    => esc_html__( 'Single product variable price', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'single-product-variable-price' ),
			),
			'single-product-variable-old-price' => array(
				'title'    => esc_html__( 'Single product variable old price', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'single-product-variable-old-price' ),
			),
			'blog'                              => array(
				'title' => esc_html__( 'Blog', 'xts-theme' ),
			),
			'post-archive-title'                => array(
				'title'                => esc_html__( 'Post archive title', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'post-archive-title' ),
				'selector-color-hover' => xts_get_typography_selectors( 'post-archive-title__color-hover' ),
			),
			'post-carousel-title'               => array(
				'title'                => esc_html__( 'Post carousel title', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'post-carousel-title' ),
				'selector-color-hover' => xts_get_typography_selectors( 'post-carousel-title__color-hover' ),
			),
			'single-post-title'                 => array(
				'title'    => esc_html__( 'Single post title', 'xts-theme' ),
				'selector' => xts_get_typography_selectors( 'single-post-title' ),
			),
			'portfolio'                         => array(
				'title' => esc_html__( 'Portfolio', 'xts-theme' ),
			),
			'project-archive-title'             => array(
				'title'                => esc_html__( 'Project archive title', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'project-archive-title' ),
				'selector-color-hover' => xts_get_typography_selectors( 'project-archive-title__color-hover' ),
			),
			'project-carousel-title'            => array(
				'title'                => esc_html__( 'Project carousel title', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'project-carousel-title' ),
				'selector-color-hover' => xts_get_typography_selectors( 'project-carousel-title__color-hover' ),
			),
			'widgets'                           => array(
				'title' => esc_html__( 'Widgets', 'xts-theme' ),
			),
			'widget-entities-names'             => array(
				'title'                => esc_html__( 'Entities names in widget', 'xts-theme' ),
				'selector'             => xts_get_typography_selectors( 'widget-entities-names' ),
				'selector-color-hover' => xts_get_typography_selectors( 'widget-entities-names__color-hover' ),
			),
			'custom_selector'                   => array(
				'title' => esc_html__( 'Write your own selector', 'xts-theme' ),
			),
			'custom'                            => array(
				'title'    => esc_html__( 'Custom selector', 'xts-theme' ),
				'selector' => 'custom',
			),
		),
	)
);
