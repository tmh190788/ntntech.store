<?php
/**
 * General framework options sections
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

/**
 * General.
 */
Options::add_section(
	array(
		'id'       => 'general_section',
		'name'     => esc_html__( 'General', 'xts-theme' ),
		'priority' => 10,
		'icon'     => 'xf-general',
	)
);

Options::add_section(
	array(
		'id'       => 'general_layout_section',
		'name'     => esc_html__( 'Layout', 'xts-theme' ),
		'parent'   => 'general_section',
		'priority' => 10,
		'icon'     => 'xf-general',
	)
);

Options::add_section(
	array(
		'id'       => 'search_section',
		'name'     => esc_html__( 'Search', 'xts-theme' ),
		'parent'   => 'general_section',
		'priority' => 20,
		'icon'     => 'xf-general',
	)
);

/**
 * Header banner (30).
 */

/**
 * Mobile bottom navbar (40).
 */

Options::add_section(
	array(
		'id'       => 'promo_popup_section',
		'name'     => esc_html__( 'Promo popup', 'xts-theme' ),
		'parent'   => 'general_section',
		'priority' => 50,
		'icon'     => 'xf-general',
	)
);

Options::add_section(
	array(
		'id'       => 'cookies_section',
		'name'     => esc_html__( 'Cookie law info', 'xts-theme' ),
		'parent'   => 'general_section',
		'priority' => 60,
		'icon'     => 'xf-general',
	)
);

/**
 * Page title.
 */
Options::add_section(
	array(
		'id'       => 'page_title_section',
		'name'     => esc_html__( 'Page title', 'xts-theme' ),
		'priority' => 20,
		'icon'     => 'xf-page-title',
	)
);

/**
 * Footer.
 */
Options::add_section(
	array(
		'id'       => 'general_footer_section',
		'name'     => esc_html__( 'Footer', 'xts-theme' ),
		'priority' => 30,
		'icon'     => 'xf-footer',
	)
);

Options::add_section(
	array(
		'id'       => 'footer_section',
		'name'     => esc_html__( 'Footer', 'xts-theme' ),
		'parent'   => 'general_footer_section',
		'priority' => 10,
		'icon'     => 'xf-footer',
	)
);

Options::add_section(
	array(
		'id'       => 'copyrights_section',
		'name'     => esc_html__( 'Copyrights', 'xts-theme' ),
		'parent'   => 'general_footer_section',
		'priority' => 20,
		'icon'     => 'xf-footer',
	)
);

Options::add_section(
	array(
		'id'       => 'prefooter_section',
		'name'     => esc_html__( 'Prefooter', 'xts-theme' ),
		'parent'   => 'general_footer_section',
		'priority' => 30,
		'icon'     => 'xf-footer',
	)
);

/**
 * Colors.
 */
Options::add_section(
	array(
		'id'       => 'colors_section',
		'name'     => esc_html__( 'Colors', 'xts-theme' ),
		'priority' => 40,
		'icon'     => 'xf-colors',
	)
);

Options::add_section(
	array(
		'id'       => 'general_colors_section',
		'name'     => esc_html__( 'Colors', 'xts-theme' ),
		'priority' => 10,
		'parent'   => 'colors_section',
		'icon'     => 'xf-colors',
	)
);

Options::add_section(
	array(
		'id'       => 'pages_bg_colors_section',
		'name'     => esc_html__( 'Pages background', 'xts-theme' ),
		'priority' => 20,
		'parent'   => 'colors_section',
		'icon'     => 'xf-colors',
	)
);

Options::add_section(
	array(
		'id'       => 'buttons_colors_section',
		'name'     => esc_html__( 'Buttons', 'xts-theme' ),
		'priority' => 30,
		'parent'   => 'colors_section',
		'icon'     => 'xf-colors',
	)
);

/**
 * Typography.
 */
Options::add_section(
	array(
		'id'       => 'typography_section',
		'name'     => esc_html__( 'Typography', 'xts-theme' ),
		'priority' => 50,
		'icon'     => 'xf-typography',
	)
);

