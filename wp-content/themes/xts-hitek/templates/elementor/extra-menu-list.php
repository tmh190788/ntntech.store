<?php
/**
 * Extra menu list template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_extra_menu_list_template' ) ) {
	/**
	 * Extra menu list template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_extra_menu_list_template( $element_args ) {
		$default_args = array(
			'title'       => '',
			'link'        => '',
			'label'       => '',
			'label_color' => 'primary',
		);

		$element_args = wp_parse_args( $element_args, $default_args ); // phpcs:ignore

		$general_label_classes     = '';
		$general_menu_text_classes = '';
		$general_image_output      = '';

		$general_label_classes .= ' xts-color-' . $element_args['label_color'];
		if ( xts_elementor_is_edit_mode() ) {
			$general_label_classes .= ' elementor-inline-editing';
		}

		if ( xts_elementor_is_edit_mode() ) {
			$general_menu_text_classes .= ' elementor-inline-editing';
		}

		$link_attrs = xts_get_link_attrs( $element_args['link'] );

		// Image settings.
		if ( isset( $element_args['image']['id'] ) && $element_args['image']['id'] ) {
			$image_url = xts_get_image_url( $element_args['image']['id'], 'image', $element_args );

			$general_image_output = apply_filters( 'xts_image', '<img class="xts-nav-img" src="' . esc_url( $image_url ) . '">' );
		}

		?>
		<ul class="xts-exta-menu-list xts-sub-menu">
			<li>
				<?php if ( $element_args['title'] ) : ?>
					<a <?php echo wp_kses( $link_attrs, true ); ?>>
						<?php if ( $general_image_output ) : ?>
								<?php echo wp_kses( $general_image_output, 'xts_media' ); ?>
						<?php endif; ?>

						<span class="xts-menu-text<?php echo esc_attr( $general_menu_text_classes ); ?>" data-elementor-setting-key="title">
							<?php echo wp_kses( $element_args['title'], xts_get_allowed_html() ); ?>
						</span>

						<?php if ( $element_args['label'] ) : ?>
							<span class="xts-nav-label<?php echo esc_attr( $general_label_classes ); ?>" data-elementor-setting-key="label">
								<?php echo wp_kses( $element_args['label'], xts_get_allowed_html() ); ?>
							</span>
						<?php endif; ?>
					</a>
				<?php endif; ?>

				<ul class="sub-sub-menu">
					<?php foreach ( $element_args['menu_items_repeater'] as $index => $item ) : ?>
						<?php
						$label_classes     = '';
						$menu_text_classes = '';
						$image_output      = '';

						$label_classes .= ' xts-color-' . $item['label_color'];
						if ( xts_elementor_is_edit_mode() ) {
							$label_classes .= ' elementor-inline-editing';
						}

						if ( xts_elementor_is_edit_mode() ) {
							$menu_text_classes .= ' elementor-inline-editing';
						}

						$link_attrs = xts_get_link_attrs( $item['link'] );

						// Image settings.
						if ( isset( $item['image']['id'] ) && $item['image']['id'] ) {
							$image_url = xts_get_image_url( $item['image']['id'], 'image', $item );

							$image_output = apply_filters( 'xts_image', '<img class="xts-nav-img" src="' . esc_url( $image_url ) . '">' );
						}

						?>

						<li>
							<a <?php echo wp_kses( $link_attrs, true ); ?>>
								<?php if ( $image_output ) : ?>
									<?php echo wp_kses( $image_output, 'xts_media' ); ?>
								<?php endif; ?>

								<span class="xts-menu-text<?php echo esc_attr( $menu_text_classes ); ?>" data-elementor-setting-key="menu_items_repeater.<?php echo esc_attr( $index ); ?>.title">
									<?php echo wp_kses( $item['title'], xts_get_allowed_html() ); ?>
								</span>

								<?php if ( $item['label'] ) : ?>
									<span class="xts-nav-label<?php echo esc_attr( $label_classes ); ?>" data-elementor-setting-key="menu_items_repeater.<?php echo esc_attr( $index ); ?>.label">
										<?php echo wp_kses( $item['label'], xts_get_allowed_html() ); ?>
									</span>
								<?php endif; ?>
							</a>
						</li>

					<?php endforeach; ?>
				</ul>
			</li>
		</ul>
		<?php
	}
}
