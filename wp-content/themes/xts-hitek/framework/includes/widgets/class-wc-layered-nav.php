<?php
/**
 * Recent posts class.
 *
 * @package xts
 */

namespace XTS\Widget;

use Automattic\Jetpack\Constants;
use WC_Query;
use WP_Meta_Query;
use WP_Tax_Query;
use XTS\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Recent posts widget
 */
class WC_Layered_Nav extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( '[XTemos] Filter Products by Attribute', 'xts-theme' ),
			'description' => esc_html__( 'Shows a custom attribute in a widget which lets you narrow down the list of products when viewing product categories.', 'xts-theme' ),
			'slug'        => 'xts-widget-filter',
			'fields'      => array(
				array(
					'id'      => 'title',
					'type'    => 'text',
					'name'    => esc_html__( 'Title', 'xts-theme' ),
					'default' => 'Filter by',
				),

				array(
					'id'      => 'attribute',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Attribute', 'xts-theme' ),
					'fields'  => $this->get_attributes_array(),
					'default' => '',
				),

				array(
					'id'      => 'category',
					'type'    => 'select2',
					'name'    => esc_html__( 'Show on category', 'xts-theme' ),
					'fields'  => $this->get_categories_array(),
					'default' => array( 'all' ),
				),

				array(
					'id'      => 'query_type',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Query type', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'AND', 'xts-theme' ) => 'and',
						esc_html__( 'OR', 'xts-theme' )  => 'or',
					),
					'default' => 'and',
				),

				array(
					'id'      => 'style',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Style', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'List', 'xts-theme' ) => 'list',
						esc_html__( 'Inline', 'xts-theme' ) => 'inline',
						esc_html__( 'Dropdown', 'xts-theme' ) => 'dropdown',
						esc_html__( '2 columns', 'xts-theme' ) => 'double',
					),
					'default' => 'list',
				),

				array(
					'id'      => 'swatches_size',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Swatches size', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Small', 'xts-theme' ) => 's',
						esc_html__( 'Medium', 'xts-theme' ) => 'm',
						esc_html__( 'Large', 'xts-theme' ) => 'l',
					),
					'default' => 'm',
				),

				array(
					'id'      => 'labels',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Show labels', 'xts-theme' ),
					'default' => true,
				),

				array(
					'id'      => 'tooltips',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Show tooltips', 'xts-theme' ),
					'default' => true,
				),

				array(
					'id'      => 'show_count',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Show count', 'xts-theme' ),
					'default' => true,
				),

				array(
					'id'      => 'close_if_not_selected',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Close widget if not selected', 'xts-theme' ),
					'default' => false,
				),
			),
		);

		$this->create_widget( $args );
	}

	/**
	 * Output widget.
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		$_chosen_attributes    = WC_Query::get_layered_nav_chosen_attributes();
		$attribute             = isset( $instance['attribute'] ) ? wc_attribute_taxonomy_name( $instance['attribute'] ) : '';
		$category              = isset( $instance['category'] ) && $instance['category'] ? $instance['category'] : array( 'all' );
		$query_type            = isset( $instance['query_type'] ) ? $instance['query_type'] : 'and';
		$style                 = isset( $instance['style'] ) ? $instance['style'] : 'list';
		$close_if_not_selected = isset( $instance['close_if_not_selected'] ) ? $instance['close_if_not_selected'] : false;
		$current_cat           = get_queried_object();

		if ( ! is_tax() && ! in_array( 'all', $category ) ) { // phpcs:ignore
			return;
		}

		if ( ! in_array( 'all', $category ) && property_exists( $current_cat, 'term_id' ) && ! in_array( $current_cat->term_id, $category ) && ! in_array( $current_cat->parent, $category ) ) { // phpcs:ignore
			return;
		}

		if ( ! taxonomy_exists( $attribute ) ) {
			return;
		}

		$terms_args = array(
			'hide_empty' => '1',
		);

		$order_by = wc_attribute_orderby( $attribute );

		switch ( $order_by ) {
			case 'name':
				$terms_args['orderby']    = 'name';
				$terms_args['menu_order'] = false;
				break;
			case 'id':
				$terms_args['orderby']    = 'id';
				$terms_args['order']      = 'ASC';
				$terms_args['menu_order'] = false;
				break;
			case 'menu_order':
				$terms_args['menu_order'] = 'ASC';
				break;
		}

		$terms = get_terms( $attribute, $terms_args );

		if ( 0 === count( $terms ) ) {
			return;
		}

		ob_start();

		$wrapper_classes = '';
		$title_classes   = '';

		if ( $close_if_not_selected ) {
			xts_enqueue_js_script( 'widget-collapse' );
			$wrapper_classes .= ' xts-widget-collapse xts-inited';
		}
		if ( is_array( $_chosen_attributes ) && array_key_exists( $attribute, $_chosen_attributes ) ) {
			$wrapper_classes .= ' xts-initially-opened';
		}
		if ( 'shop-tools-widget-sidebar' === $args['id'] ) {
			$wrapper_classes .= ' xts-shop-tools-widget';
			$title_classes   .= 'xts-tools-widget-title';
		} else {
			$title_classes .= 'widget-title title';
		}

		$title_tag = apply_filters( 'xts_widgets_title_tag', 'span' );

		?>
		<?php if ( 'filters-area-widget-sidebar' === $args['id'] ) : ?>
			<div class="xts-col">
		<?php endif; ?>
			<div id="<?php echo esc_attr( $args['widget_id'] ); ?>" class="widget xts-widget-filter<?php echo esc_attr( $wrapper_classes ); ?>">
		<?php

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			?>
			<<?php echo esc_html( $title_tag ); ?> class="<?php echo esc_attr( $title_classes ); ?>">
				<span>
					<?php echo esc_html( $instance['title'] ); ?>
				</span>
			</<?php echo esc_html( $title_tag ); ?>>
			<?php
		}

		if ( 'dropdown' === $style ) {
			wp_enqueue_script( 'selectWoo' );
			wp_enqueue_style( 'select2' );
			$found = $this->layered_nav_dropdown( $terms, $attribute, $query_type );
		} else {
			$found = $this->layered_nav_list( $terms, $attribute, $query_type, $instance );
		}

		?>
		<?php if ( 'filters-area-widget-sidebar' === $args['id'] ) : ?>
			</div>
		<?php endif; ?>
		</div>
		<?php

		// Force found when option is selected - do not force found on taxonomy attributes.
		if ( ! is_tax() && is_array( $_chosen_attributes ) && array_key_exists( $attribute, $_chosen_attributes ) ) {
			$found = true;
		}

		if ( ! $found ) {
			ob_end_clean();
		} else {
			echo ob_get_clean(); // phpcs:ignore
		}
	}

	/**
	 * Get product attributes array
	 *
	 * @return array
	 */
	protected function get_attributes_array() {
		$attributes = array();
		$taxonomies = wc_get_attribute_taxonomies();

		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				$attributes[ $taxonomy->attribute_name ] = $taxonomy->attribute_name;
			}
		}

		return $attributes;
	}

	/**
	 * Get product categories array
	 *
	 * @return array
	 */
	protected function get_categories_array() {
		$categories_array = array(
			esc_html__( 'All', 'xts-theme' ) => 'all',
		);

		$categories = $this->get_categories();

		if ( $categories ) {
			foreach ( $categories as $category ) {
				$categories_array[ $category->post_title ] = $category->id;
			}
		}

		return $categories_array;
	}

	/**
	 * Get product categories
	 *
	 * @return array
	 */
	protected function get_categories() {
		global $wpdb;

		$categories = $wpdb->get_results(
			"SELECT 
			t.term_id AS id,
			t.name    AS post_title,
			t.slug    AS post_url
		FROM {$wpdb->prefix}terms t
			LEFT JOIN {$wpdb->prefix}term_taxonomy tt
					ON t.term_id = tt.term_id
		WHERE tt.taxonomy = 'product_cat'
		ORDER BY name" ); // phpcs:ignore

		return $categories;
	}

	/**
	 * Return the currently viewed taxonomy name.
	 *
	 * @return string
	 */
	protected function get_current_taxonomy() {
		return is_tax() ? get_queried_object()->taxonomy : '';
	}

	/**
	 * Return the currently viewed term ID.
	 *
	 * @return integer
	 */
	protected function get_current_term_id() {
		return absint( is_tax() ? get_queried_object()->term_id : 0 );
	}

	/**
	 * Return the currently viewed term slug.
	 *
	 * @return integer
	 */
	protected function get_current_term_slug() {
		return absint( is_tax() ? get_queried_object()->slug : 0 );
	}

	/**
	 * Show dropdown layered nav.
	 *
	 * @param array  $terms      Terms.
	 * @param string $taxonomy   Taxonomy.
	 * @param string $query_type Query Type.
	 *
	 * @return bool Will nav display?
	 */
	protected function layered_nav_dropdown( $terms, $taxonomy, $query_type ) {
		global $wp;
		$found = false;

		if ( $taxonomy !== $this->get_current_taxonomy() ) {
			$term_counts          = $this->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );
			$_chosen_attributes   = WC_Query::get_layered_nav_chosen_attributes();
			$taxonomy_filter_name = wc_attribute_taxonomy_slug( $taxonomy );
			$taxonomy_label       = wc_attribute_label( $taxonomy );

			/* translators: %s: taxonomy name */
			$any_label      = apply_filters( 'woocommerce_layered_nav_any_label', sprintf( __( 'Any %s', 'xts-theme' ), $taxonomy_label ), $taxonomy_label, $taxonomy );
			$multiple       = 'or' === $query_type;
			$current_values = isset( $_chosen_attributes[ $taxonomy ]['terms'] ) ? $_chosen_attributes[ $taxonomy ]['terms'] : array();

			if ( '' === get_option( 'permalink_structure' ) ) {
				$form_action = remove_query_arg(
					array(
						'page',
						'paged',
					),
					add_query_arg( $wp->query_string, '', home_url( $wp->request ) )
				);
			} else {
				$form_action = preg_replace( '%\/page/[0-9]+%', '', home_url( trailingslashit( $wp->request ) ) );
			}

			xts_enqueue_js_script( 'layered-nav-dropdown' );

			echo '<form method="get" action="' . esc_url( $form_action ) . '" class="xts-widget-layered-nav-dropdown-form">';
			echo '<select class="xts-widget-layered-nav-dropdown xts_dropdown_layered_nav_' . esc_attr( $taxonomy_filter_name ) . '"' . ( $multiple ? 'multiple="multiple"' : '' ) . ' data-placeholder="' . esc_attr( $any_label ) . '" data-no-results="' . esc_attr__( 'No matches found', 'xts-theme' ) . '" data-slug="' . esc_attr( $taxonomy_filter_name ) . '">';
			echo '<option value="">' . esc_html( $any_label ) . '</option>';

			foreach ( $terms as $term ) {
				// If on a term page, skip that term in widget list.
				if ( $term->term_id === $this->get_current_term_id() ) {
					continue;
				}

				// Get count based on current view.
				$option_is_set = in_array( $term->slug, $current_values, true );
				$count         = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

				// Only show options with count > 0.
				if ( 0 < $count ) {
					$found = true;
				} elseif ( 0 === $count && ! $option_is_set ) {
					continue;
				}

				echo '<option value="' . esc_attr( urldecode( $term->slug ) ) . '" ' . selected( $option_is_set, true, false ) . '>' . esc_html( $term->name ) . '</option>';
			}

			echo '</select>';

			if ( $multiple ) {
				echo '<button class="woocommerce-widget-layered-nav-dropdown__submit" type="submit" value="' . esc_attr__( 'Apply', 'xts-theme' ) . '">' . esc_html__( 'Apply', 'xts-theme' ) . '</button>';
			}

			if ( 'or' === $query_type ) {
				echo '<input type="hidden" name="query_type_' . esc_attr( $taxonomy_filter_name ) . '" value="or" />';
			}

			echo '<input type="hidden" name="filter_' . esc_attr( $taxonomy_filter_name ) . '" value="' . esc_attr( implode( ',', $current_values ) ) . '" />';
			echo wc_query_string_form_fields( // phpcs:ignore
				null,
				array(
					'filter_' . $taxonomy_filter_name,
					'query_type_' . $taxonomy_filter_name,
				),
				'',
				true
			);
			echo '</form>';
		}

		return $found;
	}

	/**
	 * Count products within certain terms, taking the main WP query into consideration.
	 *
	 * This query allows counts to be generated based on the viewed products, not all products.
	 *
	 * @param array  $term_ids   Term IDs.
	 * @param string $taxonomy   Taxonomy.
	 * @param string $query_type Query Type.
	 *
	 * @return array
	 */
	protected function get_filtered_term_product_counts( $term_ids, $taxonomy, $query_type ) {
		global $wpdb;

		$tax_query  = WC_Query::get_main_tax_query();
		$meta_query = WC_Query::get_main_meta_query();

		if ( 'or' === $query_type ) {
			foreach ( $tax_query as $key => $query ) {
				if ( is_array( $query ) && $taxonomy === $query['taxonomy'] ) {
					unset( $tax_query[ $key ] );
				}
			}
		}

		$meta_query     = new WP_Meta_Query( $meta_query );
		$tax_query      = new WP_Tax_Query( $tax_query );
		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		// Generate query.
		$query           = array();
		$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, terms.term_id as term_count_id";
		$query['from']   = "FROM {$wpdb->posts}";
		$query['join']   = "
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			" . $tax_query_sql['join'] . $meta_query_sql['join'];

		$query['where'] = "
			WHERE {$wpdb->posts}.post_type IN ( 'product' )
			AND {$wpdb->posts}.post_status = 'publish'" . $tax_query_sql['where'] . $meta_query_sql['where'] . 'AND terms.term_id IN (' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';

		$search = WC_Query::get_main_search_query_sql();
		if ( $search ) {
			$query['where'] .= ' AND ' . $search;

			if ( xts_get_opt( 'shop_search_by_sku' ) ) {
				// Search for variations with a matching sku and return the parent.
				$sku_to_parent_id = $wpdb->get_col( $wpdb->prepare( "SELECT p.post_parent as post_id FROM {$wpdb->posts} as p join {$wpdb->wc_product_meta_lookup} ml on p.ID = ml.product_id and ml.sku LIKE '%%%s%%' where p.post_parent <> 0 group by p.post_parent", wc_clean( $_GET['s'] ) ) ); // phpcs:ignore

				// Search for a regular product that matches the sku.
				$sku_to_id  = $wpdb->get_col( $wpdb->prepare( "SELECT product_id FROM {$wpdb->wc_product_meta_lookup} WHERE sku LIKE '%%%s%%';", wc_clean( $_GET['s'] ) ) ); // phpcs:ignore
				$search_ids = array_merge( $sku_to_id, $sku_to_parent_id );
				$search_ids = array_filter( array_map( 'absint', $search_ids ) );

				if ( count( $search_ids ) > 0 ) {
					$query['where'] = str_replace( '))', ") OR ({$wpdb->posts}.ID IN (" . implode( ',', $search_ids ) . ")))", $query['where'] ); // phpcs:ignore
				}
			}
		}

		$query['group_by'] = 'GROUP BY terms.term_id';
		$query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
		$query             = implode( ' ', $query );

		// We have a query - let's see if cached results of this query already exist.
		$query_hash = md5( $query );

		// Maybe store a transient of the count values.
		$cache = apply_filters( 'woocommerce_layered_nav_count_maybe_cache', true );
		if ( true === $cache ) {
			$cached_counts = (array) get_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ) );
		} else {
			$cached_counts = array();
		}

		if ( ! isset( $cached_counts[ $query_hash ] ) ) {
			$results                      = $wpdb->get_results( $query, ARRAY_A ); // @codingStandardsIgnoreLine
			$counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
			$cached_counts[ $query_hash ] = $counts;
			if ( true === $cache ) {
				set_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ), $cached_counts, DAY_IN_SECONDS );
			}
		}

		return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
	}

	/**
	 * Show list based layered nav.
	 *
	 * @param array  $terms      Terms.
	 * @param string $taxonomy   Taxonomy.
	 * @param string $query_type Query Type.
	 * @param array  $instance   Widget instance.
	 *
	 * @return bool   Will nav display?
	 */
	protected function layered_nav_list( $terms, $taxonomy, $query_type, $instance ) {
		$style              = isset( $instance['style'] ) ? $instance['style'] : 'list';
		$swatches_size      = isset( $instance['swatches_size'] ) ? $instance['swatches_size'] : 'm';
		$labels             = isset( $instance['labels'] ) ? $instance['labels'] : false;
		$tooltips           = isset( $instance['tooltips'] ) ? $instance['tooltips'] : false;
		$show_count         = isset( $instance['show_count'] ) ? $instance['show_count'] : false;
		$term_counts        = $this->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );
		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		$found              = false;
		$wrapper_classes    = '';
		$base_link          = $this->get_page_base_url();

		$wrapper_classes .= ' xts-layout-' . $style;

		?>
		<div class="xts-scroll">
			<ul class="xts-filter-list xts-scroll-content<?php echo esc_attr( $wrapper_classes ); ?>">
				<?php foreach ( $terms as $term ) : ?>
					<?php
					$current_values = isset( $_chosen_attributes[ $taxonomy ]['terms'] ) ? $_chosen_attributes[ $taxonomy ]['terms'] : array();
					$option_is_set  = in_array( $term->slug, $current_values, true );
					$count          = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

					// Skip the term for the current archive.
					if ( $this->get_current_term_id() === $term->term_id ) {
						continue;
					}

					// Only show options with count > 0.
					if ( 0 < $count ) {
						$found = true;
					} elseif ( 0 === $count && ! $option_is_set ) {
						continue;
					}

					$filter_name = 'filter_' . wc_attribute_taxonomy_slug( $taxonomy );
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( wp_unslash( $_GET[ $filter_name ] ) ) ) : array(); // phpcs:ignore
					$current_filter = array_map( 'sanitize_title', $current_filter );

					if ( ! in_array( $term->slug, $current_filter, true ) ) {
						$current_filter[] = $term->slug;
					}

					$link = remove_query_arg( $filter_name, $base_link );

					if ( is_wp_error( $link ) ) {
						$link = '';
					}

					// Add current filters to URL.
					foreach ( $current_filter as $key => $value ) {
						// Exclude query arg for current term archive term.
						if ( $value === $this->get_current_term_slug() ) {
							unset( $current_filter[ $key ] );
						}

						// Exclude self so filter can be unset on click.
						if ( $option_is_set && $value === $term->slug ) {
							unset( $current_filter[ $key ] );
						}
					}

					if ( ! empty( $current_filter ) ) {
						asort( $current_filter );
						$link = add_query_arg( $filter_name, implode( ',', $current_filter ), $link );

						// Add Query type Arg to URL.
						if ( 'or' === $query_type && ! ( 1 === count( $current_filter ) && $option_is_set ) ) {
							$link = add_query_arg( 'query_type_' . wc_attribute_taxonomy_slug( $taxonomy ), 'or', $link );
						}
						$link = str_replace( '%2C', ',', $link );
					}

					// Add swatches block.
					$swatch_styles    = '';
					$swatch_classes   = '';
					$wrapper_classes  = '';
					$color            = get_term_meta( $term->term_id, '_xts_attribute_color', true );
					$image            = get_term_meta( $term->term_id, '_xts_attribute_image', true );
					$wrapper_classes .= $option_is_set ? ' xts-active' : '';

					$swatch_classes .= ' xts-size-' . $swatches_size;
					$swatch_classes .= xts_get_opt( 'brands_attribute' ) === $taxonomy ? ' xts-with-brand' : '';
					$swatch_classes .= $tooltips ? ' xts-with-tooltip' : '';

					if ( isset( $color['idle'] ) && $color['idle'] ) {
						if ( xts_get_opt( 'brands_attribute' ) !== $taxonomy ) {
							if ( $tooltips ) {
								xts_enqueue_js_library( 'tooltip' );
								xts_enqueue_js_script( 'tooltip' );
							}
							$swatch_classes .= ' xts-with-bg';
						}

						$swatch_styles = 'background-color:' . $color['idle'] . ';';
					} elseif ( isset( $image['id'] ) && $image['id'] ) {
						$image_url = wp_get_attachment_image_url( $image['id'] );

						if ( xts_get_opt( 'brands_attribute' ) !== $taxonomy ) {
							if ( $tooltips ) {
								xts_enqueue_js_library( 'tooltip' );
								xts_enqueue_js_script( 'tooltip' );
							}
							$swatch_classes .= ' xts-with-bg';
						}

						$swatch_styles = 'background-image: url(' . $image_url . ');';
					} else {
						$swatch_classes .= ' xts-with-text';
					}
					?>

					<li class="xts-filter-item<?php echo esc_attr( $wrapper_classes ); ?>">
						<?php if ( $option_is_set || $count > 0 ) : ?>
							<a rel="nofollow" href="<?php echo esc_url( apply_filters( 'woocommerce_layered_nav_link', $link ) ); ?>" class="xts-filter-link">
						<?php else : ?>
							<span>
						<?php endif; ?>

							<span class="xts-swatch xts-filter-swatch<?php echo esc_attr( $swatch_classes ); ?>" style="<?php echo esc_attr( $swatch_styles ); ?>">
								<?php echo esc_html( $term->name ); ?>
							</span>

							<?php if ( $swatch_styles && $labels ) : ?>
								<span class="xts-filter-item-name">
									<?php echo esc_html( $term->name ); ?>
								</span>
							<?php endif; ?>

						<?php if ( $option_is_set || $count > 0 ) : ?>
							</a>
						<?php else : ?>
							</span>
						<?php endif; ?>

						<?php if ( $show_count ) : ?>
							<span class="xts-count">
								(<?php echo esc_html( $count ); ?>)
							</span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php

		return $found;
	}

	/**
	 * Get current page URL for layered nav items.
	 *
	 * @return string
	 */
	protected function get_page_base_url() {
		$link = '';
		// Base Link decided by current page.
		if ( Constants::is_defined( 'SHOP_IS_ON_FRONT' ) ) {
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

		/**
		 * Search Arg.
		 * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
		 */
		if ( get_search_query() ) {
			$link = add_query_arg( 's', rawurlencode( wp_specialchars_decode( get_search_query() ) ), $link );
		}

		// Post Type Arg.
		if ( isset( $_GET['post_type'] ) ) { // phpcs:ignore
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
		if ( $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes() ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found, WordPress.CodeAnalysis.AssignmentInCondition.Found
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

		return apply_filters( 'woocommerce_widget_get_current_page_url', $link, $this );
	}
}