Options::add_section(
	array(
		'id'       => 'basic_typography_section',
		'name'     => esc_html__( 'Basic', 'xts-theme' ),
		'priority' => 10,
		'parent'   => 'typography_section',
		'icon'     => 'xf-typography',
	)
);

Options::add_section(
	array(
		'id'       => 'advanced_typography_section',
		'name'     => esc_html__( 'Advanced', 'xts-theme' ),
		'priority' => 20,
		'parent'   => 'typography_section',
		'icon'     => 'xf-typography',
	)
);

Options::add_section(
	array(
		'id'       => 'custom_fonts_section',
		'name'     => esc_html__( 'Custom fonts', 'xts-theme' ),
		'priority' => 30,
		'parent'   => 'typography_section',
		'icon'     => 'xf-typography',
	)
);

Options::add_section(
	array(
		'id'       => 'typekit_fonts_section',
		'name'     => esc_html__( 'Typekit fonts', 'xts-theme' ),
		'priority' => 40,
		'parent'   => 'typography_section',
		'icon'     => 'xf-typography',
	)
);

/**
 * Blog.
 */
Options::add_section(
	array(
		'id'       => 'blog_section',
		'name'     => esc_html__( 'Blog', 'xts-theme' ),
		'priority' => 60,
		'icon'     => 'xf-blog',
	)
);

Options::add_section(
	array(
		'id'       => 'blog_archive_section',
		'name'     => esc_html__( 'Blog archive', 'xts-theme' ),
		'priority' => 10,
		'parent'   => 'blog_section',
		'icon'     => 'xf-blog',
	)
);

Options::add_section(
	array(
		'id'       => 'single_post_section',
		'name'     => esc_html__( 'Single post', 'xts-theme' ),
		'priority' => 20,
		'parent'   => 'blog_section',
		'icon'     => 'xf-blog',
	)
);

/**
 * Portfolio
 */
Options::add_section(
	array(
		'id'       => 'portfolio_general_section',
		'name'     => esc_html__( 'Portfolio', 'xts-theme' ),
		'priority' => 70,
		'icon'     => 'xf-portfolio',
	)
);

Options::add_section(
	array(
		'id'       => 'portfolio_section',
		'name'     => esc_html__( 'Portfolio', 'xts-theme' ),
		'priority' => 10,
		'parent'   => 'portfolio_general_section',
		'icon'     => 'xf-portfolio',
	)
);

Options::add_section(
	array(
		'id'       => 'portfolio_archive_section',
		'name'     => esc_html__( 'Portfolio archive', 'xts-theme' ),
		'priority' => 20,
		'parent'   => 'portfolio_general_section',
		'icon'     => 'xf-portfolio',
	)
);

Options::add_section(
	array(
		'id'       => 'single_project_section',
		'name'     => esc_html__( 'Single project', 'xts-theme' ),
		'priority' => 30,
		'parent'   => 'portfolio_general_section',
		'icon'     => 'xf-portfolio',
	)
);

/**
 * Shop.
 */
Options::add_section(
	array(
		'id'       => 'shop_section',
		'name'     => esc_html__( 'Shop', 'xts-theme' ),
		'priority' => 80,
		'icon'     => 'xf-shop',
	)
);

Options::add_section(
	array(
		'id'       => 'general_shop_section',
		'name'     => esc_html__( 'Shop', 'xts-theme' ),
		'priority' => 10,
		'parent'   => 'shop_section',
		'icon'     => 'xf-shop',
	)
);

/**
 * Swatches swatches_section (20).
 */

Options::add_section(
	array(
		'id'       => 'product_labels_section',
		'name'     => esc_html__( 'Product labels', 'xts-theme' ),
		'priority' => 30,
		'parent'   => 'shop_section',
		'icon'     => 'xf-shop',
	)
);

/**
 * Brands brands_section (40).
 */

/**
 * Quick view quick_view_section (50).
 */

/**
 * Compare compare_section (60).
 */

/**
 * Wishlist wishlist_section (70).
 */

Options::add_section(
	array(
		'id'       => 'thank_you_page_section',
		'name'     => esc_html__( 'Thank you page', 'xts-theme' ),
		'priority' => 80,
		'parent'   => 'shop_section',
		'icon'     => 'xf-shop',
	)
);

