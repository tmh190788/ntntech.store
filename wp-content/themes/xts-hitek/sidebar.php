<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package xts
 */

$sidebar_classes = xts_get_sidebar_classes();
$sidebar_name    = xts_get_sidebar_name();

if ( strstr( $sidebar_classes, 'col-lg-0' ) ) {
	return;
}

$sidebar_classes .= ' xts-' . $sidebar_name;

?>

<aside class="xts-sidebar<?php echo esc_attr( $sidebar_classes ); ?>">
	<div class="xts-heading-with-btn">
		<span class="title xts-fontsize-m">
			<?php esc_html_e( 'Sidebar', 'xts-theme' ); ?>
		</span>

		<div class="xts-close-button xts-action-btn xts-style-inline">
			<a href="#" ><?php esc_html_e( 'Close', 'xts-theme' ); ?></a>
		</div>
	</div>

	<div class="xts-sidebar-inner">
		<?php dynamic_sidebar( $sidebar_name ); ?>
	</div>
</aside>

<?php do_action( 'xts_after_sidebar' ); ?>
