<?php
/**
 * Gutenberg.
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}


if ( ! function_exists( 'xts_gutenberg_editor_styles' ) ) {
	/**
	 * Gutenberg styles.
	 *
	 * @since 1.0.0
	 */
	function xts_gutenberg_editor_styles() {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'style-editor.css' );
	}

	add_action( 'after_setup_theme', 'xts_gutenberg_editor_styles', 10 );
}

if ( ! function_exists( 'xts_gutenberg_editor_custom_styles' ) ) {
	/**
	 * Gutenberg styles.
	 *
	 * @since 1.0.0
	 */
	function xts_gutenberg_editor_custom_styles() {
		$all_pages_bg        = xts_get_opt( 'all_pages_bg' );
		$content_typography  = xts_get_opt( 'content_typography' );
		$title_typography    = xts_get_opt( 'title_typography' );
		$entities_typography = xts_get_opt( 'entities_typography' );
		$links_color         = xts_get_opt( 'links_color' );
		$site_width          = xts_get_opt( 'site_width' );
		$primary_color       = xts_get_opt( 'primary_color' );

		?>
		<style>
			div.block-editor .editor-styles-wrapper {
				<?php if ( ! empty( $all_pages_bg['color'] ) ) : ?>
					background-color: <?php echo esc_attr( $all_pages_bg['color'] ); ?>;
				<?php endif; ?>

				<?php if ( ! empty( $content_typography[0]['font-size'] ) ) : ?>
				  font-size: <?php echo esc_attr( $all_pages_bg['font-size'] ); ?>;
				<?php endif; ?>

				<?php if ( ! empty( $content_typography[0]['font-family'] ) ) : ?>
					font-family: <?php echo esc_attr( $content_typography[0]['font-family'] ); ?>;
				<?php endif; ?>

				<?php if ( ! empty( $content_typography[0]['line-height'] ) ) : ?>
					line-height: <?php echo esc_attr( $content_typography[0]['line-height'] ); ?>;
				<?php endif; ?>

				<?php if ( ! empty( $content_typography[0]['color'] ) ) : ?>
					color: <?php echo esc_attr( $content_typography[0]['color'] ); ?>;
				<?php endif; ?>
			}

			<?php if ( ! empty( $links_color['idle'] ) ) : ?>
				div.block-editor .editor-styles-wrapper a {
					color: <?php echo esc_attr( $links_color['idle'] ); ?>;
				}
			<?php endif; ?>

			<?php if ( ! empty( $links_color['hover'] ) ) : ?>
				div.block-editor .editor-styles-wrapper a:hover {
					color: <?php echo esc_attr( $links_color['hover'] ); ?>;
				}
			<?php endif; ?>

			div.block-editor .editor-styles-wrapper h1,
			div.block-editor .editor-styles-wrapper h2,
			div.block-editor .editor-styles-wrapper h3,
			div.block-editor .editor-styles-wrapper h4,
			div.block-editor .editor-styles-wrapper h5,
			div.block-editor .editor-styles-wrapper h6,
			div.block-editor .editor-styles-wrapper .wp-block.editor-post-title__block .editor-post-title__input {
				<?php if ( ! empty( $title_typography[0]['font-family'] ) ) : ?>
					font-family: <?php echo esc_attr( $title_typography[0]['font-family'] ); ?>;
				<?php endif; ?>
				<?php if ( ! empty( $title_typography[0]['font-weight'] ) ) : ?>
					font-weight: <?php echo esc_attr( $title_typography[0]['font-weight'] ); ?>;
				<?php endif; ?>
				<?php if ( ! empty( $title_typography[0]['line-height'] ) ) : ?>
					line-height: <?php echo esc_attr( $title_typography[0]['line-height'] ); ?>;
				<?php endif; ?>

				<?php if ( ! empty( $title_typography[0]['color'] ) ) : ?>
					color: <?php echo esc_attr( $title_typography[0]['color'] ); ?>;
				<?php endif; ?>
			}

			div.block-editor .editor-styles-wrapper .wp-block.editor-post-title__block .editor-post-title__input {
				<?php if ( ! empty( $entities_typography[0]['font-family'] ) ) : ?>
					font-family: <?php echo esc_attr( $entities_typography[0]['font-family'] ); ?>;
				<?php endif; ?>
				<?php if ( ! empty( $entities_typography[0]['font-weight'] ) ) : ?>
					font-weight: <?php echo esc_attr( $entities_typography[0]['font-weight'] ); ?>;
				<?php endif; ?>
				<?php if ( ! empty( $entities_typography[0]['line-height'] ) ) : ?>
					line-height: <?php echo esc_attr( $entities_typography[0]['line-height'] ); ?>;
				<?php endif; ?>

				<?php if ( ! empty( $entities_typography[0]['color'] ) ) : ?>
					color: <?php echo esc_attr( $entities_typography[0]['color'] ); ?>;
				<?php endif; ?>
			}

			div.block-editor .editor-styles-wrapper .wp-block:not([data-align="full"]) {
				max-width: <?php echo esc_attr( $site_width ); ?>px;
			}

			div.block-editor .editor-styles-wrapper .wp-block[data-align="wide"] {
				max-width: <?php echo esc_attr( $site_width + 150 ); ?>px;
			}

            div.block-editor .editor-styles-wrapper blockquote {
                border-color: <?php echo esc_attr( $primary_color['idle'] ); ?>;
            }
		</style>
		<?php
	}

	add_action( 'admin_enqueue_scripts', 'xts_gutenberg_editor_custom_styles' );
}
