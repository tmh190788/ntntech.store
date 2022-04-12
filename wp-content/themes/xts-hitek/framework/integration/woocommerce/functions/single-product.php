<?php
/**
 * Woocommerce single product functions file
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\AJAX_Response;

if ( ! function_exists( 'xts_single_product_add_to_cart_scripts' ) ) {
	/**
	 * Enqueue single product scripts.
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_add_to_cart_scripts() {
		if ( xts_get_opt( 'single_product_ajax_add_to_cart' ) ) {
			xts_enqueue_js_script( 'single-product-ajax-add-to-cart' );
		}

		if ( 'no-action' !== xts_get_opt( 'action_after_add_to_cart' ) ) {
			xts_enqueue_js_library( 'magnific' );
			xts_enqueue_js_script( 'action-after-add-to-cart' );
		}
	}

	add_action( 'woocommerce_before_add_to_cart_form', 'xts_single_product_add_to_cart_scripts' );
}

if ( ! function_exists( 'xts_get_single_product_main_gallery_classes' ) ) {
	/**
	 * Get single product main gallery classes.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function xts_get_single_product_main_gallery_classes() {
		$size    = xts_get_single_product_main_gallery_size();
		$classes = 'col-lg-' . $size . ' col-md-6';

		if ( 6 === $size ) {
			$classes = 'col-md-6';
		}

		return $classes;
	}
}

if ( ! function_exists( 'xts_get_single_product_summary_classes' ) ) {
	/**
	 * Get single product summary classes.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function xts_get_single_product_summary_classes() {
		$size = xts_get_single_product_summary_size();

		$classes = 'col-lg-' . $size . ' col-md-6';

		if ( 6 === $size ) {
			$classes = 'col-md-6';
		}

		return $classes;
	}
}

if ( ! function_exists( 'xts_get_single_product_main_gallery_size' ) ) {
	/**
	 * Get single product main gallery size.
	 *
	 * @since 1.0.0
	 *
	 * @return integer
	 */
	function xts_get_single_product_main_gallery_size() {
		$summary_size = xts_get_single_product_summary_size();

		return 12 - $summary_size;
	}
}

if ( ! function_exists( 'xts_get_single_product_summary_size' ) ) {
	/**
	 * Get single product summary size.
	 *
	 * @since 1.0.0
	 *
	 * @return integer
	 */
	function xts_get_single_product_summary_size() {
		$page_layout = xts_get_opt( 'single_product_main_gallery_width' );
		$size        = 6;

		switch ( $page_layout ) {
			case 's':
				$size = 8;
				break;
			case 'm':
				$size = 6;
				break;
			case 'l':
				$size = 4;
				break;
		}

		return apply_filters( 'xts_single_product_summary_size', $size );
	}
}

if ( ! function_exists( 'xts_single_product_thumbnails_gallery_image_width' ) ) {
	/**
	 * Change default `gallery_thumbnail` size values
	 *
	 * @since 1.0.0
	 *
	 * @param array $size Default sizes.
	 *
	 * @return array
	 */
	function xts_single_product_thumbnails_gallery_image_width( $size ) {
		if ( xts_get_opt( 'single_product_thumbnails_gallery_image_width' ) ) {
			$size = array(
				'width'  => (int) xts_get_opt( 'single_product_thumbnails_gallery_image_width' ),
				'height' => 0,
				'crop'   => 0,
			);
		}

		return $size;
	}

	add_filter( 'woocommerce_get_image_size_gallery_thumbnail', 'xts_single_product_thumbnails_gallery_image_width', 10 );
}

if ( ! function_exists( 'xts_single_product_attributes_table' ) ) {
	/**
	 * Show single product attributes table
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_attributes_table() {
		global $product;
		if ( $product && ( $product->has_attributes() || apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() ) ) ) {
			wc_display_product_attributes( $product );
		}
	}
}

if ( ! function_exists( 'xts_single_product_remove_additional_information_tab' ) ) {
	/**
	 * Remove additional information tab
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array
	 */
	function xts_single_product_remove_additional_information_tab( $tabs ) {
		unset( $tabs['additional_information'] );
		return $tabs;
	}
}

if ( ! function_exists( 'xts_single_product_remove_reviews_tab' ) ) {
	/**
	 * Remove reviews tab
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array
	 */
	function xts_single_product_remove_reviews_tab( $tabs ) {
		unset( $tabs['reviews'] );
		return $tabs;
	}
}

if ( ! function_exists( 'xts_single_product_remove_description_tab' ) ) {
	/**
	 * Remove description tab
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array
	 */
	function xts_single_product_remove_description_tab( $tabs ) {
		unset( $tabs['description'] );
		return $tabs;
	}
}

