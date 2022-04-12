<?php
/**
 * Size guide
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Options\Metaboxes;
use XTS\Framework\Options;

/**
 * Size guide
 *
 * @since 1.0.0
 */
class WC_Size_Guide extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ), 10 );
		add_action( 'init', array( $this, 'add_metaboxes' ), 20 );
		add_action( 'init', array( $this, 'add_product_metaboxes' ), 30 );

		add_action( 'save_post', array( $this, 'update_product_categories' ) );
		add_action( 'edit_post', array( $this, 'update_product_categories' ) );

		add_action( 'woocommerce_single_product_summary', array( $this, 'size_guide_single_btn' ), 35 );
	}

	/**
	 * Add product to compare button on single product.
	 *
	 * @since 1.0
	 */
	public function size_guide_single_btn() {
		$this->size_guide_btn( 'xts-style-inline' );
	}

	/**
	 * Frontend size guide template
	 *
	 * @since 1.0.0
	 *
	 * @param string $button_classes Button classes.
	 */
	public function size_guide_btn( $button_classes ) {
		$id              = $this->get_id();
		$size_guide_post = get_post( $id );
		$table           = get_post_meta( $id, '_xts_size_guide_table_data', true );

		if ( ! xts_get_opt( 'single_product_size_guide' ) || ! $size_guide_post || ! $table ) {
			return;
		}

		xts_enqueue_js_library( 'tooltip' );
		xts_enqueue_js_script( 'tooltip' );
		xts_enqueue_js_library( 'magnific' );
		xts_enqueue_js_script( 'popup-element' );

		xts_get_template(
			'size-guide.php',
			array(
				'title'          => 'yes',
				'content'        => 'yes',
				'post'           => $size_guide_post,
				'hide_table'     => get_post_meta( $id, '_xts_size_guide_table', true ),
				'table'          => json_decode( $table ),
				'button_classes' => $button_classes,
			),
			'wc-size-guide'
		);
	}

	/**
	 * Get size guide ID.
	 *
	 * @since 1.0.0
	 */
	public function get_id() {
		$post_id            = get_the_ID();
		$id                 = get_post_meta( $post_id, '_xts_single_product_size_guide', true );
		$disable_size_guide = get_post_meta( $post_id, '_xts_single_product_disable_size_guide', true );

		if ( ! $id ) {
			$product_categories = get_the_terms( $post_id, 'product_cat' );

			if ( $product_categories ) {
				foreach ( $product_categories as $category ) {
					if ( get_term_meta( $category->term_id, '_xts_size_guide', true ) ) {
						$id = get_term_meta( $category->term_id, '_xts_size_guide', true );
					}
				}
			}
		}

		if ( $disable_size_guide ) {
			$id = '';
		}

		return $id;
	}

	/**
	 * Update product categories.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $post_id Post id.
	 */
	public function update_product_categories( $post_id ) {
		if ( ! isset( $_POST['_xts_size_guide_categories'] ) ) { // phpcs:ignore
			return;
		}

		$selected_categories = $_POST['_xts_size_guide_categories']; // phpcs:ignore
		$saved_categories    = get_post_meta( $post_id, '_xts_size_guide_categories', true );

		foreach ( $selected_categories as $category ) {
			update_term_meta( $category, '_xts_size_guide', $post_id );
		}

		foreach ( $saved_categories as $category ) {
			if ( ! in_array( $category, $selected_categories ) && get_term_meta( $category, '_xts_size_guide', true ) === $post_id ) { // phpcs:ignore
				delete_term_meta( $category, '_xts_size_guide' );
			}
		}
	}

	/**
	 * Add options.
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'single_product_size_guide',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Size guides', 'xts-theme' ),
				'description' => 'Turn on the size guide feature on the website. Read more information about this function in <a href="' . esc_url( XTS_DOCS_URL ) . 'size-guides" target="_blank">our documentation</a>.',
				'section'     => 'general_shop_section',
				'default'     => '1',
				'priority'    => 80,
			)
		);
	}

	/**
	 * Add product metaboxes.
	 *
	 * @since 1.0.0
	 */
	public function add_product_metaboxes() {
		$metaboxes = Metaboxes::get_metabox( 'xts_product_metaboxes' );

		$metaboxes->add_field(
			array(
				'id'          => 'single_product_disable_size_guide',
				'type'        => 'switcher',
				'section'     => 'general_section',
				'name'        => esc_html__( 'Hide size guide from this product', 'xts-theme' ),
				'description' => esc_html__( 'You can disable a global size guide from being displayed from this particular product.', 'xts-theme' ),
				'on-text'     => esc_html__( 'Yes', 'xts-theme' ),
				'off-text'    => esc_html__( 'No', 'xts-theme' ),
				'default'     => '0',
				'priority'    => 50,
			)
		);

		$metaboxes->add_field(
			array(
				'id'           => 'single_product_size_guide',
				'type'         => 'select',
				'section'      => 'general_section',
				'name'         => esc_html__( 'Choose size guide', 'xts-theme' ),
				'description'  => esc_html__( 'Set a special size guide from the list for this product only.', 'xts-theme' ),
				'empty_option' => true,
				'select2'      => true,
				'options'      => xts_get_size_guides_array(),
				'priority'     => 60,
			)
		);
	}

	/**
	 * Add metaboxes.
	 *
	 * @since 1.0.0
	 */
	public function add_metaboxes() {
		$metaboxes = Metaboxes::add_metabox(
			array(
				'id'         => 'xts_size_guide_metabox',
				'title'      => esc_html__( 'Size guide metabox', 'xts-theme' ),
				'post_types' => array( 'xts-size-guide' ),
			)
		);

		$metaboxes->add_section(
			array(
				'id'       => 'general',
				'name'     => esc_html__( 'General', 'xts-theme' ),
				'icon'     => 'xf-general',
				'priority' => 10,
			)
		);

		$metaboxes->add_field(
			array(
				'id'          => 'size_guide_table',
				'type'        => 'switcher',
				'section'     => 'general',
				'name'        => esc_html__( 'Size guide table', 'xts-theme' ),
				'description' => esc_html__( 'Display the table or leave only text from the description field.', 'xts-theme' ),
				'default'     => '1',
				'priority'    => 10,
			)
		);

		$metaboxes->add_field(
			array(
				'id'       => 'size_guide_table_data',
				'type'     => 'size_guide_table',
				'section'  => 'general',
				'name'     => esc_html__( 'Create/modify size guide table', 'xts-theme' ),
				'requires' => array(
					array(
						'key'     => 'size_guide_table',
						'compare' => 'equals',
						'value'   => '1',
					),
				),
				'priority' => 30,
			)
		);

		$metaboxes->add_field(
			array(
				'id'           => 'size_guide_categories',
				'type'         => 'select',
				'section'      => 'general',
				'name'         => esc_html__( 'Choose product categories', 'xts-theme' ),
				'description'  => esc_html__( 'Select product categories that you want to display this table on.', 'xts-theme' ),
				'select2'      => true,
				'multiple'     => true,
				'autocomplete' => array(
					'type'   => 'term',
					'value'  => 'product_cat',
					'search' => 'xts_get_taxonomies_by_query_autocomplete',
					'render' => 'xts_get_taxonomies_by_ids_autocomplete',
				),
				'priority'     => 20,
			)
		);
	}

	/**
	 * Custom post type.
	 *
	 * @since 1.0.0
	 */
	public function get_size_guide_post_type_args() {

		$labels = array(
			'name'               => esc_html__( 'Size guides', 'xts-theme' ),
			'singular_name'      => esc_html__( 'Size guide', 'xts-theme' ),
			'menu_name'          => esc_html__( 'Size guides', 'xts-theme' ),
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
			'label'               => esc_html__( 'Size guides', 'xts-theme' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-format-gallery',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);
	}
}
