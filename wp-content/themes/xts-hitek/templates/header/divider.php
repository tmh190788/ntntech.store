<?php
/**
 * Divider element template
 *
 * @package xts
 */

$classes  = '';
$classes .= ' xts-direction-' . $params['direction'];
$classes .= ' xts-' . $id;
if ( $params['css_class'] ) {
	$classes .= ' ' . $params['css_class'];
}
if ( $params['color'] ) {
	$classes .= ' xts-with-color';
}
if ( 'v' === $params['direction'] && $params['full_height'] ) {
	$classes .= ' xts-full-height';
}

?>

<div class="xts-header-divider<?php echo esc_attr( $classes ); ?>"></div>
