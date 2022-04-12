<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package xts
 */

get_header();

xts_get_sidebar( 'sidebar-left' );

?> 
<div class="xts-content-area<?php echo esc_attr( xts_get_content_classes() ); ?>"> 
	<?php
	while ( have_posts() ) {
		the_post();

		do_action( 'xts_before_page' );

		xts_get_template_part( 'templates/content-page' );

		if ( xts_get_opt( 'page_comments' ) && ( comments_open() || get_comments_number() ) ) {
			comments_template();
		}

		do_action( 'xts_after_page' );
	}
	?>
</div> 
<?php

xts_get_sidebar( 'sidebar-right' );

get_footer();
