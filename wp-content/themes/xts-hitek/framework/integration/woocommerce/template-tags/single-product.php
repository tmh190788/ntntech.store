<?php
/**
 * Woocommerce single product template file
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_variations_clear_button' ) ) {
	/**
	 * Single product clear button
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_variations_clear_button() {
		?>
			<div class="xts-reset-variations">
				<a class="reset_variations" href="#">
					<?php esc_html_e( 'Clear', 'woocommerce' ); ?>
				</a>
			</div>
		<?php
	}

	add_action( 'woocommerce_before_single_variation', 'xts_single_product_variations_clear_button', 10 );
	add_filter( 'woocommerce_reset_variations_link', '__return_false', 10 );
}

if ( ! function_exists( 'xts_single_product_action_buttons_wrapper_start' ) ) {
	/**
	 * Single product action buttons start
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_action_buttons_wrapper_start() {
		?>
			<div class="xts-single-product-actions">
		<?php
	}

	add_action( 'woocommerce_single_product_summary', 'xts_single_product_action_buttons_wrapper_start', 32 );
}

if ( ! function_exists( 'xts_single_product_action_buttons_wrapper_end' ) ) {
	/**
	 * Single product action buttons end
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_action_buttons_wrapper_end() {
		?>
			</div>
		<?php
	}

	add_action( 'woocommerce_single_product_summary', 'xts_single_product_action_buttons_wrapper_end', 36 );
}

if ( ! function_exists( 'xts_single_product_share_buttons' ) ) {
	/**
	 *  Single product share buttons
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_share_buttons() {
		if ( ! xts_get_opt( 'single_product_share_buttons' ) ) {
			return;
		}

		$type = xts_get_opt( 'single_product_share_buttons_type' );
		$text = 'share' === $type ? esc_html__( 'Share:', 'xts-theme' ) : esc_html__( 'Follow:', 'xts-theme' );

		?>
		<?php if ( xts_is_social_buttons_enable( $type ) ) : ?>
			<div class="xts-single-product-share">
				<?php
				xts_social_buttons_template(
					array(
						'type'            => $type,
						'label_text'      => $text,
						'size'            => 's',
						'align'           => 'left',
						'label_text_size' => 'inherit',
					)
				);
				?>
			</div>
		<?php endif ?>
		<?php
	}

	add_action( 'woocommerce_single_product_summary', 'xts_single_product_share_buttons', 45 );
}

if ( ! function_exists( 'xts_quantity_input_minus' ) ) {
	/**
	 *  Quantity input minus
	 *
	 * @since 1.0.0
	 */
	function xts_quantity_input_minus() {
		xts_enqueue_js_script( 'single-product-quantity' );
		?>
			<button type="button" value="-" class="xts-minus"></button>
		<?php
	}
	add_action( 'woocommerce_before_quantity_input_field', 'xts_quantity_input_minus' );
}

if ( ! function_exists( 'xts_quantity_input_plus' ) ) {
	/**
	 *  Quantity input plus
	 *
	 * @since 1.0.0
	 */
	function xts_quantity_input_plus() {
		xts_enqueue_js_script( 'single-product-quantity' );
		?>
			<button type="button" value="+" class="xts-plus"></button>
		<?php
	}

	add_action( 'woocommerce_after_quantity_input_field', 'xts_quantity_input_plus' );
}

if ( ! function_exists( 'xts_single_product_sticky_add_to_cart' ) ) {
	/**
	 * Single product sticky add to cart
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_sticky_add_to_cart() {
		global $product;

		if ( ! xts_is_woocommerce_installed() || ( ! is_singular( 'product' ) && ! is_singular( 'xts-template' ) ) || ! xts_get_opt( 'single_product_sticky_add_to_cart' ) || ! $product ) {
			return;
		}

		$wrapper_classes = '';

		if ( xts_get_opt( 'single_product_mobile_sticky_add_to_cart' ) ) {
			$wrapper_classes .= ' xts-mb-show';
		}

		xts_enqueue_js_script( 'single-product-sticky-add-to-cart' );

		?>
			<div class="xts-sticky-atc<?php echo esc_attr( $wrapper_classes ); ?>">
				<div class="container">
					<div class="row">
						<div class="col">
							<div class="xts-sticky-atc-thumb">
								<?php woocommerce_template_loop_product_thumbnail(); ?>
							</div>
							<div class="xts-sticky-atc-desc">
								<h4 class="xts-entities-title">
									<?php the_title(); ?>
								</h4>

								<?php echo wc_get_rating_html( $product->get_average_rating() ); // phpcs:ignore ?>
							</div>
						</div>

						<div class="col-auto">
							<span class="price">
								<?php echo apply_filters( 'woocommerce_xts_get_price_html', $product->get_price_html() ); // phpcs:ignore XSS ok. ?>
							</span>

							<?php if ( $product->is_type( 'simple' ) ) : ?>
								<?php woocommerce_simple_add_to_cart(); ?>
							<?php else : ?>
								<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="xts-sticky-atc-btn single_add_to_cart_button button">
									<?php if ( $product->is_type( 'variable' ) ) : ?>
										<?php esc_html_e( 'Select options', 'xts-theme' ); ?>
									<?php else : ?>
										<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
									<?php endif; ?>
								</a>
							<?php endif; ?>

							<?php xts_add_compare_button( 'xts-style-icon' ); ?>
							<?php xts_add_wishlist_button( 'xts-style-icon' ); ?>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	add_action( 'xts_after_site_wrapper', 'xts_single_product_sticky_add_to_cart', 80 );
}

if ( ! function_exists( 'xts_before_add_to_cart_content' ) ) {
	/**
	 *  Before add to cart content
	 *
	 * @since 1.0.0
	 */
	function xts_before_add_to_cart_content() {
		if ( 'html_block' === xts_get_opt( 'single_product_before_add_to_cart_content_type' ) ) {
			?>
			<div class="xts-before-add-to-cart">
				<?php echo xts_get_html_block_content( xts_get_opt( 'single_product_before_add_to_cart_html_block' ) ); // phpcs:ignore ?>
			</div>
			<?php
		} else {
			?>
			<div class="xts-before-add-to-cart">
				<?php echo xts_get_opt( 'single_product_before_add_to_cart_text' ); // phpcs:ignore ?>
			</div>
			<?php
		}
	}

	add_action( 'woocommerce_single_product_summary', 'xts_before_add_to_cart_content', 25 );
}

