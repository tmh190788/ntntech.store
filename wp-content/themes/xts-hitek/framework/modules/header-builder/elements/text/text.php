<?php
/**
 * Text element template
 *
 * @package xts
 */

$classes  = $params['inline'] ? ' xts-inline' : '';
$classes .= ' xts-' . $id;
if ( $params['css_class'] ) {
	$classes .= ' ' . $params['css_class'];
}
if ( 'inherit' !== $params['color_scheme'] ) {
	$classes .= ' xts-scheme-' . $params['color_scheme'];
}
if ( 'default' !== $params['font_size'] ) {
	$classes .= ' xts-header-fontsize-' . $params['font_size'];
}

?>

<div class="xts-header-text xts-reset-all-last<?php echo esc_attr( $classes ); ?>">
	<?php echo do_shortcode( $params['content'] ); ?>
</div>
