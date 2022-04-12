<?php
/**
 * The template for displaying portfolio Archive page
 *
 * @package xts
 */

if ( 'fragments' === xts_is_ajax() ) {
	xts_get_portfolio_main_loop( true );
	die();
}

if ( ! xts_is_ajax() ) {
	get_header();
} else {
	xts_page_top_part();
}

xts_get_sidebar( 'sidebar-left' );

if ( have_posts() ) {
	xts_get_template_part( 'templates/portfolio-loop' );
} else {
	xts_get_template_part( 'templates/content-none' );
}

xts_get_sidebar( 'sidebar-right' );

do_action( 'xts_after_portfolio_loop' );

if ( ! xts_is_ajax() ) {
	get_footer();
} else {
	xts_page_bottom_part();
}