if ( ! function_exists( 'xts_after_add_to_cart_content' ) ) {
	/**
	 *  After add to cart content
	 *
	 * @since 1.0.0
	 */
	function xts_after_add_to_cart_content() {
		if ( 'html_block' === xts_get_opt( 'single_product_after_add_to_cart_content_type' ) ) {
			?>
			<div class="xts-after-add-to-cart">
				<?php echo xts_get_html_block_content( xts_get_opt( 'single_product_after_add_to_cart_html_block' ) ); // phpcs:ignore ?>
			</div>
			<?php
		} else {
			?>
			<div class="xts-after-add-to-cart">
				<?php echo xts_get_opt( 'single_product_after_add_to_cart_text' ); // phpcs:ignore ?>
			</div>
			<?php
		}
	}

	add_action( 'woocommerce_single_product_summary', 'xts_after_add_to_cart_content', 31 );
}

if ( ! function_exists( 'xts_single_product_additional_product_tabs' ) ) {
	/**
	 * Add additional tab to single product tabs.
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Product tabs.
	 *
	 * @return mixed
	 */
	function xts_single_product_additional_product_tabs( $tabs ) {
		$additional_tab_title        = xts_get_opt( 'single_product_additional_tab_title' );
		$additional_tab_title_2      = xts_get_opt( 'single_product_additional_tab_title_2' );
		$additional_tab_title_3      = xts_get_opt( 'single_product_additional_tab_title_3' );
		$custom_additional_tab_title = get_post_meta( get_the_ID(), '_xts_single_product_custom_additional_tab_title', true );

		if ( $additional_tab_title ) {
			$tabs['xts_additional_tab'] = array(
				'title'    => $additional_tab_title,
				'priority' => 50,
				'callback' => 'xts_single_product_additional_tab_content',
			);
		}

		if ( $additional_tab_title_2 ) {
			$tabs['xts_additional_tab_2'] = array(
				'title'    => $additional_tab_title_2,
				'priority' => 60,
				'callback' => 'xts_single_product_additional_tab_content_2',
			);
		}

		if ( $additional_tab_title_3 ) {
			$tabs['xts_additional_tab_3'] = array(
				'title'    => $additional_tab_title_3,
				'priority' => 70,
				'callback' => 'xts_single_product_additional_tab_content_3',
			);
		}

		if ( $custom_additional_tab_title ) {
			$tabs['xts_custom_additional_tab'] = array(
				'title'    => $custom_additional_tab_title,
				'priority' => 80,
				'callback' => 'xts_single_product_custom_additional_tab_content',
			);
		}

		return $tabs;
	}

	add_filter( 'woocommerce_product_tabs', 'xts_single_product_additional_product_tabs' );
}

if ( ! function_exists( 'xts_single_product_additional_tab_content' ) ) {
	/**
	 *  Additional tab content callback.
	 */
	function xts_single_product_additional_tab_content() {
		if ( 'html_block' === xts_get_opt( 'single_product_additional_tab_content_type' ) ) {
			echo xts_get_html_block_content( xts_get_opt( 'single_product_additional_tab_html_block' ) ); // phpcs:ignore
		} else {
			echo xts_get_opt( 'single_product_additional_tab_text' ); // phpcs:ignore
		}
	}
}

