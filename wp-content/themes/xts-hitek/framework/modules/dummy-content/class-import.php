<?php
/**
 * Import dummy content interface.
 *
 * @package xts
 */

namespace XTS\Modules\Dummy_Content;

use Elementor\Plugin;
use Elementor\Utils;
use Exception;
use RevSlider;
use RevSliderFront;
use RevSliderSlider;
use WC_Install;
use WP_Privacy_Policy_Content;
use XTS\Framework\AJAX_Response;
use XTS\Framework\Options;
use XTS\Framework\Modules;
use XTS\Framework\Plugin_Activation;
use XTS\Elementor\Library_Source;
use XTS_Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Import dummy content interface class.
 *
 * @since 1.0.0
 */
class Import {
	/**
	 * Current version to import.
	 *
	 * @var string
	 */
	private $_version; // phpcs:ignore
	/**
	 * WordPress importer class.
	 *
	 * @var object
	 */
	private $_importer; // phpcs:ignore
	/**
	 * Options set prefix.
	 *
	 * @var array
	 */
	public static $opt_name = XTS_THEME_SLUG;

	/**
	 * Initial setup for actions and hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_xts_dummy_content', array( $this, 'import' ) );
		add_action( 'wp_ajax_xts_clear_dummy_content', array( $this, 'clear' ) );
		$this->_load_importers();
	}

	/**
	 * Allow import svg.
	 *
	 * @since 1.0.0
	 *
	 * @param array $mimes Mime types.
	 *
	 * @return array
	 */
	public function allow_import_svg( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';

		return $mimes;
	}

	/**
	 * Import the dummy content AJAX action callback.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Exception.
	 */
	public function import() {
		add_action( 'upload_mimes', array( $this, 'allow_import_svg' ) );

		$plugins          = Plugin_Activation::get_instance();
		$required_plugins = $plugins->get_required_plugins_to_activate();

		if ( $required_plugins ) {
			AJAX_Response::send_fail_msg( 'Not all required plugins are activated.' );
		}

		$this->_version = 'base';

		if ( isset( $_GET['version'] ) && 'base' !== $_GET['version'] ) { // phpcs:ignore
			$this->_version = $_GET['version']; // phpcs:ignore

			$this->import_additional_page();

			AJAX_Response::send_response();
		}

		$this->clear( true );

		$this->before();

		$this->install_woocommerce_data();

		$this->_import_xml();

		$this->import_revslider_global();

		$this->import_elementor_global();

		$this->import_images_sizes();

		$this->import_pages_meta_from_file();

		$this->_import_rev_sliders();

		$this->_import_headers();

		$this->_set_home_page();

		$this->_set_blog_page();

		$this->_import_presets();

		$this->_import_options();

		$this->_set_up_widgets();

		$this->_enable_elementor_options();

		$this->_menu_locations();

		$this->_extra_menu_items();

		$this->replace_url();

		$this->after();

		AJAX_Response::send_response();
	}

	/**
	 * Save post id.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_id Post id.
	 */
	public function save_image_id_on_import( $post_id ) {
		$imported_data = get_option( 'xts_imported_data' );

		$imported_data['posts'][] = $post_id;

		update_option( 'xts_imported_data', $imported_data );
	}

