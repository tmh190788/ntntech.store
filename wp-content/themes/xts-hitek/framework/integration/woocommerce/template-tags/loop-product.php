<?php
/**
 * Woocommerce loop product template file
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_shop_page_content' ) ) {
	/**
	 * Shop page custom content.
	 *
	 * @since 1.0.0
	 */
	function xts_shop_page_content() {
		$shop_page_content = xts_get_opt( 'shop_page_content' );

		if ( ! $shop_page_content || ! xts_is_shop_archive() ) {
			return;
		}

		?>
		<div class="xts-shop-content">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<?php echo xts_get_html_block_content( $shop_page_content ); // phpcs:ignore ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	add_action( 'xts_after_header', 'xts_shop_page_content', 20 );
}

if ( ! function_exists( 'xts_shop_filters_area' ) ) {
	/**
	 * Shop page filters and custom content
	 *
	 * @since 1.0.0
	 */
	function xts_shop_filters_area() {
		if ( ! xts_get_opt( 'shop_filters_area' ) ) {
			return;
		}

		$wrapper_classes = '';
		$inner_classes   = '';
		$content_type    = xts_get_opt( 'shop_filters_area_content_type' );

		$wrapper_classes .= xts_get_opt( 'shop_filters_area_always_open' ) && woocommerce_products_will_display() ? ' xts-always-open' : '';
		if ( 'widgets' === $content_type ) {
			$inner_classes .= xts_get_row_classes( xts_get_widget_column_numbers(), 2, 1, 30 );
		} else {
			$wrapper_classes .= ' xts-with-content';
		}

		?>
			<div class="xts-filters-area<?php echo esc_attr( $wrapper_classes ); ?>">
				<div class="xts-filters-area-inner<?php echo esc_attr( $inner_classes ); ?>">
					<?php do_action( 'xts_before_filters_area_content' ); ?>

					<?php if ( 'html_block' === $content_type ) : ?>
						<?php echo xts_get_html_block_content( xts_get_opt( 'shop_filters_area_html_block' ) ); // phpcs:ignore ?>
					<?php else : ?>
						<?php do_action( 'xts_before_filters_area_widgets_sidebar' ); ?>

						<?php dynamic_sidebar( 'filters-area-widget-sidebar' ); ?>

						<?php do_action( 'xts_after_filters_area_widgets_sidebar' ); ?>
					<?php endif; ?>
				</div>
			</div>
		<?php
	}

	add_action( 'xts_shop_filters_area', 'xts_shop_filters_area' );
}

if ( ! function_exists( 'xts_shop_filters_area_button' ) ) {
	/**
	 * Get price widget
	 *
	 * @since 1.0.0
	 */
	function xts_shop_filters_area_button() {
		$content_type = xts_get_opt( 'shop_filters_area_content_type' );

		if ( wc_get_loop_prop( 'is_shortcode' ) || ! wc_get_loop_prop( 'is_paginated' ) || ( ! woocommerce_products_will_display() && 'widgets' === $content_type ) || xts_get_opt( 'shop_filters_area_always_open' ) || ( 'html_block' === $content_type && ! xts_get_opt( 'shop_filters_area_html_block' ) ) ) {
			return;
		}

		xts_enqueue_js_script( 'shop-filters-area' );

		?>
			<div class="xts-filters-area-btn xts-action-btn xts-style-inline">
				<a href="#"><?php esc_html_e( 'Filters', 'xts-theme' ); ?></a>
			</div>
		<?php
	}
}


if ( ! function_exists( 'xts_empty_shop_filters_area_text' ) ) {
	/**
	 * Empty shop filers area text
	 *
	 * @since 1.0.0
	 */
	function xts_empty_shop_filters_area_text() {
		?>
			<div class="xts-empty-filters-area col-12">
				<?php if ( 'widgets' === xts_get_opt( 'shop_filters_area_content_type' ) ) : ?>
					<?php esc_html_e( 'You need to add widgets in Dashboard -> Appearance -> Widgets -> Shop filters widget area', 'xts-theme' ); ?>
				<?php else : ?>
					<?php esc_html_e( 'You need to select Html block in Theme settings -> Shop -> Filters area -> Html block', 'xts-theme' ); ?>
				<?php endif; ?>
			</div>
		<?php
	}
}