/**
 * Sticky categories navigation sticky_categories_navigation_section (90).
 */

/**
 * Product archive.
 */
Options::add_section(
	array(
		'id'       => 'product_archive_general_section',
		'name'     => esc_html__( 'Product archive', 'xts-theme' ),
		'priority' => 81,
		'icon'     => 'xf-product-archive',
	)
);

Options::add_section(
	array(
		'id'       => 'product_archive_section',
		'name'     => esc_html__( 'Product archive', 'xts-theme' ),
		'priority' => 10,
		'parent'   => 'product_archive_general_section',
		'icon'     => 'xf-product-archive',
	)
);

Options::add_section(
	array(
		'id'       => 'product_archive_sidebar_section',
		'name'     => esc_html__( 'Sidebar', 'xts-theme' ),
		'priority' => 20,
		'parent'   => 'product_archive_general_section',
		'icon'     => 'xf-product-archive',
	)
);

Options::add_section(
	array(
		'id'       => 'product_archive_page_title_section',
		'name'     => esc_html__( 'Page title', 'xts-theme' ),
		'priority' => 30,
		'parent'   => 'product_archive_general_section',
		'icon'     => 'xf-product-archive',
	)
);

Options::add_section(
	array(
		'id'       => 'product_archive_layout_section',
		'name'     => esc_html__( 'Layout', 'xts-theme' ),
		'priority' => 40,
		'parent'   => 'product_archive_general_section',
		'icon'     => 'xf-product-archive',
	)
);

Options::add_section(
	array(
		'id'       => 'product_archive_product_options_section',
		'name'     => esc_html__( 'Product options', 'xts-theme' ),
		'priority' => 50,
		'parent'   => 'product_archive_general_section',
		'icon'     => 'xf-product-archive',
	)
);

Options::add_section(
	array(
		'id'       => 'product_archive_categories_options_section',
		'name'     => esc_html__( 'Categories options', 'xts-theme' ),
		'priority' => 60,
		'parent'   => 'product_archive_general_section',
		'icon'     => 'xf-product-archive',
	)
);

Options::add_section(
	array(
		'id'       => 'widgets_section',
		'name'     => esc_html__( 'Widgets', 'xts-theme' ),
		'priority' => 70,
		'parent'   => 'product_archive_general_section',
		'icon'     => 'xf-product-archive',
	)
);


Options::add_section(
	array(
		'id'       => 'product_archive_filters_area_section',
		'name'     => esc_html__( 'Filters area', 'xts-theme' ),
		'priority' => 80,
		'parent'   => 'product_archive_general_section',
		'icon'     => 'xf-product-archive',
	)
);

/**
 * Single product page.
 */
Options::add_section(
	array(
		'id'       => 'single_product_section',
		'name'     => esc_html__( 'Single product', 'xts-theme' ),
		'priority' => 90,
		'icon'     => 'xf-single-product',
	)
);

Options::add_section(
	array(
		'id'       => 'general_single_product_section',
		'name'     => esc_html__( 'Single product', 'xts-theme' ),
		'priority' => 9,
		'parent'   => 'single_product_section',
		'icon'     => 'xf-single-product',
	)
);

Options::add_section(
	array(
		'id'       => 'single_product_image_section',
		'name'     => esc_html__( 'Images', 'xts-theme' ),
		'priority' => 10,
		'parent'   => 'single_product_section',
		'icon'     => 'xf-single-product',
	)
);

Options::add_section(
	array(
		'id'       => 'single_product_tabs_section',
		'name'     => esc_html__( 'Tabs', 'xts-theme' ),
		'priority' => 20,
		'parent'   => 'single_product_section',
		'icon'     => 'xf-single-product',
	)
);

Options::add_section(
	array(
		'id'       => 'single_product_add_to_cart_section',
		'name'     => esc_html__( 'Add to cart', 'xts-theme' ),
		'priority' => 30,
		'parent'   => 'single_product_section',
		'icon'     => 'xf-single-product',
	)
);

