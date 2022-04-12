<?php
/**
 * Header styles class.
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Header styles class.
 */
class Styles {
	/**
	 * Header elements css.
	 *
	 * @var string
	 */
	private $elements_css;

	/**
	 * Get elements css
	 *
	 * @return string
	 */
	public function get_elements_css() {
		return $this->elements_css;
	}

	/**
	 * Get all header css
	 *
	 * @param array $element Element.
	 * @param array $options Options.
	 *
	 * @return string
	 */
	public function get_all_css( $element, $options ) {
		$this->set_elements_css( $element );

		return $this->get_header_css( $options ) . $this->get_elements_css();
	}

	/**
	 * Set header elements css.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $element Header structure.
	 */
	public function set_elements_css( $element = false ) {
		if ( ! $element ) {
			$element = xts_get_config( 'header-builder-structure', 'theme' );
		}

		$selector = 'xts-' . $element['id'];

		if ( isset( $element['content'] ) && is_array( $element['content'] ) ) {
			foreach ( $element['content'] as $value ) {
				$this->set_elements_css( $value );
			}
		}

		$css        = '';
		$rules      = '';
		$border_css = '';
		$bg_css     = '';

		if ( isset( $element['params']['background'] ) ) {
			if ( 'categories' === $element['type'] ) {
				$css .= "\n" . '.xts-' . $element['id'] . ' .xts-header-cats-title {' . "\n";
				$css .= "\t" . $this->generate_background_css( $element['params']['background']['value'] ) . "\n";
				$css .= '}' . "\n";
			} else {
				$bg_css = $this->generate_background_css( $element['params']['background']['value'] );
			}
		}

		if ( isset( $element['params']['background_color'] ) ) {
			if ( 'custom' === $element['params']['background_color']['value'] ) {
				$rules .= $bg_css;
			}
		} elseif ( $bg_css ) {
			$rules .= $bg_css;
		}

		if ( isset( $element['params']['border'] ) ) {
			$sides = isset( $element['params']['border']['value']['sides'] ) ? $element['params']['border']['value']['sides'] : array(
				'top',
				'bottom',
				'left',
				'right',
			);

			$border_css = $this->generate_border_css( $element['params']['border']['value'], $sides );
		}

		if ( isset( $element['params']['border'] ) && isset( $element['params']['border']['value']['applyFor'] ) && 'boxed' === $element['params']['border']['value']['applyFor'] ) {
			$css .= '.' . $selector . ' .xts-header-row-inner { ' . $border_css . ' }';
		} elseif ( $border_css ) {
			$rules .= $border_css;
		}

		if ( 'categories' === $element['type'] && isset( $element['params']['more_cat_button'] ) && $element['params']['more_cat_button']['value'] ) {
			$count = $element['params']['more_cat_button_count']['value'] + 1;
			$css  .= '.' . $selector . ':not(.xts-more-cats-visible) .xts-nav-mega > li:nth-child(n+' . $count . '):not(:last-child) {
			    display: none;
			}';
		}

		if ( isset( $element['params']['text_color'] ) ) {
			$rules .= $this->generate_color_css( $element['params']['text_color']['value'] );
		}

		if ( isset( $element['params']['color'] ) ) {
			$rules .= $this->generate_color_css( $element['params']['color']['value'] );
		}

		if ( $rules ) {
			$css .= "\n" . '.' . $selector . ' {' . "\n";
			$css .= "\t" . $rules . "\n";
			$css .= '}' . "\n";
		}

