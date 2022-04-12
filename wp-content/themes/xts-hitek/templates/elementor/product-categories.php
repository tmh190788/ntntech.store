<?php
/**
 * Categories template function
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_product_categories_template' ) ) {
	/**
	 * Categories template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_product_categories_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			// General.
			'items_per_page'               => array( 'size' => 6 ),
			'include'                      => '',
			'hide_empty'                   => 'yes',
			'only_parent'                  => 'no',

			// Query.
			'orderby'                      => 'id',
			'order'                        => 'desc',
			'offset'                       => '',
			'exclude'                      => '',
			'meta_key'                     => '', // phpcs:ignore

			// General.
			'design'                       => 'default',
			'color_scheme'                 => 'inherit',
			'image_size'                   => 'woocommerce_thumbnail',
			'image_size_custom'            => '',

			// Layout.
			'view'                         => 'carousel',
			'columns'                      => array( 'size' => 3 ),
			'columns_tablet'               => array( 'size' => '' ),
			'columns_mobile'               => array( 'size' => '' ),
			'spacing'                      => xts_get_default_value( 'items_gap' ),
			'masonry'                      => 'no',
			'different_sizes'              => '0',
			'different_sizes_position'     => '2,5,8,9',

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

		$element_args = wp_parse_args( $element_args, $default_args );

		if ( 'inherit' === $element_args['color_scheme'] ) {
			$element_args['color_scheme'] = xts_get_opt( 'product_categories_color_scheme' );
		}

		$wrapper_classes          = '';
		$carousel_attrs           = '';
		$different_sizes_position = explode( ',', $element_args['different_sizes_position'] );

		xts_set_loop_prop( 'product_categories_image_size', $element_args['image_size'] );
		xts_set_loop_prop( 'product_categories_image_custom', $element_args['image_size_custom'] );

		$args = array(
			'order'      => $element_args['order'],
			'hide_empty' => $element_args['hide_empty'],
			'pad_counts' => true,
			'number'     => $element_args['items_per_page']['size'],
			'orderby'    => $element_args['orderby'],
		);

		if ( 'yes' === $element_args['only_parent'] ) {
			$args['parent'] = 0;
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

		$categories = get_terms( 'product_cat', $args );

		if ( is_wp_error( $categories ) || count( $categories ) <= 0 ) {
			return;
		}

		// Wrapper classes.
		$wrapper_classes .= ' xts-cat-design-' . $element_args['design'];
		$wrapper_classes .= ' xts-autoplay-animations-off';
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
		if ( 'inherit' !== $element_args['color_scheme'] ) {
			$wrapper_classes .= ' xts-scheme-' . $element_args['color_scheme'] . '-cat';
		}
		if ( $element_args['masonry'] ) {
			wp_enqueue_script( 'imagesloaded' );
			xts_enqueue_js_library( 'isotope-bundle' );
			xts_enqueue_js_script( 'masonry-layout' );
			$wrapper_classes .= ' xts-masonry-layout';
		}

		$index = 0;

		// Lazy loading.
		$lazy_module = Modules::get( 'lazy-loading' );
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		}

		?>
		<div class="xts-cats<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo wp_kses( $carousel_attrs, true ); ?> data-animation-delay="<?php echo esc_attr( $element_args['xts_animation_delay_items'] ); ?>">
			<?php foreach ( $categories as $category ) : ?>
				<?php
				$index ++;

				$column_classes   = '';
				$category_classes = '';

				// Template_args.
				$template_args = array(
					'category' => $category,
				);

				// Category classes.
				$category_classes .= ' xts-cat';

				if ( in_array( $index, $different_sizes_position ) && $element_args['different_sizes'] ) { // phpcs:ignore
					$column_classes .= ' xts-wide';
				}

				// Animations.
				if ( 'yes' === $element_args['animation_in_view'] && $element_args['xts_animation_items'] ) {
					$column_classes .= ' xts-animation-' . $element_args['xts_animation_items'];
					$column_classes .= ' xts-animation-' . $element_args['xts_animation_duration_items'];
				}

				// Sub categories.
				if ( 'subcat' === $element_args['design'] ) {
					$sub_categories = get_terms(
						'product_cat',
						array(
							'fields'       => 'all',
							'parent'       => $category->term_id,
							'hierarchical' => true,
							'hide_empty'   => true,
						)
					);

					if ( $sub_categories ) {
						$category_classes               .= ' xts-with-subcat';
						$template_args['sub_categories'] = $sub_categories;
					}
				}

				?>

				<div class="xts-col<?php echo esc_attr( $column_classes ); ?>">
					<div <?php wc_product_cat_class( $category_classes, $category ); ?>>
						<?php
						wc_get_template(
							'content-product-cat-' . $element_args['design'] . '.php',
							$template_args
						);
						?>
					</div>
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
