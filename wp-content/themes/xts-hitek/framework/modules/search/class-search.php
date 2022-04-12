<?php
/**
 * Search class.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use WP_Query;
use XTS\Framework\Module;
use XTS\Framework\AJAX_Response;

/**
 * Define constants, load classes and initialize everything.
 */
class Search extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'wp_ajax_xts_ajax_search', array( $this, 'ajax_action' ) );
		add_action( 'wp_ajax_nopriv_xts_ajax_search', array( $this, 'ajax_action' ) );
		add_filter( 'init', array( $this, 'init_search_by_sku' ) );
		add_filter( 'relevanssi_content_to_index', array( $this, 'relevanssi_index_variation_sku' ), 10, 2 );

		add_action( 'woocommerce_after_shop_loop', array( $this, 'search_posts_results' ), 100 );
		add_action( 'xts_after_portfolio_loop', array( $this, 'search_posts_results' ), 100 );
		add_action( 'xts_after_no_product_found', array( $this, 'search_posts_results' ), 100 );
	}

	/**
	 * Blog results on search page.
	 *
	 * @since 1.0.0
	 */
	public function search_posts_results() {
		if ( ! is_search() || ! xts_get_opt( 'search_posts_results' ) ) {
			return;
		}

		$search_query = get_search_query();
		$column       = xts_get_opt( 'search_posts_results_column' );

		ob_start();

		?>
			<div class="xts-blog-search-results">
				<h4>
					<?php esc_html_e( 'Results from blog', 'xts-theme' ); ?>
				</h4>

				<?php
				xts_blog_template(
					array(
						'items_per_page' => array( 'size' => 10 ),
						'carousel_items' => array( 'size' => $column ),
						'view'           => 'carousel',
						'search'         => $search_query,
					)
				);
				?>

				<div class="xts-search-show-all">
					<a href="<?php echo esc_url( home_url() ); ?>?s=<?php echo esc_attr( $search_query ); ?>&post_type=post" class="xts-button xts-color-default xts-style-bordered">
						<?php esc_html_e( 'Show all blog results', 'xts-theme' ); ?>
					</a>
				</div>
			</div>
		<?php

		echo ob_get_clean(); // phpcs:ignore
	}

	/**
	 * Fix for Relevanssi and AJAX search by sku.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Query content.
	 * @param object $post Post object.
	 *
	 * @return string
	 */
	public function relevanssi_index_variation_sku( $content, $post ) {
		if ( ! xts_get_opt( 'shop_search_by_sku' ) || ! xts_get_opt( 'relevanssi_search' ) || ! function_exists( 'relevanssi_do_query' ) ) {
			return $content;
		}

		if ( 'product' === $post->post_type ) {
			$args = array(
				'post_parent'    => $post->ID,
				'post_type'      => 'product_variation',
				'posts_per_page' => - 1,
			);

			$variations = get_posts( $args );

			if ( $variations ) {
				foreach ( $variations as $variation ) {
					$sku      = get_post_meta( $variation->ID, '_sku', true );
					$content .= " $sku";
				}
			}
		}

		return $content;
	}

	/**
	 * Init search by SKU
	 *
	 * @since 1.0.0
	 */
	public function init_search_by_sku() {
		if ( xts_get_opt( 'shop_search_by_sku' ) && xts_is_woocommerce_installed() ) {
			add_filter( 'posts_search', array( $this, 'product_search_sku' ) );
		}
	}

	/**
	 * Filters the search SQL that is used in the WHERE clause of WP_Query.
	 *
	 * @since 1.0.0
	 *
	 * @param string $search Search SQL for WHERE clause.
	 *
	 * @return string
	 */
	public function product_search_sku( $search ) {
		global $wp;

		if ( is_admin() || ! is_search() || ! isset( $wp->query_vars['s'] ) || ( isset( $wp->query_vars['post_type'] ) && 'product' !== $wp->query_vars['post_type'] ) || ( isset( $wp->query_vars['post_type'] ) && is_array( $wp->query_vars['post_type'] ) && ! in_array( 'product', $wp->query_vars['post_type'] ) ) ) { // phpcs:ignore
			return $search;
		}

		$query = $wp->query_vars['s'];

		return $this->product_sku_search_query( $search, $query );
	}

	/**
	 * Filters the ajax search SQL that is used in the WHERE clause of WP_Query.
	 *
	 * @since 1.0.0
	 *
	 * @param string $search Search SQL for WHERE clause.
	 *
	 * @return string
	 */
	public function product_ajax_search_sku( $search ) {
		$query = wp_strip_all_tags( sanitize_text_field( wp_unslash( $_REQUEST['query'] ) ) ); // phpcs:ignore

		if ( $query ) {
			return $this->product_sku_search_query( $search, $query );
		}

		return $search;
	}

	/**
	 * Product SKU search query
	 *
	 * @since 1.0.0
	 *
	 * @param string $search Search SQL for WHERE clause.
	 * @param string $query  Search query.
	 *
	 * @return string
	 */
	public function product_sku_search_query( $search, $query ) {
		global $wpdb;

		$search_ids = array();
		$terms      = explode( ',', $query );

		foreach ( $terms as $term ) {
			if ( is_admin() && is_numeric( $term ) ) {
				$search_ids[] = $term;
			}

			$sku_to_parent_id = $wpdb->get_col( $wpdb->prepare( "SELECT p.post_parent as post_id FROM {$wpdb->posts} as p join {$wpdb->wc_product_meta_lookup} ml on p.ID = ml.product_id and ml.sku LIKE '%%%s%%' where p.post_parent <> 0 group by p.post_parent", wc_clean( $term ) ) ); // phpcs:ignore

			$sku_to_id = $wpdb->get_col( $wpdb->prepare( "SELECT product_id FROM {$wpdb->wc_product_meta_lookup} WHERE sku LIKE '%%%s%%';", wc_clean( $term ) ) ); // phpcs:ignore

			$search_ids = array_merge( $search_ids, $sku_to_id, $sku_to_parent_id );
		}

		$search_ids = array_filter( array_map( 'absint', $search_ids ) );

		if ( count( $search_ids ) > 0 ) {
			$search = str_replace( ')))', ") OR ({$wpdb->posts}.ID IN (" . implode( ',', $search_ids ) . '))))', $search );
		}

		return $search;
	}

	/**
	 * Ajax search action
	 *
	 * @since 1.0.0
	 */
	public function ajax_action() {
		$allowed_types = array( 'post', 'xts-portfolio', 'product' );

		if ( xts_get_opt( 'shop_search_by_sku' ) && xts_is_woocommerce_installed() ) {
			add_filter( 'posts_search', array( $this, 'product_ajax_search_sku' ), 10 );
		}

		$post_type  = '';
		$query_args = array(
			'posts_per_page' => 5,
			'post_status'    => 'publish',
			'no_found_rows'  => 1,
		);

		if ( ! empty( $_REQUEST['post_type'] ) && in_array( sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) ), $allowed_types ) ) { // phpcs:ignore
			$post_type               = wp_strip_all_tags( sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) ) ); // phpcs:ignore
			$query_args['post_type'] = $post_type;
		}

		if ( 'product' === $post_type && xts_is_woocommerce_installed() ) {
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			$query_args['tax_query'][]   = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $product_visibility_term_ids['exclude-from-search'],
				'operator' => 'NOT IN',
			);

			if ( ! empty( $_REQUEST['product_cat'] ) ) { // phpcs:ignore
				$query_args['product_cat'] = wp_strip_all_tags( sanitize_text_field( wp_unslash( $_REQUEST['product_cat'] ) ) ); // phpcs:ignore
			}
		}

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && 'product' === $post_type ) {
			$query_args['meta_query'][] = array(
				'key'     => '_stock_status',
				'value'   => 'outofstock',
				'compare' => 'NOT IN',
			);
		}

		if ( ! empty( $_REQUEST['query'] ) ) { // phpcs:ignore
			$query_args['s'] = sanitize_text_field( wp_unslash( $_REQUEST['query'] ) ); // phpcs:ignore
		}

		if ( ! empty( $_REQUEST['number'] ) ) { // phpcs:ignore
			$query_args['posts_per_page'] = (int) $_REQUEST['number']; // phpcs:ignore
		}

		$results = new WP_Query( apply_filters( 'xts_ajax_search_args', $query_args ) );

		if ( xts_get_opt( 'relevanssi_search' ) && function_exists( 'relevanssi_do_query' ) ) {
			relevanssi_do_query( $results );
		}

		$suggestions = array();

		if ( $results->have_posts() ) {
			while ( $results->have_posts() ) {
				$results->the_post();

				if ( 'product' === $post_type && xts_is_woocommerce_installed() ) {
					$factory = new \WC_Product_Factory();
					$product = $factory->get_product( get_the_ID() );

					$suggestions[] = array(
						'value'     => html_entity_decode( get_the_title() ),
						'permalink' => get_the_permalink(),
						'price'     => $product->get_price_html(),
						'thumbnail' => $product->get_image(),
						'sku'       => $product->get_sku() ? esc_html__( 'SKU:', 'xts-theme' ) . ' ' . $product->get_sku() : '',
					);
				} else {
					$categories    = wp_get_post_categories( get_the_ID(), array( 'fields' => 'names' ) );
					$suggestions[] = array(
						'value'      => html_entity_decode( get_the_title() ),
						'permalink'  => get_the_permalink(),
						'thumbnail'  => get_the_post_thumbnail( null, 'medium', '' ),
						'categories' => $categories ? esc_html__( 'Categories:', 'xts-theme' ) . ' ' . implode( ', ', $categories ) : '',
					);
				}
			}

			wp_reset_postdata();
		} else {
			$message = esc_html__( 'No posts found', 'xts-theme' );

			if ( 'product' === $post_type ) {
				$message = esc_html__( 'No products found', 'xts-theme' );
			} elseif ( 'xts-portfolio' === $post_type ) {
				$message = esc_html__( 'No projects found', 'xts-theme' );
			}

			$suggestions[] = array(
				'value'     => $message,
				'no_found'  => true,
				'permalink' => '',
			);
		}

		if ( xts_get_opt( 'search_posts_results' ) && 'post' !== $post_type ) {
			$post_suggestions = $this->get_post_suggestions();
			$suggestions      = array_merge( $suggestions, $post_suggestions );
		}

		AJAX_Response::send_response(
			array(
				'suggestions' => apply_filters( 'xts_ajax_search_suggestions', $suggestions ),
			)
		);
	}

	/**
	 * Ajax search action
	 *
	 * @since 1.0.0
	 */
	public function get_post_suggestions() {
		$query_args = array(
			'posts_per_page' => 5,
			'post_status'    => 'publish',
			'post_type'      => 'post',
			'no_found_rows'  => 1,
		);

		if ( $_REQUEST['query'] ) { // phpcs:ignore
			$query_args['s'] = sanitize_text_field( $_REQUEST['query'] ); // phpcs:ignore
		}

		if ( $_REQUEST['number'] ) { // phpcs:ignore
			$query_args['posts_per_page'] = (int) $_REQUEST['number']; // phpcs:ignore
		}

		$results     = new WP_Query( $query_args );
		$suggestions = array();

		if ( $results->have_posts() ) {
			$suggestions[] = array(
				'value'   => '',
				'divider' => esc_html__( 'Results from blog', 'xts-theme' ),
			);

			while ( $results->have_posts() ) {
				$results->the_post();

				$suggestions[] = array(
					'value'     => html_entity_decode( get_the_title() ),
					'permalink' => get_the_permalink(),
					'thumbnail' => get_the_post_thumbnail( null, 'medium', '' ),
				);
			}

			wp_reset_postdata();
		}

		return $suggestions;
	}
}
