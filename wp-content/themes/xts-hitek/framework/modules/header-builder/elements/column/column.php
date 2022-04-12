<?php
/**
 * Column element template
 *
 * @package xts
 */

if ( ! $children ) {
	$class .= ' xts-empty';
}

?>

<div class="xts-header-col <?php echo esc_attr( $class ); ?>">
	<?php echo apply_filters( 'xts_header_builder_column_output', $children ); ?>
</div>
