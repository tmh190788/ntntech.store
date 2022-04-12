<?php
/**
 * Admin part class
 *
 * @package xts
 */

namespace XTS\Framework;

use XTS\Google_Fonts;

use XTS\Singleton;

use XTS\Framework\Options;

use XTS\Framework\Notices;

/**
 * Admin class
 *
 * @since 1.0.0
 */
class Admin extends Singleton {

	/**
	 * Notices object.
	 *
	 * @var object
	 */
	private $notices = null;

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->notices = new Notices();
		add_action( 'admin_enqueue_scripts', array( $this, 'style' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 20 );

	}

	/**
	 * Enqueue base admin style and scrips.
	 *
	 * @package xts
	 */
	public function style() {
		$minified = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
		wp_enqueue_style( 'xts-framework-styles', XTS_FRAMEWORK_URL . '/assets/css/style.css', array(), XTS_VERSION );
		wp_enqueue_script( 'xts-framework-scripts', XTS_FRAMEWORK_URL . '/assets/js/functions' . $minified . '.js', array(), XTS_VERSION, true );
		wp_register_script( 'select2', XTS_FRAMEWORK_URL . '/assets/js-libs/select2.full.min.js', array(), XTS_VERSION, true );

		$std_fonts = xts_get_config( 'standard-fonts' );

		$custom_fonts_data = xts_get_opt( 'custom_fonts' );
		$custom_fonts      = array();
		if ( isset( $custom_fonts_data['{{index}}'] ) ) {
			unset( $custom_fonts_data['{{index}}'] );
		}

		if ( is_array( $custom_fonts_data ) ) {
			foreach ( $custom_fonts_data as $font ) {
				if ( ! $font['font-name'] ) {
					continue;
				}

				$custom_fonts[ $font['font-name'] ] = $font['font-name'];
			}
		}

		$typekit_fonts = xts_get_opt( 'typekit_fonts' );

		if ( $typekit_fonts ) {
			$typekit = explode( ',', $typekit_fonts );
			foreach ( $typekit as $font ) {
				$custom_fonts[ ucfirst( trim( $font ) ) ] = trim( $font );
			}
		}

		wp_localize_script(
			'xts-framework-scripts',
			'xtsTypography',
			array(
				'stdfonts'    => $std_fonts,
				'googlefonts' => Google_Fonts::$all_google_fonts,
				'customFonts' => $custom_fonts,
			)
		);
	}

	/**
	 * Admin localize.
	 *
	 * @since 1.0
	 */
	public function localize_script() {
		$all_fields   = Options::get_fields();
		$all_sections = Options::get_sections();

		$options_data = array();
		foreach ( $all_fields as $field ) {
			$path       = '';
			$section_id = $field->args['section'];
			$section    = $all_sections[ $section_id ];

			if ( isset( $section['parent'] ) ) {
				$path = $all_sections[ $section['parent'] ]['name'] . ' -> ' . $section['name'];
			} else {
				$path = $section['name'];
			}

			$text = $field->args['name'];
			if ( isset( $field->args['description'] ) ) {
				$text .= ' ' . $field->args['description'];
			}
			if ( isset( $field->args['tags'] ) ) {
				$text .= ' ' . $field->args['tags'];
			}

			$options_data[] = array(
				'id'         => $field->args['id'],
				'title'      => $field->args['name'],
				'text'       => $text,
				'section_id' => $section['id'],
				'icon'       => isset( $section['icon'] ) ? $section['icon'] : $all_sections[ $section['parent'] ]['icon'],
				'path'       => $path,
			);
		}

		$localize_data['xtsOptions']                         = $options_data;
		$localize_data['theme_slug']                         = ucfirst( XTS_THEME_SLUG );
		$localize_data['ajaxUrl']                            = admin_url( 'admin-ajax.php' );
		$localize_data['wpUploadDir']                        = wp_upload_dir();
		$localize_data['activate_plugin_btn_text']           = esc_html__( 'Activate', 'xts-theme' );
		$localize_data['update_plugin_btn_text']             = esc_html__( 'Update', 'xts-theme' );
		$localize_data['deactivate_plugin_btn_text']         = esc_html__( 'Deactivate', 'xts-theme' );
		$localize_data['install_plugin_btn_text']            = esc_html__( 'Install', 'xts-theme' );
		$localize_data['activate_process_plugin_btn_text']   = esc_html__( 'Activating', 'xts-theme' );
		$localize_data['update_process_plugin_btn_text']     = esc_html__( 'Updating', 'xts-theme' );
		$localize_data['deactivate_process_plugin_btn_text'] = esc_html__( 'Deactivating', 'xts-theme' );
		$localize_data['install_process_plugin_btn_text']    = esc_html__( 'Installing', 'xts-theme' );

		wp_localize_script(
			'xts-framework-scripts',
			'xtsAdminConfig',
			$localize_data
		);
	}
}
