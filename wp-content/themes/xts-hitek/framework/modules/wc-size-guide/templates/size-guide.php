<?php
/**
 * Size guide template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="mfp-with-anim xts-size-guide xts-popup-content mfp-hide" id="xts-size-guide">
	<?php if ( 'yes' === $title ) : ?>
		<h4 class="xts-size-guide-title">
			<?php echo esc_html( $post->post_title ); ?>
		</h4>
	<?php endif; ?>

	<?php if ( 'yes' === $content && $post->post_content ) : ?>
		<div class="xts-size-guide-content">
			<?php echo do_shortcode( $post->post_content ); ?>
		</div>
	<?php endif; ?>

	<?php if ( $hide_table ) : ?>
		<table class="xts-size-guide-table">
			<?php foreach ( $table as $row ) : ?>
				<tr>
					<?php foreach ( $row as $col ) : ?>
						<td>
							<?php echo esc_html( $col ); ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>

<div class="xts-size-guide-btn xts-action-btn <?php echo esc_attr( $button_classes ); ?>">
	<a class="xts-popup-opener" href="#xts-size-guide">
		<?php esc_html_e( 'Size guide', 'xts-theme' ); ?>
	</a>
</div>
