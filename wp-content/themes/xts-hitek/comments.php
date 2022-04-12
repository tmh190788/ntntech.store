<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package xts
 */

if ( post_password_required() ) {
	return;
}

?>

<div class="xts-comments-area comments" id="comments">
	<?php if ( have_comments() ) : ?>
		<h3 class="xts-comments-title">
			<?php
				echo sprintf(
					/* translators: 1: number of comments, 2: post title */
					_nx( // phpcs:ignore
						'%1$s thought on &ldquo;%2$s&rdquo;',
						'%1$s thoughts on &ldquo;%2$s&rdquo;',
						get_comments_number(),
						'comments title',
						'xts-theme'
					),
					esc_html( number_format_i18n( get_comments_number() ) ),
					'<span>' . get_the_title() . '</span>' // phpcs:ignore
				);
			?>
		</h3>

		<ol class="commentlist">
			<?php
				wp_list_comments(
					array(
						'style'       => 'li',
						'short_ping'  => true,
						'avatar_size' => 55,
					)
				);
			?>
		</ol>
	<?php endif; ?>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav class="xts-comment-nav" role="navigation">
			<?php previous_comments_link( esc_html__( 'Older comments', 'xts-theme' ) ); ?>
			<span class="xts-comment-nav-sep"></span>
			<?php next_comments_link( esc_html__( 'Newer comments', 'xts-theme' ) ); ?>
		</nav>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="xts-no-comments"><?php esc_html_e( 'Comments are closed.', 'xts-theme' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>
</div>
