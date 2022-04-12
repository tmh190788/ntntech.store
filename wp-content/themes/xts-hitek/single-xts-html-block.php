<?php
/**
 * The template for displaying all html block.
 *
 * @package xts
 */

get_header();

$wrapper_classes   = '';
$footer_html_block = (int) xts_get_opt( 'footer_html_block' );
$color_scheme      = xts_get_opt( 'footer_color_scheme' );
$current_id        = get_the_ID();

if ( 'inherit' !== $color_scheme ) {
	$wrapper_classes .= ' xts-scheme-' . $color_scheme;
}

?>

<div class="xts-html-block-scheme-switcher">
	<div class="xts-html-block-scheme-dark" data-color="#ffffff">
		<?php esc_html_e( 'Dark', 'xts-theme' ); ?>
	</div>

	<div class="xts-html-block-scheme-light" data-color="#212121">
		<?php esc_html_e( 'Light', 'xts-theme' ); ?>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.xts-html-block-scheme-switcher > div').on('click', function () {
			jQuery('.xts-site-wrapper').css('background-color', jQuery(this).data('color'));
		});
	});
</script>

<div class="xts-content-area col-12">
	<?php if ( $current_id === $footer_html_block ) : ?>
		<footer class="xts-footer xts-with-html_block<?php echo esc_attr( $wrapper_classes ); ?>">
			<div class="container">
				<div class="row xts-footer-widgets">
					<div class="xts-footer-col col-12">
						<?php while ( have_posts() ) : ?>
							<?php the_post(); ?>
							<?php the_content(); ?>
						<?php endwhile; ?>
					</div>
				</div>
			</div>
		</footer>
	<?php else : ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<?php the_content(); ?>
		<?php endwhile; ?>
	<?php endif; ?>
</div>

<?php

get_footer();
