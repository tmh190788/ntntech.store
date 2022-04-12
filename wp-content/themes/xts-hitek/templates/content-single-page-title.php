<?php
/**
 * Template used to display post content on single pages.
 *
 * @package xts
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( xts_get_single_post_classes() ); ?>>
	<?php if ( xts_has_post_thumbnail( get_the_ID(), true ) ) : ?>
		<div class="xts-single-post-thumb">
			<?php xts_single_post_thumbnail(); ?>
		</div>
	<?php endif; ?>

	<?php if ( xts_get_opt( 'blog_single_content_boxed' ) ) : ?>
		<div class="xts-single-post-boxed">
	<?php endif; ?>

	<div class="xts-single-post-content">
		<?php the_content(); ?>
		<?php wp_link_pages(); ?>
	</div>

	<?php if ( get_the_tag_list() || xts_get_opt( 'blog_single_share_buttons' ) ) : ?>
		<footer class="xts-single-post-footer">
			<?php if ( get_the_tag_list() ) : ?>
				<div class="xts-tags-list">
					<?php the_tags( '', ' ' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( xts_get_opt( 'blog_single_share_buttons' ) ) : ?>
				<?php xts_social_buttons_template( xts_get_default_value( 'single_post_social_buttons_args' ) ); ?>
			<?php endif; ?>
		</footer>
	<?php endif; ?>

	<?php if ( get_the_author_meta( 'description' ) && xts_get_opt( 'blog_single_author_bio' ) ) : ?>
		<?php xts_author_bio(); ?>
	<?php endif; ?>

	<?php if ( xts_get_opt( 'blog_single_navigation' ) ) : ?>
		<?php xts_get_template_part( 'templates/single-posts-navigation' ); ?>
	<?php endif; ?>

	<?php if ( xts_get_opt( 'blog_single_content_boxed' ) ) : ?>
		</div>
	<?php endif; ?>
</article>

<?php if ( xts_get_opt( 'blog_single_content_boxed' ) ) : ?>
	<div class="xts-single-post-boxed">
<?php endif; ?>

<?php if ( xts_get_opt( 'blog_single_related_posts' ) ) : ?>
	<?php xts_get_related_posts( $post ); ?>
<?php endif; ?>

<?php if ( comments_open() || get_comments_number() ) : ?>
	<?php comments_template(); ?>
<?php endif; ?>

<?php if ( xts_get_opt( 'blog_single_content_boxed' ) ) : ?>
	</div>
<?php endif; ?>