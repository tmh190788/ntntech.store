<?php
/**
 * Burger element template
 *
 * @package xts
 */

$extra_classes = '';
$icon_classes  = '';
$icon_type     = $params['icon_type'];

$extra_classes .= ' xts-style-' . $params['style'];

if ( 'custom' === $icon_type ) {
	$icon_classes .= ' xts-icon-custom';
}

?>

<div class="xts-header-mobile-burger xts-header-el<?php echo esc_attr( $extra_classes ); ?>">
	<a href="#">
		<span class="xts-header-el-icon<?php echo esc_attr( $icon_classes ); ?>">
			<?php if ( 'custom' === $icon_type ) : ?>
				<?php echo xts_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore ?>
			<?php endif; ?>
		</span>

		<span class="xts-header-el-label">
			<?php esc_html_e( 'Menu', 'xts-theme' ); ?>
		</span>
	</a>
</div>
