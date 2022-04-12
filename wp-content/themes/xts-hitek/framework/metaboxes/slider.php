<?php
/**
 * Slider metaboxes
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Metaboxes;

/**
 * Slider
 */
$slider_metaboxes = Metaboxes::add_metabox(
	array(
		'id'         => 'xts_slider_metabox',
		'title'      => esc_html__( 'Slider metabox', 'xts-theme' ),
		'object'     => 'term',
		'taxonomies' => array( 'xts_slider' ),
	)
);

/**
 * Layout section
 */
$slider_metaboxes->add_section(
	array(
		'id'       => 'layout',
		'name'     => esc_html__( 'Layout', 'xts-theme' ),
		'icon'     => 'xf-layout',
		'priority' => 10,
	)
);

$stretch_slider_options = array(
	'disabled'        => array(
		'name'  => esc_html__( 'Disabled', 'xts-theme' ),
		'value' => 'disabled',
	),
	'stretch'         => array(
		'name'  => esc_html__( 'Stretch slider', 'xts-theme' ),
		'value' => 'stretch',
	),
	'stretch-content' => array(
		'name'  => esc_html__( 'Stretch slider and content', 'xts-theme' ),
		'value' => 'stretch-content',
	),
);

$slider_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Stretch slider', 'xts-theme' ),
		'id'       => 'stretch_slider',
		'type'     => 'select',
		'section'  => 'layout',
		'options'  => $stretch_slider_options,
		'default'  => 'slide',
		'priority' => 10,
	)
);

$slider_metaboxes->add_field(
	array(
		'id'       => 'full_height',
		'name'     => esc_html__( 'Full height', 'xts-theme' ),
		'type'     => 'switcher',
		'section'  => 'layout',
		'default'  => '0',
		'priority' => 20,
	)
);

$slider_metaboxes->add_field(
	array(
		'id'                  => 'height',
		'name'                => esc_html__( 'Height on desktop', 'xts-theme' ),
		'description'         => esc_html__( 'Set your value in pixels.', 'xts-theme' ),
		'type'                => 'range',
		'section'             => 'layout',
		'default'             => 500,
		'min'                 => 100,
		'max'                 => 1200,
		'step'                => 5,
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'desktop_only'        => true,
		'requires'            => array(
			array(
				'key'     => 'full_height',
				'compare' => 'equals',
				'value'   => '0',
			),
		),
		'priority'            => 30,
	)
);

$slider_metaboxes->add_field(
	array(
		'id'                  => 'height_tablet',
		'name'                => esc_html__( 'Height on tablet', 'xts-theme' ),
		'description'         => esc_html__( 'Set your value in pixels.', 'xts-theme' ),
		'type'                => 'range',
		'section'             => 'layout',
		'default'             => 500,
		'min'                 => 100,
		'max'                 => 1200,
		'step'                => 5,
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'tablet_only'         => true,
		'requires'            => array(
			array(
				'key'     => 'full_height',
				'compare' => 'equals',
				'value'   => '0',
			),
		),
		'priority'            => 40,
	)
);

$slider_metaboxes->add_field(
	array(
		'id'                  => 'height_mobile',
		'name'                => esc_html__( 'Height on mobile', 'xts-theme' ),
		'description'         => esc_html__( 'Set your value in pixels.', 'xts-theme' ),
		'type'                => 'range',
		'section'             => 'layout',
		'default'             => 500,
		'min'                 => 100,
		'max'                 => 1200,
		'step'                => 5,
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'mobile_only'         => true,
		'requires'            => array(
			array(
				'key'     => 'full_height',
				'compare' => 'equals',
				'value'   => '0',
			),
		),
		'priority'            => 50,
	)
);

/**
 * Navigation section
 */
$slider_metaboxes->add_section(
	array(
		'id'       => 'navigation',
		'name'     => esc_html__( 'Navigation', 'xts-theme' ),
		'icon'     => 'xf-navigation',
		'priority' => 20,
	)
);

