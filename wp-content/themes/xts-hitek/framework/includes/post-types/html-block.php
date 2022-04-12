<?php
/**
 * HTML Block post type functions
 *
 * @package xts
 */

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_html_block_post_type_args' ) ) {
	/**
	 * Register post type Html block.
	 *
	 * @return array Arguments for registering a post type.
	 */
	function xts_get_html_block_post_type_args() {
		$labels = array(
			'name'               => esc_html__( 'HTML Blocks', 'xts-theme' ),
			'singular_name'      => esc_html__( 'HTML Block', 'xts-theme' ),
			'menu_name'          => esc_html__( 'HTML Blocks', 'xts-theme' ),
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
			'label'               => esc_html__( 'xts-html-block', 'xts-theme' ),
			'description'         => esc_html__( 'HTML Blocks for custom HTML to place in your pages', 'xts-theme' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'has_archive'         => false,
			'public'              => true,
			'publicly_queryable'  => is_user_logged_in(),
			'hierarchical'        => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-schedule',
			'can_export'          => true,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);
	}
}


if ( ! function_exists( 'xts_get_html_block_taxonomy_args' ) ) {
	/**
	 * Register HTML Block taxonomy.
	 *
	 * @return array Arguments for registering a taxonomy.
	 */
	function xts_get_html_block_taxonomy_args() {
		$labels = array(
			'name'                  => esc_html__( 'HTML Block categories', 'xts-theme' ),
			'singular_name'         => esc_html__( 'HTML Block category', 'xts-theme' ),
			'search_items'          => esc_html__( 'Search categories', 'xts-theme' ),
			'popular_items'         => esc_html__( 'Popular categories', 'xts-theme' ),
			'all_items'             => esc_html__( 'All categories', 'xts-theme' ),
			'parent_item'           => esc_html__( 'Parent category', 'xts-theme' ),
			'parent_item_colon'     => esc_html__( 'Parent category', 'xts-theme' ),
			'edit_item'             => esc_html__( 'Edit category', 'xts-theme' ),
			'update_item'           => esc_html__( 'Update category', 'xts-theme' ),
			'add_new_item'          => esc_html__( 'Add New category', 'xts-theme' ),
			'new_item_name'         => esc_html__( 'New category', 'xts-theme' ),
			'add_or_remove_items'   => esc_html__( 'Add or remove categories', 'xts-theme' ),
			'choose_from_most_used' => esc_html__( 'Choose from most used', 'xts-theme' ),
			'menu_name'             => esc_html__( 'Category', 'xts-theme' ),
		);

		return array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'html-block-cat' ),
			'capabilities'      => array(),
			'show_in_rest'      => true,
		);
	}
}

if ( ! function_exists( 'xts_edit_html_blocks_columns' ) ) {
	/**
	 * Add the custom columns to the html block post type
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_edit_html_blocks_columns() {
		return array(
			'cb'             => '<input type="checkbox" />',
			'title'          => esc_html__( 'Title', 'xts-theme' ),
			'xts_shortcode'  => esc_html__( 'Shortcode', 'xts-theme' ),
			'xts_categories' => esc_html__( 'Categories', 'xts-theme' ),
			'date'           => esc_html__( 'Date', 'xts-theme' ),
		);
	}

	add_filter( 'manage_edit-xts-html-block_columns', 'xts_edit_html_blocks_columns', 10 );
}

if ( ! function_exists( 'xts_manage_html_blocks_columns' ) ) {
	/**
	 * Add data to the custom columns for the html block post type
	 *
	 * @since 1.0.0
	 *
	 * @param array   $columns Columns.
	 * @param integer $post_id Html block id.
	 */
	function xts_manage_html_blocks_columns( $columns, $post_id ) {
		switch ( $columns ) {
			case 'xts_shortcode':
				echo '<strong class="xts-user-select-all">[xts_html_block id="' . esc_attr( $post_id ) . '"]</strong>';
				break;

			case 'xts_categories':
				$terms     = wp_get_post_terms( $post_id, 'xts-html-block-cat' );
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

					<a href="<?php echo esc_url( 'edit.php?post_type=' . $post_type . '&xts-html-block-cat=' . $term->slug ); ?>">
						<?php echo esc_html( $name ); ?>
					</a>
				<?php endforeach; ?>
				<?php
				break;
		}
	}

	add_filter( 'manage_xts-html-block_posts_custom_column', 'xts_manage_html_blocks_columns', 10, 2 );
}

