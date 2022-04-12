<?php
/**
 * Row element template
 *
 * @package xts
 */

$class .= 'xts-' . $id;

if ( $params['sticky'] ) {
	xts_enqueue_js_script( 'header-builder' );
	$class .= ' xts-sticky-on';
} else {
	$class .= ' xts-sticky-off';
}

if ( $this->has_background( $params ) ) {
	$class .= ' xts-with-bg';
} else {
	$class .= ' xts-without-bg';
}

if ( $params['hide_desktop'] ) {
	$class .= ' xts-hide-lg';
}

if ( $params['hide_mobile'] ) {
	$class .= ' xts-hide-md';
}

if ( $params['shadow'] ) {
	$class .= ' xts-with-shadow';
}

if ( 'equal-sides' === $params['flex_layout'] ) {
	$class .= ' xts-layout-' . $params['flex_layout'];
}

if ( 'light' === $params['color_scheme'] ) {
	$class .= ' xts-header-scheme-' . $params['color_scheme'];
}

if ( $params['align_dropdowns_bottom'] ) {
	$class .= ' xts-dropdowns-align-bottom';
}

if ( ! $children ) {
	return;
}

?>

<div class="xts-header-row <?php echo esc_attr( $class ); ?>">
	<div class="container">
		<div class="xts-header-row-inner">
			<?php echo apply_filters( 'xts_header_builder_row_output', $children ); ?>
		</div>
	</div>
</div>