$slider_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Arrows style', 'xts-theme' ),
		'id'       => 'arrows_style',
		'type'     => 'buttons',
		'section'  => 'navigation',
		'options'  => xts_get_available_options( 'slider_arrows_style' ),
		'default'  => 'simple',
		'priority' => 10,
		'class'    => 'xts-slider-nav-control xts-col-6',
	)
);

$slider_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Dots style', 'xts-theme' ),
		'id'       => 'dots_style',
		'type'     => 'buttons',
		'section'  => 'navigation',
		'options'  => xts_get_available_options( 'slider_dots_style' ),
		'default'  => 'default',
		'priority' => 20,
		'class'    => 'xts-slider-nav-control xts-col-6',
	)
);

$slider_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Arrows background shape', 'xts-theme' ),
		'id'       => 'arrows_shape',
		'type'     => 'buttons',
		'section'  => 'navigation',
		'options'  => array(
			'square'  => array(
				'name'  => esc_html__( 'Square', 'xts-theme' ),
				'value' => 'square',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/arrow/square-bg.svg',
			),
			'round'   => array(
				'name'  => esc_html__( 'Round', 'xts-theme' ),
				'value' => 'round',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/arrow/round-bg.svg',
			),
			'rounded' => array(
				'name'  => esc_html__( 'Rounded', 'xts-theme' ),
				'value' => 'rounded',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/arrow/rounded-bg.svg',
			),
		),
		'default'  => 'square',
		'priority' => 30,
		'requires' => array(
			array(
				'key'     => 'arrows_style',
				'compare' => 'equals',
				'value'   => 'bg',
			),
		),
		'class'    => 'xts-slider-nav-control',
	)
);

if ( xts_get_available_options( 'slider_arrows_vertical_position' ) ) {
	$slider_metaboxes->add_field(
		array(
			'name'     => esc_html__( 'Arrows position', 'xts-theme' ),
			'id'       => 'arrows_vertical_position',
			'type'     => 'select',
			'section'  => 'navigation',
			'options'  => xts_get_available_options( 'slider_arrows_vertical_position' ),
			'default'  => 'sides',
			'priority' => 31,
		)
	);
}

$slider_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Arrows color scheme', 'xts-theme' ),
		'id'       => 'arrows_color_scheme',
		'type'     => 'buttons',
		'section'  => 'navigation',
		'options'  => array(
			'light' => array(
				'name'  => esc_html__( 'Light', 'xts-theme' ),
				'value' => 'light',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/nav-color-scheme/arrows-light.svg',
			),
			'dark'  => array(
				'name'  => esc_html__( 'Dark', 'xts-theme' ),
				'value' => 'dark',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/nav-color-scheme/arrows-dark.svg',
			),
		),
		'default'  => 'dark',
		'priority' => 40,
		'class'    => 'xts-slider-nav-control xts-col-6',
	)
);

$slider_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Dots color scheme', 'xts-theme' ),
		'id'       => 'dots_color_scheme',
		'type'     => 'buttons',
		'section'  => 'navigation',
		'options'  => array(
			'light' => array(
				'name'  => esc_html__( 'Light', 'xts-theme' ),
				'value' => 'light',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/nav-color-scheme/dots-light.svg',
			),
			'dark'  => array(
				'name'  => esc_html__( 'Dark', 'xts-theme' ),
				'value' => 'dark',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slider/nav-color-scheme/dots-dark.svg',
			),
		),
		'default'  => 'dark',
		'priority' => 50,
		'class'    => 'xts-slider-nav-control xts-col-6',
	)
);

/**
 * Extra section
 */
$slider_metaboxes->add_section(
	array(
		'id'       => 'extra',
		'name'     => esc_html__( 'Extra', 'xts-theme' ),
		'icon'     => 'xf-miscellaneous',
		'priority' => 30,
	)
);

$slider_metaboxes->add_field(
	array(
		'name'        => esc_html__( 'Enable autoplay', 'xts-theme' ),
		'description' => esc_html__( 'Rotate slider images automatically.', 'xts-theme' ),
		'id'          => 'autoplay',
		'type'        => 'switcher',
		'section'     => 'extra',
		'priority'    => 10,
	)
);