if ( ! function_exists( 'xts_grid_categories_template' ) ) {
	/**
	 * Get the product categories list.
	 *
	 * @since 1.0.0
	 */
	function xts_grid_categories_template() {
		global $product;

		if ( ! xts_get_loop_prop( 'product_categories' ) ) {
			return;
		}

		?>
     <?php
        $terms = get_the_terms( $product->get_id(), 'product_cat' );
        $is_stock_list = false;
				$stock_list_url = '';
        if (!empty($terms)) {
          foreach($terms as $term) {
            $term_id = $term->term_id;
            if (get_field( 'stocklist_page',  'product_cat_' . $term_id) == 1) {
              $is_stock_list = true;
							$stock_list_url = get_category_link( $term_id );
              break;
            }
          }
        }

		$terms_brand = get_the_terms( $product->get_id(), 'product_brand' );
		if (!empty($terms_brand)) {
			foreach ( $terms_brand as $term_brand ){
				if ( $term_brand->parent == 0 ) {
					$brand_name=  $term_brand->name;
					$brand_slug= $term_brand->slug;
				}
			}  
			$brand_link=get_term_link($term_brand->slug, 'product_brand');
		}
      ?>
		<div class="xts-product-categories xts-product-meta <?=$is_stock_list ? 'stocklist-product' : ''?>">
			<?php
        if (!$is_stock_list) {
          echo wc_get_product_category_list( $product->get_id(), ', ' ); // phpcs:ignore
        } else {
					// ?keyword=bear&filter=description
      ?>
        <!-- <div class="stocklist-info">Maker: <a href="<?=$stock_list_url?>?keyword=<?= get_field('maker') ?>&filter=maker"><?= get_field('maker') ?></a></div> -->
		<div class="stocklist-info">Model: <a href="<?=$stock_list_url?>?keyword=<?= get_field('model') ?>&filter=model"><?= get_field('model') ?></a></div>
		<div class="stocklist-info">Maker:  <a href="<?=$brand_link?>"><?= $brand_name ?></a></div>
        <div class="stocklist-info">Description: <a href="<?=$stock_list_url?>?keyword=<?=get_field('description')?>&filter=description"><?=get_field('description')?></a></div>
      <?php
        }
      ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'xts_grid_attribute_template' ) ) {
	/**
	 * Get the product tags list.
	 */
	function xts_grid_attribute_template() {
		global $product;
		$product_attributes = $product->get_attributes();
		$attributes         = array();

		if ( ! $product_attributes || ! xts_get_loop_prop( 'product_attributes' ) ) {
			return;
		}

		foreach ( $product_attributes as $attribute ) {
			if ( $attribute->get_name() === xts_get_opt( 'brands_attribute' ) || ! $attribute->get_visible() || $attribute->get_variation() ) {
				continue;
			}

			$attributes[] = $product->get_attribute( $attribute->get_name() );
		}
		?>
		<div class="xts-product-attributes xts-product-meta">
			<?php echo esc_html( implode( ', ', $attributes ) ); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'xts_products_per_row_select' ) ) {
	/**
	 * Items per page select on the shop page
	 *
	 * @since 1.0.0
	 */
	function xts_products_per_row_select() {
		if ( ! xts_get_opt( 'products_per_row_variations' ) ) {
			return;
		}

		$variations = xts_get_opt( 'products_per_row_variations' );

		?>
			<div class="xts-products-per-row">
				<?php foreach ( $variations as $size ) : ?>
					<?php
					$classes = '';

					$classes .= ' xts-per-row-' . $size;
					if ( xts_get_products_per_row() === intval( $size ) ) {
						$classes .= ' xts-active';
					}

					?>
					<a href="<?php echo esc_url( add_query_arg( 'products_per_row', $size, xts_get_shop_page_link( true ) ) ); ?>" rel="nofollow" class="xts-per-row-variation<?php echo esc_attr( $classes ); ?>">
						<?php echo xts_get_svg( 'product-grid/column-' . $size ); // phpcs:ignore ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php
	}

	add_action( 'woocommerce_before_shop_loop', 'xts_products_per_row_select', 26 );
}

