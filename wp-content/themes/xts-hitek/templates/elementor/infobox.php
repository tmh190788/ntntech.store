<?php
/**
 * Infobox template functions
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_infobox_template' ) ) {
	/**
	 * Infobox template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_infobox_template( $element_args ) {
		$default_args = array(
			// General.
			'infobox_link'                   => array(
				'url' => '',
			),

			// Icon.
			'icon_type'                      => 'icon',
			'icon_size'                      => 'm',
			'icon'                           => '',
			'text_icon'                      => '',
			'image'                          => '',
			'image_custom_dimension'         => '',

			// Text.
			'title'                          => '',
			'subtitle'                       => '',
			'description'                    => '',

			// General.
			'content_align'                  => 'center',
			'infobox_color_scheme_switcher'  => 'no',
			'infobox_color_scheme'           => 'inherit',
			'infobox_color_scheme_hover'     => 'inherit',
			'infobox_shadow_switcher'        => 'no',
			'infobox_background_switcher'    => 'no',
			'infobox_border_switcher'        => 'no',

			// Icon.
			'icon_position'                  => 'top',
			'icon_vertical_position'         => 'start',
			'icon_shape'                     => 'square',
			'icon_border_color_switcher'     => 'no',
			'icon_background_color_switcher' => 'no',
			'icon_color_switcher'            => 'no',

			// Title.
			'title_text_size'                => 'm',
			'title_color_presets'            => 'default',
			'title_tag'                      => 'h4',

			// Subtitle.
			'subtitle_text_size'             => 's',
			'subtitle_color_presets'         => 'default',

			// Description.
			'description_text_size'          => 'default',
			'description_color_presets'      => 'default',

			// Extra.
			'index'                          => '',
			'source'                         => '',
			'lazy_loading'                   => 'no',
			'extra_wrapper_classes'          => '',

			// Header builder.
			'without_title_spacing'          => '',
			'header_builder'                 => 'no',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes     = '';
		$icon_classes        = '';
		$title_classes       = '';
		$subtitle_classes    = '';
		$description_classes = '';
		$icon_output         = '';
		$onclick             = '';

		// Wrapper classes.
		$wrapper_classes .= ' xts-textalign-' . $element_args['content_align'];
		$wrapper_classes .= ' xts-icon-' . $element_args['icon_position'];
		if ( 'side' === $element_args['icon_position'] ) {
			$wrapper_classes .= ' xts-items-' . $element_args['icon_vertical_position'];
		}
		if ( 'yes' === $element_args['infobox_color_scheme_switcher'] ) {
			if ( 'inherit' !== $element_args['infobox_color_scheme'] ) {
				$wrapper_classes .= ' xts-scheme-' . $element_args['infobox_color_scheme'];
			}
			if ( 'inherit' !== $element_args['infobox_color_scheme_hover'] ) {
				$wrapper_classes .= ' xts-scheme-hover-' . $element_args['infobox_color_scheme_hover'];
			}
		}
		if ( 'yes' === $element_args['infobox_background_switcher'] ) {
			$wrapper_classes .= ' xts-with-bg-color';
		}
		if ( 'yes' === $element_args['infobox_shadow_switcher'] ) {
			$wrapper_classes .= ' xts-with-shadow';
		}
		if ( 'yes' === $element_args['infobox_border_switcher'] ) {
			$wrapper_classes .= ' xts-with-border';
		}
		if ( $element_args['extra_wrapper_classes'] ) {
			$wrapper_classes .= ' ' . $element_args['extra_wrapper_classes'];
		}

		// Header builder.
		if ( $element_args['without_title_spacing'] ) {
			$wrapper_classes .= ' xts-without-spacing';
		}

		// Icon classes.
		$icon_classes .= ' xts-type-' . $element_args['icon_type'];
		if ( 'yes' === $element_args['icon_border_color_switcher'] ) {
			$icon_classes .= ' xts-with-brd-color';
		}
		if ( 'yes' === $element_args['icon_background_color_switcher'] ) {
			$icon_classes .= ' xts-with-bg-color';
		}
		if ( 'yes' === $element_args['icon_color_switcher'] ) {
			$icon_classes .= ' xts-with-color';
		}
		if ( $element_args['icon_size'] ) {
			$icon_classes .= ' xts-size-' . $element_args['icon_size'];
		}
		if ( $element_args['icon_shape'] ) {
			$icon_classes .= ' xts-shape-' . $element_args['icon_shape'];
		}

		// Title classes.
		if ( xts_elementor_is_edit_mode() ) {
			$title_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args['title_color_presets'] ) {
			$title_classes .= ' xts-textcolor-' . $element_args['title_color_presets'];
		}
		if ( 'default' !== $element_args['title_text_size'] ) {
			if ( 'yes' === $element_args['header_builder'] ) {
				$title_classes .= ' xts-header-fontsize-' . $element_args['title_text_size'];
			} else {
				$title_classes .= ' xts-fontsize-' . $element_args['title_text_size'];
			}
		}

		// Subtitle classes.
		if ( xts_elementor_is_edit_mode() ) {
			$subtitle_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args['subtitle_color_presets'] ) {
			$subtitle_classes .= ' xts-textcolor-' . $element_args['subtitle_color_presets'];
		}
		if ( 'default' !== $element_args['subtitle_text_size'] ) {
			if ( 'yes' === $element_args['header_builder'] ) {
				$subtitle_classes .= ' xts-header-fontsize-' . $element_args['subtitle_text_size'];
			} else {
				$subtitle_classes .= ' xts-fontsize-' . $element_args['subtitle_text_size'];
			}
		}

		// Description classes.
		if ( xts_elementor_is_edit_mode() ) {
			$description_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args['description_color_presets'] ) {
			$description_classes .= ' xts-textcolor-' . $element_args['description_color_presets'];
		}
		if ( 'default' !== $element_args['description_text_size'] ) {
			if ( 'yes' === $element_args['header_builder'] ) {
				$description_classes .= ' xts-header-fontsize-' . $element_args['description_text_size'];
			} else {
				$description_classes .= ' xts-fontsize-' . $element_args['description_text_size'];
			}
		}

		// Lazy loading.
		$lazy_module = Modules::get( 'lazy-loading' );
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		}

		// Icon settings.
		$custom_image_size = isset( $element_args['image_custom_dimension']['width'] ) && $element_args['image_custom_dimension']['width'] ? $element_args['image_custom_dimension'] : array(
			'width'  => 128,
			'height' => 128,
		);

		if ( 'image' === $element_args['icon_type'] && $element_args['image'] ) {
			$icon_output = xts_get_image_html( $element_args, 'image' );

			if ( xts_is_svg( $element_args['image']['url'] ) ) {
				$icon_output = '<div class="xts-image-type-svg" style="width:' . esc_attr( $custom_image_size['width'] ) . 'px; height:' . esc_attr( $custom_image_size['height'] ) . 'px;">' . xts_get_svg( '', '', xts_get_image_url( $element_args['image']['id'], 'image', $element_args ) ) . '</div>';
			}
		} elseif ( 'text' === $element_args['icon_type'] ) {
			$icon_output = $element_args['text_icon'];
		} elseif ( 'icon' === $element_args['icon_type'] && $element_args['icon'] ) {
			$icon_output = xts_elementor_get_render_icon( $element_args['icon'] );
		}

		// Link settings.
		if ( isset( $element_args['infobox_link']['url'] ) && $element_args['infobox_link']['url'] && ! xts_elementor_is_edit_mode() ) {
			$wrapper_classes .= ' xts-cursor-pointer';

			$element_args['button_link']['url'] = $element_args['infobox_link']['url'];

			if ( 'on' === $element_args['infobox_link']['is_external'] ) {
				$onclick = 'window.open("' . esc_url( $element_args['infobox_link']['url'] ) . '","_blank")';
			} else {
				$onclick = 'window.location.href="' . esc_url( $element_args['infobox_link']['url'] ) . '"';
			}
		}

		// Inline editing.
		$inline_editing_key = '';
		if ( 'carousel' === $element_args['source'] ) {
			$inline_editing_key = 'content_repeater.' . $element_args['index'] . '.';
		}
		$element_args['inline_editing_key'] = $inline_editing_key;

		?>

		<div class="xts-infobox<?php echo esc_attr( $wrapper_classes ); ?>" onclick="<?php echo esc_js( $onclick ); ?>">
			<?php if ( $icon_output ) : ?>
				<div class="xts-box-icon-wrapper">
					<div class="xts-box-icon<?php echo esc_attr( $icon_classes ); ?>">
						<?php echo wp_kses( $icon_output, 'xts_media' ); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="xts-box-content xts-reset-mb-10 xts-reset-all-last">
				<?php if ( $element_args['subtitle'] ) : ?>
					<div class="xts-box-subtitle<?php echo esc_attr( $subtitle_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>subtitle">
						<?php echo wp_kses( $element_args['subtitle'], xts_get_allowed_html() ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $element_args['title'] ) : ?>
					<<?php echo esc_attr( $element_args['title_tag'] ); ?> class="xts-box-title title<?php echo esc_attr( $title_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>title">
						<?php echo wp_kses( $element_args['title'], xts_get_allowed_html() ); ?>
					</<?php echo esc_attr( $element_args['title_tag'] ); ?>>
				<?php endif; ?>

				<?php if ( $element_args['description'] ) : ?>
					<div class="xts-box-desc<?php echo esc_attr( $description_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>description">
						<?php echo do_shortcode( $element_args['description'] ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $element_args['button_text'] ) : ?>
					<?php xts_button_template( $element_args ); ?>
				<?php endif; ?>
			</div>

			<?php if ( 'no' === $element_args['header_builder'] ) : ?>
				<div class="xts-box-overlay xts-fill"></div>
			<?php endif; ?>
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

if ( ! function_exists( 'xts_infobox_carousel_template' ) ) {
	/**
	 * Infobox carousel template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_infobox_carousel_template( $element_args ) {
		$default_args = array(
			'content_repeater'             => array(),

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

		<div class="xts-infobox-carousel<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo xts_get_carousel_atts( $element_args ); // phpcs:ignore ?> data-animation-delay="<?php echo esc_attr( $element_args['xts_animation_delay_items'] ); ?>">
			<?php foreach ( $element_args['content_repeater'] as $index => $infobox ) : ?>
				<?php
				$column_classes_loop = '';
				$infobox             = $infobox + $element_args;
				$infobox['index']    = $index;
				$infobox['source']   = 'carousel';

				$column_classes_loop .= ' elementor-repeater-item-' . $infobox['_id'];

				?>

				<div class="xts-col<?php echo esc_attr( $column_classes . $column_classes_loop ); ?>">
					<?php xts_infobox_template( $infobox ); ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php
	}
}
