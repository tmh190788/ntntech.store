<?php
/**
 * Performance options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

/**
 * Performance.
 */

/**
 * CSS.
 */
Options::add_field(
	array(
		'id'          => 'minified_css',
		'type'        => 'switcher',
		'section'     => 'css_performance_section',
		'name'        => esc_html__( 'Include minified CSS', 'xts-theme' ),
		'description' => esc_html__( 'Minified version of style.css file will be loaded (style.min.css).', 'xts-theme' ),
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'elementor_optimized_css',
		'type'        => 'switcher',
		'section'     => 'css_performance_section',
		'name'        => esc_html__( 'Load Elementor optimized CSS', 'xts-theme' ),
		'description' => esc_html__( 'Load only theme-required styles for Elementor. Don\'t use it if you are using most of the standard Elementor\'s widgets.', 'xts-theme' ),
		'default'     => '0',
		'class'       => 'xts-col-6',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'gutenberg_css',
		'type'        => 'switcher',
		'section'     => 'css_performance_section',
		'name'        => esc_html__( 'Gutenberg styles', 'xts-theme' ),
		'description' => esc_html__( 'If you are not using Gutenberg elements you will not need these files to be loaded.', 'xts-theme' ),
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'       => 'always_use_elementor_font_awesome',
		'type'     => 'switcher',
		'section'  => 'css_performance_section',
		'name'     => esc_html__( 'Always load Font Awesome library', 'xts-theme' ),
		'default'  => '0',
		'class'    => 'xts-col-6',
		'priority' => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'google_font_display',
		'name'        => esc_html__( '"font-display" for Google Fonts', 'xts-theme' ),
		'description' => 'You can specify "font-display" property for fonts loaded from Google. Read more information <a href="https://developers.google.com/web/updates/2016/02/font-display">here</a>',
		'type'        => 'select',
		'section'     => 'css_performance_section',
		'options'     => array(
			'disabled' => array(
				'name'  => esc_html__( 'Disabled', 'xts-theme' ),
				'value' => 'disabled',
			),
			'block'    => array(
				'name'  => esc_html__( 'Block', 'xts-theme' ),
				'value' => 'block',
			),
			'swap'     => array(
				'name'  => esc_html__( 'Swap', 'xts-theme' ),
				'value' => 'swap',
			),
			'fallback' => array(
				'name'  => esc_html__( 'Fallback', 'xts-theme' ),
				'value' => 'fallback',
			),
			'optional' => array(
				'name'  => esc_html__( 'Optional', 'xts-theme' ),
				'value' => 'optional',
			),
			'auto'     => array(
				'name'  => esc_html__( 'Auto', 'xts-theme' ),
				'value' => 'auto',
			),
		),
		'default'     => 'disabled',
		'class'       => 'xts-col-6',
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'icons_font_display',
		'name'        => esc_html__( '"font-display" for icon fonts', 'xts-theme' ),
		'description' => 'You can specify "font-display" property for fonts used for icons in our theme. Read more information <a href="https://developers.google.com/web/updates/2016/02/font-display">here</a>',
		'type'        => 'select',
		'section'     => 'css_performance_section',
		'options'     => array(
			'disabled' => array(
				'name'  => esc_html__( 'Disabled', 'xts-theme' ),
				'value' => 'disabled',
			),
			'block'    => array(
				'name'  => esc_html__( 'Block', 'xts-theme' ),
				'value' => 'block',
			),
			'swap'     => array(
				'name'  => esc_html__( 'Swap', 'xts-theme' ),
				'value' => 'swap',
			),
			'fallback' => array(
				'name'  => esc_html__( 'Fallback', 'xts-theme' ),
				'value' => 'fallback',
			),
			'optional' => array(
				'name'  => esc_html__( 'Optional', 'xts-theme' ),
				'value' => 'optional',
			),
			'auto'     => array(
				'name'  => esc_html__( 'Auto', 'xts-theme' ),
				'value' => 'auto',
			),
		),
		'default'     => 'disabled',
		'class'       => 'xts-col-6',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'dequeue_styles',
		'type'        => 'text_input',
		'section'     => 'css_performance_section',
		'name'        => esc_html__( 'Dequeue styles', 'xts-theme' ),
		'description' => esc_html__( 'You can manually disable CSS files from being loaded using their keys. Write their case separated with a comma. For example: xts-style,elementor-frontend', 'xts-theme' ),
		'class'       => 'xts-col-6',
		'priority'    => 70,
	)
);

/**
 * JS.
 */
