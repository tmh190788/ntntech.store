<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package xts
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php the_content(); ?>
	<?php wp_link_pages(); ?>
</article>
