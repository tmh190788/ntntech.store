<?php
/**
 * Banner subtitle template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>

<div class="xts-iimage-subtitle<?php echo esc_attr( $subtitle_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>subtitle">
	<?php echo wp_kses( $banner['subtitle'], xts_get_allowed_html() ); ?>
</div>