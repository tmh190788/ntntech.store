<?php
/**
 * WC product page builder.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use Elementor\Plugin;
use XTS\Framework\Options;

/**
 * WC product page builder.
 *
 * @since 1.0.0
 */
class WC_Builder extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		add_action( 'init', array( $this, 'add_options' ), 10 );

		add_filter( 'wc_get_template_part', array( $this, 'override_template' ), 50, 3 );

		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 40 );

		// On Editor - Register WooCommerce frontend hooks before the Editor init.
		// Priority = 5, in order to xts-single-product-style allow plugins remove/add their wc hooks on init.
		if ( ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && is_admin() ) { // phpcs:ignore
			add_action( 'init', array( $this, 'register_wc_hooks' ), 5 );
		}

		add_action( 'init', array( $this, 'remove_wc_hooks' ), 5000 );
	}

	/**
	 * Register WC hooks when editing with elementor.
	 *
	 * @since 1.0.0
	 */
	public function register_wc_hooks() {
		wc()->frontend_includes();
	}

	/**
	 * Remove hooks when custom template is set.
	 *
	 * @since 1.0.0
	 */
	public function remove_wc_hooks() {
		if ( $this->has_custom_template() ) {
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
			remove_action( 'xts_before_single_product_main_gallery', 'woocommerce_show_product_sale_flash', 10 );
		}
	}

	/**
	 * Load scripts.
	 *
	 * @since 1.0.0
	 */
	public function load_scripts() {
		// Load gallery scripts on product pages only if supported.
		if ( is_singular( 'xts-template' ) ) {
			wp_enqueue_script( 'zoom' );
			wp_enqueue_script( 'wc-single-product' );
		}
	}

	/**
	 * Register post type Product templates.
	 *
	 * @return array Arguments for registering a post type.
	 */
	public function get_template_post_type_args() {
		$labels = array(
			'name'               => esc_html__( 'Product templates', 'xts-theme' ),
			'singular_name'      => esc_html__( 'Template', 'xts-theme' ),
			'menu_name'          => esc_html__( 'Product templates', 'xts-theme' ),
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
			'description'         => esc_html__( 'Templates for custom product and shop pages.', 'xts-theme' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'has_archive'         => false,
			'public'              => true,
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
		);
	}

	/**
	 * Override template for the product page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Template.
	 * @param string $slug     Slug.
	 * @param string $name     Name.
	 *
	 * @return bool|string
	 */
	public function override_template( $template, $slug, $name ) {
		if ( ! $this->has_custom_template() ) {
			return $template;
		}

		if ( 'content' === $slug && 'single-product' === $name ) {
			$this->display_template();
			return false;
		}

		return $template;
	}

	/**
	 * Check if the custom template is set.
	 *
	 * @since 1.0.0
	 */
	public function has_custom_template() {
		$id = $this->get_template_id();
		return ! empty( $id );
	}

	/**
	 * Display custom template on the product page.
	 *
	 * @since 1.0.0
	 */
	public function display_template() {
		$id   = $this->get_template_id();
		$post = get_post( $id );

		if ( ! $post || 'xts-template' !== $post->post_type ) {
			return;
		}

		if ( xts_is_elementor_installed() && Plugin::$instance->db->is_built_with_elementor( $id ) ) {
			$content = xts_elementor_get_content( $id );
		} else {
			$content = do_shortcode( $post->post_content );
		}

		xts_get_template(
			'product-page.php',
			array(
				'content' => $content,
			),
			'wc-builder'
		);
	}

	/**
	 * Get custom template ID.
	 *
	 * @since 1.0.0
	 */
	public function get_template_id() {
		$id = xts_get_opt( 'single_product_custom_template' );

		if ( is_singular( 'product' ) ) {
			$post_id            = get_the_ID();
			$meta_template_id   = get_post_meta( $post_id, '_xts_single_product_custom_template', true );
			$product_categories = get_the_terms( $post_id, 'product_cat' );
			if ( isset( $product_categories[0] ) && is_object( $product_categories[0] ) ) {
				$product_categories_meta_template_id = get_term_meta( $product_categories[0]->term_id, '_xts_product_categories_product_custom_template', true );
			}

			if ( isset( $product_categories_meta_template_id ) && $product_categories_meta_template_id ) {
				$id = $product_categories_meta_template_id;
			}

			if ( $meta_template_id ) {
				$id = $meta_template_id;
			}
		}

		return $id;
	}

	/**
	 * Add theme settings options.
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'single_product_builder_section',
				'name'     => esc_html__( 'Product builder', 'xts-theme' ),
				'priority' => 50,
				'parent'   => 'single_product_section',
				'icon'     => 'xf-single-product',
			)
		);

		Options::add_field(
			array(
				'id'           => 'single_product_custom_template',
				'name'         => esc_html__( 'Custom template for all products', 'xts-theme' ),
				'description'  => esc_html__( 'You can build custom template for products with Elementor.', 'xts-theme' ),
				'type'         => 'select',
				'section'      => 'single_product_builder_section',
				'empty_option' => true,
				'select2'      => true,
				'options'      => xts_get_product_templates_array(),
				'priority'     => 10,
			)
		);

		Options::add_field(
			array(
				'id'           => 'single_product_custom_template_preview_product',
				'type'         => 'select',
				'section'      => 'single_product_builder_section',
				'name'         => esc_html__( 'Select preview product to show for example in your template', 'xts-theme' ),
				'description'  => esc_html__( 'The information from this product will be used as an example while you are working with the product template and Elementor.', 'xts-theme' ),
				'select2'      => true,
				'empty_option' => true,
				'autocomplete' => array(
					'type'   => 'post_type',
					'value'  => 'product',
					'search' => 'xts_get_posts_by_query_autocomplete',
					'render' => 'xts_get_posts_by_ids_autocomplete',
				),
				'priority'     => 20,
			)
		);
	}
}
