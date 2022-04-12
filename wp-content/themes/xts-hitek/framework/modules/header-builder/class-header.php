<?php
/**
 * Base header class.
 *
 * @package xts
 */

namespace XTS\Header_Builder;

use XTS\Styles_Storage;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * All the things related to the header and its structure.
 */
class Header {
	/**
	 * Elements class object.
	 *
	 * @var object
	 */
	private $elements;
	/**
	 * Header ID.
	 *
	 * @var string
	 */
	private $id = 'none';
	/**
	 * Header name.
	 *
	 * @var string
	 */
	private $name = 'none';
	/**
	 * Header structure.
	 *
	 * @var array
	 */
	private $structure;
	/**
	 * Header settings.
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Header options.
	 *
	 * @var array
	 */
	private $header_options = array();
	/**
	 * Header structure elements for some header options.
	 *
	 * @var array
	 */
	private $structure_elements = array( 'top-bar', 'general-header', 'header-bottom' );
	/**
	 * Header structure elements by type for some header options.
	 *
	 * @var array
	 */
	private $structure_elements_types = array(
		'logo',
		'search',
		'burger',
		'mainmenu',
		'cart',
		'my-account',
		'wishlist',
		'compare',
	);

	/**
	 * Set up all properties
	 *
	 * @var Styles_Storage
	 */
	public $storage;

	/**
	 * Header constructor method.
	 *
	 * @since 1.0.0
	 *
	 * @param object  $elements Elements object.
	 * @param string  $id       Header ID.
	 * @param boolean $new      Is it a new header.
	 */
	public function __construct( $elements, $id, $new = false ) {
		$this->elements = $elements;
		$this->id       = $id ? $id : XTS_HB_DEFAULT_ID;

		if ( $new ) {
			$this->create_empty();
		} else {
			$this->load();
		}

		$this->storage = new Styles_Storage( $this->get_id(), 'option', '', false );
	}

	/**
	 * Create an empty header on basic structure.
	 *
	 * @since 1.0.0
	 */
	private function create_empty() {
		$this->set_settings();
		$this->set_structure();
	}

	/**
	 * Load header from the database based on ID.
	 *
	 * @since 1.0.0
	 */
	private function load() {
		// Get data from the database.
		$data = get_option( 'xts_' . $this->get_id() );

		$name      = isset( $data['name'] ) ? $data['name'] : XTS_HB_DEFAULT_NAME;
		$settings  = isset( $data['settings'] ) ? $data['settings'] : array();
		$structure = isset( $data['structure'] ) ? $data['structure'] : false;

		$this->set_name( $name );
		$this->set_settings( $settings );
		$this->set_structure( $structure );
	}

	/**
	 * Header name setter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Header name.
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * Set header structure or load default from config.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $structure Header name.
	 */
	public function set_structure( $structure = false ) {
		if ( ! $structure ) {
			$structure = xts_get_config( 'header-builder-structure', 'theme' );
		}

		$this->structure = $structure;
	}

	/**
	 * Set header settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Header name.
	 */
	public function set_settings( $settings = array() ) {
		$this->settings = $settings;
	}

	/**
	 * Header ID getter.
	 *
	 * @since 1.0.0
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Header name getter.
	 *
	 * @since 1.0.0
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Header structure getter.
	 *
	 * @since 1.0.0
	 */
	public function get_structure() {
		return $this->validate_structure( $this->structure );
	}

	/**
	 * Header settings getter.
	 *
	 * @since 1.0.0
	 */
	public function get_settings() {
		return $this->validate_settings( $this->settings );
	}

	/**
	 * Validate header structure.
	 *
	 * @since 1.0.0
	 *
	 * @param array $structure Header structure.
	 *
	 * @return array
	 */
	private function validate_structure( $structure ) {
		$structure = $this->validate_sceleton( $structure );
		$structure = $this->validate_element( $structure );

		return $structure;
	}

	/**
	 * Save header.
	 *
	 * @since 1.0.0
	 */
	public function save() {
		$styles = new Styles();

		$this->storage->write( $styles->get_all_css( $this->get_structure(), $this->get_options() ) );

		update_option( 'xts_' . $this->get_id(), $this->get_raw_data() );
	}

	/**
	 * Get header data to save.
	 *
	 * @since 1.0.0
	 */
	public function get_raw_data() {
		return array(
			'structure' => $this->structure,
			'settings'  => $this->settings,
			'name'      => $this->get_name(),
			'id'        => $this->get_id(),
		);
	}

	/**
	 * Get header data after validation.
	 *
	 * @since 1.0.0
	 */
	public function get_data() {
		return array(
			'structure' => $this->get_structure(),
			'settings'  => $this->get_settings(),
			'name'      => $this->get_name(),
			'id'        => $this->get_id(),
		);
	}

	/**
	 * Set header options from elements.
	 *
	 * @since 1.0.0
	 *
	 * @param array $elements Header elements.
	 */
	private function set_header_options( $elements ) {
		foreach ( $elements as $element => $params ) {
			if ( ! in_array( $element, array_merge( $this->structure_elements, $this->structure_elements_types ), true ) ) {
				continue;
			}
			foreach ( $params as $key => $param ) {
				if ( isset( $param['value'] ) ) {
					$this->header_options[ $element ][ $key ] = $param['value'];
				}
			}
		}
	}

