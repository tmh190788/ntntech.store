<?php
/**
 * Dynamic css
 *
 * @package xts
 */

namespace XTS\Options;

use XTS\Framework\Options;
use XTS\Styles_Storage;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Sanitization class for fields
 */
class Styles {
	/**
	 * Set up all properties
	 *
	 * @var Styles_Storage
	 */
	public $storage;

	/**
	 * Set up all properties
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'init', array( $this, 'set_storage' ), 100 );
		} else {
			add_action( 'wp', array( $this, 'set_storage' ), 10200 );
			add_action( 'wp', array( $this, 'print_styles' ), 10300 );
		}

		add_action( 'xts_after_theme_settings', array( $this, 'reset_data' ), 100 );
		add_action( 'xts_after_theme_settings', array( $this, 'write_file' ), 200 );
	}

	/**
	 * Set storage.
	 *
	 * @since 1.0.0
	 */
	public function set_storage() {
		$this->storage = new Styles_Storage( $this->get_file_name() );
	}

	/**
	 * Get all css.
	 *
	 * @since 1.0.0
	 *
	 * @param string $preset_id Preset id.
	 *
	 * @return string|void
	 */
	private function get_all_css( $preset_id = '' ) {
		$options = Options::get_instance();

		$options->load_defaults();
		$options->load_options();
		$options->load_presets( $preset_id );
		$options->override_options_from_meta();
		$options->setup_globals();

		$css  = $options->get_css_output();
		$css .= $this->get_icons_font_css();
		$css .= $this->get_theme_settings_css();
		$css .= $this->get_custom_fonts_css();
		$css .= $this->get_custom_css();

		return $css;
	}

	/**
	 * Get file name.
	 *
	 * @since 1.0.0
	 */
	private function get_file_name() {
		$active_presets = Presets::get_active_presets();
		$preset_id      = isset( $active_presets[0] ) ? $active_presets[0] : 'default';

		return apply_filters( 'xts_css_file_name', 'theme_settings_' . $preset_id );
	}

	/**
	 * Reset data.
	 *
	 * @since 1.0.0
	 */
	public function reset_data() {
		if ( ! isset( $_GET['settings-updated'] ) ) { // phpcs:ignore
			return;
		}

		$this->storage->reset_data();
	}

	/**
	 * Write file.
	 *
	 * @since 1.0.0
	 */
	public function write_file() {
		if ( ! isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && 'xtemos_options' !== $_GET['page'] ) ) { // phpcs:ignore
			return;
		}

		$this->storage->write( $this->get_all_css() );

