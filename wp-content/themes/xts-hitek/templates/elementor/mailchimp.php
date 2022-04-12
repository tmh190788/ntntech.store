<?php
/**
 * Mailchimp template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_mailchimp_template' ) ) {
	/**
	 * Mailchimp template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_mailchimp_template( $element_args ) {
		$default_args = array(
			'form_id'           => '0',
			'color_scheme'      => 'inherit',
			'form_color_scheme' => 'inherit',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$form_classes = '';

		// Form classes.
		if ( 'inherit' !== $element_args['color_scheme'] ) {
			$form_classes .= ' xts-scheme-' . $element_args['color_scheme'];
		}
		if ( 'inherit' !== $element_args['form_color_scheme'] ) {
			$form_classes .= ' xts-scheme-' . $element_args['form_color_scheme'] . '-form';
		}

		if ( ! $element_args['form_id'] || ! xts_is_mailchimp_installed() ) {
			?>
				<div class="xts-notification xts-color-info">
					<?php esc_html_e( 'You need to create a form using MC4WP: Mailchimp for WordPress plugin to be able to display it using this element.', 'xts-theme' ); ?>
				</div>
			<?php
			return;
		}

		echo do_shortcode( '[mc4wp_form id="' . esc_attr( $element_args['form_id'] ) . '" element_class="' . esc_attr( $form_classes ) . '"]' );
	}
}