	/**
	 * Import page.
	 *
	 * @since 1.0.0
	 */
	private function import_additional_page() {
		$source  = new Library_Source();
		$content = json_decode( $this->_get_local_file_content( $this->_get_file_to_import( 'content.json' ) ), true );

		if ( ! $content ) {
			return;
		}

		add_action( 'wp_insert_post', array( $this, 'save_image_id_on_import' ) );
		add_action( 'add_attachment', array( $this, 'save_image_id_on_import' ) );

		// Import page.
		$config = $this->get_config();

		$page = get_page_by_title( $content['page']['title'] );

		if ( ! is_null( $page ) ) {
			AJAX_Response::send_fail_msg( 'Page already exists.' );
		}

		$page_data = array(
			'post_title'  => wp_strip_all_tags( $content['page']['title'] ),
			'post_status' => 'publish',
			'post_type'   => 'page',
			'meta_input'  => array(
				'_elementor_edit_mode'     => 'builder',
				'_elementor_template_type' => 'wp-page',
				'_elementor_version'       => '3.0.13',
			),
		);

		// Import page meta.
		foreach ( $config['page_meta'] as $key => $value ) {
			$page_data['meta_input'][ $key ] = $value;
		}

		$page_id = wp_insert_post( $page_data );

		$source->get_data(
			array(
				'data'           => $content['page'],
				'editor_post_id' => $page_id,
			),
			'update'
		);

		// Import blocks.
		if ( isset( $content['html_blocks'] ) ) {
			foreach ( $content['html_blocks'] as $block ) {
				$block_data = array(
					'post_title'  => wp_strip_all_tags( $block['title'] ),
					'post_status' => 'publish',
					'post_type'   => 'xts-html-block',
					'meta_input'  => array(
						'_elementor_edit_mode'     => 'builder',
						'_elementor_template_type' => 'wp-page',
						'_elementor_version'       => '3.0.13',
					),
				);

				$block_id = wp_insert_post( $block_data );

				$source->get_data(
					array(
						'data'           => $block,
						'editor_post_id' => $block_id,
					),
					'update'
				);

				// Replace block in page.
				$page_elementor_data = wp_json_encode( get_post_meta( $page_id, '_elementor_data', true ) );

				$page_elementor_data = str_replace( $block['title'], $block_id, $page_elementor_data );

				update_post_meta( $page_id, '_elementor_data', json_decode( $page_elementor_data, true ) );
			}
		}

		AJAX_Response::add_msg( 'Additional page imported' );

		AJAX_Response::send_response(
			array(
				'status'    => 'success',
				'page_data' => array(
					'url'   => get_permalink( $page_id ),
					'title' => get_the_title( $page_id ),
				),
			)
		);
	}

	/**
	 * Replace URL.
	 *
	 * @since 1.0.0
	 */
	private function replace_url() {
		$config = $this->get_config();

		if ( ! $config['links'] ) {
			return;
		}

		$links = $config['links'];

		foreach ( $links as $key => $value ) {
			if ( 'simple' === $key ) {
				foreach ( $value as $link ) {
					try {
						Utils::replace_urls( $link, get_home_url() . '/' );
					} catch ( Exception $e ) {
						AJAX_Response::send_fail_msg( 'Error while replace link' );
					}
				}
			}

			if ( 'uploads' === $key ) {
				foreach ( $value as $link ) {
					$url_data = wp_upload_dir();
					try {
						Utils::replace_urls( $link, $url_data['baseurl'] . '/' );
					} catch ( Exception $e ) {
						AJAX_Response::send_fail_msg( 'Error while replace link' );
					}
				}
			}
		}
	}

	/**
	 * Before import.
	 *
	 * @since 1.0.0
	 */
	private function before() {
		global $wpdb;

		// Temp term for menu ids.
		if ( ! term_exists( 100 ) ) {
			$wpdb->insert( // phpcs:ignore
				$wpdb->terms,
				array(
					'term_id'    => 100,
					'name'       => 'Temp',
					'slug'       => 'temp',
					'term_group' => 0,
				)
			);
		}

		// Remove woocommerce pages before import.
		$current_theme = strtolower( xts_get_theme_info( 'Name' ) );
		$theme_list    = xts_get_config( 'theme-list' );

		if ( get_option( 'woocommerce_shop_page_id' ) && ! $theme_list[ $current_theme ]['woocommerce'] ) {
			update_option( 'xts_' . self::$opt_name . '_wc_pages_removed', 'yes' );
			wp_delete_post( get_option( 'woocommerce_shop_page_id' ), true );
			wp_delete_post( get_option( 'woocommerce_cart_page_id' ), true );
			wp_delete_post( get_option( 'woocommerce_checkout_page_id' ), true );
			wp_delete_post( get_option( 'woocommerce_myaccount_page_id' ), true );
			update_option( 'woocommerce_shop_page_id', '' );
			update_option( 'woocommerce_cart_page_id', '' );
			update_option( 'woocommerce_checkout_page_id', '' );
			update_option( 'woocommerce_myaccount_page_id', '' );
		}

		// Remove default cf7 form.
		$cf7 = get_page_by_title( 'Contact form 1', 'OBJECT', 'wpcf7_contact_form' );
		if ( $cf7 ) {
			wp_delete_post( $cf7->ID, true );
		}

		// Remove privacy policy page.
		wp_delete_post( get_option( 'wp_page_for_privacy_policy' ), true );
	}

	/**
	 * Import elementor global.
	 *
	 * @since 1.0.0
	 */
	public function import_elementor_global() {
		$config = $this->get_config();

		$elementor_global = isset( $config['elementor_global'] ) ? $config['elementor_global'] : '';

		if ( ! $elementor_global ) {
			return;
		}

		Plugin::$instance->kits_manager->get_active_kit();
		$default_post_id = get_option( 'elementor_active_kit' );
		$global_data     = get_post_meta( $default_post_id, '_elementor_page_settings', true );

		if ( ! $global_data ) {
			$global_data = array();
		}

		$global_data['container_width'] = $elementor_global;

		update_post_meta( $default_post_id, '_elementor_page_settings', $global_data );

		AJAX_Response::add_msg( 'Elementor global updated' );
	}

