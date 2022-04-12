<?php
/**
 * Portfolio post type functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_portfolio_post_type_args' ) ) {
	/**
	 * Register post type portfolio.
	 *
	 * @return array Arguments for registering a post type.
	 */
	function xts_get_portfolio_post_type_args() {
		$portfolio_page_id = xts_get_opt( 'portfolio_page' );
		$has_archive       = $portfolio_page_id && get_post( $portfolio_page_id ) ? urldecode( get_page_uri( $portfolio_page_id ) ) : 'portfolio';
		$permalinks        = xts_get_portfolio_permalink_structure();

		$labels = array(
			'name'               => esc_html__( 'Portfolio', 'xts-theme' ),
			'singular_name'      => esc_html__( 'Project', 'xts-theme' ),
			'menu_name'          => esc_html__( 'Portfolio', 'xts-theme' ),
			'parent_item_colon'  => esc_html__( 'Parent project:', 'xts-theme' ),
			'all_items'          => esc_html__( 'All projects', 'xts-theme' ),
			'view_item'          => esc_html__( 'View project', 'xts-theme' ),
			'add_new_item'       => esc_html__( 'Add new project', 'xts-theme' ),
			'add_new'            => esc_html__( 'Add new project', 'xts-theme' ),
			'edit_item'          => esc_html__( 'Edit project', 'xts-theme' ),
			'update_item'        => esc_html__( 'Update project', 'xts-theme' ),
			'search_items'       => esc_html__( 'Search project', 'xts-theme' ),
			'not_found'          => esc_html__( 'Not found', 'xts-theme' ),
			'not_found_in_trash' => esc_html__( 'Not found in Trash', 'xts-theme' ),
		);

		return array(
			'label'               => esc_html__( 'Portfolio', 'xts-theme' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'page-attributes' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-format-gallery',
			'can_export'          => true,
			'has_archive'         => $has_archive,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => array(
				'slug'       => $permalinks['project_base'],
				'with_front' => false,
				'feeds'      => true,
			),
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);
	}
}

if ( ! function_exists( 'xts_get_portfolio_taxonomy_args' ) ) {
	/**
	 * Register post type portfolio.
	 *
	 * @return array Arguments for registering a taxonomy.
	 */
	function xts_get_portfolio_taxonomy_args() {
		$permalinks = xts_get_portfolio_permalink_structure();

		$labels = array(
			'name'                  => esc_html__( 'Project categories', 'xts-theme' ),
			'singular_name'         => esc_html__( 'Project category', 'xts-theme' ),
			'search_items'          => esc_html__( 'Search categories', 'xts-theme' ),
			'popular_items'         => esc_html__( 'Popular project categories', 'xts-theme' ),
			'all_items'             => esc_html__( 'All project categories', 'xts-theme' ),
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
			'rewrite'           => array(
				'slug'         => $permalinks['category_base'],
				'with_front'   => false,
				'hierarchical' => true,
			),
			'capabilities'      => array(),
			'show_in_rest'      => true,
		);
	}
}

if ( ! function_exists( 'xts_edit_portfolio_columns' ) ) {
	/**
	 * Add the custom columns to the portfolio post type
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_edit_portfolio_columns() {
		return array(
			'cb'             => '<input type="checkbox" />',
			'xts_thumbnail'  => '',
			'title'          => esc_html__( 'Title', 'xts-theme' ),
			'xts_categories' => esc_html__( 'Categories', 'xts-theme' ),
			'date'           => esc_html__( 'Date', 'xts-theme' ),
		);
	}

	add_filter( 'manage_edit-xts-portfolio_columns', 'xts_edit_portfolio_columns', 10 );
}

if ( ! function_exists( 'xts_manage_portfolio_columns' ) ) {
	/**
	 * Add data to the custom columns for the portfolio post type
	 *
	 * @since 1.0.0
	 *
	 * @param array   $columns Columns.
	 * @param integer $post_id Portfolio id.
	 */
	function xts_manage_portfolio_columns( $columns, $post_id ) {
		switch ( $columns ) {
			case 'xts_thumbnail':
				if ( has_post_thumbnail( $post_id ) ) {
					the_post_thumbnail( array( 60, 60 ) );
				}
				break;
			case 'xts_categories':
				$terms     = wp_get_post_terms( $post_id, 'xts-portfolio-cat' );
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

					<a href="<?php echo esc_url( 'edit.php?post_type=' . $post_type . '&xts-portfolio-cat=' . $term->slug ); ?>">
						<?php echo esc_html( $name ); ?>
					</a>
				<?php endforeach; ?>
					<?php
				break;
		}
	}

	add_filter( 'manage_xts-portfolio_posts_custom_column', 'xts_manage_portfolio_columns', 10, 2 );
}

