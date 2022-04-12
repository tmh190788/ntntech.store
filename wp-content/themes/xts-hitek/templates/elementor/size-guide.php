<?php
/**
 * Size guide template function
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_size_guide_template' ) ) {
	/**
	 * Size guide template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_size_guide_template( $element_args ) {
		$default_args = array(
			'size_guide_id' => '0',
			'title'         => 'yes',
			'content'       => 'yes',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		if ( ! $element_args['size_guide_id'] ) {
			?>
				<div class="xts-notification xts-color-info">
					<?php esc_html_e( 'You need to select some size guide from the list. If you don\'t have any, go to Dashboard -> Size guide and create one.', 'xts-theme' ); ?>
				</div>
			<?php
			return;
		}

		$post       = get_post( $element_args['size_guide_id'] );
		$hide_table = get_post_meta( $element_args['size_guide_id'], '_xts_size_guide_table', true );
		$table      = json_decode( get_post_meta( $element_args['size_guide_id'], '_xts_size_guide_table_data', true ) );

		?>
			<div class="xts-size-guide">
				<?php if ( 'yes' === $element_args['title'] ) : ?>
					<h4 class="xts-size-guide-title">
						<?php echo esc_html( $post->post_title ); ?>
					</h4>
				<?php endif; ?>

				<?php if ( 'yes' === $element_args['content'] ) : ?>
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
		<?php
	}
}
