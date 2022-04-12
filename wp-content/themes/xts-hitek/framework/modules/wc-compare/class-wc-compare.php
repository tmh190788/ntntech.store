<?php
/**
 * Сompare.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * Compare.
 *
 * @since 1.0.0
 */
class WC_Compare extends Module {
	/**
	 * Base initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ) );
		add_action( 'wp_ajax_xts_add_to_compare', array( $this, 'add_to_compare' ) );
		add_action( 'wp_ajax_nopriv_xts_add_to_compare', array( $this, 'add_to_compare' ) );
		add_action( 'woocommerce_single_product_summary', array( $this, 'add_to_compare_single_btn' ), 34 );
		add_action( 'wp_ajax_xts_remove_from_compare', array( $this, 'remove_from_compare' ) );
		add_action( 'wp_ajax_nopriv_xts_remove_from_compare', array( $this, 'remove_from_compare' ) );

		$this->define_constants();
		$this->include_files();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		if ( ! defined( 'XTS_COMPARE_DIR' ) ) {
			define( 'XTS_COMPARE_DIR', XTS_FRAMEWORK_ABSPATH . '/modules/wc-compare/' );
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
			$path = XTS_COMPARE_DIR . $file . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Add product to compare
	 *
	 * @since 1.0
	 */
	public function remove_from_compare() {
		$id = sanitize_text_field( wp_unslash( $_GET['id'] ) ); // phpcs:ignore

		if ( defined( 'ICL_SITEPRESS_VERSION' ) && function_exists( 'wpml_object_id_filter' ) ) {
			global $sitepress;
			$id = wpml_object_id_filter( $id, 'product', true, $sitepress->get_default_language() );
		}

		$cookie_name = $this->get_compare_cookie_name();

		if ( ! $this->is_product_in_compare( $id ) ) {
			$this->compare_json_response();
		}

		$products = $this->get_compared_products();

		foreach ( $products as $key => $product_id ) {
			if ( intval( $id ) === intval( $product_id ) ) {
				unset( $products[ $key ] );
			}
		}

		if ( empty( $products ) ) {
			xts_set_cookie( $cookie_name, false );
		} else {
			xts_set_cookie( $cookie_name, wp_json_encode( $products ) );
		}

		$this->compare_json_response();
	}

	/**
	 * Add product to compare button on single product
	 *
	 * @since 1.0
	 */
	public function add_to_compare_single_btn() {
		$this->add_to_compare_btn( 'xts-style-inline' );
	}

	/**
	 * Add product to compare button
	 *
	 * @since 1.0
	 *
	 * @param string $classes Extra classes.
	 */
	public function add_to_compare_btn( $classes = '' ) {
		global $product;

		if ( ! xts_get_opt( 'compare' ) ) {
			return;
		}

		xts_enqueue_js_library( 'tooltip' );
		xts_enqueue_js_script( 'tooltip' );
		xts_enqueue_js_script( 'product-compare' );

		xts_get_template(
			'add-to-compare-btn.php',
			array(
				'classes' => $classes,
				'url'     => $this->get_compare_page_url(),
				'product' => $product,
			),
			'wc-compare'
		);
	}

	/**
	 * Get compare page ID.
	 *
	 * @since 1.0
	 */
	public function get_compare_page_url() {
		$page_id = xts_get_opt( 'compare_page' );

		if ( defined( 'ICL_SITEPRESS_VERSION' ) && function_exists( 'wpml_object_id_filter' ) ) {
			$page_id = wpml_object_id_filter( $page_id, 'page', true );
		}

		return get_permalink( $page_id );
	}

	/**
	 * Compare JSON response.
	 *
	 * @since 1.0
	 */
	public function compare_json_response() {
		$count    = 0;
		$products = $this->get_compared_products();

		ob_start();

		$this->compared_products_table();

		$table_html = ob_get_clean();

		if ( is_array( $products ) ) {
			$count = count( $products );
		}

		wp_send_json(
			array(
				'count' => $count,
				'table' => $table_html,
			)
		);
	}

	/**
	 * Add product to compare
	 *
	 * @since 1.0
	 */
	public function add_to_compare() {
		$id = sanitize_text_field( wp_unslash( $_GET['id'] ) ); // phpcs:ignore

		if ( defined( 'ICL_SITEPRESS_VERSION' ) && function_exists( 'wpml_object_id_filter' ) ) {
			global $sitepress;
			$id = wpml_object_id_filter( $id, 'product', true, $sitepress->get_default_language() );
		}

		$cookie_name = $this->get_compare_cookie_name();

		if ( $this->is_product_in_compare( $id ) ) {
			$this->compare_json_response();
		}

		$products = $this->get_compared_products();

		$products[] = $id;

		xts_set_cookie( $cookie_name, wp_json_encode( $products ) );

		$this->compare_json_response();
	}

