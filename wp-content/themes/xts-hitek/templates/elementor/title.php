<?php
/**
 * Title template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}


if ( ! function_exists( 'xts_title_template' ) ) {
	/**
	 * Title template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_title_template( $element_args ) {
		$default_args = array(
			'text_align'                   => 'left',
			'text_align_tablet'            => '',
			'text_align_mobile'            => '',
			'design'                       => 'default',
			'color_scheme'                 => 'inherit',

			// Image.
			'image'                        => '',

			// Title.
			'title'                        => '',
			'title_color_presets'          => 'default',
			'title_text_size'              => 'l',
			'tag'                          => 'h4',

			// Subtitle.
			'subtitle'                     => '',
			'subtitle_style'               => 'default',
			'subtitle_color_presets'       => 'default',
			'subtitle_text_size'           => 's',

			// Description.
			'description'                  => '',
			'description_color_presets'    => 'default',
			'description_text_size'        => 'default',

			// Extra.
			'animation_in_view'            => 'no',
			'xts_animation_items'          => '',
			'xts_animation_duration_items' => 'normal',
			'xts_animation_delay_items'    => '',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes             = '';
		$title_classes               = '';
		$title_wrapper_classes       = '';
		$subtitle_classes            = '';
		$subtitle_wrapper_classes    = '';
		$description_classes         = '';
		$description_wrapper_classes = '';
		$image_output                = '';

		// Wrapper classes.
		$wrapper_classes .= ' xts-textalign-' . $element_args['text_align'];
		if ( $element_args['text_align_tablet'] ) {
			$wrapper_classes .= ' xts-textalign-md-' . $element_args['text_align_tablet'];
		}
		if ( $element_args['text_align_mobile'] ) {
			$wrapper_classes .= ' xts-textalign-sm-' . $element_args['text_align_mobile'];
		}
		if ( 'inherit' !== $element_args['color_scheme'] ) {
			$wrapper_classes .= ' xts-scheme-' . $element_args['color_scheme'];
		}
		if ( 'yes' === $element_args['animation_in_view'] ) {
			xts_enqueue_js_script( 'items-animation-in-view' );
			$wrapper_classes .= ' xts-in-view-animation';
		}

		// Title classes.
		$title_wrapper_classes .= ' xts-design-' . $element_args['design'];
		if ( 'default' !== $element_args['title_color_presets'] ) {
			$title_wrapper_classes .= ' xts-textcolor-' . $element_args['title_color_presets'];
		}
		if ( 'default' !== $element_args['title_text_size'] ) {
			$title_wrapper_classes .= ' xts-fontsize-' . $element_args['title_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$title_classes .= ' elementor-inline-editing';
		}

		// Subtitle classes.
		if ( 'default' !== $element_args['subtitle_color_presets'] ) {
			$subtitle_wrapper_classes .= ' xts-textcolor-' . $element_args['subtitle_color_presets'];
		}
		if ( 'default' !== $element_args['subtitle_style'] ) {
			$subtitle_wrapper_classes .= ' xts-style-' . $element_args['subtitle_style'];
		}
		if ( 'default' !== $element_args['subtitle_text_size'] ) {
			$subtitle_wrapper_classes .= ' xts-fontsize-' . $element_args['subtitle_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$subtitle_classes .= ' elementor-inline-editing';
		}

		// Description classes.
		if ( xts_elementor_is_edit_mode() ) {
			$description_classes .= ' elementor-inline-editing';
		}
		if ( 'default' !== $element_args['description_color_presets'] ) {
			$description_wrapper_classes .= ' xts-textcolor-' . $element_args['description_color_presets'];
		}
		if ( 'default' !== $element_args['description_text_size'] ) {
			$description_wrapper_classes .= ' xts-fontsize-' . $element_args['description_text_size'];
		}

		// Image settings.
		if ( 'image' === $element_args['design'] ) {
			$image_output = xts_get_image_html( $element_args, 'image' );
		}

		// Animations.
		if ( 'yes' === $element_args['animation_in_view'] && $element_args['xts_animation_items'] ) {
			$wrapper_classes .= ' xts-autoplay-animations-off';

			$title_wrapper_classes .= ' xts-animation-item';
			$title_wrapper_classes .= ' xts-animation-' . $element_args['xts_animation_items'];
			$title_wrapper_classes .= ' xts-animation-' . $element_args['xts_animation_duration_items'];

			$subtitle_wrapper_classes .= ' xts-animation-item';
			$subtitle_wrapper_classes .= ' xts-animation-' . $element_args['xts_animation_items'];
			$subtitle_wrapper_classes .= ' xts-animation-' . $element_args['xts_animation_duration_items'];

			$description_wrapper_classes .= ' xts-animation-item';
			$description_wrapper_classes .= ' xts-animation-' . $element_args['xts_animation_items'];
			$description_wrapper_classes .= ' xts-animation-' . $element_args['xts_animation_duration_items'];
		}

		?>
			<div class="xts-section-heading xts-reset-mb-10 xts-reset-last<?php echo esc_attr( $wrapper_classes ); ?>" data-animation-delay="<?php echo esc_attr( $element_args['xts_animation_delay_items'] ); ?>">
				<?php if ( $element_args['subtitle'] ) : ?>
					<div class="xts-section-subtitle<?php echo esc_attr( $subtitle_wrapper_classes ); ?>">
						<span class="xts-section-subtitle-text<?php echo esc_attr( $subtitle_classes ); ?>" data-elementor-setting-key="subtitle">
							<?php echo wp_kses( $element_args['subtitle'], xts_get_allowed_html() ); ?>
						</span>

						<?php if ( 'divider' === $element_args['subtitle_style'] ) : ?>
							<div class="xts-section-subtitle-divider"></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $element_args['title'] ) : ?>
					<<?php echo esc_attr( $element_args['tag'] ); ?> class="xts-section-title title<?php echo esc_attr( $title_wrapper_classes ); ?>">
						<span class="xts-section-title-text<?php echo esc_attr( $title_classes ); ?>" data-elementor-setting-key="title">
							<?php echo wp_kses( $element_args['title'], xts_get_allowed_html() ); ?>
						</span>

						<?php if ( 'simple' === $element_args['design'] ) : ?>
							<div class="xts-section-title-divider"></div>
						<?php endif; ?>
					</<?php echo esc_attr( $element_args['tag'] ); ?>>
				<?php endif; ?>

				<?php if ( $image_output ) : ?>
					<div class="xts-section-title-image">
						<?php echo wp_kses( $image_output, 'xts_media' ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $element_args['description'] ) : ?>
					<div class="xts-section-desc<?php echo esc_attr( $description_wrapper_classes ); ?>">
						<div class="xts-section-desc-text xts-reset-all-last<?php echo esc_attr( $description_classes ); ?>" data-elementor-setting-key="description">
							<?php echo do_shortcode( $element_args['description'] ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php
	}
}
