<?php
/**
 * Theme setup functions.
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_theme_activation_redirect' ) ) {
	/**
	 * Redirect to welcome screen after theme activated.
	 *
	 * @since 1.0.0
	 */
	function xts_theme_activation_redirect() {
		global $pagenow;

		$current_theme = strtolower( xts_get_theme_info( 'Name' ) );
		$theme_list    = xts_get_config( 'theme-list' );

		$args = array(
			'page' => 'xts_dashboard',
		);

		if ( 'finish' !== get_option( 'xts_setup_status_' . $current_theme ) ) {
			$args['xts_setup'] = true;
		}

		if ( 'themes.php' === $pagenow && is_admin() && isset( $_GET['activated'] ) && isset( $theme_list[ $current_theme ] ) ) { // phpcs:ignore
			wp_safe_redirect( esc_url_raw( add_query_arg( $args, admin_url( 'admin.php' ) ) ) );
		}
	}

	add_action( 'admin_init', 'xts_theme_activation_redirect' );
}
if ( ! function_exists( 'xts_allow_mime_types' ) ) {
	/**
	 * New allowed mime types.
	 *
	 * @since 1.0.0
	 *
	 * @param array $mimes Mime types.
	 *
	 * @return array
	 */
	function xts_allow_mime_types( $mimes ) {
		if ( xts_get_opt( 'allow_upload_svg' ) ) {
			$mimes['svg'] = 'image/svg+xml';
		}

		if ( apply_filters( 'xts_alt_font_mime_types', false ) ) {
			$mimes['woff']  = 'font/woff';
			$mimes['woff2'] = 'font/woff2';
		} else {
			$mimes['woff']  = 'application/x-font-woff';
			$mimes['woff2'] = 'application/x-font-woff2';
		}

		return $mimes;
	}

	add_filter( 'upload_mimes', 'xts_allow_mime_types', 100 );
}

