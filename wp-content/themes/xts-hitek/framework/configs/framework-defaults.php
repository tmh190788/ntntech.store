<?php
/**
 * Default values for theme settings dashboard options.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_framework_default_values_array',
	array(
		'product_loop_quantity_product_design_condition' => array( 'summary', 'btn' ),
		'hover_product_btn_actions_classes'              => 'xts-style-icon-bg',
		'hover_product_img_btn_actions_classes'          => 'xts-style-icon-bg',
		'hover_product_icons_actions_classes'            => 'xts-style-icon-bg',
		'hover_product_icons_alt_actions_classes'        => 'xts-style-icon-bg',
		'hover_product_summary_actions_classes'          => 'xts-style-icon-bg',
		'hover_product_mask_actions_classes'             => 'xts-style-icon',
		'site_width'                                     => '1200',
		'blog_columns'                                   => '2',
		'blog_excerpt_length'                            => '145',
		'copyrights_layout'                              => 'two_columns',
		'button_element_shape'                           => 'rectangle',
		'blog_single_related_posts_design'               => 'inherit',
		'footer_color_scheme'                            => 'light',
		'items_gap'                                      => 20,
		'portfolio_design'                               => 'default',
		'carousel_arrows_color_scheme'                   => 'dark',
		'carousel_arrows_vertical_position'              => 'sides',
		'shop_page_title_categories_menu_style'          => 'underline',
		'portfolio_filters_menu_style'                   => 'underline',
		'single_product_tabs_menu_style'                 => 'underline',
		'single_product_tabs_menu_gap'                   => 'm',
		'meta_post_author_args'                          => array(
			'avatar'      => true,
			'avatar_size' => 40,
			'avatar_link' => true,
			'label'       => true,
			'name'        => true,
		),
		'blog_single_related_posts'                      => '1',
		'portfolio_social_buttons_args'                  => array(
			'size'         => 's',
			'color_scheme' => 'light',
		),
		'post_social_buttons_args'                       => array(
			'size'         => 's',
			'color_scheme' => 'light',
		),
		'blog_single_related_posts_per_row'              => 2,
		'portfolio_single_related_projects_per_row'      => 3,
		'single_post_social_buttons_args'                => array(
			'style'                 => 'colored',
			'align'                 => 'center',
			'size'                  => 'm',
			'wrapper_extra_classes' => 'xts-single-post-social',
		),
		'tooltip_top_selector'                           => '.xts-prod-design-icons .xts-product-actions > div, .xts-hint, .xts-variation-swatch.xts-with-bg, .xts-loop-swatch.xts-with-bg, .xts-sticky-atc .xts-action-btn, .xts-filter-swatch.xts-with-bg.xts-with-tooltip, [data-xts-tooltip], .xts-single-product-actions .xts-action-btn.xts-style-icon-border, .xts-single-product-actions .xts-action-btn.xts-style-icon',
		'tooltip_left_selector'                          => '.xts-prod-design-summary .xts-product-actions > div, .xts-prod-design-btn .xts-product-actions > div, .xts-prod-design-img-btn .xts-product-actions > div, .xts-prod-design-mask .xts-product-actions > div',
		'menu_animation_offset'                          => 0,
		'slider_distortion_effect'                       => 'sliderWithWave',
	)
);
