<?php
/**
 * Hotspots template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_hotspots_template' ) ) {
	/**
	 * Hotspots template
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @since 1.0.0
	 */
	function xts_hotspots_template( $element_args ) {
		$default_args = array(
			// Image.
			'image'                     => '',
			'hotspots_repeater'         => array(),

			// Style.
			'text_align'                => 'center',
			'icon_style'                => 'default',
			'color_scheme'              => 'inherit',

			// Hotspots.
			'trigger'                   => 'hover',

			// Title.
			'title_text_size'           => 'm',
			'title_color_presets'       => 'default',

			// Description.
			'description_text_size'     => 'm',
			'description_color_presets' => 'default',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		xts_enqueue_js_script( 'hotspots-element' );

		?>
		<div class="xts-spots">
			<?php echo xts_get_image_html( $element_args, 'image' ); // phpcs:ignore ?>

			<?php foreach ( $element_args['hotspots_repeater'] as $hotspot ) : ?>
				<?php
				$hotspot_default_args = array(
					// Content.
					'content_type'                => 'text',
					'title'                       => 'Title, click to edit',
					'description'                 => '',
					'image'                       => '',
					'image_custom_dimension'      => '',
					'link'                        => '#',
					'link_text'                   => 'Button',
					'product'                     => '',

					// Position.
					'hotspot_position_horizontal' => array( 'size' => 50 ),
					'hotspot_position_vertical'   => array( 'size' => 50 ),
					'content_position'            => 'left',
				);

				$hotspot = wp_parse_args( $hotspot, $hotspot_default_args );

				$content_classes      = '';
				$hotspot_icon_classes = '';
				$title_classes        = '';
				$description_classes  = '';
				$link_attrs           = '';
				$wrapper_classes      = '';
				$image_url            = '';
				$image_attrs          = '';

				// Icon classes.
				$hotspot_icon_classes .= ' xts-style-' . $element_args['icon_style'];

				// Wrapper classes.
				$wrapper_classes .= ' elementor-repeater-item-' . $hotspot['_id'];
				$wrapper_classes .= ' xts-event-' . $element_args['trigger'];

				// Content classes.
				$content_classes .= ' xts-type-' . $hotspot['content_type'];
				$content_classes .= ' xts-position-' . $hotspot['content_position'];
				$content_classes .= ' xts-textalign-' . $element_args['text_align'];
				if ( 'opened' === $element_args['trigger'] ) {
					$content_classes .= ' xts-opened';
				}
				if ( 'inherit' !== $element_args['color_scheme'] ) {
					$content_classes .= ' xts-scheme-' . $element_args['color_scheme'];
				}

				// Title classes.
				if ( 'default' !== $element_args['title_color_presets'] ) {
					$title_classes .= ' xts-textcolor-' . $element_args['title_color_presets'];
				}
				if ( 'default' !== $element_args['title_text_size'] ) {
					$title_classes .= ' xts-fontsize-' . $element_args['title_text_size'];
				}

				// Description classes.
				if ( 'default' !== $element_args['description_color_presets'] ) {
					$description_classes .= ' xts-textcolor-' . $element_args['description_color_presets'];
				}
				if ( 'default' !== $element_args['description_text_size'] ) {
					$description_classes .= ' xts-fontsize-' . $element_args['description_text_size'];
				}

				// Link settings.
				if ( $hotspot['link'] ) {
					$link_attrs = xts_get_link_attrs(
						array(
							'url'         => $hotspot['link']['url'],
							'is_external' => $hotspot['link']['is_external'],
							'nofollow'    => $hotspot['link']['nofollow'],
							'class'       => 'xts-spot-btn xts-button xts-style-link xts-color-primary',
						)
					);
				}

				// Product settings.
				$product      = '';
				$rating_count = '';
				$average      = '';
				$button_args  = '';
				if ( xts_is_woocommerce_installed() && $hotspot['product'] ) {
					$product      = wc_get_product( $hotspot['product'] );
					$rating_count = $product->get_rating_count();
					$average      = $product->get_average_rating();

					$button_args = array(
						'classes' => implode(
							' ',
							array_filter(
								array(
									'xts-spot-btn xts-button xts-style-link xts-color-primary',
									'product_type_' . $product->get_type(),
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
								)
							)
						),
						'attrs'   => wc_implode_html_attributes(
							array(
								'data-product_id' => $product->get_id(),
								'rel'             => 'nofollow',
							)
						),
					);
				}

				// Image settings.
				$custom_image_size = isset( $hotspot['image_custom_dimension']['width'] ) && $hotspot['image_custom_dimension']['width'] ? $hotspot['image_custom_dimension'] : array(
					'width'  => 128,
					'height' => 128,
				);

				if ( isset( $hotspot['image']['id'] ) && $hotspot['image']['id'] ) {
					$image_url = xts_get_image_url( $hotspot['image']['id'], 'image', $hotspot );
				}

				if ( xts_is_svg( $image_url ) ) {
					$image_attrs .= ' width="' . $custom_image_size['width'] . '" height="' . $custom_image_size['height'] . '"';
				} elseif ( isset( $hotspot['image']['url'] ) && $hotspot['image']['url'] ) {
					$image_url = $hotspot['image']['url'];
				}

				$image_output = apply_filters( 'xts_image', '<img src="' . esc_url( $image_url ) . '" ' . $image_attrs . '>' );

				?>

				<div class="xts-spot<?php echo esc_attr( $wrapper_classes ); ?>">
					<div class="xts-spot-icon<?php echo esc_attr( $hotspot_icon_classes ); ?>"></div>

					<div class="xts-spot-content xts-dropdown<?php echo esc_attr( $content_classes ); ?>">
						<div class="xts-dropdown-inner xts-reset-mb-10 xts-reset-all-last">
							<?php if ( 'product' === $hotspot['content_type'] && $product ) : ?>
								<div class="xts-spot-image">
									<a href="<?php echo esc_url( get_permalink( $product->get_ID() ) ); ?>">
										<?php echo wp_kses( $product->get_image(), 'xts_media' ); ?>
									</a>
								</div>

								<h3 class="xts-spot-title xts-entities-title<?php echo esc_attr( $title_classes ); ?>">
									<a href="<?php echo esc_url( get_permalink( $product->get_ID() ) ); ?>">
										<?php echo esc_html( $product->get_title() ); ?>
									</a>
								</h3>

								<?php if ( wc_review_ratings_enabled() ) : ?>
									<?php echo wc_get_rating_html( $average, $rating_count ); // phpcs:ignore ?>
								<?php endif; ?>

								<div class="price">
									<?php echo apply_filters( 'woocommerce_xts_get_price_html', $product->get_price_html() ); // phpcs:ignore XSS ok. ?>
								</div>

								<div class="xts-spot-desc<?php echo esc_attr( $description_classes ); ?>">
									<?php echo do_shortcode( $product->get_short_description() ); ?>
								</div>

								<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="<?php echo esc_attr( $button_args['classes'] ); ?>" <?php echo wp_kses( $button_args['attrs'], true ); ?>>
									<?php echo esc_html( $product->add_to_cart_text() ); ?>
								</a>
							<?php else : ?>
								<?php if ( $image_url ) : ?>
									<div class="xts-spot-image">
										<?php echo wp_kses( $image_output, 'xts_media' ); ?>
									</div>
								<?php endif; ?>

								<?php if ( $hotspot['title'] ) : ?>
									<h3 class="xts-spot-title<?php echo esc_attr( $title_classes ); ?>">
										<?php echo wp_kses( $hotspot['title'], xts_get_allowed_html() ); ?>
									</h3>
								<?php endif; ?>

								<?php if ( $hotspot['description'] ) : ?>
									<div class="xts-spot-desc<?php echo esc_attr( $description_classes ); ?>">
										<?php echo do_shortcode( $hotspot['description'] ); ?>
									</div>
								<?php endif; ?>

								<?php if ( $hotspot['link'] && $hotspot['link_text'] ) : ?>
									<a <?php echo wp_kses( $link_attrs, true ); ?>>
										<?php echo esc_html( $hotspot['link_text'] ); ?>
									</a>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>

			<?php endforeach; ?>
		</div>
		<?php
	}
}
