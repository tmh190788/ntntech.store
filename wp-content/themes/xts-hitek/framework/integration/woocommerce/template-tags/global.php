<?php
/**
 * Woocommerce global template functions file
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_mobile_menu_custom_links' ) ) {
	/**
	 * Add custom links to mobile menu.
	 *
	 * @since 1.0.0
	 *
	 * @param string $items The HTML list content for the menu items.
	 * @param mixed  $args  An object containing wp_nav_menu() arguments.
	 *
	 * @return string
	 */
	function xts_mobile_menu_custom_links( $items = '', $args = array() ) {
		$is_nav_mobile = strstr( $args->menu_class, 'xts-nav-mobile' );

		if ( ! $is_nav_mobile || ! xts_is_woocommerce_installed() ) {
			return $items;
		}

		$compare_module = Modules::get( 'wc-compare' );

		$settings              = xts_get_header_settings();
		$is_wishlist_in_header = isset( $settings['wishlist'] );
		$is_compare_in_header  = isset( $settings['compare'] );
		$is_account_in_header  = isset( $settings['my-account'] );

		$account_with_username = $is_account_in_header && $settings['my-account']['with_username'];
		$is_account_login_side = $is_account_in_header && $settings['my-account']['login_form'];

		$current_user    = wp_get_current_user();
		$account_classes = '';

		if ( ! is_user_logged_in() && $is_account_login_side ) {
			$account_classes .= ' xts-opener';
		}

		if ( is_user_logged_in() ) {
			$account_classes .= ' menu-item-has-children';
			$account_text     = esc_html__( 'My Account', 'xts-theme' );

			if ( $account_with_username ) {
				/* translators: 1: User name */
				$account_text = sprintf( esc_html__( 'Hello, %s', 'xts-theme' ), '<strong>' . esc_html( $current_user->display_name ) . '</strong>' );
			}
		} else {
			$account_text = esc_html__( 'Login / Register', 'xts-theme' );
		}

		ob_start();
		?>

		<?php if ( $is_wishlist_in_header ) : ?>
			<li class="xts-menu-item-wishlist xts-menu-item-with-icon">
				<a href="<?php echo esc_url( xts_get_whishlist_page_url() ); ?>" class="xts-nav-link">
					<span class="xts-nav-text">
						<?php esc_html_e( 'Wishlist', 'xts-theme' ); ?>
					</span>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( $is_compare_in_header ) : ?>
			<li class="xts-menu-item-compare xts-menu-item-with-icon">
				<a href="<?php echo esc_url( $compare_module->get_compare_page_url() ); ?>" class="xts-nav-link">
					<span class="xts-nav-text">
						<?php esc_html_e( 'Compare', 'xts-theme' ); ?>
					</span>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( $is_account_in_header ) : ?>
			<li class="xts-menu-item-account xts-menu-item-with-icon<?php echo esc_attr( $account_classes ); ?>">
				<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="xts-nav-link">
					<span class="xts-nav-text">
						<?php echo esc_html( $account_text ); ?>
					</span>
				</a>

				<?php if ( is_user_logged_in() ) : ?>
					<ul class="sub-menu xts-sub-menu">
						<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
							<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
								<a class="xts-nav-link" href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
									<?php echo esc_html( $label ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</li>
		<?php endif; ?>

		<?php

		return $items . ob_get_clean();
	}

	add_filter( 'wp_nav_menu_items', 'xts_mobile_menu_custom_links', 10, 2 );
}

if ( ! function_exists( 'xts_wc_content_wrapper_start' ) ) {
	/**
	 * Output the start of the page wrapper.
	 *
	 * @since 1.0.0
	 */
	function xts_wc_content_wrapper_start() {
		$content_classes  = xts_get_content_classes();
		$content_classes .= ' xts-description-' . xts_get_opt( 'category_description_position' );

		if ( ! have_posts() ) {
			$content_classes .= ' xts-without-products';
		}

		if ( xts_get_opt( 'ajax_shop' ) && xts_is_shop_archive() ) {
			$content_classes .= ' xts-ajax-content';
		}

		?>
		<div class="xts-content-area<?php echo esc_attr( $content_classes ); ?>">
		<?php
	}

	add_action( 'woocommerce_before_main_content', 'xts_wc_content_wrapper_start', 10 );
}

if ( ! function_exists( 'xts_wc_content_wrapper_end' ) ) {
	/**
	 * Output the end of the page wrapper.
	 *
	 * @since 1.0.0
	 */
	function xts_wc_content_wrapper_end() {
		?>
		</div>
		<?php
	}

	add_action( 'woocommerce_after_main_content', 'xts_wc_content_wrapper_end', 10 );
}

