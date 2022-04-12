<?php
/**
 * Infobox element
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Infobox element
 */
class Infobox extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'infobox';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'infobox',
			'title'           => esc_html__( 'Infobox', 'xts-theme' ),
			'text'            => esc_html__( 'Text with icon', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/infobox.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				/**
				 * General settings
				 */
				'content_align'             => array(
					'id'      => 'content_align',
					'type'    => 'selector',
					'title'   => esc_html__( 'Content alignment', 'xts-theme' ),
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'options' => array(
						'left'   => array(
							'value' => 'left',
							'label' => esc_html__( 'Left', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/infobox/align/left.svg',

						),
						'center' => array(
							'value' => 'center',
							'label' => esc_html__( 'Center', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/infobox/align/center.svg',

						),
						'right'  => array(
							'value' => 'right',
							'label' => esc_html__( 'Right', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/infobox/align/right.svg',
						),
					),
					'value'   => 'left',
				),

				/**
				 * Image settings
				 */
				'icon_position'             => array(
					'id'      => 'icon_position',
					'type'    => 'selector',
					'title'   => esc_html__( 'Image position', 'xts-theme' ),
					'tab'     => esc_html__( 'Image', 'xts-theme' ),
					'options' => array(
						'side' => array(
							'value' => 'side',
							'label' => esc_html__( 'Side', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/infobox/icon-position/side.svg',
						),
						'top'  => array(
							'value' => 'top',
							'label' => esc_html__( 'Top', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/infobox/icon-position/top.svg',
						),
					),
					'value'   => 'top',
				),

				'icon_vertical_position'    => array(
					'id'       => 'icon_vertical_position',
					'type'     => 'selector',
					'title'    => esc_html__( 'Vertical align', 'xts-theme' ),
					'tab'      => esc_html__( 'Image', 'xts-theme' ),
					'options'  => array(
						'start'  => array(
							'value' => 'start',
							'label' => esc_html__( 'Start', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/infobox/icon-vertical-position/start.svg',
						),
						'center' => array(
							'value' => 'center',
							'label' => esc_html__( 'Center', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/infobox/icon-vertical-position/center.svg',
						),
						'end'    => array(
							'value' => 'end',
							'label' => esc_html__( 'End', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/infobox/icon-vertical-position/end.svg',
						),
					),
					'requires' => array(
						'icon_position' => array(
							'comparison' => 'equal',
							'value'      => 'side',
						),
					),
					'value'    => 'start',
				),

				'image'                     => array(
					'id'    => 'image',
					'type'  => 'image',
					'title' => esc_html__( 'Image', 'xts-theme' ),
					'tab'   => esc_html__( 'Image', 'xts-theme' ),
					'value' => '',
				),

				'image_size'                => array(
					'id'      => 'image_size',
					'type'    => 'select',
					'title'   => esc_html__( 'Image size', 'xts-theme' ),
					'tab'     => esc_html__( 'Image', 'xts-theme' ),
					'options' => xts_get_all_image_sizes_names( 'header_builder' ),
					'value'   => 'thumbnail',
				),

				'image_width'               => array(
					'id'       => 'image_width',
					'type'     => 'text',
					'title'    => esc_html__( 'Width', 'xts-theme' ),
					'tab'      => esc_html__( 'Image', 'xts-theme' ),
					'requires' => array(
						'image_size' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
					'value'    => '128',
				),

				'image_height'              => array(
					'id'       => 'image_height',
					'type'     => 'text',
					'title'    => esc_html__( 'Height', 'xts-theme' ),
					'tab'      => esc_html__( 'Image', 'xts-theme' ),
					'requires' => array(
						'image_size' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
					'value'    => '128',
				),

				'image_gap'                 => array(
					'id'      => 'image_gap',
					'type'    => 'select',
					'title'   => esc_html__( 'Image gap', 'xts-theme' ),
					'tab'     => esc_html__( 'Image', 'xts-theme' ),
					'options' => array(
						's' => array(
							'value' => 's',
							'label' => esc_html__( 'Small', 'xts-theme' ),
						),
						'm' => array(
							'value' => 'm',
							'label' => esc_html__( 'Medium', 'xts-theme' ),
						),
						'l' => array(
							'value' => 'l',
							'label' => esc_html__( 'Large', 'xts-theme' ),
						),
					),
					'value'   => 'm',
				),

				/**
				 * Title settings
				 */
				'title'                     => array(
					'id'    => 'title',
					'type'  => 'text',
					'title' => esc_html__( 'Title', 'xts-theme' ),
					'tab'   => esc_html__( 'General', 'xts-theme' ),
					'value' => '',
				),

				'without_title_spacing'     => array(
					'id'      => 'without_title_spacing',
					'title'   => esc_html__( 'Remove title spacing', 'xts-theme' ),
					'type'    => 'switcher',
					'onText'  => esc_html__( 'Yes', 'xts-theme' ),
					'offText' => esc_html__( 'No', 'xts-theme' ),
					'tab'     => esc_html__( 'Title', 'xts-theme' ),
					'value'   => false,
				),

				'title_text_size'           => array(
					'id'      => 'title_text_size',
					'type'    => 'select',
					'title'   => esc_html__( 'Size presets', 'xts-theme' ),
					'tab'     => esc_html__( 'Title', 'xts-theme' ),
					'options' => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
						),
						'xs'      => array(
							'value' => 'xs',
							'label' => esc_html__( 'Extra small', 'xts-theme' ),
						),
						's'       => array(
							'value' => 's',
							'label' => esc_html__( 'Small', 'xts-theme' ),
						),
						'm'       => array(
							'value' => 'm',
							'label' => esc_html__( 'Medium', 'xts-theme' ),
						),
						'l'       => array(
							'value' => 'l',
							'label' => esc_html__( 'Large', 'xts-theme' ),
						),
					),
					'value'   => 'm',
				),

				'title_color_presets'       => array(
					'id'      => 'title_color_presets',
					'type'    => 'select',
					'title'   => esc_html__( 'Color presets', 'xts-theme' ),
					'tab'     => esc_html__( 'Title', 'xts-theme' ),
					'options' => array(
						'default'   => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
						),
						'primary'   => array(
							'value' => 'primary',
							'label' => esc_html__( 'Primary', 'xts-theme' ),
						),
						'secondary' => array(
							'value' => 'secondary',
							'label' => esc_html__( 'Secondary', 'xts-theme' ),
						),
						'white'     => array(
							'value' => 'white',
							'label' => esc_html__( 'White', 'xts-theme' ),
						),
					),
					'value'   => 'default',
				),

				'title_tag'                 => array(
					'id'      => 'title_tag',
					'type'    => 'select',
					'title'   => esc_html__( 'Tag', 'xts-theme' ),
					'tab'     => esc_html__( 'Title', 'xts-theme' ),
					'options' => array(
						'h1'   => array(
							'value' => 'h1',
							'label' => esc_html__( 'h1', 'xts-theme' ),
						),
						'h2'   => array(
							'value' => 'h2',
							'label' => esc_html__( 'h2', 'xts-theme' ),
						),
						'h3'   => array(
							'value' => 'h3',
							'label' => esc_html__( 'h3', 'xts-theme' ),
						),
						'h4'   => array(
							'value' => 'h4',
							'label' => esc_html__( 'h4', 'xts-theme' ),
						),
						'h5'   => array(
							'value' => 'h5',
							'label' => esc_html__( 'h5', 'xts-theme' ),
						),
						'h6'   => array(
							'value' => 'h6',
							'label' => esc_html__( 'h6', 'xts-theme' ),
						),
						'p'    => array(
							'value' => 'p',
							'label' => esc_html__( 'p', 'xts-theme' ),
						),
						'span' => array(
							'value' => 'span',
							'label' => esc_html__( 'span', 'xts-theme' ),
						),
						'div'  => array(
							'value' => 'div',
							'label' => esc_html__( 'div', 'xts-theme' ),
						),
					),
					'value'   => 'h4',
				),

				/**
				 * Title settings
				 */
				'subtitle'                  => array(
					'id'    => 'subtitle',
					'type'  => 'text',
					'title' => esc_html__( 'Subtitle', 'xts-theme' ),
					'tab'   => esc_html__( 'General', 'xts-theme' ),
					'value' => '',
				),

				'subtitle_text_size'        => array(
					'id'      => 'subtitle_text_size',
					'type'    => 'select',
					'title'   => esc_html__( 'Size presets', 'xts-theme' ),
					'tab'     => esc_html__( 'Subtitle', 'xts-theme' ),
					'options' => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
						),
						'xs'      => array(
							'value' => 'xs',
							'label' => esc_html__( 'Extra small', 'xts-theme' ),
						),
						's'       => array(
							'value' => 's',
							'label' => esc_html__( 'Small', 'xts-theme' ),
						),
						'm'       => array(
							'value' => 'm',
							'label' => esc_html__( 'Medium', 'xts-theme' ),
						),
						'l'       => array(
							'value' => 'l',
							'label' => esc_html__( 'Large', 'xts-theme' ),
						),
					),
					'value'   => 's',
				),

				'subtitle_color_presets'    => array(
					'id'      => 'subtitle_color_presets',
					'type'    => 'select',
					'title'   => esc_html__( 'Color presets', 'xts-theme' ),
					'tab'     => esc_html__( 'Subtitle', 'xts-theme' ),
					'options' => array(
						'default'   => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
						),
						'primary'   => array(
							'value' => 'primary',
							'label' => esc_html__( 'Primary', 'xts-theme' ),
						),
						'secondary' => array(
							'value' => 'secondary',
							'label' => esc_html__( 'Secondary', 'xts-theme' ),
						),
						'white'     => array(
							'value' => 'white',
							'label' => esc_html__( 'White', 'xts-theme' ),
						),
					),
					'value'   => 'default',
				),

				/**
				 * Description settings
				 */
				'description'               => array(
					'id'    => 'description',
					'type'  => 'textarea',
					'title' => esc_html__( 'Description', 'xts-theme' ),
					'tab'   => esc_html__( 'General', 'xts-theme' ),
					'value' => '',
				),

				'description_text_size'     => array(
					'id'      => 'description_text_size',
					'type'    => 'select',
					'title'   => esc_html__( 'Size presets', 'xts-theme' ),
					'tab'     => esc_html__( 'Description', 'xts-theme' ),
					'options' => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
						),
						'xs'      => array(
							'value' => 'xs',
							'label' => esc_html__( 'Extra small', 'xts-theme' ),
						),
						's'       => array(
							'value' => 's',
							'label' => esc_html__( 'Small', 'xts-theme' ),
						),
						'm'       => array(
							'value' => 'm',
							'label' => esc_html__( 'Medium', 'xts-theme' ),
						),
						'l'       => array(
							'value' => 'l',
							'label' => esc_html__( 'Large', 'xts-theme' ),
						),
					),
					'value'   => 'default',
				),

				'description_color_presets' => array(
					'id'      => 'description_color_presets',
					'type'    => 'select',
					'title'   => esc_html__( 'Color presets', 'xts-theme' ),
					'tab'     => esc_html__( 'Description', 'xts-theme' ),
					'options' => array(
						'default'   => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
						),
						'primary'   => array(
							'value' => 'primary',
							'label' => esc_html__( 'Primary', 'xts-theme' ),
						),
						'secondary' => array(
							'value' => 'secondary',
							'label' => esc_html__( 'Secondary', 'xts-theme' ),
						),
						'white'     => array(
							'value' => 'white',
							'label' => esc_html__( 'White', 'xts-theme' ),
						),
					),
					'value'   => 'default',
				),

				/**
				 * Description settings
				 */
				'infobox_link'              => array(
					'id'    => 'infobox_link',
					'title' => esc_html__( 'Link', 'xts-theme' ),
					'type'  => 'text',
					'tab'   => esc_html__( 'General', 'xts-theme' ),
					'value' => '',
				),
			),
		);
	}
}
