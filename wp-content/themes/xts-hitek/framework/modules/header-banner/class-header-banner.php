<?php
/**
 * Header banner
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * Header banner
 *
 * @since 1.0.0
 */
class Header_Banner extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'hooks' ) );
		add_action( 'init', array( $this, 'add_options' ) );
	}

	/**
	 * Hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'xts_before_header', array( $this, 'header_banner_template' ), 160 );
	}

	/**
	 * Template
	 *
	 * @since 1.0.0
	 */
	public function header_banner_template() {
		if ( ! xts_get_opt( 'header_banner' ) ) {
			return;
		}

		$banner_link     = xts_get_opt( 'header_banner_link' );
		$color_scheme    = xts_get_opt( 'header_banner_color_scheme' );
		$wrapper_classes = '';

		if ( 'inherit' !== $color_scheme ) {
			$wrapper_classes .= ' xts-scheme-' . $color_scheme;
		}
		if ( ! xts_get_opt( 'header_banner_close_button' ) && xts_get_opt( 'header_banner' ) && ! xts_is_maintenance_page() ) {
			$wrapper_classes .= ' xts-display';
		}

		xts_enqueue_js_script( 'header-banner' );

		xts_get_template(
			'banner-template.php',
			array(
				'wrapper_classes' => $wrapper_classes,
				'banner_link'     => $banner_link,
			),
			'header-banner'
		);
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'header_banner_section',
				'name'     => esc_html__( 'Header banner', 'xts-theme' ),
				'parent'   => 'general_section',
				'priority' => 30,
				'icon'     => 'xf-header-banner',
			)
		);

		Options::add_field(
			array(
				'id'          => 'header_banner',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Header banner', 'xts-theme' ),
				'description' => esc_html__( 'Special area before the header to display some global website information or promotion.', 'xts-theme' ),
				'section'     => 'header_banner_section',
				'default'     => '0',
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'          => 'header_banner_link',
				'type'        => 'text_input',
				'name'        => esc_html__( 'Link', 'xts-theme' ),
				'description' => esc_html__( 'The link will be added to the whole banner area.', 'xts-theme' ),
				'group'       => esc_html__( 'Content', 'xts-theme' ),
				'section'     => 'header_banner_section',
				'priority'    => 20,
			)
		);

		Options::add_field(
			array(
				'id'          => 'header_banner_content_type',
				'name'        => esc_html__( 'Content type', 'xts-theme' ),
				'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
				'group'       => esc_html__( 'Content', 'xts-theme' ),
				'type'        => 'buttons',
				'section'     => 'header_banner_section',
				'options'     => array(
					'text'       => array(
						'name'  => esc_html__( 'Text', 'xts-theme' ),
						'value' => 'text',
					),
					'html_block' => array(
						'name'  => esc_html__( 'HTML Block', 'xts-theme' ),
						'value' => 'html_block',
					),
				),
				'default'     => 'text',
				'priority'    => 30,
			)
		);

		Options::add_field(
			array(
				'id'          => 'header_banner_text',
				'name'        => esc_html__( 'Text', 'xts-theme' ),
				'description' => esc_html__( 'You can use any text or HTML here.', 'xts-theme' ),
				'group'       => esc_html__( 'Content', 'xts-theme' ),
				'section'     => 'header_banner_section',
				'type'        => 'textarea',
				'wysiwyg'     => true,
				'requires'    => array(
					array(
						'key'     => 'header_banner_content_type',
						'compare' => 'equals',
						'value'   => 'text',
					),
				),
				'priority'    => 40,
			)
		);

		Options::add_field(
			array(
				'id'           => 'header_banner_html_block',
				'name'         => esc_html__( 'HTML Block', 'xts-theme' ),
				'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
				'group'        => esc_html__( 'Content', 'xts-theme' ),
				'type'         => 'select',
				'section'      => 'header_banner_section',
				'empty_option' => true,
				'select2'      => true,
				'options'      => xts_get_html_blocks_array(),
				'requires'     => array(
					array(
						'key'     => 'header_banner_content_type',
						'compare' => 'equals',
						'value'   => 'html_block',
					),
				),
				'class'        => 'xts-html-block-links',
				'priority'     => 50,
			)
		);

		Options::add_field(
			array(
				'id'                  => 'header_banner_height',
				'name'                => esc_html__( 'Height (desktop)', 'xts-theme' ),
				'description'         => esc_html__( 'The height for the banner area in pixels on desktop devices.', 'xts-theme' ),
				'group'               => esc_html__( 'Style', 'xts-theme' ),
				'type'                => 'range',
				'section'             => 'header_banner_section',
				'responsive'          => true,
				'responsive_variants' => array( 'desktop', 'tablet', 'mobile', 'mobile_small' ),
				'desktop_only'        => true,
				'min'                 => 0,
				'max'                 => 200,
				'step'                => 1,
				'default'             => 40,
				'priority'            => 60,
			)
		);

		Options::add_field(
			array(
				'id'                  => 'header_banner_height_tablet',
				'name'                => esc_html__( 'Height (tablet)', 'xts-theme' ),
				'description'         => esc_html__( 'The height for the banner area in pixels on tablet devices.', 'xts-theme' ),
				'group'               => esc_html__( 'Style', 'xts-theme' ),
				'type'                => 'range',
				'section'             => 'header_banner_section',
				'responsive'          => true,
				'responsive_variants' => array( 'desktop', 'tablet', 'mobile', 'mobile_small' ),
				'tablet_only'         => true,
				'min'                 => 0,
				'max'                 => 200,
				'step'                => 1,
				'default'             => 40,
				'priority'            => 70,
			)
		);

		Options::add_field(
			array(
				'id'                  => 'header_banner_height_mobile',
				'name'                => esc_html__( 'Height (mobile)', 'xts-theme' ),
				'description'         => esc_html__( 'The height for the banner area in pixels on mobile devices.', 'xts-theme' ),
				'group'               => esc_html__( 'Style', 'xts-theme' ),
				'type'                => 'range',
				'section'             => 'header_banner_section',
				'responsive'          => true,
				'responsive_variants' => array( 'desktop', 'tablet', 'mobile', 'mobile_small' ),
				'mobile_only'         => true,
				'min'                 => 0,
				'max'                 => 200,
				'step'                => 1,
				'default'             => 40,
				'priority'            => 70,
			)
		);

		Options::add_field(
			array(
				'id'                  => 'header_banner_height_mobile_small',
				'name'                => esc_html__( 'Height (mobile small)', 'xts-theme' ),
				'description'         => esc_html__( 'The height for the banner area in pixels on small mobile devices.', 'xts-theme' ),
				'group'               => esc_html__( 'Style', 'xts-theme' ),
				'type'                => 'range',
				'section'             => 'header_banner_section',
				'responsive'          => true,
				'responsive_variants' => array( 'desktop', 'tablet', 'mobile', 'mobile_small' ),
				'mobile_small_only'   => true,
				'min'                 => 0,
				'max'                 => 200,
				'step'                => 1,
				'default'             => 40,
				'priority'            => 70,
			)
		);

		Options::add_field(
			array(
				'id'          => 'header_banner_background',
				'name'        => esc_html__( 'Background', 'xts-theme' ),
				'description' => esc_html__( 'You can set your background color or upload some graphic.', 'xts-theme' ),
				'group'       => esc_html__( 'Style', 'xts-theme' ),
				'type'        => 'background',
				'section'     => 'header_banner_section',
				'selector'    => '.xts-header-banner-bg',
				'priority'    => 80,
			)
		);

		Options::add_field(
			array(
				'id'          => 'header_banner_color_scheme',
				'name'        => esc_html__( 'Color scheme', 'xts-theme' ),
				'description' => esc_html__( 'You can set different text colors depending on its background. May be light or dark.', 'xts-theme' ),
				'group'       => esc_html__( 'Style', 'xts-theme' ),
				'type'        => 'buttons',
				'section'     => 'header_banner_section',
				'options'     => array(
					'inherit' => array(
						'name'  => esc_html__( 'Inherit', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/options/color/inherit.svg',
						'value' => 'inherit',
					),
					'dark'    => array(
						'name'  => esc_html__( 'Dark', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/options/color/dark.svg',
						'value' => 'dark',
					),
					'light'   => array(
						'name'  => esc_html__( 'Light', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/options/color/light.svg',
						'value' => 'light',
					),
				),
				'default'     => 'inherit',
				'priority'    => 90,
				'class'       => 'xts-color-scheme-picker',
			)
		);

		Options::add_field(
			array(
				'id'          => 'header_banner_close_button',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Close button', 'xts-theme' ),
				'description' => esc_html__( 'Disable this option if you want to keep the banner always opened and not allow customers to hide it.', 'xts-theme' ),
				'group'       => esc_html__( 'Settings', 'xts-theme' ),
				'section'     => 'header_banner_section',
				'default'     => '1',
				'priority'    => 100,
			)
		);

		Options::add_field(
			array(
				'id'          => 'header_banner_version',
				'type'        => 'text_input',
				'name'        => esc_html__( 'Version', 'xts-theme' ),
				'description' => esc_html__( 'If you change your banner you can increase their version to show the banner to all visitors again.', 'xts-theme' ),
				'group'       => esc_html__( 'Settings', 'xts-theme' ),
				'section'     => 'header_banner_section',
				'requires'    => array(
					array(
						'key'     => 'header_banner_close_button',
						'compare' => 'equals',
						'value'   => '1',
					),
				),
				'default'     => '1',
				'priority'    => 110,
			)
		);
	}
}
