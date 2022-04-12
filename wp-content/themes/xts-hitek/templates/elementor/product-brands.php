<?php
/**
 * Brands template function
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_product_brands_template' ) ) {
	/**
	 * Brands template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_product_brands_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			// General.
			'items_per_page'               => array( 'size' => 10 ),
			'include'                      => '',

			// Query.
			'orderby'                      => 'id',
			'order'                        => 'desc',
			'offset'                       => '',
			'exclude'                      => '',
			'meta_key'                     => '', // phpcs:ignore

			// Style.
			'hover'                        => 'default',
			'design'                       => 'default',
			'color_scheme'                 => 'inherit',

			// Layout.
			'view'                         => 'grid',
			'columns'                      => array( 'size' => 3 ),
			'columns_tablet'               => array( 'size' => '' ),
			'columns_mobile'               => array( 'size' => '' ),
			'spacing'                      => xts_get_default_value( 'items_gap' ),

			// Carousel.
			'carousel_items'               => array( 'size' => 3 ),
			'carousel_items_tablet'        => array( 'size' => '' ),
			'carousel_items_mobile'        => array( 'size' => '' ),
			'carousel_spacing'             => xts_get_default_value( 'items_gap' ),

			// Extra.
			'animation_in_view'            => 'no',
			'xts_animation_items'          => '',
			'xts_animation_duration_items' => 'normal',
			'xts_animation_delay_items'    => '',
			'lazy_loading'                 => 'no',
		);

		$element_args = wp_parse_args( $element_args, $default_args ); // phpcs:ignore

		$wrapper_classes = '';
		$carousel_attrs  = '';

		$attribute = xts_get_opt( 'brands_attribute' );

		$args = array(
			'taxonomy'   => $attribute,
			'hide_empty' => false,
			'order'      => $element_args['order'],
			'number'     => $element_args['items_per_page']['size'],
			'orderby'    => $element_args['orderby'],
		);

		if ( 'rand' === $element_args['orderby'] ) {
			$args['orderby'] = 'id';
			$brands_count    = wp_count_terms(
				$attribute,
				array(
					'hide_empty' => 0,
				)
			);

			$offset = wp_rand( 0, $brands_count - $element_args['items_per_page']['size'] );

			if ( $offset <= 0 ) {
				$offset = '';
			}

			$args['offset'] = $offset;
		}

		if ( $element_args['offset'] ) {
			$args['offset'] = $element_args['offset'];
		}

		if ( $element_args['exclude'] ) {
			$args['exclude'] = $element_args['exclude'];
		}

		if ( $element_args['meta_key'] ) {
			$args['meta_key'] = $element_args['meta_key']; // phpcs:ignore
		}

		if ( $element_args['include'] ) {
			$args['include'] = $element_args['include'];
		}

		$brands   = get_terms( $args );
		$taxonomy = get_taxonomy( $attribute );

		if ( is_wp_error( $brands ) || count( $brands ) <= 0 ) {
			?>
				<div class="xts-notification xts-color-info">
					<?php esc_html_e( 'To display brands list you need to create product attribute first. Go to Dashboard -> Products -> Attribute -> create new for your product brands. Then go to Theme Settings -> Shop -> Brands and set the newly created attribute. Be sure that you have added some brands to that attribute also.', 'xts-theme' ); ?>
				</div>
			<?php
			return;
		}

		if ( 'rand' === $element_args['orderby'] ) {
			shuffle( $brands );
		}

		if ( xts_is_shop_on_front() ) {
			$link = home_url();
		} else {
			$link = get_post_type_archive_link( 'product' );
		}

		if ( 'carousel' === $element_args['view'] ) {
			$wrapper_classes .= xts_get_carousel_classes( $element_args );
			$wrapper_classes .= xts_get_row_classes( $element_args['carousel_items']['size'], $element_args['carousel_items_tablet']['size'], $element_args['carousel_items_mobile']['size'], $element_args['carousel_spacing'] );
			$carousel_attrs  .= xts_get_carousel_atts( $element_args );
		} else {
			$wrapper_classes .= xts_get_row_classes( $element_args['columns']['size'], $element_args['columns_tablet']['size'], $element_args['columns_mobile']['size'], $element_args['spacing'] );
		}
		if ( 'yes' === $element_args['animation_in_view'] ) {
			xts_enqueue_js_script( 'items-animation-in-view' );
			$wrapper_classes .= ' xts-in-view-animation';
		}
		$wrapper_classes .= ' `xts-autoplay-animations-off`';
		$wrapper_classes .= ' xts-design-' . $element_args['design'];
		if ( 'inherit' !== $element_args['color_scheme'] ) {
			$wrapper_classes .= ' xts-scheme-' . $element_args['color_scheme'];
		}
		if ( 'default' === $element_args['design'] ) {
			$wrapper_classes .= ' xts-hover-' . $element_args['hover'];
		}

		// Lazy loading.
		$lazy_module = Modules::get( 'lazy-loading' );
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		}

		?>
		<div class="xts-brands<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo wp_kses( $carousel_attrs, true ); ?> data-animation-delay="<?php echo esc_attr( $element_args['xts_animation_delay_items'] ); ?>">
			<?php foreach ( $brands as $brand ) : ?>
				<?php
				$column_classes = '';
				$image          = get_term_meta( $brand->term_id, '_xts_attribute_image', true );
				$filter_name    = 'filter_' . sanitize_title( str_replace( 'pa_', '', $attribute ) );

				if ( is_object( $taxonomy ) && $taxonomy->public ) {
					$attribute_link = get_term_link( $brand->term_id, $brand->taxonomy );
				} else {
					$attribute_link = add_query_arg( $filter_name, $brand->slug, $link );
				}

				// Animations.
				if ( 'yes' === $element_args['animation_in_view'] && $element_args['xts_animation_items'] ) {
					$column_classes .= ' xts-animation-' . $element_args['xts_animation_items'];
					$column_classes .= ' xts-animation-' . $element_args['xts_animation_duration_items'];
				}
				?>

				<div class="xts-col<?php echo esc_attr( $column_classes ); ?>">
					<?php
					xts_get_template(
						'product-brand-item-' . $element_args['design'] . '.php',
						array(
							'attribute_link' => $attribute_link,
							'image'          => $image,
							'brand'          => $brand,
						),
						'',
						'templates/elementor'
					);
					?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php

		// Lazy loading.
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init();
		}
	}
}