	/**
	 * Get header options.
	 *
	 * @since 1.0.0
	 */
	public function get_options() {
		$this->validate_settings( $this->settings );

		return $this->transform_settings_to_values( $this->header_options );
	}

	/**
	 * Validate header settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Header settings.
	 *
	 * @return array
	 */
	private function validate_settings( $settings ) {
		$default_settings = xts_get_config( 'header-builder-settings' );

		$settings = $this->validate_element_params( $settings, $default_settings );

		$this->header_options = array_merge( $settings, $this->header_options );

		return $settings;
	}

	/**
	 * Transform settings to values only.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Header settings.
	 *
	 * @return array
	 */
	private function transform_settings_to_values( $settings ) {
		foreach ( $settings as $key => $value ) {
			if ( isset( $value['value'] ) ) {
				$settings[ $key ] = $value['value'];
			}
			if ( in_array( $key, $this->structure_elements, true ) ) {
				if ( $value['hide_desktop'] ) {
					$settings[ $key ]['height'] = 0;
				}
				if ( $value['hide_mobile'] ) {
					$settings[ $key ]['mobile_height'] = 0;
				}
				if ( ( $value['hide_mobile'] && $value['hide_desktop'] ) || ! $value['sticky'] ) {
					$settings[ $key ]['sticky_height'] = 0;
				}
			}
		}

		return $settings;
	}

	/**
	 * Validate header sceleton.
	 *
	 * @since 1.0.0
	 *
	 * @param array $structure Header sceleton.
	 *
	 * @return array
	 */
	private function validate_sceleton( $structure ) {
		$sceleton = $this->get_header_sceleton();

		$structure_params = $this->grab_params_from_elements( $structure['content'] );

		$this->set_header_options( $structure_params );

		$structure_elements = $this->grab_content_from_elements( $structure['content'] );

		$sceleton  = $this->fill_sceleton_with_params( $sceleton, $structure_params );
		$structure = $this->fill_sceleton_with_elements( $sceleton, $structure_elements );

		return $structure;
	}

	/**
	 * Take params from structure elements of the header.
	 *
	 * @since 1.0.0
	 *
	 * @param array $elements Header elements.
	 *
	 * @return array
	 */
	private function grab_params_from_elements( $elements ) {
		$params = array();

		foreach ( $elements as $key => $element ) {
			if ( isset( $element['params'] ) && is_array( $element['params'] ) ) {
				$params[ $element['id'] ] = $element['params'];
			}

			if ( in_array( $element['type'], $this->structure_elements_types, true ) ) {
				$params[ $element['type'] ] = $element['params'];
			}

			if ( isset( $element['content'] ) && is_array( $element['content'] ) ) {
				$params = array_merge( $params, $this->grab_params_from_elements( $element['content'] ) );
			}
		}

		return $params;
	}

	/**
	 * Take elements content.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $elements Header elements.
	 * @param string $parent   Parent element's ID.
	 *
	 * @return array
	 */
	private function grab_content_from_elements( $elements, $parent = 'root' ) {
		$structure_elements            = array();
		$structure_elements[ $parent ] = array();

		foreach ( $elements as $key => $element ) {
			if ( isset( $element['content'] ) && is_array( $element['content'] ) ) {
				$structure_elements = array_merge( $structure_elements, $this->grab_content_from_elements( $element['content'], $element['id'] ) );
			} else {
				$structure_elements[ $parent ][ $element['id'] ] = $element;
			}
		}

		if ( empty( $structure_elements[ $parent ] ) ) {
			unset( $structure_elements[ $parent ] );
		}

		return $structure_elements;
	}

	/**
	 * Get header sceleton from configs.
	 *
	 * @since 1.0.0
	 */
	public function get_header_sceleton() {
		return xts_get_config( 'header-sceleton' );
	}

	/**
	 * Fill header base structure with elements.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sceleton          Header sceleton.
	 * @param array $default_structure Default elements structure..
	 *
	 * @return array
	 */
	public function fill_sceleton_with_elements( $sceleton, $default_structure ) {
		$sceleton = $this->fill_element_with_content( $sceleton, $default_structure );

		return $sceleton;
	}

	/**
	 * Add content to the element.
	 *
	 * @since 1.0.0
	 *
	 * @param array $element   Header element.
	 * @param array $structure Elements structure..
	 *
	 * @return array
	 */
	private function fill_element_with_content( $element, $structure ) {
		if ( empty( $element['content'] ) && isset( $structure[ $element['id'] ] ) ) {
			$element['content'] = $structure[ $element['id'] ];
		} elseif ( isset( $element['content'] ) && is_array( $element['content'] ) ) {
			$element['content'] = $this->fill_elements_with_content( $element['content'], $structure );
		}

		return $element;
	}

