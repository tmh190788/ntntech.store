<?php
/**
 * Woocommerce page title template functions file
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_page_title_checkout_steps' ) ) {
	/**
	 * Checkout steps in page title
	 *
	 * @since 1.0.0
	 */
	function xts_page_title_checkout_steps() {

		?>
			<ul class="xts-checkout-steps">
				<li class="xts-step-cart <?php echo ( is_cart() ) ? 'xts-active' : ''; ?>">
					<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
						<?php esc_html_e( 'Shopping cart', 'xts-theme' ); ?>
					</a>
				</li>

				<li class="xts-step-checkout <?php echo ( is_checkout() && ! is_order_received_page() ) ? 'xts-active' : ''; ?>">
					<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
						<?php esc_html_e( 'Checkout', 'xts-theme' ); ?>
					</a>
				</li>

				<li class="xts-step-complete <?php echo ( is_order_received_page() ) ? 'xts-active' : ''; ?>">
					<span>
						<?php esc_html_e( 'Order complete', 'xts-theme' ); ?>
					</span>
				</li>
			</ul>
		<?php
	}
}

if ( ! function_exists( 'xts_page_title_shop_categories_mobile_button' ) ) {
	/**
	 * Button to open categories on mobile.
	 *
	 * @since 1.0.0
	 */
	function xts_page_title_shop_categories_mobile_button() {
		if ( is_singular( 'product' ) || ! xts_get_opt( 'page_title_shop_categories' ) ) {
			return;
		}

		?>
			<div class="xts-action-btn xts-style-inline xts-show-cat-btn">
				<a href="#">
					<?php esc_html_e( 'Categories', 'xts-theme' ); ?>
				</a>
			</div>
		<?php
	}

	add_action( 'xts_after_shop_page_title', 'xts_page_title_shop_categories_mobile_button', 20 );
}

if ( ! function_exists( 'xts_page_title_shop_categories' ) ) {
	/**
	 * Display categories menu
	 *
	 * @since 1.0.0
	 */
	function xts_page_title_shop_categories() {
		global $wp_query;

		$show_subcategories        = xts_get_opt( 'page_title_shop_categories_ancestors' );
		$show_categories_neighbors = xts_get_opt( 'page_title_shop_categories_neighbors' );
		$all_link_icon             = xts_get_opt( 'page_title_shop_category_all_link_icon' );
		$icon_output               = '';
		$menu_style                = xts_get_default_value( 'shop_page_title_categories_menu_style' );

		// Setup current category.
		$current_cat = false;

		if ( is_tax( 'product_cat' ) ) {
			$current_cat = $wp_query->queried_object;
		}

		$list_args = array(
			'taxonomy'         => 'product_cat',
			'hide_empty'       => xts_get_opt( 'page_title_shop_hide_empty_categories' ),
			'menu_order'       => 'asc',
			'depth'            => 5,
			'child_of'         => 0,
			'title_li'         => '',
			'hierarchical'     => 1,
			'show_count'       => xts_get_opt( 'page_title_shop_categories_products_count' ),
			'show_option_none' => esc_html__( 'No product categories exist.', 'xts-theme' ),
			'walker'           => new XTS\Walker_Category( $menu_style ),
		);

		if ( xts_get_opt( 'page_title_shop_categories_exclude' ) ) {
			$list_args['exclude'] = xts_get_opt( 'page_title_shop_categories_exclude' );
		}

		$wrapper_classes  = '';
		$all_link_classes = '';

		$wrapper_classes .= xts_get_opt( 'page_title_shop_categories_products_count' ) ? ' xts-has-count' : ' xts-without-count';
		$wrapper_classes .= ' xts-style-' . $menu_style;

		if ( is_shop() ) {
			$all_link_classes = ' xts-active';
		}

		if ( xts_is_shop_on_front() ) {
			$shop_link = home_url();
		} else {
			$shop_link = get_post_type_archive_link( 'product' );
		}

		if ( is_object( $current_cat ) && ! get_term_children( $current_cat->term_id, 'product_cat' ) && $show_subcategories && ! $show_categories_neighbors ) {
			return;
		}

		// All link icon settings.
		if ( isset( $all_link_icon['id'] ) && $all_link_icon['id'] ) {
			$icon_output .= '<img src="' . esc_url( wp_get_attachment_image_url( $all_link_icon['id'] ) ) . '" alt="' .  esc_attr__( 'all products link', 'xts-theme' ) . '" class="xts-nav-img" />';
		}

		xts_enqueue_js_script( 'page-title-product-categories' );

		?>
			<ul class="xts-nav xts-nav-shop-cat xts-gap-m<?php echo esc_attr( $wrapper_classes ); ?>">
				<li class="xts-shop-all-link<?php echo esc_attr( $all_link_classes ); ?>">
					<a class="xts-nav-link" href="<?php echo esc_url( $shop_link ); ?>">
						<?php echo wp_kses( $icon_output, 'xts_media' ); ?>
						<span class="xts-nav-summary">
							<?php if ( 'underline-2' === $menu_style ) : ?>
								<span class="xts-nav-text">
									<span>
										<?php esc_html_e( 'All', 'xts-theme' ); ?>
									</span>
								</span>
							<?php else : ?>
								<span class="xts-nav-text">
									<?php esc_html_e( 'All', 'xts-theme' ); ?>
								</span>
							<?php endif; ?>

							<?php if ( xts_get_opt( 'page_title_shop_categories_products_count' ) ) : ?>
								<span class="xts-nav-count">
									<?php esc_html_e( 'Products', 'xts-theme' ); ?>
								</span>
							<?php endif; ?>
						</span>
					</a>
				</li>

				<?php if ( $show_subcategories ) : ?>
					<?php xts_page_title_shop_category_ancestors(); ?>
				<?php else : ?>
					<?php wp_list_categories( $list_args ); ?>
				<?php endif; ?>
			</ul>
		<?php
	}
}