Options::add_section(
	array(
		'id'       => 'single_product_elements_section',
		'name'     => esc_html__( 'Elements', 'xts-theme' ),
		'priority' => 40,
		'parent'   => 'single_product_section',
		'icon'     => 'xf-single-product',
	)
);

/**
 * WC Product builder single_product_builder_section (50).
 */

/**
 * Comments single_product_comments_section (60).
 */

/**
 * Social profiles.
 */
Options::add_section(
	array(
		'id'       => 'social_profiles_section',
		'name'     => esc_html__( 'Social profiles', 'xts-theme' ),
		'priority' => 100,
		'icon'     => 'xf-social-profiles',
	)
);

Options::add_section(
	array(
		'id'       => 'social_links_section',
		'name'     => esc_html__( 'Social profiles', 'xts-theme' ),
		'parent'   => 'social_profiles_section',
		'priority' => 10,
		'icon'     => 'xf-social-profiles',
	)
);

Options::add_section(
	array(
		'id'       => 'share_buttons_section',
		'name'     => esc_html__( 'Share buttons', 'xts-theme' ),
		'parent'   => 'social_profiles_section',
		'priority' => 20,
		'icon'     => 'xf-social-profiles',
	)
);

/**
 * API integrations.
 */
Options::add_section(
	array(
		'id'       => 'api_integrations_section',
		'name'     => esc_html__( 'API integrations', 'xts-theme' ),
		'priority' => 100,
		'icon'     => 'xf-api-integrations',
	)
);

Options::add_section(
	array(
		'id'       => 'instagram_api_section',
		'name'     => esc_html__( 'Instagram API', 'xts-theme' ),
		'parent'   => 'api_integrations_section',
		'priority' => 10,
		'icon'     => 'xf-api-integrations',
	)
);

Options::add_section(
	array(
		'id'       => 'google_map_api_section',
		'name'     => esc_html__( 'Google map API', 'xts-theme' ),
		'parent'   => 'api_integrations_section',
		'priority' => 20,
		'icon'     => 'xf-api-integrations',
	)
);
/**
 * WC_Social_Authentication api_integrations_section (30).
 */

/**
 * Performance.
 */
Options::add_section(
	array(
		'id'       => 'general_performance_section',
		'name'     => esc_html__( 'Performance', 'xts-theme' ),
		'priority' => 110,
		'icon'     => 'xf-performance',
	)
);

Options::add_section(
	array(
		'id'       => 'css_performance_section',
		'name'     => esc_html__( 'CSS', 'xts-theme' ),
		'priority' => 10,
		'parent'   => 'general_performance_section',
		'icon'     => 'xf-performance',
	)
);

Options::add_section(
	array(
		'id'       => 'js_performance_section',
		'name'     => esc_html__( 'JS', 'xts-theme' ),
		'priority' => 20,
		'parent'   => 'general_performance_section',
		'icon'     => 'xf-performance',
	)
);

/**
 * Lazy loading lazy_loading_section (30).
 */

/**
 * Preloader preloader_section (40).
 */

/**
 * Custom CSS.
 */
Options::add_section(
	array(
		'id'       => 'custom_css_section',
		'name'     => esc_html__( 'Custom CSS', 'xts-theme' ),
		'priority' => 120,
		'icon'     => 'xf-custom-css',
	)
);

/**
 * Custom JS.
 */
Options::add_section(
	array(
		'id'       => 'custom_js_section',
		'name'     => esc_html__( 'Custom JS', 'xts-theme' ),
		'priority' => 130,
		'icon'     => 'xf-custom-js',
	)
);

/**
 * Miscellaneous.
 */
Options::add_section(
	array(
		'id'       => 'miscellaneous_section',
		'name'     => esc_html__( 'Miscellaneous', 'xts-theme' ),
		'priority' => 140,
		'icon'     => 'xf-miscellaneous',
	)
);

/**
 * Import/export.
 */
Options::add_section(
	array(
		'id'       => 'import_export_section',
		'name'     => esc_html__( 'Import/export', 'xts-theme' ),
		'priority' => 150,
		'icon'     => 'xf-import-export',
	)
);
