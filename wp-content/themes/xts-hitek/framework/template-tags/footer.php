<?php
/**
 * Footer templates functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_page_bottom_part' ) ) {
	/**
	 * Generate page bottom part
	 *
	 * @since 1.0.0
	 */
	function xts_page_bottom_part() {
		?>	
						</div> <!-- .row -->

					</div> <!-- .container -->

				<?php do_action( 'xts_after_site_content_container' ); ?>

			<?php if ( ! xts_is_ajax() ) : ?>
				</div> <!-- .xts-site-content -->
			<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'xts_footer' ) ) {
	/**
	 * Generate footer
	 *
	 * @since 1.0.0
	 */
	function xts_footer() {
		$page_id         = xts_get_page_id();
		$wrapper_classes = '';

		$metabox_disable_footer = get_post_meta( $page_id, '_xts_footer', true );

		$footer_config       = xts_get_footer_grid();
		$footer_layout       = xts_get_opt( 'footer_layout' );
		$color_scheme        = xts_get_opt( 'footer_color_scheme' );
		$footer_html_block   = xts_get_opt( 'footer_html_block' );
		$footer_content_type = xts_get_opt( 'footer_content_type' );

		if ( xts_get_opt( 'footer_widgets_collapse' ) ) {
			xts_enqueue_js_script( 'widget-collapse' );
		}

		$footer_needed = $metabox_disable_footer ? false : xts_get_opt( 'footer' );

		if ( 'inherit' !== $color_scheme ) {
			$wrapper_classes .= ' xts-scheme-' . $color_scheme;
		}
		if ( 'light' === $color_scheme ) {
			$wrapper_classes .= ' xts-widget-scheme-light';
		}
		$wrapper_classes .= ' xts-with-' . $footer_content_type;

		?>
		<footer class="xts-footer<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php if ( isset( $footer_config[ $footer_layout ] ) && $footer_needed && ( ! xts_is_footer_empty() || ( $footer_html_block && 'html_block' === $footer_content_type ) ) ) : ?>
				<div class="container">
					<div class="row row-spacing-bottom-30 xts-footer-widgets">
						<?php
						if ( $footer_html_block && 'html_block' === $footer_content_type ) {
							?>
							<div class="xts-footer-col col-12">
								<?php echo xts_get_html_block_content( $footer_html_block ); // phpcs:ignore ?>
							</div>
							<?php
						} elseif ( 'widgets' === $footer_content_type ) {
							foreach ( $footer_config[ $footer_layout ]['cols'] as $key => $columns ) {
								$index = $key + 1;
								?>
								<div class="xts-footer-col <?php echo esc_attr( $columns ); ?>">
									<?php dynamic_sidebar( 'footer-' . $index ); ?>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
			<?php endif; ?>

			<?php xts_copyrights(); ?>

		</footer>
		<?php
	}

	add_action( 'xts_footer', 'xts_footer', 20 );
}

if ( ! function_exists( 'xts_copyrights' ) ) {
	/**
	 * Generate copyrights
	 *
	 * @since 1.0.0
	 */
	function xts_copyrights() {
		$page_id = xts_get_page_id();

		$metabox_disable_copyrights = get_post_meta( $page_id, '_xts_copyrights', true );

		$copyrights_needed = 'on' === $metabox_disable_copyrights ? false : xts_get_opt( 'copyrights' );
		$text_left         = xts_get_opt( 'copyrights_left_text' );
		$text_right        = xts_get_opt( 'copyrights_right_text' );
		$copyrights_layout = xts_get_opt( 'copyrights_layout' );
		$content_type      = xts_get_opt( 'copyrights_content_type' );
		$column_width      = 6;

		if ( 'centered' === $copyrights_layout ) {
			$column_width = 12;
		}

		if ( ! $copyrights_needed ) {
			return;
		}

		?>
		<div class="xts-copyrights-wrapper">
			<div class="container">
				<div class="row row-spacing-bottom-20 xts-copyrights xts-layout-<?php echo esc_attr( $copyrights_layout ); ?>">
					<div class="col-left col-12 col-lg-<?php echo esc_attr( $column_width ); ?>">
						<?php if ( 'text' === $content_type ) : ?>
							<?php if ( $text_left ) : ?>
								<?php echo do_shortcode( $text_left ); ?>
							<?php else : ?>
								<p>&copy; <?php echo esc_html( current_time( 'Y' ) ); ?>
									<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>. <?php esc_html_e( 'All rights reserved', 'xts-theme' ); ?>
								</p>
							<?php endif ?>
						<?php else : ?>
							<?php dynamic_sidebar( 'copyrights-left-widget-sidebar' ); ?>
						<?php endif; ?>
					</div>

					<?php if ( ( $text_right || is_active_sidebar( 'copyrights-right-widget-sidebar' ) ) && 'two_columns' === $copyrights_layout ) : ?>
						<div class="col-right col-12 col-lg-<?php echo esc_attr( $column_width ); ?>">
							<?php if ( 'text' === $content_type ) : ?>
								<?php echo do_shortcode( $text_right ); ?>
							<?php else : ?>
								<?php dynamic_sidebar( 'copyrights-right-widget-sidebar' ); ?>
							<?php endif; ?>
						</div>
					<?php endif ?>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'xts_prefooter' ) ) {
	/**
	 * Generate prefooter
	 *
	 * @since 1.0.0
	 */
	function xts_prefooter() {
		$page_id = xts_get_page_id();

		$metabox_disable_prefooter = get_post_meta( $page_id, '_xts_prefooter', true );

		$prefooter_content = xts_get_opt( 'prefooter_html_block' );
		$prefooter_needed  = $metabox_disable_prefooter ? false : $prefooter_content;

		if ( ! $prefooter_needed ) {
			return;
		}

		?>
		<div class="xts-prefooter">
			<div class="container">
				<?php echo xts_get_html_block_content( $prefooter_content ); // phpcs:ignore ?>
			</div>
		</div>
		<?php
	}

	add_action( 'xts_footer', 'xts_prefooter', 10 );
}
