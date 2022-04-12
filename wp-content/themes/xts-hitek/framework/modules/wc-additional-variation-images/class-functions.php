<?php
/**
 * Wishlist.
 *
 * @package xts
 */

namespace XTS\WC_Additional_Variation_Images;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Modules;
use XTS\Singleton;

/**
 * Wishlist.
 *
 * @since 1.0.0
 */
class Functions extends Singleton {
	/**
	 * Initialize action.
	 *
	 * @since 1.0
	 */
	public function init() {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		add_action( 'init', array( $this, 'hooks' ), 100 );
	}

	/**
	 * Register hooks and actions.
	 *
	 * @since 1.0
	 */
	public function hooks() {
		add_filter( 'woocommerce_available_variation', array( $this, 'update_available_variation' ), 10 );
	}

	/**
	 * Update available variation.
	 *
	 * @since 1.0.0
	 *
	 * @param array $available_variation Available variation.
	 *
	 * @return array
	 */
	public function update_available_variation( $available_variation ) {
		if ( ! xts_get_opt( 'single_product_additional_variations_images', '1' ) ) {
			return $available_variation;
		}

		$product_id          = get_the_ID();
		$default_images_data = $this->get_default_data( $product_id );
		$variation_id        = $available_variation['variation_id'];
		$images_data         = get_post_meta( $variation_id, 'xts_additional_variation_images_data', true );
		$ids                 = array_filter( explode( ',', $images_data ) );

		if ( has_post_thumbnail( $variation_id ) ) {
			$available_variation['additional_variation_images'][] = $this->get_image_data( get_post_thumbnail_id( $variation_id ), true );
		}

		foreach ( $ids as $id ) {
			$available_variation['additional_variation_images'][] = $this->get_image_data( $id );
		}

		if ( $default_images_data ) {
			$available_variation['additional_variation_images_default'] = $default_images_data;
		}

		return $available_variation;
	}

	/**
	 * Get default images data.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $product_id Product id.
	 *
	 * @return array|void
	 */
	public function get_default_data( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return;
		}

		$default_image_ids = $product->get_gallery_image_ids();
		$images            = array();

		if ( has_post_thumbnail( $product_id ) ) {
			$images[] = $this->get_image_data( get_post_thumbnail_id( $product_id ), true );
		}

		if ( $default_image_ids && is_array( $default_image_ids ) ) {
			foreach ( $default_image_ids as $id ) {
				$images[] = $this->get_image_data( $id );
			}
		}

		return $images;
	}

	/**
	 * Get image data.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $attachment_id Attachment id.
	 * @param boolean $main_image    Is main image.
	 *
	 * @return array
	 */
	public function get_image_data( $attachment_id, $main_image = false ) {
		$lazy_module = Modules::get( 'lazy-loading' );
		$lazy_module->lazy_disable( true );

		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$thumbnail_size    = apply_filters(
			'woocommerce_gallery_thumbnail_size',
			array(
				$gallery_thumbnail['width'],
				$gallery_thumbnail['height'],
			)
		);
		$image_size        = 'woocommerce_single';
		$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
		$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
		$image_src         = wp_get_attachment_image_src( $attachment_id, $image_size );

		$output = array(
			'width'                   => $image_src[1],
			'height'                  => $image_src[2],
			'src'                     => $image_src[0],
			'full_src'                => $full_src[0],
			'thumbnail_src'           => $thumbnail_src[0],
			'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
			'alt'                     => trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ),
			'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
			'data_caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
			'data_src'                => esc_url( $full_src[0] ),
			'data_large_image'        => esc_url( $full_src[0] ),
			'data_large_image_width'  => esc_attr( $full_src[1] ),
			'data_large_image_height' => esc_attr( $full_src[2] ),
		);

		$image_meta = wp_get_attachment_metadata( $attachment_id );

		if ( is_array( $image_meta ) && xts_is_core_module_exists() ) {
			$size_array = array( absint( $image_src[1] ), absint( $image_src[2] ) );
			$srcset     = xts_calculate_image( $size_array, $image_src[0], $image_meta, $attachment_id ); // Does not remove core functionality.
			$sizes      = wp_calculate_image_sizes( $size_array, $image_src[0], $image_meta, $attachment_id );

			if ( $srcset && ( $sizes || ! empty( $attr['sizes'] ) ) ) {
				$output['srcset'] = $srcset;

				if ( empty( $attr['sizes'] ) ) {
					$output['sizes'] = $sizes;
				}
			}
		}

		$lazy_module->lazy_init();

		return $output;
	}
}
