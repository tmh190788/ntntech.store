<?php
/**
 * Button function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_button_template' ) ) {
	/**
	 * Button template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_button_template( $element_args ) {
		$default_args = array(
			'button_align'                       => '',
			'button_text'                        => 'Read more',
			'button_link'                        => '#',
			'button_size'                        => 'm',
			'button_full_width'                  => 'no',
			'button_color'                       => 'primary',
			'button_style'                       => 'default',
			'button_shape'                       => xts_get_default_value( 'button_element_shape' ),
			'button_extra_classes'               => '',
			'button_shadow_switcher'             => 'no',

			// Smooth scroll.
			'button_smooth_scroll'               => 'no',
			'button_smooth_scroll_time'          => 100,
			'button_smooth_scroll_offset'        => 100,

			// Icon.
			'button_icon_type'                   => 'icon',
			'button_icon_size'                   => 'default',
			'button_icon_style'                  => 'default',
			'button_icon_position'               => 'right',
			'button_icon_animation'              => 'without',
			'button_icon'                        => '',
			'button_icon_image'                  => '',
			'button_icon_image_custom_dimension' => '',
			'button_icon_color_switcher'         => 'no',

			// Product.
			'button_link_type'                   => 'link',
			'button_product_id_1'                => '',

			// Extra.
			'wrapper_extra_classes'              => '',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$button_classes      = '';
		$button_text_classes = '';
		$button_icon_classes = '';
		$wrapper_classes     = '';
		$wrapper_attrs       = '';
		$inline_editing_key  = '';
		$icon_output         = '';
		$product             = '';
		$link_attrs          = xts_get_link_attrs( $element_args['button_link'] );

		// Wrapper classes.
		if ( $element_args['button_align'] ) {
			$wrapper_classes .= ' xts-textalign-' . $element_args['button_align'];
		}
		if ( $element_args['wrapper_extra_classes'] ) {
			$wrapper_classes .= ' ' . $element_args['wrapper_extra_classes'];
		}

		// Button classes.
		$button_classes .= ' xts-size-' . $element_args['button_size'];
		$button_classes .= ' xts-style-' . $element_args['button_style'];
		$button_classes .= ' xts-color-' . $element_args['button_color'];
		if ( 'link' !== $element_args['button_style'] ) {
			$button_classes .= ' xts-shape-' . $element_args['button_shape'];
		}
		if ( $element_args['button_extra_classes'] ) {
			$button_classes .= ' ' . $element_args['button_extra_classes'];
		}
		if ( 'yes' === $element_args['button_shadow_switcher'] ) {
			$button_classes .= ' xts-with-shadow';
		}
		if ( 'yes' === $element_args['button_full_width'] ) {
			$button_classes .= ' xts-width-full';
		}

		// Button icon classes.
		if ( ( isset( $element_args['button_icon']['value'] ) && $element_args['button_icon']['value'] ) || $element_args['button_icon_image'] ) {
			$button_classes .= ' xts-icon-pos-' . $element_args['button_icon_position'];

			if ( 'link' === $element_args['button_style'] || 'bordered' === $element_args['button_style'] ) {
				$element_args['button_icon_style'] = 'default';
			}

			$button_classes .= ' xts-icon-style-' . $element_args['button_icon_style'];

			if ( 'without' !== $element_args['button_icon_animation'] ) {
				$button_classes .= ' xts-icon-anim-' . $element_args['button_icon_animation'];
			}
		}

		// Smooth scroll.
		if ( 'yes' === $element_args['button_smooth_scroll'] ) {
			xts_enqueue_js_script( 'button-element-smooth-scroll' );
			$wrapper_classes .= ' xts-smooth-scroll';
			$wrapper_attrs   .= ' data-smooth-time="' . $element_args['button_smooth_scroll_time'] . '"';
			$wrapper_attrs   .= ' data-smooth-offset="' . $element_args['button_smooth_scroll_offset'] . '"';
		}

		// Button text classes.
		if ( xts_elementor_is_edit_mode() ) {
			$button_text_classes .= ' elementor-inline-editing';
		}

		// Icon settings.
		$custom_image_size = isset( $element_args['button_icon_image_custom_dimension']['width'] ) && $element_args['button_icon_image_custom_dimension']['width'] ? $element_args['button_icon_image_custom_dimension'] : array(
			'width'  => 20,
			'height' => 20,
		);

		if ( 'image' === $element_args['button_icon_type'] ) {
			$icon_output = '<span class="xts-button-image">' . xts_get_image_html( $element_args, 'button_icon_image' ) . '</span>';

			if ( 'bg' === $element_args['button_icon_style'] && ( 'fade-left' === $element_args['button_icon_animation'] || 'fade-right' === $element_args['button_icon_animation'] ) ) {
				$icon_output .= '<span class="xts-button-image">' . xts_get_image_html( $element_args, 'button_icon_image' ) . '</span>';
			}

			if ( xts_is_svg( $element_args['button_icon_image']['url'] ) ) {
				$icon_output = '<span class="xts-button-image">' . apply_filters( 'xts_image', '<img src="' . esc_url( xts_get_image_url( $element_args['button_icon_image']['id'], 'button_icon_image', $element_args ) ) . '" width="' . esc_attr( $custom_image_size['width'] ) . '" height="' . esc_attr( $custom_image_size['height'] ) . '">' ) . '</span>';
				if ( 'bg' === $element_args['button_icon_style'] && ( 'fade-left' === $element_args['button_icon_animation'] || 'fade-right' === $element_args['button_icon_animation'] ) ) {
					$icon_output .= '<span class="xts-button-image">' . apply_filters( 'xts_image', '<img src="' . esc_url( xts_get_image_url( $element_args['button_icon_image']['id'], 'button_icon_image', $element_args ) ) . '" width="' . esc_attr( $custom_image_size['width'] ) . '" height="' . esc_attr( $custom_image_size['height'] ) . '">' ) . '</span>';
				}
			}
		} elseif ( 'icon' === $element_args['button_icon_type'] && $element_args['button_icon'] ) {
			$icon_output = xts_elementor_get_render_icon( $element_args['button_icon'] );

			if ( 'bg' === $element_args['button_icon_style'] && ( 'fade-left' === $element_args['button_icon_animation'] || 'fade-right' === $element_args['button_icon_animation'] ) ) {
				$icon_output .= xts_elementor_get_render_icon( $element_args['button_icon'] );
			}
		}

		// Button icon classes.
		if ( 'default' !== $element_args['button_icon_size'] && 'image' !== $element_args['button_icon_type'] ) {
			$button_icon_classes .= ' xts-size-' . $element_args['button_icon_size'];
		}

		// Inline editing settings.
		if ( isset( $element_args['inline_editing_key'] ) ) {
			$inline_editing_key = $element_args['inline_editing_key'];
		}

		// Product settings.
		if ( xts_is_woocommerce_installed() && $element_args['button_product_id_1'] && 'product' === $element_args['button_link_type'] ) {
			xts_enqueue_js_script( 'action-after-add-to-cart' );
			$product = wc_get_product( $element_args['button_product_id_1'] );

			if ( $product ) {
				$button_classes .= implode(
					' ',
					array_filter(
						array(
							' button',
							' product_type_' . $product->get_type(),
							$product->is_purchasable() && $product->is_in_stock() ? ' add_to_cart_button' : '',
							$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? ' ajax_add_to_cart' : '',
						)
					)
				);
			}
		}

		?>
			<div class="xts-button-wrapper<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo wp_kses( $wrapper_attrs, true ); ?>>
				<?php if ( $product ) : ?>
					<a class="xts-button<?php echo esc_attr( $button_classes ); ?>" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $element_args['button_product_id_1'] ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>" rel="nofollow">
						<span class="xts-button-text<?php echo esc_attr( $button_text_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>button_text">
							<?php echo wp_kses( $element_args['button_text'], xts_get_allowed_html() ); ?>
						</span>

						<?php if ( $icon_output ) : ?>
							<span class="xts-button-icon<?php echo esc_attr( $button_icon_classes ); ?>">
								<?php echo wp_kses( $icon_output, 'xts_media' ); ?>
							</span>
						<?php endif; ?>
					</a>
				<?php else : ?>
					<a class="xts-button<?php echo esc_attr( $button_classes ); ?>" <?php echo wp_kses( $link_attrs, true ); ?>>
						<span class="xts-button-text<?php echo esc_attr( $button_text_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>button_text">
							<?php echo wp_kses( $element_args['button_text'], xts_get_allowed_html() ); ?>
						</span>

						<?php if ( $icon_output ) : ?>
							<span class="xts-button-icon<?php echo esc_attr( $button_icon_classes ); ?>">
								<?php echo wp_kses( $icon_output, 'xts_media' ); ?>
							</span>
						<?php endif; ?>
					</a>
				<?php endif; ?>
			</div>
		<?php
	}
}

