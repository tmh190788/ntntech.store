<?php
/**
 * JS scripts.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return array(
	'scripts'                             => array(
		array(
			'title'     => esc_html__( 'Helpers', 'xts-theme' ),
			'name'      => 'scripts',
			'file'      => '/js/scripts/helpers',
			'in_footer' => true,
		),
	),
	'accordion-element'                   => array(
		array(
			'title'     => esc_html__( 'Accordion element init', 'xts-theme' ),
			'name'      => 'accordion-element',
			'file'      => '/js/scripts/accordionElement',
			'in_footer' => true,
		),
	),
	'animated-text-element'               => array(
		array(
			'title'     => esc_html__( 'Animated text element init', 'xts-theme' ),
			'name'      => 'animated-text-element',
			'file'      => '/js/scripts/animatedTextElement',
			'in_footer' => true,
		),
	),
	'button-element-smooth-scroll'        => array(
		array(
			'title'     => esc_html__( 'Button element smooth scroll script', 'xts-theme' ),
			'name'      => 'button-element-smooth-scroll',
			'file'      => '/js/scripts/buttonSmoothScroll',
			'in_footer' => true,
		),
	),
	'circle-progress-bar-element'         => array(
		array(
			'title'     => esc_html__( 'Circle progress bar element init', 'xts-theme' ),
			'name'      => 'circle-progress-bar-element',
			'file'      => '/js/scripts/circleProgressBarElement',
			'in_footer' => true,
		),
	),
	'threesixty'                          => array(
		array(
			'title'     => esc_html__( '360 element init', 'xts-theme' ),
			'name'      => 'threesixty',
			'file'      => '/js/scripts/threeSixty',
			'in_footer' => true,
		),
	),
	'tabs-element'                        => array(
		array(
			'title'     => esc_html__( 'Tabs element init', 'xts-theme' ),
			'name'      => 'tabs-element',
			'file'      => '/js/scripts/tabsElement',
			'in_footer' => true,
		),
	),
	'popup-element'                       => array(
		array(
			'title'     => esc_html__( 'Popup element init', 'xts-theme' ),
			'name'      => 'popup-element',
			'file'      => '/js/scripts/popupElement',
			'in_footer' => true,
		),
	),
	'video-element'                       => array(
		array(
			'title'     => esc_html__( 'Video element init', 'xts-theme' ),
			'name'      => 'video-element',
			'file'      => '/js/scripts/videoElement',
			'in_footer' => true,
		),
	),
	'video-element-popup'                 => array(
		array(
			'title'     => esc_html__( 'Video element popup', 'xts-theme' ),
			'name'      => 'video-element-popup',
			'file'      => '/js/scripts/videoElementPopup',
			'in_footer' => true,
		),
	),
	'countdown-timer-element'             => array(
		array(
			'title'     => esc_html__( 'Countdown timer element init', 'xts-theme' ),
			'name'      => 'countdown-timer-element',
			'file'      => '/js/scripts/countDownTimerElement',
			'in_footer' => true,
		),
	),
	'google-map-element'                  => array(
		array(
			'title'     => esc_html__( 'Google map element init', 'xts-theme' ),
			'name'      => 'google-map-element',
			'file'      => '/js/scripts/googleMapElement',
			'in_footer' => true,
		),
	),
	'hotspots-element'                    => array(
		array(
			'title'     => esc_html__( 'Hotspots element init', 'xts-theme' ),
			'name'      => 'hotspots-element',
			'file'      => '/js/scripts/hotSpotsElement',
			'in_footer' => true,
		),
	),
	'price-plan-switcher-element'         => array(
		array(
			'title'     => esc_html__( 'Price plan element init', 'xts-theme' ),
			'name'      => 'price-plan-switcher-element',
			'file'      => '/js/scripts/pricePlanSwitcherElement',
			'in_footer' => true,
		),
	),
	'slider-element'                      => array(
		array(
			'title'     => esc_html__( 'Slider element init', 'xts-theme' ),
			'name'      => 'slider-element',
			'file'      => '/js/scripts/sliderElement',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Flickity init script', 'xts-theme' ),
			'name'      => 'flickity-init-method',
			'file'      => '/js/scripts/carouselInitFlickity',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Video proportional script', 'xts-theme' ),
			'name'      => 'video-size-method',
			'file'      => '/js/scripts/calcVideoSize',
			'in_footer' => true,
		),
	),
	'post-video-controls'                 => array(
		array(
			'title'     => esc_html__( 'Post video controls script', 'xts-theme' ),
			'name'      => 'post-video-controls',
			'file'      => '/js/scripts/postVideoControls',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Video proportional script', 'xts-theme' ),
			'name'      => 'video-size-method',
			'file'      => '/js/scripts/calcVideoSize',
			'in_footer' => true,
		),
	),
	'image-gallery-element'               => array(
		array(
			'title'     => esc_html__( 'Photoswipe script', 'xts-theme' ),
			'name'      => 'photoswipe-method',
			'file'      => '/js/scripts/callPhotoSwipe',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Image gallery element init', 'xts-theme' ),
			'name'      => 'image-gallery-element',
			'file'      => '/js/scripts/imageGalleryElement',
			'in_footer' => true,
		),
	),
	'image-element'                       => array(
		array(
			'title'     => esc_html__( 'Photoswipe script', 'xts-theme' ),
			'name'      => 'photoswipe-method',
			'file'      => '/js/scripts/callPhotoSwipe',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Image element init', 'xts-theme' ),
			'name'      => 'image-element',
			'file'      => '/js/scripts/imageElement',
			'in_footer' => true,
		),
	),
	'masonry-layout'                      => array(
		array(
			'title'     => esc_html__( 'Masonry layout init', 'xts-theme' ),
			'name'      => 'masonry-layout-method',
			'file'      => '/js/scripts/masonryLayout',
			'in_footer' => true,
		),
	),
	'blog-load-more'                      => array(
		array(
			'title'     => esc_html__( 'Blog load more button script', 'xts-theme' ),
			'name'      => 'blog-load-more-method',
			'file'      => '/js/scripts/blogLoadMore',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Click on load more button script', 'xts-theme' ),
			'name'      => 'click-on-scroll-button-method',
			'file'      => '/js/scripts/clickOnScrollButton',
			'in_footer' => true,
		),
	),
	'shop-load-more'                      => array(
		array(
			'title'     => esc_html__( 'Shop load more button script', 'xts-theme' ),
			'name'      => 'shop-load-more-method',
			'file'      => '/js/scripts/productsLoadMore',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Click on load more button script', 'xts-theme' ),
			'name'      => 'click-on-scroll-button-method',
			'file'      => '/js/scripts/clickOnScrollButton',
			'in_footer' => true,
		),
	),
	'portfolio-load-more'                 => array(
		array(
			'title'     => esc_html__( 'Portfolio load more button script', 'xts-theme' ),
			'name'      => 'portfolio-load-more-method',
			'file'      => '/js/scripts/portfolioLoadMore',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Click on load more button script', 'xts-theme' ),
			'name'      => 'click-on-scroll-button-method',
			'file'      => '/js/scripts/clickOnScrollButton',
			'in_footer' => true,
		),
	),
	'menu-click-event'                    => array(
		array(
			'title'     => esc_html__( 'Menu click script', 'xts-theme' ),
			'name'      => 'menu-click-event-method',
			'file'      => '/js/scripts/menuClickEvent',
			'in_footer' => true,
		),
	),
	'menu-dropdown-ajax'                  => array(
		array(
			'title'     => esc_html__( 'Menu AJAX dropdown script', 'xts-theme' ),
			'name'      => 'menu-dropdown-ajax-method',
			'file'      => '/js/scripts/menuDropdownsAJAX',
			'in_footer' => true,
		),
	),
	'menu-offsets'                        => array(
		array(
			'title'     => esc_html__( 'Menu dropdown offsets script', 'xts-theme' ),
			'name'      => 'menu-offsets-method',
			'file'      => '/js/scripts/menuOffsets',
			'in_footer' => true,
		),
	),
	'menu-mobile'                         => array(
		array(
			'title'     => esc_html__( 'Menu mobile script', 'xts-theme' ),
			'name'      => 'menu-mobile-method',
			'file'      => '/js/scripts/mobileNavigation',
			'in_footer' => true,
		),
	),
	'menu-one-page'                       => array(
		array(
			'title'     => esc_html__( 'Menu one page script', 'xts-theme' ),
			'name'      => 'menu-one-page-method',
			'file'      => '/js/scripts/onePageMenu',
			'in_footer' => true,
		),
	),
	'ajax-search'                         => array(
		array(
			'title'     => esc_html__( 'AJAX search init', 'xts-theme' ),
			'name'      => 'ajax-search-method',
			'file'      => '/js/scripts/ajaxSearch',
			'in_footer' => true,
		),
	),
	'animations'                          => array(
		array(
			'title'     => esc_html__( 'Animations script', 'xts-theme' ),
			'name'      => 'animations-method',
			'file'      => '/js/scripts/animations',
			'in_footer' => true,
		),
	),
	'items-animation-in-view'             => array(
		array(
			'title'     => esc_html__( 'Animations in view script', 'xts-theme' ),
			'name'      => 'items-animation-in-view-method',
			'file'      => '/js/scripts/itemsAnimationInView',
			'in_footer' => true,
		),
	),
	'swiper-init'                         => array(
		array(
			'title'     => esc_html__( 'Swiper init script', 'xts-theme' ),
			'name'      => 'swiper-init-method',
			'file'      => '/js/scripts/carouselInitSwiper',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Swiper structure script', 'xts-theme' ),
			'name'      => 'add-swiper-structure-method',
			'file'      => '/js/scripts/addSwiperStructure',
			'in_footer' => true,
		),
	),
	'cookies-popup'                       => array(
		array(
			'title'     => esc_html__( 'Cookies popup script', 'xts-theme' ),
			'name'      => 'cookies-popup-method',
			'file'      => '/js/scripts/cookiesPopup',
			'in_footer' => true,
		),
	),
	'header-banner'                       => array(
		array(
			'title'     => esc_html__( 'Header banner script', 'xts-theme' ),
			'name'      => 'header-banner-method',
			'file'      => '/js/scripts/headerBanner',
			'in_footer' => true,
		),
	),
	'header-builder'                      => array(
		array(
			'title'     => esc_html__( 'Header builder script', 'xts-theme' ),
			'name'      => 'header-builder-method',
			'file'      => '/js/scripts/headerBuilder',
			'in_footer' => true,
		),
	),
	'hide-notices'                        => array(
		array(
			'title'     => esc_html__( 'Hide notices script', 'xts-theme' ),
			'name'      => 'hide-notices-method',
			'file'      => '/js/scripts/hideNotices',
			'in_footer' => true,
		),
	),
	'lazy-loading'                        => array(
		array(
			'title'     => esc_html__( 'Lazy loading script', 'xts-theme' ),
			'name'      => 'lazy-loading-method',
			'file'      => '/js/scripts/lazyLoading',
			'in_footer' => true,
		),
	),
	'more-categories-button'              => array(
		array(
			'title'     => esc_html__( 'More categories button script', 'xts-theme' ),
			'name'      => 'more-categories-button-method',
			'file'      => '/js/scripts/moreCategoriesButton',
			'in_footer' => true,
		),
	),
	'offcanvas-sidebar'                   => array(
		array(
			'title'     => esc_html__( 'Offcanvas sidebar script', 'xts-theme' ),
			'name'      => 'offcanvas-sidebar-method',
			'file'      => '/js/scripts/offCanvasSidebar',
			'in_footer' => true,
		),
	),
	'page-title-effect'                   => array(
		array(
			'title'     => esc_html__( 'Page title scroll effect script', 'xts-theme' ),
			'name'      => 'page-title-effect-method',
			'file'      => '/js/scripts/pageTitleEffect',
			'in_footer' => true,
		),
	),
	'parallax-3d'                         => array(
		array(
			'title'     => esc_html__( 'Parallax 3D script', 'xts-theme' ),
			'name'      => 'parallax-3d-method',
			'file'      => '/js/scripts/parallax3d',
			'in_footer' => true,
		),
	),
	'promo-popup'                         => array(
		array(
			'title'     => esc_html__( 'Promo popup script', 'xts-theme' ),
			'name'      => 'promo-popup-method',
			'file'      => '/js/scripts/promoPopup',
			'in_footer' => true,
		),
	),
	'scroll-to-top'                       => array(
		array(
			'title'     => esc_html__( 'Scroll to top button script', 'xts-theme' ),
			'name'      => 'scroll-to-top-method',
			'file'      => '/js/scripts/scrollTopButton',
			'in_footer' => true,
		),
	),
	'search-dropdown'                     => array(
		array(
			'title'     => esc_html__( 'Search dropdown script', 'xts-theme' ),
			'name'      => 'search-dropdown-method',
			'file'      => '/js/scripts/searchDropdown',
			'in_footer' => true,
		),
	),
	'search-full-screen'                  => array(
		array(
			'title'     => esc_html__( 'Search full screen script', 'xts-theme' ),
			'name'      => 'search-full-screen-method',
			'file'      => '/js/scripts/searchElement',
			'in_footer' => true,
		),
	),
	'sticky-column'                       => array(
		array(
			'title'     => esc_html__( 'Sticky column script', 'xts-theme' ),
			'name'      => 'sticky-column-method',
			'file'      => '/js/scripts/stickyColumn',
			'in_footer' => true,
		),
	),
	'sticky-sidebar'                      => array(
		array(
			'title'     => esc_html__( 'Sticky sidebar script', 'xts-theme' ),
			'name'      => 'sticky-sidebar-method',
			'file'      => '/js/scripts/stickySidebar',
			'in_footer' => true,
		),
	),
	'sticky-loader-position'              => array(
		array(
			'title'     => esc_html__( 'Sticky loader position script', 'xts-theme' ),
			'name'      => 'sticky-loader-position-method',
			'file'      => '/js/scripts/stickyLoaderPosition',
			'in_footer' => true,
		),
	),
	'tooltip'                             => array(
		array(
			'title'     => esc_html__( 'Tooltip script', 'xts-theme' ),
			'name'      => 'tooltip-method',
			'file'      => '/js/scripts/tooltip',
			'in_footer' => true,
		),
	),
	'widget-collapse'                     => array(
		array(
			'title'     => esc_html__( 'Widgets collapse script', 'xts-theme' ),
			'name'      => 'widget-collapse-method',
			'file'      => '/js/scripts/widgetsCollapse',
			'in_footer' => true,
		),
	),
	'ajax-portfolio'                      => array(
		array(
			'title'     => esc_html__( 'AJAX portfolio script', 'xts-theme' ),
			'name'      => 'ajax-portfolio-method',
			'file'      => '/js/scripts/ajaxPortfolio',
			'in_footer' => true,
		),
	),
	'ajax-shop'                           => array(
		array(
			'title'     => esc_html__( 'AJAX shop script', 'xts-theme' ),
			'name'      => 'ajax-shop-method',
			'file'      => '/js/scripts/ajaxShop',
			'in_footer' => true,
		),
	),
	'portfolio-filters'                   => array(
		array(
			'title'     => esc_html__( 'Portfolio filters script', 'xts-theme' ),
			'name'      => 'portfolio-filters-method',
			'file'      => '/js/scripts/portfolioFilters',
			'in_footer' => true,
		),
	),
	'portfolio-photoswipe'                => array(
		array(
			'title'     => esc_html__( 'Photoswipe script', 'xts-theme' ),
			'name'      => 'photoswipe-method',
			'file'      => '/js/scripts/callPhotoSwipe',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Portfolio photoswipe script', 'xts-theme' ),
			'name'      => 'portfolio-photoswipe-method',
			'file'      => '/js/scripts/portfolioPhotoSwipe',
			'in_footer' => true,
		),
	),
	'action-after-add-to-cart'            => array(
		array(
			'title'     => esc_html__( 'Action after add to cart script', 'xts-theme' ),
			'name'      => 'action-after-add-to-cart-method',
			'file'      => '/js/scripts/actionAfterAddToCart',
			'in_footer' => true,
		),
	),
	'comment-images'                      => array(
		array(
			'title'     => esc_html__( 'Reviews images script', 'xts-theme' ),
			'name'      => 'comment-images-method',
			'file'      => '/js/scripts/commentImage',
			'in_footer' => true,
		),
	),
	'shop-filters-area'                   => array(
		array(
			'title'     => esc_html__( 'Shop filters area script', 'xts-theme' ),
			'name'      => 'shop-filters-area-method',
			'file'      => '/js/scripts/filtersArea',
			'in_footer' => true,
		),
	),
	'grid-swatches'                       => array(
		array(
			'title'     => esc_html__( 'Grid swatches script', 'xts-theme' ),
			'name'      => 'grid-swatches-method',
			'file'      => '/js/scripts/gridSwatches',
			'in_footer' => true,
		),
	),
	'layered-nav-dropdown'                => array(
		array(
			'title'     => esc_html__( 'Layered nav dropdown script', 'xts-theme' ),
			'name'      => 'layered-nav-dropdown-method',
			'file'      => '/js/scripts/layeredNavDropdown',
			'in_footer' => true,
		),
	),
	'mini-cart-quantity'                  => array(
		array(
			'title'     => esc_html__( 'Mini cart quantity script', 'xts-theme' ),
			'name'      => 'mini-cart-quantity-method',
			'file'      => '/js/scripts/miniCartQuantity',
			'in_footer' => true,
		),
	),
	'offcanvas-cart-widget'               => array(
		array(
			'title'     => esc_html__( 'Offcanvas cart widget script', 'xts-theme' ),
			'name'      => 'offcanvas-cart-widget-method',
			'file'      => '/js/scripts/offCanvasCartWidget',
			'in_footer' => true,
		),
	),
	'offcanvas-my-account'                => array(
		array(
			'title'     => esc_html__( 'Offcanvas my account script', 'xts-theme' ),
			'name'      => 'offcanvas-my-account-method',
			'file'      => '/js/scripts/offCanvasMyAccount',
			'in_footer' => true,
		),
	),
	'page-title-product-categories'       => array(
		array(
			'title'     => esc_html__( 'Page title product categories script', 'xts-theme' ),
			'name'      => 'page-title-product-categories-method',
			'file'      => '/js/scripts/pageTitleProductCategories',
			'in_footer' => true,
		),
	),
	'product-categories-widget-accordion' => array(
		array(
			'title'     => esc_html__( 'Product categories widget accordion script', 'xts-theme' ),
			'name'      => 'product-categories-widget-accordion-method',
			'file'      => '/js/scripts/productCategoriesWidgetAccordion',
			'in_footer' => true,
		),
	),
	'product-hover-summary'               => array(
		array(
			'title'     => esc_html__( 'Product hover summary script', 'xts-theme' ),
			'name'      => 'product-hover-summary-method',
			'file'      => '/js/scripts/productHoverSummary',
			'in_footer' => true,
		),
	),
	'product-loop-quantity'               => array(
		array(
			'title'     => esc_html__( 'Product loop quantity script', 'xts-theme' ),
			'name'      => 'product-loop-quantity-method',
			'file'      => '/js/scripts/productLoopQuantity',
			'in_footer' => true,
		),
	),
	'product-quick-view'                  => array(
		array(
			'title'     => esc_html__( 'Product quick view script', 'xts-theme' ),
			'name'      => 'product-quick-view-method',
			'file'      => '/js/scripts/productQuickView',
			'in_footer' => true,
		),
	),
	'product-compare'                     => array(
		array(
			'title'     => esc_html__( 'Product compare script', 'xts-theme' ),
			'name'      => 'product-compare-method',
			'file'      => '/js/scripts/productsCompare',
			'in_footer' => true,
		),
	),
	'product-tabs-element'                => array(
		array(
			'title'     => esc_html__( 'Product tab element init', 'xts-theme' ),
			'name'      => 'product-tabs-element',
			'file'      => '/js/scripts/productsTabs',
			'in_footer' => true,
		),
	),
	'single-product-quantity'             => array(
		array(
			'title'     => esc_html__( 'Single product quantity script', 'xts-theme' ),
			'name'      => 'single-product-quantity-method',
			'file'      => '/js/scripts/quantity',
			'in_footer' => true,
		),
	),
	'product-quick-shop'                  => array(
		array(
			'title'     => esc_html__( 'Product quick shop script', 'xts-theme' ),
			'name'      => 'product-quick-shop-method',
			'file'      => '/js/scripts/quickShop',
			'in_footer' => true,
		),
	),
	'search-categories-dropdown'          => array(
		array(
			'title'     => esc_html__( 'Search categories dropdown script', 'xts-theme' ),
			'name'      => 'search-categories-dropdown-method',
			'file'      => '/js/scripts/searchCatDropdown',
			'in_footer' => true,
		),
	),
	'single-product-accordion'            => array(
		array(
			'title'     => esc_html__( 'Single product accordion script', 'xts-theme' ),
			'name'      => 'single-product-accordion-method',
			'file'      => '/js/scripts/singleProductAccordion',
			'in_footer' => true,
		),
	),
	'single-product-ajax-add-to-cart'     => array(
		array(
			'title'     => esc_html__( 'Single product AJAX add to cart script', 'xts-theme' ),
			'name'      => 'single-product-ajax-add-to-cart',
			'file'      => '/js/scripts/singleProductAjaxAddToCart',
			'in_footer' => true,
		),
	),
	'single-product-gallery'              => array(
		array(
			'title'     => esc_html__( 'Single product gallery script', 'xts-theme' ),
			'name'      => 'single-product-gallery-method',
			'file'      => '/js/scripts/singleProductGallery',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Swiper structure script', 'xts-theme' ),
			'name'      => 'add-swiper-structure-method',
			'file'      => '/js/scripts/addSwiperStructure',
			'in_footer' => true,
		),
	),
	'single-product-gallery-photoswipe'   => array(
		array(
			'title'     => esc_html__( 'Single product gallery photoswipe script', 'xts-theme' ),
			'name'      => 'single-product-gallery-photoswipe-method',
			'file'      => '/js/scripts/singleProductGalleryPhotoSwipe',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Photoswipe script', 'xts-theme' ),
			'name'      => 'photoswipe-method',
			'file'      => '/js/scripts/callPhotoSwipe',
			'in_footer' => true,
		),
	),
	'single-product-gallery-zoom'         => array(
		array(
			'title'     => esc_html__( 'Single product gallery zoom script', 'xts-theme' ),
			'name'      => 'single-product-gallery-zoom-method',
			'file'      => '/js/scripts/singleProductGalleryZoom',
			'in_footer' => true,
		),
	),
	'single-product-sticky'               => array(
		array(
			'title'     => esc_html__( 'Single product sticky script', 'xts-theme' ),
			'name'      => 'single-product-sticky-method',
			'file'      => '/js/scripts/singleProductSticky',
			'in_footer' => true,
		),
	),
	'single-product-sticky-add-to-cart'   => array(
		array(
			'title'     => esc_html__( 'Single product sticky add to cart script', 'xts-theme' ),
			'name'      => 'single-product-sticky-add-to-cart-method',
			'file'      => '/js/scripts/singleProductStickyAddToCart',
			'in_footer' => true,
		),
	),
	'variations-swatches'                 => array(
		array(
			'title'     => esc_html__( 'Variation swatches script', 'xts-theme' ),
			'name'      => 'variations-swatches-method',
			'file'      => '/js/scripts/variationsSwatches',
			'in_footer' => true,
		),
	),
	'product-wishlist'                    => array(
		array(
			'title'     => esc_html__( 'Product wishlist script', 'xts-theme' ),
			'name'      => 'product-wishlist-method',
			'file'      => '/js/scripts/wishlist',
			'in_footer' => true,
		),
	),
	'wc-comments'                         => array(
		array(
			'title'     => esc_html__( 'WC comments script', 'xts-theme' ),
			'name'      => 'wc-comments-method',
			'file'      => '/js/scripts/woocommerceComments',
			'in_footer' => true,
		),
	),
	'wc-price-slider'                     => array(
		array(
			'title'     => esc_html__( 'WC price slider script', 'xts-theme' ),
			'name'      => 'wc-price-slider-method',
			'file'      => '/js/scripts/woocommercePriceSlider',
			'in_footer' => true,
		),
	),
	'variations-price'                    => array(
		array(
			'title'     => esc_html__( 'Variations price script', 'xts-theme' ),
			'name'      => 'variations-price-method',
			'file'      => '/js/scripts/variationsPrice',
			'in_footer' => true,
		),
	),
);
