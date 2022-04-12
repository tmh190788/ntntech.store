<?php
/**
 * Mega menu template function
 *
 * @package xts
 */

use XTS\Module\Mega_Menu\Walker;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_mega_menu_template' ) ) {
	/**
	 * Mega menu template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_mega_menu_template( $element_args ) {
		$default_args = array(
			// Content.
			'title'                   => '',
			'menu'                    => '',

			// Style.
			'align'                   => 'left',
			'color_scheme'            => 'dark',
			'style'                   => 'default',
			'design'                  => 'horizontal',
			'items_gap'               => 's',
			'submenu_indicator_style' => 'chevron',

			// Title.
			'title_color_presets'     => 'white',
			'title_text_size'         => 'default',
			'title_bg_color'          => 'primary',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		if ( ! $element_args['menu'] ) {
			?>
				<div class="xts-notification xts-color-info">
					<?php esc_html_e( 'You need to select a menu from the list. If you don\'t have any, go to Dashboard -> Appearance -> Menus and create one.', 'xts-theme' ); ?>
				</div>
			<?php
			return;
		}

		$container_classes = '';
		$menu_classes      = '';
		$wrapper_classes   = '';
		$title_classes     = '';

		// Menu classes.
		$menu_classes .= ' xts-design-' . $element_args['design'];
		$menu_classes .= ' xts-style-' . $element_args['style'];
		$menu_classes .= ' xts-indicator-style-' . $element_args['submenu_indicator_style'];
		if ( 'dark' !== $element_args['color_scheme'] ) {
			$menu_classes .= ' xts-nav-scheme-' . $element_args['color_scheme'];
		}
		if ( 'vertical' === $element_args['design'] ) {
			$menu_classes .= ' xts-direction-v';
		} else {
			$menu_classes .= ' xts-gap-' . $element_args['items_gap'];
			$menu_classes .= ' xts-direction-h';

			// Container classes.
			$container_classes .= ' xts-textalign-' . $element_args['align'];
		}

		// Title classes.
		$title_classes .= ' xts-bgcolor-' . $element_args['title_bg_color'];
		if ( 'default' !== $element_args['title_color_presets'] ) {
			$title_classes .= ' xts-textcolor-' . $element_args['title_color_presets'];
		}
		if ( 'default' !== $element_args['title_text_size'] ) {
			$title_classes .= ' xts-fontsize-' . $element_args['title_text_size'];
		}
		if ( xts_elementor_is_edit_mode() ) {
			$title_classes .= ' elementor-inline-editing';
		}

		// Wrapper classes.
		if ( $element_args['title'] ) {
			$wrapper_classes .= ' xts-with-title';
		}

		?>
			<div class="xts-mega-menu<?php echo esc_attr( $wrapper_classes ); ?>">
				<?php if ( 'vertical' === $element_args['design'] && $element_args['title'] ) : ?>
					<div class="xts-mega-title title<?php echo esc_attr( $title_classes ); ?>" data-elementor-setting-key="title">
						<?php echo wp_kses( $element_args['title'], xts_get_allowed_html() ); ?>
					</div>
				<?php endif; ?>

				<?php if ( wp_get_nav_menu_object( $element_args['menu'] ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'menu'            => $element_args['menu'],
							'container_class' => 'xts-nav-wrapper' . esc_attr( $container_classes ),
							'menu_class'      => 'menu xts-nav xts-nav-mega ' . esc_attr( $menu_classes ),
							'walker'          => new Walker( $element_args['style'] ),
						)
					);
					?>
				<?php endif; ?>
			</div>
		<?php
	}
}
