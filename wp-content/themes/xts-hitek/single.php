<?php
/**
 * The template for displaying all single posts.
 *
 * @package xts
 */

$template_name = 'single';

$design = xts_get_opt( 'blog_single_design' );

if ( 'default' !== $design ) {
	$template_name = 'single-' . $design;
}
$content_classes = xts_get_content_classes();

get_header();

xts_get_sidebar( 'sidebar-left' );

?>
<div class="xts-content-area<?php echo esc_attr( $content_classes ); ?>"> 
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php xts_get_template_part( 'templates/content-' . $template_name ); ?>
	<?php endwhile; ?>
</div> 
<?php

xts_get_sidebar( 'sidebar-right' );

get_footer();