if ( ! function_exists( 'xts_single_product_photoswipe_btn' ) ) {
	/**
	 * Add zoom icon to single product main gallery
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_photoswipe_btn() {
		if ( xts_get_loop_prop( 'is_quick_view' ) || ! xts_get_opt( 'single_product_main_gallery_photoswipe_btn' ) || 'photoswipe' === xts_get_opt( 'single_product_main_gallery_click_action' ) ) {
			return;
		}

		xts_enqueue_js_library( 'photoswipe-bundle' );
		xts_enqueue_js_script( 'single-product-gallery-photoswipe' );

		?>
		<div class="xts-photoswipe-btn xts-action-btn xts-style-icon-bg-text">
			<a href="#">
				<span>
					<?php esc_html_e( 'Click to enlarge', 'xts-theme' ); ?>
				</span>
			</a>
		</div>
		<?php
	}

	add_action( 'xts_single_product_main_gallery_action_buttons', 'xts_single_product_photoswipe_btn', 10 );
}

if ( ! function_exists( 'xts_single_product_video_btn' ) ) {
	/**
	 * Add video icon to single product main gallery
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_video_btn() {
		$video_url = get_post_meta( get_the_ID(), '_xts_single_product_video_url', true );

		if ( ! $video_url || xts_get_loop_prop( 'is_quick_view' ) ) {
			return;
		}

		xts_enqueue_js_library( 'magnific' );
		xts_enqueue_js_script( 'video-element-popup' );

		?>
			<div class="xts-video-btn xts-action-btn xts-style-icon-bg-text">
				<a href="<?php echo esc_url( $video_url ); ?>" class="xts-video-btn-link">
					<span>
						<?php esc_html_e( 'Watch video', 'xts-theme' ); ?>
					</span>
				</a>
			</div>
		<?php
	}

	add_action( 'xts_single_product_main_gallery_action_buttons', 'xts_single_product_video_btn', 20 );
}

if ( ! function_exists( 'xts_single_product_360_view_btn' ) ) {
	/**
	 * Add 360 view icon to single product main gallery
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_360_view_btn() {
		$raw_images = get_post_meta( get_the_ID(), '_xts_single_product_360_view', true );

		if ( ! $raw_images || xts_get_loop_prop( 'is_quick_view' ) ) {
			return;
		}

		$raw_images = explode( ',', $raw_images );
		$images     = array();

		foreach ( $raw_images as $image ) {
			$images[]['id'] = $image;
		}

		xts_enqueue_js_library( 'magnific' );
		xts_enqueue_js_script( 'popup' );

		?>
			<div class="xts-360-view-btn xts-action-btn xts-style-icon-bg-text">
				<a href="#xts-single-product-360-view" class="xts-popup-opener">
					<span>
						<?php esc_html_e( '360 product view', 'xts-theme' ); ?>
					</span>
				</a>
			</div>

			<div class="xts-popup-content xts-360-popup mfp-with-anim mfp-hide" id="xts-single-product-360-view">
				<?php
				xts_360_view_template(
					array(
						'images' => $images,
					)
				);
				?>
			</div>
		<?php
	}

	add_action( 'xts_single_product_main_gallery_action_buttons', 'xts_single_product_360_view_btn', 30 );
}

if ( ! function_exists( 'xts_grouped_product_columns' ) ) {
	/**
	 * Add thumbnail column to grouped product
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_grouped_product_columns() {
		return array(
			'thumbnail',
			'label',
			'quantity',
			'price',
		);
	}

	add_filter( 'woocommerce_grouped_product_columns', 'xts_grouped_product_columns', 100 );
}

if ( ! function_exists( 'xts_grouped_product_thumbnail_column' ) ) {
	/**
	 * Add grouped product thumbnail to column
	 *
	 * @since 1.0.0
	 *
	 * @param string $value                 Html.
	 * @param object $grouped_product_child Child product.
	 *
	 * @return string
	 */
	function xts_grouped_product_thumbnail_column( $value, $grouped_product_child ) {
		$attachment_id = get_post_meta( $grouped_product_child->get_id(), '_thumbnail_id', true );

		return wp_get_attachment_image( $attachment_id, 'woocommerce_thumbnail' );
	}

	add_filter( 'woocommerce_grouped_product_list_column_thumbnail', 'xts_grouped_product_thumbnail_column', 100, 2 );
}

if ( ! function_exists( 'xts_single_product_ajax_add_to_cart' ) ) {
	/**
	 * AJAX add to cart for all product types
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_ajax_add_to_cart() {
		ob_start();

		wc_print_notices();

		$notices = ob_get_clean();

		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		$data = array(
			'notices'   => $notices,
			'fragments' => apply_filters(
				'woocommerce_add_to_cart_fragments',
				array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( wp_json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() ),
		);

		AJAX_Response::send_response( $data, true );
	}

	add_action( 'wp_ajax_xts_single_product_ajax_add_to_cart', 'xts_single_product_ajax_add_to_cart' );
	add_action( 'wp_ajax_nopriv_xts_single_product_ajax_add_to_cart', 'xts_single_product_ajax_add_to_cart' );
}
