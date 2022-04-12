<?php
/**
 * Color picker button control.
 *
 * @package xts
 */

namespace XTS\Options\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Field;
use XTS\Framework\Options;

/**
 * Input type text field control.
 */
class Color extends Field {


	/**
	 * Displays the field control HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function render_control() {
		$default = Options::get_default( $this->args );
		?>
			<div class="xts-color-control-inner">
				<?php if ( isset( $this->args['selector_hover'] ) || isset( $this->args['selector_bg_hover'] ) ) : ?>
					<label>
						<?php esc_html_e( 'Idle', 'xts-theme' ); ?>
					</label>
				<?php endif; ?>

				<input type="text" name="<?php echo esc_attr( $this->get_input_name( 'idle' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'idle' ) ); ?>" data-alpha="<?php echo isset( $this->args['alpha'] ) ? esc_attr( $this->args['alpha'] ) : 'true'; ?>" data-default-color="<?php echo isset( $default['idle'] ) ? esc_attr( $default['idle'] ) : ''; ?>" />
			</div>

			<?php if ( isset( $this->args['selector_hover'] ) || isset( $this->args['selector_bg_hover'] ) ) : ?>
				<div class="xts-color-control-inner">
					<label>
						<?php esc_html_e( 'Hover', 'xts-theme' ); ?>
					</label>

					<input type="text" name="<?php echo esc_attr( $this->get_input_name( 'hover' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'hover' ) ); ?>" data-alpha="<?php echo isset( $this->args['alpha'] ) ? esc_attr( $this->args['alpha'] ) : 'true'; ?>" data-default-color="<?php echo isset( $default['hover'] ) ? esc_attr( $default['hover'] ) : ''; ?>" />
				</div>
			<?php endif; ?>

			<input type="hidden" class="xts-css-output" name="<?php echo esc_attr( $this->get_input_name( 'css_output' ) ); ?>" value="1">
		<?php
	}

	/**
	 * Enqueue color picker lib.
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker-alpha', XTS_FRAMEWORK_URL . '/assets/js-libs/wp-color-picker-alpha.js', array( 'wp-color-picker' ), XTS_VERSION, true );
	}

	/**
	 * Output field's css code on the color.
	 *
	 * @since 1.0.0
	 *
	 * @return string $output Generated CSS code.
	 */
	public function css_output() {
		if ( empty( $this->get_field_value( 'idle' ) ) || ! $this->get_field_value( 'css_output' ) ) {
			return '';
		}

		$output = '';

		if ( isset( $this->args['selector'] ) ) {
			$output .= $this->args['selector'] . '{' . "\n";
			$output .= "\t" . 'color:' . $this->get_field_value( 'idle' ) . ';' . "\n";
			$output .= '}' . "\n\n";
		}

		if ( isset( $this->args['selector_hover'] ) ) {
			$output .= $this->add_hover_state( $this->args['selector_hover'], isset( $this->args['auto_hover_selector'] ) ) . '{' . "\n";
			$output .= "\t" . 'color:' . $this->get_field_value( 'hover' ) . ';' . "\n";
			$output .= '}' . "\n\n";
		}

		if ( isset( $this->args['selector_bg'] ) ) {
			$output .= $this->args['selector_bg'] . '{' . "\n";
			$output .= "\t" . 'background-color:' . $this->get_field_value( 'idle' ) . ';' . "\n";
			$output .= '}' . "\n\n";
		}

		if ( isset( $this->args['selector_bg_hover'] ) ) {
			$output .= $this->add_hover_state( $this->args['selector_bg_hover'], isset( $this->args['auto_hover_selector'] ) ) . '{' . "\n";
			$output .= "\t" . 'background-color:' . $this->get_field_value( 'hover' ) . ';' . "\n";
			$output .= '}' . "\n\n";
		}

		if ( isset( $this->args['selector_border'] ) ) {
			$output .= $this->args['selector_border'] . '{' . "\n";
			$output .= "\t" . 'border-color:' . $this->get_field_value( 'idle' ) . ';' . "\n";
			$output .= '}' . "\n\n";
		}

		if ( isset( $this->args['selector_darken_hover'] ) ) {
			$output .= $this->args['selector_darken_hover'] . '{' . "\n";
			$output .= "\t" . 'background-color:' . $this->get_field_value( 'idle' ) . ';' . "\n";
			if ( xts_theme_supports( 'buttons-shadow' ) ) {
				$output .= "\t" . 'box-shadow: 1px 2px 13px ' . $this->adjust_color( $this->get_field_value( 'idle' ), 0, -0.5 ) . ';' . "\n";
			}
			$output .= '}' . "\n";

			$output .= $this->add_hover_state( $this->args['selector_darken_hover'], isset( $this->args['auto_hover_selector'] ) ) . '{' . "\n";
			$output .= "\t" . 'background-color:' . $this->adjust_color( $this->get_field_value( 'idle' ), 7 ) . ';' . "\n";
			if ( xts_theme_supports( 'buttons-shadow' ) ) {
				$output .= "\t" . 'box-shadow: 1px 2px 13px ' . $this->adjust_color( $this->get_field_value( 'idle' ), 7, -0.3 ) . ';' . "\n";
			}
			$output .= '}' . "\n\n";
		}


		return $output;
	}

