<?php
/**
 *  Handle backend AJAX actions. test
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\AJAX_Response;

/**
 * Creat, load, remove headers from the backend interface with AJAX.
 */
class Manager {
	/**
	 * Header factory class object.
	 *
	 * @var Header_Factory
	 */
	private $factory;

	/**
	 * Headers list class object.
	 *
	 * @var object
	 */
	private $list;

	/**
	 * Options set prefix.
	 *
	 * @var array
	 */
	private $opt_name = XTS_THEME_SLUG;

	/**
	 * Class constructor.
	 *
	 * @param object $factory Header factory class object.
	 * @param object $list    Header class object.
	 */
	public function __construct( $factory, $list ) {
		$this->factory = $factory;
		$this->list    = $list;
		$this->ajax_actions();
	}

	/**
	 * Register all AJAX actions for header backend.
	 *
	 * @var object
	 */
	private function ajax_actions() {
		add_action( 'wp_ajax_xts_save_header', array( $this, 'save_header' ) );
		add_action( 'wp_ajax_xts_load_header', array( $this, 'load_header' ) );
		add_action( 'wp_ajax_xts_remove_header', array( $this, 'remove_header' ) );
		add_action( 'wp_ajax_xts_set_default_header', array( $this, 'set_default_header' ) );
	}

	/**
	 * Save header data.
	 *
	 * @var object
	 */
	public function save_header() {

		$structure = stripslashes( $_POST['structure'] ); // phpcs:ignore
		$settings  = stripslashes( $_POST['settings'] ); // phpcs:ignore

		// If we import a new header we don't have an ID.
		$id   = ( isset( $_POST['id'] ) ) ? stripslashes( $_POST['id'] ) : $this->generate_id(); // phpcs:ignore
		$name = stripslashes( $_POST['name'] ); // phpcs:ignore

		$header = $this->factory->update_header( $id, $name, json_decode( $structure, true ), json_decode( $settings, true ) );

		$this->list->add_header( $id, $name );

		$this->send_header_data( $header );
	}

	/**
	 * Load header by id.
	 *
	 * @var object
	 */
	public function load_header() {

		$id   = sanitize_text_field( $_GET['id'] ); // phpcs:ignore
		$base = ( isset( $_GET['base'] ) ) ? sanitize_text_field( $_GET['base'] ) : false; // phpcs:ignore

		if ( $_GET['initial'] || $base ) { // phpcs:ignore
			$header = $this->new_header( $base );
		} else {
			$header = $this->factory->get_header( $id );
		}

		$this->send_header_data( $header );
	}

	/**
	 * Send header JSON data.
	 *
	 * @param object $header Header data.
	 */
	private function send_header_data( $header ) {
		$data = $header->get_data();

		$data['list'] = $this->list->get_all();

		AJAX_Response::send_response( $data );
	}

	/**
	 * Create a new header.
	 *
	 * @param bool $base Header ID for the base.
	 *
	 * @return object
	 */
	private function new_header( $base = false ) {
		$list      = $this->list->get_all();
		$id        = $this->generate_id();
		$name      = 'Header layout (' . ( count( $list ) + 1 ) . ')';
		$structure = false;
		$settings  = false;

		if ( $base ) {
			$examples = $this->list->get_examples();
			if ( isset( $examples[ $base ] ) ) {
				$data      = json_decode( $this->get_example_json( $base ), true );
				$structure = $data['structure'];
				$settings  = $data['settings'];
				$name      = $data['name'];
			} elseif ( isset( $list[ $base ] ) ) {
				$data      = $this->factory->get_header( $base );
				$structure = $data->get_structure();
				$settings  = $data->get_settings();
			}

			$header = $this->factory->create_new( $id, $name, $structure, $settings );
		} else {
			$header = $this->factory->create_new( $id, $name );
		}

		$this->list->add_header( $id, $name );

		return $header;
	}

	/**
	 * Get header example JSON.
	 *
	 * @param string $file Example file name.
	 *
	 * @return false|string
	 */
	private function get_example_json( $file ) {
		$file = XTS_HB_DIR . '/examples/' . $file . '.json';
		ob_start();
		include $file;
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	 * Generate random header ID.
	 *
	 * @return string
	 */
	private function generate_id() {
		return 'header_' . wp_rand( 100000, 999999 );
	}

	/**
	 * Remove header from the database based on ID.
	 *
	 * @var object
	 */
	public function remove_header() {
		$id = stripslashes( $_GET['id'] ); // phpcs:ignore

		delete_option( 'xts_' . $id );

		AJAX_Response::send_response(
			array(
				'list' => $this->list->remove( $id ),
			)
		);
	}

	/**
	 * Set default header ID.
	 *
	 * @var object
	 */
	public function set_default_header() {
		$id = stripslashes( $_GET['id'] ); // phpcs:ignore

		update_option( 'xts_main_header', $id );

		$options = get_option( 'xts-' . $this->opt_name . '-options' );

		$options['default_header'] = $id;

		update_option( 'xts-' . $this->opt_name . '-options', $options );

		AJAX_Response::send_response(
			array(
				'default_header' => $id,
			)
		);
	}

	/**
	 * Get default header Id from the database.
	 *
	 * @var object
	 *
	 * @return mixed|string|void
	 */
	public function get_default_header() {
		$id = get_option( 'xts_main_header' );

		if ( ! $id ) {
			$id = XTS_HB_DEFAULT_ID;
		}

		return $id;
	}
}
