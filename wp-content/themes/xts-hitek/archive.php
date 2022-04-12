<?php
/**
 * The template for displaying archive pages.
 *
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
