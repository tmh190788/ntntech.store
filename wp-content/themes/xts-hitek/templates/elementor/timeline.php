<?php
/**
 * Timeline template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_timeline_template' ) ) {
	/**
	 * Timeline template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_timeline_template( $element_args ) {
		$default_args = array(
			// Items.
			'timeline_items'                      => array(),

			// First item.
			'first_color_scheme'                  => 'dark',
			'first_item_text_align'               => 'center',
			'first_item_text_align_tablet'        => '',
			'first_item_text_align_mobile'        => '',
			'first_timeline_background_switcher'  => 'no',
			'first_timeline_shadow_switcher'      => 'no',

			// Second item.
			'second_color_scheme'                 => 'dark',
			'second_item_text_align'              => 'center',
			'second_item_text_align_tablet'       => '',
			'second_item_text_align_mobile'       => '',
			'second_timeline_background_switcher' => 'no',
			'second_timeline_shadow_switcher'     => 'no',

			// Breakpoint.
			'breakpoint_text_size'                => 's',
			'breakpoint_color_presets'            => 'white',
			'breakpoint_background_switcher'      => 'yes',
			'breakpoint_shadow_switcher'          => 'no',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$first_item_classes  = '';
		$second_item_classes = '';
		$breakpoint_classes  = '';

		// First item classes.
		$first_item_classes .= ' xts-textalign-' . $element_args['first_item_text_align'];
		if ( $element_args['first_item_text_align_tablet'] ) {
			$first_item_classes .= ' xts-textalign-md-' . $element_args['first_item_text_align_tablet'];
		}
		if ( $element_args['first_item_text_align_mobile'] ) {
			$first_item_classes .= ' xts-textalign-sm-' . $element_args['first_item_text_align_mobile'];
		}
		if ( 'yes' === $element_args['first_timeline_background_switcher'] ) {
			$first_item_classes .= ' xts-with-bg-color';
		}
		if ( 'yes' === $element_args['first_timeline_shadow_switcher'] ) {
			$first_item_classes .= ' xts-with-shadow';
		}
		if ( 'inherit' !== $element_args['first_color_scheme'] ) {
			$first_item_classes .= ' xts-scheme-' . $element_args['first_color_scheme'];
		}

		// Second item classes.
		$second_item_classes .= ' xts-textalign-' . $element_args['second_item_text_align'];
		if ( $element_args['second_item_text_align_tablet'] ) {
			$second_item_classes .= ' xts-textalign-md-' . $element_args['second_item_text_align_tablet'];
		}
		if ( $element_args['second_item_text_align_mobile'] ) {
			$second_item_classes .= ' xts-textalign-sm-' . $element_args['second_item_text_align_mobile'];
		}
		if ( 'yes' === $element_args['second_timeline_background_switcher'] ) {
			$second_item_classes .= ' xts-with-bg-color';
		}
		if ( 'yes' === $element_args['second_timeline_shadow_switcher'] ) {
			$second_item_classes .= ' xts-with-shadow';
		}
		if ( 'inherit' !== $element_args['second_color_scheme'] ) {
			$second_item_classes .= ' xts-scheme-' . $element_args['second_color_scheme'];
		}

		// Breakpoint classes.
		if ( 'default' !== $element_args['breakpoint_color_presets'] ) {
			$breakpoint_classes .= ' xts-textcolor-' . $element_args['breakpoint_color_presets'];
		}
		if ( 'default' !== $element_args['breakpoint_text_size'] ) {
			$breakpoint_classes .= ' xts-fontsize-' . $element_args['breakpoint_text_size'];
		}
		if ( 'yes' === $element_args['breakpoint_background_switcher'] ) {
			$breakpoint_classes .= ' xts-with-bg-color';
		}
		if ( 'yes' === $element_args['breakpoint_shadow_switcher'] ) {
			$breakpoint_classes .= ' xts-with-shadow';
		}

		?>
			<div class="xts-timeline">
				<div class="xts-timeline-line xts-fill">
					<span class="xts-timeline-dot xts-dot-start"></span>
					<span class="xts-timeline-dot xts-dot-end"></span>
				</div>

				<?php foreach ( $element_args['timeline_items'] as $index => $item ) : ?>
					<?php
					$item_classes = '';

					$item          = $item + $element_args;
					$item['index'] = $index;

					// Item classes.
					$item_classes .= ' xts-timeline-' . $item['items_type'];
					if ( 'yes' === $item['items_reverse'] ) {
						$item_classes .= ' xts-reverse';
					}
					if ( 'yes' === $item['items_reverse_mobile'] ) {
						$item_classes .= ' xts-reverse-md';
					}

					?>

					<div class="<?php echo esc_attr( $item_classes ); ?>">

						<?php if ( 'items' === $item['items_type'] ) : ?>
							<div class="xts-timeline-dot"></div>
							<?php if ( $item['first_subtitle'] || $item['first_title'] || $item['first_description'] || $item['first_image']['id'] || $item['first_html_block_id'] ) : ?>
								<div class="xts-timeline-item xts-timeline-item-first xts-reset-all-last xts-reset-mb-10<?php echo esc_attr( $first_item_classes ); ?>">
									<?php xts_timeline_item_template( $item, 'first' ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $item['second_subtitle'] || $item['second_title'] || $item['second_description'] || $item['second_image']['id'] || $item['second_html_block_id'] ) : ?>
								<div class="xts-timeline-item xts-timeline-item-second xts-reset-all-last xts-reset-mb-10<?php echo esc_attr( $second_item_classes ); ?>">
									<?php xts_timeline_item_template( $item, 'second' ); ?>
								</div>
							<?php endif; ?>
						<?php elseif ( 'breakpoint' === $item['items_type'] && $item['breakpoint_text'] ) : ?>
							<div class="xts-timeline-breakpoint-title<?php echo esc_attr( $breakpoint_classes ); ?>">
								<?php echo wp_kses( $item['breakpoint_text'], xts_get_allowed_html() ); ?>
							</div>
						<?php endif; ?>

					</div>
				<?php endforeach; ?>
			</div>
		<?php
	}
}

if ( ! function_exists( 'xts_timeline_item_template' ) ) {
	/**
	 * Timeline item template
	 *
	 * @since 1.0.0
	 *
	 * @param array  $element_args Associative array of arguments.
	 * @param string $key Unique key.
	 */
	function xts_timeline_item_template( $element_args, $key ) {
		$default_args = array(
			// Title.
			$key . '_title'                     => '',
			$key . '_title_text_size'           => 'm',
			$key . '_title_color_presets'       => 'default',
			$key . '_title_tag'                 => 'h4',

			// Subtitle.
			$key . '_subtitle'                  => '',
			$key . '_subtitle_text_size'        => 's',
			$key . '_subtitle_color_presets'    => 'default',

			// Description.
			$key . '_description'               => '',
			$key . '_description_text_size'     => 'default',
			$key . '_description_color_presets' => 'default',

			// Image.
			$key . '_image'                     => '',

			// Html block.
			$key . '_html_block_id'             => '',

			// Extra.
			'index'                             => '',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$title_classes       = '';
		$subtitle_classes    = '';
		$description_classes = '';
		$inline_editing_key  = 'timeline_items.' . $element_args['index'] . '.' . $key;

		// Title classes.
		if ( xts_elementor_is_edit_mode() ) {
			$title_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args[ $key . '_title_color_presets' ] ) {
			$title_classes .= ' xts-textcolor-' . $element_args[ $key . '_title_color_presets' ];
		}
		if ( 'default' !== $element_args[ $key . '_title_text_size' ] ) {
			$title_classes .= ' xts-fontsize-' . $element_args[ $key . '_title_text_size' ];
		}

		// Subtitle classes.
		if ( xts_elementor_is_edit_mode() ) {
			$subtitle_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args[ $key . '_subtitle_color_presets' ] ) {
			$subtitle_classes .= ' xts-textcolor-' . $element_args[ $key . '_subtitle_color_presets' ];
		}
		if ( 'default' !== $element_args[ $key . '_subtitle_text_size' ] ) {
			$subtitle_classes .= ' xts-fontsize-' . $element_args[ $key . '_subtitle_text_size' ];
		}

		// Description classes.
		if ( xts_elementor_is_edit_mode() ) {
			$description_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args[ $key . '_description_color_presets' ] ) {
			$description_classes .= ' xts-textcolor-' . $element_args[ $key . '_description_color_presets' ];
		}
		if ( 'default' !== $element_args[ $key . '_description_text_size' ] ) {
			$description_classes .= ' xts-fontsize-' . $element_args[ $key . '_description_text_size' ];
		}

		// Image settings.
		$image_output = xts_get_image_html( $element_args, $key . '_image' );

		?>

		<?php if ( 'text' === $element_args[ $key . '_content_type' ] ) : ?>
			<?php if ( $image_output ) : ?>
				<div class="xts-timeline-image">
					<?php echo wp_kses( $image_output, 'xts_media' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $element_args[ $key . '_subtitle' ] ) : ?>
				<div class="xts-timeline-subtitle<?php echo esc_attr( $subtitle_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>_subtitle">
					<?php echo wp_kses( $element_args[ $key . '_subtitle' ], xts_get_allowed_html() ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $element_args[ $key . '_title' ] ) : ?>
				<<?php echo esc_attr( $element_args[ $key . '_title_tag' ] ); ?> class="xts-timeline-title title<?php echo esc_attr( $title_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>_title">
					<?php echo wp_kses( $element_args[ $key . '_title' ], xts_get_allowed_html() ); ?>
				</<?php echo esc_attr( $element_args[ $key . '_title_tag' ] ); ?>>
			<?php endif; ?>

			<?php if ( $element_args[ $key . '_description' ] ) : ?>
				<div class="xts-timeline-desc<?php echo esc_attr( $description_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>_description">
					<?php echo do_shortcode( $element_args[ $key . '_description' ] ); ?>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<?php if ( $element_args[ $key . '_html_block_id' ] ) : ?>
				<div class="xts-timeline-html-block">
					<?php echo xts_get_html_block_content( $element_args[ $key . '_html_block_id' ] ); // phpcs:ignore ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php
	}
}
