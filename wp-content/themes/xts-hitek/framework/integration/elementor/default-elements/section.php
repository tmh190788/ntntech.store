<?php
/**
 * Elementor section custom controls.
 *
 * @package xts
 */

use Elementor\Controls_Stack;
use Elementor\Plugin;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_section_before_render' ) ) {
	/**
	 * Section before render.
	 *
	 * @since 1.0.0
	 *
	 * @param object $widget Element.
	 */
	function xts_section_before_render( $widget ) {
		$settings = $widget->get_settings_for_display();

		if ( isset( $settings['xts_animation'] ) && $settings['xts_animation'] ) {
			xts_enqueue_js_script( 'animations' );
		}
	}

	add_action( 'elementor/frontend/section/before_render', 'xts_section_before_render', 10 );
}

if ( ! function_exists( 'xts_add_section_class_if_content_width' ) ) {
	/**
	 * Add class to section is content with is set.
	 *
	 * @since 1.0.0
	 *
	 * @param object $widget Element.
	 */
	function xts_add_section_class_if_content_width( $widget ) {
		$settings = $widget->get_settings_for_display();

		if ( isset( $settings['content_width'] ) && isset( $settings['content_width']['size'] ) && ! $settings['content_width']['size'] ) {
			$widget->add_render_attribute( '_wrapper', 'class', 'xts-negative-gap' );
		}
	}
}

if ( ! function_exists( 'xts_section_negative_gap' ) ) {
	/**
	 * Section negative gap fix.
	 *
	 * @since 1.0.0
	 */
	function xts_section_negative_gap() {
		if ( 'enabled' === xts_get_opt( 'negative_gap', 'enabled' ) ) {
			add_action( 'elementor/frontend/section/before_render', 'xts_add_section_class_if_content_width', 10 );
		}

		$negative_gap = get_post_meta( get_the_ID(), '_xts_negative_gap', true );

		if ( 'enabled' === $negative_gap ) {
			add_action(
				'xts_before_site_content_container',
				function() {
					add_action( 'elementor/frontend/section/before_render', 'xts_add_section_class_if_content_width', 10 );
				},
				10
			);

			add_action(
				'xts_after_site_content_container',
				function() {
					remove_action( 'elementor/frontend/section/before_render', 'xts_add_section_class_if_content_width', 10 );
				},
				10
			);
		} elseif ( 'disabled' === $negative_gap ) {
			add_action(
				'xts_before_site_content_container',
				function() {
					remove_action( 'elementor/frontend/section/before_render', 'xts_add_section_class_if_content_width', 10 );
				},
				10
			);

			add_action(
				'xts_after_site_content_container',
				function() {
					add_action( 'elementor/frontend/section/before_render', 'xts_add_section_class_if_content_width', 10 );
				},
				10
			);
		}
	}

	add_action( 'wp', 'xts_section_negative_gap' );
}

