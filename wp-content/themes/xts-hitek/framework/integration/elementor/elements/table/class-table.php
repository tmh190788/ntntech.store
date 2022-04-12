<?php
/**
 * Table map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Table extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_table';
	}

	/**
	 * Get widget title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Table', 'xts-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'xf-el-table';
	}

	/**
	 * Get widget categories.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'xts-elements' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore
		/**
		* Content tab
		* /

		/**
		 * Heading settings
		 */
		$this->start_controls_section(
			'heading_general_section',
			[
				'label' => esc_html__( 'Heading', 'xts-theme' ),
			]
		);

		$heading_repeater = new Repeater();

		$heading_repeater->add_control(
			'heading_content_type',
			[
				'label'   => esc_html__( 'Action', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cell',
				'options' => [
					'row'  => esc_html__( 'Start new row', 'xts-theme' ),
					'cell' => esc_html__( 'Add new cell', 'xts-theme' ),
				],
			]
		);

		$heading_repeater->start_controls_tabs( 'heading_items_tabs' );

		$heading_repeater->start_controls_tab(
			'tab_heading_cell_content',
			[
				'label'     => esc_html__( 'Content', 'xts-theme' ),
				'condition' => [
					'heading_content_type' => 'cell',
				],
			]
		);

		$heading_repeater->add_control(
			'heading_cell_text',
			[
				'label'     => esc_html__( 'Text', 'xts-theme' ),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => 'Content',
				'condition' => [
					'heading_content_type' => 'cell',
				],
			]
		);

		$heading_repeater->end_controls_tab();

		$heading_repeater->start_controls_tab(
			'tab_heading_cell_settings',
			[
				'label'     => esc_html__( 'Settings', 'xts-theme' ),
				'condition' => [
					'heading_content_type' => 'cell',
				],
			]
		);

		$heading_repeater->add_control(
			'heading_cell_span',
			[
				'label'     => esc_html__( 'Column Span', 'xts-theme' ),
				'title'     => esc_html__( 'How many columns should this column span across.', 'xts-theme' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 20,
				'step'      => 1,
				'condition' => [
					'heading_content_type' => 'cell',
				],
			]
		);

		$heading_repeater->add_control(
			'heading_cell_row_span',
			[
				'label'     => esc_html__( 'Row Span', 'xts-theme' ),
				'title'     => esc_html__( 'How many rows should this column span across.', 'xts-theme' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 20,
				'step'      => 1,
				'separator' => 'below',
				'condition' => [
					'heading_content_type' => 'cell',
				],
			]
		);

		$heading_repeater->add_control(
			'heading_cell_color',
			[
				'label'     => esc_html__( 'Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
				],
				'condition' => [
					'heading_content_type' => 'cell',
				],
			]
		);

		$heading_repeater->add_control(
			'heading_cell_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'heading_content_type' => 'cell',
				],
			]
		);

		$heading_repeater->end_controls_tab();

		$heading_repeater->end_controls_tabs();

		$this->add_control(
			'heading_items',
			[
				'type'          => Controls_Manager::REPEATER,
				'title_field'   => '{{ heading_content_type }}: {{{ heading_cell_text }}}',
				'fields'        => $heading_repeater->get_controls(),
				'prevent_empty' => false,
				'default'       => [
					[
						'heading_content_type' => 'row',
					],
					[
						'heading_cell_text'    => 'Heading #1',
						'heading_content_type' => 'cell',
					],
					[
						'heading_cell_text'    => 'Heading #2',
						'heading_content_type' => 'cell',
					],
					[
						'heading_cell_text'    => 'Heading #3',
						'heading_content_type' => 'cell',
					],
					[
						'heading_cell_text'    => 'Heading #4',
						'heading_content_type' => 'cell',
					],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Content settings
		 */
		$this->start_controls_section(
			'general_content_section',
			[
				'label' => esc_html__( 'Body', 'xts-theme' ),
			]
		);

		$body_repeater = new Repeater();

		$body_repeater->add_control(
			'body_content_type',
			[
				'label'   => esc_html__( 'Action', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cell',
				'options' => [
					'row'  => esc_html__( 'Start new row', 'xts-theme' ),
					'cell' => esc_html__( 'Add new cell', 'xts-theme' ),
				],
			]
		);

		$body_repeater->start_controls_tabs( 'body_item_tabs' );

		$body_repeater->start_controls_tab(
			'tab_body_cell_content',
			[
				'label'     => esc_html__( 'Content', 'xts-theme' ),
				'condition' => [
					'body_content_type' => 'cell',
				],
			]
		);

		$body_repeater->add_control(
			'body_cell_text',
			[
				'label'     => esc_html__( 'Text', 'xts-theme' ),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => 'Content',
				'condition' => [
					'body_content_type' => 'cell',
				],
			]
		);

		$body_repeater->end_controls_tab();

		$body_repeater->start_controls_tab(
			'tab_body_cell_settings',
			[
				'label'     => esc_html__( 'Settings', 'xts-theme' ),
				'condition' => [
					'body_content_type' => 'cell',
				],
			]
		);

		$body_repeater->add_control(
			'body_cell_type',
			[
				'label'     => esc_html__( 'Cell Type', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'td',
				'options'   => [
					'td' => esc_html__( 'Default', 'xts-theme' ),
					'th' => esc_html__( 'Header', 'xts-theme' ),
				],
				'condition' => [
					'body_content_type' => 'cell',
				],
			]
		);

		$body_repeater->add_control(
			'body_cell_span',
			[
				'label'     => esc_html__( 'Column Span', 'xts-theme' ),
				'title'     => esc_html__( 'How many columns should this column span across.', 'xts-theme' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 20,
				'step'      => 1,
				'condition' => [
					'body_content_type' => 'cell',
				],
			]
		);

		$body_repeater->add_control(
			'body_cell_row_span',
			[
				'label'     => esc_html__( 'Row Span', 'xts-theme' ),
				'title'     => esc_html__( 'How many rows should this column span across.', 'xts-theme' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 20,
				'step'      => 1,
				'separator' => 'below',
				'condition' => [
					'body_content_type' => 'cell',
				],
			]
		);

		$body_repeater->add_control(
			'body_cell_color',
			[
				'label'     => esc_html__( 'Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
				],
				'condition' => [
					'body_content_type' => 'cell',
				],
			]
		);

		$body_repeater->add_control(
			'body_cell_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'body_content_type' => 'cell',
				],
			]
		);

		$body_repeater->end_controls_tab();

		$body_repeater->end_controls_tabs();

		$this->add_control(
			'body_items',
			[
				'type'        => Controls_Manager::REPEATER,
				'title_field' => '{{ body_content_type }}: {{{ body_cell_text }}}',
				'fields'      => $body_repeater->get_controls(),
				'default'     => [
					[
						'body_content_type' => 'row',
					],
					[
						'body_cell_text'    => 'Row #1 Content #1',
						'body_content_type' => 'cell',
					],
					[
						'body_cell_text'    => 'Row #1 Content #2',
						'body_content_type' => 'cell',
					],
					[
						'body_cell_text'    => 'Row #1 Content #3',
						'body_content_type' => 'cell',
					],
					[
						'body_cell_text'    => 'Row #1 Content #4',
						'body_content_type' => 'cell',
					],
					[
						'body_content_type' => 'row',
					],
					[
						'body_cell_text'    => 'Row #2 Content #1',
						'body_content_type' => 'cell',
					],
					[
						'body_cell_text'    => 'Row #2 Content #2',
						'body_content_type' => 'cell',
					],
					[
						'body_cell_text'    => 'Row #2 Content #3',
						'body_content_type' => 'cell',
					],
					[
						'body_cell_text'    => 'Row #2 Content #4',
						'body_content_type' => 'cell',
					],
					[
						'body_content_type' => 'row',
					],
					[
						'body_cell_text'    => 'Row #3 Content #1',
						'body_content_type' => 'cell',
					],
					[
						'body_cell_text'    => 'Row #3 Content #2',
						'body_content_type' => 'cell',
					],
					[
						'body_cell_text'    => 'Row #3 Content #3',
						'body_content_type' => 'cell',
					],
					[
						'body_cell_text'    => 'Row #3 Content #4',
						'body_content_type' => 'cell',
					],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style tab
		 */

		/**
		 * Heading settings
		 */
		$this->start_controls_section(
			'heading_style_section',
			[
				'label' => esc_html__( 'Heading', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_text_align',
			[
				'label'   => esc_html__( 'Text alignment', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/left.svg',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/center.svg',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/right.svg',
					],
				],
				'default' => 'center',
			]
		);

		$this->add_control(
			'heading_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_text_color',
			[
				'label'     => esc_html__( 'Text color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_typography_heading',
			[
				'label'     => esc_html__( 'Typography', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector' => '{{WRAPPER}} th',
				'key'      => 'heading',
			]
		);

		$this->add_control(
			'heading_border_heading',
			[
				'label'     => esc_html__( 'Border', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'heading_border',
				'label'          => esc_html__( 'Border', 'xts-theme' ),
				'fields_options' => [
					'border' => [
						'default' => '',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#bbb',
					],
				],
				'selector'       => '{{WRAPPER}} th',
			]
		);

		$this->add_control(
			'heading_padding_heading',
			[
				'label'     => esc_html__( 'Padding', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'heading_cell_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'bottom'   => '',
					'left'     => '',
					'right'    => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Body settings
		 */
		$this->start_controls_section(
			'body_style_section',
			[
				'label' => esc_html__( 'Body', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'body_text_align',
			[
				'label'   => esc_html__( 'Text alignment', 'xts-theme' ),
				'type'    => 'xts_buttons',
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/left.svg',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/center.svg',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'xts-theme' ),
						'image' => XTS_ASSETS_IMAGES_URL . '/elementor/align/right.svg',
					],
				],
				'default' => 'center',
			]
		);

		$this->add_control(
			'body_background_heading',
			[
				'label'     => esc_html__( 'Background', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'body_background_type',
			[
				'label'   => esc_html__( 'Background type', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'body',
				'options' => [
					'body'       => esc_html__( 'Body', 'xts-theme' ),
					'h_even_odd' => esc_html__( 'Horizontal even & odd', 'xts-theme' ),
					'v_even_odd' => esc_html__( 'Vertical even & odd', 'xts-theme' ),
				],
			]
		);

		$this->add_control(
			'body_background_color',
			[
				'label'     => esc_html__( 'Body background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody td' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'body_background_type' => 'body',
				],
			]
		);

		$this->add_control(
			'body_text_color',
			[
				'label'     => esc_html__( 'Body text color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody td' => 'color: {{VALUE}};',
				],
				'condition' => [
					'body_background_type' => 'body',
				],
			]
		);

		$this->add_control(
			'h_even_background_color',
			[
				'label'     => esc_html__( 'Horizontal even background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody tr:nth-child(even)' => 'background-color: {{VALUE}};',
				],
				'default'   => '#F8F8F8',
				'condition' => [
					'body_background_type' => 'h_even_odd',
				],
			]
		);

		$this->add_control(
			'h_odd_background_color',
			[
				'label'     => esc_html__( 'Horizontal odd background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody tr:nth-child(odd)' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'body_background_type' => 'h_even_odd',
				],
			]
		);

		$this->add_control(
			'h_even_text_color',
			[
				'label'     => esc_html__( 'Horizontal even text color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody tr:nth-child(even)' => 'color: {{VALUE}};',
				],
				'condition' => [
					'body_background_type' => 'h_even_odd',
				],
			]
		);

		$this->add_control(
			'h_odd_text_color',
			[
				'label'     => esc_html__( 'Horizontal odd text color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody tr:nth-child(odd)' => 'color: {{VALUE}};',
				],
				'condition' => [
					'body_background_type' => 'h_even_odd',
				],
			]
		);

		$this->add_control(
			'v_even_background_color',
			[
				'label'     => esc_html__( 'Vertical even background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody td:nth-child(even), {{WRAPPER}} tbody th:nth-child(even)' => 'background-color: {{VALUE}};',
				],
				'default'   => '#F8F8F8',
				'condition' => [
					'body_background_type' => 'v_even_odd',
				],
			]
		);

		$this->add_control(
			'v_odd_background_color',
			[
				'label'     => esc_html__( 'Vertical odd background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody td:nth-child(odd), {{WRAPPER}} tbody th:nth-child(odd)' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'body_background_type' => 'v_even_odd',
				],
			]
		);

		$this->add_control(
			'v_even_text_color',
			[
				'label'     => esc_html__( 'Vertical even text color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody td:nth-child(even), {{WRAPPER}} tbody th:nth-child(even)' => 'color: {{VALUE}};',
				],
				'condition' => [
					'body_background_type' => 'v_even_odd',
				],
			]
		);

		$this->add_control(
			'v_odd_text_color',
			[
				'label'     => esc_html__( 'Vertical odd text color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody td:nth-child(odd), {{WRAPPER}} tbody th:nth-child(odd)' => 'color: {{VALUE}};',
				],
				'condition' => [
					'body_background_type' => 'v_even_odd',
				],
			]
		);

		$this->add_control(
			'body_typography_heading',
			[
				'label'     => esc_html__( 'Typography', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		xts_get_typography_map(
			$this,
			[
				'selector' => '{{WRAPPER}} td',
				'key'      => 'body',
			]
		);

		$this->add_control(
			'body_border_heading',
			[
				'label'     => esc_html__( 'Border', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'body_border',
				'label'          => esc_html__( 'Border', 'xts-theme' ),
				'fields_options' => [
					'border' => [
						'default' => '',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#bbb',
					],
				],
				'selector'       => '{{WRAPPER}} td',
			]
		);

		$this->add_control(
			'body_cell_padding_heading',
			[
				'label'     => esc_html__( 'Padding', 'xts-theme' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'body_cell_padding',
			[
				'label'      => esc_html__( 'Padding', 'xts-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'bottom'   => '',
					'left'     => '',
					'right'    => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		xts_table_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Table() );
