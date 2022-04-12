<?php
/**
 * Framework main file
 *
 * @package xts
 */

namespace XTS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Layout;
use XTS\Framework\Activation;
use XTS\Framework\Admin;
use XTS\Framework\Dashboard;
use XTS\Framework\Modules;
use XTS\Framework\Options;
use XTS\Options\Page;
use XTS\Options\Styles;

/**
 * Main framework class.
 *
 * @package xts
 */
class Framework {
	/**
	 * Object of the Layout class for page layout data store.
	 *
	 * @var object
	 */
	public $layout;

	/**
	 * Styles.
	 *
	 * @var object
	 */
	public $styles;

	/**
	 * Instance of this static object.
	 *
	 * @var array
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @return object Current object instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Prevent singleton class clone.
	 *
	 * @since 1.0.0
	 */
	private function __clone() {
	}

	/**
	 * Prevent singleton class initialization.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
	}

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->define_constants();

		$this->include_files();

		$this->init_classes();

		if ( xts_is_elementor_installed() ) {
			add_action( 'init', array( $this, 'init_elementor' ), 20 );
		}
	}

	/**
	 * Define XTS Constants.
	 *
	 * @since 1.0.0
	 */
	public function define_constants() {
		$this->define( 'XTS_FRAMEWORK_VERSION', '1.1.0' );
		$this->define( 'XTS_FRAMEWORK_FILE', __FILE__ );
		$this->define( 'XTS_FRAMEWORK_ABSPATH', dirname( XTS_FRAMEWORK_FILE ) . '/' );
		$this->define( 'XTS_THEME_ABSPATH', XTS_ABSPATH . 'theme/' );
		$this->define( 'XTS_THEME_URL', get_template_directory_uri() );
		$this->define( 'XTS_FRAMEWORK_URL', XTS_THEME_URL . '/framework' );
		$this->define( 'XTS_INCLUDES_URL', XTS_THEME_URL . '/includes' );
		$this->define( 'XTS_SCRIPTS_URL', XTS_THEME_URL . '/js' );
		$this->define( 'XTS_STYLES_URL', XTS_THEME_URL . '/css' );
		$this->define( 'XTS_IMAGES_URL', XTS_THEME_URL . '/images' );
		$this->define( 'XTS_ELEMENTOR_URL', XTS_INCLUDES_URL . '/integration/elementor' );
		$this->define( 'XTS_THEME_DIR', get_template_directory() );
		$this->define( 'XTS_INCLUDES_DIR', XTS_THEME_DIR . '/includes' );
		$this->define( 'XTS_ASSETS_URL', XTS_FRAMEWORK_URL . '/assets' );
		$this->define( 'XTS_ASSETS_IMAGES_URL', XTS_ASSETS_URL . '/images' );
		$this->define( 'XTS_ELEMENTOR_DIR', XTS_INCLUDES_DIR . '/integration/elementor' );
		$this->define( 'XTS_ADMIN_DIR', XTS_INCLUDES_DIR . '/admin' );
		$this->define( 'XTS_SPACE_URL', 'https://space.xtemos.com/' );
		$this->define( 'XTS_DEMO_URL', XTS_SPACE_URL . 'demo/' );
		$this->define( 'XTS_DEMO_THEME_LIST_IMAGES_URL', XTS_SPACE_URL . 'wp-content/uploads/theme-list/' );
		$this->define( 'XTS_DOCS_URL', XTS_SPACE_URL . 'article/' );
		$this->define( 'XTS_API_URL', XTS_SPACE_URL . 'wp-json/xts/v1/' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, apply_filters( 'xts_define_constant', $value, $name ) );
		}
	}

	/**
	 * Include basic files.
	 *
	 * @since 1.0.0
	 */
	public function include_files() {
		require_once XTS_FRAMEWORK_ABSPATH . 'includes/classes/class-singleton.php';
		require_once XTS_FRAMEWORK_ABSPATH . 'includes/helpers.php';
		require_once XTS_FRAMEWORK_ABSPATH . 'includes/functions.php';
		require_once XTS_FRAMEWORK_ABSPATH . 'includes/theme-setup.php';
		require_once XTS_FRAMEWORK_ABSPATH . 'includes/enqueue.php';

		// Gutenberg.
		xts_get_file( 'framework/integration/gutenberg/functions' );

		// WPML.
		xts_get_file( 'framework/integration/wpml/functions' );

		// TGM.
		xts_get_file( 'framework/integration/tgm/class-tgm-plugin-activation' );

		// Woocommerce.
		xts_get_file( 'framework/integration/woocommerce/functions/global' );
		xts_get_file( 'framework/integration/woocommerce/functions/helpers' );
		xts_get_file( 'framework/integration/woocommerce/functions/loop-product' );
		xts_get_file( 'framework/integration/woocommerce/functions/single-product' );

		xts_get_file( 'framework/integration/woocommerce/template-tags/global' );
		xts_get_file( 'framework/integration/woocommerce/template-tags/single-product' );
		xts_get_file( 'framework/integration/woocommerce/template-tags/loop-product' );
		xts_get_file( 'framework/integration/woocommerce/template-tags/loop-category' );
		xts_get_file( 'framework/integration/woocommerce/template-tags/cart' );
		xts_get_file( 'framework/integration/woocommerce/template-tags/my-account' );
		xts_get_file( 'framework/integration/woocommerce/template-tags/page-title' );

		// Classes.
		xts_get_file( 'framework/includes/classes/class-notices' );
		xts_get_file( 'framework/includes/classes/class-api-client' );
		xts_get_file( 'framework/includes/classes/class-auto-updates' );
		xts_get_file( 'framework/includes/classes/class-config' );
		xts_get_file( 'framework/includes/classes/class-activation' );
		xts_get_file( 'framework/includes/classes/class-dashboard' );
		xts_get_file( 'framework/includes/classes/class-admin' );
		xts_get_file( 'framework/includes/classes/class-ajax-response' );
		xts_get_file( 'framework/includes/classes/class-layout' );
		xts_get_file( 'framework/includes/classes/class-google-fonts' );
		xts_get_file( 'framework/includes/classes/class-widget-base' );
		xts_get_file( 'framework/includes/classes/class-modules' );
		xts_get_file( 'framework/includes/classes/class-module' );
		xts_get_file( 'framework/includes/classes/class-theme-features' );
		xts_get_file( 'framework/includes/classes/class-walker-category' );
		xts_get_file( 'framework/includes/classes/class-styles-storage' );
		xts_get_file( 'framework/includes/classes/class-plugin-activation' );

		// Options classes.
		xts_get_file( 'framework/includes/options/class-sanitize' );
		xts_get_file( 'framework/includes/options/class-field' );
		xts_get_file( 'framework/includes/options/class-presets' );
		xts_get_file( 'framework/includes/options/class-styles' );

		// Options controls.
		xts_get_file( 'framework/includes/options/controls/select/class-select' );
		xts_get_file( 'framework/includes/options/controls/text-input/class-text-input' );
		xts_get_file( 'framework/includes/options/controls/switcher/class-switcher' );
		xts_get_file( 'framework/includes/options/controls/color/class-color' );
		xts_get_file( 'framework/includes/options/controls/checkbox/class-checkbox' );
		xts_get_file( 'framework/includes/options/controls/buttons/class-buttons' );
		xts_get_file( 'framework/includes/options/controls/upload/class-upload' );
		xts_get_file( 'framework/includes/options/controls/upload-list/class-upload-list' );
		xts_get_file( 'framework/includes/options/controls/background/class-background' );
		xts_get_file( 'framework/includes/options/controls/textarea/class-textarea' );
		xts_get_file( 'framework/includes/options/controls/image-dimensions/class-image-dimensions' );
		xts_get_file( 'framework/includes/options/controls/typography/class-typography' );
		xts_get_file( 'framework/includes/options/controls/custom-fonts/class-custom-fonts' );
		xts_get_file( 'framework/includes/options/controls/range/class-range' );
		xts_get_file( 'framework/includes/options/controls/editor/class-editor' );
		xts_get_file( 'framework/includes/options/controls/import/class-import' );
		xts_get_file( 'framework/includes/options/controls/size-guide-table/class-size-guide-table' );
		xts_get_file( 'framework/includes/options/controls/notice/class-notice' );
		xts_get_file( 'framework/includes/options/controls/instagram-api/class-instagram-api' );

		xts_get_file( 'framework/includes/options/class-options' );
		xts_get_file( 'framework/includes/options/class-page' );
		xts_get_file( 'framework/includes/options/class-metabox' );
		xts_get_file( 'framework/includes/options/class-metaboxes' );

		// Post types.
		xts_get_file( 'framework/includes/post-types/portfolio' );
		xts_get_file( 'framework/includes/post-types/html-block' );
		xts_get_file( 'framework/includes/post-types/slider' );
		xts_get_file( 'framework/includes/post-types/sidebar' );

		// Widgets.
		xts_get_file( 'framework/includes/widgets/class-search' );
		xts_get_file( 'framework/includes/widgets/class-html-block' );
		xts_get_file( 'framework/includes/widgets/class-instagram' );
		xts_get_file( 'framework/includes/widgets/class-recent-posts' );
		xts_get_file( 'framework/includes/widgets/class-wc-layered-nav' );
		xts_get_file( 'framework/includes/widgets/class-wc-price-filter' );
		xts_get_file( 'framework/includes/widgets/class-wc-sort-by' );
		xts_get_file( 'framework/includes/widgets/class-wc-stock-status' );
		xts_get_file( 'framework/includes/widgets/class-twitter' );
		xts_get_file( 'framework/includes/widgets/class-image' );
		xts_get_file( 'framework/includes/widgets/class-menu' );
		xts_get_file( 'framework/includes/widgets/class-social-buttons' );

		// Modules.
		// TODO: Move this include to header builder module.
		xts_get_file( 'framework/modules/header-builder/functions' );
		if ( xts_is_build_for_space() ) {
			Modules::register( 'core', false );
		}
		Modules::register( 'header-builder' );
		Modules::register( 'mega-menu' );
		Modules::register( 'search' );
		Modules::register( 'lazy-loading' );
		Modules::register( 'dummy-content' );
		Modules::register( 'maintenance' );
		Modules::register( 'header-banner' );
		Modules::register( 'sticky-bottom-navbar' );
		Modules::register( 'preloader' );
		Modules::register( 'white-label' );

		Modules::register( 'wc-variations-swatches' );
		Modules::register( 'wc-catalog-mode' );
		Modules::register( 'wc-builder' );
		Modules::register( 'wc-login-to-see-price' );
		Modules::register( 'wc-brands' );
		Modules::register( 'wc-compare' );
		Modules::register( 'wc-wishlist' );
		Modules::register( 'wc-size-guide' );
		Modules::register( 'wc-product-countdown' );
		Modules::register( 'wc-quick-view' );
		Modules::register( 'wc-additional-variation-images' );
		Modules::register( 'wc-stock-progress-bar' );
		Modules::register( 'wc-comment-images' );
		Modules::register( 'wc-brands-background' );
		Modules::register( 'wc-my-account-links' );
		Modules::register( 'wc-mini-cart-quantity' );
		Modules::register( 'wc-product-loop-quantity' );
		if ( apply_filters( 'xts_demo_preview_panel', false ) ) {
			Modules::register( 'demo-preview-panel' );
		}

		// Header builder elements.
		xts_get_file( 'framework/modules/header-builder/elements/burger/class-burger' );
		xts_get_file( 'framework/modules/header-builder/elements/column/class-column' );
		xts_get_file( 'framework/modules/header-builder/elements/cart/class-cart' );
		xts_get_file( 'framework/modules/header-builder/elements/divider/class-divider' );
		xts_get_file( 'framework/modules/header-builder/elements/html-block/class-html-block' );
		xts_get_file( 'framework/modules/header-builder/elements/logo/class-logo' );
		xts_get_file( 'framework/modules/header-builder/elements/main-menu/class-main-menu' );
		xts_get_file( 'framework/modules/header-builder/elements/menu/class-menu' );
		xts_get_file( 'framework/modules/header-builder/elements/mobile-search/class-mobile-search' );
		xts_get_file( 'framework/modules/header-builder/elements/social-buttons/class-social-buttons' );
		xts_get_file( 'framework/modules/header-builder/elements/button/class-button' );
		xts_get_file( 'framework/modules/header-builder/elements/root/class-root' );
		xts_get_file( 'framework/modules/header-builder/elements/row/class-row' );
		xts_get_file( 'framework/modules/header-builder/elements/search/class-search' );
		xts_get_file( 'framework/modules/header-builder/elements/space/class-space' );
		xts_get_file( 'framework/modules/header-builder/elements/text/class-text' );
		xts_get_file( 'framework/modules/header-builder/elements/my-account/class-my-account' );
		xts_get_file( 'framework/modules/header-builder/elements/compare/class-compare' );
		xts_get_file( 'framework/modules/header-builder/elements/wishlist/class-wishlist' );
		xts_get_file( 'framework/modules/header-builder/elements/categories/class-categories' );

		Options::get_instance();

		// Options.
		xts_get_file( 'framework/options/sections' );
		xts_get_file( 'framework/options/product-archive' );
		xts_get_file( 'framework/options/general' );
		xts_get_file( 'framework/options/page-title' );
		xts_get_file( 'framework/options/footer' );
		xts_get_file( 'framework/options/blog' );
		xts_get_file( 'framework/options/portfolio' );
		xts_get_file( 'framework/options/performance' );
		xts_get_file( 'framework/options/social-profiles' );
		xts_get_file( 'framework/options/typography' );
		xts_get_file( 'framework/options/custom-css' );
		xts_get_file( 'framework/options/custom-js' );
		xts_get_file( 'framework/options/colors' );
		xts_get_file( 'framework/options/import' );
		xts_get_file( 'framework/options/shop' );
		xts_get_file( 'framework/options/single-product-page' );
		xts_get_file( 'framework/options/api-integrations' );
		xts_get_file( 'framework/options/miscellaneous' );

		// Metaboxes.
		xts_get_file( 'framework/metaboxes/general' );
		xts_get_file( 'framework/metaboxes/categories' );
		xts_get_file( 'framework/metaboxes/slider' );
		xts_get_file( 'framework/metaboxes/post-formats' );
		xts_get_file( 'framework/metaboxes/wc-attributes' );
		xts_get_file( 'framework/metaboxes/product' );
		xts_get_file( 'framework/metaboxes/product-categories' );
		xts_get_file( 'framework/metaboxes/html-block' );

		// Elements.
		xts_get_file( 'templates/elementor/portfolio' );
		xts_get_file( 'templates/elementor/contact-form-7' );
		xts_get_file( 'templates/elementor/mailchimp' );
		xts_get_file( 'templates/elementor/single-product/add-to-cart' );
		xts_get_file( 'templates/elementor/single-product/badges' );
		xts_get_file( 'templates/elementor/single-product/breadcrumb' );
		xts_get_file( 'templates/elementor/single-product/brands' );
		xts_get_file( 'templates/elementor/single-product/excerpt' );
		xts_get_file( 'templates/elementor/single-product/gallery' );
		xts_get_file( 'templates/elementor/single-product/hook' );
		xts_get_file( 'templates/elementor/single-product/meta' );
		xts_get_file( 'templates/elementor/single-product/notices' );
		xts_get_file( 'templates/elementor/single-product/price' );
		xts_get_file( 'templates/elementor/single-product/rating' );
		xts_get_file( 'templates/elementor/single-product/tabs' );
		xts_get_file( 'templates/elementor/single-product/title' );
		xts_get_file( 'templates/elementor/single-product/nav' );
		xts_get_file( 'templates/elementor/single-product/countdown' );
		xts_get_file( 'templates/elementor/single-product/stock-progress-bar' );
		xts_get_file( 'templates/elementor/single-product/additional-info-table' );
		xts_get_file( 'templates/elementor/single-product/reviews' );
		xts_get_file( 'templates/elementor/single-product/description' );
		xts_get_file( 'templates/elementor/single-product/action-buttons' );

		xts_get_file( 'templates/elementor/accordion' );
		xts_get_file( 'templates/elementor/banner' );
		xts_get_file( 'templates/elementor/blog' );
		xts_get_file( 'templates/elementor/product-brands' );
		xts_get_file( 'templates/elementor/extra-menu-list' );
		xts_get_file( 'templates/elementor/google-map' );
		xts_get_file( 'templates/elementor/hotspots' );
		xts_get_file( 'templates/elementor/html-block' );
		xts_get_file( 'templates/elementor/image-gallery' );
		xts_get_file( 'templates/elementor/infobox' );
		xts_get_file( 'templates/elementor/price-plan' );
		xts_get_file( 'templates/elementor/price-plan-switcher' );
		xts_get_file( 'templates/elementor/slider' );
		xts_get_file( 'templates/elementor/button' );
		xts_get_file( 'templates/elementor/social-buttons' );
		xts_get_file( 'templates/elementor/countdown-timer' );
		xts_get_file( 'templates/elementor/instagram' );
		xts_get_file( 'templates/elementor/team-member' );
		xts_get_file( 'templates/elementor/testimonials' );
		xts_get_file( 'templates/elementor/title' );
		xts_get_file( 'templates/elementor/image' );
		xts_get_file( 'templates/elementor/popup' );
		xts_get_file( 'templates/elementor/search' );
		xts_get_file( 'templates/elementor/tabs' );
		xts_get_file( 'templates/elementor/blockquote' );
		xts_get_file( 'templates/elementor/table' );
		xts_get_file( 'templates/elementor/timeline' );
		xts_get_file( 'templates/elementor/view-360' );
		xts_get_file( 'templates/elementor/twitter' );
		xts_get_file( 'templates/elementor/circle-progress' );
		xts_get_file( 'templates/elementor/animated-text' );
		xts_get_file( 'templates/elementor/products' );
		xts_get_file( 'templates/elementor/product-tabs' );
		xts_get_file( 'templates/elementor/compare' );
		xts_get_file( 'templates/elementor/wishlist' );
		xts_get_file( 'templates/elementor/product-categories' );
		xts_get_file( 'templates/elementor/menu-price' );
		xts_get_file( 'templates/elementor/mega-menu' );
		xts_get_file( 'templates/elementor/menu-anchor' );
		xts_get_file( 'templates/elementor/video' );
		xts_get_file( 'templates/elementor/shape' );
		xts_get_file( 'templates/elementor/size-guide' );
		xts_get_file( 'templates/elementor/working-hours' );

		// Template tags.
		xts_get_file( 'framework/template-tags/blog' );
		xts_get_file( 'framework/template-tags/footer' );
		xts_get_file( 'framework/template-tags/global' );
		xts_get_file( 'framework/template-tags/header' );
		xts_get_file( 'framework/template-tags/page-title' );
		xts_get_file( 'framework/template-tags/portfolio' );
		xts_get_file( 'framework/template-tags/search' );
	}

	/**
	 * Include elementor files.
	 *
	 * @since 1.0.0
	 */
	public function init_elementor() {
		// Product elements.
		xts_get_file( 'framework/integration/elementor/elements/single-product/action-buttons/class-action-buttons' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/add-to-cart/class-add-to-cart' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/additional-info-table/class-additional-info-table' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/badges/class-badges' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/breadcrumb/class-breadcrumb' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/countdown/class-countdown' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/description/class-description' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/excerpt/class-excerpt' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/gallery/class-gallery' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/hook/class-hook' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/meta/class-meta' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/nav/class-nav' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/notices/class-notices' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/price/class-price' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/rating/class-rating' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/reviews/class-reviews' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/stock-progress-bar/class-stock-progress-bar' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/tabs/class-tabs' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/title/class-title' );
		xts_get_file( 'framework/integration/elementor/elements/single-product/brands/class-brands' );

		// Default elements.
		xts_get_file( 'framework/integration/elementor/default-elements/column' );
		xts_get_file( 'framework/integration/elementor/default-elements/common' );
		xts_get_file( 'framework/integration/elementor/default-elements/section' );
		xts_get_file( 'framework/integration/elementor/default-elements/text-editor' );
		xts_get_file( 'framework/integration/elementor/default-elements/slider-revolution' );

		// Global maps.
		xts_get_file( 'framework/integration/elementor/global-carousel' );
		xts_get_file( 'framework/integration/elementor/global-maps' );

		// Controls.
		xts_get_file( 'framework/integration/elementor/controls/class-autocomplete' );
		xts_get_file( 'framework/integration/elementor/controls/class-buttons' );
		xts_get_file( 'framework/integration/elementor/controls/class-google-json' );

		// Elements.
		xts_get_file( 'framework/integration/elementor/elements/accordion/class-accordion' );
		xts_get_file( 'framework/integration/elementor/elements/animated-text/class-animated-text' );
		xts_get_file( 'framework/integration/elementor/elements/banner/class-banner' );
		xts_get_file( 'framework/integration/elementor/elements/banner/class-banner-carousel' );
		xts_get_file( 'framework/integration/elementor/elements/banner/global-banner' );
		xts_get_file( 'framework/integration/elementor/elements/blog/class-blog' );
		xts_get_file( 'framework/integration/elementor/elements/button/class-button' );
		xts_get_file( 'framework/integration/elementor/elements/button/global-button' );
		xts_get_file( 'framework/integration/elementor/elements/blockquote/class-blockquote' );
		xts_get_file( 'framework/integration/elementor/elements/product-brands/class-product-brands' );
		xts_get_file( 'framework/integration/elementor/elements/contact-form-7/class-contact-form-7' );
		xts_get_file( 'framework/integration/elementor/elements/countdown-timer/class-countdown-timer' );
		xts_get_file( 'framework/integration/elementor/elements/circle-progress/class-circle-progress' );
		xts_get_file( 'framework/integration/elementor/elements/compare/class-compare' );
		xts_get_file( 'framework/integration/elementor/elements/product-categories/class-product-categories' );
		xts_get_file( 'framework/integration/elementor/elements/extra-menu-list/class-extra-menu-list' );
		xts_get_file( 'framework/integration/elementor/elements/google-map/class-google-map' );
		xts_get_file( 'framework/integration/elementor/elements/hotspots/class-hotspots' );
		xts_get_file( 'framework/integration/elementor/elements/html-block/class-html-block' );
		xts_get_file( 'framework/integration/elementor/elements/image-gallery/class-image-gallery' );
		xts_get_file( 'framework/integration/elementor/elements/image/class-image' );
		xts_get_file( 'framework/integration/elementor/elements/infobox/class-infobox' );
		xts_get_file( 'framework/integration/elementor/elements/infobox/class-infobox-carousel' );
		xts_get_file( 'framework/integration/elementor/elements/infobox/global-infobox' );
		xts_get_file( 'framework/integration/elementor/elements/instagram/class-instagram' );
		xts_get_file( 'framework/integration/elementor/elements/mailchimp/class-mailchimp' );
		xts_get_file( 'framework/integration/elementor/elements/menu-price/class-menu-price' );
		xts_get_file( 'framework/integration/elementor/elements/mega-menu/class-mega-menu' );
		xts_get_file( 'framework/integration/elementor/elements/menu-anchor/class-menu-anchor' );
		xts_get_file( 'framework/integration/elementor/elements/portfolio/class-portfolio' );
		xts_get_file( 'framework/integration/elementor/elements/price-plan/class-price-plan' );
		xts_get_file( 'framework/integration/elementor/elements/price-plan/class-price-plan-switcher' );
		xts_get_file( 'framework/integration/elementor/elements/products/class-products' );
		xts_get_file( 'framework/integration/elementor/elements/product-tabs/class-product-tabs' );
		xts_get_file( 'framework/integration/elementor/elements/popup/class-popup' );
		xts_get_file( 'framework/integration/elementor/elements/slider/class-slider' );
		xts_get_file( 'framework/integration/elementor/elements/search/class-search' );
		xts_get_file( 'framework/integration/elementor/elements/social-buttons/class-social-buttons' );
		xts_get_file( 'framework/integration/elementor/elements/social-buttons/global-social-buttons' );
		xts_get_file( 'framework/integration/elementor/elements/shape/class-shape' );
		xts_get_file( 'framework/integration/elementor/elements/size-guide/class-size-guide' );
		xts_get_file( 'framework/integration/elementor/elements/team-member/class-team-member' );
		xts_get_file( 'framework/integration/elementor/elements/testimonials/class-testimonials' );
		xts_get_file( 'framework/integration/elementor/elements/tabs/class-tabs' );
		xts_get_file( 'framework/integration/elementor/elements/title/class-title' );
		xts_get_file( 'framework/integration/elementor/elements/table/class-table' );
		xts_get_file( 'framework/integration/elementor/elements/timeline/class-timeline' );
		xts_get_file( 'framework/integration/elementor/elements/twitter/class-twitter' );
		xts_get_file( 'framework/integration/elementor/elements/view-360/class-view-360' );
		xts_get_file( 'framework/integration/elementor/elements/video/class-video' );
		xts_get_file( 'framework/integration/elementor/elements/video/global-video' );
		xts_get_file( 'framework/integration/elementor/elements/wishlist/class-wishlist' );
		xts_get_file( 'framework/integration/elementor/elements/working-hours/class-working-hours' );

		// Template library.
		if ( xts_is_build_for_space() ) {
			xts_get_file( 'framework/integration/elementor/template-library/class-library-source' );
			xts_get_file( 'framework/integration/elementor/template-library/class-library' );
		}

		xts_get_file( 'framework/integration/elementor/elementor' );
	}

	/**
	 * Init classes.
	 *
	 * @since 1.0.0
	 */
	public function init_classes() {
		Admin::get_instance();
		Dashboard::get_instance();
		Page::get_instance();
		Activation::get_instance();
		Auto_Updates::get_instance();

		$this->layout = new Layout();
		$this->styles = new Styles();
	}
}

Framework::get_instance();