	/**
	 * Is product in compare
	 *
	 * @since 1.0
	 *
	 * @param integer $id Product id.
	 *
	 * @return boolean
	 */
	public function is_product_in_compare( $id ) {
		$products = $this->get_compared_products();

		return in_array( $id, $products, true );
	}

	/**
	 * Get compare number.
	 *
	 * @since 1.0
	 *
	 * @return integer
	 */
	public function get_compare_count() {
		$count    = 0;
		$products = $this->get_compared_products();

		if ( is_array( $products ) ) {
			$count = count( $products );
		}

		return $count;
	}

	/**
	 * Get compare cookie name.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_compare_cookie_name() {
		$name = 'xts_compare_list';

		if ( is_multisite() ) {
			$name .= '_' . get_current_blog_id();
		}

		return $name;
	}

	/**
	 * Get compared products IDs array
	 *
	 * @since 1.0
	 *
	 * @return mixed
	 */
	public function get_compared_products() {
		$cookie_name = $this->get_compare_cookie_name();

		$cookie = xts_get_cookie( $cookie_name );

		return $cookie ? json_decode( wp_unslash( $cookie ), true ) : array(); // phpcs:ignore
	}

	/**
	 * Checks if the products have such a field.
	 *
	 * @since 3.4
	 *
	 * @param integer $field_id Field id.
	 * @param array   $products Product array.
	 *
	 * @return boolean
	 */
	public function is_products_have_field( $field_id, $products ) {
		foreach ( $products as $product ) {
			if ( isset( $product[ $field_id ] ) && ( ! empty( $product[ $field_id ] ) && '-' !== $product[ $field_id ] && 'N/A' !== $product[ $field_id ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get compared products data table HTML
	 *
	 * @since 1.0
	 */
	public function compared_products_table() {
		$products           = $this->get_compared_products_data();
		$fields             = $this->get_compare_fields();
		$empty_compare_text = xts_get_opt( 'compare_empty_text' );

		xts_enqueue_js_script( 'product-compare' );

		xts_get_template(
			'table.php',
			array(
				'products'           => $products,
				'fields'             => $fields,
				'empty_compare_text' => $empty_compare_text,
				'compare'            => $this,
			),
			'wc-compare'
		);
	}

	/**
	 * Get compare fields data.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_compare_fields() {
		$fields = array(
			'base' => esc_html__( 'Base', 'xts-theme' ),
		);

		$fields_settings = xts_get_opt( 'compare_fields' );

		if ( count( $fields_settings ) > 1 ) {
			$available_fields = $this->get_available_fields();

			foreach ( $fields_settings as $field ) {
				if ( isset( $available_fields[ $field ] ) ) {
					$fields[ $field ] = $available_fields[ $field ]['name'];
				}
			}
		}

		return $fields;
	}

	/**
	 * Get compare fields data.
	 *
	 * @since 1.0
	 *
	 * @param integer $field_id Field id.
	 * @param array   $product  Product array.
	 */
	public function compare_display_field( $field_id, $product ) {
		$type = $field_id;

		if ( 'pa_' === substr( $field_id, 0, 3 ) ) {
			$type = 'attribute';
		}

		switch ( $type ) {
			case 'base':
				xts_get_template(
					'field-base.php',
					array(
						'product' => $product,
					),
					'wc-compare'
				);
				break;

			case 'attribute':
				if ( xts_get_opt( 'brands_attribute' ) === $field_id ) {
					$brands = wc_get_product_terms( $product['id'], $field_id, array( 'fields' => 'all' ) );

					if ( empty( $brands ) ) {
						echo '-';

						return;
					}

					foreach ( $brands as $brand ) {
						$image = get_term_meta( $brand->term_id, 'image', true );

						if ( ! empty( $image ) ) {
							echo '<img src="' . esc_url( $image ) . '" title="' . esc_attr( $brand->slug ) . '" alt="' . esc_attr( $brand->slug ) . '" />';
						} else {
							echo apply_filters( 'xts_attribute_compare_field', $product[ $field_id ] );
						}
					}
				} else {
					echo apply_filters( 'xts_attribute_compare_field', $product[ $field_id ] );
				}
				break;

			case 'weight':
				if ( $product[ $field_id ] ) {
					$unit = '-' !== $product[ $field_id ] ? get_option( 'woocommerce_weight_unit' ) : '';
					echo wc_format_localized_decimal( $product[ $field_id ] ) . ' ' . esc_attr( $unit );
				}
				break;

			case 'description':
				echo apply_filters( 'woocommerce_short_description', $product[ $field_id ] );
				break;

			default:
				echo apply_filters( 'xts_default_compare_field', $product[ $field_id ] );
				break;
		}
	}

	/**
	 * Get compared products data
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_compared_products_data() {
		$ids = $this->get_compared_products();

		if ( ! $ids ) {
			return array();
		}

		$products_data = array();
		$divider       = '-';
		$fields        = $this->get_compare_fields();
		$products      = wc_get_products(
			array(
				'include' => $ids,
				'limit'   => 100,
			)
		);
		$fields        = array_filter(
			$fields,
			function ( $field ) {
				return 'pa_' === substr( $field, 0, 3 );
			},
			ARRAY_FILTER_USE_KEY
		);

		foreach ( $products as $product ) {
			$rating_count = $product->get_rating_count();
			$average      = $product->get_average_rating();

			$products_data[ $product->get_id() ] = array(
				'base'         => array(
					'title'       => $product->get_title() ? $product->get_title() : $divider,
					'image'       => $product->get_image() ? $product->get_image() : $divider,
					'rating'      => wc_get_rating_html( $average, $rating_count ),
					'price'       => $product->get_price_html() ? $product->get_price_html() : $divider,
					'add_to_cart' => $this->add_to_cart_html( $product ) ? $this->add_to_cart_html( $product ) : $divider,
				),
				'id'           => $product->get_id(),
				'image_id'     => $product->get_image_id(),
				'permalink'    => $product->get_permalink(),
				'dimensions'   => wc_format_dimensions( $product->get_dimensions( false ) ),
				'description'  => $product->get_short_description() ? $product->get_short_description() : $divider,
				'weight'       => $product->get_weight() ? $product->get_weight() : $divider,
				'sku'          => $product->get_sku() ? $product->get_sku() : $divider,
				'availability' => $this->get_availability_html( $product ),
			);

			foreach ( $fields as $field_id => $field_name ) {
				if ( taxonomy_exists( $field_id ) ) {
					$products_data[ $product->get_id() ][ $field_id ] = array();
					$terms = get_the_terms( $product->get_id(), $field_id );

					if ( ! empty( $terms ) ) {
						foreach ( $terms as $term ) {
							$term = sanitize_term( $term, $field_id );
							$products_data[ $product->get_id() ][ $field_id ][] = $term->name;
						}
					} else {
						$products_data[ $product->get_id() ][ $field_id ][] = '-';
					}

					$products_data[ $product->get_id() ][ $field_id ] = implode( ', ', $products_data[ $product->get_id() ][ $field_id ] );
				}
			}
		}

		return $products_data;
	}

	/**
	 * Get product availability html.
	 *
	 * @since 1.0
	 *
	 * @param object $product Product object.
	 *
	 * @return string
	 */
	public function get_availability_html( $product ) {
		$html         = '';
		$availability = $product->get_availability();

		if ( empty( $availability['availability'] ) ) {
			$availability['availability'] = esc_html__( 'In stock', 'xts-theme' );
		}

		if ( ! empty( $availability['availability'] ) ) {
			ob_start();

			wc_get_template(
				'single-product/stock.php',
				array(
					'product'      => $product,
					'class'        => $availability['class'],
					'availability' => $availability['availability'],
				)
			);

			$html = ob_get_clean();
		}

		return apply_filters( 'woocommerce_get_stock_html', $html, $product );
	}

	/**
	 * Get product add to cart html.
	 *
	 * @since 1.0
	 *
	 * @param object $product Product object.
	 *
	 * @return string
	 */
	public function add_to_cart_html( $product ) {
		if ( ! $product ) {
			return false;
		}

		$defaults = array(
			'quantity'   => 1,
			'class'      => implode(
				' ',
				array_filter(
					array(
						'button',
						'product_type_' . $product->get_type(),
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
					)
				)
			),
			'attributes' => array(
				'data-product_id'  => $product->get_id(),
				'data-product_sku' => $product->get_sku(),
				'aria-label'       => $product->add_to_cart_description(),
				'rel'              => 'nofollow',
			),
		);

		$args = apply_filters( 'woocommerce_loop_add_to_cart_args', $defaults, $product );

		if ( isset( $args['attributes']['aria-label'] ) ) {
			$args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
		}

		return apply_filters( 'woocommerce_loop_add_to_cart_link', sprintf( '<a href="%s" data-quantity="%s" class="%s add-to-cart-loop" %s><span>%s</span></a>', esc_url( $product->add_to_cart_url() ), esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ), esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ), isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '', esc_html( $product->add_to_cart_text() ) ), $product, $args );
	}

	/**
	 * WooCommerce compare page shortcode.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function shortcode() {
		ob_start();

		$this->compared_products_table();

		return ob_get_clean();
	}

	/**
	 * All available fields for Theme Settings sorter option.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_available_fields() {
		$product_attributes = array();

		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
			$product_attributes = wc_get_attribute_taxonomies();
		}

		$fields = array(
			'description'  => array(
				'name'  => esc_html__( 'Description', 'xts-theme' ),
				'value' => 'description',
			),
			'sku'          => array(
				'name'  => esc_html__( 'Sku', 'xts-theme' ),
				'value' => 'sku',
			),
			'availability' => array(
				'name'  => esc_html__( 'Availability', 'xts-theme' ),
				'value' => 'availability',
			),
			'weight'       => array(
				'name'  => esc_html__( 'Weight', 'xts-theme' ),
				'value' => 'weight',
			),
			'dimensions'   => array(
				'name'  => esc_html__( 'Dimensions', 'xts-theme' ),
				'value' => 'dimensions',
			),
		);

		if ( count( $product_attributes ) > 0 ) {
			foreach ( $product_attributes as $attribute ) {
				$fields[ 'pa_' . $attribute->attribute_name ] = array(
					'name'  => ucfirst( wc_attribute_label( $attribute->attribute_label ) ),
					'value' => 'pa_' . $attribute->attribute_name,
				);
			}
		}

		return $fields;
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'compare_section',
				'name'     => esc_html__( 'Compare', 'xts-theme' ),
				'parent'   => 'shop_section',
				'priority' => 60,
				'icon'     => 'xf-shop',
			)
		);

		Options::add_field(
			array(
				'id'          => 'compare',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Compare', 'xts-theme' ),
				'description' => 'Enable compare functionality built in with the theme. Read more information in our <a href="' . esc_url( XTS_DOCS_URL ) . 'compare" target="_blank">documentation.</a>',
				'section'     => 'compare_section',
				'default'     => '1',
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'           => 'compare_page',
				'type'         => 'select',
				'name'         => esc_html__( 'Compare page', 'xts-theme' ),
				'description'  => esc_html__( 'Select a page for compare table. It should contain a special "Compare" element added with Elementor.', 'xts-theme' ),
				'section'      => 'compare_section',
				'empty_option' => true,
				'select2'      => true,
				'options'      => xts_get_pages_array(),
				'priority'     => 11,
			)
		);

		Options::add_field(
			array(
				'id'          => 'product_loop_compare',
				'type'        => 'switcher',
				'name'        => esc_html__( 'Show button on products archive', 'xts-theme' ),
				'description' => esc_html__( 'Display compare product button on all products grids and lists.', 'xts-theme' ),
				'section'     => 'compare_section',
				'default'     => '1',
				'priority'    => 20,
			)
		);

		Options::add_field(
			array(
				'id'          => 'compare_fields',
				'type'        => 'select',
				'name'        => esc_html__( 'Select fields for compare table', 'xts-theme' ),
				'description' => esc_html__( 'Choose which fields should be presented on the product compare page with table.', 'xts-theme' ),
				'section'     => 'compare_section',
				'multiple'    => true,
				'select2'     => true,
				'options'     => $this->get_available_fields(),
				'default'     => array(
					'description',
					'sku',
					'availability',
				),
				'priority'    => 30,
			)
		);

		Options::add_field(
			array(
				'id'          => 'compare_empty_text',
				'type'        => 'textarea',
				'name'        => esc_html__( 'Empty compare text', 'xts-theme' ),
				'description' => esc_html__( 'Text will be displayed if user don\'t add any products to compare.', 'xts-theme' ),
				'section'     => 'compare_section',
				'wysiwyg'     => false,
				'default'     => 'No products added to the compare list. You must add some products to compare them.<br> You will find a lot of interesting products on our "Shop" page.',
				'priority'    => 40,
			)
		);
	}
}
