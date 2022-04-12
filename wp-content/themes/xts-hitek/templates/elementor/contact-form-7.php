<?php
/**
 * Contact form 7 template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}


if ( ! function_exists( 'xts_contact_form_7_template' ) ) {
	/**
	 * Contact form 7 template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_contact_form_7_template( $element_args ) {
		$default_args = array(
			'form_id'           => '0',
			'color_scheme'      => 'inherit',
			'form_color_scheme' => 'inherit',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$form_classes = '';

		if ( 'inherit' !== $element_args['color_scheme'] ) {
			$form_classes .= ' xts-scheme-' . $element_args['color_scheme'];
		}

		if ( 'inherit' !== $element_args['form_color_scheme'] ) {
			$form_classes .= ' xts-scheme-' . $element_args['form_color_scheme'] . '-form';
		}

		if ( 'publish' !== get_post_status( $element_args['form_id'] ) ) {
			$posts = get_posts(
				array(
					'posts_per_page' => 1,
					'post_type'      => 'wpcf7_contact_form',
				)
			);

			if ( $posts ) {
				$element_args['form_id'] = $posts[0]->ID;
			}
		}

		if ( ! $element_args['form_id'] || ! xts_is_contact_form_7_installed() ) {
			?>
			<div class="xts-notification xts-color-info">
				<?php esc_html_e( 'You need to create a form using Contact form 7 plugin to be able to display it using this element.', 'xts-theme' ); ?>
			</div>
			<?php
			return;
		}

		echo do_shortcode( '[contact-form-7 id="' . esc_attr( $element_args['form_id'] ) . '" html_class="' . esc_attr( $form_classes ) . '"]' );
	}
}
