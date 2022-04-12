<?php
/**
 * Main menu element template
 *
 * @package xts
 */

$menu_style     = $params['menu_style'] ? $params['menu_style'] : 'default';
$menu_items_gap = $params['menu_items_gap'] ? $params['menu_items_gap'] : 'm';

$location = 'main-menu';

$container_classes = '';
$menu_classes      = '';

// Container classes.
$container_classes = ' xts-textalign-' . $params['menu_align'];
if ( $params['menu_full_height'] && ( 'separated' === $menu_style || 'underline-dot' === $menu_style ) ) {
	$container_classes .= ' xts-full-height';
}

// Menu classes.
$menu_classes  = ' xts-style-' . $menu_style;
$menu_classes .= ' xts-gap-' . $menu_items_gap;

if ( has_nav_menu( $location ) ) {
	wp_nav_menu(
		array(
			'theme_location'  => $location,
			'container_class' => 'xts-header-nav-wrapper xts-nav-wrapper' . esc_attr( $container_classes ),
			'menu_class'      => 'menu xts-nav xts-nav-main xts-direction-h' . esc_attr( $menu_classes ),
			'walker'          => new XTS\Module\Mega_Menu\Walker( $menu_style ),
		)
	);

	do_action( 'xts_after_main_menu' );
} else {
	?>
	<div class="xts-nav-msg xts-textalign-<?php echo esc_attr( $params['menu_align'] ); ?>">
	<?php
		printf(
			wp_kses(
				/* translators: s: menu link */
				__( 'Create your first <a href="%s"><strong>navigation menu here</strong></a> and add it to the "Main menu" location.', 'xts-theme' ),
				'default'
			),
			esc_url( get_admin_url( null, 'nav-menus.php' ) )
		);
	?>
	</div>
	<?php
}
