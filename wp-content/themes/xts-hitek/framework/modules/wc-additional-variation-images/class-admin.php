<?php
/**
 * Wishlist UI.
 *
 * @package xts
 */

namespace XTS\WC_Additional_Variation_Images;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Singleton;

/**
 * Wishlist UI.
 *
 * @since 1.0.0
 */
class Admin extends Singleton {
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
		add_action( 'woocommerce_variation_options', array( $this, 'add_images_picker' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_images' ), 10 );
	}

	/**
	 * Add images picker
	 *
	 * @since 1.0.0
	 *
	 * @param integer $loop Loop.
	 * @param array   $variation_data Variation data.
	 * @param object  $variation Variation object.
	 */
	public function add_images_picker( $loop, $variation_data, $variation ) {
		global $post;

		if ( ! xts_get_opt( 'single_product_additional_variations_images', '1' ) ) {
			return;
		}

		xts_get_template(
			'admin-images-picker.php',
			array(
				'variation' => $variation,
				'post'      => $post,
				'admin'     => $this,
			),
			'wc-additional-variation-images'
		);
	}

	/**
	 * Get attachments data
	 *
	 * @since 1.0.0
	 *
	 * @param object $variation Variation object.
	 *
	 * @return array
	 */
	public function get_attachments_data( $variation ) {
		$attachments      = $this->get_attachments( $variation );
		$attachments_data = array();

		if ( ! $attachments ) {
			return $attachments_data;
		}

		foreach ( $attachments as $attachment_id ) {
			$attachments_data[] = array(
				'id'  => $attachment_id,
				'url' => wp_get_attachment_image_src( $attachment_id ),
			);
		}

		return $attachments_data;
	}

	/**
	 * Get attachments
	 *
	 * @since 1.0.0
	 *
	 * @param object $variation  Variation object.
	 *
	 * @return array
	 */
	public function get_attachments( $variation ) {
		$images_data = get_post_meta( $variation->ID, 'xts_additional_variation_images_data', true );

		if ( ! $images_data ) {
			return array();
		}

		return array_filter( explode( ',', $images_data ) );
	}

	/**
	 * Save images.
	 *
	 * @since 1.0
	 *
	 * @param integer $variation_id Variation id.
	 */
	public function save_images( $variation_id ) {
		if ( isset( $_POST[ 'xts_additional_variation_images' ] ) ) { // phpcs:ignore
			if ( isset( $_POST[ 'xts_additional_variation_images' ][ $variation_id ] ) ) { // phpcs:ignore
				$ids = sanitize_text_field( wp_unslash( $_POST['xts_additional_variation_images'][ $variation_id ] ) ); // phpcs:ignore
				update_post_meta( $variation_id, 'xts_additional_variation_images_data', $ids );
			} else {
				delete_post_meta( $variation_id, 'xts_additional_variation_images_data' );
			}
		} else {
			delete_post_meta( $variation_id, 'xts_additional_variation_images_data' );
		}
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
}
