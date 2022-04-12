<?php
/**
 * Single product navigations template.
 *
 * @package xts
 */

$next = get_next_post();
$prev = get_previous_post();

$next_post = $next ? wc_get_product( $next->ID ) : false;
$prev_post = $prev ? wc_get_product( $prev->ID ) : false;

?>

<div class="xts-single-product-nav">
	<?php if ( $prev_post ) : ?>
		<div class="xts-nav-btn xts-prev xts-event-hover">
			<a href="<?php echo esc_url( $prev_post->get_permalink() ); ?>"></a>

			<div class="xts-dropdown">
				<div class="xts-dropdown-inner">
					<a href="<?php echo esc_url( $prev_post->get_permalink() ); ?>" class="xts-thumb">
						<?php echo wp_kses( $prev_post->get_image( 'woocommerce_gallery_thumbnail' ), 'xts_media' ); ?>
					</a>

					<div class="xts-content">
						<a href="<?php echo esc_url( $prev_post->get_permalink() ); ?>" class="xts-entities-title">
							<?php echo esc_html( $prev_post->get_title() ); ?>
						</a>

						<span class="price">
							<?php echo apply_filters( 'woocommerce_xts_get_price_html', $prev_post->get_price_html() ); // phpcs:ignore XSS ok. ?>
						</span>
					</div>
				</div>
			</div>
		</div>
	<?php endif ?>

	<?php if ( $next_post ) : ?>
		<div class="xts-nav-btn xts-next xts-event-hover">
			<a href="<?php echo esc_url( $next_post->get_permalink() ); ?>"></a>

			<div class="xts-dropdown">
				<div class="xts-dropdown-inner">
					<a href="<?php echo esc_url( $next_post->get_permalink() ); ?>" class="xts-thumb">
						<?php echo wp_kses( $next_post->get_image( 'woocommerce_gallery_thumbnail' ), 'xts_media' ); ?>
					</a>

					<div class="xts-content">
						<a href="<?php echo esc_url( $next_post->get_permalink() ); ?>" class="xts-entities-title">
							<?php echo esc_html( $next_post->get_title() ); ?>
						</a>

						<span class="price">
							<?php echo apply_filters( 'woocommerce_xts_get_price_html', $next_post->get_price_html() ); // phpcs:ignore XSS ok. ?>
						</span>
					</div>
				</div>
			</div>
		</div>
	<?php endif ?>
</div>
