<?php
/**
 * Accordion template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_accordion_template' ) ) {
	/**
	 * Accordion template
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @since 1.0.0
	 */
	function xts_accordion_template( $element_args ) {
		$default_args = array(
			// Content.
			'accordion_items'                       => array(),

			// General style.
			'style'                                 => 'default',
			'state'                                 => 'first',

			// Title.
			'title'                                 => '',
			'title_align'                           => 'left',
			'title_background_color_switcher'       => 'no',

			// Description.
			'description'                           => '',
			'description_align'                     => 'left',
			'description_color_presets'             => 'default',
			'description_background_color_switcher' => 'no',

			// Icon.
			'icon_style'                            => 'arrow',
			'icon_position'                         => 'left',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$title_classes       = '';
		$title_text_classes  = '';
		$description_classes = '';
		$wrapper_classes     = '';
		$icon_classes        = '';

		// Wrapper classes.
		$wrapper_classes .= ' xts-style-' . $element_args['style'];

		// Icon classes.
		$icon_classes .= ' xts-style-' . $element_args['icon_style'];

		// Title classes.
		$title_classes .= ' xts-textalign-' . $element_args['title_align'];
		$title_classes .= ' xts-icon-' . $element_args['icon_position'];
		if ( 'yes' === $element_args['title_background_color_switcher'] ) {
			$title_classes .= ' xts-with-bg';
		}

		// Description classes.
		if ( xts_elementor_is_edit_mode() ) {
			$description_classes .= ' elementor-inline-editing';
		}

		xts_enqueue_js_script( 'accordion-element' );

		?>
		<div class="xts-accordion<?php echo esc_attr( $wrapper_classes ); ?>" data-toggle-self="yes" data-state="<?php echo esc_attr( $element_args['state'] ); ?>">
			<?php foreach ( $element_args['accordion_items'] as $index => $item ) : ?>
				<div class="xts-accordion-item">
					<?php
					$accordion_default_args = array(
						// Content.
						'item_title'    => '',
						'content_type'  => 'text',
						'item_desc'     => '',
						'html_block_id' => 0,
					);

					$item = wp_parse_args( $item, $accordion_default_args );

					$accordion_classes = '';
					$content_classes   = '';

					if ( 0 === $index && 'first' === $element_args['state'] ) {
						$accordion_classes .= ' xts-active';
						$content_classes   .= ' xts-active';
					}

					$content_classes .= ' elementor-repeater-item-' . $item['_id'];
					$content_classes .= ' xts-textalign-' . $element_args['description_align'];
					if ( 'default' !== $element_args['description_color_presets'] ) {
						$content_classes .= ' xts-textcolor-' . $element_args['description_color_presets'];
					}
					if ( 'yes' === $element_args['description_background_color_switcher'] ) {
						$content_classes .= ' xts-with-bg';
					}

					?>

					<div class="xts-accordion-title<?php echo esc_attr( $title_classes . $accordion_classes ); ?>"
						data-accordion-index="<?php echo esc_attr( $index ); ?>">
						<span class="xts-accordion-title-text<?php echo esc_attr( $title_text_classes ); ?>"><?php echo esc_html( $item['item_title'] ); ?></span>
						<span class="xts-accordion-icon<?php echo esc_attr( $icon_classes ); ?>"></span>
					</div>

					<div class="xts-accordion-content<?php echo esc_attr( $content_classes ); ?>"
						data-accordion-index="<?php echo esc_attr( $index ); ?>">
						<?php if ( 'text' === $item['content_type'] && $item['item_desc'] ) : ?>
							<div class="xts-accordion-desc<?php echo esc_attr( $description_classes ); ?>"
								data-elementor-setting-key="accordion_items.<?php echo esc_attr( $index ); ?>.item_desc">
								<?php echo do_shortcode( $item['item_desc'] ); ?>
							</div>
						<?php elseif ( 'html_block' === $item['content_type'] && $item['html_block_id'] ) : ?>
							<div class="xts-accordion-desc">
								<?php echo xts_get_html_block_content( $item['html_block_id'] ); // phpcs:ignore ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
