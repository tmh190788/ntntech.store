<?php
/**
 * Banner template function
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_banner_template' ) ) {
	/**
	 * Banner template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_banner_template( $element_args ) {
		$default_args = array(
			// Style.
			'banner_style'                => 'default',
			'banner_hover'                => 'none',
			'color_scheme'                => 'light',
			'banner_shadow_switcher'      => 'no',

			// Layout.
			'content_align'               => 'center',
			'content_vertical_position'   => 'start',
			'content_horizontal_position' => 'start',

			// General.
			'banner_link'                 => array(
				'url' => '#',
			),

			// Image.
			'image'                       => '',
			'image_type'                  => 'image',
			'image_bg_position'           => 'center-center',

			// Title.
			'title'                       => '',
			'title_text_size'             => 'm',
			'title_color_presets'         => 'default',
			'title_tag'                   => 'h4',

			// Subtitle.
			'subtitle'                    => '',
			'subtitle_text_size'          => 's',
			'subtitle_color_presets'      => 'default',

			// Description.
			'description'                 => '',
			'description_text_size'       => 'default',
			'description_color_presets'   => 'default',

			// Extra.
			'index'                       => '',
			'source'                      => '',
			'lazy_loading'                => 'no',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes     = '';
		$title_classes       = '';
		$subtitle_classes    = '';
		$description_classes = '';
		$content_classes     = '';
		$image_classes       = '';
		$onclick             = '';

		// Wrapper classes.
		$wrapper_classes .= ' xts-with-' . $element_args['image_type'];
		if ( 'default' !== $element_args['banner_style'] ) {
			$wrapper_classes .= ' xts-style-' . $element_args['banner_style'];
		}
		if ( 'none' !== $element_args['banner_hover'] ) {
			$wrapper_classes .= ' xts-hover-' . $element_args['banner_hover'];
		}
		if ( 'parallax' === $element_args['banner_hover'] ) {
			xts_enqueue_js_library( 'parallax' );
			xts_enqueue_js_script( 'parallax-3d' );
		}
		if ( 'inherit' !== $element_args['color_scheme'] ) {
			$wrapper_classes .= ' xts-scheme-' . $element_args['color_scheme'];
		}
		if ( 'yes' === $element_args['banner_shadow_switcher'] ) {
			$wrapper_classes .= ' xts-with-shadow';
		}

		// Title classes.
		if ( xts_elementor_is_edit_mode() ) {
			$title_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args['title_color_presets'] ) {
			$title_classes .= ' xts-textcolor-' . $element_args['title_color_presets'];
		}
		if ( 'default' !== $element_args['title_text_size'] ) {
			$title_classes .= ' xts-fontsize-' . $element_args['title_text_size'];
		}

		// Subtitle classes.
		if ( xts_elementor_is_edit_mode() ) {
			$subtitle_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args['subtitle_color_presets'] ) {
			$subtitle_classes .= ' xts-textcolor-' . $element_args['subtitle_color_presets'];
		}
		if ( 'default' !== $element_args['subtitle_text_size'] ) {
			$subtitle_classes .= ' xts-fontsize-' . $element_args['subtitle_text_size'];
		}

		// Description classes.
		if ( xts_elementor_is_edit_mode() ) {
			$description_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args['description_color_presets'] ) {
			$description_classes .= ' xts-textcolor-' . $element_args['description_color_presets'];
		}
		if ( 'default' !== $element_args['description_text_size'] ) {
			$description_classes .= ' xts-fontsize-' . $element_args['description_text_size'];
		}

		// Image classes.
		if ( $element_args['image_bg_position'] ) {
			$image_classes .= ' xts-bg-position-' . $element_args['image_bg_position'];
		}

		// Content classes.
		$content_classes .= ' xts-justify-' . $element_args['content_horizontal_position'];
		$content_classes .= ' xts-items-' . $element_args['content_vertical_position'];
		$content_classes .= ' xts-textalign-' . $element_args['content_align'];

		// Banner link settings.
		if ( isset( $element_args['banner_link']['url'] ) && $element_args['banner_link']['url'] && ! xts_elementor_is_edit_mode() ) {
			$wrapper_classes .= ' xts-cursor-pointer';

			$element_args['button_link']['url'] = $element_args['banner_link']['url'];

			if ( 'on' === $element_args['banner_link']['is_external'] ) {
				$onclick = 'window.open("' . esc_url( $element_args['banner_link']['url'] ) . '","_blank")';
			} else {
				$onclick = 'window.location.href="' . esc_url( $element_args['banner_link']['url'] ) . '"';
			}
		}

		// Inline editing.
		$inline_editing_key = '';
		if ( 'carousel' === $element_args['source'] ) {
			$inline_editing_key = 'content_repeater.' . $element_args['index'] . '.';
		}
		$element_args['inline_editing_key'] = $inline_editing_key;

		// Lazy loading.
		$lazy_module = Modules::get( 'lazy-loading' );
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		}

		?>
		<div class="xts-iimage<?php echo esc_attr( $wrapper_classes ); ?>" onclick="<?php echo esc_js( $onclick ); ?>">

			<div class="xts-iimage-img-wrapper">
				<div class="xts-iimage-img<?php echo esc_attr( $image_classes ); ?>">
					<?php echo xts_get_image_html( $element_args, 'image' ); // phpcs:ignore ?>
				</div>

				<div class="xts-iimage-overlay xts-fill"></div>
			</div>

			<div class="xts-iimage-content-wrapper xts-fill<?php echo esc_attr( $content_classes ); ?>">
				<div class="xts-iimage-content xts-reset-all-last">
					<?php if ( $element_args['subtitle'] ) : ?>
						<?php
						xts_get_template(
							'banner-subtitle.php',
							array(
								'subtitle_classes'   => $subtitle_classes,
								'inline_editing_key' => $inline_editing_key,
								'banner'             => $element_args,
							),
							'',
							'templates/elementor'
						);
						?>
					<?php endif; ?>

					<?php if ( $element_args['title'] ) : ?>
						<<?php echo esc_attr( $element_args['title_tag'] ); ?> class="xts-iimage-title title<?php echo esc_attr( $title_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>title">
							<?php echo wp_kses( $element_args['title'], xts_get_allowed_html() ); ?>
						</<?php echo esc_attr( $element_args['title_tag'] ); ?>>
					<?php endif; ?>

					<?php if ( $element_args['description'] ) : ?>
						<div class="xts-iimage-desc xts-reset-mb-10<?php echo esc_attr( $description_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>description">
							<?php echo do_shortcode( $element_args['description'] ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $element_args['button_text'] ) : ?>
						<?php xts_button_template( $element_args ); ?>
					<?php endif; ?>
				</div>
			</div>
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

if ( ! function_exists( 'xts_banner_carousel_template' ) ) {
	/**
	 * Banner carousel template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_banner_carousel_template( $element_args ) {
		$default_args = array(
			'content_repeater'             => array(),

			// Carousel.
			'carousel_items'               => array( 'size' => 2 ),
			'carousel_items_tablet'        => array( 'size' => '' ),
			'carousel_items_mobile'        => array( 'size' => '' ),
			'carousel_spacing'             => xts_get_default_value( 'items_gap' ),

			// Extra.
			'animation_in_view'            => 'no',
			'xts_animation_items'          => '',
			'xts_animation_duration_items' => 'normal',
			'xts_animation_delay_items'    => '',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes = '';
		$column_classes  = '';

		// Wrapper classes.
		$wrapper_classes .= xts_get_row_classes( $element_args['carousel_items']['size'], $element_args['carousel_items_tablet']['size'], $element_args['carousel_items_mobile']['size'], $element_args['carousel_spacing'] );
		$wrapper_classes .= xts_get_carousel_classes( $element_args );

		if ( 'yes' === $element_args['animation_in_view'] ) {
			xts_enqueue_js_script( 'items-animation-in-view' );
			$wrapper_classes .= ' xts-in-view-animation';
		}
		$wrapper_classes .= ' xts-autoplay-animations-off';

		// Animations.
		if ( 'yes' === $element_args['animation_in_view'] && $element_args['xts_animation_items'] ) {
			$column_classes .= ' xts-animation-' . $element_args['xts_animation_items'];
			$column_classes .= ' xts-animation-' . $element_args['xts_animation_duration_items'];
		}

		?>

		<div class="xts-iimage-carousel<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo xts_get_carousel_atts( $element_args ); // phpcs:ignore ?> data-animation-delay="<?php echo esc_attr( $element_args['xts_animation_delay_items'] ); ?>">
			<?php foreach ( $element_args['content_repeater'] as $index => $banner ) : ?>
				<?php
				$column_classes_loop = '';
				$banner              = $banner + $element_args;
				$banner['index']     = $index;
				$banner['source']    = 'carousel';

				$column_classes_loop .= ' elementor-repeater-item-' . $banner['_id'];

				?>

				<div class="xts-col<?php echo esc_attr( $column_classes . $column_classes_loop ); ?>">
					<?php xts_banner_template( $banner ); ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php
	}
}
