<?php
/**
 * Image widget class.
 *
 * @package xts
 */

namespace XTS\Widget;

use XTS\Module\Mega_Menu\Walker;
use XTS\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Menu widget class.
 */
class Menu extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( '[XTemos] Mega menu', 'xts-theme' ),
			'description' => esc_html__( 'Display any mega menu built in Appearance - Menus.', 'xts-theme' ),
			'slug'        => 'xts-widget-mega-menu',
			'fields'      => array(
				array(
					'id'   => 'title',
					'type' => 'text',
					'name' => esc_html__( 'Title', 'xts-theme' ),
				),

				array(
					'id'      => 'menu',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Select menu', 'xts-theme' ),
					'fields'  => xts_get_menus_array( 'widget' ),
					'default' => '',
				),

				array(
					'id'      => 'design',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Design', 'xts-theme' ),
					'fields'  => xts_get_available_options( 'menu_orientation_widget' ),
					'default' => 'horizontal',
				),

				array(
					'id'      => 'style',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Style', 'xts-theme' ),
					'fields'  => xts_get_available_options( 'menu_style_widget' ),
					'default' => 'default',
				),

				array(
					'id'      => 'color_scheme',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Color scheme', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Dark', 'xts-theme' )  => 'dark',
						esc_html__( 'Light', 'xts-theme' ) => 'light',
					),
					'default' => 'dark',
				),

				array(
					'id'      => 'align',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Alignment', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Inherit', 'xts-theme' ) => 'inherit',
						esc_html__( 'Left', 'xts-theme' )  => 'left',
						esc_html__( 'Center', 'xts-theme' ) => 'center',
						esc_html__( 'Right', 'xts-theme' ) => 'right',
					),
					'default' => 'left',
				),

				array(
					'id'      => 'items_gap',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Items gap', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Small', 'xts-theme' ) => 's',
						esc_html__( 'Medium', 'xts-theme' ) => 'm',
						esc_html__( 'Large', 'xts-theme' ) => 'l',
					),
					'default' => 's',
				),
			),
		);

		$this->create_widget( $args );
	}

	/**
	 * Output widget.
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		echo wp_kses( $args['before_widget'], 'xts_widget' );

		$default_args = array(
			'title'        => '',
			'menu'         => '',
			'align'        => 'left',
			'style'        => 'default',
			'design'       => 'horizontal',
			'color_scheme' => 'dark',
			'items_gap'    => 's',
		);

		$instance = wp_parse_args( $instance, $default_args );

		$container_classes = '';
		$menu_classes      = '';

		// Menu classes.
		$menu_classes .= ' xts-style-' . $instance['style'];
		$menu_classes .= ' xts-design-' . $instance['design'];

		if ( 'vertical' === $instance['design'] ) {
			$menu_classes .= ' xts-direction-v';
		} else {
			$menu_classes .= ' xts-gap-' . $instance['items_gap'];
			$menu_classes .= ' xts-direction-h';
			if ( 'dark' !== $instance['color_scheme'] ) {
				$menu_classes .= ' xts-nav-scheme-' . $instance['color_scheme'];
			}

			// Container classes.
			if ( 'inherit' !== $instance['align'] ) {
				$container_classes .= ' xts-textalign-' . $instance['align'];
			}
		}

		if ( 'vertical' === $instance['design'] && $instance['title'] ) {
			?>
				<div class="xts-mega-title">
					<?php echo esc_html( $instance['title'] ); ?>
				</div>
			<?php
		} else {
			if ( isset( $instance['title'] ) && $instance['title'] ) {
				echo wp_kses( $args['before_title'], 'xts_widget' ) . $instance['title'] . wp_kses( $args['after_title'], 'xts_widget' ); // phpcs:ignore
			}
		}

		if ( wp_get_nav_menu_object( $instance['menu'] ) ) {
			wp_nav_menu(
				array(
					'menu'            => $instance['menu'],
					'container_class' => 'xts-mega-menu xts-nav-wrapper' . esc_attr( $container_classes ),
					'menu_class'      => 'menu xts-nav xts-nav-mega ' . esc_attr( $menu_classes ),
					'walker'          => new Walker( $instance['style'] ),
				)
			);
		}

		echo wp_kses( $args['after_widget'], 'xts_widget' );
	}
}