if ( ! function_exists( 'xts_register_sidebars' ) ) {
	/**
	 * Register widget area.
	 *
	 * @since 1.0.0
	 */
	function xts_register_sidebars() {
		$title_tag = apply_filters( 'xts_widgets_title_tag', 'span' );

		register_sidebar(
			array(
				'name'          => esc_html__( 'Main widget area', 'xts-theme' ),
				'id'            => 'main-widget-sidebar',
				'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'xts-theme' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<' . $title_tag . ' class="widget-title title"><span>',
				'after_title'   => '</span></' . $title_tag . '>',
			)
		);

		register_sidebar(
			array(
				'name'          => esc_html__( 'Area after the mobile menu', 'xts-theme' ),
				'id'            => 'mobile-menu-widget-sidebar',
				'description'   => esc_html__( 'Add your widgets that will be displayed after the mobile menu links.', 'xts-theme' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<' . $title_tag . ' class="widget-title title"><span>',
				'after_title'   => '</span></' . $title_tag . '>',
			)
		);

		if ( xts_is_woocommerce_installed() ) {
			register_sidebar(
				array(
					'name'          => esc_html__( 'Shop page widget area', 'xts-theme' ),
					'id'            => 'shop-widget-sidebar',
					'description'   => esc_html__( 'Widget Area for shop pages', 'xts-theme' ),
					'class'         => '',
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<' . $title_tag . ' class="widget-title title"><span>',
					'after_title'   => '</span></' . $title_tag . '>',
				)
			);

			register_sidebar(
				array(
					'name'          => esc_html__( 'Single product page widget area', 'xts-theme' ),
					'id'            => 'single-product-widget-sidebar',
					'description'   => esc_html__( 'Widget Area for single product page', 'xts-theme' ),
					'class'         => '',
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<' . $title_tag . ' class="widget-title title"><span>',
					'after_title'   => '</span></' . $title_tag . '>',
				)
			);

			register_sidebar(
				array(
					'name'          => esc_html__( 'My account page widget area', 'xts-theme' ),
					'id'            => 'my-account-widget-sidebar',
					'description'   => esc_html__( 'Widget Area for my account page', 'xts-theme' ),
					'class'         => '',
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<' . $title_tag . ' class="widget-title title"><span>',
					'after_title'   => '</span></' . $title_tag . '>',
				)
			);

			register_sidebar(
				array(
					'name'          => esc_html__( 'Shop filters widget area', 'xts-theme' ),
					'id'            => 'filters-area-widget-sidebar',
					'description'   => esc_html__( 'Widget Area for shop filters', 'xts-theme' ),
					'class'         => '',
					'before_widget' => '<div class="xts-col"><div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div></div>',
					'before_title'  => '<' . $title_tag . ' class="widget-title title"><span>',
					'after_title'   => '</span></' . $title_tag . '>',
				)
			);
		}

		// Footer sidebars register.
		$footer_classes = '';
		$footer_config  = xts_get_footer_grid();
		$footer_layout  = xts_get_opt( 'footer_layout' );

		if ( xts_get_opt( 'footer_widgets_collapse' ) ) {
			$footer_classes .= ' xts-widget-collapse';
		}

		if ( apply_filters( 'xts_show_all_footer_sidebars', false ) ) {
			$footer_layout = '6';
		}

		if ( isset( $footer_config[ $footer_layout ] ) ) {
			foreach ( $footer_config[ $footer_layout ]['cols'] as $key => $columns ) {
				$index = $key + 1;
				register_sidebar(
					array(
						'name'          => esc_html__( 'Footer column', 'xts-theme' ) . ' ' . $index,
						'id'            => 'footer-' . $index,
						'class'         => '',
						'before_widget' => '<div id="%1$s" class="widget xts-footer-widget %2$s' . esc_attr( $footer_classes ) . '">',
						'after_widget'  => '</div>',
						'before_title'  => '<' . $title_tag . ' class="widget-title title"><span>',
						'after_title'   => '</span></' . $title_tag . '>',
					)
				);
			}
		}

		// Copyrights.
		if ( 'widgets' === xts_get_opt( 'copyrights_content_type' ) ) {
			register_sidebar(
				array(
					'name'          => esc_html__( 'Copyrights left area', 'xts-theme' ),
					'id'            => 'copyrights-left-widget-sidebar',
					'description'   => esc_html__( 'Add widgets here to appear in your copyrights.', 'xts-theme' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<' . $title_tag . ' class="widget-title title"><span>',
					'after_title'   => '</span></' . $title_tag . '>',
				)
			);

			register_sidebar(
				array(
					'name'          => esc_html__( 'Copyrights right area', 'xts-theme' ),
					'id'            => 'copyrights-right-widget-sidebar',
					'description'   => esc_html__( 'Add widgets here to appear in your copyrights.', 'xts-theme' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<' . $title_tag . ' class="widget-title title"><span>',
					'after_title'   => '</span></' . $title_tag . '>',
				)
			);
		}

		// Custom sidebars.
		$custom_sidebars = get_posts(
			array(
				'post_type'   => 'xts-sidebar',
				'post_status' => 'publish',
				'numberposts' => - 1,
			)
		);

		foreach ( $custom_sidebars as $sidebar ) {
			register_sidebar(
				array(
					'name'          => $sidebar->post_title,
					'id'            => 'sidebar-' . $sidebar->ID,
					'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'xts-theme' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<' . $title_tag . ' class="widget-title title"><span>',
					'after_title'   => '</span></' . $title_tag . '>',
				)
			);
		}
	}

	add_action( 'widgets_init', 'xts_register_sidebars' );
}

if ( ! function_exists( 'xts_theme_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since 1.0.0
	 */
	function xts_theme_setup() {
		/**
		 * Add support for post formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'video',
				'audio',
				'quote',
				'image',
				'gallery',
				'link',
			)
		);
		/**
		 * Add support for automatic feed links
		 */
		add_theme_support( 'automatic-feed-links' );

		/**
		 * Add support for post thumbnails
		 */
		add_theme_support( 'post-thumbnails' );

		/**
		 * Add support for post title tag
		 */
		add_theme_support( 'title-tag' );

		/**
		 * Gutenberg
		 */
		add_theme_support( 'align-wide' );

		/**
		 * Register nav menus
		 */
		register_nav_menus(
			array(
				'main-menu'   => esc_html__( 'Main menu', 'xts-theme' ),
				'mobile-menu' => esc_html__( 'Mobile menu', 'xts-theme' ),
			)
		);

		/**
		 * Add theme support for WooCommerce
		 */
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
	}

	add_action( 'after_setup_theme', 'xts_theme_setup' );
}

if ( ! function_exists( 'xts_register_required_plugins' ) ) {
	/**
	 * TGM Plugin activator.
	 *
	 * @since 1.0.0
	 */
	function xts_register_required_plugins() {
		$current_theme = strtolower( xts_get_theme_info( 'Name' ) );
		$theme_list    = xts_get_config( 'theme-list' );

		$plugins[] = array(
			'name'     => 'Elementor',
			'slug'     => 'elementor',
			'required' => true,
			'tooltip'  => 'Required page builder for our theme',
		);

		if ( isset( $theme_list[ $current_theme ] ) && $theme_list[ $current_theme ]['woocommerce'] ) {
			$plugins[] = array(
				'name'     => 'WooCommerce',
				'slug'     => 'woocommerce',
				'required' => true,
				'tooltip'  => 'Base plugin for your online store',
			);
		}

		if ( ! xts_is_build_for_space() ) {
			$plugins[] = array(
				'name'     => 'XTemos theme core',
				'slug'     => 'xts-theme-core',
				'required' => true,
				'version'  => defined( 'XTS_CORE_VERSION' ) ? XTS_CORE_VERSION : '1.0.0',
				'source'   => 'https://woodmart.xtemos.com/plugins/xts-theme-core.zip',
				'tooltip'  => 'Base plugin for your theme',
			);
		}

		$plugins[] = array(
			'name'    => 'Slider Revolution',
			'slug'    => 'revslider',
			'version' => '6.5.7',
			'source'  => 'https://woodmart.xtemos.com/plugins/revslider.zip',
			'tooltip' => 'Powerful plugin for sliders and effects',
		);

		$plugins[] = array(
			'name'    => 'Contact Form 7',
			'slug'    => 'contact-form-7',
			'tooltip' => 'Plugin for creating custom contact forms',
		);

		$plugins[] = array(
			'name'    => 'MC4WP: Mailchimp for WordPress',
			'slug'    => 'mailchimp-for-wp',
			'tooltip' => 'Create newsletter subscription form for Mailchimp',
		);

		tgmpa( $plugins );
	}

	add_action( 'tgmpa_register', 'xts_register_required_plugins' );
}

/**
 * Disable emoji styles
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Set up the content width value based on theme's design.
 */
$GLOBALS['content_width'] = 1200;

/**
 * Make the theme available for translations.
 */
load_theme_textdomain( 'xts-theme', XTS_THEME_DIR . '/languages' );

/*
 * Switch default core markup for search form, comment form, and comments to output valid HTML5.
 */
add_theme_support(
	'html5',
	array(
		'comment-list',
	)
);

/**
 * Remove default WordPress styles for widgets
 */
add_filter( 'show_recent_comments_widget_style', '__return_false' );
