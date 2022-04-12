<?php
/**
 * Testimonials template function
 *
 * @package xts
 * @version 1.0.0
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_testimonials_template' ) ) {
	/**
	 * Testimonials template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_testimonials_template( $element_args ) {
		$default_args = array(
			// Layout.
			'view'                             => 'carousel',
			'columns'                          => array( 'size' => 3 ),
			'columns_tablet'                   => array( 'size' => '' ),
			'columns_mobile'                   => array( 'size' => '' ),
			'spacing'                          => xts_get_default_value( 'items_gap' ),

			// Carousel.
			'carousel_items'                   => array( 'size' => 3 ),
			'carousel_items_tablet'            => array( 'size' => '' ),
			'carousel_items_mobile'            => array( 'size' => '' ),
			'carousel_spacing'                 => xts_get_default_value( 'items_gap' ),

			// Name.
			'name_text_size'                   => 's',
			'name_color_presets'               => 'default',

			// Title.
			'title_text_size'                  => 'xs',
			'title_color_presets'              => 'default',

			// Description.
			'description_text_size'            => 'default',
			'description_color_presets'        => 'default',

			// Style.
			'color_scheme'                     => 'inherit',
			'text_align'                       => 'center',
			'stars_rating'                     => 'no',
			'testimonials_background_switcher' => 'no',
			'testimonials_shadow_switcher'     => 'no',

			// Extra.
			'animation_in_view'                => 'no',
			'xts_animation_items'              => '',
			'xts_animation_duration_items'     => 'normal',
			'xts_animation_delay_items'        => '',
			'lazy_loading'                     => 'no',
		);

		extract( wp_parse_args( $element_args, $default_args ) ); // phpcs:ignore

		$wrapper_classes     = '';
		$carousel_attrs      = '';
		$name_classes        = '';
		$title_classes       = '';
		$description_classes = '';
		$column_classes      = '';

		$wrapper_classes .= ' xts-textalign-' . $text_align;
		if ( 'inherit' !== $color_scheme ) {
			$wrapper_classes .= ' xts-scheme-' . $color_scheme;
		}
		if ( 'yes' === $testimonials_background_switcher ) {
			$wrapper_classes .= ' xts-with-bg-color';
		}
		if ( 'yes' === $testimonials_shadow_switcher ) {
			$wrapper_classes .= ' xts-with-shadow';
		}
		if ( 'yes' === $animation_in_view ) {
			xts_enqueue_js_script( 'items-animation-in-view' );
			$wrapper_classes .= ' xts-in-view-animation';
		}
		$wrapper_classes .= ' xts-autoplay-animations-off';

		// Name classes.
		if ( xts_elementor_is_edit_mode() ) {
			$name_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $name_color_presets ) {
			$name_classes .= ' xts-textcolor-' . $name_color_presets;
		}
		if ( 'default' !== $name_text_size ) {
			$name_classes .= ' xts-fontsize-' . $name_text_size;
		}

		// title classes.
		if ( xts_elementor_is_edit_mode() ) {
			$title_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $title_color_presets ) {
			$title_classes .= ' xts-textcolor-' . $title_color_presets;
		}
		if ( 'default' !== $title_text_size ) {
			$title_classes .= ' xts-fontsize-' . $title_text_size;
		}

		// Description classes.
		if ( xts_elementor_is_edit_mode() ) {
			$description_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $description_color_presets ) {
			$description_classes .= ' xts-textcolor-' . $description_color_presets;
		}
		if ( 'default' !== $description_text_size ) {
			$description_classes .= ' xts-fontsize-' . $description_text_size;
		}

		if ( 'carousel' === $view ) {
			$wrapper_classes .= xts_get_carousel_classes( $element_args );
			$wrapper_classes .= xts_get_row_classes( $carousel_items['size'], $carousel_items_tablet['size'], $carousel_items_mobile['size'], $carousel_spacing );

			$carousel_attrs .= xts_get_carousel_atts( $element_args );
		} else {
			$wrapper_classes .= xts_get_row_classes( $columns['size'], $columns_tablet['size'], $columns_mobile['size'], $spacing );
		}

		// Animations.
		if ( 'yes' === $animation_in_view && $xts_animation_items ) {
			$column_classes .= ' xts-animation-' . $xts_animation_items;
			$column_classes .= ' xts-animation-' . $xts_animation_duration_items;
		}

		// Lazy loading.
		$lazy_module = Modules::get( 'lazy-loading' );
		if ( 'yes' === $lazy_loading ) {
			$lazy_module->lazy_init( true );
		} elseif ( 'no' === $lazy_loading ) {
			$lazy_module->lazy_disable( true );
		}
		?>
			<div class="xts-testimonials<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo wp_kses( $carousel_attrs, true ); ?> data-animation-delay="<?php echo esc_attr( $xts_animation_delay_items ); ?>">
				<?php foreach ( $testimonials as $index => $testimonial ) : ?>
					<?php $image_output = xts_get_image_html( $testimonial, 'image' ); ?>

					<div class="xts-col<?php echo esc_attr( $column_classes ); ?>">
						<?php
						xts_get_template(
							'testimonial-item.php',
							array(
								'testimonial'         => $testimonial,
								'index'               => $index,
								'stars_rating'        => $stars_rating,
								'title_classes'       => $title_classes,
								'name_classes'        => $name_classes,
								'description_classes' => $description_classes,
								'image_output'        => $image_output,
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
		if ( 'yes' === $lazy_loading ) {
			$lazy_module->lazy_disable( true );
		} elseif ( 'no' === $lazy_loading ) {
			$lazy_module->lazy_init();
		}
	}
}