$slider_metaboxes->add_field(
	array(
		'id'       => 'autoplay_speed',
		'name'     => esc_html__( 'Autoplay interval (ms)', 'xts-theme' ),
		'type'     => 'range',
		'min'      => '1000',
		'max'      => '30000',
		'step'     => '100',
		'default'  => '9000',
		'section'  => 'extra',
		'priority' => 11,
		'requires' => array(
			array(
				'key'     => 'autoplay',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
	)
);

$slider_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Slide change animation', 'xts-theme' ),
		'id'       => 'animation',
		'type'     => 'select',
		'section'  => 'extra',
		'options'  => xts_get_available_options( 'slide_change_animation' ),
		'default'  => 'slide',
		'priority' => 20,
	)
);

/**
 * Slide
 */
$slide_metaboxes = Metaboxes::add_metabox(
	array(
		'id'         => 'xts_slides_metabox',
		'title'      => esc_html__( 'Slides metabox', 'xts-theme' ),
		'post_types' => array( 'xts-slide' ),
	)
);

/**
 * Image section
 */
$slide_metaboxes->add_section(
	array(
		'id'       => 'background',
		'name'     => esc_html__( 'Background', 'xts-theme' ),
		'icon'     => 'xf-background',
		'priority' => 10,
	)
);

$slide_metaboxes->add_field(
	array(
		'name'                => esc_html__( 'Background', 'xts-theme' ),
		'id'                  => 'background',
		'type'                => 'background',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'desktop_only'        => true,
		'section'             => 'background',
		'default'             => array(
			'repeat'   => 'no-repeat',
			'position' => 'center center',
			'size'     => 'cover',
		),
		'priority'            => 10,
	)
);

$slide_metaboxes->add_field(
	array(
		'name'                => esc_html__( 'Background (tablet)', 'xts-theme' ),
		'id'                  => 'background_tablet',
		'type'                => 'background',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'tablet_only'         => true,
		'section'             => 'background',
		'priority'            => 20,
	)
);

$slide_metaboxes->add_field(
	array(
		'name'                => esc_html__( 'Background (mobile)', 'xts-theme' ),
		'id'                  => 'background_mobile',
		'type'                => 'background',
		'responsive'          => true,
		'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
		'mobile_only'         => true,
		'section'             => 'background',
		'priority'            => 30,
	)
);

$slide_metaboxes->add_field(
	array(
		'id'       => 'video_source',
		'name'     => esc_html__( 'Video source', 'xts-theme' ),
		'type'     => 'buttons',
		'section'  => 'background',
		'options'  => array(
			'mp4'     => array(
				'name'  => esc_html__( 'MP4', 'xts-theme' ),
				'value' => 'mp4',
			),
			'youtube' => array(
				'name'  => esc_html__( 'YouTube', 'xts-theme' ),
				'value' => 'youtube',
			),
			'vimeo'   => array(
				'name'  => esc_html__( 'Vimeo', 'xts-theme' ),
				'value' => 'vimeo',
			),
		),
		'default'  => 'mp4',
		'priority' => 40,
	)
);

$slide_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Video MP4', 'xts-theme' ),
		'id'       => 'slide_video_mp4',
		'type'     => 'upload',
		'section'  => 'background',
		'requires' => array(
			array(
				'key'     => 'video_source',
				'compare' => 'equals',
				'value'   => 'mp4',
			),
		),
		'priority' => 50,
	)
);

if ( apply_filters( 'xts_video_ogg_webm_formats', false ) ) {
	$slide_metaboxes->add_field(
		array(
			'name'     => esc_html__( 'Video WEBM', 'xts-theme' ),
			'id'       => 'slide_video_webm',
			'type'     => 'upload',
			'section'  => 'background',
			'priority' => 60,
		)
	);

	$slide_metaboxes->add_field(
		array(
			'name'     => esc_html__( 'Video OGG', 'xts-theme' ),
			'id'       => 'slide_video_ogg',
			'type'     => 'upload',
			'section'  => 'background',
			'priority' => 70,
		)
	);
}