		if ( ! Presets::get_active_presets() && isset( $_GET['settings-updated'] ) && Presets::get_all() ) { // phpcs:ignore
			$index = 0;
			foreach ( Presets::get_all() as $preset ) {
				$index++;
				$this->storage->set_data_name( 'theme_settings_' . $preset['id'] );
				$this->storage->set_data( 'xts-theme_settings_' . $preset['id'] . '-' . $this->storage->opt_name . '-file-data' );
				$this->storage->set_css_data( 'xts-theme_settings_' . $preset['id'] . '-' . $this->storage->opt_name . '-css-data' );
				$this->storage->reset_data();
				$this->storage->delete_file();

				if ( $index <= apply_filters( 'xts_theme_settings_presets_file_reset_count', 10 ) ) {
					$this->storage->write( $this->get_all_css( $preset['id'] ) );
				}
			}
		}
	}

	/**
	 * Print styles.
	 *
	 * @since 1.0.0
	 */
	public function print_styles() {
		if ( ! $this->storage->is_css_exists() ) {
			$this->storage->write( $this->get_all_css(), true );
		}

		$this->storage->print_styles();
	}

	/**
	 * Get custom css.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_custom_css() {
		$output      = '';
		$global_css  = xts_get_opt( 'css_global' );
		$css_desktop = xts_get_opt( 'css_desktop' );
		$css_tablet  = xts_get_opt( 'css_tablet' );
		$css_mobile  = xts_get_opt( 'css_mobile' );

		if ( $global_css ) {
			$output .= $global_css;
		}

		if ( $css_desktop ) {
			$output .= '@media (min-width: 1025px) {' . "\n";
			$output .= "\t" . $css_desktop . "\n";
			$output .= '}' . "\n\n";
		}

		if ( $css_tablet ) {
			$output .= '@media (min-width: 768px) and (max-width: 1024px) {' . "\n";
			$output .= "\t" . $css_tablet . "\n";
			$output .= '}' . "\n\n";
		}

		if ( $css_mobile ) {
			$output .= '@media (max-width: 767px) {' . "\n";
			$output .= "\t" . $css_mobile . "\n";
			$output .= '}' . "\n\n";
		}

		return $output;
	}

	/**
	 * Get custom fonts css.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_custom_fonts_css() {
		$fonts = xts_get_opt( 'custom_fonts' );

		$output       = '';
		$font_display = xts_get_opt( 'google_font_display' );

		if ( isset( $fonts['{{index}}'] ) ) {
			unset( $fonts['{{index}}'] );
		}

		if ( ! $fonts ) {
			return $output;
		}

		foreach ( $fonts as $key => $value ) {
			$woff  = $this->get_custom_font_url( $value['font-woff'] );
			$woff2 = $this->get_custom_font_url( $value['font-woff2'] );

			if ( ! $value['font-name'] ) {
				continue;
			}

			$output .= '@font-face {' . "\n";

			$output .= "\t" . 'font-family: "' . sanitize_text_field( $value['font-name'] ) . '";' . "\n";

			if ( $woff || $woff2 ) {
				$output .= "\t" . 'src: ';

				if ( $woff ) {
					$output .= 'url("' . esc_url( $woff ) . '") format("woff")';
				}

				if ( $woff2 ) {
					if ( $woff ) {
						$output .= ', ' . "\n";
					}
					$output .= 'url("' . esc_url( $woff2 ) . '") format("woff2")';
				}

				$output .= ';' . "\n";
			}

			if ( $value['font-weight'] ) {
				$output .= "\t" . 'font-weight: ' . sanitize_text_field( $value['font-weight'] ) . ';' . "\n";
			} else {
				$output .= "\t" . 'font-weight: normal;' . "\n";
			}

			if ( 'disabled' !== $font_display ) {
				$output .= "\t" . 'font-display:' . $font_display . ';' . "\n";
			}

			$output .= "\t" . 'font-style: normal;' . "\n";

			$output .= '}' . "\n\n";
		}

		return $output;
	}

	/**
	 * Icons font css.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_icons_font_css() {
		$output = '';
		$url    = xts_get_link_without_http( XTS_THEME_URL );

		$font_display = xts_get_opt( 'icons_font_display' );

		$output .= '@font-face {' . "\n";

		$output .= "\t" . 'font-weight: normal;' . "\n";
		$output .= "\t" . 'font-style: normal;' . "\n";
		$output .= "\t" . 'font-family: "font-icon";' . "\n";
		$output .= "\t" . 'src: url("' . $url . '/fonts/font-icon.woff") format("woff"),' . "\n";
		$output .= "\t" . 'url("' . $url . '/fonts/font-icon.woff2") format("woff2");' . "\n";

		if ( 'disabled' !== $font_display ) {
			$output .= "\t" . 'font-display:' . $font_display . ';' . "\n";
		}

		$output .= '}' . "\n\n";

		return $output;
	}

	/**
	 * Get custom font url.
	 *
	 * @since 1.0.0
	 *
	 * @param array $font Font data.
	 *
	 * @return string
	 */
	public function get_custom_font_url( $font ) {
		$url = '';

		if ( isset( $font['id'] ) && $font['id'] ) {
			$url = wp_get_attachment_url( $font['id'] );
		} elseif ( is_array( $font ) ) {
			$url = $font['url'];
		}

		return xts_get_link_without_http( $url );
	}

	/**
	 * Get theme settings css.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_theme_settings_css() {
		$site_width                        = xts_get_opt( 'site_width' );
		$site_layout                       = xts_get_opt( 'site_layout' );
		$promo_popup_width                 = xts_get_opt( 'promo_popup_width' );
		$quick_view_width                  = xts_get_opt( 'quick_view_width' );
		$blog_single_content_boxed         = xts_get_opt( 'blog_single_content_boxed' );
		$blog_single_content_width         = xts_get_opt( 'blog_single_content_width' );
		$header_banner                     = xts_get_opt( 'header_banner' );
		$header_banner_desktop_height      = xts_get_opt( 'header_banner_height' );
		$header_banner_tablet_height       = xts_get_opt( 'header_banner_height_tablet' );
		$header_banner_mobile_height       = xts_get_opt( 'header_banner_height_mobile' );
		$header_banner_mobile_small_height = xts_get_opt( 'header_banner_height_mobile_small' );

		$spacing = 30;

		if ( 'boxed' === $site_layout ) {
			$spacing = 60;
		}

		ob_start();
		// phpcs:disable
?>
<?php if ( $site_width ) : ?>
<?php if ( 'boxed' === $site_layout ) : ?>
.xts-site-wrapper, .xts-header-main {
	max-width: <?php echo esc_html( $site_width ); ?>px;
}

.container {
	max-width: <?php echo esc_html( $site_width - 30); ?>px;
}
<?php else : ?>
.container {
	max-width: <?php echo esc_html( $site_width ); ?>px;
}
<?php endif; ?>

.xts-dropdown-menu.xts-style-container {
	max-width: <?php echo esc_html( $site_width - 30 ); ?>px;
}

<?php if ( 'enabled' === xts_get_opt( 'negative_gap', 'enabled' ) ) : ?>
.elementor-section.xts-section-stretch > .elementor-column-gap-no {
	max-width: <?php echo esc_html( $site_width - $spacing ); ?>px;
}

.elementor-section.xts-section-stretch > .elementor-column-gap-narrow {
	max-width: <?php echo esc_html( $site_width - $spacing + 10); ?>px;
}

.elementor-section.xts-section-stretch > .elementor-column-gap-default {
	max-width: <?php echo esc_html( $site_width - $spacing + 20); ?>px;
}

.elementor-section.xts-section-stretch > .elementor-column-gap-extended {
	max-width: <?php echo esc_html( $site_width - $spacing + 30); ?>px;
}

.elementor-section.xts-section-stretch > .elementor-column-gap-wide {
	max-width: <?php echo esc_html( $site_width - $spacing + 40); ?>px;
}

.elementor-section.xts-section-stretch > .elementor-column-gap-wider {
	max-width: <?php echo esc_html( $site_width - $spacing + 60); ?>px;
}

@media (min-width: <?php echo esc_html( $site_width + 17 ); ?>px) {
	.platform-Windows .xts-section-stretch > .elementor-container {
		margin-left: auto;
		margin-right: auto;
	}
}

@media (min-width: <?php echo esc_html( $site_width ); ?>px) {
	html:not(.platform-Windows) .xts-section-stretch > .elementor-container {
		margin-left: auto;
		margin-right: auto;
	}
}
<?php endif; ?>
<?php endif; ?>

<?php if ( $header_banner ) : ?>
/* Header banner */
.xts-header-banner.xts-display,
.xts-header-banner-bg,
.xts-header-banner-content,
.xts-header-banner-close {
	height: <?php echo esc_html( $header_banner_desktop_height ); ?>px;
}

