<?php
/**
 * Elementor text editor custom controls
 *
 * @package xts
 */

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_add_inherit_font_to_slider_revolution_element' ) ) {
	/**
	 * Add inherit theme front option to slider revolution element
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	function xts_add_inherit_font_to_slider_revolution_element( $element ) {
		$content_font = xts_get_opt( 'content_typography' );
		$title_font   = xts_get_opt( 'title_typography' );

		$content_font_family = isset( $content_font[0]['font-family'] ) && $content_font[0]['font-family'] ? '"' . $content_font[0]['font-family'] . '"' : 'inherit';
		$title_font_family   = isset( $title_font[0]['font-family'] ) && $title_font[0]['font-family'] ? '"' . $title_font[0]['font-family'] . '"' : 'inherit';

		$element->add_control(
			'theme_inherit_font',
			[
				'label'        => esc_html__( 'Inherit theme front', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '-font',
				'prefix_class' => 'xts-inherit',
				'render_type'  => 'template',
				'selectors'    => array(
					'{{WRAPPER}} [data-type="text"], {{WRAPPER}} [data-type="button"]' => 'font-family: ' . $content_font_family . ' !important;',
					'{{WRAPPER}} h1[data-type="text"], {{WRAPPER}} h2[data-type="text"], {{WRAPPER}} h3[data-type="text"], {{WRAPPER}} h4[data-type="text"], {{WRAPPER}} h5[data-type="text"], {{WRAPPER}} h6[data-type="text"] ' => 'font-family: ' . $title_font_family . ' !important;',
				),
			]
		);
	}

	add_filter( 'elementor/element/slider_revolution/content_section/after_section_start', 'xts_add_inherit_font_to_slider_revolution_element', 10, 2 );
}
