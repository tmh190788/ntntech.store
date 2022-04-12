<?php
/**
 * Template used to display post content in link format.
 *
 * @package xts
 */

$article_id           = get_the_ID();
$link_target          = get_post_meta( get_the_ID(), '_xts_post_link_blank', true );
$target               = $link_target ? ' _blank' : '_self';
$post_content_classes = '';

if ( ! xts_get_loop_prop( 'blog_post_black_white' ) ) {
	$post_content_classes = ' xts-scheme-light';
}

?>

<article id="post-<?php echo esc_attr( $article_id ); ?>" <?php post_class( xts_get_post_classes() ); ?>>

	<a href="<?php echo esc_url( get_post_meta( get_the_ID(), '_xts_post_link', true ) ); ?>" class="xts-post-link xts-fill" target="<?php echo esc_attr( $target ); ?>" rel="bookmark"></a>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="xts-post-image xts-with-bg xts-fill" style="background-image:url(<?php echo esc_url( get_the_post_thumbnail_url() ); ?>);"></div>
	<?php endif; ?>

	<?php xts_meta_post_labels(); ?>

	<div class="xts-post-hover xts-fill"></div>

	<div class="xts-post-content<?php echo esc_attr( $post_content_classes ); ?>">
		<?php if ( xts_get_loop_prop( 'blog_post_title' ) ) : ?>
			<h3 class="xts-post-title xts-entities-title">
				<?php the_title(); ?>
			</h3>
		<?php endif; ?>

		<?php xts_post_link_template(); ?>
	</div>

</article>
