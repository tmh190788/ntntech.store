<?php
/**
 * Template used to display post content on single portfolio pages.
 *
 * @package xts
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="xts-single-project-content">
		<?php the_content(); ?>
	</div>
</article>
