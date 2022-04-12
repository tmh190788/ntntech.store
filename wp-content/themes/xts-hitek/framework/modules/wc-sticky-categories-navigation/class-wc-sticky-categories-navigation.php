<?php
/**
 * Sticky categories navigation.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Framework\Options;
use XTS\Module\Mega_Menu\Walker;

/**
 * Sticky categories navigation.
 *
 * @since 1.0.0
 */
class WC_Sticky_Categories_Navigation extends Module {
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
	 * Hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'xts_after_site_wrapper', array( $this, 'template' ), 70 );
	}

	/**
	 * Print
	 *
	 * @since 1.0.0
	 */
	public function template() {
		if ( ! xts_get_opt( 'sticky_categories_navigation_menu' ) ) {
			return;
		}

		?>
			<div class="xts-sticky-cats">
				<div class="xts-sticky-cats-title">
					<span><?php esc_html_e( 'Product Categories', 'xts-theme' ); ?></span>
				</div>

				<?php
				wp_nav_menu(
					array(
						'menu'       => xts_get_opt( 'sticky_categories_navigation_menu' ),
						'menu_class' => 'menu xts-nav xts-nav-sticky-cat xts-style-separated xts-direction-v',
						'container'  => '',
						'walker'     => new Walker( 'default' ),
					)
				);
				?>

				<?php if ( xts_get_opt( 'sticky_categories_navigation_social_buttons' ) ) : ?>
					<?php
					xts_social_buttons_template(
						array(
							'size'                  => 's',
							'label_text'            => esc_html__( 'Follow:', 'xts-theme' ),
							'name'                  => 'yes',
							'style'                 => 'with-text',
							'type'                  => 'follow',
							'wrapper_extra_classes' => 'xts-sticky-social-wrapper',
						)
					);
					?>
				<?php endif; ?>
			</div>
		<?php
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'sticky_categories_navigation_section',
				'name'     => esc_html__( 'Sticky categories navigation', 'xts-theme' ),
				'parent'   => 'shop_section',
				'priority' => 90,
				'icon'     => 'xf-general',
			)
		);

		Options::add_field(
			array(
				'id'           => 'sticky_categories_navigation_menu',
				'type'         => 'select',
				'name'         => esc_html__( 'Select menu', 'xts-theme' ),
				'empty_option' => true,
				'select2'      => true,
				'options'      => xts_get_menus_array( 'default' ),
				'section'      => 'sticky_categories_navigation_section',
				'default'      => '',
				'priority'     => 10,
			)
		);

		Options::add_field(
			array(
				'id'       => 'sticky_categories_navigation_social_buttons',
				'type'     => 'switcher',
				'name'     => esc_html__( 'Add social buttons', 'xts-theme' ),
				'section'  => 'sticky_categories_navigation_section',
				'default'  => '1',
				'priority' => 20,
			)
		);
	}
}
