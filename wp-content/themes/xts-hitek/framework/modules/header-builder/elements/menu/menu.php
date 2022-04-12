<?php
/**
 * Menu element template
 *
 * @package xts
 */

$menu_style     = $params['menu_style'] ? $params['menu_style'] : 'default';
$menu_items_gap = $params['menu_items_gap'] ? $params['menu_items_gap'] : 'm';

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

if ( wp_get_nav_menu_object( $params['menu_id'] ) ) {
	wp_nav_menu(
		array(
			'menu'            => $params['menu_id'],
			'container_class' => 'xts-header-nav-wrapper xts-nav-wrapper' . esc_attr( $container_classes ),
			'menu_class'      => 'menu xts-nav xts-nav-secondary xts-direction-h' . esc_attr( $menu_classes ),
			'walker'          => new XTS\Module\Mega_Menu\Walker( $menu_style ),
		)
	);
} else {
	?>
	<div class="xts-nav-msg xts-textalign-<?php echo esc_attr( $params['menu_align'] ); ?>">
		<?php
		printf(
			wp_kses(
				/* translators: s: menu link */
				__( 'Create your first menu <a href="%s"><strong>here</strong></a>.', 'xts-theme' ),
				'default'
			),
			esc_url( get_admin_url( null, 'nav-menus.php' ) )
		);
		?>
	</div>
	<?php
}

