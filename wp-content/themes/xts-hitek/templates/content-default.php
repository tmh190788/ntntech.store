<?php
/**
 * Template used to display post content.
 *
 * @package xts
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( xts_get_post_classes() ); ?>>

	<?php if ( xts_has_post_thumbnail( get_the_ID() ) ) : ?>
		<div class="xts-post-thumb">
			<?php xts_post_thumbnail( array( 'video', 'audio', 'gallery' ) ); ?>

			<?php xts_meta_post_labels(); ?>
			
			<a class="xts-post-link xts-fill" href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"></a>
		</div>
	<?php endif; ?>

	<div class="xts-post-content">
		<?php if ( xts_get_loop_prop( 'blog_post_title' ) ) : ?>
			<h3 class="xts-post-title xts-entities-title">
				<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
					<?php the_title(); ?>
				</a>
			</h3>
		<?php endif; ?>

		<?php if ( xts_get_loop_prop( 'blog_post_meta' ) || xts_get_loop_prop( 'blog_post_categories' ) ) : ?>
			<div class="xts-post-header">
				<?php if ( xts_get_loop_prop( 'blog_post_meta' ) ) : ?>
					<div class="xts-post-meta">
						<?php xts_meta_post_date(); ?>
					</div>
				<?php endif; ?>

				<?php if ( xts_get_loop_prop( 'blog_post_categories' ) ) : ?>
					<?php xts_meta_post_categories(); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( is_search() && xts_get_loop_prop( 'blog_post_text' ) ) : ?>
			<div class="xts-post-desc">
				<?php the_excerpt(); ?>
			</div>
		<?php elseif ( xts_get_loop_prop( 'blog_post_text' ) ) : ?>
			<div class="xts-post-desc xts-reset-all-last">
				<?php xts_the_content(); ?>
			</div>
		<?php endif; ?>

		<a class="<?php echo esc_attr( xts_get_rtl_inverted_string( 'xts-button xts-size-l xts-style-link-3 xts-color-default xts-icon-pos-right xts-icon-style-default xts-icon-anim-move-right' ) ); ?>" href="<?php echo esc_url( xts_get_post_read_more_link() ); ?>">
			<span class="xts-button-text"><?php echo esc_html__( 'Read more', 'xts-theme' ); ?></span>
			<span class="xts-button-icon"><i></i></span>
		</a>
	</div>

</article>
