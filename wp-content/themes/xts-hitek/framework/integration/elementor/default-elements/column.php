<?php
/**
 * Elementor column custom controls
 *
 * @package xts
 */

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_column_before_render' ) ) {
	/**
	 * Column before render.
	 *
	 * @since 1.0.0
	 *
	 * @param object $widget Element.
	 */
	function xts_column_before_render( $widget ) {
		$settings = $widget->get_settings_for_display();

		if ( isset( $settings['column_sticky'] ) && $settings['column_sticky'] ) {
			xts_enqueue_js_library( 'sticky-kit' );
			xts_enqueue_js_script( 'sticky-column' );
		}

		if ( isset( $settings['column_parallax'] ) && $settings['column_parallax'] ) {
			xts_enqueue_js_library( 'parallax-scroll' );
		}

		if ( isset( $settings['xts_animation'] ) && $settings['xts_animation'] ) {
			xts_enqueue_js_script( 'animations' );
		}
	}

	add_action( 'elementor/frontend/column/before_render', 'xts_column_before_render', 10 );
}

if ( ! function_exists( 'xts_add_column_custom_controls' ) ) {
	/**
	 * Column custom controls
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	function xts_add_column_custom_controls( $element ) {
		$element->start_controls_section(
			'xts_extra',
			[
				'label' => esc_html__( '[XTemos] Extra', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

		/**
		 * Sticky column
		 */
		$element->add_control(
			'column_sticky',
			[
				'label'        => esc_html__( 'Sticky column', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'sticky-column',
				'prefix_class' => 'xts-',
				'render_type'  => 'template',
			]
		);

		$element->add_control(
			'column_sticky_offset',
			[
				'label'        => esc_html__( 'Sticky column offset (px)', 'xts-theme' ),
				'type'         => Controls_Manager::TEXT,
				'default'      => 50,
				'render_type'  => 'template',
				'prefix_class' => 'xts_sticky_offset_',
				'condition'    => [
					'column_sticky' => [ 'sticky-column' ],
				],
			]
		);

		$element->add_control(
			'column_sticky_hr',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		/**
		 * Column parallax on scroll
		 */
		$element->add_control(
			'column_parallax',
			[
				'label'        => esc_html__( 'Parallax on scroll', 'xts-theme' ),
				'description'  => esc_html__( 'Smooth element movement when you scroll the page to create beautiful parallax effect.', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'parallax-on-scroll',
				'prefix_class' => 'xts-',
				'render_type'  => 'template',
			]
		);

		$element->add_control(
			'scroll_x',
			[
				'label'        => esc_html__( 'X axis translation', 'xts-theme' ),
				'description'  => esc_html__( 'Recommended -200 to 200', 'xts-theme' ),
				'type'         => Controls_Manager::TEXT,
				'default'      => 0,
				'render_type'  => 'template',
				'prefix_class' => 'xts_scroll_x_',
				'condition'    => [
					'column_parallax' => [ 'parallax-on-scroll' ],
				],
			]
		);

		$element->add_control(
			'scroll_y',
			[
				'label'        => esc_html__( 'Y axis translation', 'xts-theme' ),
				'description'  => esc_html__( 'Recommended -200 to 200', 'xts-theme' ),
				'type'         => Controls_Manager::TEXT,
				'default'      => - 80,
				'render_type'  => 'template',
				'prefix_class' => 'xts_scroll_y_',
				'condition'    => [
					'column_parallax' => [ 'parallax-on-scroll' ],
				],
			]
		);

		$element->add_control(
			'scroll_z',
			[
				'label'        => esc_html__( 'Z axis translation', 'xts-theme' ),
				'description'  => esc_html__( 'Recommended -200 to 200', 'xts-theme' ),
				'type'         => Controls_Manager::TEXT,
				'default'      => 0,
				'render_type'  => 'template',
				'prefix_class' => 'xts_scroll_z_',
				'condition'    => [
					'column_parallax' => [ 'parallax-on-scroll' ],
				],
			]
		);

		$element->add_control(
			'scroll_smoothness',
			[
				'label'        => esc_html__( 'Parallax smoothness', 'xts-theme' ),
				'description'  => esc_html__( 'Define the parallax smoothness on mouse scroll. By default - 30', 'xts-theme' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'10'  => '10',
					'20'  => '20',
					'30'  => '30',
					'40'  => '40',
					'50'  => '50',
					'60'  => '60',
					'70'  => '70',
					'80'  => '80',
					'90'  => '90',
					'100' => '100',
				],
				'default'      => '30',
				'render_type'  => 'template',
				'prefix_class' => 'xts_scroll_smoothness_',
				'condition'    => [
					'column_parallax' => [ 'parallax-on-scroll' ],
				],
			]
		);

		$element->add_control(
			'column_parallax_hr',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		/**
		 * Animations
		 */
		xts_get_animation_map( $element );

		$element->end_controls_section();
	}

	add_action( 'elementor/element/column/section_advanced/after_section_end', 'xts_add_column_custom_controls' );
}
