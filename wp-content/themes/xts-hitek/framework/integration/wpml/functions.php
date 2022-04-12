<?php
/**
 * WPML.
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_wpml_register_header_builder_strings' ) ) {
	/**
	 * Register header builder strings.
	 *
	 * @since 1.1.0
	 *
	 * @param string $file File.
	 */
	function xts_wpml_register_header_builder_strings( $file ) {
		global $wpdb;

		if ( is_string( $file ) && 'xts-' . XTS_THEME_SLUG === basename( dirname( $file ) ) && class_exists( 'WPML_Admin_Text_Configuration' ) ) {
			$admin_texts = array();
			$headers     = get_option( 'xts_saved_headers', array() );

			foreach ( $headers as $key => $header ) {
				$admin_texts[] = array(
					'value' => '',
					'attr'  => array( 'name' => 'xts_' . $key ),
					'key'   => array(
						array(
							'value' => '',
							'attr'  => array( 'name' => 'structure' ),
							'key'   => array(
								array(
									'value' => '',
									'attr'  => array( 'name' => 'content' ),
									'key'   => array(
										array(
											'value' => '',
											'attr'  => array( 'name' => '*' ),
											'key'   => array(
												array(
													'value' => '',
													'attr' => array( 'name' => 'content' ),
													'key'  => array(
														array(
															'value' => '',
															'attr'  => array( 'name' => '*' ),
															'key'   => array(
																array(
																	'value' => '',
																	'attr'  => array( 'name' => 'content' ),
																	'key'   => array(
																		array(
																			'value' => '',
																			'attr'  => array( 'name' => '*' ),
																			'key'   => array(
																				array(
																					'value' => '',
																					'attr'  => array( 'name' => 'params' ),
																					'key'   => array(
																						array(
																							'value' => '',
																							'attr'  => array( 'name' => 'content' ),
																							'key'   => array(
																								array(
																									'value' => '',
																									'attr'  => array( 'name' => 'value' ),
																									'key'   => array(),
																								),
																							),
																						),
																						array(
																							'value' => '',
																							'attr'  => array( 'name' => 'title' ),
																							'key'   => array(
																								array(
																									'value' => '',
																									'attr'  => array( 'name' => 'value' ),
																									'key'   => array(),
																								),
																							),
																						),
																						array(
																							'value' => '',
																							'attr'  => array( 'name' => 'subtitle' ),
																							'key'   => array(
																								array(
																									'value' => '',
																									'attr'  => array( 'name' => 'value' ),
																									'key'   => array(),
																								),
																							),
																						),
																					),
																				),
																			),
																		),
																	),
																),
															),
														),
													),
												),
											),
										),
									),
								),
							),
						),
					),
				);
			}

			$object = (object) array(
				'config'             => array(
					'wpml-config' => array(
						'admin-texts' => array(
							'value' => '',
							'key'   => $admin_texts,
						),
					),
				),
				'type'               => 'theme',
				'admin_text_context' => 'xts-header-builder',
			);

			$config       = new WPML_Admin_Text_Configuration( $object );
			$config_array = $config->get_config_array();

			if ( $config_array ) {
				$st_records          = new WPML_ST_Records( $wpdb );
				$import              = new WPML_Admin_Text_Import( $st_records, new WPML_WP_API() );
				$config_handler_hash = md5( serialize( 'xts' ) ); // phpcs:ignore
				$import->parse_config( $config_array, $config_handler_hash );
			}
		}
	}

	add_filter( 'wpml_parse_config_file', 'xts_wpml_register_header_builder_strings' );
}

if ( ! function_exists( 'xts_ajax_actions_wpml_compatibility' ) ) {
	/**
	 * AJAX actions for WPML.
	 *
	 * @since 1.0.0
	 *
	 * @param array $ajax_actions AJAX Actions.
	 *
	 * @return mixed
	 */
	function xts_ajax_actions_wpml_compatibility( $ajax_actions ) {
		$ajax_actions[] = 'xts_single_product_ajax_add_to_cart';
		$ajax_actions[] = 'xts_quick_view';
		$ajax_actions[] = 'xts_ajax_search';
		$ajax_actions[] = 'xts_load_html_dropdowns';
		$ajax_actions[] = 'xts_update_cart_item';
		$ajax_actions[] = 'xts_get_products_tab_element';
		$ajax_actions[] = 'xts_ajax_search';
		$ajax_actions[] = 'xts_get_product_element';

		return $ajax_actions;
	}

	add_filter( 'wcml_multi_currency_ajax_actions', 'xts_ajax_actions_wpml_compatibility', 10, 1 );
}