if ( ! function_exists( 'xts_page_title_shop_category_ancestors' ) ) {
	/**
	 * Display ancestors of current category
	 *
	 * @since 1.0.0
	 */
	function xts_page_title_shop_category_ancestors() {
		global $wp_query;

		$current_cat = false;

		$show_categories_neighbors = xts_get_opt( 'page_title_shop_categories_neighbors' );
		$menu_style                = xts_get_default_value( 'shop_page_title_categories_menu_style' );

		if ( is_tax( 'product_cat' ) ) {
			$current_cat = $wp_query->queried_object;
		}

		$list_args = array(
			'taxonomy'         => 'product_cat',
			'hide_empty'       => xts_get_opt( 'page_title_shop_hide_empty_categories' ),
			'depth'            => 1,
			'child_of'         => 0,
			'pad_counts'       => 1,
			'title_li'         => '',
			'hierarchical'     => 1,
			'show_option_none' => esc_html__( 'No product categories exist.', 'xts-theme' ),
			'show_count'       => xts_get_opt( 'page_title_shop_categories_products_count' ),
			'current_category' => $current_cat ? $current_cat->term_id : '',
			'walker'           => new XTS\Walker_Category( $menu_style ),
		);

		// Show siblings and children only.
		if ( $current_cat ) {
			// Direct children are wanted.
			$include = get_terms(
				'product_cat',
				array(
					'fields'       => 'ids',
					'parent'       => $current_cat->term_id,
					'hierarchical' => true,
					'hide_empty'   => xts_get_opt( 'page_title_shop_hide_empty_categories' ),
				)
			);

			$list_args['include'] = $include;

			if ( ! $include && ! $show_categories_neighbors ) {
				return;
			}

			if ( $show_categories_neighbors ) {
				if ( get_term_children( $current_cat->term_id, 'product_cat' ) ) {
					$list_args['child_of'] = $current_cat->term_id;
				} elseif ( 0 !== $current_cat->parent ) {
					$list_args['child_of'] = $current_cat->parent;
				}
			}
		}

		wp_list_categories( $list_args );
	}
}
