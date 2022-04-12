<?php
/**
 * Working hours template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}


if ( ! function_exists( 'xts_working_hours_template' ) ) {
	/**
	 * Working hours template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_working_hours_template( $element_args ) {
		$default_args = array(
			// General.
			'items'                     => array(),

			// General.
			'style'                     => 'default',

			// Icon.
			'icon_color_presets'        => 'primary',
			'icon_text_size'            => 's',

			// Title.
			'title_color_presets'       => 'default',
			'title_text_size'           => 's',
			'tag'                       => 'h4',

			// Time.
			'time_color_presets'        => 'primary',
			'time_text_size'            => 's',

			// Description.
			'description_color_presets' => 'default',
			'description_text_size'     => 'default',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes     = '';
		$title_classes       = '';
		$icon_classes        = '';
		$time_classes        = '';
		$description_classes = '';

		// Wrapper classes.
		$wrapper_classes .= ' xts-style-' . $element_args['style'];

		// Icon classes.
		if ( 'default' !== $element_args['icon_color_presets'] ) {
			$icon_classes .= ' xts-textcolor-' . $element_args['icon_color_presets'];
		}
		if ( 'default' !== $element_args['icon_text_size'] ) {
			$icon_classes .= ' xts-fontsize-' . $element_args['icon_text_size'];
		}

		// Title classes.
		if ( 'default' !== $element_args['title_color_presets'] ) {
			$title_classes .= ' xts-textcolor-' . $element_args['title_color_presets'];
		}
		if ( 'default' !== $element_args['title_text_size'] ) {
			$title_classes .= ' xts-fontsize-' . $element_args['title_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$title_classes .= ' elementor-inline-editing';
		}

		// time classes.
		if ( 'default' !== $element_args['time_color_presets'] ) {
			$time_classes .= ' xts-textcolor-' . $element_args['time_color_presets'];
		}
		if ( 'default' !== $element_args['time_text_size'] ) {
			$time_classes .= ' xts-fontsize-' . $element_args['time_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$time_classes .= ' elementor-inline-editing';
		}

		// Description classes.
		if ( 'default' !== $element_args['description_color_presets'] ) {
			$description_classes .= ' xts-textcolor-' . $element_args['description_color_presets'];
		}
		if ( 'default' !== $element_args['description_text_size'] ) {
			$description_classes .= ' xts-fontsize-' . $element_args['description_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$description_classes .= ' elementor-inline-editing';
		}

		?>
			<div class="xts-work-hours<?php echo esc_attr( $wrapper_classes ); ?>">
				<?php foreach ( $element_args['items'] as $key => $item ) : ?>
					<?php
					$item_default_args = array(
						// Content.
						'title'       => '',
						'time'        => '',
						'icon'        => '',
						'description' => '',
					);

					$item = wp_parse_args( $item, $item_default_args );

					$inline_editing_key = 'items.' . $key . '.';

					?>
					<div class="xts-work-item">
						<div class="xts-work-head">
							<?php if ( $item['icon'] ) : ?>
								<?php echo xts_elementor_get_render_icon( $item['icon'], array( 'class' => 'xts-work-icon' . $icon_classes ) ); // phpcs:ignore ?>
							<?php endif; ?>

							<?php if ( $item['title'] ) : ?>
								<div class="xts-work-title<?php echo esc_attr( $title_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>title">
									<?php echo wp_kses( $item['title'], xts_get_allowed_html() ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $item['time'] ) : ?>
								<div class="xts-work-time<?php echo esc_attr( $time_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>time">
									<?php echo wp_kses( $item['time'], xts_get_allowed_html() ); ?>
								</div>
							<?php endif; ?>
						</div>

						<?php if ( $item['description'] ) : ?>
							<div class="xts-work-desc<?php echo esc_attr( $description_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>description">
								<?php echo wp_kses( $item['description'], xts_get_allowed_html() ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php
	}
}
