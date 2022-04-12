<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package xts
 */

get_header();

?> 
<div class="xts-content-area xts-404-content col-12"> 
	<span><?php esc_html_e( '404', 'xts-theme' ); ?></span>
	<h3><?php esc_html_e( 'Page not found', 'xts-theme' ); ?></h3>
	<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'xts-theme' ); ?></p>
	<?php get_search_form(); ?>
</div> 
<?php

get_footer();
