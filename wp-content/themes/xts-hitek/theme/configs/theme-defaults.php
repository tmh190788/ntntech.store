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
	'xts_theme_default_values_array',
	array(
		'single_post_social_buttons_args' => array(
			'style'                 => 'bordered',
			'align'                 => 'center',
			'size'                  => 'm',
			'wrapper_extra_classes' => 'xts-single-post-social',
		),
		'tooltip_left_selector'           => '.xts-prod-design-summary .xts-product-actions > div, .xts-prod-design-btn .xts-product-actions > div, .xts-prod-design-img-btn .xts-product-actions > div, .xts-prod-design-mask .xts-product-actions > div, .xts-prod-design-icons-alt .xts-product-actions > div',
		'tooltip_top_selector'            => '.xts-prod-design-icons .xts-product-actions > div, .xts-hint, .xts-variation-swatch.xts-with-bg, .xts-loop-swatch.xts-with-bg, .xts-sticky-atc .xts-action-btn, .xts-filter-swatch.xts-with-bg.xts-with-tooltip, [data-xts-tooltip], .xts-prod-design-summary-alt .xts-product-price-actions > div',
		// Theme settings.
		'page_title_bg'                   => array(
			'color' => '#f9f9f9',
			'url'        => '',
			'id'         => '',
			'repeat'     => '',
			'size'       => '',
			'attachment' => '',
			'position'   => '',
			'position_x' => '0',
			'position_y' => '0',
			'css_output' => '1',
		),
		'page_title_size'                 => 's',
		'blog_single_related_posts'       => '0',
		'blog_spacing'                    => '40',
		'blog_columns'                    => '1',
		'blog_excerpt_length'             => '180',
		'content_typography'              => array(
			0 => array(
				'custom'         => '',
				'google'         => '1',
				'font-family'    => 'Karla',
				'font-weight'    => '',
				'font-style'     => '',
				'font-subset'    => '',
				'text-transform' => '',
				'font-size'      => '',
				'tablet'         => array(
					'font-size'   => '',
					'line-height' => '',
				),
				'mobile'         => array(
					'font-size'   => '',
					'line-height' => '',
				),
				'line-height'    => '',
				'color'          => '',
			),
		),
		'entities_typography'             => array(
			0 => array(
				'custom'         => '',
				'google'         => '1',
				'font-family'    => 'Asap',
				'font-weight'    => '',
				'font-style'     => '',
				'font-subset'    => '',
				'text-transform' => '',
				'line-height'    => '',
				'tablet'         => array(
					'line-height' => '',
				),
				'mobile'         => array(
					'line-height' => '',
				),
				'color'          => '#333333',
				'hover'          => array(
					'color' => '#439665',
				),
			),
		),
		'footer_bg'                       => array(
			'color'      => '#282828',
			'url'        => '',
			'id'         => '',
			'repeat'     => '',
			'size'       => '',
			'attachment' => '',
			'position'   => '',
			'position_x' => '0',
			'position_y' => '0',
			'css_output' => '1',
		),
		'footer_color_scheme'             => 'light',
		'header_typography'               => array(
			0 => array(
				'custom'         => '',
				'google'         => '1',
				'font-family'    => 'Asap',
				'font-weight'    => '500',
				'font-style'     => '',
				'font-subset'    => '',
				'text-transform' => '',
				'font-size'      => '',
				'tablet'         => array(
					'font-size'   => '',
					'line-height' => '',
				),
				'mobile'         => array(
					'font-size'   => '',
					'line-height' => '',
				),
				'line-height'    => '',
				'color'          => '',
				'hover'          => array(
					'color' => '',
				),
				'active'         => array(
					'color' => '',
				),
			),
		),
		'primary_color'                   => array(
			'idle'       => '#439665',
			'css_output' => '1',
		),
		'secondary_color'                 => array(
			'idle'       => '#333333',
			'css_output' => '1',
		),
		'site_width'                      => '1400',
		'title_typography'                => array(
			0 => array(
				'custom'         => '',
				'google'         => '1',
				'font-family'    => 'Asap',
				'font-weight'    => '',
				'font-style'     => '',
				'font-subset'    => '',
				'text-transform' => '',
				'line-height'    => '',
				'tablet'         => array(
					'line-height' => '',
				),
				'mobile'         => array(
					'line-height' => '',
				),
				'color'          => '',
			),
		),
		'widget_title_typography'         => array(
			0 => array(
				'custom'         => '',
				'google'         => '1',
				'font-family'    => 'Asap',
				'font-weight'    => '',
				'font-style'     => '',
				'font-subset'    => '',
				'text-transform' => '',
				'font-size'      => '',
				'tablet'         => array(
					'font-size'   => '',
					'line-height' => '',
				),
				'mobile'         => array(
					'font-size'   => '',
					'line-height' => '',
				),
				'line-height'    => '',
				'color'          => '',
			),
		),
	)
);
