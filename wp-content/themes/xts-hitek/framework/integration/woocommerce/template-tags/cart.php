<?php
/**
 * Woocommerce cart template functions file
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_wc_empty_cart_message' ) ) {
	/**
	 * Show notice if cart is empty.
	 *
	 * @since 1.0.0
	 */
	function xts_wc_empty_cart_message() {
		?>
		<h4 class="cart-empty xts-empty-page">
			<?php echo apply_filters( 'wc_empty_cart_message', esc_html__( 'Your cart is currently empty.', 'woocommerce' ) ); ?>
		</h4>
		<?php
	}

	add_action( 'woocommerce_cart_is_empty', 'xts_wc_empty_cart_message', 10 );
}

if ( ! function_exists( 'xts_wc_empty_cart_text' ) ) {
	/**
	 * My account wrapper start
	 *
	 * @since 1.0.0
	 */
	function xts_wc_empty_cart_text() {
		?>
			<p class="xts-empty-page-text">
				<?php echo wp_kses( xts_get_opt( 'empty_cart_text' ), xts_get_allowed_html() ); ?>
			</p>
		<?php
	}

	add_action( 'woocommerce_cart_is_empty', 'xts_wc_empty_cart_text', 20 );
}

if ( ! function_exists( 'woocommerce_widget_shopping_cart_button_view_cart' ) ) {
	/**
	 * Output the view cart button.
	 *
	 * @since 1.0.0
	 */
	function woocommerce_widget_shopping_cart_button_view_cart() {
		?>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="xts-mini-cart-btn button wc-forward">
				<?php esc_html_e( 'View cart', 'woocommerce' ); ?>
			</a>
		<?php
	}
}

if ( ! function_exists( 'xts_wc_cart_count' ) ) {
	/**
	 * Display cart count
	 *
	 * @since 1.0.0
	 */
	function xts_wc_cart_count() {
		$count = WC()->cart->get_cart_contents_count();

		?>
			<span class="xts-cart-count">
				<?php echo esc_html( $count ); ?>

				<span>
					<?php echo esc_html( _n( 'item', 'items', $count, 'xts-theme' ) ); ?>
				</span>
			</span>
		<?php
	}
}

if ( ! function_exists( 'xts_wc_cart_subtotal' ) ) {
	/**
	 * Display cart subtotal
	 *
	 * @since 1.0.0
	 */
	function xts_wc_cart_subtotal() {
		?>
			<span class="xts-cart-subtotal">
				<?php wc_cart_totals_subtotal_html(); // phpcs:ignore ?>
			</span>
		<?php
	}
}

if ( ! function_exists( 'xts_wc_cart_widget_template' ) ) {
	/**
	 * Display cart widget side
	 *
	 * @since 1.0.0
	 */
	function xts_wc_cart_widget_template() {
		$wrapper_classes = '';
		$settings        = xts_get_header_settings();
		$position        = isset( $settings['cart']['position'] ) ? $settings['cart']['position'] : '';
		$color_scheme    = isset( $settings['cart']['color_scheme'] ) ? $settings['cart']['color_scheme'] : '';

		if ( ! xts_is_woocommerce_installed() || ! isset( $settings['cart'] ) || ( isset( $settings['cart'] ) && isset( $settings['cart']['widget_type'] ) && 'side' !== $settings['cart']['widget_type'] && 'top' !== $settings['cart']['widget_type'] ) ) {
			return;
		}

		if ( 'top' === $settings['cart']['widget_type'] ) {
			$position = 'top';
		}

		$wrapper_classes .= ' xts-side-' . $position;
		if ( 'dark' !== $color_scheme && $color_scheme ) {
			$wrapper_classes .= ' xts-scheme-' . $color_scheme;
		}

		xts_enqueue_js_script( 'offcanvas-cart-widget' );

		if ( 'top' === $position ) {
			xts_wc_cart_top_template( $wrapper_classes );
		} else {
			xts_wc_cart_side_template( $wrapper_classes );
		}
	}

	add_action( 'xts_after_site_wrapper', 'xts_wc_cart_widget_template', 60 );
}

if ( ! function_exists( 'xts_wc_cart_side_template' ) ) {
	/**
	 * Default template cart side.
	 *
	 * @param string $wrapper_classes Classes.
	 */
	function xts_wc_cart_side_template( $wrapper_classes ) {
		?>
		<div class="xts-cart-widget-side xts-side-hidden xts-scroll<?php echo esc_attr( $wrapper_classes ); ?>">
			<div class="xts-heading-with-btn">
				<span class="title xts-fontsize-m">
					<?php esc_html_e( 'Shopping cart', 'xts-theme' ); ?>
				</span>

				<div class="xts-close-button xts-action-btn xts-style-inline">
					<a href="#"><?php esc_html_e( 'Close', 'xts-theme' ); ?></a>
				</div>
			</div>

			<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'xts_wc_cart_top_template' ) ) {
	/**
	 * Top template cart side.
	 *
	 * @param string $wrapper_classes Classes.
	 */
	function xts_wc_cart_top_template( $wrapper_classes ) {
		?>
		<div class="xts-cart-widget-side xts-side-hidden xts-scroll<?php echo esc_attr( $wrapper_classes ); ?>">
			<div class="xts-close-button xts-action-btn xts-cross-btn xts-style-icon">
				<a href="#"></a>
			</div>
			<div class="container">
				<div class="xts-heading-with-btn">
					<span class="title xts-fontsize-m">
						<?php esc_html_e( 'Shopping cart', 'xts-theme' ); ?>
					</span>
				</div>
				<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
			</div>
		</div>
		<?php
	}
}
