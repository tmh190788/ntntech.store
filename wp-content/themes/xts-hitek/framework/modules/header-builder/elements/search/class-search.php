<?php
/**
 * Search form. A few kinds of it.
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Search form. A few kinds of it.
 */
class Search extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();

		$this->template_name = 'search';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$post_types = array(
			'post'          => array(
				'value' => 'post',
				'label' => esc_html__( 'Post', 'xts-theme' ),
			),
			'xts-portfolio' => array(
				'value' => 'xts-portfolio',
				'label' => esc_html__( 'Portfolio', 'xts-theme' ),
			),
		);

		if ( xts_is_woocommerce_installed() ) {
			$post_types['product'] = array(
				'value' => 'product',
				'label' => esc_html__( 'Product', 'xts-theme' ),
			);
		}

		$this->args = array(
			'type'            => 'search',
			'title'           => esc_html__( 'Search', 'xts-theme' ),
			'text'            => esc_html__( 'Search form', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/search.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'desktop'         => true,
			'params'          => apply_filters(
				'xts_search_element_params',
				array(
					'display'           => array(
						'id'          => 'display',
						'title'       => esc_html__( 'Display', 'xts-theme' ),
						'type'        => 'selector',
						'tab'         => esc_html__( 'General', 'xts-theme' ),
						'value'       => 'full-screen',
						'options'     => array(
							'full-screen' => array(
								'value' => 'full-screen',
								'label' => esc_html__( 'Full screen', 'xts-theme' ),
							),
							'dropdown'    => array(
								'value' => 'dropdown',
								'label' => esc_html__( 'Dropdown', 'xts-theme' ),
							),
							'form'        => array(
								'value' => 'form',
								'label' => esc_html__( 'Form', 'xts-theme' ),
							),
						),
						'description' => esc_html__( 'Display search icon/form in the header in different views.', 'xts-theme' ),
					),

					'color_scheme'      => array(
						'id'      => 'color_scheme',
						'title'   => esc_html__( 'Color scheme', 'xts-theme' ),
						'tab'     => esc_html__( 'General', 'xts-theme' ),
						'type'    => 'selector',
						'value'   => 'dark',
						'options' => array(
							'dark'  => array(
								'value' => 'dark',
								'label' => esc_html__( 'Dark', 'xts-theme' ),
								'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/color/dark.svg',
							),
							'light' => array(
								'value' => 'light',
								'label' => esc_html__( 'Light', 'xts-theme' ),
								'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/color/light.svg',
							),
						),
					),

					'search_style'      => array(
						'id'       => 'search_style',
						'title'    => esc_html__( 'Search style', 'xts-theme' ),
						'type'     => 'selector',
						'tab'      => esc_html__( 'General', 'xts-theme' ),
						'value'    => 'icon-alt',
						'options'  => xts_get_available_options( 'search_style_header_builder' ),
						'requires' => array(
							'display' => array(
								'comparison' => 'equal',
								'value'      => 'form',
							),
						),
					),

					'form_color_scheme' => array(
						'id'          => 'form_color_scheme',
						'title'       => esc_html__( 'Form color scheme', 'xts-theme' ),
						'type'        => 'selector',
						'tab'         => esc_html__( 'General', 'xts-theme' ),
						'value'       => 'dark',
						'options'     => array(
							'dark'  => array(
								'value' => 'dark',
								'label' => esc_html__( 'Dark', 'xts-theme' ),
								'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/color/dark.svg',
							),
							'light' => array(
								'value' => 'light',
								'label' => esc_html__( 'Light', 'xts-theme' ),
								'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/color/light.svg',
							),
						),
						'description' => esc_html__( 'Select different text color scheme depending on your background.', 'xts-theme' ),
						'requires'    => array(
							'display' => array(
								'comparison' => 'equal',
								'value'      => 'form',
							),
						),
					),

					'icon_style'        => array(
						'id'       => 'icon_style',
						'title'    => esc_html__( 'Icon style', 'xts-theme' ),
						'type'     => 'selector',
						'tab'      => esc_html__( 'General', 'xts-theme' ),
						'value'    => 'icon',
						'options'  => xts_get_available_options( 'search_icon_style_header_builder' ),
						'requires' => array(
							'display' => array(
								'comparison' => 'not_equal',
								'value'      => 'form',
							),
						),
					),

					'icon_type'         => array(
						'id'       => 'icon_type',
						'title'    => esc_html__( 'Icon', 'xts-theme' ),
						'type'     => 'selector',
						'tab'      => esc_html__( 'General', 'xts-theme' ),
						'value'    => 'default',
						'options'  => array(
							'default' => array(
								'value' => 'default',
								'label' => esc_html__( 'Default', 'xts-theme' ),
								'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/search/icon/default.svg',
							),
							'custom'  => array(
								'value' => 'custom',
								'label' => esc_html__( 'Custom', 'xts-theme' ),
								'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/custom-icon.svg',
							),
						),
						'requires' => array(
							'icon_style' => array(
								'comparison' => 'not_equal',
								'value'      => 'text',
							),
						),
					),

					'custom_icon'       => array(
						'id'          => 'custom_icon',
						'title'       => esc_html__( 'Custom icon', 'xts-theme' ),
						'type'        => 'image',
						'tab'         => esc_html__( 'General', 'xts-theme' ),
						'value'       => '',
						'description' => '',
						'requires'    => array(
							'icon_type'  => array(
								'comparison' => 'equal',
								'value'      => 'custom',
							),
							'icon_style' => array(
								'comparison' => 'not_equal',
								'value'      => 'text',
							),
						),
					),

					'ajax'              => array(
						'id'          => 'ajax',
						'title'       => esc_html__( 'Search with AJAX', 'xts-theme' ),
						'type'        => 'switcher',
						'tab'         => esc_html__( 'Search results', 'xts-theme' ),
						'value'       => true,
						'description' => esc_html__( 'Enable instant AJAX search functionality for this form.', 'xts-theme' ),
					),

					'ajax_result_count' => array(
						'id'          => 'ajax_result_count',
						'title'       => esc_html__( 'AJAX search results count', 'xts-theme' ),
						'description' => esc_html__( 'Number of products to display in AJAX search results.', 'xts-theme' ),
						'type'        => 'slider',
						'tab'         => esc_html__( 'Search results', 'xts-theme' ),
						'from'        => 3,
						'to'          => 50,
						'value'       => 20,
						'units'       => '',
						'requires'    => array(
							'ajax' => array(
								'comparison' => 'equal',
								'value'      => true,
							),
						),
					),

					'post_type'         => array(
						'id'          => 'post_type',
						'title'       => esc_html__( 'Post type', 'xts-theme' ),
						'type'        => 'selector',
						'tab'         => esc_html__( 'Search results', 'xts-theme' ),
						'value'       => 'post',
						'options'     => $post_types,
						'description' => esc_html__( 'You can set up the search for posts, projects or for products (woocommerce).', 'xts-theme' ),
					),
				)
			),
		);

		if ( xts_is_woocommerce_installed() ) {
			$categories_dropdown = array(
				'categories_dropdown' => array(
					'id'       => 'categories_dropdown',
					'title'    => esc_html__( 'Product categories dropdown', 'xts-theme' ),
					'type'     => 'switcher',
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'value'    => false,
					'requires' => array(
						'display' => array(
							'comparison' => 'equal',
							'value'      => 'form',
						),
					),
				),
			);

			$this->args['params'] = array_slice( $this->args['params'], 0, 4, true ) + $categories_dropdown + array_slice( $this->args['params'], 4, count( $this->args['params'] ) - 1, true );
		}
	}
}