if ( ! function_exists( 'xts_add_section_hide_bg_on_devices_control' ) ) {
	/**
	 * Section hide bg on devices option.
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	function xts_add_section_hide_bg_on_devices_control( $element ) {
		$element->add_control(
			'xts_hide_bg_heading',
			[
				'label'      => esc_html__( '[XTemos] Options', 'xts-theme' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'background_background',
							'operator' => '===',
							'value'    => 'classic',
						],
						[
							'name'     => 'background_background',
							'operator' => '===',
							'value'    => 'gradient',
						],
						[
							'name'     => 'background_hover_background',
							'operator' => '===',
							'value'    => 'classic',
						],
						[
							'name'     => 'background_hover_background',
							'operator' => '===',
							'value'    => 'gradient',
						],
					],
				],
			]
		);

		$element->add_control(
			'xts_hide_bg_on_tablet',
			[
				'label'        => esc_html__( 'Hide background on tablet', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'hide-bg-md',
				'render_type'  => 'template',
				'prefix_class' => 'xts-',
				'conditions'   => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'background_background',
							'operator' => '===',
							'value'    => 'classic',
						],
						[
							'name'     => 'background_background',
							'operator' => '===',
							'value'    => 'gradient',
						],
						[
							'name'     => 'background_hover_background',
							'operator' => '===',
							'value'    => 'classic',
						],
						[
							'name'     => 'background_hover_background',
							'operator' => '===',
							'value'    => 'gradient',
						],
					],
				],
			]
		);

		$element->add_control(
			'xts_hide_bg_on_mobile',
			[
				'label'        => esc_html__( 'Hide background on mobile', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'hide-bg-sm',
				'render_type'  => 'template',
				'prefix_class' => 'xts-',
				'conditions'   => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'background_background',
							'operator' => '===',
							'value'    => 'classic',
						],
						[
							'name'     => 'background_background',
							'operator' => '===',
							'value'    => 'gradient',
						],
						[
							'name'     => 'background_hover_background',
							'operator' => '===',
							'value'    => 'classic',
						],
						[
							'name'     => 'background_hover_background',
							'operator' => '===',
							'value'    => 'gradient',
						],
					],
				],
			]
		);
	}

	add_action( 'elementor/element/section/section_background/before_section_end', 'xts_add_section_hide_bg_on_devices_control' );
}

if ( ! function_exists( 'xts_add_section_full_width_control' ) ) {
	/**
	 * Section full width option.
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	function xts_add_section_full_width_control( $element ) {
		$element->start_controls_section(
			'xts_extra_layout',
			[
				'label' => esc_html__( '[XTemos] Layout', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			]
		);

		$options = array(
			'disabled'        => esc_html__( 'Disabled', 'xts-theme' ),
			'stretch'         => esc_html__( 'Stretch section', 'xts-theme' ),
			'stretch-content' => esc_html__( 'Stretch section and content', 'xts-theme' ),
		);

		$element->add_control(
			'xts_section_stretch',
			[
				'label'        => esc_html__( 'Stretch Section CSS', 'xts-theme' ),
				'description'  => esc_html__( 'Enable this option instead of native Elementor\'s one to stretch section with CSS and not with JS.', 'xts-theme' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'disabled',
				'options'      => $options,
				'render_type'  => 'template',
				'prefix_class' => 'xts-section-',
			]
		);

		$element->end_controls_section();
	}

	add_action( 'elementor/element/section/section_layout/after_section_end', 'xts_add_section_full_width_control' );
}

if ( ! function_exists( 'xts_add_section_custom_controls' ) ) {
	/**
	 * Column section controls.
	 *
	 * @since 1.0.0
	 *
	 * @param Controls_Stack $element The control.
	 */
	function xts_add_section_custom_controls( $element ) {
		$element->start_controls_section(
			'xts_extra_advanced',
			[
				'label' => esc_html__( '[XTemos] Extra', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

		/**
		 * Animations
		 */
		xts_get_animation_map( $element );

		$element->end_controls_section();
	}

	add_action( 'elementor/element/section/section_advanced/after_section_end', 'xts_add_section_custom_controls' );
}

if ( ! function_exists( 'xts_override_section_margin_control' ) ) {
	/**
	 * Add custom fonts to theme group
	 *
	 * @since 1.0.0
	 *
	 * @param Controls_Stack $control_stack The control.
	 */
	function xts_override_section_margin_control( $control_stack ) {
		$control = Plugin::instance()->controls_manager->get_control_from_stack( $control_stack->get_unique_name(), 'margin' );

		$control['allowed_dimensions'] = [ 'top', 'right', 'bottom', 'left' ];
		$control['placeholder']        = [
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		];
		$control['selectors']          = [
			'{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		];

		$control_stack->update_responsive_control( 'margin', $control );
	}

	add_action( 'elementor/element/section/section_advanced/before_section_end', 'xts_override_section_margin_control', 10, 2 );
}
