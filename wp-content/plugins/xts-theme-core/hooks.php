<?php
/**
 * Add hooks for template.
 *
 * @package xts.
 */

use XTS\Options\Metaboxes;
use XTS\Options\Page;

add_action( 'add_meta_boxes', array( Metaboxes::get_instance(), 'register_metaboxes' ) );
add_action( 'add_meta_boxes_comment', array( Metaboxes::get_instance(), 'register_comment_metaboxes' ) );

add_action( 'admin_bar_menu', array( Page::get_instance(), 'admin_bar_links' ), 100 );