	/**
	 * Add :hover state to selector
	 *
	 * @since 1.0.0
	 *
	 * @param string  $selector Selector to work with.
	 * @param boolean $auto Is add hover state.
	 *
	 * @return string
	 */
	private function add_hover_state( $selector, $auto = false ) {
		if ( ! $auto ) {
			return $selector;
		}

		$parts = explode( ',', $selector );

		return implode( ':hover,', $parts ) . ':hover';
	}

	/**
	 * Adjust color brightness.
	 *
	 * @since 1.0.0
	 *
	 * @param string $color_code          Color to adjust.
	 * @param int    $percentage_adjuster 0-100 adjust koef.
	 * @param int    $alpha               Add alpha channel.
	 *
	 * @return string
	 */
	private function adjust_color( $color_code, $percentage_adjuster = 0, $alpha = 0 ) {
		$percentage_adjuster = round( $percentage_adjuster / 100, 2 );

		$r = $g = $b = $a = 0; // phpcs:ignore

		if ( substr( $color_code, 0, 3 ) === 'rgb' ) {
			$rgba  = array();
			$regex = '#\((([^()]+|(?R))*)\)#';
			if ( preg_match_all( $regex, $color_code, $matches ) ) {
				$rgba = explode( ',', implode( ' ', $matches[1] ) );
			} else {
				$rgba = explode( ',', $color_code );
			}

			$r = ( $rgba['0'] );
			$g = ( $rgba['1'] );
			$b = ( $rgba['2'] );
			$a = '';

			$r = $r - ( round( $r ) * $percentage_adjuster );
			$g = $g - ( round( $g ) * $percentage_adjuster );
			$b = $b - ( round( $b ) * $percentage_adjuster );

			if ( array_key_exists( '3', $rgba ) ) {
				$a = $rgba['3'];
			}
		} elseif ( preg_match( '/#/', $color_code ) ) {
			$hex = str_replace( '#', '', $color_code );
			$r   = ( strlen( $hex ) == 3 ) ? hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) ) : hexdec( substr( $hex, 0, 2 ) );
			$g   = ( strlen( $hex ) == 3 ) ? hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) ) : hexdec( substr( $hex, 2, 2 ) );
			$b   = ( strlen( $hex ) == 3 ) ? hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) ) : hexdec( substr( $hex, 4, 2 ) );

			$r = round( $r - ( $r * $percentage_adjuster ) );
			$g = round( $g - ( $g * $percentage_adjuster ) );
			$b = round( $b - ( $b * $percentage_adjuster ) );

			$a = 1;
		}

		$a = $a + $alpha;

		return 'rgba(' . round( max( 0, min( 255, $r ) ) ) . ', ' . round( max( 0, min( 255, $g ) ) ) . ', ' . round( max( 0, min( 255, $b ) ) ) . ', ' . max( 0, min( 1, $a ) ) . ')';
	}
}