$slide_metaboxes->add_field(
	array(
		'name'        => esc_html__( 'Video YouTube', 'xts-theme' ),
		'description' => esc_html__( 'Example: https://youtu.be/LXb3EKWsInQ', 'xts-theme' ),
		'id'          => 'slide_video_youtube',
		'type'        => 'text_input',
		'section'     => 'background',
		'requires'    => array(
			array(
				'key'     => 'video_source',
				'compare' => 'equals',
				'value'   => 'youtube',
			),
		),
		'priority'    => 80,
	)
);

$slide_metaboxes->add_field(
	array(
		'name'        => esc_html__( 'Video Vimeo', 'xts-theme' ),
		'description' => esc_html__( 'Example: https://vimeo.com/259400046', 'xts-theme' ),
		'id'          => 'slide_video_vimeo',
		'type'        => 'text_input',
		'section'     => 'background',
		'requires'    => array(
			array(
				'key'     => 'video_source',
				'compare' => 'equals',
				'value'   => 'vimeo',
			),
		),
		'priority'    => 90,
	)
);

$slide_metaboxes->add_field(
	array(
		'id'       => 'overlay_mask',
		'name'     => esc_html__( 'Overlay mask', 'xts-theme' ),
		'type'     => 'buttons',
		'section'  => 'background',
		'options'  => array(
			'without' => array(
				'name'  => esc_html__( 'Without', 'xts-theme' ),
				'value' => 'without',
			),
			'dotted'  => array(
				'name'  => esc_html__( 'Dotted overlay', 'xts-theme' ),
				'value' => 'dotted',
			),
			'color'   => array(
				'name'  => esc_html__( 'Color', 'xts-theme' ),
				'value' => 'color',
			),
		),
		'default'  => 'without',
		'priority' => 100,
	)
);

$slide_metaboxes->add_field(
	array(
		'id'       => 'overlay_color',
		'name'     => esc_html__( 'Overlay color', 'xts-theme' ),
		'section'  => 'background',
		'type'     => 'color',
		'requires' => array(
			array(
				'key'     => 'overlay_mask',
				'compare' => 'equals',
				'value'   => 'color',
			),
		),
		'priority' => 110,
	)
);

$slide_metaboxes->add_field(
	array(
		'id'       => 'dotted_overlay_style',
		'name'     => esc_html__( 'Dotted overlay style', 'xts-theme' ),
		'type'     => 'buttons',
		'section'  => 'background',
		'options'  => array(
			'light' => array(
				'name'  => esc_html__( 'Light', 'xts-theme' ),
				'value' => 'light',
			),
			'dark'  => array(
				'name'  => esc_html__( 'Dark', 'xts-theme' ),
				'value' => 'dark',
			),
		),
		'requires' => array(
			array(
				'key'     => 'overlay_mask',
				'compare' => 'equals',
				'value'   => 'dotted',
			),
		),
		'default'  => 'dark',
		'priority' => 120,
	)
);

/**
 * Content section
 */
$slide_metaboxes->add_section(
	array(
		'id'       => 'content',
		'name'     => esc_html__( 'Content', 'xts-theme' ),
		'icon'     => 'xf-content',
		'priority' => 20,
	)
);

$slide_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Vertical content align', 'xts-theme' ),
		'id'       => 'vertical_align',
		'type'     => 'buttons',
		'section'  => 'content',
		'options'  => array(
			'start'  => array(
				'name'  => esc_html__( 'Start', 'xts-theme' ),
				'value' => 'start',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slide/content/vertical-position/top.svg',
			),
			'center' => array(
				'name'  => esc_html__( 'Center', 'xts-theme' ),
				'value' => 'center',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slide/content/vertical-position/middle.svg',
			),
			'end'    => array(
				'name'  => esc_html__( 'End', 'xts-theme' ),
				'value' => 'end',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slide/content/vertical-position/bottom.svg',
			),
		),
		'default'  => 'center',
		'priority' => 10,
		'class'    => 'xts-content-align',
	)
);