if ( ! function_exists( 'xts_get_portfolio_categories_array' ) ) {
	/**
	 * Get portfolio taxonomies dropdown
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_portfolio_categories_array() {
		$output = array( '' => esc_html__( 'All', 'xts-theme' ) );

		if ( ! post_type_exists( 'xts-portfolio' ) ) {
			return array();
		}

		$categories = get_terms( 'xts-portfolio-cat' );

		foreach ( $categories as $key => $category ) {
			$output[ $category->term_id ] = $category->name;
		}

		return $output;
	}
}

if ( ! function_exists( 'xts_get_portfolio_post_classes' ) ) {
	/**
	 * Get portfolio post classes
	 *
	 * @since 1.0.0
	 * @return array
	 */
	function xts_get_portfolio_post_classes() {
		$design            = xts_get_loop_prop( 'portfolio_design' );
		$distortion_effect = xts_get_loop_prop( 'portfolio_distortion_effect' );

		$classes = array();

		$classes[] = 'xts-project';
		if ( 'default' === $design && $distortion_effect ) {
			wp_enqueue_script( 'imagesloaded' );
			xts_enqueue_js_script( 'distortion-effect-native' );
			$classes[] = 'xts-distortion';
		}

		return $classes;
	}
}

if ( ! function_exists( 'xts_scpo_single_posts_navigation_fix' ) ) {
	/**
	 * Fix active class in nav for shop page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $menu_items Menu items.
	 *
	 * @return array
	 */
	function xts_nav_menu_item_classes( $menu_items ) {
		$portfolio_page = (int) xts_get_opt( 'portfolio_page' );

		if ( ! empty( $menu_items ) && is_array( $menu_items ) ) {
			foreach ( $menu_items as $key => $menu_item ) {
				$classes = (array) $menu_item->classes;
				$menu_id = (int) $menu_item->object_id;

				// Unset active class for blog page.
				if ( xts_is_portfolio_archive() && $portfolio_page === $menu_id && 'page' === $menu_item->object ) {
					// Set active state if this is the shop page link.
					$menu_items[ $key ]->current = true;
					$classes[]                   = 'current-menu-item';
					$classes[]                   = 'current_page_item';
				} elseif ( is_singular( 'xts-portfolio' ) && $portfolio_page === $menu_id ) {
					// Set parent state if this is a product page.
					$classes[] = 'current_page_parent';
				}

				$menu_items[ $key ]->classes = array_unique( $classes );
			}
		}

		return $menu_items;
	}

	add_filter( 'wp_nav_menu_objects', 'xts_nav_menu_item_classes', 2 );
}

if ( ! function_exists( 'xts_template_redirect' ) ) {
	/**
	 * Handle redirects before content is output - hooked into template_redirect so is_page works.
	 *
	 * @since 1.0.0
	 */
	function xts_template_redirect() {
		if ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && (int) xts_get_opt( 'portfolio_page' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'xts-portfolio' ) ) { // phpcs:ignore
			wp_safe_redirect( get_post_type_archive_link( 'xts-portfolio' ) );
			exit;
		}
	}

	add_action( 'template_redirect', 'xts_template_redirect' );
}

