<?php
/**
 * Lazy loading class.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use WP_Post;
use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * Lazy loading class.
 *
 * @since 1.0.0
 */
class Lazy_Loading extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'hooks' ) );
		add_action( 'init', array( $this, 'add_options' ) );
		add_action( 'wp', array( $this, 'rss_lazy_disable' ) );
	}

	/**
	 * Fix for RSS images.
	 *
	 * @since 1.0.0
	 */
	public function rss_lazy_disable() {
		if ( is_feed() ) {
			$this->lazy_disable( true );
		}
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'lazy_loading_section',
				'name'     => esc_html__( 'Lazy loading', 'xts-theme' ),
				'priority' => 30,
				'parent'   => 'general_performance_section',
				'icon'     => 'xf-performance',
			)
		);

		Options::add_field(
			array(
				'id'          => 'lazy_loading',
				'type'        => 'switcher',
				'section'     => 'lazy_loading_section',
				'name'        => esc_html__( 'Lazy loading for images', 'xts-theme' ),
				'description' => esc_html__( 'Enable this option to optimize your images loading on the website. They will be loaded only when user will scroll the page.', 'xts-theme' ),
				'default'     => '0',
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'          => 'lazy_loading_offset',
				'name'        => esc_html__( 'Offset', 'xts-theme' ),
				'description' => esc_html__( 'Start load images X pixels before the page is scrolled to the item', 'xts-theme' ),
				'type'        => 'range',
				'section'     => 'lazy_loading_section',
				'default'     => 0,
				'min'         => 0,
				'step'        => 10,
				'max'         => 1000,
				'priority'    => 20,
			)
		);

		Options::add_field(
			array(
				'id'       => 'lazy_effect',
				'name'     => esc_html__( 'Appearance effect', 'xts-theme' ),
				'type'     => 'buttons',
				'section'  => 'lazy_loading_section',
				'options'  => array(
					'fade'    => array(
						'name'  => esc_html__( 'Fade', 'xts-theme' ),
						'value' => 'fade',
					),
					'without' => array(
						'name'  => esc_html__( 'Without', 'xts-theme' ),
						'value' => 'without',
					),
				),
				'default'  => 'fade',
				'priority' => 30,
			)
		);

		Options::add_field(
			array(
				'id'          => 'lazy_generate_previews',
				'type'        => 'switcher',
				'section'     => 'lazy_loading_section',
				'name'        => esc_html__( 'Generate previews', 'xts-theme' ),
				'description' => esc_html__( 'Create placeholders previews as miniatures from the original images.', 'xts-theme' ),
				'default'     => '1',
				'priority'    => 40,
			)
		);

		Options::add_field(
			array(
				'id'          => 'lazy_base_64',
				'type'        => 'switcher',
				'section'     => 'lazy_loading_section',
				'name'        => esc_html__( 'Base 64 encode for placeholders', 'xts-theme' ),
				'description' => esc_html__( 'This option allows you to decrease a number of HTTP requests replacing images with base 64 encoded sources.', 'xts-theme' ),
				'default'     => '1',
				'priority'    => 50,
			)
		);

		Options::add_field(
			array(
				'id'          => 'lazy_proportional_size',
				'type'        => 'switcher',
				'section'     => 'lazy_loading_section',
				'name'        => esc_html__( 'Proportional placeholders size', 'xts-theme' ),
				'description' => esc_html__( 'Will generate proportional image size for the placeholder based on original image size.', 'xts-theme' ),
				'default'     => '1',
				'priority'    => 60,
			)
		);

		Options::add_field(
			array(
				'id'          => 'lazy_custom_placeholder',
				'type'        => 'upload',
				'section'     => 'lazy_loading_section',
				'name'        => esc_html__( 'Upload custom placeholder image', 'xts-theme' ),
				'description' => esc_html__( 'Add your custom image placeholder that will be used before the original image will be loaded.', 'xts-theme' ),
				'priority'    => 70,
			)
		);
	}

	/**
	 * Hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'lazy_init' ), 500 );
		add_action( 'wp', array( $this, 'lazy_init' ), 500 );
		add_action( 'woocommerce_email_before_order_table', array( $this, 'disable_before_order_table' ), 20 );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'init_after_order_table' ), 20 );
	}

	/**
	 * Init lazy loading
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $force_init Force init.
	 */
	public function lazy_init( $force_init = false ) {
		if ( ( ! xts_get_opt( 'lazy_loading' ) || is_admin() ) && ( ! $force_init || is_object( $force_init ) ) ) {
			return;
		}

		// Used for product categories images for example.
		add_filter( 'xts_attachment', array( $this, 'filter_attachment_images' ), 10, 3 );

		// Used for avatar images.
		add_filter( 'get_avatar', array( $this, 'filter_avatar_image' ), 10 );

		// Used for instagram images.
		add_filter( 'xts_image', array( $this, 'filter_images' ), 10 );

		// Elementor.
		add_filter( 'elementor/image_size/get_attachment_image_html', array( $this, 'filter_elementor_images' ), 10, 4 );

		// Products, blog, a lot of other standard WordPress images.
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'get_lazy_attributes' ), 10, 3 );
	}

	/**
	 * Disable lazy loading
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $force_disable Force disable.
	 */
	public function lazy_disable( $force_disable = false ) {
		if ( xts_get_opt( 'lazy_loading' ) && ! $force_disable ) {
			return;
		}

		remove_action( 'xts_attachment', array( $this, 'filter_attachment_images' ) );
		remove_action( 'get_avatar', array( $this, 'filter_avatar_image' ) );
		remove_action( 'xts_image', array( $this, 'filter_images' ) );
		remove_action( 'wp_get_attachment_image_attributes', array( $this, 'get_lazy_attributes' ) );
		remove_action( 'elementor/image_size/get_attachment_image_html', array( $this, 'filter_elementor_images' ) );
	}

	/**
	 * Filters HTML <img> tag and adds lazy loading attributes. Used for avatar images.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html Image html.
	 *
	 * @return string|string[]|null
	 */
	public function filter_avatar_image( $html ) {
		if ( preg_match( "/src=['\"]data:image/is", $html ) ) {
			return $html;
		}

		$uploaded   = xts_get_opt( 'lazy_custom_placeholder' );
		$lazy_image = '';

		if ( isset( $uploaded['id'] ) && $uploaded['id'] ) {
			$uploaded_url = wp_get_attachment_image_url( $uploaded['id'], 'large' );

			if ( $uploaded_url ) {
				$lazy_image = $uploaded_url;
			}
		} else {
			$lazy_image = $this->get_default_preview();
		}

		xts_enqueue_js_script( 'lazy-loading' );

		return $this->replace_image( $html, $lazy_image );
	}

	/**
	 * Filters HTML <img> tag and adds lazy loading attributes. Used for product categories images for example.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $html Image html.
	 * @param integer $id   Image id.
	 * @param array   $size Image size.
	 *
	 * @return string|string[]|null
	 */
	public function filter_attachment_images( $html, $id, $size ) {
		if ( preg_match( "/src=['\"]data:image/is", $html ) ) {
			return $html;
		}

		if ( $id ) {
			$lazy_image = $this->get_attachment_placeholder( $id, $size );
		} else {
			$lazy_image = $this->get_default_preview();
		}

		xts_enqueue_js_script( 'lazy-loading' );

		return $this->replace_image( $html, $lazy_image );
	}

	/**
	 * Filters HTML <img> tag and adds lazy loading attributes. Used for theme images.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html Image html.
	 *
	 * @return string|string[]|null
	 */
	public function filter_images( $html ) {
		if ( preg_match( "/src=['\"]data:image/is", $html ) ) {
			return $html;
		}

		$lazy_image = $this->get_default_preview();

		xts_enqueue_js_script( 'lazy-loading' );

		return $this->replace_image( $html, $lazy_image );
	}

	/**
	 * Filters HTML <img> tag and adds lazy loading attributes. Used for elementor images.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html           Image html.
	 * @param array  $settings       Control settings.
	 * @param string $image_size_key Optional. Settings key for image size.
	 * @param string $image_key      Optional. Settings key for image..
	 *
	 * @return string|string[]|null
	 */
	public function filter_elementor_images( $html, $settings, $image_size_key, $image_key ) {
		if ( preg_match( "/src=['\"]data:image/is", $html ) ) {
			return $html;
		}

		$image         = $settings[ $image_key ];
		$image_sizes   = get_intermediate_image_sizes();
		$image_sizes[] = 'full';
		$size          = $settings[ $image_size_key . '_size' ];

		if ( $image['id'] && in_array( $size, $image_sizes ) ) { // phpcs:ignore
			return $html;
		}

		if ( $image['id'] ) {
			$lazy_image = $this->get_attachment_placeholder( $image['id'], $size );
		} else {
			$lazy_image = $this->get_default_preview();
		}

		xts_enqueue_js_script( 'lazy-loading' );

		return $this->replace_image( $html, $lazy_image );
	}

	/**
	 * Filters <img> tag passed as an argument.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html Image html.
	 * @param string $src  Lazy image src.
	 *
	 * @return string|string[]|null
	 */
	public function replace_image( $html, $src ) {
		$classes = $this->get_css_classes();

		$output = preg_replace( '/<img(.*?)src=/is', '<img$1src="' . $src . '" data-xts-src=', $html );
		$output = preg_replace( '/<img(.*?)srcset=/is', '<img$1srcset="" data-srcset=', $output );

		if ( preg_match( '/class=["\']/i', $output ) ) {
			$output = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1' . $classes . ' $2$1', $output );
		} else {
			$output = preg_replace( '/<img/is', '<img class="' . $classes . '"', $output );
		}

		return $output;
	}

	/**
	 * Filters default WordPress images ATTRIBUTES array called by core API functions.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $attrs      Attributes for the image markup.
	 * @param WP_Post $attachment Image attachment post.
	 * @param array   $size       Image size.
	 *
	 * @return array
	 */
	public function get_lazy_attributes( $attrs, $attachment, $size ) {
		$attrs['data-xts-src'] = $attrs['src'];
		if ( isset( $attrs['srcset'] ) ) {
			$attrs['data-srcset'] = $attrs['srcset'];
		}
		$attrs['srcset'] = '';
		$attrs['src']    = $this->get_attachment_placeholder( $attachment->ID, $size );
		$attrs['class']  = $attrs['class'] . ' ' . $this->get_css_classes();

		xts_enqueue_js_script( 'lazy-loading' );

		return $attrs;
	}

	/**
	 * Get lazy loading image CSS class.
	 *
	 * @since 1.0.0
	 */
	private function get_css_classes() {
		$effect = xts_get_opt( 'lazy_effect' );

		$class = 'xts-lazy-load';
		if ( 'without' !== $effect ) {
			$class .= ' xts-lazy-' . $effect;
		}

		return $class;
	}

	/**
	 * Get placeholder image. Needs ID to generate a blurred preview and size.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id   Image id.
	 * @param mixed   $size Image size.
	 *
	 * @return integer|mixed|string
	 */
	private function get_attachment_placeholder( $id, $size ) {
		// Get size from array.
		if ( is_array( $size ) ) {
			$width  = $size[0];
			$height = $size[1];
		} else {
			// Take it from the original image.
			$image  = wp_get_attachment_image_src( $id, $size );
			$width  = $image[1];
			$height = $image[2];
		}

		$placeholder_size = $this->get_placeholder_size( $width, $height );

		$custom_placeholder = xts_get_opt( 'lazy_custom_placeholder' );

		if ( xts_get_opt( 'lazy_generate_previews' ) ) {
			$img = xts_get_image_url(
				$id,
				'image',
				array(
					'image_size'             => 'custom',
					'image_custom_dimension' => $placeholder_size,
					'image'                  => array(
						'id' => $id,
					),
				)
			);
		} elseif ( ! empty( $custom_placeholder ) && is_array( $custom_placeholder ) && ! empty( $custom_placeholder['id'] ) ) {
			$img = $custom_placeholder['id'];

			if ( xts_get_opt( 'lazy_proportional_size' ) ) {
				$img = xts_get_image_url(
					$custom_placeholder['id'],
					'image',
					array(
						'image_size'             => 'custom',
						'image_custom_dimension' => array(
							'width'  => $width,
							'height' => $height,
						),
						'image'                  => array(
							'id' => $custom_placeholder['id'],
						),
					)
				);
			}
		} else {
			return $this->get_default_preview();
		}

		if ( xts_get_opt( 'lazy_base_64' ) ) {
			$img = $this->get_encoded_image( $id, $img );
		}

		return $img;
	}

	/**
	 * Encode small preview image to BASE 64
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id  Image id.
	 * @param integer $url Image url.
	 *
	 * @return int|mixed|string
	 */
	private function get_encoded_image( $id, $url ) {
		if ( ! wp_attachment_is_image( $id ) || preg_match( '/^data\:image/', $url ) ) {
			return $url;
		}

		$meta_key = '_base64_image.' . md5( $url );

		$img_url = get_post_meta( $id, $meta_key, true );

		if ( $img_url ) {
			return $img_url;
		}

		$image_path = preg_replace( '/^.*?wp-content\/uploads\//i', '', $url );
		$uploads    = wp_get_upload_dir();

		if ( $uploads && ( false === $uploads['error'] ) && ( 0 !== strpos( $image_path, $uploads['basedir'] ) ) ) {
			if ( false !== strpos( $image_path, 'wp-content/uploads' ) ) {
				$image_path = trailingslashit( $uploads['basedir'] . '/' . _wp_get_attachment_relative_path( $image_path ) ) . basename( $image_path );
			} else {
				$image_path = $uploads['basedir'] . '/' . $image_path;
			}
		}

		$max_size = 150 * 1024;

		if ( file_exists( $image_path ) && ( ! $max_size || ( filesize( $image_path ) <= $max_size ) ) ) {
			$filetype = wp_check_filetype( $image_path );

			// Read image path, convert to base64 encoding.
			if ( ! xts_is_core_module_exists() ) {
				return '';
			}

			$image_data = xts_compress( xts_get_content_file( $image_path ) );

			// Format the image SRC:  data:{mime};base64,{data};.
			$img_url = 'data:image/' . $filetype['ext'] . ';base64,' . $image_data;

			update_post_meta( $id, $meta_key, $img_url );

			return $img_url;
		}

		return $url;
	}

	/**
	 * Generate placeholder preview small size.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $x0 X.
	 * @param integer $y0 Y.
	 *
	 * @return array
	 */
	private function get_placeholder_size( $x0, $y0 ) {
		$x = 10;
		$y = 10;

		if ( $x0 < $y0 ) {
			$y = ( $x * $y0 ) / $x0;
		}

		if ( $x0 > $y0 ) {
			$x = ( $y * $x0 ) / $y0;
		}

		$x = ceil( $x );
		$y = ceil( $y );

		return array(
			'width'  => $x,
			'height' => $y,
		);
	}

	/**
	 * Get default preview image.
	 *
	 * @since 1.0.0
	 */
	private function get_default_preview() {
		return XTS_IMAGES_URL . '/lazy.png';
	}

	/**
	 * Fix Woocommerce email with lazy load. Disable lazy before email order table.
	 *
	 * @since 1.0.0
	 */
	public function disable_before_order_table() {
		$this->lazy_disable( true );
	}

	/**
	 * Fix Woocommerce email with lazy load. Init lazy after email order table.
	 *
	 * @since 1.0.0
	 */
	public function init_after_order_table() {
		$this->lazy_init( true );
	}
}
