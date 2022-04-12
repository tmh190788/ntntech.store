<?php
/**
 * Sidebar post type functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_sidebar_post_type_args' ) ) {
	/**
	 * Register post type sidebar.
	 *
	 * @return array Arguments for registering a post type.
	 */
	function xts_get_sidebar_post_type_args() {
		$labels = array(
			'name'               => esc_html__( 'Sidebars', 'xts-theme' ),
			'singular_name'      => esc_html__( 'Sidebar', 'xts-theme' ),
			'menu_name'          => esc_html__( 'Sidebars', 'xts-theme' ),
			'parent_item_colon'  => esc_html__( 'Parent item:', 'xts-theme' ),
			'all_items'          => esc_html__( 'All items', 'xts-theme' ),
			'view_item'          => esc_html__( 'View item', 'xts-theme' ),
			'add_new_item'       => esc_html__( 'Add new item', 'xts-theme' ),
			'add_new'            => esc_html__( 'Add new', 'xts-theme' ),
			'edit_item'          => esc_html__( 'Edit item', 'xts-theme' ),
			'update_item'        => esc_html__( 'Update item', 'xts-theme' ),
			'search_items'       => esc_html__( 'Search item', 'xts-theme' ),
			'not_found'          => esc_html__( 'Not found', 'xts-theme' ),
			'not_found_in_trash' => esc_html__( 'Not found in Trash', 'xts-theme' ),
		);

		return array(
			'label'               => esc_html__( 'xts-sidebar', 'xts-theme' ),
			'description'         => esc_html__( 'You can create additional custom sidebar', 'xts-theme' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-welcome-widgets-menus',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'page',
		);
	}
}
