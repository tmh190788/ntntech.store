<?php
/**
 * Price plan template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_price_plan_template' ) ) {
	/**
	 * Price plan template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_price_plan_template( $element_args ) {
		$default_args = array(
			// General.
			'text_align'                       => 'center',
			'price_plan_color_scheme_switcher' => 'no',
			'price_plan_color_scheme'          => 'inherit',
			'price_plan_color_scheme_hover'    => 'inherit',
			'price_plan_background_switcher'   => 'no',
			'price_plan_shadow_switcher'       => 'no',
			'price_plan_border_switcher'       => 'no',
			'featured_item'                    => 'no',

			// Label.
			'label_switcher'                   => 'yes',
			'label_text'                       => 'Popular',
			'label_color'                      => '',
			'label_align'                      => 'right',
			'custom_label_color_switcher'      => 'no',

			// Title.
			'title'                            => 'Title',
			'title_text_size'                  => 'm',
			'title_color_presets'              => 'default',

			// Description.
			'description'                      => 'Description',
			'description_text_size'            => 'default',
			'description_color_presets'        => 'default',

			// Position.
			'features_text_text_size'          => 'default',
			'features_text_color_presets'      => 'default',

			// Header.
			'icon_type'                        => 'icon',
			'icon'                             => '',
			'icon_size'                        => 'm',
			'icon_color_switcher'              => 'no',
			'image_custom_dimension'           => '',

			// Amount.
			'pricing_text_size'                => 'l',
			'pricing_color_presets'            => 'default',

			// Subtitle.
			'subtitle_text_size'               => 'm',
			'subtitle_color_presets'           => 'default',

			// Pricing.
			'currency_symbol'                  => '$',
			'title_1'                          => 'per month',
			'price_1'                          => '39',
			'fraction_1'                       => '99',
			'title_2'                          => 'per year',
			'price_2'                          => '139',
			'fraction_2'                       => '99',
			'title_3'                          => 'lifetime',
			'price_3'                          => '239',
			'fraction_3'                       => '99',

			// Product.
			'button_link_type'                 => 'link',
			'button_product_id_1'              => '',
			'button_product_id_2'              => '',
			'button_product_id_3'              => '',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes          = '';
		$label_classes            = '';
		$icon_classes             = '';
		$title_classes            = '';
		$description_classes      = '';
		$pricing_classes          = '';
		$pricing_subtitle_classes = '';
		$features_list_classes    = '';
		$icon_output              = '';

		// Pricing classes.
		if ( 'default' !== $element_args['pricing_color_presets'] ) {
			$pricing_classes .= ' xts-textcolor-' . $element_args['pricing_color_presets'];
		}
		$pricing_classes .= ' xts-fontsize-' . $element_args['pricing_text_size'];

		// Subtitle classes.
		if ( 'default' !== $element_args['subtitle_color_presets'] ) {
			$pricing_subtitle_classes .= ' xts-textcolor-' . $element_args['subtitle_color_presets'];
		}
		$pricing_subtitle_classes .= ' xts-fontsize-' . $element_args['subtitle_text_size'];

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

		// Features list classes.
		if ( 'default' !== $element_args['features_text_color_presets'] ) {
			$features_list_classes .= ' xts-textcolor-' . $element_args['features_text_color_presets'];
		}
		if ( 'default' !== $element_args['features_text_text_size'] ) {
			$features_list_classes .= ' xts-fontsize-' . $element_args['features_text_text_size'];
		}

		// Wrapper classes.
		$wrapper_classes .= ' xts-textalign-' . $element_args['text_align'];
		if ( 'yes' === $element_args['featured_item'] ) {
			$wrapper_classes .= ' xts-featured';
		}
		if ( 'yes' === $element_args['price_plan_color_scheme_switcher'] ) {
			if ( 'inherit' !== $element_args['price_plan_color_scheme'] ) {
				$wrapper_classes .= ' xts-scheme-' . $element_args['price_plan_color_scheme'];
			}
			if ( 'inherit' !== $element_args['price_plan_color_scheme_hover'] ) {
				$wrapper_classes .= ' xts-scheme-hover-' . $element_args['price_plan_color_scheme_hover'];
			}
		}
		if ( 'yes' === $element_args['price_plan_background_switcher'] ) {
			$wrapper_classes .= ' xts-with-bg-color';
		}
		if ( 'yes' === $element_args['price_plan_shadow_switcher'] ) {
			$wrapper_classes .= ' xts-with-shadow';
		}
		if ( 'yes' === $element_args['price_plan_border_switcher'] ) {
			$wrapper_classes .= ' xts-with-border';
		}

		// Label classes.
		if ( xts_elementor_is_edit_mode() ) {
			$label_classes .= ' elementor-inline-editing';
		}
		$label_classes .= ' xts-' . $element_args['label_align'];
		if ( 'yes' !== $element_args['custom_label_color_switcher'] && 'yes' === $element_args['label_switcher'] ) {
			$label_classes .= ' xts-bgcolor-' . $element_args['label_color'];
		}

		// Icon classes.
		if ( $element_args['icon_size'] ) {
			$icon_classes .= ' xts-size-' . $element_args['icon_size'];
		}
		if ( 'yes' === $element_args['icon_color_switcher'] ) {
			$icon_classes .= ' xts-with-color';
		}

		// Icon settings.
		$custom_image_size = isset( $element_args['image_custom_dimension']['width'] ) && $element_args['image_custom_dimension']['width'] ? $element_args['image_custom_dimension'] : array(
			'width'  => 128,
			'height' => 128,
		);

		if ( 'image' === $element_args['icon_type'] ) {
			$icon_output = xts_get_image_html( $element_args, 'image' );

			if ( xts_is_svg( $element_args['image']['url'] ) ) {
				$icon_output = '<div class="xts-image-type-svg" style="width:' . esc_attr( $custom_image_size['width'] ) . 'px; height:' . esc_attr( $custom_image_size['height'] ) . 'px;">' . xts_get_svg( '', '', xts_get_image_url( $element_args['image']['id'], 'image', $element_args ) ) . '</div>';
			}
		} elseif ( 'icon' === $element_args['icon_type'] && $element_args['icon'] ) {
			$icon_output = xts_elementor_get_render_icon( $element_args['icon'] );
		}

		// Pricing switcher.
		$pricing_data = array(
			'price_1' => array(
				'title'       => $element_args['title_1'],
				'price'       => $element_args['price_1'],
				'fraction'    => $element_args['fraction_1'],
				'button_data' => xts_get_price_plan_product_button_data( $element_args['button_product_id_1'] ),
			),
			'price_2' => array(
				'title'       => $element_args['title_2'],
				'price'       => $element_args['price_2'],
				'fraction'    => $element_args['fraction_2'],
				'button_data' => xts_get_price_plan_product_button_data( $element_args['button_product_id_2'] ),
			),
			'price_3' => array(
				'title'       => $element_args['title_3'],
				'price'       => $element_args['price_3'],
				'fraction'    => $element_args['fraction_3'],
				'button_data' => xts_get_price_plan_product_button_data( $element_args['button_product_id_3'] ),
			),
		);

		?>
			<div class="xts-price-plan<?php echo esc_attr( $wrapper_classes ); ?>">
				<div class="xts-plan-overlay xts-fill"></div>

				<?php if ( $element_args['label_text'] ) : ?>
					<div class="xts-plan-label<?php echo esc_attr( $label_classes ); ?>" data-elementor-setting-key="label_text">
						<?php echo wp_kses( $element_args['label_text'], xts_get_allowed_html() ); ?>
					</div>
				<?php endif; ?>

				<div class="xts-plan-inner xts-reset-mb-10 xts-reset-all-last">

					<?php if ( $icon_output ) : ?>
						<div class="xts-plan-icon<?php echo esc_attr( $icon_classes ); ?>">
							<?php echo wp_kses( $icon_output, 'xts_media' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $element_args['title'] ) : ?>
						<h4 class="xts-plan-title title<?php echo esc_attr( $title_classes ); ?>" data-elementor-setting-key="title">
							<?php echo wp_kses( $element_args['title'], xts_get_allowed_html() ); ?>
						</h4>
					<?php endif; ?>

					<?php if ( $element_args['description'] ) : ?>
						<p class="xts-plan-desc<?php echo esc_attr( $description_classes ); ?>" data-elementor-setting-key="description">
							<?php echo wp_kses( $element_args['description'], xts_get_allowed_html() ); ?>
						</p>
					<?php endif; ?>

					<div class="xts-plan-pricing<?php echo esc_attr( $pricing_classes ); ?>" data-pricing='<?php echo wp_json_encode( $pricing_data ); ?>'>
						<?php if ( $element_args['currency_symbol'] ) : ?>
							<span class="xts-plan-symbol">
								<?php echo esc_html( $element_args['currency_symbol'] ); ?>
							</span>
						<?php endif; ?>

						<?php if ( $element_args['price_1'] || $element_args['price_2'] || $element_args['price_3'] ) : ?>
							<span class="xts-plan-price">
								<?php echo esc_html( $element_args['price_1'] ); ?>
							</span>
						<?php endif; ?>

						<?php if ( $element_args['fraction_1'] || $element_args['fraction_2'] || $element_args['fraction_3'] ) : ?>
							<span class="xts-plan-fraction">
								<?php echo esc_html( $element_args['fraction_1'] ); ?>
							</span>
						<?php endif; ?>
					</div>

					<?php if ( $element_args['title_1'] || $element_args['title_2'] || $element_args['title_3'] ) : ?>
						<div class="xts-plan-pricing-subtitle<?php echo esc_attr( $pricing_subtitle_classes ); ?>">
							<?php echo esc_html( $element_args['title_1'] ); ?>
						</div>
					<?php endif; ?>

					<ul class="xts-plan-features<?php echo esc_attr( $features_list_classes ); ?>">
						<?php foreach ( $element_args['features_list'] as $index => $item ) : ?>
							<?php
							$features_default_args = array(
								// Content.
								'item_icon' => '',
								'item_text' => 'Plan advantage',
							);

							$item = wp_parse_args( $item, $features_default_args );

							$featured_text_classes = '';

							if ( xts_elementor_is_edit_mode() ) {
								$featured_text_classes .= ' elementor-inline-editing';
							}

							?>
							<li class="elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>">
								<?php if ( $item['item_icon'] ) : ?>
									<?php echo xts_elementor_get_render_icon( $item['item_icon'] ); // phpcs:ignore ?>
								<?php endif; ?>

								<?php if ( $item['item_text'] ) : ?>
									<span class="<?php echo esc_attr( $featured_text_classes ); ?>" data-elementor-setting-key="features_list.<?php echo esc_attr( $index ); ?>.item_text">
										<?php echo do_shortcode( $item['item_text'] ); ?>
									</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>

					<?php if ( $element_args['button_text'] ) : ?>
						<?php xts_button_template( $element_args ); ?>
					<?php endif; ?>
				</div>
			</div>
		<?php
	}
}

if ( ! function_exists( 'xts_get_price_plan_product_button_data' ) ) {
	/**
	 * Get button data
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id Product id.
	 *
	 * @return array|void
	 */
	function xts_get_price_plan_product_button_data( $id ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$product = wc_get_product( $id );

		if ( ! $product ) {
			return;
		}

		return array(
			'product_id'  => $product->get_id(),
			'product_sku' => $product->get_sku(),
			'href'        => $product->add_to_cart_url(),
		);
	}
}