Options::add_field(
	array(
		'id'          => 'libraries_combined_js',
		'type'        => 'switcher',
		'section'     => 'js_performance_section',
		'name'        => esc_html__( 'Combine libraries files', 'xts-theme' ),
		'description' => esc_html__( 'Use this option if you want to load all libraries on all pages.', 'xts-theme' ),
		'group'       => esc_html__( 'General', 'xts-theme' ),
		'default'     => '0',
		'class'       => 'xts-col-6',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'scripts_combined_js',
		'type'        => 'switcher',
		'section'     => 'js_performance_section',
		'name'        => esc_html__( 'Combine scripts files', 'xts-theme' ),
		'description' => esc_html__( 'Combine all theme initialization scripts. Reduces the number of files but increases the size in KB.', 'xts-theme' ),
		'group'       => esc_html__( 'General', 'xts-theme' ),
		'default'     => '0',
		'class'       => 'xts-col-6',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'minified_js',
		'type'        => 'switcher',
		'section'     => 'js_performance_section',
		'name'        => esc_html__( 'Include minified JS', 'xts-theme' ),
		'description' => esc_html__( 'Load minified versions of all JS files.', 'xts-theme' ),
		'group'       => esc_html__( 'General', 'xts-theme' ),
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'disable_carousel_mobile_devices',
		'type'        => 'switcher',
		'section'     => 'js_performance_section',
		'name'        => esc_html__( 'Disable carousel on mobile devices', 'xts-theme' ),
		'description' => esc_html__( 'Improve performance on mobile devices by replacing carousel script with CSS scrolling function.', 'xts-theme' ),
		'group'       => esc_html__( 'General', 'xts-theme' ),
		'default'     => '0',
		'class'       => 'xts-col-6',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'swiper_library',
		'section'  => 'js_performance_section',
		'name'     => esc_html__( 'Swiper library', 'xts-theme' ),
		'group'    => esc_html__( 'Elementor', 'xts-theme' ),
		'type'     => 'buttons',
		'options'  => array(
			'always'   => array(
				'name'  => esc_html__( 'Always load', 'xts-theme' ),
				'value' => 'always',
			),
			'required' => array(
				'name'  => esc_html__( 'On demand', 'xts-theme' ),
				'value' => 'required',
			),
			'not_use'  => array(
				'name'  => esc_html__( 'Never load', 'xts-theme' ),
				'value' => 'not_use',
			),
		),
		'default'  => 'always',
		'class'    => 'xts-col-6',
		'priority' => 70,
	)
);

Options::add_field(
	array(
		'id'       => 'waypoints_library',
		'section'  => 'js_performance_section',
		'name'     => esc_html__( 'Waypoints library', 'xts-theme' ),
		'group'    => esc_html__( 'Elementor', 'xts-theme' ),
		'type'     => 'buttons',
		'options'  => array(
			'always'   => array(
				'name'  => esc_html__( 'Always load', 'xts-theme' ),
				'value' => 'always',
			),
			'required' => array(
				'name'  => esc_html__( 'On demand', 'xts-theme' ),
				'value' => 'required',
			),
			'not_use'  => array(
				'name'  => esc_html__( 'Never load', 'xts-theme' ),
				'value' => 'not_use',
			),
		),
		'default'  => 'always',
		'class'    => 'xts-col-6',
		'priority' => 80,
	)
);

$config_libraries = xts_get_config( 'framework-js-libraries' );
foreach ( $config_libraries as $key => $libraries ) {
	foreach ( $libraries as $library ) {
		Options::add_field(
			array(
				'id'       => $library['name'] . '_library',
				'section'  => 'js_performance_section',
				'name'     => ucfirst( $library['name'] ) . ' library',
				'group'    => esc_html__( 'Libraries', 'xts-theme' ),
				'type'     => 'buttons',
				'options'  => array(
					'always'   => array(
						'name'  => esc_html__( 'Always load', 'xts-theme' ),
						'value' => 'always',
					),
					'required' => array(
						'name'  => esc_html__( 'On demand', 'xts-theme' ),
						'value' => 'required',
					),
					'not_use'  => array(
						'name'  => esc_html__( 'Never load', 'xts-theme' ),
						'value' => 'not_use',
					),
				),
				'default'  => 'required',
				'class'    => 'xts-col-6',
				'priority' => 90,
			)
		);
	}
}

$config_scripts  = xts_get_js_scripts();
$scripts_options = array();
foreach ( $config_scripts as $key => $scripts ) {
	foreach ( $scripts as $script ) {
		$scripts_options[ $key ] = array(
			'name'  => $script['title'],
			'value' => $key,
		);
	}
}

asort( $scripts_options );

Options::add_field(
	array(
		'id'          => 'scripts_always_use',
		'name'        => esc_html__( 'Scripts always load', 'xts-theme' ),
		'description' => esc_html__( 'You can manually load some initialization scripts on all pages.', 'xts-theme' ),
		'group'       => esc_html__( 'Advanced', 'xts-theme' ),
		'section'     => 'js_performance_section',
		'type'        => 'select',
		'multiple'    => true,
		'select2'     => true,
		'options'     => $scripts_options,
		'default'     => array(),
		'class'       => 'xts-col-6',
		'priority'    => 100,
	)
);

Options::add_field(
	array(
		'id'          => 'scripts_not_use',
		'name'        => esc_html__( 'Scripts never load', 'xts-theme' ),
		'description' => esc_html__( 'You can manually unload some initialization scripts on all pages.', 'xts-theme' ),
		'group'       => esc_html__( 'Advanced', 'xts-theme' ),
		'section'     => 'js_performance_section',
		'type'        => 'select',
		'multiple'    => true,
		'select2'     => true,
		'options'     => $scripts_options,
		'default'     => array(),
		'class'       => 'xts-col-6',
		'priority'    => 110,
	)
);