	/**
	 * Add content to elements.
	 *
	 * @since 1.0.0
	 *
	 * @param array $elements  Header elements.
	 * @param array $structure Elements structure..
	 *
	 * @return array
	 */
	private function fill_elements_with_content( $elements, $structure ) {
		foreach ( $elements as $id => $element ) {
			$elements[ $id ] = $this->fill_element_with_content( $element, $structure );
		}

		return $elements;
	}

	/**
	 * Fill sceleton structure with parameters.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sceleton Header sceleton.
	 * @param array $params   Parameters.
	 *
	 * @return array
	 */
	public function fill_sceleton_with_params( $sceleton, $params ) {
		$sceleton = $this->fill_element_with_params( $sceleton, $params );

		return $sceleton;
	}

	/**
	 * Fill element structure with parameters.
	 *
	 * @since 1.0.0
	 *
	 * @param array $element Header element.
	 * @param array $params  Parameters.
	 *
	 * @return array
	 */
	private function fill_element_with_params( $element, $params ) {
		if ( empty( $element['params'] ) && isset( $params[ $element['id'] ] ) ) {
			$element['params'] = $params[ $element['id'] ];
		} elseif ( isset( $element['content'] ) && is_array( $element['content'] ) ) {
			$element['content'] = $this->fill_elements_with_params( $element['content'], $params );
		}

		return $element;
	}

	/**
	 * Fill elements structure with parameters.
	 *
	 * @since 1.0.0
	 *
	 * @param array $elements Header elements.
	 * @param array $params   Parameters.
	 *
	 * @return array
	 */
	private function fill_elements_with_params( $elements, $params ) {
		foreach ( $elements as $id => $element ) {
			$elements[ $id ] = $this->fill_element_with_params( $element, $params );
		}

		return $elements;
	}

	/**
	 * Validate elements.
	 *
	 * @since 1.0.0
	 *
	 * @param array $elements Header elements.
	 *
	 * @return array
	 */
	private function validate_elements( $elements ) {
		foreach ( $elements as $key => $element ) {
			$validated = $this->validate_element( $element );
			if ( $validated ) {
				$elements[ $key ] = $validated;
			} else {
				unset( $elements[ $key ] );
			}
		}

		return $elements;
	}

	/**
	 * Validate element.
	 *
	 * @since 1.0.0
	 *
	 * @param array $el Header elements.
	 *
	 * @return array
	 */
	private function validate_element( $el ) {
		$type = trim( $el['type'] );

		$all_elements = $this->elements->get_all();

		if ( ! isset( $all_elements[ $type ] ) ) {
			return false;
		}

		$el_class = $all_elements[ $type ];

		$default = $el_class->get_args();

		$el = $this->validate_element_args( $el, $default );

		return $el;
	}

	/**
	 * Validate elements arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args    Arguments.
	 * @param array $default Defaults.
	 *
	 * @return array
	 */
	private function validate_element_args( $args, $default ) {
		foreach ( $default as $key => $value ) {
			if ( 'params' === $key && isset( $args[ $key ] ) ) {
				$args[ $key ] = $this->validate_element_params( $args[ $key ], $value );
			} elseif ( 'content' === $key && isset( $args[ $key ] ) ) {
				$args[ $key ] = $this->validate_elements( $args[ $key ] );
			} elseif ( ! isset( $args[ $key ] ) ) {
				$args[ $key ] = $value;
			}
		}

		return $args;
	}

	/**
	 * Validate elements parameters.
	 *
	 * @since 1.0.0
	 *
	 * @param array $params  Parameters.
	 * @param array $default Defaults.
	 *
	 * @return array
	 */
	private function validate_element_params( $params, $default ) {
		$params = wp_parse_args( $params, $default );

		foreach ( $params as $key => $value ) {
			if ( ! isset( $default[ $key ] ) ) {
				unset( $params[ $key ] );
			} else {
				$params[ $key ] = $this->validate_param( $params[ $key ], $default[ $key ] );
			}
		}

		return $params;
	}

	/**
	 * Validate parameter.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args         Arguments.
	 * @param array $default_args Defaults.
	 *
	 * @return array
	 */
	private function validate_param( $args, $default_args ) {
		foreach ( $default_args as $key => $value ) {
			// Validate image param by ID.
			if ( 'image' === $args['type'] && ! empty( $args['value'] ) && ! empty( $args['value']['id'] ) ) {
				$attachment = wp_get_attachment_image_src( $args['value']['id'], 'full' );
				if ( isset( $attachment[0] ) && ! empty( $attachment[0] ) ) {
					$args['value']['url']    = $attachment[0];
					$args['value']['width']  = $attachment[1];
					$args['value']['height'] = $attachment[2];
				} else {
					$args['value'] = '';
				}
			}

			if ( 'border' === $args['type'] && isset( $default_args['sides'] ) && is_array( $args['value'] ) ) {
				$args['value']['sides'] = $default_args['sides'];
			}

			if ( 'value' !== $key || ( 'value' === $key && ! isset( $args['value'] ) ) ) {
				$args[ $key ] = $value;
			}
		}

		return $args;
	}
}
