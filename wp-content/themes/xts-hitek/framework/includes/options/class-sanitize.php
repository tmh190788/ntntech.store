<?php
/**
 * Sanitize fields values before save
 *
 * @package xts
 */

namespace XTS\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Sanitization class for fields
 */
class Sanitize {
	/**
	 * Field class
	 *
	 * @var Field
	 */
	private $field;

	/**
	 * Initial field value
	 *
	 * @var Field
	 */
	private $value;

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 *
	 * @param object $field Field object.
	 * @param string $value field value.
	 */
	public function __construct( $field, $value ) {
		$this->field = $field;
		$this->value = $value;
	}

	/**
	 * Run field value sanitization.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed value
	 */
	public function sanitize() {
		global $wpdb;

		$val = $this->value;

		switch ( $this->field->args['type'] ) {
			case 'size_guide_table':
			case 'custom_fonts':
			case 'typography':
				// TODO: sanitize complex array.
				break;

			case 'textarea':
				$val     = wp_kses_post( $val );
				$charset = $wpdb->get_col_charset( $wpdb->options, 'options_value' );
				if ( 'utf8' === $charset ) {
					$val = wp_encode_emoji( $val );
				}
				break;

			case 'text_input':
				$val     = sanitize_text_field( $val );
				$charset = $wpdb->get_col_charset( $wpdb->options, 'options_value' );
				if ( 'utf8' === $charset ) {
					$val = wp_encode_emoji( $val );
				}
				break;

			case 'editor':
				if ( 'css' === $this->field->args['language'] ) {
					$val = wp_filter_nohtml_kses( $val );
					$val = str_replace( '&gt;', '>', $val );
					$val = stripslashes( $val );
				} elseif ( 'js' === $this->field->args['language'] ) {
					$val = esc_js( $val );
				}
				break;

			default:
				$val = is_array( $val ) ? array_map( 'sanitize_text_field', $val ) : sanitize_text_field( $val );
				break;
		}

		return $val;
	}
}
