<?php
/**
 * My account links.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * My account links.
 *
 * @since 1.0.0
 */
class WC_My_Account_Links extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'hooks' ) );
		add_action( 'init', array( $this, 'add_options' ) );
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'woocommerce_account_dashboard', array( $this, 'links' ), 10 );
	}

	/**
	 * Links.
	 *
	 * @since 1.0.0
	 */
	public function links() {
		if ( ! xts_get_opt( 'my_account_links' ) ) {
			return;
		}

		xts_get_template(
			'links.php',
			array(),
			'wc-my-account-links'
		);
	}

	/**
	 * Add options.
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'my_account_links',
				'name'        => esc_html__( 'Dashboard icons menu', 'xts-theme' ),
				'description' => esc_html__( 'Adds icons blocks to the my account page as a navigation.', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'general_shop_section',
				'default'     => '1',
				'priority'    => 70,
			)
		);
	}
}
