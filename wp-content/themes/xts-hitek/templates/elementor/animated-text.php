<?php
/**
 * Animated text template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_animated_text_template' ) ) {
	/**
	 * Animated text template
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @since 1.0.0
	 */
	function xts_animated_text_template( $element_args ) {
		$default_args = array(
			// Content.
			'before_text'                => '',
			'after_text'                 => '',
			'animated_text'              => '',

			// Style general.
			'animation_effect'           => 'typing',
			'text_align'                 => 'left',
			'animation_time'             => 600,
			'character_time'             => 150,
			'interval_time'              => 2500,

			// Before & after.
			'before_after_color_presets' => 'default',
			'before_after_text_size'     => 'l',

			// Animated.
			'animated_color_presets'     => 'default',
			'animated_text_size'         => 'l',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$before_after_classes = '';
		$animated_classes     = '';
		$wrapper_classes      = '';

		$animated_text = explode( "\n", $element_args['animated_text'] );

		// Wrapper classes.
		$wrapper_classes .= ' xts-textalign-' . $element_args['text_align'];
		$wrapper_classes .= ' xts-effect-' . $element_args['animation_effect'];

		// Before & after classes.
		if ( 'default' !== $element_args['before_after_color_presets'] ) {
			$before_after_classes .= ' xts-textcolor-' . $element_args['before_after_color_presets'];
		}
		if ( 'default' !== $element_args['before_after_text_size'] ) {
			$before_after_classes .= ' xts-fontsize-' . $element_args['before_after_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$before_after_classes .= ' elementor-inline-editing';
		}

		// Animated classes.
		if ( 'default' !== $element_args['animated_color_presets'] ) {
			$animated_classes .= ' xts-textcolor-' . $element_args['animated_color_presets'];
		}
		if ( 'default' !== $element_args['animated_text_size'] ) {
			$animated_classes .= ' xts-fontsize-' . $element_args['animated_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$animated_classes .= ' elementor-inline-editing';
		}

		xts_enqueue_js_script( 'animated-text-element' );

		?>
			<div class="xts-anim-text<?php echo esc_attr( $wrapper_classes ); ?>" data-interval-time="<?php echo esc_attr( $element_args['interval_time'] ); ?>" data-character-time="<?php echo esc_attr( $element_args['character_time'] ); ?>" data-animation-time="<?php echo esc_attr( $element_args['animation_time'] ); ?>">
				<span class="xts-anim-text-before<?php echo esc_attr( $before_after_classes ); ?>" data-elementor-setting-key="before_text">
					<?php echo esc_html( $element_args['before_text'] ); ?>
				</span>

				<span class="xts-anim-text-list<?php echo esc_attr( $animated_classes ); ?>" data-effect="<?php echo esc_attr( $element_args['animation_effect'] ); ?>">
					<?php foreach ( $animated_text as $key => $text ) : ?>
						<?php
						$text_classes = '';

						if ( 0 === $key ) {
							$text_classes .= ' xts-active';
						} else {
							$text_classes .= ' xts-hidden';
						}
						?>
						<span class="xts-anim-text-item<?php echo esc_attr( $text_classes ); ?>">
							<?php echo esc_html( $text ); ?>
						</span>
					<?php endforeach; ?>
				</span>

				<span class="xts-anim-text-after<?php echo esc_attr( $before_after_classes ); ?>"
					data-elementor-setting-key="after_text">
					<?php echo esc_html( $element_args['after_text'] ); ?>
				</span>
			</div>
		<?php
	}
}
