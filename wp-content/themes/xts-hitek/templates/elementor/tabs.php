<?php
/**
 * Tabs template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_tabs_template' ) ) {
	/**
	 * Tabs template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_tabs_template( $element_args ) {
		$default_args = array(
			// Content.
			'tabs_items'                            => '',

			// Title.
			'title'                                 => '',
			'title_style'                           => 'default',
			'title_align'                           => 'left',
			'icon_position'                         => 'left',
			'title_background_color_switcher'       => 'no',
			'title_shadow_switcher'                 => 'no',
			'title_border_switcher'                 => 'no',

			// Description.
			'description'                           => '',
			'description_text_size'                 => 'default',
			'description_color_presets'             => 'default',
			'description_align'                     => 'left',
			'description_background_color_switcher' => 'no',
			'description_animations'                => 'fade-in',
		);

		$element_args = wp_parse_args( $element_args, $default_args ); // phpcs:ignore

		$title_text_classes          = '';
		$description_classes         = '';
		$description_wrapper_classes = '';
		$title_wrapper_classes       = '';
		$title_classes               = '';

		// Title classes.
		$title_classes .= ' xts-style-' . $element_args['title_style'];
		$title_classes .= ' xts-icon-' . $element_args['icon_position'];
		if ( 'yes' === $element_args['title_background_color_switcher'] ) {
			$title_classes .= ' xts-with-bg-color';
		}
		if ( 'yes' === $element_args['title_shadow_switcher'] ) {
			$title_classes .= ' xts-with-shadow';
		}
		if ( 'yes' === $element_args['title_border_switcher'] ) {
			$title_classes .= ' xts-with-border';
		}

		// Title wrapper classes.
		$title_wrapper_classes .= ' xts-textalign-' . $element_args['title_align'];

		// Description wrapper classes.
		$description_wrapper_classes .= ' xts-textalign-' . $element_args['description_align'];
		$description_wrapper_classes .= ' xts-anim-' . $element_args['description_animations'];
		if ( 'default' !== $element_args['description_color_presets'] ) {
			$description_wrapper_classes .= ' xts-textcolor-' . $element_args['description_color_presets'];
		}
		if ( 'default' !== $element_args['description_text_size'] ) {
			$description_wrapper_classes .= ' xts-fontsize-' . $element_args['description_text_size'];
		}
		if ( 'yes' === $element_args['description_background_color_switcher'] ) {
			$description_wrapper_classes .= ' xts-with-bg-color';
		}

		// Description classes.
		if ( xts_elementor_is_edit_mode() ) {
			$description_classes .= ' elementor-inline-editing';
		}

		xts_enqueue_js_script( 'tabs-element' );

		?>
			<div class="xts-tabs">
				<div class="xts-nav-wrapper xts-nav-tabs-wrapper xts-mb-action-swipe<?php echo esc_attr( $title_wrapper_classes ); ?>">
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

							?>

							<li class="<?php echo esc_attr( $tab_classes ); ?>" data-tab-index="<?php echo esc_attr( $index ); ?>">
								<a href="#" class="xts-nav-link">
									<span class="xts-nav-text<?php echo esc_attr( $title_text_classes ); ?>">
										<?php echo esc_html( $item['item_title'] ); ?>
									</span>

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

				<div class="xts-tab-content-wrapper<?php echo esc_attr( $description_wrapper_classes ); ?>">
					<?php foreach ( $element_args['tabs_items'] as $index => $item ) : ?>
						<?php
						$content_classes = '';

						$content_classes .= ' elementor-repeater-item-' . $item['_id'];
						if ( 0 === $index ) {
							$content_classes .= ' xts-active xts-in';
						}

						?>

						<div class="xts-tab-content<?php echo esc_attr( $content_classes ); ?>" data-tab-index="<?php echo esc_attr( $index ); ?>">
							<?php if ( 'text' === $item['content_type'] && $item['item_desc'] ) : ?>
								<div class="xts-tab-desc<?php echo esc_attr( $description_classes ); ?>" data-elementor-setting-key="tabs_items.<?php echo esc_attr( $index ); ?>.item_desc">
									<?php echo do_shortcode( $item['item_desc'] ); ?>
								</div>
							<?php elseif ( 'html_block' === $item['content_type'] && $item['html_block_id'] ) : ?>
								<div class="xts-tab-desc">
									<?php echo xts_get_html_block_content( $item['html_block_id'] ); // phpcs:ignore ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php
	}
}