	/**
	 * Import revslider sizes.
	 *
	 * @since 1.0.0
	 */
	public function import_revslider_global() {
		$config = $this->get_config();

		$revslider_global = isset( $config['revslider_global'] ) ? $config['revslider_global'] : '';

		if ( ! $revslider_global ) {
			return;
		}

		$revslider_data = json_decode( get_option( 'revslider-global-settings' ), true );

		$revslider_data['size'] = $revslider_global;

		update_option( 'revslider-global-settings', wp_json_encode( $revslider_data ) );

		AJAX_Response::add_msg( 'Revslider global updated' );
	}

	/**
	 * Import images sizes.
	 *
	 * @since 1.0.0
	 */
	public function import_images_sizes() {
		$config = $this->get_config();

		if ( ! $config['images_sizes'] ) {
			return;
		}

		$sizes = $config['images_sizes'];

		foreach ( $sizes as $key => $value ) {
			update_option( $key, $value );
		}

		AJAX_Response::add_msg( 'Images sizes updated' );
	}

	/**
	 * Install woocommerce pages.
	 *
	 * @since 1.0.0
	 */
	public function install_woocommerce_data() {
		$current_theme = strtolower( xts_get_theme_info( 'Name' ) );
		$theme_list    = xts_get_config( 'theme-list' );

		if ( ! $theme_list[ $current_theme ]['woocommerce'] ) {
			return;
		}

		// Pages.
		WC_Install::create_pages();

		// Default product attributes.
		$config = $this->get_config();

		$attrs = isset( $config['product_attributes'] ) ? $config['product_attributes'] : '';

		if ( $attrs ) {
			foreach ( $attrs as $attr ) {
				wc_create_attribute(
					array(
						'name'         => $attr['name'],
						'slug'         => $attr['slug'],
						'type'         => 'select',
						'order_by'     => 'menu_order',
						'has_archives' => $attr['has_archives'],
					)
				);
				xts_taxonomy_register_on_import( $attr );

				if ( $attr['swatches'] ) {
					update_option( 'xts_pa_' . $attr['slug'] . '_attribute_swatch', 'on' );
				}
			}

			flush_rewrite_rules();
			wp_cache_flush();
			delete_transient( 'wc_attribute_taxonomies' );
		}

		AJAX_Response::add_msg( 'Woocommerce pages created.' );
	}

