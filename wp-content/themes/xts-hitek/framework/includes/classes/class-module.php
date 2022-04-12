<?php
/**
 * Module abstract class.
 *
 * @package xts
 */

namespace XTS\Framework;

/**
 * Required for module basic structure pattern.
 *
 * @since 1.0.0
 */
abstract class Module {
	/**
	 * Class basic constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the module and all its data with files.
	 *
	 * @since 1.0.0
	 */
	abstract public function init();
}
