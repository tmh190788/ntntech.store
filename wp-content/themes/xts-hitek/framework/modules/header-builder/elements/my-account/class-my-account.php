<?php
/**
 * Account links in the header. Login / register, my account, logout.
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Modules;
use XTS\Header_Builder\Element;

/**
 * Account links in the header. Login / register, my account, logout.
 */
class My_Account extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'my-account';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$this->args = array(
			'type'            => 'my-account',
			'title'           => esc_html__( 'Account', 'xts-theme' ),
			'text'            => esc_html__( 'Login/register links', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/account.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				'style'         => array(
					'id'      => 'style',
					'title'   => esc_html__( 'Style', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'icon',
					'options' => array(
						'icon'      => array(
							'value' => 'icon',
							'label' => esc_html__( 'Icon only', 'xts-theme' ),
						),
						'icon-text' => array(
							'value' => 'icon-text',
							'label' => esc_html__( 'Icon with text', 'xts-theme' ),
						),
						'text'      => array(
							'value' => 'text',
							'label' => esc_html__( 'Only text', 'xts-theme' ),
						),
					),
				),

				'icon_style'    => array(
					'id'       => 'icon_style',
					'type'     => 'selector',
					'title'    => esc_html__( 'Icon', 'xts-theme' ),
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'options'  => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/account.svg',
						),
						'custom'  => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/custom-icon.svg',
						),
					),
					'value'    => 'default',
					'requires' => array(
						'style' => array(
							'comparison' => 'not_equal',
							'value'      => 'text',
						),
					),
				),

				'custom_icon'   => array(
					'id'       => 'custom_icon',
					'type'     => 'image',
					'title'    => esc_html__( 'Custom icon', 'xts-theme' ),
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'requires' => array(
						'icon_style' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
					'value'    => '',
				),

				'with_username' => array(
					'id'          => 'with_username',
					'type'        => 'switcher',
					'title'       => esc_html__( 'Username', 'xts-theme' ),
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'description' => esc_html__( 'Display username when user is logged in.', 'xts-theme' ),
					'value'       => false,
					'requires'    => array(
						'style' => array(
							'comparison' => 'not_equal',
							'value'      => 'icon',
						),
					),
				),

				'login_form'    => array(
					'id'          => 'login_form',
					'type'        => 'switcher',
					'title'       => esc_html__( 'Login form', 'xts-theme' ),
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'description' => esc_html__( 'Display login form when the customer is not logged in.', 'xts-theme' ),
					'value'       => true,
				),

				'widget_type'   => array(
					'id'       => 'widget_type',
					'type'     => 'selector',
					'title'    => esc_html__( 'Widget type', 'xts-theme' ),
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'options'  => xts_get_available_options( 'my_account_widget_type_header_builder' ),
					'requires' => array(
						'login_form' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
					'value'    => 'side',
				),

				'position'      => array(
					'id'          => 'position',
					'type'        => 'selector',
					'title'       => esc_html__( 'Position', 'xts-theme' ),
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'description' => esc_html__( 'Position of the login form sidebar widget.', 'xts-theme' ),
					'options'     => array(
						'left'  => array(
							'value' => 'left',
							'label' => esc_html__( 'Left', 'xts-theme' ),
						),
						'right' => array(
							'value' => 'right',
							'label' => esc_html__( 'Right', 'xts-theme' ),
						),
					),
					'requires'    => array(
						'login_form'  => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'widget_type' => array(
							'comparison' => 'equal',
							'value'      => 'side',
						),
					),
					'value'       => 'right',
				),

				'color_scheme'  => array(
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
			),
		);
	}
}

