<?php
/**
 * Logo element template
 *
 * @package xts
 */

$logo            = XTS_THEME_URL . '/images/logo.svg';
$protocol        = xts_get_http_protocol() . '://';
$has_sticky_logo = isset( $params['sticky_image']['id'] ) && $params['sticky_image']['id'];

if ( isset( $params['image']['url'] ) && $params['image']['url'] ) {
	$logo = $params['image']['url'];
}

if ( isset( $params['image']['id'] ) && $params['image']['id'] ) {
	$logo = wp_get_attachment_image_url( $params['image']['id'], 'medium' );
}

$logo = $protocol . str_replace( array( 'http://', 'https://' ), '', $logo );

$width        = isset( $params['width'] ) ? (int) $params['width'] : 150;
$sticky_width = isset( $params['sticky_width'] ) ? (int) $params['sticky_width'] : 150;

$sticky_logo_class = $has_sticky_logo ? ' xts-sticky-logo' : '';

?>

<div class="xts-logo<?php echo esc_attr( $sticky_logo_class ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<?php echo '<img class="xts-logo-main" src="' . esc_url( $logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="max-width: ' . esc_attr( $width ) . 'px;" />'; ?>

		<?php if ( $has_sticky_logo ) : ?>
			<?php $logo_sticky = $protocol . str_replace( array( 'http://', 'https://' ), '', wp_get_attachment_image_url( $params['sticky_image']['id'], 'medium' ) ); ?>
				<?php echo '<img class="xts-logo-second" src="' . esc_url( $logo_sticky ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="max-width: ' . esc_attr( $sticky_width ) . 'px;" />'; ?>
		<?php endif ?>
	</a>
</div>
