<?php
/**
 * Header builder base class.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Header_Builder\Backend;
use XTS\Header_Builder\Frontend;
use XTS\Header_Builder\Elements;
use XTS\Header_Builder\Headers_List;
use XTS\Header_Builder\Header_Factory;
use XTS\Header_Builder\Styles;
use XTS\Header_Builder\Manager;

/**
 * Define constants, load classes and initialize everything.
 */
class Header_Builder extends Module {

	/**
	 * Elements class object.
	 *
	 * @var object
	 */
	public $elements = null;

	/**
	 * Headers list class object.
	 *
	 * @var object
	 */
	public $list = null;

	/**
	 * Header factory class object.
	 *
	 * @var object
	 */
	public $factory = null;

	/**
	 * Header manager class object.
	 *
	 * @var object
	 */
	public $manager = null;

	/**
	 * Header backend part class object.
	 *
	 * @var object
	 */
	public $backend = null;

	/**
	 * Header frontend part class object.
	 *
	 * @var object
	 */
	public $frontend = null;

	/**
	 * Header styles.
	 *
	 * @var object
	 */
	public $styles = null;

	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->define_constants();
		$this->include_files();
		$this->init_classes();
	}

	/**
	 * Define basic constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		define( 'XTS_HB_VERSION', '1.0' );
		define( 'XTS_HB_DEFAULT_ID', 'default_header_' . XTS_THEME_SLUG );
		define( 'XTS_HB_DEFAULT_NAME', 'Default ' . XTS_THEME_SLUG . ' header layout' );
		define( 'XTS_HB_DIR', XTS_FRAMEWORK_ABSPATH . 'modules/header-builder/' );
		define( 'XTS_HB_TEMPLATES', XTS_HB_DIR . 'elements/' );
	}

	/**
	 * Include main files.
	 *
	 * @since 1.0.0
	 */
	private function include_files() {
		$classes = array(
			'class-element',
			'class-backend',
			'class-frontend',
			'class-manager',
			'class-header-factory',
			'class-headers-list',
			'class-header',
			'class-elements',
			'class-styles',
		);

		foreach ( $classes as $class ) {
			$path = XTS_HB_DIR . $class . '.php';

			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Initialize base classes.
	 *
	 * @since 1.0.0
	 */
	private function init_classes() {
		$this->elements = new Elements();
		$this->list     = new Headers_List();
		$this->factory  = new Header_Factory( $this->elements );
		$this->manager  = new Manager( $this->factory, $this->list );
		$this->backend  = new Backend( $this );
		$this->frontend = new Frontend( $this );
		$this->styles   = new Styles();
	}
}
