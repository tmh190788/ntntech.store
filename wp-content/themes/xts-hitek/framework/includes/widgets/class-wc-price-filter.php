<?php
/**
 * Price filter class.
 *
 * @package xts
 */

namespace XTS\Widget;

use WC_Query;
use WP_Tax_Query;
use XTS\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Price filter widget
 */
class WC_Price_Filter extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( '[XTemos] Filter Products by Price', 'xts-theme' ),
			'description' => esc_html__( 'Price filter list.', 'xts-theme' ),
			'slug'        => 'xts-widget-wc-price-filter',
			'fields'      => array(
				array(
					'id'      => 'title',
					'type'    => 'text',
					'name'    => esc_html__( 'Title', 'xts-theme' ),
					'default' => 'Filter by price',
				),

				array(
					'id'          => 'price_ranges',
					'type'        => 'checkbox',
					'name'        => esc_html__( 'Show empty price ranges', 'xts-theme' ),
					'description' => esc_html__( 'May increase this widget performance if you have a lot of products in your store.', 'xts-theme' ),
					'default'     => true,
				),
			),
		);

		$this->create_widget( $args );
	}

	/**
	 * Output widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		global $wp_the_query;

		$default_args = array(
			'title'        => 'Sort by',
			'price_ranges' => true,
		);

		$instance = wp_parse_args( $instance, $default_args );

		if ( ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) || ! $wp_the_query->post_count ) {
			return;
		}

		$min_price = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : ''; // phpcs:ignore
		$max_price = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : ''; // phpcs:ignore

		// Find min and max price in current result set.
		$prices = $this->get_filtered_price();
		$min    = floor( $prices->min_price );
		$max    = ceil( $prices->max_price );

		if ( $min === $max ) {
			return;
		}

		/**
		 * Adjust max if the store taxes are not displayed how they are stored.
		 * Min is left alone because the product may not be taxable.
		 * Kicks in when prices excluding tax are displayed including tax.
		 */
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_classes = array_merge( array( '' ), WC_Tax::get_tax_classes() );
			$class_max   = $max;

			foreach ( $tax_classes as $tax_class ) {
				$tax_rates = WC_Tax::get_rates( $tax_class );

				if ( $tax_rates ) {
					$class_max = $max + WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max, $tax_rates ) );
				}
			}

			$max = $class_max;
		}

		$links = $this->generate_price_links( $min, $max, $min_price, $max_price, $instance['price_ranges'] );

		if ( ! $links ) {
			return;
		}

		echo wp_kses( $args['before_widget'], 'xts_widget' );

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo wp_kses( $args['before_title'], 'xts_widget' ) . $instance['title'] . wp_kses( $args['after_title'], 'xts_widget' ); // phpcs:ignore
		}

		?>
			<div class="xts-wc-price-filter">
				<ul>
					<?php foreach ( $links as $link ) : ?>
						<li>
							<a rel="nofollow" href="<?php echo esc_url( $link['href'] ); ?>" class="<?php echo esc_attr( $link['classes'] ); ?>"><?php echo wp_kses( $link['title'], xts_get_allowed_html() ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php

		echo wp_kses( $args['after_widget'], 'xts_widget' );
	}

	/**
	 * Get filtered price
	 *
	 * @since 1.0.0
	 */
	protected function get_filtered_price() {
		global $wpdb;

		$args       = WC()->query->get_main_query()->query_vars;
		$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
		$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

		if ( ! is_post_type_archive( 'product' ) && ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
			$tax_query[] = array(
				'taxonomy' => $args['taxonomy'],
				'terms'    => array( $args['term'] ),
				'field'    => 'slug',
			);
		}

		foreach ( $meta_query + $tax_query as $key => $query ) {
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}

		$meta_query = new \WP_Meta_Query( $meta_query );
		$tax_query  = new \WP_Tax_Query( $tax_query );
		$search     = \WC_Query::get_main_search_query_sql();

		$meta_query_sql   = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql    = $tax_query->get_sql( $wpdb->posts, 'ID' );
		$search_query_sql = $search ? ' AND ' . $search : '';

		$sql = "
			SELECT min( min_price ) as min_price, MAX( max_price ) as max_price
			FROM {$wpdb->wc_product_meta_lookup}
			WHERE product_id IN (
				SELECT ID FROM {$wpdb->posts}
				" . $tax_query_sql['join'] . $meta_query_sql['join'] . "
				WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
				AND {$wpdb->posts}.post_status = 'publish'
				" . $tax_query_sql['where'] . $meta_query_sql['where'] . $search_query_sql . '
			)';

		$sql = apply_filters( 'woocommerce_price_filter_sql', $sql, $meta_query_sql, $tax_query_sql );

		return $wpdb->get_row( $sql ); // phpcs:ignore
	}

	/**
	 * Generate price links
	 *
	 * @since 1.0.0
	 *
	 * @param integer $min          Min value.
	 * @param integer $max          Max value.
	 * @param integer $min_price    Min price.
	 * @param integer $max_price    Max price.
	 * @param boolean $price_ranges Is show price ranges.
	 *
	 * @return array
	 */
	private function generate_price_links( $min, $max, $min_price, $max_price, $price_ranges ) {
		$links = array();

		// Remember current filters/search.
		$link          = xts_get_shop_page_link( true );
		$link_no_price = remove_query_arg( 'min_price', $link );
		$link_no_price = remove_query_arg( 'max_price', $link_no_price );

		$need_more = false;

		$steps      = 4;
		$step_value = $max / $steps;

		if ( $step_value < 10 ) {
			$step_value = 10;
		}

		$step_value = round( $step_value, -1 );

		$all_link_classes = '';

		if ( empty( $min_price ) && empty( $max_price ) ) {
			$all_link_classes = 'xts-selected';
		}

		// Link to all prices.
		$links[] = array(
			'href'    => $link_no_price,
			'title'   => esc_html__( 'All', 'xts-theme' ),
			'classes' => $all_link_classes,
		);

		for ( $i = 0; $i < (int) $steps; $i++ ) {

			$step_classes = '';

			$step_min = $step_value * $i;

			$step_max = $step_value * ( $i + 1 );

			if ( $step_max > $max ) {
				$need_more = true;
				$i++;
				break;
			}

			$href = add_query_arg( 'min_price', $step_min, $link );
			$href = add_query_arg( 'max_price', $step_max, $href );

			$step_title = wc_price( $step_min ) . ' - ' . wc_price( $step_max );

			if ( ! empty( $min_price ) && ! empty( $max_price ) && ( $min_price >= $step_min && $max_price <= $step_max )
				|| ( 0 == $i && ! empty( $max_price ) && ( $max_price <= $step_max ) )
				) {
				$step_classes = 'xts-selected';
			}

			if ( $this->check_range( $step_min, $step_max, $price_ranges ) ) {
				$links[] = array(
					'href'    => $href,
					'title'   => $step_title,
					'classes' => $step_classes,
				);
			}
		}

		if ( $max > $step_max ) {
			$need_more = true;
			$step_min  = $step_value * $i;
		}

		if ( $need_more ) {

			$step_classes = '';

			$href = add_query_arg( 'min_price', $step_min, $link );
			$href = add_query_arg( 'max_price', $max, $href );

			$step_title = wc_price( $step_min ) . ' +';

			if ( $min_price >= $step_min && $max_price <= $max ) {
				$step_classes = 'xts-selected';
			}

			if ( $this->check_range( $step_min, $step_max, $price_ranges ) ) {
				$links[] = array(
					'href'    => $href,
					'title'   => $step_title,
					'classes' => $step_classes,
				);
			}
		}

		return $links;
	}

	/**
	 * Check range
	 *
	 * @since 1.0.0
	 *
	 * @param integer $min          Min value.
	 * @param integer $max          Max value.
	 * @param boolean $price_ranges Is show price ranges.
	 *
	 * @return bool
	 */
	private function check_range( $min, $max, $price_ranges ) {
		global $wpdb;

		if ( ! $price_ranges ) {
			return true;
		}

		$tax_query     = WC_Query::get_main_tax_query();
		$tax_query     = new WP_Tax_Query( $tax_query );
		$tax_query_sql = $tax_query->get_sql( $wpdb->posts, 'ID' );

		// Generate query.
		$query           = array();
		$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as range_count";
		$query['from']   = "FROM {$wpdb->posts}";

		$query['join'] = "
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			INNER JOIN {$wpdb->wc_product_meta_lookup} ON ( {$wpdb->posts}.ID = {$wpdb->wc_product_meta_lookup}.product_id )
			" . $tax_query_sql['join'];

		$query['where'] = "
			WHERE {$wpdb->posts}.post_type IN ( 'product' )
			AND {$wpdb->posts}.post_status = 'publish'
			AND {$wpdb->wc_product_meta_lookup}.min_price >= '" . $min . "' AND {$wpdb->wc_product_meta_lookup}.max_price <= '" . $max . "'
			" . $tax_query_sql['where'] . '
		';

		$search = method_exists( 'WC_Query', 'get_main_search_query_sql' ) ? WC_Query::get_main_search_query_sql() : '';

		if ( $search ) {
			$query['where'] .= ' AND ' . $search;

			if ( xts_get_opt( 'shop_search_by_sku' ) ) {
				// search for variations with a matching sku and return the parent.
				$sku_to_parent_id = $wpdb->get_col( $wpdb->prepare( "SELECT p.post_parent as post_id FROM {$wpdb->posts} as p join {$wpdb->wc_product_meta_lookup} ml on p.ID = ml.product_id and ml.sku LIKE '%%%s%%' where p.post_parent <> 0 group by p.post_parent", wc_clean( $_GET['s'] ) ) ); // phpcs:ignore

				// Search for a regular product that matches the sku.
				$sku_to_id = $wpdb->get_col( $wpdb->prepare( "SELECT product_id FROM {$wpdb->wc_product_meta_lookup} WHERE sku LIKE '%%%s%%';", wc_clean( $_GET['s'] ) ) ); // phpcs:ignore

				$search_ids = array_merge( $sku_to_id, $sku_to_parent_id );

				$search_ids = array_filter( array_map( 'absint', $search_ids ) );

				if ( count( $search_ids ) > 0 ) {
					$query['where'] = str_replace( '))', ") OR ({$wpdb->posts}.ID IN (" . implode( ',', $search_ids ) . ')))', $query['where'] );
				}
			}
		}

		$query   = implode( ' ', $query );
		$results = $wpdb->get_var( $query ); // phpcs:ignore

		return $results > 0;
	}
}