		$this->elements_css .= $css;
	}

	/**
	 * Generate color CSS code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $color_data Color data.
	 *
	 * @return string
	 */
	public function generate_color_css( $color_data ) {
		$css = '';

		if ( isset( $color_data['r'] ) && isset( $color_data['g'] ) && isset( $color_data['b'] ) && isset( $color_data['a'] ) ) {
			$css .= 'color: rgba(' . $color_data['r'] . ', ' . $color_data['g'] . ', ' . $color_data['b'] . ', ' . $color_data['a'] . ');';
		}

		return $css;
	}

	/**
	 * Generate background CSS code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $bg Background data.
	 *
	 * @return string
	 */
	public function generate_background_css( $bg ) {
		$css = '';

		if ( isset( $bg['background-color'] ) ) {
			extract( $bg['background-color'] ); // phpcs:ignore
		}

		if ( isset( $r ) && isset( $g ) && isset( $b ) && isset( $a ) ) {
			$css .= 'background-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
		}

		if ( isset( $bg['background-image'] ) ) {
			extract( $bg['background-image'] ); // phpcs:ignore
		}

		if ( isset( $url ) ) {
			$css .= 'background-image: url(' . $url . ');';
		}

		if ( isset( $bg['background-size'] ) ) {
			$css .= 'background-size: ' . $bg['background-size'] . ';';
		}

		if ( isset( $bg['background-attachment'] ) ) {
			$css .= 'background-attachment: ' . $bg['background-attachment'] . ';';
		}

		if ( isset( $bg['background-position'] ) ) {
			$css .= 'background-position: ' . $bg['background-position'] . ';';
		}

		if ( isset( $bg['background-repeat'] ) ) {
			$css .= 'background-repeat: ' . $bg['background-repeat'] . ';';
		}

		return $css;
	}

	/**
	 * Generate border CSS code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $border Border data.
	 * @param array $sides Sides data.
	 *
	 * @return string
	 */
	public function generate_border_css( $border, $sides ) {
		$css = '';

		if ( is_array( $border ) ) {
			extract( $border ); // phpcs:ignore
		}

		if ( isset( $color ) ) {
			extract( $color ); // phpcs:ignore
		}

		if ( isset( $r ) && isset( $g ) && isset( $b ) && isset( $a ) ) {
			$css .= 'border-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
		}

		foreach ( $sides as $side ) {
			if ( isset( $border[ 'width-' . $side ] ) && $border[ 'width-' . $side ] > 0 ) {
				$css .= 'border-' . $side . '-width: ' . $border[ 'width-' . $side ] . 'px;';
			}
		}

		$css .= ( isset( $style ) ) ? 'border-style: ' . $style . ';' : 'border-style: solid;';

		return $css;
	}

	/**
	 * Get header CSS code based on its options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Options.
	 *
	 * @return false|string
	 */
	public function get_header_css( $options ) {
		$top_border    = isset( $options['top-bar']['border']['width'] ) ? (int) $options['top-bar']['border']['width'] : 0;
		$header_border = isset( $options['general-header']['border']['width'] ) ? (int) $options['general-header']['border']['width'] : 0;
		$bottom_border = isset( $options['header-bottom']['border']['width'] ) ? (int) $options['header-bottom']['border']['width'] : 0;

		$total_border_height = $top_border + $header_border + $bottom_border;

		$total_height = $options['top-bar']['height'] + $options['general-header']['height'] + $options['header-bottom']['height'];

		$mobile_height = $options['top-bar']['mobile_height'] + $options['general-header']['mobile_height'] + $options['header-bottom']['mobile_height'] + $total_border_height;

		$total_height += $total_border_height;

		if ( $options['boxed'] && ( $options['top-bar']['hide_desktop'] || ( ! $options['top-bar']['hide_desktop'] && $options['top-bar']['background'] ) ) ) {/* Quick view */
			$total_height = $total_height + 30;
		}

		$sticky_elements = array_filter(
			$options,
			function ( $el ) {
				return isset( $el['sticky'] ) && $el['sticky'];
			}
		);

		$sticky_color_data_css = '';
		if ( $sticky_elements ) {
			$last_element = end( $sticky_elements );
			if ( isset( $last_element['background']['background-color'] ) ) {
				$sticky_color_data     = $last_element['background']['background-color'];
				$sticky_color_data_css = 'rgba(' . $sticky_color_data['r'] . ', ' . $sticky_color_data['g'] . ', ' . $sticky_color_data['b'] . ', ' . $sticky_color_data['a'] . ')';
			}
		}

		ob_start(); // phpcs:disable
?>
<?php if ( $sticky_color_data_css ) : ?>
.xts-header {
	background-color: <?php echo esc_html( $sticky_color_data_css ); ?>;
}
<?php endif; ?>

<?php if ( isset( $options['top-bar']['align_dropdowns_bottom'] ) && $options['top-bar']['align_dropdowns_bottom'] ) : ?>
/* DROPDOWN ALIGN BOTTOM IN TOP BAR */

.xts-top-bar.xts-dropdowns-align-bottom .xts-dropdown {
	margin-top: <?php echo esc_html( $options['top-bar']['height'] / 2 - 20 ); ?>px;
}

.xts-top-bar.xts-dropdowns-align-bottom .xts-dropdown:after {
	height: <?php echo esc_html( $options['top-bar']['height'] / 2 - 10 ); ?>px;
}

<?php if ( $options['top-bar']['sticky_height'] ) : ?>
	.xts-sticked .xts-top-bar.xts-dropdowns-align-bottom .xts-dropdown {
		margin-top: <?php echo esc_html( $options['top-bar']['sticky_height'] / 2 - 20 ); ?>px;
	}

	.xts-sticked .xts-top-bar.xts-dropdowns-align-bottom .xts-dropdown:after {
		height: <?php echo esc_html( $options['top-bar']['sticky_height'] / 2 - 10 ); ?>px;
	}
<?php endif; ?>
<?php endif; ?>

<?php if ( isset( $options['general-header']['align_dropdowns_bottom'] ) && $options['general-header']['align_dropdowns_bottom'] ) : ?>
/* DROPDOWN ALIGN BOTTOM IN GENERAL HEADER */

.xts-general-header.xts-dropdowns-align-bottom .xts-dropdown {
	margin-top: <?php echo esc_html( $options['general-header']['height'] / 2 - 20 ); ?>px;
}

.xts-general-header.xts-dropdowns-align-bottom .xts-dropdown:after {
	height: <?php echo esc_html( $options['general-header']['height'] / 2 - 10 ); ?>px;
}

<?php if ( $options['general-header']['sticky_height'] ) : ?>
	.xts-sticked .xts-general-header.xts-dropdowns-align-bottom .xts-dropdown {
		margin-top: <?php echo esc_html( $options['general-header']['sticky_height'] / 2 - 20 ); ?>px;
	}

	.xts-sticked .xts-general-header.xts-dropdowns-align-bottom .xts-dropdown:after {
		height: <?php echo esc_html( $options['general-header']['sticky_height'] / 2 - 10 ); ?>px;
	}
<?php endif; ?>
<?php endif; ?>

<?php if ( isset( $options['header-bottom']['align_dropdowns_bottom'] ) && $options['header-bottom']['align_dropdowns_bottom'] ) : ?>
/* DROPDOWN ALIGN BOTTOM IN HEADER BOTTOM */

.xts-header-bottom.xts-dropdowns-align-bottom .xts-dropdown {
	margin-top: <?php echo esc_html( $options['header-bottom']['height'] / 2 - 20 ); ?>px;
}

.xts-header-bottom.xts-dropdowns-align-bottom .xts-dropdown:after {
	height: <?php echo esc_html( $options['header-bottom']['height'] / 2 - 10 ); ?>px;
}

<?php if ( $options['header-bottom']['sticky_height'] ) : ?>
	.xts-sticked .xts-header-bottom.xts-dropdowns-align-bottom .xts-dropdown {
		margin-top: <?php echo esc_html( $options['header-bottom']['sticky_height'] / 2 - 20 ); ?>px;
	}

	.xts-sticked .xts-header-bottom.xts-dropdowns-align-bottom .xts-dropdown:after {
		height: <?php echo esc_html( $options['header-bottom']['sticky_height'] / 2 - 10 ); ?>px;
	}
<?php endif; ?>
<?php endif; ?>

<?php if ( isset( $options['general-header']['align_dropdowns_bottom'] ) && $options['general-header']['align_dropdowns_bottom'] && $options['sticky_clone'] ) : ?>
/* DROPDOWN ALIGN BOTTOM IN HEADER CLONE */

.xts-sticked .xts-header-clone .xts-general-header.xts-dropdowns-align-bottom .xts-dropdown {
	margin-top: <?php echo esc_html( $options['sticky_height'] / 2 - 20 ); ?>px;
}

.xts-sticked .xts-header-clone .xts-general-header.xts-dropdowns-align-bottom .xts-dropdown:after {
	height: <?php echo esc_html( $options['sticky_height'] / 2 - 10 ); ?>px;
}
<?php endif; ?>

@media (min-width: 1025px) {

	/* HEIGHT OF ROWS ON HEADER */

	.xts-top-bar .xts-header-row-inner {
		height: <?php echo esc_html( $options['top-bar']['height'] ); ?>px;
	}

	.xts-general-header .xts-header-row-inner {
		height: <?php echo esc_html( $options['general-header']['height'] ); ?>px;
	}

	.xts-header-bottom .xts-header-row-inner {
		height: <?php echo esc_html( $options['header-bottom']['height'] ); ?>px;
	}

	/* HEIGHT OF ROWS WHEN HEADER IS STICKY */

	.xts-sticky-real.xts-sticked .xts-top-bar .xts-header-row-inner {
		height: <?php echo esc_html( $options['top-bar']['sticky_height'] ); ?>px;
	}

	.xts-sticky-real.xts-sticked .xts-general-header .xts-header-row-inner {
		height: <?php echo esc_html( $options['general-header']['sticky_height'] ); ?>px;
	}

	.xts-sticky-real.xts-sticked .xts-header-bottom .xts-header-row-inner {
		height: <?php echo esc_html( $options['header-bottom']['sticky_height'] ); ?>px;
	}

	/* HEIGHT OF HEADER CLONE */

	.xts-header-clone .xts-header-row-inner {
		height: <?php echo esc_html( $options['sticky_height'] ); ?>px;
	}

	/* HEIGHT OF PAGE TITLE WHEN HEADER IS OVER CONTENT */

	.xts-header-overlap .xts-page-title.xts-size-xs {
		padding-top: <?php echo esc_html( $total_height + 10 ); ?>px;
	}

	.xts-header-overlap .xts-page-title.xts-size-s {
		padding-top: <?php echo esc_html( $total_height + 20 ); ?>px;
	}

	.xts-header-overlap .xts-page-title.xts-size-m {
		padding-top: <?php echo esc_html( $total_height + 40 ); ?>px;
	}

	.xts-header-overlap .xts-page-title.xts-size-l {
		padding-top: <?php echo esc_html( $total_height + 80 ); ?>px;
	}

	.xts-header-overlap .xts-page-title.xts-size-xl {
		padding-top: <?php echo esc_html( $total_height + 110 ); ?>px;
	}

	.xts-header-overlap .xts-page-title.xts-size-xxl {
		padding-top: <?php echo esc_html( $total_height + 200 ); ?>px;
	}

	/* HEIGHT OF HEADER BUILDER ELEMENTS */

	/* HEIGHT ELEMENTS IN TOP BAR */

	.xts-top-bar .xts-logo img {
		max-height: <?php echo esc_html( $options['top-bar']['height'] ); ?>px;
	}

	.xts-sticked .xts-top-bar .xts-logo img {
		max-height: <?php echo esc_html( $options['top-bar']['sticky_height'] ); ?>px;
	}

	/* HEIGHT ELEMENTS IN GENERAL HEADER */

	.xts-general-header .xts-logo img {
		max-height: <?php echo esc_html( $options['general-header']['height'] ); ?>px;
	}

	.xts-sticked .xts-general-header .xts-logo img {
		max-height: <?php echo esc_html( $options['general-header']['sticky_height'] ); ?>px;
	}

	/* HEIGHT ELEMENTS IN BOTTOM HEADER */

	.xts-header-bottom .xts-logo img {
		max-height: <?php echo esc_html( $options['header-bottom']['height'] ); ?>px;
	}

	.xts-sticked .xts-header-bottom .xts-logo img {
		max-height: <?php echo esc_html( $options['header-bottom']['sticky_height'] ); ?>px;
	}

	/* HEIGHT ELEMENTS IN HEADER CLONE */

	.xts-header-clone .xts-general-header .xts-logo img {
		max-height: <?php echo esc_html( $options['sticky_height'] ); ?>px;
	}
}

@media (max-width: 1024px) {

	/* HEIGHT OF ROWS ON HEADER */

	.xts-top-bar .xts-header-row-inner {
		height: <?php echo esc_html( $options['top-bar']['mobile_height'] ); ?>px;
	}

	.xts-general-header .xts-header-row-inner {
		height: <?php echo esc_html( $options['general-header']['mobile_height'] ); ?>px;
	}

	.xts-header-bottom .xts-header-row-inner {
		height: <?php echo esc_html( $options['header-bottom']['mobile_height'] ); ?>px;
	}

	/* HEADER OVER CONTENT */

	.xts-header-overlap .xts-page-title {
		padding-top: <?php echo esc_html( $mobile_height + 20 ); ?>px;
	}

	/* HEIGHT ELEMENTS IN TOP BAR */

	.xts-top-bar .xts-logo img {
		max-height: <?php echo esc_html( $options['top-bar']['mobile_height'] ); ?>px;
	}

	/* HEIGHT ELEMENTS IN GENERAL HEADER */

	.xts-general-header .xts-logo img {
		max-height: <?php echo esc_html( $options['general-header']['mobile_height'] ); ?>px;
	}

	/* HEIGHT ELEMENTS IN BOTTOM HEADER */

	.xts-header-bottom .xts-logo img {
		max-height: <?php echo esc_html( $options['header-bottom']['mobile_height'] ); ?>px;
	}
}
<?php // phpcs:enable

		return apply_filters( 'xts_header_css', ob_get_clean(), $options );
	}
}
