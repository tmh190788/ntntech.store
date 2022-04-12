<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the .website-wrapper div and all content after
 *
 * @package xts
 */

?>
		<?php xts_page_bottom_part(); ?>

		<?php if ( xts_needs_footer() && ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) ) : ?>
			<?php do_action( 'xts_footer' ); ?>
		<?php endif; ?>

		</div> <!-- .site-wrapper -->

		<?php do_action( 'xts_after_site_wrapper' ); ?>

		<?php wp_footer(); ?>

	</body>
</html>
