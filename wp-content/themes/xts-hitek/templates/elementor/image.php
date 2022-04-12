<?php
/**
 * Image template function
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_image_template' ) ) {
	/**
	 * Image template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_image_template( $element_args ) {
		global $xts_image_index;

		$default_args = array(
			// General.
			'image'                  => '',
			'image_custom_dimension' => '',

			// General.
			'align'                  => 'center',
			'hover_effect'           => 'none',
			'image_shadow_switcher'  => 'no',

			// Extra.
			'custom_link'            => '',
			'caption'                => 'disabled',
			'click_action'           => 'nothing',
			'global_lightbox'        => 'no',
			'lightbox_gallery'       => 'yes',
			'lazy_loading'           => 'no',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes = '';
		$image_attrs     = '';
		$image_url       = '';
		$link_attrs      = '';

		// Wrapper settings.
		if ( 'inherit' !== $element_args['align'] ) {
			$wrapper_classes .= ' xts-textalign-' . $element_args['align'];
		}
		if ( 'yes' === $element_args['image_shadow_switcher'] ) {
			$wrapper_classes .= ' xts-with-shadow';
		}
		if ( 'lightbox' === $element_args['click_action'] ) {
			xts_enqueue_js_library( 'photoswipe-bundle' );
			xts_enqueue_js_script( 'image-element' );

			$wrapper_classes .= ' xts-photoswipe-image';

			if ( 'yes' === $element_args['global_lightbox'] ) {
				$wrapper_classes .= ' xts-image-global-lightbox';
			}

			if ( 'yes' === $element_args['lightbox_gallery'] ) {
				$wrapper_classes .= ' xts-lightbox-gallery';
			}
		}
		if ( 'disabled' !== $element_args['caption'] ) {
			$wrapper_classes .= ' xts-caption-' . $element_args['caption'];
		}
		if ( 'none' !== $element_args['hover_effect'] ) {
			$wrapper_classes .= ' xts-image-hover-' . $element_args['hover_effect'];
		}

		// Caption settings.
		$caption_text = wp_get_attachment_caption( $element_args['image']['id'] );
		if ( 'lightbox' === $element_args['caption'] ) {
			$image_attrs = ' title="' . esc_attr( $caption_text ) . '"';
		}

		// Image settings.
		$custom_image_size = isset( $element_args['image_custom_dimension']['width'] ) && $element_args['image_custom_dimension']['width'] ? $element_args['image_custom_dimension'] : array(
			'width'  => 128,
			'height' => 128,
		);

		if ( isset( $element_args['image']['id'] ) && $element_args['image']['id'] ) {
			$image_url = xts_get_image_url( $element_args['image']['id'], 'image', $element_args );

			// Image alt.
			$image_alt = get_post_meta( $element_args['image']['id'], '_wp_attachment_image_alt', true );

			if ( $image_alt ) {
				$image_attrs .= ' alt="' . esc_attr( $image_alt ) . '"';
			} else {
				$image_attrs .= ' alt="' . esc_attr( get_the_title( $element_args['image']['id'] ) ) . '"';
			}
		} elseif ( isset( $element_args['image']['url'] ) && $element_args['image']['url'] ) {
			$image_url = $element_args['image']['url'];
		}

		if ( ! $image_url ) {
			return;
		}

		// Lazy loading.
		$lazy_module = Modules::get( 'lazy-loading' );
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		}

		if ( xts_is_svg( $image_url ) ) {
			$image_attrs .= ' width="' . $custom_image_size['width'] . '" height="' . $custom_image_size['height'] . '"';

			$image_output = apply_filters( 'xts_image', '<img src="' . esc_url( $image_url ) . '" ' . $image_attrs . '>' );
		} else {
			$image_output = xts_get_image_html( $element_args, 'image' );
		}

		// Link settings.
		if ( 'lightbox' === $element_args['click_action'] && ! xts_elementor_is_edit_mode() && ! xts_is_svg( $image_url ) && $element_args['image']['id'] ) {
			$image_data = wp_get_attachment_image_src( $element_args['image']['id'], 'full' );

			if ( ! $xts_image_index ) {
				$xts_image_index = 0;
			}

			$link_attrs = xts_get_link_attrs(
				array(
					'url'   => $image_data[0],
					'class' => 'xts-image-inner',
					'data'  => 'data-width="' . esc_attr( $image_data[1] ) . '" data-height="' . esc_attr( $image_data[2] ) . '" data-index="' . esc_attr( $xts_image_index ) . '" data-elementor-open-lightbox="no"',
				)
			);
		}

		if ( 'custom_link' === $element_args['click_action'] && $element_args['custom_link'] && ! xts_elementor_is_edit_mode() ) {
			$element_args['custom_link']['class'] = 'xts-image-inner';

			$link_attrs = xts_get_link_attrs( $element_args['custom_link'] );
		}

		?>
			<div class="xts-image-single<?php echo esc_attr( $wrapper_classes ); ?>">
				<figure class="xts-image">
					<?php if ( 'nothing' !== $element_args['click_action'] && $link_attrs ) : ?>
						<a <?php echo wp_kses( $link_attrs, true ); ?>>
					<?php else : ?>
						<div class="xts-image-inner">
					<?php endif ?>

						<?php echo wp_kses( $image_output, 'xts_media' ); ?>

					<?php if ( 'nothing' !== $element_args['click_action'] && $link_attrs ) : ?>
						</a>
					<?php else : ?>
						</div>
					<?php endif ?>

					<?php if ( 'on-image' === $element_args['caption'] && $caption_text ) : ?>
						<figcaption class="xts-image-caption">
							<?php echo esc_html( $caption_text ); ?>
						</figcaption>
					<?php endif ?>
				</figure>

				<?php if ( 'under-image' === $element_args['caption'] && $caption_text ) : ?>
					<div class="xts-image-caption">
						<?php echo esc_html( $caption_text ); ?>
					</div>
				<?php endif ?>
			</div>
		<?php

		if ( 'lightbox' === $element_args['click_action'] && 'yes' === $element_args['global_lightbox'] ) {
			$xts_image_index++;
		}

		// Lazy loading.
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init();
		}
	}
}