$slide_metaboxes->add_field(
	array(
		'name'     => esc_html__( 'Horizontal content align', 'xts-theme' ),
		'id'       => 'horizontal_align',
		'type'     => 'buttons',
		'section'  => 'content',
		'options'  => array(
			'start'  => array(
				'name'  => esc_html__( 'Start', 'xts-theme' ),
				'value' => 'start',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slide/content/horizontal-position/left.svg',
			),
			'center' => array(
				'name'  => esc_html__( 'Center', 'xts-theme' ),
				'value' => 'center',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slide/content/horizontal-position/center.svg',
			),
			'end'    => array(
				'name'  => esc_html__( 'End', 'xts-theme' ),
				'value' => 'end',
				'image' => XTS_ASSETS_IMAGES_URL . '/metaboxes/slide/content/horizontal-position/right.svg',
			),
		),
		'default'  => 'center',
		'priority' => 20,
		'class'    => 'xts-content-align',
	)
);

$slide_metaboxes->add_field(
	array(
		'name'        => esc_html__( 'Content without space', 'xts-theme' ),
		'description' => esc_html__( 'The content block will not have any paddings', 'xts-theme' ),
		'id'          => 'content_without_padding',
		'type'        => 'switcher',
		'section'     => 'content',
		'priority'    => 30,
	)
);

$slide_metaboxes->add_field(
	array(
		'name'        => esc_html__( 'Full width content', 'xts-theme' ),
		'description' => esc_html__( 'Takes the slider\'s width', 'xts-theme' ),
		'id'          => 'content_full_width',
		'type'        => 'switcher',
		'section'     => 'content',
		'default'     => '1',
		'priority'    => 40,
	)
);

$slide_metaboxes->add_field(
	array(
		'id'          => 'content_width',
		'name'        => esc_html__( 'Content width [on desktop]', 'xts-theme' ),
		'description' => esc_html__( 'Set your value in pixels.', 'xts-theme' ),
		'type'        => 'range',
		'section'     => 'content',
		'default'     => 1200,
		'min'         => 100,
		'max'         => 1920,
		'step'        => 5,
		'requires'    => array(
			array(
				'key'     => 'content_full_width',
				'compare' => 'equals',
				'value'   => '0',
			),
		),
		'priority'    => 50,
		'class'       => 'xts-col-4 xts-option-icon xts-option-icon-desktop',
	)
);

$slide_metaboxes->add_field(
	array(
		'id'          => 'content_width_tablet',
		'name'        => esc_html__( 'Content width [on tablets]', 'xts-theme' ),
		'description' => esc_html__( 'Set your value in pixels.', 'xts-theme' ),
		'type'        => 'range',
		'section'     => 'content',
		'default'     => 1025,
		'min'         => 100,
		'max'         => 1025,
		'step'        => 5,
		'requires'    => array(
			array(
				'key'     => 'content_full_width',
				'compare' => 'equals',
				'value'   => '0',
			),
		),
		'priority'    => 60,
		'class'       => 'xts-col-4 xts-option-icon xts-option-icon-tablet',
	)
);

$slide_metaboxes->add_field(
	array(
		'id'          => 'content_width_mobile',
		'name'        => esc_html__( 'Content width [on mobile]', 'xts-theme' ),
		'description' => esc_html__( 'Set your value in pixels.', 'xts-theme' ),
		'type'        => 'range',
		'section'     => 'content',
		'default'     => 770,
		'min'         => 50,
		'max'         => 770,
		'step'        => 5,
		'requires'    => array(
			array(
				'key'     => 'content_full_width',
				'compare' => 'equals',
				'value'   => '0',
			),
		),
		'priority'    => 70,
		'class'       => 'xts-col-4 xts-option-icon xts-option-icon-mobile',
	)
);
