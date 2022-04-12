<?php
/**
 * Woocommerce helpers functions file
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_pagenum_link' ) ) {
	/**
	 * Remove from page number link pjax parameter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $link The page number link.
	 *
	 * @return string
	 */
	function xts_get_pagenum_link( $link ) {
		$link = str_replace( '&_pjax=.xts-site-content', '', $link );
		$link = str_replace( '?_pjax=.xts-site-content', '', $link );

		return $link;
	}

	add_filter( 'get_pagenum_link', 'xts_get_pagenum_link' );
}

if ( ! function_exists( 'xts_is_shop_on_front' ) ) {
	/**
	 * Is shop on front page
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	function xts_is_shop_on_front() {
		return function_exists( 'wc_get_page_id' ) && 'page' === get_option( 'show_on_front' ) && wc_get_page_id( 'shop' ) === get_option( 'page_on_front' );
	}
}

if ( ! function_exists( 'xts_is_product_attribute_archive' ) ) {
	/**
	 * Determine is it product attribute archive page
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	function xts_is_product_attribute_archive() {
		$queried_object = get_queried_object();

		if ( $queried_object && property_exists( $queried_object, 'taxonomy' ) ) {
			$taxonomy = $queried_object->taxonomy;

			return substr( $taxonomy, 0, 3 ) === 'pa_';
		}

		return false;
	}
}

if ( ! function_exists( 'xts_is_shop_archive' ) ) {
	/**
	 * If current page shop archive
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	function xts_is_shop_archive() {
		return xts_is_woocommerce_installed() && ( is_shop() || is_product_category() || is_product_tag() || xts_is_product_attribute_archive() );
	}
}

if ( ! function_exists( 'xts_is_portfolio_archive' ) ) {
	/**
	 * If current page portfolio archive
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	function xts_is_portfolio_archive() {
		return is_post_type_archive( 'xts-portfolio' ) || is_tax( 'xts-portfolio-cat' );
	}
}

if ( ! function_exists( 'xts_get_shop_page_link' ) ) {
	/**
	 * Get base shop page link
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $keep_query Keep query.
	 *
	 * @return string
	 */
	function xts_get_shop_page_link( $keep_query = false ) {
		$link = '';

		if ( Automattic\Jetpack\Constants::is_defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
		} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) || is_shop() ) {
			$link = get_permalink( wc_get_page_id( 'shop' ) );
		} elseif ( is_product_category() ) {
			$link = get_term_link( get_query_var( 'product_cat' ), 'product_cat' );
		} elseif ( is_product_tag() ) {
			$link = get_term_link( get_query_var( 'product_tag' ), 'product_tag' );
		} else {
			$queried_object = get_queried_object();
			if ( is_object( $queried_object ) && property_exists( $queried_object, 'slug' ) && property_exists( $queried_object, 'taxonomy' ) ) {
				$link = get_term_link( $queried_object->slug, $queried_object->taxonomy );
			}
		}

		if ( $keep_query ) {
			// Min/Max.
			if ( isset( $_GET['min_price'] ) ) { // phpcs:ignore
				$link = add_query_arg( 'min_price', wc_clean( wp_unslash( $_GET['min_price'] ) ), $link ); // phpcs:ignore
			}

			if ( isset( $_GET['max_price'] ) ) { // phpcs:ignore
				$link = add_query_arg( 'max_price', wc_clean( wp_unslash( $_GET['max_price'] ) ), $link ); // phpcs:ignore
			}

			// Order by.
			if ( isset( $_GET['orderby'] ) ) { // phpcs:ignore
				$link = add_query_arg( 'orderby', wc_clean( wp_unslash( $_GET['orderby'] ) ), $link ); // phpcs:ignore
			}

			// Stock status widget.
			if ( isset( $_GET['stock_status'] ) ) { // phpcs:ignore
				$link = add_query_arg( 'stock_status', wc_clean( wp_unslash( $_GET['stock_status'] ) ), $link ); // phpcs:ignore
			}

			/**
			 * Search Arg.
			 * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
			 */
			if ( get_search_query() ) {
				$link = add_query_arg( 's', rawurlencode( wp_specialchars_decode( get_search_query() ) ), $link );
			}

			// Post Type Arg.
			if ( isset( $_GET['post_type'] ) ) {
				$link = add_query_arg( 'post_type', wc_clean( wp_unslash( $_GET['post_type'] ) ), $link ); // phpcs:ignore

				// Prevent post type and page id when pretty permalinks are disabled.
				if ( is_shop() ) {
					$link = remove_query_arg( 'page_id', $link );
				}
			}

			// Min Rating Arg.
			if ( isset( $_GET['rating_filter'] ) ) { // phpcs:ignore
				$link = add_query_arg( 'rating_filter', wc_clean( wp_unslash( $_GET['rating_filter'] ) ), $link ); // phpcs:ignore
			}

			// All current filters.
			if ( $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes() ) { // phpcs:ignore
				foreach ( $_chosen_attributes as $name => $data ) {
					$filter_name = wc_attribute_taxonomy_slug( $name );
					if ( ! empty( $data['terms'] ) ) {
						$link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
					}
					if ( 'or' === $data['query_type'] ) {
						$link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
					}
				}
			}
		}

		if ( is_string( $link ) ) {
			return apply_filters( 'xts_shop_page_link', $link );
		}

		return '';
	}
}
