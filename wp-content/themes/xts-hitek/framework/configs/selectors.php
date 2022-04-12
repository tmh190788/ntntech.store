<?php
/**
 * Selectors for theme settings dashboard options.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_selectors_configs_array',
	array(
		'primary-color'                    => '.xts-textcolor-primary, .xts-scheme-light .xts-textcolor-primary, .xts-scheme-dark .xts-textcolor-primary, .xts-mobile-menu > li.current-menu-item > a, .xts-mobile-menu ul li.current-menu-item > a, .xts-box-icon, .xts-button.xts-color-primary.xts-style-bordered, .xts-plan-pricing, .xts-scheme-hover-dark:hover .xts-plan-pricing, .xts-plan-icon, .xts-404-title, .xts-no-results-title',
		'primary-background'               => '.flickity-page-dots .dot:before, .xts-menu-label.xts-color-primary, .xts-menu.xts-style-underline .xts-menu-text:after, .searchform .searchsubmit, .comment-form input[type="submit"], .xts-post-categories > a, .xts-pagination > span, .xts-post-reply .replies-count, .xts-tags-list > a:hover:before, .xts-project-categories > span, .xts-portfolio-filters a:after, .widget_calendar #today, .xts-button.xts-color-primary, .xts-button.xts-color-primary.xts-style-bordered:hover, .xts-section-title.xts-design-simple:after, .xts-social-buttons.xts-style-simple a:hover, .xts-plan-label.xts-color-primary, .xts-countdown-timer.xts-bg-color-primary .xts-countdown-item',
		'primary-border-color'             => 'blockquote, .xts-tags-list > a:hover, .widget_tag_cloud a:hover, .xts-button.xts-color-primary.xts-style-bordered, .xts-button.xts-color-primary.xts-style-link, .xts-button.xts-color-primary.xts-style-link:hover, .xts-section-title.xts-design-underline .xts-section-title-text',
		'secondary-color'                  => '.xts-textcolor-secondary, .xts-scheme-light .xts-textcolor-secondary, .xts-scheme-dark .xts-textcolor-secondary, .xts-button.xts-color-secondary.xts-style-bordered',
		'secondary-background'             => '.xts-menu-label.xts-color-secondary, .xts-button.xts-color-secondary, .xts-button.xts-color-secondary.xts-style-bordered:hover, .xts-plan-label.xts-color-secondary, .xts-countdown-timer.xts-bg-color-secondary .xts-countdown-item',
		'secondary-border-color'           => '.xts-button.xts-color-secondary.xts-style-bordered, .xts-button.xts-color-secondary.xts-style-link, .xts-button.xts-color-secondary.xts-style-link:hover',
		'content-font'                        => 'body',
		'content-font__font-family'           => '.xts-textfont-body',
		'content-font__font-size'             => '',
		'content-font__color'                 => '',
		'title-font'                       => 'h1, h2, h3, h4, h5, h6, .title, legend, th',
		'title-font__font-family'          => '.xts-textfont-title',
		'title-font__color'                => '',
		'entities-title-font'              => '.autocomplete-suggestion .suggestion-title, .xts-post-title, .widget_recent_comments li > a, .widget_recent_entries a, .widget_rss li > a',
		'entities-title-font__font-family' => '.xts-project-title',
		'entities-title-font__color'       => '',
		'entities-title-font__color-hover' => '.xts-post-title:hover a, .widget_recent_comments li > a:hover, .widget_recent_entries a:hover, .widget_rss li > a:hover',
		'header-font'                      => '.xts-menu > li > a',
		'header-font__font-family'         => '.xts-header-element .xts-header-el-label',
		'header-font__font-size'           => '.xts-header-element .xts-header-el-label',
		'header-font__color'               => '.xts-header-element > a',
		'header-font__color-hover'         => '.xts-header-element:hover > a, .xts-menu > li:hover > a',
		'secondary-font'                   => '',
		'alternative-font__font-family'      => '.xts-textfont-secondary',
	)
);
