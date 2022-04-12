<?php
/**
 * Mobile search element template
 *
 * @package xts
 */

$extra_classes = '';
$icon_type     = $params['icon_type'];

if ( 'custom' === $icon_type ) {
	$extra_classes .= ' xts-icon-custom';
}

$extra_classes .= ' xts-style-' . $params['style'];

?>

<div class="xts-header-mobile-search xts-header-el<?php echo esc_attr( $extra_classes ); ?>">
	<a href="#">
		<span class="xts-header-el-icon">
			<?php if ( 'custom' === $icon_type ) : ?>
				<?php echo xts_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore ?>
			<?php endif; ?>
		</span>

		<span class="xts-header-el-label">
			<?php esc_html_e( 'Search', 'xts-theme' ); ?>
		</span>
	</a>
</div>
