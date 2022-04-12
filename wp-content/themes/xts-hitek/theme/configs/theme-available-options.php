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
	'xts_theme_available_options_array',
	array(
		'items_gap_elementor'                           => array(
			40 => esc_html__( '40 px', 'xts-theme' ),
		),

		'items_gap'                                     => array(
			40 => array(
				'name'  => 40,
				'value' => 40,
			),
		),

		'product_tabs_heading_design'           => array(
			'by-sides-2' => esc_html__( 'By sides bordered', 'xts-theme' ),
		),

		'product_loop_design'                   => array(
			'summary-alt' => array(
				'name'  => esc_html__( 'Summary alternative', 'xts-theme' ),
				'value' => 'summary-alt',
			),
			'icons-alt'   => array(
				'name'  => esc_html__( 'Icons on image alternative', 'xts-theme' ),
				'value' => 'icons-alt',
			),
		),

		'product_loop_design_elementor'         => array(
			'summary-alt' => esc_html__( 'Summary alternative', 'xts-theme' ),
			'icons-alt'   => esc_html__( 'Icons on image alternative', 'xts-theme' ),
		),

		'button_style_elementor'                => array(
			'link-3' => array(
				'title' => esc_html__( 'Link without border', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/button/style/link-3.svg',
			),
		),

		'button_style_header_builder'           => array(
			'link-3' => array(
				'value' => 'link-3',
				'label' => esc_html__( 'Link without border', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/button/style/link.svg',
			),
		),

		'search_style_header_builder'           => array(
			'icon-alt-2' => array(
				'title' => esc_html__( 'Button left', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/elementor/search/form/icon-alt-2.svg',
			),
		),

		'search_style_widget'                   => array(
			esc_html__( 'Button left', 'xts-theme' ) => 'icon-alt-2',
		),

		'search_style_elementor'                => array(
			'icon-alt-2' => array(
				'value' => 'icon-alt-2',
				'label' => esc_html__( 'Button left', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/search/form/icon-alt-2.svg',
			),
		),

		'cart_widget_type_header_builder'       => array(
			'add_after' => 'dropdown',
			'top'       => array(
				'value' => 'top',
				'label' => esc_html__( 'Position top', 'xts-theme' ),
			),
		),

		'my_account_widget_type_header_builder' => array(
			'top' => array(
				'value' => 'top',
				'label' => esc_html__( 'Position top', 'xts-theme' ),
			),
		),

		'cart_design_header_builder'            => array(
			'round-bordered' => array(
				'value' => 'round-bordered',
				'label' => esc_html__( 'Round Bordered', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/count-round-bordered.svg',
			),
			'round'          => array(
				'value' => 'round',
				'label' => esc_html__( 'Round', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/cart/design/count-round.svg',
			),
		),

		'wishlist_design_header_builder'        => array(
			'round-bordered' => array(
				'value' => 'round-bordered',
				'label' => esc_html__( 'Round Bordered', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/wishlist/design/count-round-bordered.svg',
			),
			'round'          => array(
				'value' => 'round',
				'label' => esc_html__( 'Round', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/wishlist/design/count-round.svg',
			),
		),

		'compare_design_header_builder'         => array(
			'round-bordered' => array(
				'value' => 'round-bordered',
				'label' => esc_html__( 'Round Bordered', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/compare/design/count-round-bordered.svg',
			),
			'round'          => array(
				'value' => 'round',
				'label' => esc_html__( 'Round', 'xts-theme' ),
				'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/compare/design/count-round.svg',
			),
		),
	)
);