if ( ! function_exists( 'xts_product_labels' ) ) {
	/**
	 * Display product labels
	 *
	 * @since 1.0.0
	 *
	 * @param array $custom_args Custom args.
	 */
	function xts_product_labels( $custom_args = array() ) {
		global $product;

		if ( 'small' === xts_get_loop_prop( 'product_design' ) || 'small-bg' === xts_get_loop_prop( 'product_design' ) ) {
			return;
		}

		$default_args = array(
			'shape' => xts_get_opt( 'product_label_shape' ),
		);

		$args = wp_parse_args( $custom_args, $default_args );

		$output = array();

		$product_attributes = xts_get_product_attributes_labels();
		$percentage_label   = xts_get_opt( 'product_label_percentage' );

		if ( $product->is_on_sale() ) {

			$percentage = '';

			if ( $product->is_type( 'variable' ) && $percentage_label ) {

				$available_variations = $product->get_variation_prices();
				$max_percentage       = 0;

				foreach ( $available_variations['regular_price'] as $key => $regular_price ) {
					$sale_price = $available_variations['sale_price'][ $key ];

					if ( $sale_price < $regular_price ) {
						$percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );

						if ( $percentage > $max_percentage ) {
							$max_percentage = $percentage;
						}
					}
				}

				$percentage = $max_percentage;
			} elseif ( ( $product->is_type( 'simple' ) || $product->is_type( 'external' ) ) && $percentage_label ) {
				$percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
			}

			if ( $percentage ) {
				$output[] = '<span class="xts-onsale xts-product-label">-' . esc_attr( $percentage ) . '%</span>';
			} else {
				$output[] = '<span class="xts-onsale xts-product-label">' . esc_html__( 'Sale', 'xts-theme' ) . '</span>';
			}
		}

		if ( ! $product->is_in_stock() ) {
			$output[] = '<span class="xts-out-of-stock xts-product-label">' . esc_html__( 'Sold', 'xts-theme' ) . '</span>';
		}

		if ( $product->is_featured() && xts_get_opt( 'product_label_hot' ) ) {
			$output[] = '<span class="xts-featured xts-product-label">' . esc_html__( 'Hot', 'xts-theme' ) . '</span>';
		}

		if ( get_post_meta( $product->get_id(), '_xts_product_label_new', true ) && xts_get_opt( 'product_label_new' ) ) {
			$output[] = '<span class="xts-new xts-product-label">' . esc_html__( 'New', 'xts-theme' ) . '</span>';
		}

		if ( $product_attributes ) {
			$output[] = $product_attributes;
		}

		if ( $output ) {
			?>
				<div class="xts-product-labels xts-shape-<?php echo esc_attr( $args['shape'] ); ?>">
					<?php echo implode( '', $output ); // phpcs:ignore ?>
				</div>
			<?php
		}
	}

	add_filter( 'woocommerce_sale_flash', 'xts_product_labels', 100 );
}

if ( ! function_exists( 'xts_get_product_attributes_labels' ) ) {
	/**
	 * Get product attributes labels
	 *
	 * @since 1.0.0
	 */
	function xts_get_product_attributes_labels() {
		global $product;
		$attributes = $product->get_attributes();

		ob_start();

		foreach ( $attributes as $attribute ) {
			if ( ! isset( $attribute['name'] ) ) {
				continue;
			}

			$show_attr_on_product = get_option( 'xts_' . $attribute['name'] . '_show_on_product' );

			if ( 'on' === $show_attr_on_product ) {
				$terms = wc_get_product_terms( $product->get_id(), $attribute['name'], array( 'fields' => 'all' ) );

				foreach ( $terms as $term ) {
					$classes   = '';
					$content   = $term->name;
					$image     = get_term_meta( $term->term_id, '_xts_attribute_image', true );
					$image_url = wp_get_attachment_image_url( $image['id'] );

					$classes .= ' xts-term-' . $term->slug;
					$classes .= ' xts-attribute-' . $attribute['name'];

					if ( $image_url ) {
						$classes .= ' xts-with-img';
						$content  = apply_filters( 'xts_image', '<img src="' . esc_url( $image_url ) . '" title="' . esc_attr( $term->slug ) . '" alt="' . esc_attr( $term->slug ) . '" />' );
					}

					?>
					<span class="xts-attribute-label xts-product-label<?php echo esc_attr( $classes ); ?>">
							<?php echo wp_kses( $content, 'xts_media' ); ?>
						</span>
					<?php

				}
			}
		}

		return ob_get_clean();
	}
}

if ( ! function_exists( 'xts_product_attributes_options' ) ) {
	/**
	 * Add product attribute labels options
	 *
	 * @since 1.0.0
	 */
	function xts_product_attributes_options() {
		?>
			<div class="xts-options xts-metaboxes">
				<div class="xts-fields-tabs">
					<div class="xts-sections">
						<div class="xts-fields-section xts-active-section" data-id="general">
							<div class="xts-section-content xts-row">
								<?php do_action( 'xts_product_attributes_labels_options' ); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	add_action( 'woocommerce_after_edit_attribute_fields', 'xts_product_attributes_options' );
	add_action( 'woocommerce_after_add_attribute_fields', 'xts_product_attributes_options' );
}

if ( ! function_exists( 'xts_product_attributes_labels_options' ) ) {
	/**
	 * Add product attribute labels options
	 *
	 * @since 1.0.0
	 */
	function xts_product_attributes_labels_options() {
		$show = '';

		if ( isset( $_GET['edit'] ) ) { // phpcs:ignore
			$attribute_id   = sanitize_text_field( wp_unslash( $_GET['edit'] ) ); // phpcs:ignore
			$taxonomy_ids   = wc_get_attribute_taxonomy_ids();
			$attribute_name = array_search( $attribute_id, $taxonomy_ids, false ); // phpcs:ignore
			$show           = get_option( 'xts_pa_' . $attribute_name . '_show_on_product' );
		}

		?>
			<div class="xts-field xts-col xts-buttons-control xts-label_color_scheme-field" data-id="label_color_scheme">
				<div class="xts-field-title">
					<span>
						<?php esc_html_e( 'Show attribute label on products', 'xts-theme' ); ?>
					</span>
				</div>

				<div class="xts-field-inner">
					<input <?php checked( $show, 'on' ); ?> name="attribute_show_on_product" id="attribute_show_on_product" type="checkbox">

					<p class="xts-description">
						<?php esc_html_e( 'Display special labels on products based on this attribute and its terms.', 'xts-theme' ); ?>
					</p>
				</div>
			</div>
		<?php
	}

	add_action( 'xts_product_attributes_labels_options', 'xts_product_attributes_labels_options', 20 );
}
