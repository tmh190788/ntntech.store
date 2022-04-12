<?php
/**
 * Menu price template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_menu_price_template' ) ) {
	/**
	 * Menu price template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_menu_price_template( $element_args ) {
		$default_args = array(
			// Link.
			'link'                      => '',

			// Price.
			'price_text_size'           => 's',
			'price_color_presets'       => 'default',

			// Title.
			'title_text_size'           => 'xs',
			'title_color_presets'       => 'default',

			// Description.
			'description_text_size'     => 'default',
			'description_color_presets' => 'default',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$price_classes       = '';
		$title_classes       = '';
		$description_classes = '';

		// Price classes.
		$price_classes .= ' xts-textcolor-' . $element_args['price_color_presets'];
		if ( xts_elementor_is_edit_mode() ) {
			$price_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args['price_text_size'] ) {
			$price_classes .= ' xts-fontsize-' . $element_args['price_text_size'];
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

		?>
		<div class="xts-menu-price">
			<?php foreach ( $element_args['menu_price_items'] as $index => $item ) : ?>
				<?php
				$menu_price_default_args = array(
					// Content.
					'link'        => array(
						'url' => '#',
					),
					'title'       => '',
					'price'       => '',
					'description' => '',
				);

				$item = wp_parse_args( $item, $menu_price_default_args );

				$item_wrapper_classes = '';
				$onclick              = '';

				// Image settings.
				$custom_image_size = isset( $item['image_custom_dimension']['width'] ) && $item['image_custom_dimension']['width'] ? $item['image_custom_dimension'] : array(
					'width'  => 25,
					'height' => 25,
				);

				$image_output = xts_get_image_html( $item, 'image' );

				if ( xts_is_svg( $item['image']['url'] ) ) {
					$image_output = apply_filters( 'xts_image', '<img src="' . esc_url( xts_get_image_url( $item['image']['id'], 'image', $item ) ) . '" width="' . esc_attr( $custom_image_size['width'] ) . '" height="' . esc_attr( $custom_image_size['height'] ) . '">' );
				}

				// Link settings.
				if ( isset( $item['link']['url'] ) && $item['link']['url'] && ! xts_elementor_is_edit_mode() ) {
					$item_wrapper_classes .= ' xts-cursor-pointer';
					if ( 'on' === $item['link']['is_external'] ) {
						$onclick = 'window.open("' . esc_url( $item['link']['url'] ) . '","_blank")';
					} else {
						$onclick = 'window.location.href="' . esc_url( $item['link']['url'] ) . '"';
					}
				}

				?>

				<div class="xts-menu-price-item<?php echo esc_attr( $item_wrapper_classes ); ?>" onclick="<?php echo esc_js( $onclick ); ?>">
					<?php if ( $image_output ) : ?>
						<div class="xts-menu-price-img">
							<?php echo wp_kses( $image_output, 'xts_media' ); ?>
						</div>
					<?php endif; ?>

					<div class="xts-menu-price-content">
						<div class="xts-menu-price-head">
							<h4 class="xts-menu-price-title title xts-entities-title<?php echo esc_attr( $title_classes ); ?>" data-elementor-setting-key="menu_price_items.<?php echo esc_attr( $index ); ?>.title">
								<?php echo wp_kses( $item['title'], xts_get_allowed_html() ); ?>
							</h4>

							<div class="xts-menu-price-amount<?php echo esc_attr( $price_classes ); ?>" data-elementor-setting-key="menu_price_items.<?php echo esc_attr( $index ); ?>.price">
								<?php echo wp_kses( $item['price'], xts_get_allowed_html() ); ?>
							</div>
						</div>

						<div class="xts-menu-price-desc<?php echo esc_attr( $description_classes ); ?>" data-elementor-setting-key="menu_price_items.<?php echo esc_attr( $index ); ?>.description">
							<?php echo wp_kses( $item['description'], xts_get_allowed_html() ); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
