<?php
/**
 * Grid swatches template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-product-swatches xts-swatches">
	<?php foreach ( $swatches_to_show as $key => $swatch ) : ?>
		<?php
		$style   = '';
		$classes = '';
		$attrs   = '';
		$term    = get_term_by( 'slug', $key, $attribute_name ); // phpcs:ignore

		if ( isset( $swatch['color']['idle'] ) && $swatch['color']['idle'] ) {
			xts_enqueue_js_library( 'tooltip' );
			xts_enqueue_js_script( 'tooltip' );
			$classes .= ' xts-with-bg';
			$style    = 'background-color:' . $swatch['color']['idle'] . ';';
		} elseif ( isset( $swatch['image']['id'] ) && $swatch['image']['id'] ) {
			xts_enqueue_js_library( 'tooltip' );
			xts_enqueue_js_script( 'tooltip' );
			$classes .= ' xts-with-bg';
			$style    = 'background-image: url(' . wp_get_attachment_image_url( $swatch['image']['id'] ) . ');';
		} else {
			$classes .= ' xts-with-text';
		}

		if ( isset( $swatch['image_src'] ) ) {
			$attrs .= ' data-image-src="' . $swatch['image_src'] . '"';
			$attrs .= ' data-attr="' . $attribute_name . '"';
			$attrs .= ' data-value="' . $key . '"';
			$attrs .= ' data-image-srcset="' . $swatch['image_srcset'] . '"';
			$attrs .= ' data-image-sizes="' . $swatch['image_sizes'] . '"';

			if ( is_array( $swatches_use_variation_images ) && in_array( $attribute_name, $swatches_use_variation_images ) ) { // phpcs:ignore
				$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $swatch['variation_id'] ), 'thumbnail' );
				if ( ! empty( $thumb ) ) {
					xts_enqueue_js_library( 'tooltip' );
					xts_enqueue_js_script( 'tooltip' );
					$style   = 'background-image: url(' . $thumb[0] . ');';
					$classes = ' xts-with-bg';
				}
			}

			if ( ! $swatch['is_in_stock'] ) {
				$classes .= ' xts-out-of-stock';
			}

			$classes .= ' xts-enabled';
		}

		$classes .= ' xts-size-' . $swatch_size;
		?>

		<div class="xts-loop-swatch xts-swatch<?php echo esc_attr( $classes ); ?>" style="<?php echo esc_attr( $style ); ?>" <?php echo wp_kses( $attrs, true ); // phpcs:ignore ?>>
			<?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ); ?>
		</div>
	<?php endforeach; ?>
</div>