	/**
	 * After import.
	 *
	 * @since 1.0.0
	 */
	private function after() {
		global $wpdb;

		if ( term_exists( 100 ) ) {
			$wpdb->delete( // phpcs:ignore
				$wpdb->terms,
				array(
					'term_id' => 100,
				)
			);
		}

		$mc4wp = get_posts(
			array(
				'post_type'   => 'mc4wp-form',
				'numberposts' => 1,
			)
		);

		if ( $mc4wp ) {
			update_option( 'mc4wp_default_form_id', $mc4wp[0]->ID );
		}

		// Wc recreate pages.
		if ( 'yes' === get_option( 'xts_' . self::$opt_name . '_wc_pages_removed' ) && class_exists( 'WC_Install' ) ) {
			delete_option( 'xts_' . self::$opt_name . '_wc_pages_removed' );
			WC_Install::create_pages();
		}

		// Shop is home.
		$config = $this->get_config();

		if ( isset( $config['home_is_shop'] ) && $config['home_is_shop'] ) {
			$home_page = get_page_by_title( 'Home page' );
			if ( ! is_null( $home_page ) ) {
				update_option( 'woocommerce_shop_page_id', $home_page->ID );
			}
		}

		// Wc lookup tables.
		if ( function_exists( 'wc_update_product_lookup_tables_is_running' ) && ! wc_update_product_lookup_tables_is_running() ) {
			wc_update_product_lookup_tables();
		}

		// Clear elementor cache.
		Plugin::$instance->files_manager->clear_cache();

		// Clear rewrite.
		flush_rewrite_rules();

		// Privacy policy recreate.
		$privacy_policy = get_page_by_title( 'Privacy Policy', 'OBJECT' );
		if ( ! $privacy_policy ) {
			if ( ! class_exists( 'WP_Privacy_Policy_Content' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-privacy-policy-content.php';
			}

			$privacy_policy_page_content = WP_Privacy_Policy_Content::get_default_content();
			$privacy_policy_page_id      = wp_insert_post(
				array(
					'post_title'   => esc_html__( 'Privacy Policy', 'xts-theme' ),
					'post_status'  => 'draft',
					'post_type'    => 'page',
					'post_content' => $privacy_policy_page_content,
				),
				true
			);

			if ( ! is_wp_error( $privacy_policy_page_id ) ) {
				update_option( 'wp_page_for_privacy_policy', $privacy_policy_page_id );
			}
		}
	}

	/**
	 * Import pages meta from file.
	 *
	 * @since 1.0.0
	 */
	private function import_pages_meta_from_file() {
		$config = $this->get_config();

		$pages_meta = isset( $config['pages_meta'] ) ? $config['pages_meta'] : '';

		if ( ! $pages_meta ) {
			return;
		}

		foreach ( $pages_meta as $page_data ) {
			$page = get_page_by_title( $page_data['title'] );

			if ( is_null( $page ) ) {
				continue;
			}

			foreach ( $page_data['meta'] as $key => $value ) {
				update_post_meta( $page->ID, $key, $value );
			}
		}
	}

	/**
	 * Extra menu items.
	 *
	 * @since 1.0.0
	 */
	private function _extra_menu_items() { // phpcs:ignore
		$config = $this->get_config();

		$menu_items = isset( $config['menu_items'] ) ? $config['menu_items'] : '';

		if ( ! $menu_items ) {
			return;
		}

		foreach ( $menu_items as $item ) {
			$meta = isset( $item['meta'] ) ? $item['meta'] : array();
			$this->add_menu_item_by_title( $item['title'], $meta, $item['position'], $item['menu'] );
		}
	}

	/**
	 * Add menu item by title.
	 *
	 * @param string $title    Title.
	 * @param array  $meta     Meta array.
	 * @param false  $position Position.
	 * @param string $menu     Menu.
	 */
	public function add_menu_item_by_title( $title, $meta, $position = false, $menu = 'main-menu' ) {
		$page = get_page_by_title( $title );

		if ( is_null( $page ) ) {
			return;
		}

		$this->insert_menu_item( $title, $meta, $position, $page->ID, $menu );
	}

	/**
	 * Insets menu item.
	 *
	 * @param string $page_title Title.
	 * @param array  $meta       Meta array.
	 * @param false  $position   Position.
	 * @param false  $page_id    Page id.
	 * @param string $menu       Menu.
	 */
	public function insert_menu_item( $page_title, $meta, $position = false, $page_id = false, $menu = 'main-menu' ) {
		$main_menu = get_term_by( 'slug', $menu, 'nav_menu' );
		$args      = array(
			'menu-item-title'  => $page_title,
			'menu-item-object' => 'page',
			'menu-item-type'   => 'post_type',
			'menu-item-status' => 'publish',
		);

		if ( $position ) {
			$args['menu-item-position'] = $position;
		}

		if ( $page_id ) {
			$args['menu-item-object-id'] = $page_id;
		}

		$item_id = wp_update_nav_menu_item( $main_menu->term_id, 0, $args );

		if ( $meta ) {
			foreach ( $meta as $key => $value ) {
				update_post_meta( $item_id, $key, $value );
			}
		}
	}

	/**
	 * Enable Elementor for custom post types.
	 *
	 * @since 1.0.0
	 */
	private function _enable_elementor_options() { // phpcs:ignore
		$post_types = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );

		$post_types[] = 'product';
		$post_types[] = 'xts-portfolio';
		$post_types[] = 'xts-html-block';
		$post_types[] = 'xts-slide';
		$post_types[] = 'xts-template';

		update_option( 'elementor_cpt_support', $post_types );
		update_option( 'elementor_disable_color_schemes', 'yes' );
		update_option( 'elementor_disable_typography_schemes', 'yes' );
		update_option( 'elementor_optimized_dom_output', 'enabled' );
	}

