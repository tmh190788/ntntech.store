<?php
/**
 * Video item template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>

<div class="xts-el-video<?php echo esc_attr( $wrapper_classes ); ?>">
	<?php if ( 'button' === $element_args['video_action_button'] && $element_args['button_text'] ) : ?>
		<?php xts_button_template( $element_args ); ?>
	<?php endif; ?>

	<?php if ( 'play' === $element_args['video_action_button'] ) : ?>
		<a href="<?php echo esc_url( $element_args['button_link']['url'] ); ?>" class="xts-el-video-btn<?php echo esc_attr( $play_classes ); ?>">
			<span class="xts-el-video-play-btn"></span>
			<?php if ( $element_args['play_button_label'] ) : ?>
				<span class="xts-el-video-play-label">
					<?php echo esc_html( $element_args['play_button_label'] ); ?>
				</span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( 'overlay' === $element_args['video_action_button'] ) : ?>
		<div class="xts-el-video-overlay xts-fill" style="background-image: url(<?php echo esc_url( $image_url ); ?>);"></div>

		<div class="xts-el-video-control xts-fill">
			<span class="xts-el-video-play-btn"></span>

			<?php if ( $element_args['play_button_label'] ) : ?>
				<span class="xts-el-video-play-label">
					<?php echo esc_html( $element_args['play_button_label'] ); ?>
				</span>
			<?php endif; ?>
		</div>

		<a class="xts-el-video-link xts-el-video-btn-overlay xts-fill<?php echo esc_attr( $play_classes ); ?>" href="<?php echo esc_url( $element_args['button_link']['url'] ); ?>"></a>
	<?php endif; ?>

	<?php if ( 'hosted' === $element_args['video_type'] || 'yes' !== $element_args['video_overlay_lightbox'] ) : ?>
		<?php echo wp_kses( $video_html, 'xts_media' ); ?>
	<?php endif; ?>
</div>