if ( ! function_exists( 'xts_single_product_additional_tab_content_2' ) ) {
	/**
	 *  Additional tab content callback.
	 */
	function xts_single_product_additional_tab_content_2() {
		if ( 'html_block' === xts_get_opt( 'single_product_additional_tab_content_type_2' ) ) {
			echo xts_get_html_block_content( xts_get_opt( 'single_product_additional_tab_html_block_2' ) ); // phpcs:ignore
		} else {
			echo xts_get_opt( 'single_product_additional_tab_text_2' ); // phpcs:ignore
		}
	}
}

if ( ! function_exists( 'xts_single_product_additional_tab_content_3' ) ) {
	/**
	 *  Additional tab content callback.
	 */
	function xts_single_product_additional_tab_content_3() {
		if ( 'html_block' === xts_get_opt( 'single_product_additional_tab_content_type_3' ) ) {
			echo xts_get_html_block_content( xts_get_opt( 'single_product_additional_tab_html_block_3' ) ); // phpcs:ignore
		} else {
			echo xts_get_opt( 'single_product_additional_tab_text_3' ); // phpcs:ignore
		}
	}
}

if ( ! function_exists( 'xts_single_product_custom_additional_tab_content' ) ) {
	/**
	 *  Custom additional tab content callback
	 */
	function xts_single_product_custom_additional_tab_content() {
		if ( 'html_block' === get_post_meta( get_the_ID(), '_xts_single_product_custom_additional_tab_content_type', true ) ) {
			echo xts_get_html_block_content( get_post_meta( get_the_ID(), '_xts_single_product_custom_additional_tab_html_block', true ) ); // phpcs:ignore
		} else {
			echo get_post_meta( get_the_ID(), '_xts_single_product_custom_additional_tab_text', true ); // phpcs:ignore
		}
	}
}

if ( ! function_exists( 'xts_wc_get_gallery_html' ) ) {
	/**
	 * Get HTML for a gallery.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $gallery_image_ids Gallery ids.
	 * @param boolean $main_image        Is this the main image or a thumbnail?.
	 *
	 * @return string
	 */
	function xts_wc_get_gallery_html( $gallery_image_ids, $main_image = false ) {
		$gallery_output = '';

		foreach ( $gallery_image_ids as $gallery_image_id ) {
			$gallery_output .= xts_wc_get_gallery_image_html( $gallery_image_id, $main_image );
		}

		return $gallery_output;
	}
}

if ( ! function_exists( 'xts_wc_get_gallery_image_html' ) ) {
	/**
	 * Get HTML for a gallery image.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $attachment_id Attachment ID.
	 * @param bool $main_image    Is this the main image or a thumbnail?.
	 *
	 * @return string
	 */
	function xts_wc_get_gallery_image_html( $attachment_id, $main_image = false ) {
		$flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$thumbnail_size    = apply_filters(
			'woocommerce_gallery_thumbnail_size',
			array(
				$gallery_thumbnail['width'],
				$gallery_thumbnail['height'],
			)
		);
		$image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
		$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
		$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
		$alt_text          = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

		if ( $main_image ) {
			$image = wp_get_attachment_image(
				$attachment_id,
				$image_size,
				false,
				apply_filters(
					'woocommerce_gallery_image_html_attachment_image_params',
					array(
						'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
						'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
						'data-src'                => esc_url( $full_src[0] ),
						'data-large_image'        => esc_url( $full_src[0] ),
						'data-large_image_width'  => esc_attr( $full_src[1] ),
						'data-large_image_height' => esc_attr( $full_src[2] ),
						'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
					),
					$attachment_id,
					$image_size,
					$main_image
				)
			);
		} else {
			$image = wp_get_attachment_image( $attachment_id, $image_size );
		}

		ob_start();

		?>
			<div class="xts-col" data-thumb="<?php echo esc_attr( $thumbnail_src[0] ); ?>" data-thumb-alt="<?php echo esc_attr( $alt_text ); ?>">
				<?php if ( $main_image ) : ?>
					<div class="xts-col-inner">
						<a href="<?php echo esc_url( $full_src[0] ); ?>" data-elementor-open-lightbox="no">
							<?php endif; ?>

							<?php echo wp_kses( $image, 'xts_media' ); ?>

							<?php if ( $main_image ) : ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'xts_single_product_breadcrumbs' ) ) {
	/**
	 * Single product page breadcrumbs
	 *
	 * @since 1.0.0
	 */
	function xts_single_product_breadcrumbs() {
		?>
			<div class="xts-single-product-navs col-md-12">
				<div class="row">
					<div class="col"><?php xts_current_shop_breadcrumbs(); ?></div>
					<div class="col-auto">
						<?php if ( xts_get_opt( 'single_product_nav' ) ) : ?>
							<?php xts_get_template_part( 'woocommerce/single-product-navigation' ); ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php
	}

	add_action( 'woocommerce_before_single_product_summary', 'xts_single_product_breadcrumbs', 10 );
}