/* Tablet */
@media (max-width: 1024px) and (min-width: 768px) {
/* Header banner */
.xts-header-banner.xts-display,
.xts-header-banner-bg,
.xts-header-banner-content,
.xts-header-banner-close {
	height: <?php echo esc_html( $header_banner_tablet_height ); ?>px;
}
}

/* Mobile */
@media (max-width: 767px) {
/* Header banner */
.xts-header-banner.xts-display,
.xts-header-banner-bg,
.xts-header-banner-content,
.xts-header-banner-close {
	height: <?php echo esc_html( $header_banner_mobile_height ); ?>px;
}
}

/* Mobile small */
@media (max-width: 575px) {
/* Header banner */
.xts-header-banner.xts-display,
.xts-header-banner-bg,
.xts-header-banner-content,
.xts-header-banner-close {
	height: <?php echo esc_html( $header_banner_mobile_small_height ); ?>px;
}
}
<?php endif; ?>

/* Quick view */
.xts-quick-view-popup {
	max-width: <?php echo esc_html( $quick_view_width ); ?>px;
}

/* Promo popup */
.xts-promo-popup {
	max-width: <?php echo esc_html( $promo_popup_width ); ?>px;
}

/* Blog single content boxed */
<?php if ( $blog_single_content_boxed ) : ?>
	.xts-content-area.col-lg-12 .xts-single-post-boxed {
		max-width: <?php echo esc_html( $blog_single_content_width ); ?>px;
	}
<?php endif; ?>

/* Header Boxed */
.xts-header.xts-design-boxed:not(.xts-full-width) .xts-header-main {
	max-width: <?php echo esc_html( $site_width - 30 ); ?>px;
}

<?php if ( xts_get_opt( 'product_layered_nav_widgets_scroll' ) ): ?>
.xts-widget-filter .xts-scroll-content {
	max-height: <?php echo esc_html( xts_get_opt( 'product_layered_nav_widgets_height' ) ); ?>px;
}
<?php endif; ?>
<?php

		return ob_get_clean();
		// phpcs:enable
	}
}
