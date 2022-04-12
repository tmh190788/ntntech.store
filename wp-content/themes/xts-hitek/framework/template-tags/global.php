<?php
/**
 * Global templates functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_previous_comments_link_classes' ) ) {
	/**
	 * Previous comments link classes
	 *
	 * @since 1.0.0
	 *
	 * @param string $attrs Link attributes.
	 *
	 * @return string
	 */
	function xts_previous_comments_link_classes( $attrs ) {
		$attrs .= ' class="xts-prev"';

		return $attrs;
	}

	add_filter( 'previous_comments_link_attributes', 'xts_previous_comments_link_classes', 10 );
}

if ( ! function_exists( 'xts_next_comments_link_classes' ) ) {
	/**
	 * Next comments link classes
	 *
	 * @since 1.0.0
	 *
	 * @param string $attrs Link attributes.
	 *
	 * @return string
	 */
	function xts_next_comments_link_classes( $attrs ) {
		$attrs .= ' class="xts-next"';

		return $attrs;
	}

	add_filter( 'next_comments_link_attributes', 'xts_next_comments_link_classes', 10 );
}

if ( ! function_exists( 'xts_cookies_template' ) ) {
	/**
	 * Cookies law template
	 *
	 * @since 1.0.0
	 */
	function xts_cookies_template() {
		if ( ! xts_get_opt( 'cookies' ) ) {
			return;
		}

		$page_id = xts_get_opt( 'cookies_policy_page' );
		$title   = xts_get_opt( 'cookies_title' );

		xts_enqueue_js_script( 'cookies-popup' );

		?>
		<div class="xts-cookies">
			<?php if ( $title ) : ?>
				<h5 class="xts-cookies-title">
					<?php echo esc_html( $title ); ?>
				</h5>
			<?php endif; ?>

			<div class="xts-cookies-content">
				<?php echo do_shortcode( xts_get_opt( 'cookies_content' ) ); ?>
			</div>
			<a href="#" class="xts-cookies-accept-btn button xts-color-primary">
				<?php esc_html_e( 'Accept', 'xts-theme' ); ?>
			</a>

			<?php if ( $page_id ) : ?>
				<a href="<?php echo esc_url( get_permalink( $page_id ) ); ?>" class="xts-button xts-style-link xts-cookies-more-btn">
					<?php esc_html_e( 'More info', 'xts-theme' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}

	add_action( 'xts_after_site_wrapper', 'xts_cookies_template', 300 );
}

if ( ! function_exists( 'xts_video_template' ) ) {
	/**
	 * Video template
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Video template settings.
	 */
	function xts_video_template( $config = array() ) {
		?>
		<video class="<?php echo esc_attr( $config['classes'] ); ?>" <?php echo esc_attr( implode( ' ', $config['attrs'] ) ); ?>>
			<?php if ( isset( $config['video_mp4']['id'] ) && $config['video_mp4']['id'] ) : ?>
				<source src="<?php echo esc_url( wp_get_attachment_url( $config['video_mp4']['id'] ) ); ?>" type="video/mp4">
			<?php endif; ?>

			<?php if ( isset( $config['video_webm']['id'] ) && $config['video_webm']['id'] ) : ?>
				<source src="<?php echo esc_url( wp_get_attachment_url( $config['video_webm']['id'] ) ); ?>" type="video/webm">
			<?php endif; ?>

			<?php if ( isset( $config['video_ogg']['id'] ) && $config['video_ogg']['id'] ) : ?>
				<source src="<?php echo esc_url( wp_get_attachment_url( $config['video_ogg']['id'] ) ); ?>" type="video/ogg">
			<?php endif; ?>
		</video>
		<?php
	}
}

if ( ! function_exists( 'xts_promo_popup_template' ) ) {
	/**
	 * Promo popup template
	 *
	 * @since 1.0.0
	 */
	function xts_promo_popup_template() {
		if ( ! xts_get_opt( 'promo_popup' ) || is_singular( 'xts-html-block' ) || is_singular( 'xts-slide' ) || xts_is_maintenance_page() ) {
			return;
		}

		xts_enqueue_js_library( 'magnific' );
		xts_enqueue_js_script( 'promo-popup' );

		$inner_classes = '';

		if ( 'inherit' !== xts_get_opt( 'promo_popup_color_scheme' ) ) {
			$inner_classes .= ' xts-scheme-' . xts_get_opt( 'promo_popup_color_scheme' );
		}

		?>
		<div class="xts-promo-popup mfp-with-anim xts-popup-content">
			<div class="xts-popup-inner<?php echo esc_attr( $inner_classes ); ?>">
				<?php
				if ( 'html_block' === xts_get_opt( 'promo_popup_content_type' ) ) {
					echo xts_get_html_block_content( xts_get_opt( 'promo_popup_html_block' ) ); // phpcs:ignore
				} else {
					echo xts_get_opt( 'promo_popup_text' ); // phpcs:ignore
				}
				?>
			</div>
		</div>
		<?php
	}

	add_action( 'xts_after_site_wrapper', 'xts_promo_popup_template', 10 );
}

if ( ! function_exists( 'xts_close_side_div' ) ) {
	/**
	 * Print close side div
	 *
	 * @since 1.0.0
	 */
	function xts_close_side_div() {
		?>
		<div class="xts-close-side xts-fill"></div>
		<?php
	}

	add_action( 'xts_after_site_wrapper', 'xts_close_side_div', 30 );
}

if ( ! function_exists( 'xts_scroll_top_button' ) ) {
	/**
	 * Add scroll to top button
	 *
	 * @since 1.0.0
	 */
	function xts_scroll_top_button() {
		if ( ! xts_get_opt( 'scroll_to_top' ) ) {
			return;
		}

		xts_enqueue_js_script( 'scroll-to-top' );

		xts_get_template_part( 'templates/scroll-to-top' );
	}

	add_action( 'xts_after_site_wrapper', 'xts_scroll_top_button', 40 );
}

if ( ! function_exists( 'xts_offcanvas_sidebar_button' ) ) {
	/**
	 * Off canvas sidebar opener
	 *
	 * @since 1.0.0
	 */
	function xts_offcanvas_sidebar_button() {
		$sidebar_class        = xts_get_sidebar_classes();
		$sticky_navbar_fields = xts_get_opt( 'sticky_bottom_navbar_fields' );
		$sticky_navbar        = xts_get_opt( 'sticky_bottom_navbar' );
		$show                 = false;

		if ( strstr( $sidebar_class, 'col-lg-0' ) || xts_is_maintenance_page() ) { // phpcs:ignore
			return;
		}

		if ( ( is_singular( 'post' ) || xts_is_blog_archive() ) && ( xts_get_opt( 'blog_offcanvas_sidebar_desktop' ) || ( xts_get_opt( 'blog_offcanvas_sidebar_mobile' ) && ( ( $sticky_navbar && ! in_array( 'sidebar', $sticky_navbar_fields ) ) || ! $sticky_navbar ) ) ) ) { // phpcs:ignore
			$show = true;
		} elseif ( ( xts_is_woocommerce_installed() && xts_is_shop_archive() ) && ( ( xts_get_opt( 'shop_offcanvas_sidebar_desktop' ) || xts_get_opt( 'shop_offcanvas_sidebar_mobile' ) ) && ( ( $sticky_navbar && ! in_array( 'sidebar', $sticky_navbar_fields ) ) || ! $sticky_navbar ) ) ) { // phpcs:ignore
			$show = true;
		} elseif ( is_singular( 'product' ) && ( xts_get_opt( 'single_product_offcanvas_sidebar_desktop' ) || ( xts_get_opt( 'single_product_offcanvas_sidebar_mobile' ) && ( ( $sticky_navbar && ! in_array( 'sidebar', $sticky_navbar_fields ) ) || ! $sticky_navbar ) ) ) ) { // phpcs:ignore
			$show = true;
		} elseif ( xts_get_opt( 'offcanvas_sidebar_desktop' ) || ( xts_get_opt( 'offcanvas_sidebar_mobile' ) && ( ( $sticky_navbar && ! in_array( 'sidebar', $sticky_navbar_fields ) ) || ! $sticky_navbar ) ) ) { // phpcs:ignore
			$show = true;
		}

		if ( ! $show ) {
			return;
		}

		xts_enqueue_js_script( 'offcanvas-sidebar' );

		?>
		<div class="xts-sidebar-opener xts-action-btn xts-style-icon-bg-text">
			<a href="#">
				<span>
					<?php esc_html_e( 'Open sidebar', 'xts-theme' ); ?>
				</span>
			</a>
		</div>
		<?php
	}

	add_action( 'xts_after_sidebar', 'xts_offcanvas_sidebar_button', 50 );
}
