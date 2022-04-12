<?php
/**
 * Variations swatches class.
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
 * Variations swatches class.xts-compare-remove
 *
 * @since 1.0.0
 */
class WC_Variations_Swatches extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_product_metaboxes' ) );

		add_action( 'init', array( $this, 'add_attribute_metaboxes' ) );

		add_action( 'init', array( $this, 'add_options' ) );

		add_action( 'init', array( $this, 'hooks' ) );

		$this->define_constants();
		$this->include_files();
	}

	/**
	 * Hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'add_product_attributes_preview' ) );

		add_action( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'variation_swatches_template' ), 10, 2 );

		add_action( 'xts_product_attributes_labels_options', array( $this, 'add_product_attribute_options' ) );

		add_action( 'woocommerce_attribute_updated', array( $this, 'product_attribute_update' ), 10, 3 );

		add_action( 'woocommerce_attribute_added', array( $this, 'product_attribute_add' ), 10, 2 );

		add_action( 'save_post', array( $this, 'clear_swatches_cache' ) );

		add_action( 'wp_ajax_xts_load_variations', array( $this, 'load_variations' ) );

		add_action( 'wp_ajax_nopriv_xts_load_variations', array( $this, 'load_variations' ) );
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		if ( ! defined( 'XTS_VARIATIONS_SWATCHES_DIR' ) ) {
			define( 'XTS_VARIATIONS_SWATCHES_DIR', XTS_FRAMEWORK_ABSPATH . '/modules/wc-variations-swatches/' );
		}
	}

	/**
	 * Include main files.
	 *
	 * @since 1.0.0
	 */
	private function include_files() {
		$files = array(
			'functions',
		);

		foreach ( $files as $file ) {
			$path = XTS_VARIATIONS_SWATCHES_DIR . $file . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Attribute update.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $attribute_id       Added attribute ID.
	 * @param array   $attribute          Attribute data.
	 * @param string  $old_attribute_name Attribute old name.
	 */
	public function product_attribute_update( $attribute_id, $attribute, $old_attribute_name ) {
		if ( isset( $_POST['attribute_swatch_size'] ) ) { // phpcs:ignore
			update_option( 'xts_pa_' . $attribute['attribute_name'] . '_attribute_swatch_size', sanitize_text_field( wp_unslash( $_POST['attribute_swatch_size'] ) ) ); // phpcs:ignore
		}

		if ( isset( $_POST['attribute_swatch'] ) ) { // phpcs:ignore
			update_option( 'xts_pa_' . $attribute['attribute_name'] . '_attribute_swatch', sanitize_text_field( wp_unslash( $_POST['attribute_swatch'] ) ) ); // phpcs:ignore
		} else {
			delete_option( 'xts_pa_' . $attribute['attribute_name'] . '_attribute_swatch' ); // phpcs:ignore
		}
	}

	/**
	 * Attribute add.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $attribute_id Added attribute ID.
	 * @param array   $attribute    Attribute data.
	 */
	public function product_attribute_add( $attribute_id, $attribute ) {
		if ( isset( $_POST['attribute_swatch_size'] ) ) { // phpcs:ignore
			add_option( 'xts_pa_' . $attribute['attribute_name'] . '_attribute_swatch_size', sanitize_text_field( wp_unslash( $_POST['attribute_swatch_size'] ) ) ); // phpcs:ignore
		}

		if ( isset( $_POST['attribute_swatch'] ) ) { // phpcs:ignore
			add_option( 'xts_pa_' . $attribute['attribute_name'] . '_attribute_swatch', sanitize_text_field( wp_unslash( $_POST['attribute_swatch'] ) ) ); // phpcs:ignore
		} else {
			delete_option( 'xts_pa_' . $attribute['attribute_name'] . '_attribute_swatch' ); // phpcs:ignore
		}
	}

	/**
	 * Add product attribute options
	 *
	 * @since 1.0.0
	 */
	public function add_product_attribute_options() {
		$swatch_size      = '';
		$attribute_swatch = '';

		if ( isset( $_GET['edit'] ) ) { // phpcs:ignore
			$attribute_id     = sanitize_text_field( wp_unslash( $_GET['edit'] ) ); // phpcs:ignore
			$taxonomy_ids     = wc_get_attribute_taxonomy_ids();
			$attribute_name   = array_search( $attribute_id, $taxonomy_ids, false ); // phpcs:ignore
			$swatch_size      = get_option( 'xts_pa_' . $attribute_name . '_attribute_swatch_size' );
			$attribute_swatch = get_option( 'xts_pa_' . $attribute_name . '_attribute_swatch' );
		}

		xts_get_template(
			'admin-attribute-options.php',
			array(
				'swatch_size'      => $swatch_size ? $swatch_size : 'm',
				'attribute_swatch' => $attribute_swatch ? $attribute_swatch : '',
			),
			'wc-variations-swatches'
		);
	}

	/**
	 * Add product metaboxes
	 *
	 * @since 1.0.0
	 */
	public function add_product_metaboxes() {
		$metaboxes = Metaboxes::get_metabox( 'xts_product_metaboxes' );

		$metaboxes->add_field(
			array(
				'id'           => 'swatches_attribute',
				'type'         => 'select',
				'name'         => esc_html__( 'Attribute to display on the shop page', 'xts-theme' ),
				'description'  => esc_html__( 'Select an attribute that will be used to display product swatches on the shop page.', 'xts-theme' ),
				'section'      => 'general_section',
				'empty_option' => true,
				'select2'      => true,
				'options'      => $this->get_product_attributes_array(),
				'priority'     => 70,
			)
		);
	}

	/**
	 * Add swatches metaboxes
	 *
	 * @since 1.0.0
	 */
	public function add_attribute_metaboxes() {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		foreach ( wc_get_attribute_taxonomies() as $attribute ) {
			$metaboxes = Metaboxes::get_metabox( 'xts_attributes_metabox_' . $attribute->attribute_name );

			$metaboxes->add_section(
				array(
					'id'       => 'general',
					'name'     => esc_html__( 'General', 'xts-theme' ),
					'priority' => 10,
					'icon'     => 'xf-general',
				)
			);

			$metaboxes->add_field(
				array(
					'name'        => esc_html__( 'Color', 'xts-theme' ),
					'description' => esc_html__( 'Set color for this term if swatches are enabled for this attribute.', 'xts-theme' ),
					'id'          => 'attribute_color',
					'type'        => 'color',
					'section'     => 'general',
					'default'     => '',
					'priority'    => 20,
				)
			);

			$metaboxes->add_field(
				array(
					'name'        => esc_html__( 'Image', 'xts-theme' ),
					'description' => esc_html__( 'Upload an image for this term if swatches are enabled for this attribute.', 'xts-theme' ),
					'id'          => 'attribute_image',
					'type'        => 'upload',
					'section'     => 'general',
					'default'     => '',
					'priority'    => 30,
				)
			);
		}
	}

	/**
	 * Add swatches options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		/**
		 * Swatches
		 */
		Options::add_section(
			array(
				'id'       => 'swatches_section',
				'name'     => esc_html__( 'Swatches', 'xts-theme' ),
				'priority' => 20,
				'parent'   => 'shop_section',
				'icon'     => 'xf-shop',
			)
		);

		Options::add_field(
			array(
				'id'          => 'variations_on_shop',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Show variations on the shop page', 'xts-theme' ),
				'description' => esc_html__( 'Display variations form on the shop page to allow customers to purchase variable products directly from the shop page.', 'xts-theme' ),
				'section'     => 'swatches_section',
				'default'     => '1',
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'           => 'grid_swatches_attribute',
				'type'         => 'select',
				'name'         => esc_html__( 'Attribute to display on the shop page', 'xts-theme' ),
				'description'  => esc_html__( 'Select an attribute that will be used to display product swatches on the shop page.', 'xts-theme' ),
				'section'      => 'swatches_section',
				'empty_option' => true,
				'select2'      => true,
				'options'      => $this->get_product_attributes_array(),
				'priority'     => 20,
				'requires'     => array(
					array(
						'key'     => 'variations_on_shop',
						'compare' => 'equals',
						'value'   => '0',
					),
				),
			)
		);

		Options::add_field(
			array(
				'id'           => 'swatches_use_variation_images',
				'type'         => 'select',
				'name'         => esc_html__( 'Use images from product variations', 'xts-theme' ),
				'description'  => esc_html__( 'If enabled swatches buttons will be filled with images choosed for product variations and not with images uploaded to attribute terms.', 'xts-theme' ),
				'multiple'     => true,
				'select2'      => true,
				'empty_option' => true,
				'options'      => $this->get_product_attributes_array(),
				'section'      => 'swatches_section',
				'priority'     => 30,
			)
		);
	}

	/**
	 * Get attribute taxonomies
	 *
	 * @since 1.0.0
	 */
	public function get_product_attributes_array() {
		$attributes = array();

		if ( ! xts_is_woocommerce_installed() ) {
			return $attributes;
		}

		foreach ( wc_get_attribute_taxonomies() as $attribute ) {
			$attributes[ 'pa_' . $attribute->attribute_name ] = array(
				'name'  => $attribute->attribute_label,
				'value' => 'pa_' . $attribute->attribute_name,
			);
		}

		return $attributes;
	}

	/**
	 * Add product attributes preview
	 *
	 * @since 1.0.0
	 */
	public function add_product_attributes_preview() {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		foreach ( wc_get_attribute_taxonomies() as $attribute ) {
			add_filter( 'manage_edit-pa_' . $attribute->attribute_name . '_columns', array( $this, 'add_product_attributes_preview_column' ) );
			add_filter( 'manage_pa_' . $attribute->attribute_name . '_custom_column', array( $this, 'add_product_attributes_preview_column_content' ), 10, 3 );
		}
	}

	/**
	 * Add product attributes preview column
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_product_attributes_preview_column() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'name'        => esc_html__( 'Name', 'xts-theme' ),
			'preview'     => esc_html__( 'Preview', 'xts-theme' ),
			'description' => esc_html__( 'Description', 'xts-theme' ),
			'slug'        => esc_html__( 'Slug', 'xts-theme' ),
			'posts'       => esc_html__( 'Count', 'xts-theme' ),
		);
	}

	/**
	 * Add product attributes preview column content
	 *
	 * @since 1.0.0
	 *
	 * @param string  $content     Content.
	 * @param string  $column_name Column name.
	 * @param integer $term_id     Term id.
	 */
	public function add_product_attributes_preview_column_content( $content, $column_name, $term_id ) {
		if ( 'preview' === $column_name ) {
			$color = get_term_meta( $term_id, '_xts_attribute_color', true );
			$image = get_term_meta( $term_id, '_xts_attribute_image', true );

			xts_get_template(
				'attributes-preview-column.php',
				array(
					'color' => $color,
					'image' => $image,
				),
				'wc-variations-swatches'
			);
		}
	}

	/**
	 * Get variations swatches
	 *
	 * @since 1.0.0
	 *
	 * @param integer $product_id                    Product id.
	 * @param string  $attribute_name                Attribute name.
	 * @param array   $options                       Options.
	 * @param array   $available_variations          Available variations.
	 * @param array   $swatches_use_variation_images Swatches use variation images.
	 *
	 * @return array
	 */
	public function get_variations_swatches( $product_id, $attribute_name, $options, $available_variations, $swatches_use_variation_images = array() ) {
		$swatches = array();

		foreach ( $options as $key => $value ) {
			$swatch = $this->get_variation_swatch( $product_id, $attribute_name, $value );

			if ( $available_variations && is_array( $swatches_use_variation_images ) && in_array( $attribute_name, $swatches_use_variation_images ) ) { // phpcs:ignore
				$variation = $this->get_option_variations( $attribute_name, $available_variations, $value );

				$swatch = array_merge( $swatch, $variation );
			}

			if ( $swatch ) {
				$swatches[ $key ] = $swatch;
			}
		}

		return $swatches;
	}

	/**
	 * Get variations swatches
	 *
	 * @since 1.0.0
	 *
	 * @param string  $attribute_name       Attribute name.
	 * @param array   $available_variations Available variations.
	 * @param boolean $option               Option value.
	 * @param bool    $product_id           Product id.
	 *
	 * @return array
	 */
	public function get_option_variations( $attribute_name, $available_variations, $option = false, $product_id = false ) {
		$swatches_to_show = array();
		$product_image_id = get_post_thumbnail_id( $product_id );

		foreach ( $available_variations as $key => $variation ) {
			$option_variation = array();
			$attr_key         = 'attribute_' . $attribute_name;

			if ( ! isset( $variation['attributes'][ $attr_key ] ) ) {
				return $swatches_to_show;
			}

			$value = $variation['attributes'][ $attr_key ]; // red green black ..

			if ( $variation['image']['src'] ) {
				$option_variation = array(
					'variation_id' => $variation['variation_id'],
					'is_in_stock'  => $variation['is_in_stock'],
				);

				if ( $variation['image_id'] !== $product_image_id ) {
					$option_variation['image_src']    = $variation['image']['src'];
					$option_variation['image_srcset'] = $variation['image']['srcset'];
					$option_variation['image_sizes']  = $variation['image']['sizes'];
				}
			}

			// Get only one variation by attribute option value.
			if ( $option ) {
				if ( $value !== $option ) {
					continue;
				} else {
					return $option_variation;
				}
			} else {
				// Or get all variations with swatches to show by attribute name.
				$swatch                     = $this->get_variation_swatch( $product_id, $attribute_name, $value );
				$swatches_to_show[ $value ] = array_merge( $swatch, $option_variation );
			}
		}

		return $swatches_to_show;
	}

	/**
	 * Get variations swatch
	 *
	 * @since 1.0.0
	 *
	 * @param integer $product_id     Product id.
	 * @param string  $attribute_name Attribute name.
	 * @param string  $value          Option value.
	 *
	 * @return array
	 */
	public function get_variation_swatch( $product_id, $attribute_name, $value ) {
		$swatches         = array();
		$color            = '';
		$image            = '';
		$attribute_swatch = '';

		$term = get_term_by( 'slug', $value, $attribute_name );

		if ( is_object( $term ) ) {
			$color            = get_term_meta( $term->term_id, '_xts_attribute_color', true );
			$image            = get_term_meta( $term->term_id, '_xts_attribute_image', true );
			$attribute_swatch = get_option( 'xts_pa_' . $attribute_name . '_attribute_swatch' );
		}

		if ( isset( $color['idle'] ) && $color['idle'] ) {
			$swatches['color'] = $color;
		}

		if ( isset( $image['id'] ) && $image['id'] ) {
			$swatches['image'] = $image;
		}

		if ( 'on' === $attribute_swatch ) {
			$swatches['attribute_swatch'] = $attribute_swatch;
		}

		return $swatches;
	}

	/**
	 * Get active variations
	 *
	 * @since 1.0.0
	 *
	 * @param string $attribute_name       Attribute name.
	 * @param array  $available_variations Available variations.
	 *
	 * @return array
	 */
	public function get_active_variations( $attribute_name, $available_variations ) {
		$results = array();

		if ( ! $available_variations ) {
			return $results;
		}

		foreach ( $available_variations as $variation ) {
			$attr_key = 'attribute_' . $attribute_name;

			if ( isset( $variation['attributes'][ $attr_key ] ) ) {
				$results[] = $variation['attributes'][ $attr_key ];
			}
		}

		return $results;
	}

	/**
	 * Get available variations
	 *
	 * @since 1.0.0
	 *
	 * @param object $product Product object.
	 *
	 * @return array
	 */
	public function get_available_variations( $product ) {
		$available_variations = array();

		foreach ( $product->get_children() as $child_id ) {
			$available_variations[] = $product->get_available_variation( wc_get_product( $child_id ) );
		}

		return array_values( array_filter( $available_variations ) );
	}

	/**
	 * Get grid swatches attribute
	 *
	 * @since 1.0.0
	 *
	 * @param integer $product_id Product id.
	 *
	 * @return mixed
	 */
	public function get_grid_swatches_attribute( $product_id ) {
		$custom = get_post_meta( $product_id, '_xts_swatches_attribute', true );

		return $custom ? $custom : xts_get_opt( 'grid_swatches_attribute' );
	}

	/**
	 * Grid swatches template
	 *
	 * @since 1.0.0
	 */
	public function grid_swatches_template() {
		global $product;

		if ( xts_get_opt( 'variations_on_shop' ) ) {
			return;
		}

		$product_id                    = $product->get_id();
		$swatches_use_variation_images = xts_get_opt( 'swatches_use_variation_images' );
		$attribute_name                = $this->get_grid_swatches_attribute( $product_id );

		if ( ! $product_id || ! $product->is_type( 'variable' ) ) {
			return;
		}

		if ( ! $attribute_name ) {
			return;
		}

		// Swatches cache.
		$cache          = apply_filters( 'xts_swatches_cache', false );
		$transient_name = 'xts_swatches_cache_' . $product_id;

		if ( $cache ) {
			$available_variations = get_transient( $transient_name );
		} else {
			$available_variations = array();
		}

		if ( ! $available_variations ) {
			$available_variations = $this->get_available_variations( $product );

			if ( $cache ) {
				set_transient( $transient_name, $available_variations, apply_filters( 'xts_swatches_cache_time', WEEK_IN_SECONDS ) );
			}
		}

		if ( ! $available_variations ) {
			return;
		}

		$swatches_to_show = $this->get_option_variations( $attribute_name, $available_variations, false, $product_id );
		$swatch_size      = get_option( 'xts_' . $attribute_name . '_attribute_swatch_size' );

		if ( ! $swatches_to_show ) {
			return;
		}

		if ( apply_filters( 'xts_grid_swatches_right_order', true ) ) {
			$terms                = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'slugs' ) );
			$swatches_to_show_tmp = $swatches_to_show;
			$swatches_to_show     = array();

			foreach ( $terms as $id => $slug ) {
				// Fixed php notice.
				if ( ! isset( $swatches_to_show_tmp[ $slug ] ) ) {
					continue;
				}

				$swatches_to_show[ $slug ] = $swatches_to_show_tmp[ $slug ];
			}
		}

		xts_enqueue_js_script( 'grid-swatches' );

		xts_get_template(
			'loop-swatches.php',
			array(
				'swatches_to_show'              => $swatches_to_show,
				'swatch_size'                   => $swatch_size ? $swatch_size : 'm',
				'attribute_name'                => $attribute_name,
				'swatches_use_variation_images' => $swatches_use_variation_images,
			),
			'wc-variations-swatches'
		);
	}

	/**
	 * Show variations form on the shop page.
	 *
	 * @since 1.0.0
	 */
	public function grid_variations_template() {
		global $product;

		if ( ! $product->is_type( 'variable' ) || ! xts_get_opt( 'variations_on_shop' ) ) {
			return;
		}

		$count_variations = count( $product->get_children() );

		$attributes = $product->get_variation_attributes();

		if ( count( $attributes ) === 1 ) {
			$get_variations = $count_variations <= apply_filters( 'xts_woocommerce_ajax_variation_threshold', 10, $product );
		} else {
			$get_variations = false;
		}

		xts_enqueue_js_script( 'product-quick-shop' );
		xts_enqueue_js_script( 'variations-swatches' );
		wp_enqueue_script( 'wc-add-to-cart-variation' );

		xts_get_template(
			'loop-variations.php',
			array(
				'count_variations' => $count_variations,
				'get_variations'   => $get_variations,
				'product'          => $product,
				'attributes'       => $attributes,
			),
			'wc-variations-swatches'
		);
	}

	/**
	 * Clear swatches cache
	 *
	 * @since 1.0.0
	 *
	 * @param integer $post_id Post id.
	 */
	public function clear_swatches_cache( $post_id ) {
		if ( ! apply_filters( 'xts_swatches_cache', true ) ) {
			return;
		}

		$transient_name = 'xts_swatches_cache_' . $post_id;

		delete_transient( $transient_name );
	}

	/**
	 * Swatches template
	 *
	 * @since 1.0.0
	 *
	 * @param string $html Html.
	 * @param array  $args Arguments.
	 *
	 * @return string
	 */
	public function variation_swatches_template( $html, $args ) {
		$attribute_name                = $args['attribute'];
		$options                       = $args['options'];
		$product                       = $args['product'];
		$swatches_use_variation_images = xts_get_opt( 'swatches_use_variation_images' );
		$attribute_swatch              = get_option( 'xts_' . $attribute_name . '_attribute_swatch' );

		if ( 'on' !== $attribute_swatch ) {
			return $html;
		}

		$available_variations = $this->get_available_variations( $product );
		$swatches             = $this->get_variations_swatches( $product->get_id(), $attribute_name, $options, $available_variations, $swatches_use_variation_images );

		xts_enqueue_js_script( 'variations-swatches' );

		xts_get_template(
			'single-swatches.php',
			array(
				'attribute_name'                => $attribute_name,
				'product'                       => $product,
				'options'                       => $options,
				'selected_attributes'           => $product->get_default_attributes(),
				'swatches_use_variation_images' => $swatches_use_variation_images,
				'swatches'                      => $swatches,
				'active_variations'             => $this->get_active_variations( $attribute_name, $available_variations ),
				'html'                          => $html,
			),
			'wc-variations-swatches'
		);
	}

	/**
	 * Load variations AJAX action.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_variations() {
		if ( empty( $_GET['id'] ) ) { // phpcs:ignore
			wp_die();
		}

		$product = wc_get_product( absint( $_GET['id'] ) ); // phpcs:ignore

		if ( ! $product ) {
			wp_die();
		}

		// Get Available variations.
		$variations = $product->get_available_variations();

		wp_send_json( $variations );

		die();
	}
}
