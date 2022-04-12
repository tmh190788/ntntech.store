<?php
/**
 * Menu overlay mode class.
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
 * Menu overlay mode class.
 *
 * @since 1.0.0
 */
class Menu_Overlay extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ), 10 );
		add_action( 'xts_header_css', array( $this, 'custom_css' ), 10, 2 );
	}

	/**
	 * Add custom css to header.
	 *
	 * @since 1.0.0
	 *
	 * @param string $css     CSS.
	 * @param array  $options Options.
	 *
	 * @return string
	 */
	public function custom_css( $css, $options ) {
		$top_border    = ( isset( $options['top-bar']['border']['width'] ) ) ? (int) $options['top-bar']['border']['width'] : 0;
		$header_border = ( isset( $options['general-header']['border']['width'] ) ) ? (int) $options['general-header']['border']['width'] : 0;
		$bottom_border = ( isset( $options['header-bottom']['border']['width'] ) ) ? (int) $options['header-bottom']['border']['width'] : 0;

		$total_border_height = $top_border + $header_border + $bottom_border;

		$total_height        = $options['top-bar']['height'] + $options['general-header']['height'] + $options['header-bottom']['height'];
		$total_sticky_height = $options['top-bar']['sticky_height'] + $options['general-header']['sticky_height'] + $options['header-bottom']['sticky_height'];

		$total_height += $total_border_height;

		if ( $options['boxed'] && ( $options['top-bar']['hide_desktop'] || ( ! $options['top-bar']['hide_desktop'] && $options['top-bar']['background'] ) ) ) { // Quick view.
			$total_height = $total_height + 30;
		}

		if ( $options['sticky_clone'] ) {
			$total_sticky_height = $options['sticky_height'];
		}

		ob_start();
		?>
.xts-close-side.xts-location-header {
	top: <?php echo esc_html( $total_height ); ?>px;
}
.admin-bar .xts-close-side.xts-location-header {
	top: <?php echo esc_html( $total_height + 32 ); ?>px;
}
.xts-close-side.xts-location-sticky-header {
	top: <?php echo esc_html( $total_sticky_height ); ?>px;
}
.admin-bar .xts-close-side.xts-location-sticky-header {
	top: <?php echo esc_html( $total_sticky_height + 32 ); ?>px;
}
		<?php
		return $css . ob_get_clean();
	}

	/**
	 * Add theme settings options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		/**
		 * Menu overlay.
		 */
		Options::add_field(
			array(
				'id'          => 'menu_overlay',
				'name'        => esc_html__( 'Menu content overlay', 'xts-theme' ),
				'description' => esc_html__( 'Adds a black overlay to content when hover on menu items in the header.', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'miscellaneous_section',
				'default'     => '1',
				'priority'    => 60,
			)
		);
	}
}