if ( ! function_exists( 'xts_products_per_page_select' ) ) {
	/**
	 * Items per page select on the shop page
	 *
	 * @since 1.0.0
	 */
	function xts_products_per_page_select() {
		if ( ! xts_get_opt( 'products_per_page_variations' ) ) {
			return;
		}

		$products_per_page = explode( ',', xts_get_opt( 'products_per_page_variations' ) );

		?>
		<div class="xts-products-per-page">
			<span class="xts-per-page-title">
				<?php esc_html_e( 'Show', 'xts-theme' ); ?>
			</span>

			<?php foreach ( $products_per_page as $value ) : ?>
				<a href="<?php echo esc_url( add_query_arg( 'products_per_page', $value, xts_get_shop_page_link( true ) ) ); ?>" rel="nofollow" class="<?php echo xts_get_products_per_page() === intval( $value ) ? ' xts-active' : ''; ?>">
					<?php if ( '-1' === $value ) : ?>
						<?php esc_html_e( 'All', 'xts-theme' ); ?>
					<?php else : ?>
						<?php echo esc_html( $value ); ?>
					<?php endif; ?>
				</a>

				<span class="xts-per-page-border"></span>
			<?php endforeach; ?>
		</div>
		<?php
	}

	add_action( 'woocommerce_before_shop_loop', 'xts_products_per_page_select', 25 );
}

if ( ! function_exists( 'xts_get_product_main_loop' ) ) {
	/**
	 * Main woocommerce loop
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $fragments Fragments.
	 */
	function xts_get_product_main_loop( $fragments = false ) {
		global $paged, $wp_query;

		$max_page = $wp_query->max_num_pages;

		if ( $fragments ) {
			ob_start();
		}

		if ( $fragments && isset( $_GET['loop'] ) ) { // phpcs:ignore
			xts_set_loop_prop( 'woocommerce_loop', (int) sanitize_text_field( $_GET['loop'] ) ); // phpcs:ignore
		}

		?>

		<?php if ( woocommerce_product_loop() ) : ?>
			<?php if ( ! $fragments ) : ?>
				<?php woocommerce_product_loop_start(); ?>
			<?php endif; ?>

			<?php if ( wc_get_loop_prop( 'total' ) || $fragments ) : ?>
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>

					<?php
					/**
					 * Hook: woocommerce_shop_loop.
					 *
					 * @hooked WC_Structured_Data::generate_product_data() - 10
					 */
					do_action( 'woocommerce_shop_loop' );
					?>

					<?php wc_get_template_part( 'content', 'product' ); ?>
				<?php endwhile; ?>
			<?php endif; ?>

			<?php if ( ! $fragments ) : ?>
				<?php woocommerce_product_loop_end(); ?>

				<?php
				/**
				 * Hook: woocommerce_after_shop_loop.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
				?>
			<?php endif; ?>
		<?php else : ?>
			<?php
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @hooked wc_no_products_found - 10
			 */
			do_action( 'woocommerce_no_products_found' );
			?>
		<?php endif; ?>

		<?php

		if ( $fragments ) {
			$output = array(
				'items'       => ob_get_clean(),
				'status'      => $max_page > $paged ? 'have-posts' : 'no-more-posts',
				'nextPage'    => str_replace( '&#038;', '&', next_posts( $max_page, false ) ),
				'currentPage' => strtok( xts_get_current_url(), '?' ),
			);

			echo wp_json_encode( $output );
		}
	}

	add_action( 'xts_product_main_loop', 'xts_get_product_main_loop' );
}

