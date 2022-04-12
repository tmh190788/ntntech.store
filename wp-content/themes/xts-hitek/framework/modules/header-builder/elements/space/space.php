<?php
/**
 * Spa element template
 *
 * @package xts
 */

$classes  = ' ' . $params['css_class'];
$classes .= ' xts-direction-' . $params['direction'];

if ( 'v' === $params['direction'] ) {
	$style = 'height:' . $params['width'] . 'px;';
} else {
	$style = 'width:' . $params['width'] . 'px;';
}

?>

<div class="xts-header-space<?php echo esc_attr( $classes ); ?>" style="<?php echo esc_attr( $style ); ?>"></div>
