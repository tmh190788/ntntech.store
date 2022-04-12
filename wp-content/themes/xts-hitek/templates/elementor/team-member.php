<?php
/**
 * Team member template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}


if ( ! function_exists( 'xts_team_member_template' ) ) {
	/**
	 * Team member template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_team_member_template( $element_args ) {
		$default_args = array(
			// Layout.
			'design'                                  => 'default',
			'color_scheme'                            => 'inherit',
			'text_align'                              => 'center',
			'team_member_background_switcher'         => 'no',
			'team_member_hovered_background_switcher' => 'no',
			'team_member_shadow_switcher'             => 'no',

			// Name.
			'name'                                    => '',
			'name_text_size'                          => 'm',
			'name_color_presets'                      => 'default',

			// Position.
			'position'                                => '',
			'position_text_size'                      => 's',
			'position_color_presets'                  => 'default',

			// Description.
			'description'                             => '',
			'description_text_size'                   => 'default',
			'description_color_presets'               => 'default',

			// Links.
			'style'                                   => 'default',
			'size'                                    => 'm',
			'shape'                                   => 'circle',
			'social_icon_list'                        => '',
			'social_icons_switcher'                   => 'yes',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes     = '';
		$buttons_classes     = '';
		$name_classes        = '';
		$position_classes    = '';
		$description_classes = '';

		// Wrapper classes.
		$wrapper_classes .= ' xts-design-' . $element_args['design'];
		$wrapper_classes .= ' xts-textalign-' . $element_args['text_align'];
		if ( 'inherit' !== $element_args['color_scheme'] ) {
			$wrapper_classes .= ' xts-scheme-' . $element_args['color_scheme'];
		}
		if ( 'yes' === $element_args['team_member_background_switcher'] || 'yes' === $element_args['team_member_hovered_background_switcher'] ) {
			$wrapper_classes .= ' xts-with-bg-color';
		}
		if ( 'yes' === $element_args['team_member_shadow_switcher'] ) {
			$wrapper_classes .= ' xts-with-shadow';
		}

		// Buttons classes.
		$buttons_classes .= ' xts-style-' . $element_args['style'];
		$buttons_classes .= ' xts-size-' . $element_args['size'];
		$buttons_classes .= ' xts-shape-' . $element_args['shape'];
		if ( 'inherit' !== $element_args['color_scheme'] ) {
			$buttons_classes .= ' xts-scheme-' . $element_args['color_scheme'];
		}

		// Image settings.
		$image_output = xts_get_image_html( $element_args, 'image' );

		// Name classes.
		if ( xts_elementor_is_edit_mode() ) {
			$name_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args['name_color_presets'] ) {
			$name_classes .= ' xts-textcolor-' . $element_args['name_color_presets'];
		}
		if ( 'default' !== $element_args['name_text_size'] ) {
			$name_classes .= ' xts-fontsize-' . $element_args['name_text_size'];
		}

		// Position classes.
		if ( xts_elementor_is_edit_mode() ) {
			$position_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args['position_color_presets'] ) {
			$position_classes .= ' xts-textcolor-' . $element_args['position_color_presets'];
		}
		if ( 'default' !== $element_args['position_text_size'] ) {
			$position_classes .= ' xts-fontsize-' . $element_args['position_text_size'];
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

		<div class="xts-member<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php if ( $image_output ) : ?>
				<div class="xts-member-image">
					<?php echo wp_kses( $image_output, 'xts_media' ); ?>
				</div>
			<?php endif; ?>

			<div class="xts-member-content xts-reset-mb-10 xts-reset-last">
				<?php if ( $element_args['name'] ) : ?>
					<h4 class="xts-member-name<?php echo esc_attr( $name_classes ); ?>" data-elementor-setting-key="name">
						<?php echo wp_kses( $element_args['name'], xts_get_allowed_html() ); ?>
					</h4>
				<?php endif; ?>

				<?php if ( $element_args['position'] ) : ?>
					<div class="xts-member-position<?php echo esc_attr( $position_classes ); ?>" data-elementor-setting-key="position">
						<?php echo wp_kses( $element_args['position'], xts_get_allowed_html() ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $element_args['description'] ) : ?>
					<div class="xts-member-description<?php echo esc_attr( $description_classes ); ?>" data-elementor-setting-key="description">
						<?php echo do_shortcode( $element_args['description'] ); ?>
					</div>
				<?php endif; ?>

				<?php if ( 'yes' === $element_args['social_icons_switcher'] ) : ?>
					<div class="xts-social-buttons xts-social-icons<?php echo esc_attr( $buttons_classes ); ?>">
						<?php foreach ( $element_args['social_icon_list'] as $value ) : ?>
							<a target="_blank" class="xts-social-<?php echo esc_attr( $value['social_icon'] ); ?>" href="<?php echo esc_url( $value['social_link'] ); ?>">
								<i class="xts-i-<?php echo esc_attr( $value['social_icon'] ); ?>"></i>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php

	}
}
