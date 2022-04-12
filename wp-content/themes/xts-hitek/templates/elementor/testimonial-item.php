<?php
/**
 * Testimonial item template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>

<div class="xts-testimonial">
	<header class="xts-testimonial-header">
		<?php if ( $image_output ) : ?>
			<div class="xts-testimonial-image">
				<?php echo wp_kses( $image_output, 'xts_media' ); ?>
			</div>
		<?php endif; ?>

		<div class="xts-testimonial-author xts-reset-mb-10 xts-reset-all-last">
			<?php if ( $testimonial['name'] ) : ?>
				<span class="title xts-testimonial-name<?php echo esc_attr( $name_classes ); ?>" data-elementor-setting-key="testimonials.<?php echo esc_attr( $index ); ?>.name">
					<?php echo wp_kses( $testimonial['name'], xts_get_allowed_html() ); ?>
				</span>
			<?php endif; ?>

			<?php if ( 'yes' === $stars_rating ) : ?>
				<div class="xts-testimonial-stars star-rating">
					<span></span>
				</div>
			<?php endif; ?>

			<?php if ( $testimonial['title'] ) : ?>
				<div class="xts-testimonial-user-title<?php echo esc_attr( $title_classes ); ?>" data-elementor-setting-key="testimonials.<?php echo esc_attr( $index ); ?>.title">
					<?php echo wp_kses( $testimonial['title'], xts_get_allowed_html() ); ?>
				</div>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( $testimonial['description'] ) : ?>
		<div class="xts-testimonial-desc<?php echo esc_attr( $description_classes ); ?>" data-elementor-setting-key="testimonials.<?php echo esc_attr( $index ); ?>.description">
			<?php echo do_shortcode( $testimonial['description'] ); ?>
		</div>
	<?php endif; ?>
</div>