if ( ! function_exists( 'xts_add_taxonomy_filter_to_post_types' ) ) {
	/**
	 * Add taxonomy select to post types.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type The post type slug.
	 */
	function xts_add_taxonomy_filter_to_post_types( $post_type ) {
		$allowed_post_types = array(
			'xts-html-block',
			'xts-portfolio',
			'xts-slide',
		);

		if ( ! in_array( $post_type, $allowed_post_types ) ) { // phpcs:ignore
			return;
		}

		$post_types_taxonomy = array(
			'xts-html-block' => 'xts-html-block-cat',
			'xts-portfolio'  => 'xts-portfolio-cat',
			'xts-slide'      => 'xts_slider',
		);

		$taxonomy_slug = $post_types_taxonomy[ $post_type ];
		$terms         = get_terms( $taxonomy_slug );

		?>
		<select name="<?php echo esc_attr( $taxonomy_slug ); ?>" class="postform">
			<option value="">
				<?php echo esc_html__( 'All Categories ', 'xts-theme' ); ?>
			</option>

			<?php foreach ( $terms as $term ) : ?>
				<?php
				$selected_attrs = '';

				if ( isset( $_GET[ $taxonomy_slug ] ) && $_GET[ $taxonomy_slug ] === $term->slug ) { // phpcs:ignore
					$selected_attrs = 'selected';
				}

				?>

				<option value="<?php echo esc_attr( $term->slug ); ?>" <?php echo esc_attr( $selected_attrs ); ?>>
					<?php echo esc_html( $term->name . ' (' . $term->count . ')' ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	add_action( 'restrict_manage_posts', 'xts_add_taxonomy_filter_to_post_types', 10, 2 );
}

if ( ! function_exists( 'xts_get_html_block_content' ) ) {
	/**
	 * Function to get HTML Block content
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id HTML Block id.
	 *
	 * @return mixed
	 */
	function xts_get_html_block_content( $id ) {
		$id = apply_filters( 'wpml_object_id', $id, 'xts-html-block', true );

		$post = get_post( $id );

		if ( ! $post || 'xts-html-block' !== $post->post_type ) {
			return '';
		}

		$negative_gap = get_post_meta( $id, '_xts_negative_gap', true );

		if ( 'inherit' === $negative_gap || ! $negative_gap ) {
			$negative_gap = xts_get_opt( 'negative_gap', 'enabled' );
		}

		if ( 'enabled' === $negative_gap ) {
			add_action( 'elementor/frontend/section/before_render', 'xts_add_section_class_if_content_width', 1000 );
		} elseif ( 'disabled' === $negative_gap ) {
			remove_action( 'elementor/frontend/section/before_render', 'xts_add_section_class_if_content_width', 1000 );
		}

		if ( xts_is_elementor_installed() && Plugin::$instance->db->is_built_with_elementor( $id ) ) {
			$content = xts_elementor_get_content( $id );
		} else {
			$content = do_shortcode( $post->post_content );
		}

		if ( 'enabled' === $negative_gap ) {
			remove_action( 'elementor/frontend/section/before_render', 'xts_add_section_class_if_content_width', 1000 );
		}

		return $content;
	}
}

if ( ! function_exists( 'xts_html_block_shortcode' ) ) {
	/**
	 * HTML Block shortcode
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Associative array of attributes.
	 *
	 * @return string
	 */
	function xts_html_block_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts
		);

		return xts_get_html_block_content( $atts['id'] );
	}
}