if ( ! function_exists( 'xts_add_display_post_states' ) ) {
	/**
	 * Add display post states.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $post_states An array of post display states.
	 * @param WP_Post $post        The current post object.
	 *
	 * @return array
	 */
	function xts_add_display_post_states( $post_states, $post ) {
		if ( (int) xts_get_opt( 'portfolio_page' ) === $post->ID ) {
			$post_states['xts_page_for_projects'] = esc_html__( 'Portfolio Page', 'xts-theme' );
		}

		return $post_states;
	}

	add_filter( 'display_post_states', 'xts_add_display_post_states', 10, 2 );
}

if ( ! function_exists( 'xts_add_portfolio_permalink_settings' ) ) {
	/**
	 * Add portfolio permalink settings.
	 *
	 * @since 1.0.0
	 */
	function xts_add_portfolio_permalink_settings() {
		add_settings_section( 'xts_portfolio_permalink', esc_html__( 'Portfolio permalinks', 'xts-theme' ), 'xts_portfolio_permalink_settings_init', 'permalink' );
	}

	add_action( 'admin_menu', 'xts_add_portfolio_permalink_settings' );
}

if ( ! function_exists( 'xts_get_portfolio_permalink_structure' ) ) {
	/**
	 * Get portfolio permalink settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_portfolio_permalink_structure() {
		$saved_permalinks = (array) get_option( 'xts_portfolio_permalinks', array() );

		return wp_parse_args(
			array_filter( $saved_permalinks ),
			array(
				'project_base'  => 'project',
				'category_base' => 'project-cat',
			)
		);
	}
}

if ( ! function_exists( 'xts_portfolio_permalink_settings_init' ) ) {
	/**
	 * Show portfolio permalink settings HTML.
	 *
	 * @since 1.0.0
	 */
	function xts_portfolio_permalink_settings_init() {
		$permalinks = xts_get_portfolio_permalink_structure();

		?>
		<table class="form-table wc-permalink-structure">
			<tbody>
			<tr>
				<th>
					<label>
						<?php esc_html_e( 'Project base', 'xts-theme' ); ?>
					</label>
				</th>
				<td>
					<input name="xts_project_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $permalinks['project_base'] ); ?>" />
				</td>
			</tr>

			<tr>
				<th>
					<label>
						<?php esc_html_e( 'Project category base', 'xts-theme' ); ?>
					</label>
				</th>
				<td>
					<input name="xts_project_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $permalinks['category_base'] ); ?>" />
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}
}

if ( ! function_exists( 'xts_portfolio_permalink_settings_save' ) ) {
	/**
	 * Save portfolio permalink settings.
	 *
	 * @since 1.0.0
	 */
	function xts_portfolio_permalink_settings_save() {
		if ( ! is_admin() ) {
			return;
		}

		if ( isset( $_POST['xts_project_slug'], $_POST['xts_project_category_slug'] ) ) { // phpcs:ignore
			$permalinks                  = (array) get_option( 'xts_project_permalinks', array() );
			$permalinks['category_base'] = stripslashes_deep( $_POST['xts_project_category_slug'] ); // phpcs:ignore
			$permalinks['project_base']  = stripslashes_deep( $_POST['xts_project_slug'] ); // phpcs:ignore

			update_option( 'xts_portfolio_permalinks', $permalinks );
		}
	}

	add_action( 'admin_menu', 'xts_portfolio_permalink_settings_save' );
}

if ( ! function_exists( 'xts_set_projects_per_page' ) ) {
	/**
	 * Portfolio projects per page.
	 *
	 * @since 1.0.0
	 *
	 * @param object $query Query.
	 */
	function xts_set_projects_per_page( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( $query->is_post_type_archive( 'xts-portfolio' ) || $query->is_tax( 'xts-portfolio-cat' ) ) {
			$query->set( 'posts_per_page', (int) xts_get_opt( 'portfolio_per_page' ) );
		}
	}

	add_action( 'pre_get_posts', 'xts_set_projects_per_page' );
}