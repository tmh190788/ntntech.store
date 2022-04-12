<?php
/**
 * Video global map file.
 *
 * @package xts
 */

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_add_video_play_button_border_controls' ) ) {
	/**
	 * Add border controls.
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	function xts_add_video_play_button_border_controls( $element ) {
		$element->add_responsive_control(
			'play_button_border_width',
			[
				'label'     => esc_html__( 'Border width', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xts-el-video-play-btn' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		xts_get_color_map(
			$element,
			[
				'key'              => 'play_button_border',
				'switcher_title'   => esc_html__( 'Border color', 'xts-theme' ),
				'divider'          => 'no',
				'normal_selectors' => [
					'{{WRAPPER}} div.xts-el-video .xts-el-video-play-btn' => 'border-color: {{VALUE}}',
				],
				'hover_selectors'  => [
					'{{WRAPPER}} .xts-action-play .xts-el-video-btn:hover .xts-el-video-play-btn, {{WRAPPER}} .xts-action-overlay:hover .xts-el-video-play-btn' => 'border-color: {{VALUE}}',
				],
			]
		);
	}

	add_filter( 'elementor/element/xts_video/style_play_button_section/before_section_end', 'xts_add_video_play_button_border_controls', 10 );
}
