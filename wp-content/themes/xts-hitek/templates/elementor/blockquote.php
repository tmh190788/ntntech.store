<?php
/**
 * Blockquote template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_blockquote_template' ) ) {
	/**
	 * Blockquote template
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @since 1.0.0
	 */
	function xts_blockquote_template( $element_args ) {
		$default_args = array(
			// General styles.
			'style'                    => 'default',
			'text_align'               => 'left',

			// Blockquote.
			'blockquote'               => 'I am enough of an artist to draw freely upon my imagination. Imagination is more important than knowledge. Knowledge is limited. Imagination encircles the world.',
			'blockquote_color_presets' => 'default',
			'blockquote_text_size'     => 'default',

			// Author.
			'author'                   => 'Albert Einstein',
			'author_color_presets'     => 'default',
			'author_text_size'         => 'default',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		if ( ! $element_args['blockquote'] ) {
			return;
		}

		$blockquote_classes      = '';
		$blockquote_text_classes = '';
		$author_classes          = '';

		// Blockquote classes.
		$blockquote_classes .= ' xts-textalign-' . $element_args['text_align'];
		if ( 'default' !== $element_args['style'] ) {
			$blockquote_classes .= ' wp-block-quote is-style-' . $element_args['style'];
		}
		if ( 'default' !== $element_args['blockquote_color_presets'] ) {
			$blockquote_classes .= ' xts-textcolor-' . $element_args['blockquote_color_presets'];
		}
		if ( 'default' !== $element_args['blockquote_text_size'] ) {
			$blockquote_classes .= ' xts-fontsize-' . $element_args['blockquote_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$blockquote_text_classes .= ' elementor-inline-editing';
		}

		// Author classes.
		if ( 'default' !== $element_args['author_color_presets'] ) {
			$author_classes .= ' xts-textcolor-' . $element_args['author_color_presets'];
		}
		if ( 'default' !== $element_args['author_text_size'] ) {
			$author_classes .= ' xts-fontsize-' . $element_args['author_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$author_classes .= ' elementor-inline-editing';
		}

		?>
		<blockquote class="<?php echo esc_attr( $blockquote_classes ); ?>">
			<p class="<?php echo esc_attr( $blockquote_text_classes ); ?>" data-elementor-setting-key="blockquote">
				<?php echo wp_kses_post( $element_args['blockquote'] ); ?>
			</p>

			<?php if ( $element_args['author'] ) : ?>
				<cite class="<?php echo esc_attr( $author_classes ); ?>" data-elementor-setting-key="author">
					<?php echo wp_kses_post( $element_args['author'] ); ?>
				</cite>
			<?php endif; ?>
		</blockquote>
		<?php
	}
}
