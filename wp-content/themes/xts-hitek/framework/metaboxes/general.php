<?php
/**
 * Page metaboxes
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Metaboxes;

if ( ! function_exists( 'xts_register_page_metaboxes' ) ) {
	/**
	 * Register page metaboxes
	 *
	 * @since 1.0.0
	 */
	function xts_register_page_metaboxes() {
		$page_metabox = Metaboxes::add_metabox(
			array(
				'id'         => 'xts_page_metaboxes',
				'title'      => esc_html__( 'Page metaboxes', 'xts-theme' ),
				'post_types' => array( 'page', 'post', 'xts-portfolio' ),
			)
		);

		$page_metabox->add_section(
			array(
				'id'       => 'general_section',
				'name'     => esc_html__( 'General', 'xts-theme' ),
				'priority' => 10,
				'icon'     => 'xf-general',
			)
		);

		$page_metabox->add_section(
			array(
				'id'       => 'sidebar_section',
				'name'     => esc_html__( 'Sidebar', 'xts-theme' ),
				'priority' => 20,
				'icon'     => 'xf-side-bar',
			)
		);

		$page_metabox->add_section(
			array(
				'id'       => 'page_title_section',
				'name'     => esc_html__( 'Page title', 'xts-theme' ),
				'priority' => 30,
				'icon'     => 'xf-page-title',
			)
		);

		$page_metabox->add_section(
			array(
				'id'       => 'footer_section',
				'name'     => esc_html__( 'Footer', 'xts-theme' ),
				'priority' => 40,
				'icon'     => 'xf-footer',
			)
		);

		/**
		 * General.
		 */
		$page_metabox->add_field(
			array(
				'id'          => 'cl',
				'name'        => esc_html__( 'Negative gap', 'xts-theme' ),
				'description' => esc_html__( 'Add a negative margin to each Elementor section to align the content with your website container.', 'xts-theme' ),
				'type'        => 'buttons',
				'section'     => 'general_section',
				'options'     => array(
					'inherit'  => array(
						'name'  => esc_html__( 'Inherit', 'xts-theme' ),
						'value' => 'inherit',
					),
					'enabled'  => array(
						'name'  => esc_html__( 'Enabled', 'xts-theme' ),
						'value' => 'enabled',
					),
					'disabled' => array(
						'name'  => esc_html__( 'Disabled', 'xts-theme' ),
						'value' => 'disabled',
					),
				),
				'default'     => 'inherit',
				'priority'    => 9,
			)
		);

		$page_metabox->add_field(
			array(
				'id'          => 'open_categories',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Open categories menu', 'xts-theme' ),
				'description' => esc_html__( 'Keep the categories menu in the header always opened. You need to add a categories element to the header using our Header Builder.', 'xts-theme' ),
				'section'     => 'general_section',
				'default'     => '0',
				'priority'    => 10,
			)
		);

		$page_metabox->add_field(
			array(
				'name'         => esc_html__( 'Custom header for this page', 'xts-theme' ),
				'id'           => 'page_custom_header',
				'type'         => 'select',
				'empty_option' => true,
				'select2'      => true,
				'options'      => xts_get_headers_array(),
				'section'      => 'general_section',
				'priority'     => 20,
			)
		);

		$page_metabox->add_field(
			array(
				'name'            => esc_html__( 'Background for page', 'xts-theme' ),
				'id'              => 'all_pages_bg',
				'type'            => 'background',
				'option_override' => 'all_pages_bg',
				'section'         => 'general_section',
				'selector'        => '.xts-site-wrapper',
				'priority'        => 30,
			)
		);

		/**
		 * Sidebar.
		 */
		$page_metabox->add_field(
			array(
				'id'              => 'sidebar_position',
				'name'            => esc_html__( 'Position', 'xts-theme' ),
				'description'     => esc_html__( 'Select main content and sidebar alignment.', 'xts-theme' ),
				'type'            => 'buttons',
				'section'         => 'sidebar_section',
				'option_override' => 'sidebar_position',
				'options'         => array(
					'inherit'  => array(
						'name'  => esc_html__( 'Inherit', 'xts-theme' ),
						'value' => 'inherit',
					),
					'disabled' => array(
						'name'  => esc_html__( 'Disabled', 'xts-theme' ),
						'value' => 'disabled',
					),
					'left'     => array(
						'name'  => esc_html__( 'Left', 'xts-theme' ),
						'value' => 'left',
					),
					'right'    => array(
						'name'  => esc_html__( 'Right', 'xts-theme' ),
						'value' => 'right',
					),
				),
				'default'         => 'inherit',
				'priority'        => 10,
			)
		);

		$page_metabox->add_field(
			array(
				'id'              => 'sidebar_size',
				'name'            => esc_html__( 'Size', 'xts-theme' ),
				'description'     => esc_html__( 'You can set different sizes for your pages sidebar', 'xts-theme' ),
				'type'            => 'buttons',
				'section'         => 'sidebar_section',
				'option_override' => 'sidebar_size',
				'options'         => array(
					'inherit' => array(
						'name'  => esc_html__( 'Inherit', 'xts-theme' ),
						'value' => 'inherit',
					),
					'small'   => array(
						'name'  => esc_html__( 'Small', 'xts-theme' ),
						'value' => 'small',
					),
					'medium'  => array(
						'name'  => esc_html__( 'Medium', 'xts-theme' ),
						'value' => 'medium',
					),
					'large'   => array(
						'name'  => esc_html__( 'Large', 'xts-theme' ),
						'value' => 'large',
					),
				),
				'default'         => 'inherit',
				'priority'        => 20,
			)
		);

		$page_metabox->add_field(
			array(
				'id'              => 'sidebar_sticky',
				'type'            => 'switcher',
				'name'            => esc_html__( 'Sticky sidebar', 'xts-theme' ),
				'description'     => esc_html__( 'Make your sidebar stuck while you are scrolling the page content.', 'xts-theme' ),
				'section'         => 'sidebar_section',
				'option_override' => 'sidebar_sticky',
				'default'         => '0',
				'priority'        => 30,
			)
		);

		$page_metabox->add_field(
			array(
				'id'              => 'offcanvas_sidebar_desktop',
				'type'            => 'switcher',
				'name'            => esc_html__( 'Off canvas sidebar for desktop', 'xts-theme' ),
				'description'     => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
				'section'         => 'sidebar_section',
				'option_override' => 'offcanvas_sidebar_desktop',
				'class'           => 'xts-col-6',
				'default'         => '0',
				'priority'        => 40,
			)
		);

		$page_metabox->add_field(
			array(
				'id'              => 'offcanvas_sidebar_mobile',
				'type'            => 'switcher',
				'name'            => esc_html__( 'Off canvas sidebar for mobile devices', 'xts-theme' ),
				'description'     => esc_html__( 'Display the sidebar as a off-canvas element on special button click.', 'xts-theme' ),
				'section'         => 'sidebar_section',
				'option_override' => 'offcanvas_sidebar_mobile',
				'class'           => 'xts-col-6',
				'default'         => '1',
				'priority'        => 50,
			)
		);

		$page_metabox->add_field(
			array(
				'name'         => esc_html__( 'Custom sidebar for this page', 'xts-theme' ),
				'description'  => esc_html__( 'You can create a custom sidebar particularly for this page and fill it with any widgets via Appearance -> Widgets.', 'xts-theme' ),
				'id'           => 'custom_sidebar',
				'type'         => 'select',
				'empty_option' => true,
				'select2'      => true,
				'section'      => 'sidebar_section',
				'options'      => xts_get_sidebars_array(),
				'priority'     => 60,
			)
		);

		/**
		 * Page title
		 */
		$page_metabox->add_field(
			array(
				'id'              => 'page_title_design',
				'name'            => esc_html__( 'Design', 'xts-theme' ),
				'description'     => esc_html__( 'Select page title section design or disable it completely on all pages.', 'xts-theme' ),
				'type'            => 'buttons',
				'section'         => 'page_title_section',
				'option_override' => 'page_title_design',
				'options'         => xts_get_available_options( 'page_title_design_metabox' ),
				'default'         => 'inherit',
				'priority'        => 10,
			)
		);

		$page_metabox->add_field(
			array(
				'id'       => 'page_title_bg_image',
				'name'     => esc_html__( 'Background image', 'xts-theme' ),
				'type'     => 'upload',
				'section'  => 'page_title_section',
				'class'    => 'xts-col-6',
				'priority' => 20,
			)
		);

		$page_metabox->add_field(
			array(
				'id'       => 'page_title_bg_color',
				'name'     => esc_html__( 'Background color', 'xts-theme' ),
				'type'     => 'color',
				'section'  => 'page_title_section',
				'class'    => 'xts-col-6',
				'priority' => 30,
			)
		);

		$page_metabox->add_field(
			array(
				'id'              => 'page_title_color_scheme',
				'name'            => esc_html__( 'Color scheme', 'xts-theme' ),
				'description'     => esc_html__( 'You can set different colors depending on it\'s background. May be light or dark.', 'xts-theme' ),
				'type'            => 'buttons',
				'section'         => 'page_title_section',
				'option_override' => 'page_title_color_scheme',
				'options'         => array(
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
				'default'         => 'inherit',
				'priority'        => 40,
				'class'           => 'xts-color-scheme-picker',
			)
		);

		$page_metabox->add_field(
			array(
				'id'              => 'page_title_size',
				'name'            => esc_html__( 'Size', 'xts-theme' ),
				'description'     => esc_html__( 'You can set different sizes for your pages titles.', 'xts-theme' ),
				'type'            => 'buttons',
				'section'         => 'page_title_section',
				'option_override' => 'page_title_size',
				'options'         => array(
					'inherit' => array(
						'name'  => esc_html__( 'Inherit', 'xts-theme' ),
						'value' => 'inherit',
					),
					'xs'      => array(
						'name'  => esc_html__( 'XS', 'xts-theme' ),
						'value' => 'xs',
					),
					's'       => array(
						'name'  => esc_html__( 'S', 'xts-theme' ),
						'value' => 's',
					),
					'm'       => array(
						'name'  => esc_html__( 'M', 'xts-theme' ),
						'value' => 'm',
					),
					'l'       => array(
						'name'  => esc_html__( 'L', 'xts-theme' ),
						'value' => 'l',
					),
					'xl'      => array(
						'name'  => esc_html__( 'XL', 'xts-theme' ),
						'value' => 'xl',
					),
					'xxl'     => array(
						'name'  => esc_html__( 'XXL', 'xts-theme' ),
						'value' => 'xxl',
					),
				),
				'default'         => 'inherit',
				'priority'        => 50,
			)
		);

		/**
		 * Footer
		 */
		$page_metabox->add_field(
			array(
				'id'       => 'footer',
				'type'     => 'switcher',
				'name'     => esc_html__( 'Disable footer', 'xts-theme' ),
				'on-text'  => esc_html__( 'Yes', 'xts-theme' ),
				'off-text' => esc_html__( 'No', 'xts-theme' ),
				'section'  => 'footer_section',
				'default'  => '0',
				'priority' => 10,
			)
		);

		$page_metabox->add_field(
			array(
				'id'       => 'copyrights',
				'type'     => 'switcher',
				'name'     => esc_html__( 'Disable copyrights', 'xts-theme' ),
				'on-text'  => esc_html__( 'Yes', 'xts-theme' ),
				'off-text' => esc_html__( 'No', 'xts-theme' ),
				'section'  => 'footer_section',
				'default'  => '0',
				'priority' => 20,
			)
		);

		$page_metabox->add_field(
			array(
				'id'       => 'prefooter',
				'type'     => 'switcher',
				'name'     => esc_html__( 'Disable prefooter', 'xts-theme' ),
				'on-text'  => esc_html__( 'Yes', 'xts-theme' ),
				'off-text' => esc_html__( 'No', 'xts-theme' ),
				'section'  => 'footer_section',
				'default'  => '0',
				'priority' => 30,
			)
		);
	}

	add_action( 'init', 'xts_register_page_metaboxes', 100 );
}
