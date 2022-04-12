<?php
/**
 * Global map file.
 *
 * @package xts
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_posts_query_map' ) ) {
	/**
	 * Posts query map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element     Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_posts_query_map( $element, $custom_args ) {
		$default_args = array(
			'exclude_search'  => '',
			'exclude_render'  => '',
			'taxonomy'        => '',
			'post_type'       => '',
			'query_type'      => 'no',
			'orderby_options' => [
				'id'             => esc_html__( 'ID', 'xts-theme' ),
				'date'           => esc_html__( 'Date', 'xts-theme' ),
				'title'          => esc_html__( 'Title', 'xts-theme' ),
				'rand'           => esc_html__( 'Random', 'xts-theme' ),
				'menu_order'     => esc_html__( 'Menu order', 'xts-theme' ),
				'author'         => esc_html__( 'Author', 'xts-theme' ),
				'modified'       => esc_html__( 'Last modified date', 'xts-theme' ),
				'comment_count'  => esc_html__( 'Number of comments', 'xts-theme' ),
				'meta_value'     => esc_html__( 'Meta value', 'xts-theme' ), // phpcs:ignore
				'meta_value_num' => esc_html__( 'Meta value number', 'xts-theme' ),
				'post__in'       => esc_html__( 'Include order', 'xts-theme' ),
			],
		);

		$args = wp_parse_args( $custom_args, $default_args );

		$element->add_control(
			'query_heading',
			[
				'label'     => esc_html__( 'Query', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$element->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Order by', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'id',
				'options' => $args['orderby_options'],
			]
		);

		if ( 'yes' === $args['query_type'] ) {
			$element->add_control(
				'query_type',
				[
					'label'   => esc_html__( 'Query type', 'xts-theme' ),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'OR'  => esc_html__( 'OR', 'xts-theme' ),
						'AND' => esc_html__( 'AND', 'xts-theme' ),
					],
					'default' => 'OR',
				]
			);
		}

		$element->add_control(
			'order',
			[
				'label'   => esc_html__( 'Order', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'desc' => esc_html__( 'Descending', 'xts-theme' ),
					'asc'  => esc_html__( 'Ascending', 'xts-theme' ),
				],
			]
		);

		$element->add_control(
			'offset',
			[
				'label'       => esc_html__( 'Offset', 'xts-theme' ),
				'description' => esc_html__( 'You can set a number of posts to displace or pass over', 'xts-theme' ),
				'type'        => Controls_Manager::NUMBER,
			]
		);

		$element->add_control(
			'meta_key',
			[
				'label'     => esc_html__( 'Meta key', 'xts-theme' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'orderby' => [
						'meta_value',
						'meta_value_num',
					],
				],
			]
		);

		$element->add_control(
			'exclude',
			[
				'label'       => esc_html__( 'Exclude', 'xts-theme' ),
				'type'        => 'xts_autocomplete',
				'search'      => $args['exclude_search'],
				'render'      => $args['exclude_render'],
				'taxonomy'    => $args['taxonomy'],
				'post_type'   => $args['post_type'],
				'multiple'    => true,
				'label_block' => true,
			]
		);
	}
}


if ( ! function_exists( 'xts_get_lazy_loading_map' ) ) {
	/**
	 * Get lazy loading map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_lazy_loading_map( $element ) {
		$element->add_control(
			'lazy_loading',
			[
				'label'       => esc_html__( 'Lazy loading', 'xts-theme' ),
				'description' => esc_html__( 'Enable lazy loading for images for this element.', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'inherit' => esc_html__( 'Inherit', 'xts-theme' ),
					'yes'     => esc_html__( 'Enable', 'xts-theme' ),
					'no'      => esc_html__( 'Disable', 'xts-theme' ),
				],
				'default'     => 'inherit',
			]
		);
	}
}

if ( ! function_exists( 'xts_get_animation_map' ) ) {
	/**
	 * Get animation map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_animation_map( $element, $custom_args = array() ) {
		$default_args = array(
			'key'       => '',
			'type'      => 'element',
			'condition' => array(),
		);

		$args = wp_parse_args( $custom_args, $default_args );

		$animation = [
			'label'       => esc_html__( 'Animations', 'xts-theme' ),
			'description' => esc_html__( 'Add an appearance animation effect to this element.', 'xts-theme' ),
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'options'     => xts_get_animations_array(),
			'default'     => 'short-in-up',
		];

		$animation_duration = [
			'label'     => esc_html__( 'Animation duration', 'xts-theme' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'fast',
			'options'   => [
				'slow'   => esc_html__( 'Slow', 'xts-theme' ),
				'normal' => esc_html__( 'Normal', 'xts-theme' ),
				'fast'   => esc_html__( 'Fast', 'xts-theme' ),
			],
			'condition' => [
				'xts_animation' . $args['key'] . '!' => '',
			],
		];

		$animation_delay = [
			'label'     => esc_html__( 'Animation delay', 'xts-theme' ) . ' (ms)',
			'type'      => Controls_Manager::NUMBER,
			'default'   => 100,
			'min'       => 0,
			'step'      => 100,
			'condition' => [
				'xts_animation' . $args['key'] . '!' => '',
			],
		];

		if ( 'element' === $args['type'] ) {
			$animation['render_type']  = 'template';
			$animation['prefix_class'] = 'xts-animation-';
			$animation['default']      = '';

			$animation_duration['render_type']  = 'template';
			$animation_duration['prefix_class'] = 'xts-animation-';

			$animation_delay['render_type']  = 'template';
			$animation_delay['prefix_class'] = 'xts_delay_';
		}

		if ( $args['condition'] ) {
			$animation['condition']          = $args['condition'];
			$animation_duration['condition'] = $animation_duration['condition'] + $args['condition'];
			$animation_delay['condition']    = $animation_delay['condition'] + $args['condition'];
		}

		if ( 'items' === $args['type'] ) {
			$element->add_control(
				'animation_in_view',
				[
					'label'        => esc_html__( 'Animation in view', 'xts-theme' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
					'label_off'    => esc_html__( 'No', 'xts-theme' ),
					'return_value' => 'yes',
				]
			);
		}

		$element->add_control(
			'xts_animation' . $args['key'],
			$animation
		);

		$element->add_control(
			'xts_animation_duration' . $args['key'],
			$animation_duration
		);

		$element->add_control(
			'xts_animation_delay' . $args['key'],
			$animation_delay
		);
	}
}

if ( ! function_exists( 'xts_get_color_map' ) ) {
	/**
	 * Get color settings map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_color_map( $element, $custom_args = array() ) {
		$default_args = [
			'key'              => '',
			'normal_selectors' => false,
			'active_selectors' => false,
			'hover_selectors'  => false,
			'divider'          => 'yes',
			'switcher_default' => 'no',
			'switcher_title'   => esc_html__( 'Color', 'xts-theme' ),
		];

		$args = wp_parse_args( $custom_args, $default_args );

		// Switcher.
		$element->add_control(
			$args['key'] . '_color_switcher',
			[
				'label'        => $args['switcher_title'],
				'type'         => Controls_Manager::SWITCHER,
				'default'      => $args['switcher_default'],
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		// Normal.
		if ( $args['hover_selectors'] || $args['active_selectors'] ) {
			$element->start_controls_tabs(
				$args['key'] . '_color_tabs',
				[
					'condition' => [
						$args['key'] . '_color_switcher' => [ 'yes' ],
					],
				]
			);

			$element->start_controls_tab(
				$args['key'] . '_color_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'xts-theme' ),
				]
			);
		}

		$element->add_control(
			$args['key'] . '_normal_color',
			[
				'label'     => esc_html__( 'Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $args['normal_selectors'],
			]
		);

		// Hover.
		if ( $args['hover_selectors'] ) {
			$element->end_controls_tab();

			$element->start_controls_tab(
				$args['key'] . '_color_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'xts-theme' ),
				]
			);

			$element->add_control(
				$args['key'] . '_hover_color',
				[
					'label'     => esc_html__( 'Hover color', 'xts-theme' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => $args['hover_selectors'],
				]
			);

			$element->end_controls_tab();
		}

		// Active.
		if ( $args['active_selectors'] ) {
			$element->start_controls_tab(
				$args['key'] . '_color_active_tab',
				[
					'label' => esc_html__( 'Active', 'xts-theme' ),
				]
			);

			$element->add_control(
				$args['key'] . '_active_color',
				[
					'label'     => esc_html__( 'Active color', 'xts-theme' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => $args['active_selectors'],
				]
			);

			$element->end_controls_tab();
		}

		if ( $args['hover_selectors'] || $args['active_selectors'] ) {
			$element->end_controls_tabs();
		}

		if ( 'yes' === $args['divider'] ) {
			$element->add_control(
				$args['key'] . '_color_divider',
				[
					'type'      => Controls_Manager::DIVIDER,
					'style'     => 'thick',
					'condition' => [
						$args['key'] . '_color_switcher' => [ 'yes' ],
					],
				]
			);
		}
	}
}

if ( ! function_exists( 'xts_get_border_color_map' ) ) {
	/**
	 * Get color settings map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_border_color_map( $element, $custom_args = array() ) {
		$default_args = [
			'key'              => '',
			'normal_selectors' => false,
			'active_selectors' => false,
			'hover_selectors'  => false,
			'switcher_default' => 'no',
		];

		$args = wp_parse_args( $custom_args, $default_args );

		// Switcher.
		$element->add_control(
			$args['key'] . '_border_color_switcher',
			[
				'label'        => esc_html__( 'Border color', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => $args['switcher_default'],
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		// Normal.
		if ( $args['hover_selectors'] || $args['active_selectors'] ) {
			$element->start_controls_tabs(
				$args['key'] . '_border_color_tabs',
				[
					'condition' => [
						$args['key'] . '_border_color_switcher' => [ 'yes' ],
					],
				]
			);

			$element->start_controls_tab(
				$args['key'] . '_border_color_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'xts-theme' ),
				]
			);
		}

		$element->add_control(
			$args['key'] . '_normal_border_color',
			[
				'label'     => esc_html__( 'Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $args['normal_selectors'],
			]
		);

		// Hover.
		if ( $args['hover_selectors'] ) {
			$element->end_controls_tab();

			$element->start_controls_tab(
				$args['key'] . '_border_color_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'xts-theme' ),
				]
			);

			$element->add_control(
				$args['key'] . '_hover_border_color',
				[
					'label'     => esc_html__( 'Hover color', 'xts-theme' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => $args['hover_selectors'],
				]
			);

			$element->end_controls_tab();
		}

		// Active.
		if ( $args['active_selectors'] ) {
			$element->start_controls_tab(
				$args['key'] . '_border_color_active_tab',
				[
					'label' => esc_html__( 'Active', 'xts-theme' ),
				]
			);

			$element->add_control(
				$args['key'] . '_active_border_color',
				[
					'label'     => esc_html__( 'Active color', 'xts-theme' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => $args['active_selectors'],
				]
			);

			$element->end_controls_tab();
		}

		if ( $args['hover_selectors'] || $args['active_selectors'] ) {
			$element->end_controls_tabs();
		}

		$element->add_control(
			$args['key'] . '_border_color_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'style'     => 'thick',
				'condition' => [
					$args['key'] . '_border_color_switcher' => [ 'yes' ],
				],
			]
		);
	}
}

if ( ! function_exists( 'xts_get_background_color_map' ) ) {
	/**
	 * Get color settings map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_background_color_map( $element, $custom_args = array() ) {
		$default_args = [
			'key'                  => '',
			'normal_selectors'     => false,
			'normal_default_color' => false,
			'active_selectors'     => false,
			'hover_selectors'      => false,
			'switcher_default'     => 'no',
			'divider'              => 'yes',
		];

		$args = wp_parse_args( $custom_args, $default_args );

		// Switcher.
		$element->add_control(
			$args['key'] . '_background_color_switcher',
			[
				'label'        => esc_html__( 'Background color', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => $args['switcher_default'],
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		// Normal.
		if ( $args['hover_selectors'] || $args['active_selectors'] ) {
			$element->start_controls_tabs(
				$args['key'] . '_background_color_tabs',
				[
					'condition' => [
						$args['key'] . '_background_color_switcher' => [ 'yes' ],
					],
				]
			);

			$element->start_controls_tab(
				$args['key'] . '_background_color_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'xts-theme' ),
				]
			);
		}

		$element->add_control(
			$args['key'] . '_normal_background_color',
			[
				'label'     => esc_html__( 'Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $args['normal_selectors'],
				'default'   => $args['normal_default_color'],
				'condition' => [
					$args['key'] . '_background_color_switcher' => [ 'yes' ],
				],
			]
		);

		// Hover.
		if ( $args['hover_selectors'] ) {
			$element->end_controls_tab();

			$element->start_controls_tab(
				$args['key'] . '_background_color_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'xts-theme' ),
				]
			);

			$element->add_control(
				$args['key'] . '_hover_background_color',
				[
					'label'     => esc_html__( 'Hover color', 'xts-theme' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => $args['hover_selectors'],
				]
			);

			$element->end_controls_tab();
		}

		// Active.
		if ( $args['active_selectors'] ) {
			$element->start_controls_tab(
				$args['key'] . '_background_color_active_tab',
				[
					'label' => esc_html__( 'Active', 'xts-theme' ),
				]
			);

			$element->add_control(
				$args['key'] . '_active_background_color',
				[
					'label'     => esc_html__( 'Active color', 'xts-theme' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => $args['active_selectors'],
				]
			);

			$element->end_controls_tab();
		}

		if ( $args['hover_selectors'] || $args['active_selectors'] ) {
			$element->end_controls_tabs();
		}

		if ( 'yes' === $args['divider'] ) {
			$element->add_control(
				$args['key'] . '_background_color_divider',
				[
					'type'      => Controls_Manager::DIVIDER,
					'style'     => 'thick',
					'condition' => [
						$args['key'] . '_background_color_switcher' => [ 'yes' ],
					],
				]
			);
		}
	}
}

if ( ! function_exists( 'xts_get_border_map' ) ) {
	/**
	 * Get border settings map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_border_map( $element, $custom_args = array() ) {
		$default_args = [
			'key'              => '',
			'normal_selector'  => false,
			'active_selector'  => false,
			'hover_selector'   => false,
			'switcher_default' => 'no',
			'divider'          => 'yes',
		];

		$args = wp_parse_args( $custom_args, $default_args );

		$element->add_control(
			$args['key'] . '_border_switcher',
			[
				'label'        => esc_html__( 'Border', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => $args['switcher_default'],
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$element->add_responsive_control(
			$args['key'] . '_border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$args['normal_selector'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					$args['key'] . '_border_switcher' => [ 'yes' ],
				],
			]
		);

		if ( $args['hover_selector'] || $args['active_selector'] ) {
			$element->start_controls_tabs(
				$args['key'] . '_border_tabs',
				[
					'condition' => [
						$args['key'] . '_border_switcher' => [ 'yes' ],
					],
				]
			);

			$element->start_controls_tab(
				$args['key'] . '_normal_border_tab',
				[
					'label' => esc_html__( 'Normal', 'xts-theme' ),
				]
			);
		}

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => $args['key'] . '_border_normal',
				'selector'  => $args['normal_selector'],
				'condition' => [
					$args['key'] . '_border_switcher' => [ 'yes' ],
				],
			]
		);

		if ( $args['hover_selector'] ) {
			$element->end_controls_tab();

			$element->start_controls_tab(
				$args['key'] . '_border_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'xts-theme' ),
				]
			);

			$element->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => $args['key'] . '_border_hover',
					'selector'  => $args['hover_selector'],
					'condition' => [
						$args['key'] . '_border_switcher' => [ 'yes' ],
					],
				]
			);

			$element->end_controls_tab();
		}

		if ( $args['active_selector'] ) {
			$element->start_controls_tab(
				$args['key'] . '_border_active_tab',
				[
					'label' => esc_html__( 'Active', 'xts-theme' ),
				]
			);

			$element->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => $args['key'] . '_border_active',
					'selector'  => $args['active_selector'],
					'condition' => [
						$args['key'] . '_border_switcher' => [ 'yes' ],
					],
				]
			);

			$element->end_controls_tab();
		}

		if ( $args['hover_selector'] || $args['active_selector'] ) {
			$element->end_controls_tabs();
		}

		if ( 'yes' === $args['divider'] ) {
			$element->add_control(
				$args['key'] . '_border_divider',
				[
					'type'      => Controls_Manager::DIVIDER,
					'style'     => 'thick',
					'condition' => [
						$args['key'] . '_border_switcher' => [ 'yes' ],
					],
				]
			);
		}
	}
}

if ( ! function_exists( 'xts_get_color_scheme_map' ) ) {
	/**
	 * Get color scheme settings map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_color_scheme_map( $element, $custom_args = array() ) {
		$default_args = [
			'key'              => '',
			'switcher_default' => 'no',
		];

		$args = wp_parse_args( $custom_args, $default_args );

		$element->add_control(
			$args['key'] . '_color_scheme_switcher',
			[
				'label'        => esc_html__( 'Color scheme', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => $args['switcher_default'],
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			]
		);

		$element->start_controls_tabs(
			$args['key'] . '_color_scheme_tabs',
			[
				'condition' => [
					$args['key'] . '_color_scheme_switcher' => [ 'yes' ],
				],
			]
		);

		$element->start_controls_tab(
			$args['key'] . '_color_scheme_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'xts-theme' ),
			]
		);

		$element->add_control(
			$args['key'] . '_color_scheme',
			[
				'label'   => esc_html__( 'Color scheme', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'inherit' => [
						'title' => esc_html__( 'Inherit', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/inherit.svg',
					],
					'dark'    => [
						'title' => esc_html__( 'Dark', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/dark.svg',
					],
					'light'   => [
						'title' => esc_html__( 'Light', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/light.svg',
					],
				],
				'default' => 'inherit',
			]
		);

		$element->end_controls_tab();

		$element->start_controls_tab(
			$args['key'] . '_color_scheme_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'xts-theme' ),
			]
		);

		$element->add_control(
			$args['key'] . '_color_scheme_hover',
			[
				'label'   => esc_html__( 'Hover color scheme', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'inherit' => [
						'title' => esc_html__( 'Inherit', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/inherit.svg',
					],
					'dark'    => [
						'title' => esc_html__( 'Dark', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/dark.svg',
					],
					'light'   => [
						'title' => esc_html__( 'Light', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/color/light.svg',
					],
				],
				'default' => 'inherit',
			]
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

		$element->add_control(
			$args['key'] . '_color_scheme_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'style'     => 'thick',
				'condition' => [
					$args['key'] . '_color_scheme_switcher' => [ 'yes' ],
				],
			]
		);
	}
}

if ( ! function_exists( 'xts_get_background_map' ) ) {
	/**
	 * Get background settings map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_background_map( $element, $custom_args = array() ) {
		$default_args = [
			'key'                  => '',
			'normal_selector'      => false,
			'active_selector'      => false,
			'hover_selector'       => false,
			'switcher_default'     => 'no',
			'normal_default_color' => 'rgba(255,255,255,1)',
			'switcher_condition'   => array(),
			'tabs_condition'       => array(),
		];

		$args = wp_parse_args( $custom_args, $default_args );

		$element->add_control(
			$args['key'] . '_background_switcher',
			[
				'label'        => esc_html__( 'Background', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => $args['switcher_default'],
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
				'condition'    => $args['switcher_condition'],
			]
		);

		if ( $args['hover_selector'] || $args['active_selector'] ) {
			$element->start_controls_tabs(
				$args['key'] . '_background_tabs',
				[
					'condition' => [ $args['key'] . '_background_switcher' => [ 'yes' ] ] + $args['tabs_condition'],
				]
			);

			$element->start_controls_tab(
				$args['key'] . '_background_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'xts-theme' ),
				]
			);
		}

		$element->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => $args['key'] . '_background_normal',
				'selector'       => $args['normal_selector'],
				'condition'      => [ $args['key'] . '_background_switcher' => [ 'yes' ] ] + $args['tabs_condition'],
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color'      => [
						'default' => $args['normal_default_color'],
					],
				],
			]
		);

		if ( $args['hover_selector'] ) {
			$element->end_controls_tab();

			$element->start_controls_tab(
				$args['key'] . '_background_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'xts-theme' ),
				]
			);

			$element->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => $args['key'] . '_background_hover',
					'selector' => $args['hover_selector'],
				]
			);

			$element->end_controls_tab();
		}

		if ( $args['active_selector'] ) {
			$element->start_controls_tab(
				$args['key'] . '_background_active_tab',
				[
					'label' => esc_html__( 'Active', 'xts-theme' ),
				]
			);

			$element->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => $args['key'] . '_background_active',
					'selector' => $args['active_selector'],
				]
			);

			$element->end_controls_tab();
		}

		if ( $args['hover_selector'] || $args['active_selector'] ) {
			$element->end_controls_tabs();
		}

		$element->add_control(
			$args['key'] . '_background_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'style'     => 'thick',
				'condition' => [ $args['key'] . '_background_switcher' => [ 'yes' ] ] + $args['tabs_condition'],
			]
		);
	}
}

if ( ! function_exists( 'xts_get_shadow_map' ) ) {
	/**
	 * Get shadow settings map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_shadow_map( $element, $custom_args = array() ) {
		$default_args = [
			'key'                => '',
			'normal_selector'    => false,
			'active_selector'    => false,
			'hover_selector'     => false,
			'switcher_default'   => 'no',
			'switcher_condition' => array(),
			'tabs_condition'     => array(),
			'divider'            => 'yes',
		];

		$args = wp_parse_args( $custom_args, $default_args );

		// Switcher.
		$element->add_control(
			$args['key'] . '_shadow_switcher',
			[
				'label'        => esc_html__( 'Shadow', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => $args['switcher_default'],
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
				'condition'    => $args['switcher_condition'],
			]
		);

		// Normal.
		if ( $args['hover_selector'] || $args['active_selector'] ) {
			$element->start_controls_tabs(
				$args['key'] . '_shadow_tabs',
				[
					'condition' => [ $args['key'] . '_shadow_switcher' => [ 'yes' ] ] + $args['tabs_condition'],
				]
			);

			$element->start_controls_tab(
				$args['key'] . '_shadow_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'xts-theme' ),
				]
			);
		}

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => $args['key'] . '_shadow_normal',
				'selector'  => $args['normal_selector'],
				'condition' => [ $args['key'] . '_shadow_switcher' => [ 'yes' ] ] + $args['tabs_condition'],
			]
		);

		// Hover.
		if ( $args['hover_selector'] ) {
			$element->end_controls_tab();

			$element->start_controls_tab(
				$args['key'] . '_shadow_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'xts-theme' ),
				]
			);

			$element->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => $args['key'] . '_shadow_hover',
					'selector' => $args['hover_selector'],
				]
			);

			$element->end_controls_tab();
		}

		// Active.
		if ( $args['active_selector'] ) {
			$element->start_controls_tab(
				$args['key'] . '_shadow_active_tab',
				[
					'label' => esc_html__( 'Active', 'xts-theme' ),
				]
			);

			$element->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => $args['key'] . '_shadow_active',
					'selector' => $args['active_selector'],
				]
			);

			$element->end_controls_tab();
		}

		if ( $args['hover_selector'] || $args['active_selector'] ) {
			$element->end_controls_tabs();
		}

		if ( 'yes' === $args['divider'] ) {
			$element->add_control(
				$args['key'] . '_shadow_divider',
				[
					'type'      => Controls_Manager::DIVIDER,
					'style'     => 'thick',
					'condition' => [ $args['key'] . '_shadow_switcher' => [ 'yes' ] ] + $args['tabs_condition'],
				]
			);
		}
	}
}

if ( ! function_exists( 'xts_get_typography_map' ) ) {
	/**
	 * Get typography map
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 * @param array  $custom_args Custom args.
	 */
	function xts_get_typography_map( $element, $custom_args = array() ) {
		$default_args = array(
			'key'                   => '',
			'selector'              => '',
			'hover_selector'        => '',
			'text_size_default'     => 'default',
			'color_presets_default' => 'default',
			'font_presets_default'  => 'default',
			'size_presets'          => 'yes',
			'typography'            => 'yes',
			'text_size_options'     => array(
				'default' => esc_html__( 'Default', 'xts-theme' ),
				'xs'      => esc_html__( 'Extra small', 'xts-theme' ),
				's'       => esc_html__( 'Small', 'xts-theme' ),
				'm'       => esc_html__( 'Medium', 'xts-theme' ),
				'l'       => esc_html__( 'Large', 'xts-theme' ),
				'xl'      => esc_html__( 'Extra large', 'xts-theme' ),
				'xxl'     => esc_html__( 'Extra extra large', 'xts-theme' ),
			),
			'color_presets_options' => array(
				'default'   => esc_html__( 'Default', 'xts-theme' ),
				'primary'   => esc_html__( 'Primary', 'xts-theme' ),
				'secondary' => esc_html__( 'Secondary', 'xts-theme' ),
				'white'     => esc_html__( 'White', 'xts-theme' ),
				'custom'    => esc_html__( 'Custom', 'xts-theme' ),
			),
		);

		$args = wp_parse_args( $custom_args, $default_args );

		if ( 'yes' === $args['size_presets'] ) {
			$element->add_control(
				$args['key'] . '_text_size',
				[
					'label'   => esc_html__( 'Size presets', 'xts-theme' ),
					'type'    => Controls_Manager::SELECT,
					'options' => $args['text_size_options'],
					'default' => $args['text_size_default'],
				]
			);
		}

		$element->add_control(
			$args['key'] . '_color_presets',
			[
				'label'   => esc_html__( 'Color presets', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $args['color_presets_options'],
				'default' => $args['color_presets_default'],
			]
		);

		$element->add_control(
			$args['key'] . '_custom_color',
			[
				'label'     => esc_html__( 'Custom color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$args['selector'] => 'color: {{VALUE}}',
				],
				'condition' => [
					$args['key'] . '_color_presets' => 'custom',
				],
			]
		);

		if ( $args['hover_selector'] ) {
			$element->add_control(
				$args['key'] . '_custom_hover_color',
				[
					'label'     => esc_html__( 'Custom hover color', 'xts-theme' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						$args['hover_selector'] => 'color: {{VALUE}}',
					],
					'condition' => [
						$args['key'] . '_color_presets' => 'custom',
					],
				]
			);
		}

		if ( 'yes' === $args['typography'] ) {
			$element->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => $args['key'] . '_custom_typography',
					'label'    => esc_html__( 'Custom typography', 'xts-theme' ),
					'selector' => $args['selector'],
				]
			);
		}
	}
}
