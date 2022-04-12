<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package xts
 */

if ( xts_is_ajax() ) {
	xts_get_template_part( 'templates/loop' );
	die();
}

get_header();

xts_get_sidebar( 'sidebar-left' );

if ( have_posts() ) {
	xts_get_template_part( 'templates/loop' );
} else {
	xts_get_template_part( 'templates/content-none' );
}

xts_get_sidebar( 'sidebar-right' );

get_footer();
