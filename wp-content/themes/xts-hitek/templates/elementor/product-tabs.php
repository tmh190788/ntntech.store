<?php
/**
 * Products tabs template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\AJAX_Response;

if ( ! function_exists( 'xts_product_tabs_template' ) ) {
	/**
	 * Products tabs template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_product_tabs_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			// Heading.
			'tabs_title'                          => '',
			'tabs_subtitle'                       => '',
			'tabs_description'                    => '',
			'heading_icon_type'                   => 'icon',
			'heading_icon'                        => '',
			'heading_icon_image'                  => '',
			'heading_icon_image_custom_dimension' => '',

			// Tabs.
			'tabs_items'                          => '',

			// Heading.
			'tabs_heading_design'                 => 'default',
			'title_align'                         => 'left',

			// Subtitle.
			'tabs_subtitle_color_presets'         => 'default',
			'tabs_subtitle_text_size'             => 'default',

			// Title.
			'tabs_title_color_presets'            => 'default',
			'tabs_title_text_size'                => 'default',

			// Description.
			'tabs_description_color_presets'      => 'default',
			'tabs_description_text_size'          => 'default',

			// Tab title.
			'title_icon_position'                 => 'left',
			'title_style'                         => 'default',
			'title_background_color_switcher'     => 'no',
			'title_shadow_switcher'               => 'no',
			'title_border_switcher'               => 'no',

			// Product design.
			'design'                              => 'inherit',
			'image_size'                          => 'woocommerce_thumbnail',
			'image_size_custom'                   => '',

			// Layout.
			'items_per_page'                      => array( 'size' => 8 ),
			'pagination'                          => 'without',
			'columns'                             => array( 'size' => 4 ),
			'columns_tablet'                      => array( 'size' => '' ),
			'columns_mobile'                      => array( 'size' => '' ),
			'spacing'                             => xts_get_default_value( 'items_gap' ),
			'masonry'                             => 'no',
			'different_sizes'                     => '0',
			'different_sizes_position'            => '2,5,8,9',

			// Carousel.
			'carousel_items'                      => array( 'size' => 4 ),
			'carousel_items_tablet'               => array( 'size' => '' ),
			'carousel_items_mobile'               => array( 'size' => '' ),
			'carousel_spacing'                    => xts_get_default_value( 'items_gap' ),
			'autoplay'                            => 'no',
			'autoplay_speed'                      => array( 'size' => 2000 ),
			'infinite_loop'                       => 'no',
			'center_mode'                         => 'no',
			'auto_height'                         => 'no',
			'init_on_scroll'                      => 'yes',
			'dots'                                => 'no',
			'dots_color_scheme'                   => 'dark',
			'arrows'                              => 'yes',
			'arrows_vertical_position'            => xts_get_default_value( 'carousel_arrows_vertical_position' ),
			'arrows_color_scheme'                 => xts_get_default_value( 'carousel_arrows_color_scheme' ),

			// Visibility.
			'countdown'                           => '0',
			'quantity'                            => '0',
			'stock_progress_bar'                  => '0',
			'categories'                          => '0',
			'product_attributes'                  => '0',
			'brands'                              => '0',
			'rating'                              => '1',

			// Extra.
			'ajax_page'                           => '',
			'animation_in_view'                   => 'no',
			'xts_animation_items'                 => '',
			'xts_animation_duration_items'        => 'normal',
			'xts_animation_delay_items'           => '',
			'lazy_loading'                        => 'no',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$title_text_classes         = '';
		$title_classes              = '';
		$tabs_header_classes        = '';
		$tabs_title_classes         = '';
		$tabs_title_text_classes    = '';
		$tabs_subtitle_classes      = '';
		$tabs_description_classes   = '';
		$tabs_title_wrapper_classes = '';
		$tabs_nav_wrapper_classes   = '';
		$icon_output                = '';

		// Title classes.
		$title_classes .= ' xts-style-' . $element_args['title_style'];
		$title_classes .= ' xts-icon-' . $element_args['title_icon_position'];
		if ( 'yes' === $element_args['title_background_color_switcher'] ) {
			$title_classes .= ' xts-with-bg-color';
		}
		if ( 'yes' === $element_args['title_shadow_switcher'] ) {
			$title_classes .= ' xts-with-shadow';
		}
		if ( 'yes' === $element_args['title_border_switcher'] ) {
			$title_classes .= ' xts-with-border';
		}

		// Tabs header wrapper classes.
		$tabs_header_classes .= ' xts-design-' . $element_args['tabs_heading_design'];

		// Tabs header wrapper classes.
		if ( 'by-sides' !== $element_args['tabs_heading_design'] ) {
			$tabs_header_classes .= ' xts-textalign-' . $element_args['title_align'];
		}

		// Title classes.
		if ( 'default' !== $element_args['tabs_title_color_presets'] ) {
			$tabs_title_classes .= ' xts-textcolor-' . $element_args['tabs_title_color_presets'];
		}
		if ( 'default' !== $element_args['tabs_title_text_size'] ) {
			$tabs_title_classes .= ' xts-fontsize-' . $element_args['tabs_title_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$tabs_title_text_classes .= ' elementor-inline-editing';
		}

		// Subtitle classes.
		if ( 'default' !== $element_args['tabs_subtitle_color_presets'] ) {
			$tabs_subtitle_classes .= ' xts-textcolor-' . $element_args['tabs_subtitle_color_presets'];
		}
		if ( 'default' !== $element_args['tabs_subtitle_text_size'] ) {
			$tabs_subtitle_classes .= ' xts-fontsize-' . $element_args['tabs_subtitle_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$tabs_subtitle_classes .= ' elementor-inline-editing';
		}

		// Description classes.
		if ( 'default' !== $element_args['tabs_description_color_presets'] ) {
			$tabs_description_classes .= ' xts-textcolor-' . $element_args['tabs_description_color_presets'];
		}
		if ( 'default' !== $element_args['tabs_description_text_size'] ) {
			$tabs_description_classes .= ' xts-fontsize-' . $element_args['tabs_description_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$tabs_description_classes .= ' elementor-inline-editing';
		}

		// Icon settings.
		$custom_image_size = isset( $element_args['heading_icon_image_custom_dimension']['width'] ) && $element_args['heading_icon_image_custom_dimension']['width'] ? $element_args['heading_icon_image_custom_dimension'] : array(
			'width'  => 25,
			'height' => 25,
		);

		if ( 'image' === $element_args['heading_icon_type'] ) {
			$icon_output = xts_get_image_html( $element_args, 'heading_icon_image' );

			if ( xts_is_svg( $element_args['heading_icon_image']['url'] ) ) {
				$icon_output = apply_filters( 'xts_image', '<img src="' . esc_url( xts_get_image_url( $element_args['heading_icon_image']['id'], 'heading_icon_image', $element_args ) ) . '" width="' . esc_attr( $custom_image_size['width'] ) . '" height="' . esc_attr( $custom_image_size['height'] ) . '">' );
			}
		} elseif ( 'icon' === $element_args['heading_icon_type'] && $element_args['heading_icon'] ) {
			$icon_output = xts_elementor_get_render_icon( $element_args['heading_icon'] );
		}

		xts_enqueue_js_script( 'product-tabs-element' );

		?>
			<div class="xts-products-tabs">
				<div class="xts-tabs-header<?php echo esc_attr( $tabs_header_classes ); ?>">
					<?php if ( $element_args['tabs_subtitle'] || $element_args['tabs_title'] ) : ?>
						<div class="xts-tabs-title-wrapper<?php echo esc_attr( $tabs_title_wrapper_classes ); ?>">
							<?php if ( $element_args['tabs_subtitle'] ) : ?>
								<div class="xts-tabs-subtitle<?php echo esc_attr( $tabs_subtitle_classes ); ?>" data-elementor-setting-key="tabs_subtitle">
									<?php echo esc_html( $element_args['tabs_subtitle'] ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $element_args['tabs_title'] ) : ?>
								<h3 class="xts-tabs-title<?php echo esc_attr( $tabs_title_classes ); ?>">
									<?php if ( $icon_output ) : ?>
										<span class="xts-tabs-title-icon">
											<?php echo wp_kses( $icon_output, 'xts_media' ); ?>
										</span>
									<?php endif; ?>

									<span class="<?php echo esc_attr( $tabs_title_text_classes ); ?>" data-elementor-setting-key="tabs_title">
										<?php echo esc_html( $element_args['tabs_title'] ); ?>
									</span>
								</h3>
							<?php endif; ?>

							<?php if ( $element_args['tabs_description'] ) : ?>
								<p class="xts-tabs-desc<?php echo esc_attr( $tabs_description_classes ); ?>" data-elementor-setting-key="tabs_title">
									<?php echo esc_html( $element_args['tabs_description'] ); ?>
								</p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="xts-nav-wrapper xts-nav-tabs-wrapper xts-mb-action-swipe<?php echo esc_attr( $tabs_nav_wrapper_classes ); ?>">
						<ul class="xts-nav xts-nav-tabs<?php echo esc_attr( $title_classes ); ?>">
							<?php foreach ( $element_args['tabs_items'] as $index => $item ) : ?>
								<?php
								$tab_classes = '';

								if ( 0 === $index ) {
									$tab_classes .= ' xts-active';
								}

								// Icon settings.
								$custom_image_size = isset( $item['image_custom_dimension']['width'] ) && $item['image_custom_dimension']['width'] ? $item['image_custom_dimension'] : array(
									'width'  => 25,
									'height' => 25,
								);

								if ( 'image' === $item['icon_type'] ) {
									$icon_output = xts_get_image_html( $item, 'image' );

									if ( xts_is_svg( $item['image']['url'] ) ) {
										$icon_output = apply_filters( 'xts_image', '<img src="' . esc_url( xts_get_image_url( $item['image']['id'], 'image', $item ) ) . '" width="' . esc_attr( $custom_image_size['width'] ) . '" height="' . esc_attr( $custom_image_size['height'] ) . '">' );
									}
								} elseif ( 'icon' === $item['icon_type'] && $item['icon'] ) {
									$icon_output = xts_elementor_get_render_icon( $item['icon'] );
								}

								// Settings.
								$args         = $element_args + $item;
								$default_args = xts_get_products_element_config();
								unset( $args['tabs_items'] );
								$encoded_atts = wp_json_encode( array_intersect_key( $args, $default_args ) );

								?>

								<li class="xts-products-tab-title<?php echo esc_attr( $tab_classes ); ?>" data-atts="<?php echo esc_attr( $encoded_atts ); ?>">
									<a href="#" class="xts-nav-link">
										<?php if ( 'underline-2' === $element_args['title_style'] ) : ?>
											<span class="xts-nav-text<?php echo esc_attr( $title_text_classes ); ?>">
												<span>
													<?php echo esc_html( $item['item_title'] ); ?>
												</span>
											</span>
										<?php else : ?>
											<span class="xts-nav-text<?php echo esc_attr( $title_text_classes ); ?>">
												<?php echo esc_html( $item['item_title'] ); ?>
											</span>
										<?php endif; ?>

										<?php if ( $icon_output ) : ?>
											<span class="xts-tab-icon">
												<?php echo wp_kses( $icon_output, 'xts_media' ); ?>
											</span>
										<?php endif; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<div class="xts-tabs-loader"></div>
				</div>

				<?php echo xts_get_products_tab_template( $element_args + $element_args['tabs_items'][0] ); // phpcs:ignore ?>
			</div>
		<?php
	}
}

if ( ! function_exists( 'xts_get_products_tab_template' ) ) {
	/**
	 * Products tab template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @return array|false|string
	 */
	function xts_get_products_tab_template( $element_args ) {
		$is_ajax         = xts_is_ajax();
		$wrapper_classes = '';

		$element_args['force_not_ajax'] = 'yes';

		if ( 'yes' === $element_args['animation_in_view'] ) {
			$wrapper_classes .= ' xts-tab-in-view-animation';
		}

		ob_start();

		?>
			<?php if ( ! $is_ajax ) : ?>
				<div class="xts-products-tab-content<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php endif; ?>

				<?php xts_products_template( $element_args ); ?>

			<?php if ( ! $is_ajax ) : ?>
				</div>
			<?php endif; ?>
		<?php

		if ( $is_ajax ) {
			return array(
				'html' => ob_get_clean(),
			);

		} else {
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'xts_get_products_tab_element_ajax' ) ) {
	/**
	 * Return products on AJAX
	 *
	 * @since 1.0.0
	 */
	function xts_get_products_tab_element_ajax() {
		if ( $_POST['atts'] ) { // phpcs:ignore
			$atts = $_POST['atts']; // phpcs:ignore

			AJAX_Response::send_response( xts_get_products_tab_template( $atts ) );
		}
	}

	add_action( 'wp_ajax_xts_get_products_tab_element', 'xts_get_products_tab_element_ajax' );
	add_action( 'wp_ajax_nopriv_xts_get_products_tab_element', 'xts_get_products_tab_element_ajax' );
}
