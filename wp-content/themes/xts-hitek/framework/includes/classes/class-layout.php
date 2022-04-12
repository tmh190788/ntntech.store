<?php
/**
 * Theme layout functions
 *
 * @package xts
 */

namespace XTS\Framework;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Layout Class set up layout settings
 * for the current page when initializing
 * based on theme options and custom metaboxes
 *
 * @since 1.0.0
 */
class Layout {
	/**
	 * ID for the current page/post/product/project
	 *
	 * @var integer
	 */
	private $page_id = 0;

	/**
	 * CSS bootstrap classes for the content section
	 *
	 * @var string
	 */
	private $content_classes = '';

	/**
	 * CSS bootstrap classes for the sidebar section
	 *
	 * @var string
	 */
	private $sidebar_classes = '';

	/**
	 * Width of the content X/12
	 *
	 * @var integer
	 */
	private $content_column_width = 0;

	/**
	 * Width of the sidebar X/12
	 *
	 * @var integer
	 */
	private $sidebar_column_width = 0;

	/**
	 * Page layout
	 *
	 * @var string
	 */
	private $page_layout = '';

	/**
	 * Sidebar name
	 *
	 * @var string
	 */
	private $sidebar_name = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			return;
		}

		add_action( 'wp', array( $this, 'set_page_id' ), 10 );
		add_action( 'wp', array( $this, 'init' ), 500 );
	}

	/**
	 * Set up all properties
	 */
	public function init() {
		$this->set_page_layout();
		$this->set_sidebar_name();
		$this->set_sidebar_column_width();
		$this->set_content_column_width();
		$this->set_sidebar_classes();
		$this->set_content_classes();
	}

	/**
	 * Get page id
	 *
	 * @return string
	 */
	public function get_page_id() {
		return $this->page_id;
	}

	/**
	 * Get CSS classes for the content element
	 *
	 * @return string
	 */
	public function get_content_classes() {
		return $this->content_classes;
	}

	/**
	 * Get CSS classes for the sidebar element
	 *
	 * @return string
	 */
	public function get_sidebar_classes() {
		return $this->sidebar_classes;
	}

	/**
	 * Get content column width
	 *
	 * @return integer
	 */
	public function get_content_column_width() {
		return $this->content_column_width;
	}

	/**
	 * Get sidebar column width
	 *
	 * @return integer
	 */
	public function get_sidebar_column_width() {
		$sidebar_sizes = array(
			'small'  => 2,
			'medium' => 3,
			'large'  => 4,
		);

		return isset( $sidebar_sizes[ $this->sidebar_column_width ] ) ? $sidebar_sizes[ $this->sidebar_column_width ] : 0;
	}

	/**
	 * Get page layout
	 *
	 * @return string
	 */
	public function get_page_layout() {
		return $this->page_layout;
	}

	/**
	 * Get sidebar name
	 *
	 * @return string
	 */
	public function get_sidebar_name() {
		return $this->sidebar_name;
	}

	/**
	 * Set page id
	 */
	public function set_page_id() {
		$this->page_id = xts_get_page_id();
	}

	/**
	 * Set CSS classes for the content element
	 */
	private function set_content_classes() {
		$content_size = $this->get_content_column_width();
		$layout       = $this->get_page_layout();

		$this->content_classes = ' col-lg-' . $content_size . ' col-12';

		$this->content_classes .= 'sidebar-disabled' === $layout || 12 === $content_size ? ' col-md-12' : ' col-md-9';
	}

	/**
	 * Set CSS classes for the sidebar element
	 */
	private function set_sidebar_classes() {
		$sidebar_size = $this->get_sidebar_column_width();
		$layout       = $this->get_page_layout();

		$this->sidebar_classes = ' col-lg-' . $sidebar_size . ' col-md-3 col-12 order-last';

		if ( 'sidebar-left' === $layout ) {
			$this->sidebar_classes .= ' order-md-first';
		}

		if ( ! strstr( $this->sidebar_classes, 'col-lg-0' ) ) {
			$this->sidebar_classes .= ' xts-' . $layout;
		}

		if ( is_singular( 'post' ) || xts_is_blog_archive() ) {
			if ( xts_get_opt( 'blog_offcanvas_sidebar_desktop' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'blog_offcanvas_sidebar_mobile' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-hidden-md';
			}

			if ( xts_get_opt( 'blog_sidebar_sticky' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-sticky';
			}
		} elseif ( xts_is_shop_archive() ) {
			if ( xts_get_opt( 'shop_offcanvas_sidebar_desktop' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'shop_offcanvas_sidebar_mobile' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-hidden-md';
			}

			if ( xts_get_opt( 'shop_sidebar_sticky' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-sticky';
			}
		} elseif ( is_singular( 'product' ) || is_singular( 'xts-template' ) ) {
			if ( xts_get_opt( 'single_product_offcanvas_sidebar_desktop' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'single_product_offcanvas_sidebar_mobile' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-hidden-md';
			}

			if ( xts_get_opt( 'single_product_sidebar_sticky' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-sticky';
			}
		} else {
			if ( xts_get_opt( 'offcanvas_sidebar_desktop' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'offcanvas_sidebar_mobile' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-hidden-md';
			}

			if ( xts_get_opt( 'sidebar_sticky' ) ) {
				$this->sidebar_classes .= ' xts-sidebar-sticky';
			}
		}
	}

	/**
	 * Set sidebar column width
	 */
	private function set_sidebar_column_width() {
		$sidebar_size                = xts_get_opt( 'sidebar_size' );
		$blog_sidebar_size           = xts_get_opt( 'blog_sidebar_size' );
		$portfolio_sidebar_size      = xts_get_opt( 'portfolio_sidebar_size' );
		$shop_sidebar_size           = xts_get_opt( 'shop_sidebar_size' );
		$single_product_sidebar_size = xts_get_opt( 'single_product_sidebar_size' );
		$page_id                     = $this->get_page_id();
		$metabox_sidebar_size        = get_post_meta( $page_id, '_xts_sidebar_size', true );
		$layout                      = $this->get_page_layout();
		$sidebar_name                = $this->get_sidebar_name();

		$this->sidebar_column_width = $sidebar_size;

		if ( ( is_singular( 'post' ) || xts_is_blog_archive() ) && ( 'inherit' === $metabox_sidebar_size || ! $metabox_sidebar_size ) ) {
			$this->sidebar_column_width = $blog_sidebar_size;
		}

		if ( ( is_singular( 'xts-portfolio' ) || xts_is_portfolio_archive() ) && ( 'inherit' === $metabox_sidebar_size || ! $metabox_sidebar_size ) ) {
			$this->sidebar_column_width = $portfolio_sidebar_size;
		}

		if ( xts_is_shop_archive() ) {
			$this->sidebar_column_width = $shop_sidebar_size;
		}

		if ( is_singular( 'product' ) || is_singular( 'xts-template' ) ) {
			$this->sidebar_column_width = $single_product_sidebar_size;
		}

		// Remove sidebar if it has no widgets.
		if ( ! is_active_sidebar( $sidebar_name ) ) {
			$this->sidebar_column_width = 0;
		}

		if ( 'sidebar-disabled' === $layout ) {
			$this->sidebar_column_width = 0;
		}
	}

	/**
	 * Set content column width
	 */
	private function set_content_column_width() {
		$sidebar_width              = $this->get_sidebar_column_width();
		$this->content_column_width = 12 - $sidebar_width;
	}

	/**
	 * Set page layout
	 */
	private function set_page_layout() {
		$sidebar_position                = xts_get_opt( 'sidebar_position' );
		$portfolio_sidebar_position      = xts_get_opt( 'portfolio_sidebar_position' );
		$blog_sidebar_position           = xts_get_opt( 'blog_sidebar_position' );
		$shop_sidebar_position           = xts_get_opt( 'shop_sidebar_position' );
		$single_product_sidebar_position = xts_get_opt( 'single_product_sidebar_position' );
		$page_id                         = $this->get_page_id();
		$metabox_sidebar_position        = get_post_meta( $page_id, '_xts_sidebar_position', true );

		$this->page_layout = 'sidebar-' . $sidebar_position;

		if ( ( is_singular( 'post' ) || xts_is_blog_archive() ) && ( 'inherit' === $metabox_sidebar_position || ! $metabox_sidebar_position ) ) {
			$this->page_layout = 'sidebar-' . $blog_sidebar_position;
		}

		if ( ( is_singular( 'xts-portfolio' ) || xts_is_portfolio_archive() ) && ( 'inherit' === $metabox_sidebar_position || ! $metabox_sidebar_position ) ) {
			$this->page_layout = 'sidebar-' . $portfolio_sidebar_position;
		}

		if ( xts_is_shop_archive() ) {
			$this->page_layout = 'sidebar-' . $shop_sidebar_position;
		}

		if ( is_singular( 'product' ) || is_singular( 'xts-template' ) ) {
			$this->page_layout = 'sidebar-' . $single_product_sidebar_position;
		}
	}

	/**
	 * Set the name of sidebar
	 */
	private function set_sidebar_name() { // phpcs:ignore
		$specific           = '';
		$this->sidebar_name = apply_filters( 'xts_default_sidebar_name', 'main-widget-sidebar' );
		$page_id            = $this->get_page_id();

		if ( xts_is_shop_archive() ) {
			$this->sidebar_name = 'shop-widget-sidebar';
		}

		if ( is_singular( 'product' ) || is_singular( 'xts-template' ) ) {
			$this->sidebar_name = 'single-product-widget-sidebar';
		}

		if ( $page_id ) {
			$specific = get_post_meta( $page_id, '_xts_custom_sidebar', true );
		}

		if ( $specific ) {
			$this->sidebar_name = $specific;
		}
	}
}
