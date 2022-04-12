<?php
/**
 * Products template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\AJAX_Response;
use XTS\Framework\Modules;

if ( ! function_exists( 'xts_get_products_element_config' ) ) {
	/**
	 * Products element config
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_products_element_config() {
		return array(
			// General.
			'items_per_page'               => array( 'size' => 10 ),
			'taxonomies'                   => '',
			'include'                      => '',
			'product_source'               => 'all_products',

			// Query.
			'orderby'                      => 'id',
			'order'                        => 'desc',
			'offset'                       => '',
			'exclude'                      => '',
			'meta_key'                     => '', // phpcs:ignore
			'query_type'                   => 'OR',

			// General.
			'design'                       => 'inherit',
			'color_scheme'                 => 'dark',
			'image_size'                   => 'woocommerce_thumbnail',
			'image_size_custom'            => '',

			// Layout.
			'pagination'                   => 'without',
			'view'                         => 'grid',
			'columns'                      => array( 'size' => 3 ),
			'columns_tablet'               => array( 'size' => '' ),
			'columns_mobile'               => array( 'size' => '' ),
			'spacing'                      => xts_get_default_value( 'items_gap' ),
			'masonry'                      => 'no',
			'different_sizes'              => '0',
			'different_sizes_position'     => '2,5,8,9',

			// Carousel.
			'carousel_items'               => array( 'size' => 4 ),
			'carousel_items_tablet'        => array( 'size' => '' ),
			'carousel_items_mobile'        => array( 'size' => '' ),
			'carousel_spacing'             => xts_get_default_value( 'items_gap' ),
			'autoplay'                     => 'no',
			'autoplay_speed'               => array( 'size' => 2000 ),
			'infinite_loop'                => 'no',
			'center_mode'                  => 'no',
			'auto_height'                  => 'no',
			'init_on_scroll'               => 'yes',
			'dots'                         => 'no',
			'dots_color_scheme'            => 'dark',
			'arrows'                       => 'yes',
			'arrows_vertical_position'     => xts_get_default_value( 'carousel_arrows_vertical_position' ),
			'arrows_color_scheme'          => xts_get_default_value( 'carousel_arrows_color_scheme' ),

			// Visibility.
			'countdown'                    => '0',
			'quantity'                     => '0',
			'stock_progress_bar'           => '0',
			'categories'                   => '0',
			'product_attributes'           => '0',
			'brands'                       => '0',
			'hover_image'                  => '1',
			'rating'                       => '1',

			// Extra.
			'ajax_page'                    => '',
			'animation_in_view'            => 'no',
			'xts_animation_items'          => '',
			'xts_animation_duration_items' => 'normal',
			'xts_animation_delay_items'    => '',
			'lazy_loading'                 => 'no',
			'force_not_ajax'               => 'no',
			'loop'                         => 0,
		);
	}
}

if ( ! function_exists( 'xts_products_template' ) ) {
	/**
	 * Products template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @return array|false|string|void
	 */
	function xts_products_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		global $product;

		$default_args = xts_get_products_element_config();

		$element_args = wp_parse_args( $element_args, $default_args );
		extract( $element_args ); // phpcs:ignore

		$paged                          = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$is_ajax                        = xts_is_ajax() && 'full-page' !== xts_is_ajax() && 'yes' !== $force_not_ajax;
		$element_args['force_not_ajax'] = 'no';
		$encoded_atts                   = wp_json_encode( array_intersect_key( $element_args, $default_args ) );
		$different_sizes_position       = explode( ',', $different_sizes_position );
		$ordering_args                  = WC()->query->get_catalog_ordering_args( $orderby, $order );
		$meta_query                     = WC()->query->get_meta_query();
		$tax_query                      = WC()->query->get_tax_query();
		$wrapper_classes                = '';
		$carousel_attrs                 = '';
		$uniqid                         = uniqid();

		if ( 'inherit' !== $design ) {
			xts_set_loop_prop( 'product_loop_quantity', $quantity );
		}

		if ( 'inherit' === $design ) {
			$design = xts_get_opt( 'product_loop_design' );
		}

		xts_set_loop_prop( 'product_image_size', $image_size );
		xts_set_loop_prop( 'product_image_custom', $image_size_custom );
		xts_set_loop_prop( 'product_countdown', $countdown );
		xts_set_loop_prop( 'product_stock_progress_bar', $stock_progress_bar );
		xts_set_loop_prop( 'product_rating', $rating );
		xts_set_loop_prop( 'product_categories', $categories );
		xts_set_loop_prop( 'product_attributes', $product_attributes );
		xts_set_loop_prop( 'product_brands', $brands );
		xts_set_loop_prop( 'product_hover_image', $hover_image );
		xts_set_loop_prop( 'product_design', $design );

		if ( $ajax_page > 1 ) {
			$paged = $ajax_page;
		}

		if ( 'list_of_products' === $orderby ) {
			$ordering_args['orderby'] = $orderby;
		}

		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'paged'               => $paged,
			'orderby'             => $ordering_args['orderby'],
			'order'               => $ordering_args['order'],
			'posts_per_page'      => $items_per_page['size'],
			'meta_query'          => $meta_query, // phpcs:ignore
			'tax_query'           => $tax_query, // phpcs:ignore
		);

		if ( $ordering_args['meta_key'] ) {
			$query_args['meta_key'] = $ordering_args['meta_key']; // phpcs:ignore
		}

		if ( $meta_key ) {
			$query_args['meta_key'] = $meta_key; // phpcs:ignore
		}

		if ( $include && 'list_of_products' === $product_source ) {
			$query_args['post__in'] = $include;
		}

		if ( $exclude ) {
			$query_args['post__not_in'] = $exclude;
		}

		if ( $offset ) {
			$query_args['offset'] = $offset;
		}

		if ( $order ) {
			$query_args['order'] = $order;
		}

		if ( 'sale' === $product_source ) {
			$query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
		}

		if ( 'bestsellers' === $product_source ) {
			$query_args['orderby']  = 'meta_value_num';
			$query_args['meta_key'] = 'total_sales'; // phpcs:ignore
			$query_args['order']    = 'DESC';
		}

		if ( 'new' === $product_source ) {
			$query_args['meta_query'][] = array(
				'key'      => '_xts_product_label_new',
				'value'    => '1',
				'operator' => 'IN',
			);
		}

		if ( 'featured' === $product_source ) {
			$query_args['tax_query'][] = array(
				'taxonomy'         => 'product_visibility',
				'field'            => 'name',
				'terms'            => 'featured',
				'operator'         => 'IN',
				'include_children' => false,
			);
		}

		if ( apply_filters( 'xts_hide_out_of_stock_items', false ) && 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$query_args['meta_query'][] = array(
				'key'     => '_stock_status',
				'value'   => 'outofstock',
				'compare' => 'NOT IN',
			);
		}

		if ( $taxonomies ) {
			$terms = get_terms(
				get_object_taxonomies( 'product' ),
				array(
					'orderby'    => 'name',
					'include'    => $taxonomies,
					'hide_empty' => false,
				)
			);

			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				if ( 'featured' === $product_source ) {
					$query_args['tax_query'] = array( 'relation' => 'AND' ); // phpcs:ignore
				}

				$relation = $query_type ? $query_type : 'OR';

				if ( count( $terms ) > 1 ) {
					$query_args['tax_query']['categories'] = array( 'relation' => $relation );
				}

				foreach ( $terms as $term ) {
					$query_args['tax_query']['categories'][] = array(
						'taxonomy'         => $term->taxonomy,
						'field'            => 'slug',
						'terms'            => array( $term->slug ),
						'include_children' => true,
						'operator'         => 'IN',
					);
				}
			}
		}

		if ( 'upsells' === $product_source && $product && is_singular( 'product' ) ) {
			$query_args['post__in'] = array_merge( array( 0 ), $product->get_upsell_ids() );
		}

		if ( 'related' === $product_source && $product && is_singular( 'product' ) ) {
			$query_args['post__in'] = array_merge( array( 0 ), wc_get_related_products( $product->get_id(), $query_args['posts_per_page'], $product->get_upsell_ids() ) );
		}

		if ( 'top_rated' === $product_source ) {
			add_filter( 'posts_clauses', 'xts_order_by_rating_post_clauses' );
			$products = new WP_Query( $query_args );
			remove_action( 'posts_clauses', 'xts_order_by_rating_post_clauses' );
		} else {
			$products = new WP_Query( $query_args );
		}

		if ( ! $products || ! $products->have_posts() ) {
			return;
		}

		// Wrapper classes.
		$wrapper_classes .= ' xts-autoplay-animations-off';
		$wrapper_classes .= ' xts-prod-design-' . $design;

		if ( 'summary' === $design || 'summary-alt' === $design ) {
			wp_enqueue_script( 'imagesloaded' );
			xts_enqueue_js_script( 'product-hover-summary' );
		}
		if ( 'dark' !== $color_scheme ) {
			$wrapper_classes .= ' xts-scheme-' . $color_scheme . '-prod';
		}
		if ( 'carousel' === $view ) {
			$wrapper_classes .= xts_get_carousel_classes( $element_args );
			$wrapper_classes .= xts_get_row_classes( $carousel_items['size'], $carousel_items_tablet['size'], $carousel_items_mobile['size'], $carousel_spacing );
			$carousel_attrs  .= xts_get_carousel_atts( $element_args );
		} else {
			$wrapper_classes .= xts_get_row_classes( $columns['size'], $columns_tablet['size'], $columns_mobile['size'], $spacing );
		}
		if ( 'yes' === $masonry ) {
			wp_enqueue_script( 'imagesloaded' );
			xts_enqueue_js_library( 'isotope-bundle' );
			xts_enqueue_js_script( 'masonry-layout' );
			$wrapper_classes .= ' xts-masonry-layout';
		}
		if ( $different_sizes ) {
			$wrapper_classes .= ' xts-different-sizes';
		}
		if ( 'yes' === $animation_in_view ) {
			xts_enqueue_js_script( 'items-animation-in-view' );
			$wrapper_classes .= ' xts-in-view-animation';
		}
		if ( 'default' !== xts_get_opt( 'product_title_lines_limit' ) ) {
			$wrapper_classes .= ' xts-title-limit-' . xts_get_opt( 'product_title_lines_limit' );
		}
		if ( 'arrows' === $pagination ) {
			$wrapper_classes .= ' xts-pagination-arrows';
		}

		$index = $element_args['loop'];

		// Lazy loading.
		$lazy_module = Modules::get( 'lazy-loading' );
		if ( 'yes' === $lazy_loading ) {
			$lazy_module->lazy_init( true );
		} elseif ( 'no' === $lazy_loading ) {
			$lazy_module->lazy_disable( true );
		}

		?>

		<?php if ( ! $is_ajax ) : ?>
			<?php if ( 'arrows' === $pagination ) : ?>
				<div class="xts-arrows-loader" data-id="<?php echo esc_attr( $uniqid ); ?>">
					<span class="xts-loader"></span>
				</div>
			<?php endif; ?>

			<div id="<?php echo esc_attr( $uniqid ); ?>" class="xts-products<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo wp_kses( $carousel_attrs, true ); ?> data-source="element" data-paged="1" data-atts="<?php echo esc_attr( $encoded_atts ); ?>" data-animation-delay="<?php echo esc_attr( $xts_animation_delay_items ); ?>">
		<?php endif ?>

		<?php
		if ( $is_ajax ) {
			ob_start();
		}
		?>

		<?php while ( $products->have_posts() ) : ?>
			<?php $products->the_post(); ?>

			<?php
			$index ++;

			$column_classes = '';

			if ( in_array( $index, $different_sizes_position, false ) && $different_sizes ) { // phpcs:ignore
				$column_classes .= ' xts-wide';
			}

			// Animations.
			if ( 'yes' === $animation_in_view && $xts_animation_items ) {
				$column_classes .= ' xts-animation-' . $xts_animation_items;
				$column_classes .= ' xts-animation-' . $xts_animation_duration_items;
			}

			?>

			<div class="xts-col<?php echo esc_attr( $column_classes ); ?>" data-loop="<?php echo esc_attr( $index ); ?>">
				<?php do_action( 'xts_before_shop_loop_product' ); ?>
				<div <?php wc_product_class( 'xts-product', get_post( get_the_ID() ) ); ?> data-id="<?php echo esc_attr( get_the_ID() ); ?>">
					<?php wc_get_template_part( 'content', 'product-' . $design ); ?>
				</div>
			</div>
		<?php endwhile; ?>

		<?php
		if ( $is_ajax ) {
			$output = ob_get_clean();
		}
		?>

		<?php if ( ! $is_ajax ) : ?>
			</div>
		<?php endif ?>

		<?php if ( $products->max_num_pages > 1 && ! $is_ajax && 'carousel' !== $view ) : ?>
			<?php if ( 'load_more' === $pagination || 'infinite' === $pagination ) : ?>
				<?php xts_loadmore_pagination( $pagination, 'shop', $products->max_num_pages, $uniqid, 'element' ); ?>
			<?php elseif ( 'arrows' === $pagination ) : ?>
				<?php xts_enqueue_js_script( 'shop-load-more' ); ?>
				<div class="xts-ajax-arrows xts-arrows-design-default xts-arrows-vpos-sides" data-id="<?php echo esc_attr( $uniqid ); ?>">
					<div class="xts-nav-arrow xts-prev xts-disabled"><div class="xts-arrow-inner"></div></div>
					<div class="xts-nav-arrow xts-next"><div class="xts-arrow-inner"></div></div>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php

		wp_reset_postdata();
		xts_reset_loop();

		// Lazy loading.
		if ( 'yes' === $lazy_loading ) {
			$lazy_module->lazy_disable( true );
		} elseif ( 'no' === $lazy_loading ) {
			$lazy_module->lazy_init();
		}

		if ( $is_ajax ) {
			$output = array(
				'items'  => $output,
				'status' => $products->max_num_pages > $paged ? 'have-posts' : 'no-more-posts',
			);

			return $output;
		}

		return $products;
	}
}

