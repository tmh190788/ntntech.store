<?php
/**
 * Slider post type functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_slider_post_type_args' ) ) {
	/**
	 * Register post type slider.
	 *
	 * @return array Arguments for registering a post type.
	 */
	function xts_get_slider_post_type_args() {
		$labels = array(
			'name'               => esc_html__( '[XTemos] Slider', 'xts-theme' ),
			'singular_name'      => esc_html__( 'Slide', 'xts-theme' ),
			'menu_name'          => esc_html__( 'Slides', 'xts-theme' ),
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
			'label'               => 'xts-slide',
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'page-attributes', 'custom-fields' ),
			'hierarchical'        => false,
			'public'              => true,
			'publicly_queryable'  => is_user_logged_in(),
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-images-alt2',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);
	}
}

if ( ! function_exists( 'xts_get_slider_taxonomy_args' ) ) {
	/**
	 * Register slider taxonomy.
	 *
	 * @return array Arguments for registering a taxonomy.
	 */
	function xts_get_slider_taxonomy_args() {
		$labels = array(
			'name'                  => esc_html__( 'Sliders', 'xts-theme' ),
			'singular_name'         => esc_html__( 'Slider', 'xts-theme' ),
			'search_items'          => esc_html__( 'Search sliders', 'xts-theme' ),
			'popular_items'         => esc_html__( 'Popular sliders', 'xts-theme' ),
			'all_items'             => esc_html__( 'All items', 'xts-theme' ),
			'parent_item'           => esc_html__( 'Parent item', 'xts-theme' ),
			'parent_item_colon'     => esc_html__( 'Parent item', 'xts-theme' ),
			'edit_item'             => esc_html__( 'Edit item', 'xts-theme' ),
			'update_item'           => esc_html__( 'Update item', 'xts-theme' ),
			'add_new_item'          => esc_html__( 'Add new item', 'xts-theme' ),
			'new_item_name'         => esc_html__( 'New item', 'xts-theme' ),
			'add_or_remove_items'   => esc_html__( 'Add or remove item', 'xts-theme' ),
			'choose_from_most_used' => esc_html__( 'Choose from most used sliders', 'xts-theme' ),
			'menu_name'             => esc_html__( 'Slider', 'xts-theme' ),
		);

		return array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => false,
			'show_ui'           => true,
			'query_var'         => false,
			'rewrite'           => false,
			'capabilities'      => array(),
			'show_in_rest'      => true,
		);
	}
}

if ( ! function_exists( 'xts_duplicate_slide_action' ) ) {
	/**
	 * Duplicate slide
	 *
	 * @since 1.0.0
	 *
	 * @param array  $actions An array of row action links.
	 * @param object $post    The post object.
	 *
	 * @return array
	 */
	function xts_duplicate_slide_action( $actions, $post ) {
		if ( 'xts-slide' !== $post->post_type ) {
			return $actions;
		}

		if ( current_user_can( 'edit_posts' ) ) {
			$actions['duplicate'] = '<a href="' . wp_nonce_url( 'admin.php?action=xts_duplicate_post_as_draft&post=' . $post->ID, basename( __FILE__ ), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
		}

		return $actions;
	}

	add_filter( 'post_row_actions', 'xts_duplicate_slide_action', 10, 2 );
}

if ( ! function_exists( 'xts_duplicate_post_as_draft' ) ) {
	/**
	 * Duplicate slide as draft
	 *
	 * @since 1.0.0
	 */
	function xts_duplicate_post_as_draft() {
		global $wpdb;

		if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'xts_duplicate_post_as_draft' === $_REQUEST['action'] ) ) ) {
			wp_die( 'No post to duplicate has been supplied!' );
		}

		if ( ! isset( $_GET['duplicate_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['duplicate_nonce'] ) ), basename( __FILE__ ) ) ) {
			return;
		}

		$post_id = ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
		$post    = get_post( $post_id );

		$current_user    = wp_get_current_user();
		$new_post_author = $current_user->ID;

		if ( isset( $post ) && null !== $post ) {

			// new post data array.
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => $post->post_title . ' (duplicate)',
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order,
			);

			$new_post_id = wp_insert_post( $args );

			$taxonomies = get_object_taxonomies( $post->post_type );
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}

			$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" ); // phpcs:ignore

			if ( 0 !== count( $post_meta_infos ) ) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				foreach ( $post_meta_infos as $meta_info ) {
					$meta_key = $meta_info->meta_key;

					if ( '_wp_old_slug' === $meta_key ) {
						continue;
					}

					$meta_value      = addslashes( $meta_info->meta_value );
					$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
				}

				$sql_query .= implode( ' UNION ALL ', $sql_query_sel );
				$wpdb->query( $sql_query ); // phpcs:ignore
			}

			wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
			exit;
		} else {
			wp_die( 'Post creation failed, could not find original post: ' . esc_attr( $post_id ) );
		}
	}

	add_filter( 'admin_action_xts_duplicate_post_as_draft', 'xts_duplicate_post_as_draft', 10, 2 );
}

if ( ! function_exists( 'xts_edit_slide_columns' ) ) {
	/**
	 * Add the custom columns to the slide post type
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_edit_slide_columns() {
		return array(
			'cb'         => '<input type="checkbox" />',
			'title'      => esc_html__( 'Title', 'xts-theme' ),
			'xts_slider' => esc_html__( 'Slider', 'xts-theme' ),
			'date'       => esc_html__( 'Date', 'xts-theme' ),
		);
	}

	add_filter( 'manage_edit-xts-slide_columns', 'xts_edit_slide_columns', 10 );
}

if ( ! function_exists( 'xts_manage_slide_columns' ) ) {
	/**
	 * Add data to the custom columns for the slide post type
	 *
	 * @since 1.0.0
	 *
	 * @param array   $columns Columns.
	 * @param integer $post_id Portfolio id.
	 */
	function xts_manage_slide_columns( $columns, $post_id ) {
		switch ( $columns ) {
			case 'xts_slider':
				$terms     = wp_get_post_terms( $post_id, 'xts_slider' );
				$post_type = get_post_type( $post_id );
				$keys      = array_keys( $terms );
				$last_key  = end( $keys );

				if ( ! $terms ) {
					echo '—';
				}

				?>
				<?php foreach ( $terms as $key => $term ) : ?>
					<?php
					$name = $term->name;

					if ( $key !== $last_key ) {
						$name .= ',';
					}

					?>

				<a href="<?php echo esc_url( 'edit.php?post_type=' . $post_type . '&xts_slider=' . $term->slug ); ?>">
					<?php echo esc_html( $name ); ?>
				</a>
			<?php endforeach; ?>
				<?php
				break;
		}
	}

	add_filter( 'manage_xts-slide_posts_custom_column', 'xts_manage_slide_columns', 10, 2 );
}