	/**
	 * Clear previously imported dummy content.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $check Should we check or not.
	 *
	 * @throws Exception Exception.
	 */
	public function clear( $check = false ) {
		global $wpdb;

		$imported_data = get_option( 'xts_imported_data' );

		if ( ! $imported_data || ! is_array( $imported_data ) ) {
			if ( ! $check ) {
				AJAX_Response::send_fail_msg( 'There is no information about imported data in the database.' );
			}

			return;
		}

		$imported_data['posts'][] = get_option( 'woocommerce_shop_page_id' );
		$imported_data['posts'][] = get_option( 'woocommerce_cart_page_id' );
		$imported_data['posts'][] = get_option( 'woocommerce_checkout_page_id' );
		$imported_data['posts'][] = get_option( 'woocommerce_myaccount_page_id' );

		if ( isset( $imported_data['menus'] ) && ! empty( $imported_data['menus'] ) ) {
			foreach ( $imported_data['menus'] as $menu_id ) {
				wp_delete_nav_menu( $menu_id );
			}
		}

		if ( isset( $imported_data['terms'] ) && ! empty( $imported_data['terms'] ) ) {
			foreach ( $imported_data['terms'] as $term_id => $taxonomy ) {
				wp_delete_term( $term_id, $taxonomy );
			}
		}

		if ( isset( $imported_data['tags'] ) && ! empty( $imported_data['tags'] ) ) {
			foreach ( $imported_data['tags'] as $id ) {
				wp_delete_term( $id, 'post_tag' );
			}
		}

		if ( isset( $imported_data['categories'] ) && ! empty( $imported_data['categories'] ) ) {
			foreach ( $imported_data['categories'] as $id ) {
				wp_delete_term( $id, 'category' );
			}
		}

		// Revslider.
		if ( isset( $imported_data['rev_sliders'] ) && ! empty( $imported_data['rev_sliders'] ) ) {
			foreach ( $imported_data['rev_sliders'] as $slider_data ) {
				$slider_id   = $slider_data['sliderID'];
				$slides_data = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . RevSliderFront::TABLE_SLIDES . ' WHERE `slider_id` = %s', $slider_id ), ARRAY_A ); // phpcs:ignore

				$slides_data_static = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . RevSliderFront::TABLE_STATIC_SLIDES . ' WHERE `slider_id` = %s', $slider_id ), ARRAY_A ); // phpcs:ignore

				if ( $slides_data_static ) {
					$slides_data = array_merge( $slides_data, $slides_data_static );
				}

				foreach ( $slides_data as $slide_data ) {
					$layers = json_decode( $slide_data['layers'], true );
					$params = json_decode( $slide_data['params'], true );

					foreach ( $layers as $layer_data ) {
						if ( isset( $layer_data['media'] ) && isset( $layer_data['media']['imageUrl'] ) ) {
							wp_delete_post( attachment_url_to_postid( $layer_data['media']['imageUrl'] ), true );
						}

						if ( isset( $layer_data['idle'] ) && isset( $layer_data['idle']['backgroundImage'] ) ) {
							wp_delete_post( attachment_url_to_postid( $layer_data['idle']['backgroundImage'] ), true );
						}
					}

					if ( isset( $params['bg'] ) && isset( $params['bg']['image'] ) ) {
						wp_delete_post( attachment_url_to_postid( $params['bg']['image'] ), true );
					}

					if ( isset( $params['thumb'] ) && isset( $params['thumb']['customThumbSrc'] ) ) {
						wp_delete_post( attachment_url_to_postid( $params['thumb']['customThumbSrc'] ), true );
					}
				}

				$revslider = new RevSliderSlider();
				$revslider->init_by_id( $slider_id );
				$revslider->delete_slider();
			}
		}

		// Delete taxonomies.
		if ( xts_is_woocommerce_installed() ) {
			foreach ( wc_get_attribute_taxonomy_ids() as $key => $value ) {
				delete_option( 'xts_pa_' . $key . '_attribute_swatch' );
				wc_delete_attribute( $value );
			}

			flush_rewrite_rules();
			wp_cache_flush();
			delete_transient( 'wc_attribute_taxonomies' );
		}

		// Widgets.
		$sidebars = get_option( 'sidebars_widgets' );
		foreach ( $sidebars as $key => $value ) {
			if ( 'main-widget-sidebar' !== $key ) {
				unset( $sidebars[ $key ] );
			}
		}
		update_option( 'sidebars_widgets', $sidebars );

		// Posts.
		if ( isset( $imported_data['posts'] ) && ! empty( $imported_data['posts'] ) ) {
			foreach ( $imported_data['posts'] as $post_id ) {
				wp_delete_post( $post_id, true );
			}
		}

		// Theme settings.
		$options           = Options::get_instance();
		$sanitized_options = $options->sanitize_before_save( array( 'reset-defaults' => true ) );
		update_option( 'xts-theme_settings_default-status', 'invalid' );
		delete_option( 'xts-theme_settings_default-credentials' );
		$options->update_options( $sanitized_options );

		// Header.
		$imported_data['headers'][] = 'default_header_' . $imported_data['theme_name'];
		if ( isset( $imported_data['headers'] ) && ! empty( $imported_data['headers'] ) ) {
			foreach ( $imported_data['headers'] as $header_id ) {
				xts_get_header_builder()->list->remove( $header_id );
				delete_option( 'xts_' . $header_id );
			}
		}

		// Reset ID counters.
		$wpdb->query( "ALTER TABLE {$wpdb->posts} AUTO_INCREMENT = 1" ); // phpcs:ignore

		$wpdb->query( "ALTER TABLE {$wpdb->postmeta} AUTO_INCREMENT = 1" ); // phpcs:ignore

		$wpdb->query( "ALTER TABLE {$wpdb->terms} AUTO_INCREMENT = 1" ); // phpcs:ignore

		delete_option( 'xts_imported_data' );

		AJAX_Response::add_msg( 'Dummy content cleared.' );

		if ( ! $check ) {
			AJAX_Response::send_response(
				array(
					'status' => 'success',
					'action' => 'clear',
				)
			);
			die();
		}
	}

	/**
	 * Run WordPress importer for content.xml file.
	 *
	 * @since 1.0.0
	 */
	private function _import_xml() { // phpcs:ignore
		$file = $this->_get_file_to_import( 'content.xml' );

		// Check if XML file exists.
		if ( ! $file ) {
			AJAX_Response::send_fail_msg( 'File does not exist <strong>' . $this->_version . '/content.xml</strong>' );
		}

		try {
			ob_start();

			// Prevent generating of thumbnails for 8 sizes. Only original.
			add_filter( 'intermediate_image_sizes', array( $this, 'sizes_array' ) );

			$this->_importer->fetch_attachments = true;

			// Run WP Importer for XML file.
			$this->_importer->import( $file );

			$output = ob_get_contents();

			ob_end_clean();

			AJAX_Response::add_msg( $output );
		} catch ( Exception $e ) {
			AJAX_Response::send_fail_msg( 'Error while importing' );
		}
	}

	/**
	 * Import theme settings json file.
	 *
	 * @since 1.0.0
	 */
	private function _import_options() { // phpcs:ignore
		$file = $this->_get_file_to_import( 'options.json' );

		if ( ! $file ) {
			return;
		}

		$new_options_json = json_decode( $this->_get_local_file_content( $file ), true );
		$options          = Options::get_instance();
		$options_array    = $options::get_options();

		foreach ( $new_options_json as $key => $value ) {
			$options_array[ $key ] = $value;
		}

		$pseudo_post_data = array(
			'import-btn'    => true,
			'import_export' => wp_json_encode( $options_array ),
		);

		$sanitized_options = $options->sanitize_before_save( $pseudo_post_data );

		update_option( 'xts-theme_settings_default-status', 'invalid' );
		delete_option( 'xts-theme_settings_default-credentials' );

		$options->update_options( $sanitized_options );

		AJAX_Response::add_msg( 'Options updated' );
	}

	/**
	 * Import presets json file.
	 *
	 * @since 1.0.0
	 */
	private function _import_presets() { // phpcs:ignore
		$file = $this->_get_file_to_import( 'presets.json' );

		if ( ! $file ) {
			return;
		}

		update_option( 'xts-' . self::$opt_name . '-options-presets', json_decode( $this->_get_local_file_content( $file ), true ) );

		AJAX_Response::add_msg( 'Presets updated' );
	}

	/**
	 * Import Revolution Sliders zip files.
	 *
	 * @since 1.0.0
	 */
	private function _import_rev_sliders() { // phpcs:ignore
		if ( ! xts_is_revslider_installed() ) {
			return;
		}

		$sliders       = array();
		$imported_data = get_option( 'xts_imported_data' );

		for ( $i = 1; $i <= 5; $i ++ ) {
			$slider_name = 'revslider-' . $i . '.zip';
			if ( $this->_get_file_to_import( $slider_name ) ) {
				$sliders[ 'revslider-' . $i ] = $this->_revolution_import( $slider_name );
			}
		}

		$imported_data['rev_sliders'] = $sliders;

		update_option( 'xts_imported_data', $imported_data );
	}

	/**
	 * Import Revolution Sliders zip files.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filename File name.
	 *
	 * @return false|string|void
	 */
	private function _revolution_import( $filename ) { // phpcs:ignore
		$file = $this->_get_file_to_import( $filename );

		if ( ! $file ) {
			return;
		}

		$revapi = new RevSlider();

		return $revapi->importSliderFromPost( true, true, $file );
	}

	/**
	 * Import header builder json files.
	 *
	 * @since 1.0.0
	 */
	private function _import_headers() { // phpcs:ignore
		try {
			for ( $i = 1; $i <= 5; $i ++ ) {
				$file = $this->_get_file_to_import( 'header-' . $i . '.json' );
				if ( $file ) {
					$default = 1 === $i;
					$this->_create_new_header( $file, $default );
				}
			}
		} catch ( Exception $e ) {
			AJAX_Response::send_fail_msg( 'Error while importing header' );
		}

		AJAX_Response::add_msg( 'Header updated' );
	}

	/**
	 * Replace link.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data    Data.
	 * @param string $replace Replace.
	 *
	 * @return string|string[]
	 */
	private function links_replace( $data, $replace = '\/' ) {
		$config = $this->get_config();
		$links  = $config['links'];

		foreach ( $links as $key => $value ) {
			if ( 'simple' === $key ) {
				foreach ( $value as $link ) {
					$data = str_replace( str_replace( '/', $replace, $link ), str_replace( '/', $replace, get_home_url() . '/' ), $data );
				}
			}

			if ( 'uploads' === $key ) {
				foreach ( $value as $link ) {
					$url_data = wp_upload_dir();
					$data     = str_replace( str_replace( '/', $replace, $link ), str_replace( '/', $replace, $url_data['baseurl'] . '/' ), $data );
				}
			}
		}

		return $data;
	}

	/**
	 * Create new header in the header builder.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $file    File.
	 * @param boolean $default Is default header.
	 */
	private function _create_new_header( $file, $default = false ) { // phpcs:ignore
		$builder       = Modules::get( 'header-builder' );
		$header_data   = json_decode( $this->links_replace( $this->_get_local_file_content( $file ), '/' ), true );
		$imported_data = get_option( 'xts_imported_data' );

		$imported_data['headers'][] = $header_data['id'];

		$builder->list->add_header( $header_data['id'], $header_data['name'] );
		$builder->factory->create_new( $header_data['id'], $header_data['name'], $header_data['structure'], $header_data['settings'] );
		update_option( 'xts_imported_data', $imported_data );

		if ( $default ) {
			update_option( 'xts_main_header', $header_data['id'] );
		}
	}

	/**
	 * Set home page in Settings -> Reading.
	 *
	 * @since 1.0.0
	 */
	private function _set_home_page() { // phpcs:ignore
		$home_page_title = 'Home page';
		$home_page       = get_page_by_title( $home_page_title );

		if ( ! is_null( $home_page ) ) {
			update_option( 'page_on_front', $home_page->ID );
			update_option( 'show_on_front', 'page' );

			AJAX_Response::add_msg( 'Front page set to <strong>"' . $home_page_title . '"</strong>' );
		} else {
			AJAX_Response::add_msg( 'Front page is not changed' );
		}
	}

	/**
	 * Set blog page in Settings -> Reading.
	 *
	 * @since 1.0.0
	 */
	public function _set_blog_page() { // phpcs:ignore
		$blog_page_title = 'Blog';
		$blog_page       = get_page_by_title( $blog_page_title );
		$demo_post       = get_page_by_title( 'Hello world!', OBJECT, 'post' );
		$demo_page       = get_page_by_title( 'Sample Page' );

		if ( ! is_null( $blog_page ) ) {
			update_option( 'page_for_posts', $blog_page->ID );
			update_option( 'show_on_front', 'page' );
		}
		if ( ! is_null( $demo_post ) ) {
			wp_delete_post( $demo_page->ID, true );
		}
		if ( ! is_null( $demo_post ) ) {
			wp_delete_post( $demo_post->ID, true );
		}
	}

	/**
	 * Specify menus locations.
	 *
	 * @since 1.0.0
	 */
	public function _menu_locations() { // phpcs:ignore
		global $wpdb;

		$location        = 'main-menu';
		$mobile_location = 'mobile-menu';

		$menu_ids = $wpdb->get_results( // phpcs:ignore
			'
		    SELECT term_id, name
		    FROM ' . $wpdb->prefix . 'terms' . "
		    WHERE name IN ( 'Main menu', 'Mobile menu' )
		    ORDER BY name ASC
		    "
		);

		$locations = get_theme_mod( 'nav_menu_locations' );

		foreach ( $menu_ids as $menu ) {
			if ( 'Main menu' === $menu->name ) {
				if ( ! has_nav_menu( $location ) ) {
					$locations[ $location ] = $menu->term_id;
				}

				if ( ! has_nav_menu( $mobile_location ) ) {
					$locations[ $mobile_location ] = $menu->term_id;
				}
			}

			if ( 'Mobile menu' === $menu->name ) {
				if ( ! has_nav_menu( $mobile_location ) ) {
					$locations[ $mobile_location ] = $menu->term_id;
				}
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/**
	 * Import widgets json file. The data structure is the same as for widgets import / export plugin.
	 *
	 * @since 1.0.0
	 */
	private function _set_up_widgets() { // phpcs:ignore
		$config = $this->get_config();

		if ( ! $config['widgets'] ) {
			return;
		}

		$version_widgets = json_decode( $this->links_replace( wp_json_encode( $config['widgets'] ) ), true );

		// We don't want to undo user changes, so we look for changes first.
		$active_widgets = get_option( 'sidebars_widgets' );

		if ( ! isset( $version_widgets['mobile-menu-widget-sidebar'] ) ) {
			unset( $active_widgets['mobile-menu-widget-sidebar'] );
		}

		$widgets_counter = 1;

		foreach ( $version_widgets as $area => $widgets ) {
			unset( $active_widgets[ $area ] );
			foreach ( $widgets as $widget => $options ) {
				$widget = preg_replace( '/-[0-9]+$/', '', $widget );

				$active_widgets[ $area ][] = $widget . '-' . $widgets_counter;

				$widget_content = get_option( 'widget_' . $widget );

				if ( 'nav_menu' === $widget ) {
					$term_data           = get_term_by( 'name', $options['title'], 'nav_menu' );
					$options['nav_menu'] = $term_data->term_id;
				}

				$widget_content[ $widgets_counter ] = $options;

				update_option( 'widget_' . $widget, $widget_content );

				$widgets_counter ++;
			}
		}

		// Now save the $active_widgets array.
		update_option( 'sidebars_widgets', $active_widgets );

		AJAX_Response::add_msg( 'Widgets updated' );
	}

	/**
	 * Get file content locally.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file File name.
	 *
	 * @return false|string
	 */
	private function _get_local_file_content( $file ) { // phpcs:ignore
		ob_start();

		include $file;
		$file_content = ob_get_contents();

		ob_end_clean();

		return $file_content;
	}

	/**
	 * Get file to import and check if it exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filename File name.
	 *
	 * @return bool|string
	 */
	private function _get_file_to_import( $filename ) { // phpcs:ignore
		$file = $this->_get_version_folder() . $filename;

		if ( ! file_exists( $file ) ) {
			return false;
		}

		return $file;
	}

	/**
	 * Generate version folder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version Version name.
	 *
	 * @return string
	 */
	private function _get_version_folder( $version = '' ) { // phpcs:ignore
		if ( ! $version ) {
			$version = $this->_version;
		}

		return XTS_THEME_ABSPATH . 'dummy-content/' . $version . '/';
	}

	/**
	 * Load WordPress importer class.
	 *
	 * @since 1.0.0
	 */
	private function _load_importers() { // phpcs:ignore
		// Load Importer API.
		require_once ABSPATH . 'wp-admin/includes/import.php';

		$importer_error = false;

		// check if wp_importer, the base importer class is available, otherwise include it.
		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				require_once $class_wp_importer;
			} else {
				$importer_error = true;
			}
		}

		if ( ! xts_is_core_module_exists() ) {
			return;
		}

		if ( ! xts_is_build_for_space() && defined( 'XTS_CORE_PLUGIN_PATH' ) ) {
			include XTS_CORE_PLUGIN_PATH . '/importer/wordpress-importer.php';
		} else {
			xts_get_file( 'framework/modules/core/importer/wordpress-importer' );
		}

		if ( false !== $importer_error ) {
			AJAX_Response::send_fail_msg( 'The Auto importing script could not be loaded. Please use the WordPress importer and import the XML file that is located in your themes folder manually.' );
		}

		if ( class_exists( 'WP_Importer' ) && class_exists( 'XTS_Import' ) ) {
			$this->_importer = new XTS_Import();
		} else {
			AJAX_Response::send_fail_msg( 'Can\'t find WP_Importer or XTS_Import class' );
		}
	}

	/**
	 * Prevent thumbnails generation process for better performance.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function sizes_array() {
		return array();
	}

	/**
	 * Get config.
	 *
	 * @return mixed|null
	 */
	public function get_config() {
		return json_decode( $this->_get_local_file_content( $this->_get_file_to_import( 'config.json' ) ), true );
	}
}

new Import();
