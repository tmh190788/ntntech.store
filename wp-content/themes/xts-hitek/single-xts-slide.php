<?php
/**
 * The template for displaying slide.
 *
 * @package xts
 */

get_header();

global $post;

$slider_term  = wp_get_post_terms( $post->ID, 'xts_slider' );
$slider_id    = $slider_term ? $slider_term[0]->term_id : '';
$mask_classes = '';

// Video settings.
$video_mp4     = get_post_meta( $post->ID, '_xts_slide_video_mp4', true );
$video_webm    = get_post_meta( $post->ID, '_xts_slide_video_webm', true );
$video_ogg     = get_post_meta( $post->ID, '_xts_slide_video_ogg', true );
$video_youtube = get_post_meta( $post->ID, '_xts_slide_video_youtube', true );
$video_vimeo   = get_post_meta( $post->ID, '_xts_slide_video_vimeo', true );

// Overlay mask.
$overlay_mask         = get_post_meta( $post->ID, '_xts_overlay_mask', true );
$dotted_overlay_style = get_post_meta( $post->ID, '_xts_dotted_overlay_style', true );
$mask_classes        .= ' xts-style-' . $overlay_mask;
if ( 'dotted' === $overlay_mask ) {
	$mask_classes .= ' xts-dotted-' . $dotted_overlay_style;
}

?>
<style>
	body .xts-slider.xts-anim-distortion .xts-slide-container {
		opacity: 1;
		pointer-events: visible;
		pointer-events: unset;
	}
</style>
<div class="xts-content-area col-12 xts-single-slide">
	<div id="xts-slider-<?php echo esc_attr( $slider_id ); ?>" class="xts-slider<?php echo esc_attr( xts_get_slider_classes( $slider_id, true ) ); ?>">
		<div id="xts-slide-<?php echo esc_attr( $post->ID ); ?>" class="xts-slide xts-loaded">
			<div class="container xts-slide-container<?php echo esc_attr( xts_get_slide_classes( $post->ID ) ); ?>">
				<div class="xts-slide-content">
					<?php
					while ( have_posts() ) {
						the_post();

						the_content();
					}
					?>
				</div>
			</div>

			<div class="xts-slide-bg xts-video-resize xts-fill">
				<?php if ( ( isset( $video_mp4['id'] ) && $video_mp4['id'] ) || ( isset( $video_webm['id'] ) && $video_webm['id'] ) || ( isset( $video_ogg['id'] ) && $video_ogg['id'] ) ) : ?>
					<?php
					xts_video_template(
						array(
							'classes'    => '',
							'attrs'      => array( 'preload', 'playsinline', 'muted', 'loop', 'autoplay' ),
							'video_mp4'  => $video_mp4,
							'video_webm' => $video_webm,
							'video_ogg'  => $video_ogg,
						)
					);
					?>
				<?php elseif ( $video_youtube ) : ?>
					<?php
					wp_enqueue_script( 'xts-youtube-player', 'https://www.youtube.com/player_api', array(), XTS_VERSION, true );

					echo Elementor\Embed::get_embed_html( // phpcs:ignore
						$video_youtube,
						array(
							'enablejsapi' => 1,
							'autoplay'    => 1,
							'controls'    => 0,
							'showinfo'    => 0,
							'loop'        => 1,
							'mute'        => 1,
							'rel'         => 0,
						),
						array(
							'lazy_load' => 0,
						),
						array(
							'class'  => 'xts-slide-video-youtube',
							'allow'  => 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture',
							'width'  => '100%',
							'height' => '100%',
						)
					);
					?>
				<?php elseif ( $video_vimeo ) : ?>
					<?php
					xts_enqueue_js_library( 'vimeo-player' );

					echo Elementor\Embed::get_embed_html( // phpcs:ignore
						$video_vimeo,
						array(
							'api'        => 1,
							'muted'      => 1,
							'background' => 1,
							'autoplay'   => 1,
							'loop'       => 1,
						),
						array(
							'lazy_load' => 0,
						),
						array(
							'class'  => 'xts-slide-video-vimeo',
							'allow'  => 'autoplay',
							'width'  => '100%',
							'height' => '100%',
						)
					);
					?>
				<?php endif; ?>
			</div>
			<?php if ( 'without' !== $overlay_mask ) : ?>
				<div class="xts-slide-overlay xts-fill<?php echo esc_attr( $mask_classes ); ?>"></div>
			<?php endif; ?>
		</div>

		<?php xts_get_slider_css( $slider_id, 'xts-slider-' . $slider_id, array( $post ) ); ?>
	</div>
</div>

<?php get_footer(); ?>
