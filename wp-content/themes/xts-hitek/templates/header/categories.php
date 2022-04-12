<?php
/**
 * Compare element template
 *
 * @package xts
 */

if ( ! wp_get_nav_menu_object( $params['menu_id'] ) ) {
	return;
}

$wrapper_classes  = '';
$title_classes    = '';
$icon_classes     = '';
$dropdown_classes = '';
$is_opened        = get_post_meta( xts_get_page_id(), '_xts_open_categories', true );

if ( xts_is_woocommerce_installed() && is_singular( 'product' ) ) {
	$is_opened = false;
}

$wrapper_classes .= ' xts-' . $id;
$wrapper_classes .= ' xts-style-' . $params['style'];
if ( ! $is_opened ) {
	$wrapper_classes .= ' xts-event-hover';
}

if ( $is_opened ) {
	$dropdown_classes .= ' xts-opened';
}

$icon_classes .= ' xts-icon-' . $params['icon_style'];

if ( 'custom' !== $params['background_color'] ) {
	$title_classes .= ' xts-bgcolor-' . $params['background_color'];
}
if ( 'light' !== $params['color_scheme'] ) {
	$title_classes .= ' xts-scheme-' . $params['color_scheme'];
}

$html = '';
if ( $params['more_cat_button'] ) {
	xts_enqueue_js_script( 'more-categories-button' );
	$wrapper_classes .= ' xts-more-cats';
	$html            .= '<li class="menu-item item-level-0 xts-more-cats-btn"><a href="#" class="xts-nav-link"></a></li>';
}

?>

<div class="xts-header-cats<?php echo esc_attr( $wrapper_classes ); ?>" role="navigation">
	<span class="xts-header-cats-title<?php echo esc_attr( $title_classes ); ?>">
		<?php if ( 'icon-text' === $params['style'] ) : ?>
			<span class="xts-header-cats-icon<?php echo esc_attr( $icon_classes ); ?>">
				<?php if ( 'custom' === $params['icon_style'] ) : ?>
					<?php echo xts_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore ?>
				<?php endif; ?>
			</span>
		<?php endif; ?>

		<span class="xts-header-cats-label">
			<?php esc_html_e( 'Browse categories', 'xts-theme' ); ?>
		</span>

	</span>

	<div class="xts-dropdown xts-dropdown-cats<?php echo esc_attr( $dropdown_classes ); ?>">
		<?php
		wp_nav_menu(
			array(
				'menu'            => $params['menu_id'],
				'menu_class'      => 'menu xts-nav xts-nav-mega xts-design-vertical xts-style-separated xts-direction-v',
				'container_class' => 'xts-dropdown-inner',
				'walker'          => new XTS\Module\Mega_Menu\Walker( 'default' ),
				'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s' . $html . '</ul>',
			)
		);
		?>
	</div>
</div>