if ( ! function_exists( 'woocommerce_template_loop_product_title' ) ) {
	/**
	 * Show the product title in the product loop. By default this is an H2.
	 *
	 * @since 1.0.0
	 */
	function woocommerce_template_loop_product_title() {
		?>
		<h2 class="woocommerce-loop-product__title xts-entities-title">
			<a href="<?php echo esc_url( get_the_permalink() ); ?>">
				<?php echo get_the_title(); // phpcs:ignore ?>
			</a>
		</h2>
		<?php
	}
}

if ( ! function_exists( 'xts_product_loop_thumbnail' ) ) {
	/**
	 * Get the product thumbnail for the loop.
	 *
	 * @since 1.0.0
	 */
	function xts_product_loop_thumbnail() {
		global $product;

		$attachment_ids    = $product->get_gallery_image_ids();
		$image_size        = xts_get_loop_prop( 'product_image_size' );
		$custom_image_size = xts_get_loop_prop( 'product_image_custom' );
		$product_design    = xts_get_loop_prop( 'product_design' );

		?>
		<div class="xts-product-image">
			<?php
			echo xts_get_image_html( // phpcs:ignore
				array(
					'image_size'             => $image_size,
					'image_custom_dimension' => $custom_image_size,
					'image'                  => array(
						'id' => get_post_thumbnail_id(),
					),
				),
				'image'
			);
			?>
		</div>

		<?php if ( isset( $attachment_ids[0] ) && $attachment_ids[0] && 'small' !== $product_design && 'small-bg' !== $product_design && 'mask' !== $product_design && xts_get_loop_prop( 'product_hover_image' ) ) : ?>
			<div class="xts-product-hover-image xts-fill">
				<?php
				echo xts_get_image_html( // phpcs:ignore
					array(
						'image_size'             => $image_size,
						'image_custom_dimension' => $custom_image_size,
						'image'                  => array(
							'id' => $attachment_ids[0],
						),
					),
					'image'
				);
				?>
			</div>
		<?php endif; ?>
		<?php
	}

	add_action( 'woocommerce_before_shop_loop_item_title', 'xts_product_loop_thumbnail', 10 );
}

if ( ! function_exists( 'xts_clear_filters_btn' ) ) {
	/**
	 * Clear all filters button
	 *
	 * @since 1.0.0
	 */
	function xts_clear_filters_btn() {
		global $wp;

		$url               = home_url( add_query_arg( array( $_GET ), $wp->request ) ); // phpcs:ignore
		$chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();

		$min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : ''; // phpcs:ignore
		$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : ''; // phpcs:ignore

		if ( 0 < count( $chosen_attributes ) || $min_price || $max_price ) {
			$reset_url = strtok( $url, '?' );

			if ( isset( $_GET['post_type'] ) ) { // phpcs:ignore
				$reset_url = add_query_arg( 'post_type', sanitize_text_field( wp_unslash( $_GET['post_type'] ) ), $reset_url ); // phpcs:ignore
			}

			?>
				<div class="xts-clear-filters">
					<a class="xts-clear-filters-btn" href="<?php echo esc_url( $reset_url ); ?>">
						<?php echo esc_html__( 'Clear filters', 'xts-theme' ); ?>
					</a>
				</div>
			<?php
		}
	}
}

if ( ! function_exists( 'xts_active_product_filters' ) ) {
	/**
	 * Get the product thumbnail for the loop.
	 *
	 * @since 1.0.0
	 */
	function xts_active_product_filters() {
		xts_clear_filters_btn();

		the_widget(
			'WC_Widget_Layered_Nav_Filters',
			array(
				'title' => '',
			),
			array()
		);
	}

	add_action( 'xts_active_product_filters', 'xts_active_product_filters', 10 );
}
