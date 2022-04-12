<?php
/**
 * Header banner main template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-header-banner<?php echo esc_attr( $wrapper_classes ); ?>">
	<div class="xts-header-banner-bg xts-fill"></div>
	<?php if ( xts_get_opt( 'header_banner_close_button' ) ) : ?>
		<a href="#" class="xts-header-banner-close"></a>
	<?php endif; ?>

	<?php if ( $banner_link ) : ?>
		<a href="<?php echo esc_url( $banner_link ); ?>" class="xts-header-banner-link xts-fill"></a>
	<?php endif; ?>

	<div class="xts-header-banner-content">
		<div class="container xts-reset-mb-10 xts-reset-last">
			<?php if ( ( 'html_block' === xts_get_opt( 'header_banner_content_type' ) ) ) : ?>
				<?php echo xts_get_html_block_content( xts_get_opt( 'header_banner_html_block' ) ); // phpcs:ignore ?>
			<?php else : ?>
				<?php echo xts_get_opt( 'header_banner_text' ); // phpcs:ignore ?>
			<?php endif; ?>
		</div>
	</div>
</div>