if ( ! function_exists( 'xts_get_products_element_ajax' ) ) {
	/**
	 * Return products on AJAX
	 *
	 * @since 1.0.0
	 */
	function xts_get_products_element_ajax() {
		if ( $_POST['atts'] ) { // phpcs:ignore
			$atts              = $_POST['atts']; // phpcs:ignore
			$atts['ajax_page'] = ! $_POST['paged'] ? 2 : (int) $_POST['paged']; // phpcs:ignore

			AJAX_Response::send_response( xts_products_template( $atts ) );
		}
	}

	add_action( 'wp_ajax_xts_get_product_element', 'xts_get_products_element_ajax' );
	add_action( 'wp_ajax_nopriv_xts_get_product_element', 'xts_get_products_element_ajax' );
}

if ( ! function_exists( 'xts_order_by_rating_post_clauses' ) ) {
	/**
	 * Order by rating post clauses.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Query args.
	 *
	 * @return array
	 */
	function xts_order_by_rating_post_clauses( $args ) {
		global $wpdb;

		$args['where']  .= " AND $wpdb->commentmeta.meta_key = 'rating' ";
		$args['join']   .= "LEFT JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID) LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)";
		$args['orderby'] = "$wpdb->commentmeta.meta_value DESC";
		$args['groupby'] = "$wpdb->posts.ID";

		return $args;
	}
}
