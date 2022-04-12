<?php
/**
 * Header builder backend class.
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Backend hooks.
 */
class Backend {
	/**
	 * Main header builder class object.
	 *
	 * @var object
	 */
	private $builder = null;

	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 *
	 * @param object $builder Main header builder class object.
	 */
	public function __construct( $builder ) {
		$this->builder = $builder;
		if ( ! isset( $_GET['page'] ) || 'xts_header_builder' !== $_GET['page'] ) { // phpcs:ignore
			return;
		}
		$this->add_actions();
	}

	/**
	 * Register actions hooks.
	 *
	 * @since 1.0.0
	 */
	private function add_actions() {
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ), 50 );
	}

	/**
	 * Register builder's styles and scripts.
	 *
	 * @since 1.0.0
	 */
	public function scripts() {
		$dev = apply_filters( 'xts_debug_mode', false );

		$assets_path = ( $dev ) ? plugins_url( 'header-builder/builder/public/' ) : XTS_FRAMEWORK_URL . '/assets/';

		wp_register_script( 'xts-header-builder', $assets_path . 'js/builder.js', array(), XTS_HB_VERSION, true );
		wp_register_script( 'xts-react', XTS_FRAMEWORK_URL . '/assets/js/react.production.min.js', array(), XTS_HB_VERSION, true );
		wp_register_script( 'xts-react-dom', XTS_FRAMEWORK_URL . '/assets/js/react-dom.production.min.js', array(), XTS_HB_VERSION, true );

		wp_enqueue_style( 'xts-header-builder', $assets_path . 'css/builder.css', array(), XTS_HB_VERSION );

		wp_localize_script(
			'xts-header-builder',
			'headerBuilder',
			array(
				'sceleton'        => $this->builder->factory->get_header( false )->get_structure(),
				'settings'        => $this->builder->factory->get_header( false )->get_settings(),
				'name'            => XTS_HB_DEFAULT_NAME,
				'id'              => XTS_HB_DEFAULT_ID,
				'headersList'     => $this->builder->list->get_all(),
				'headersExamples' => $this->builder->list->get_examples(),
				'defaultHeader'   => $this->builder->manager->get_default_header(),
				'texts'           => array(
					'managerTitle'                       => esc_html__( 'Headers Manager', 'xts-theme' ),
					'description'                        => esc_html__( 'Here you can manage your header layouts, create new ones, import and export. You can set which header to use for all pages by default.', 'xts-theme' ),
					'createNew'                          => esc_html__( 'Create a new header', 'xts-theme' ),
					'import'                             => esc_html__( 'Import', 'xts-theme' ),
					'remove'                             => esc_html__( 'Remove header', 'xts-theme' ),
					'makeDefault'                        => esc_html__( 'Make default header', 'xts-theme' ),
					'headerSettings'                     => esc_html__( 'Header settings', 'xts-theme' ),
					'delete'                             => esc_html__( 'Delete', 'xts-theme' ),
					'Make it default'                    => esc_html__( 'Make it default', 'xts-theme' ),
					'Import new header'                  => esc_html__( 'Import new header', 'xts-theme' ),
					'Import'                             => esc_html__( 'Import', 'xts-theme' ),
					'JSON code for import is not valid!' => esc_html__( 'JSON code for import is not valid!', 'xts-theme' ),
					'Paste your JSON header export data here and click "Import"' => esc_html__( 'Paste your JSON header export data here and click Import', 'xts-theme' ),
					'Are you sure you want to remove this header?' => esc_html__( 'Are you sure you want to remove this header?', 'xts-theme' ),
					'Press OK to make this header default for all pages, Cancel to leave.' => esc_html__( 'Press OK to make this header default for all pages, Cancel to leave.', 'xts-theme' ),
					'Choose which layout you want to use as a base for your new header.' => esc_html__( 'Choose which layout you want to use as a base for your new header.', 'xts-theme' ),
					'Examples library'                   => esc_html__( 'Examples library', 'xts-theme' ),
					'User headers'                       => esc_html__( 'User headers', 'xts-theme' ),
					'Background image repeat'            => esc_html__( 'Background image repeat', 'xts-theme' ),
					'Inherit'                            => esc_html__( 'Inherit', 'xts-theme' ),
					'No repeat'                          => esc_html__( 'No repeat', 'xts-theme' ),
					'Repeat All'                         => esc_html__( 'Repeat All', 'xts-theme' ),
					'Repeat horizontally'                => esc_html__( 'Repeat horizontally', 'xts-theme' ),
					'Repeat vertically'                  => esc_html__( 'Repeat vertically', 'xts-theme' ),
					'Background image size'              => esc_html__( 'Background image size', 'xts-theme' ),
					'Cover'                              => esc_html__( 'Cover', 'xts-theme' ),
					'Contain'                            => esc_html__( 'Contain', 'xts-theme' ),
					'Background image attachment'        => esc_html__( 'Background image attachment', 'xts-theme' ),
					'Fixed'                              => esc_html__( 'Fixed', 'xts-theme' ),
					'Scroll'                             => esc_html__( 'Scroll', 'xts-theme' ),
					'Background image position'          => esc_html__( 'Background image position', 'xts-theme' ),
					'Left top'                           => esc_html__( 'Left top', 'xts-theme' ),
					'Left center'                        => esc_html__( 'Left center', 'xts-theme' ),
					'Left bottom'                        => esc_html__( 'Left bottom', 'xts-theme' ),
					'Center top'                         => esc_html__( 'Center top', 'xts-theme' ),
					'Center center'                      => esc_html__( 'Center center', 'xts-theme' ),
					'Center bottom'                      => esc_html__( 'Center bottom', 'xts-theme' ),
					'Right top'                          => esc_html__( 'Right top', 'xts-theme' ),
					'Right center'                       => esc_html__( 'Right center', 'xts-theme' ),
					'Right bottom'                       => esc_html__( 'Right bottom', 'xts-theme' ),
					'Preview'                            => esc_html__( 'Preview', 'xts-theme' ),
					'Width'                              => esc_html__( 'Width', 'xts-theme' ),
					'Style'                              => esc_html__( 'Style', 'xts-theme' ),
					'Container'                          => esc_html__( 'Container', 'xts-theme' ),
					'fullwidth'                          => esc_html__( 'fullwidth', 'xts-theme' ),
					'boxed'                              => esc_html__( 'boxed', 'xts-theme' ),
					'Upload an image'                    => esc_html__( 'Upload an image', 'xts-theme' ),
					'Upload'                             => esc_html__( 'Upload', 'xts-theme' ),
					'Open in new window'                 => esc_html__( 'Open in new window', 'xts-theme' ),
					'Add element to this section'        => esc_html__( 'Add element to this section', 'xts-theme' ),
					'Are you sure you want to delete this element?' => esc_html__( 'Are you sure you want to delete this element?', 'xts-theme' ),
					'Edit settings'                      => esc_html__( 'Edit settings', 'xts-theme' ),
					'Export this header structure'       => esc_html__( 'Export this header structure', 'xts-theme' ),
					'importDescription'                  => esc_html__(
						'Copy the code from the following text area and save it. You will be
					able to import it later with our import function in the headers
					manager.',
						'xts-theme'
					),
					'Save header'                        => esc_html__( 'Save header', 'xts-theme' ),
					'Back to headers list'               => esc_html__( 'Back to headers list', 'xts-theme' ),
					'Edit'                               => esc_html__( 'Edit', 'xts-theme' ),
					'Clone'                              => esc_html__( 'Clone', 'xts-theme' ),
					'Remove'                             => esc_html__( 'Remove', 'xts-theme' ),
					'Add element'                        => esc_html__( 'Add element', 'xts-theme' ),
					'Loading, please wait...'            => esc_html__( 'Loading, please wait...', 'xts-theme' ),
					'Close'                              => esc_html__( 'Close', 'xts-theme' ),
					'Save'                               => esc_html__( 'Save', 'xts-theme' ),
					'Header settings'                    => esc_html__( 'Header settings', 'xts-theme' ),
					'Export header'                      => esc_html__( 'Export header', 'xts-theme' ),
					'Desktop layout'                     => esc_html__( 'Desktop layout', 'xts-theme' ),
					'Mobile layout'                      => esc_html__( 'Mobile layout', 'xts-theme' ),
					'Header is successfully saved.'      => esc_html__( 'Header is successfully saved.', 'xts-theme' ),
					'Default header for all pages is changed.' => esc_html__( 'Default header for all pages is changed.', 'xts-theme' ),
					'Configure'                          => esc_html__( 'Configure', 'xts-theme' ),
					'settings'                           => esc_html__( 'settings', 'xts-theme' ),
				),
			)
		);

		wp_enqueue_script( 'xts-react' );
		wp_enqueue_script( 'xts-react-dom' );
		wp_enqueue_script( 'xts-header-builder' );

		wp_enqueue_editor();
		wp_enqueue_media();
	}
}
