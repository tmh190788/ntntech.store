<?php
/**
 * Swatches template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-single-product-swatches xts-swatches">
	<?php
	if ( taxonomy_exists( $attribute_name ) ) {
		$terms          = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) );
		$swatch_size    = get_option( 'xts_' . $attribute_name . '_attribute_swatch_size' );
		$options_fliped = array_flip( $options );

		if ( isset( $_REQUEST[ 'attribute_' . $attribute_name ] ) ) { // phpcs:ignore
			$selected_value = wp_unslash( $_REQUEST[ 'attribute_' . $attribute_name ] ); // phpcs:ignore
		} elseif ( isset( $selected_attributes[ $attribute_name ] ) ) {
			$selected_value = $selected_attributes[ $attribute_name ];
		} else {
			$selected_value = '';
		}

		foreach ( $terms as $term ) { // phpcs:ignore
			$style   = '';
			$classes = '';

			if ( ! in_array( $term->slug, $options, false ) ) { // phpcs:ignore
				continue;
			}

			$key = $options_fliped[ $term->slug ];

			// Classes.
			if ( isset( $swatches[ $key ]['color']['idle'] ) && $swatches[ $key ]['color']['idle'] ) {
				xts_enqueue_js_library( 'tooltip' );
				xts_enqueue_js_script( 'tooltip' );
				$classes .= ' xts-with-bg';
				$style    = 'background-color:' . $swatches[ $key ]['color']['idle'] . ';';
			} elseif ( isset( $swatches[ $key ]['image']['id'] ) && $swatches[ $key ]['image']['id'] ) {
				xts_enqueue_js_library( 'tooltip' );
				xts_enqueue_js_script( 'tooltip' );
				$classes .= ' xts-with-bg';
				$style    = 'background-image: url(' . wp_get_attachment_image_url( $swatches[ $key ]['image']['id'] ) . ');';
			} else {
				$classes .= ' xts-with-text';
			}

			if ( is_array( $swatches_use_variation_images ) && in_array( $attribute_name, $swatches_use_variation_images ) && isset( $swatches[ $key ]['image_src'] ) ) { // phpcs:ignore
				$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $swatches[ $key ]['variation_id'] ), 'thumbnail' );


				if ( ! empty( $thumb ) ) {
					xts_enqueue_js_library( 'tooltip' );
					xts_enqueue_js_script( 'tooltip' );
					$style   = 'background-image: url(' . $thumb[0] . ');';
					$classes = ' xts-with-bg';
				}
			}

			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && $active_variations ) {
				if ( in_array( $term->slug, $active_variations, false ) ) { // phpcs:ignore
					$classes .= ' xts-enabled';
				} else {
					$classes .= ' xts-disabled';
				}
			}

			$classes .= ' xts-size-' . $swatch_size;

			if ( $selected_value === $term->slug ) {
				$classes .= ' xts-active';
			}

			?>
				<div class="xts-variation-swatch xts-swatch<?php echo esc_attr( $classes ); ?>" data-taxonomy="<?php echo esc_attr( $term->taxonomy ); ?>" data-term="<?php echo esc_attr( $term->slug ); ?>" style="<?php echo esc_attr( $style ); ?>" <?php echo selected( sanitize_title( $selected_value ), sanitize_title( $term->slug ), false ); ?>>
					<?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ); ?>
				</div>
			<?php
		}
	} else {
		foreach ( $options as $option ) {
			$style   = '';
			$classes = '';

			if ( $selected_value === $option ) {
				$classes .= ' xts-active';
			}

			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && $active_variations ) {
				if ( in_array( $term->slug, $active_variations ) ) { // phpcs:ignore
					$classes .= ' xts-enabled';
				} else {
					$classes .= ' xts-disabled';
				}
			}

			?>
				<div class="xts-variation-swatch<?php echo esc_attr( $classes ); ?>" data-slug="<?php echo esc_attr( $option ); ?>" <?php echo selected( sanitize_title( $selected_value ), sanitize_title( $option ), false ); ?>>
					<?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ); ?>
				</div>
			<?php
		}
	}
	?>
</div>
<?php echo apply_filters( 'xts_variations_swatches_wc_output', $html ); ?>
