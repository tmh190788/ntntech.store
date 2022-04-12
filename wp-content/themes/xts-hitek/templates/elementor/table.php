<?php
/**
 * Table template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_table_template' ) ) {
	/**
	 * Table template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_table_template( $element_args ) {
		$default_args = array(
			// Heading.
			'heading_items'         => array(),
			'heading_text_align'    => 'center',
			'heading_color_presets' => 'default',
			'heading_text_size'     => 'default',

			// Body.
			'body_items'            => array(),
			'body_text_align'       => 'center',
			'body_color_presets'    => 'default',
			'body_text_size'        => 'default',
		);

		$element_args = wp_parse_args( $element_args, $default_args ); // phpcs:ignore

		$heading_classes = '';
		$body_classes    = '';
		$wrapper_classes = '';

		// Heading classes.
		$heading_classes .= ' xts-textalign-' . $element_args['heading_text_align'];
		if ( 'default' !== $element_args['heading_color_presets'] ) {
			$heading_classes .= ' xts-textcolor-' . $element_args['heading_color_presets'];
		}
		if ( 'default' !== $element_args['heading_text_size'] ) {
			$heading_classes .= ' xts-fontsize-' . $element_args['heading_text_size'];
		}

		// Body classes.
		$body_classes .= ' xts-textalign-' . $element_args['body_text_align'];
		if ( 'default' !== $element_args['body_color_presets'] ) {
			$body_classes .= ' xts-textcolor-' . $element_args['body_color_presets'];
		}
		if ( 'default' !== $element_args['body_text_size'] ) {
			$body_classes .= ' xts-fontsize-' . $element_args['body_text_size'];
		}

		?>
		<div class="xts-table-wrapper xts-reset-all-last<?php echo esc_attr( $wrapper_classes ); ?>">
			<table class="xts-table">
				<?php if ( $element_args['heading_items'] ) : ?>
					<thead class="<?php echo esc_attr( $heading_classes ); ?>">
						<?php $heading_counter = 1; ?>

						<?php foreach ( $element_args['heading_items'] as $heading ) : ?>
							<?php
							$heading_classes  = '';
							$heading_classes .= ' elementor-repeater-item-' . $heading['_id'];

							?>

							<?php if ( 'cell' === $heading['heading_content_type'] ) : ?>
								<th class="xts-table-cell<?php echo esc_attr( $heading_classes ); ?>" colspan="<?php echo esc_attr( $heading['heading_cell_span'] ); ?>" rowspan="<?php echo esc_attr( $heading['heading_cell_row_span'] ); ?>">
									<?php echo wp_kses( $heading['heading_cell_text'], 'xts_table' ); ?>
								</th>
							<?php else : ?>
								<?php if ( $heading_counter > 1 && $heading_counter ) : ?>
									</tr><tr class="xts-table-row">
								<?php else : ?>
									<tr class="xts-table-row">
								<?php endif; ?>
							<?php endif; ?>

							<?php $heading_counter++; ?>
						<?php endforeach; ?>
					</thead>
				<?php endif; ?>

				<tbody class="<?php echo esc_attr( $body_classes ); ?>">
					<?php $body_counter = 1; ?>

					<?php foreach ( $element_args['body_items'] as $item ) : ?>
						<?php
						$cell_classes = '';
						$cell_classes .= ' elementor-repeater-item-' . $item['_id'];

						?>

						<?php if ( 'cell' === $item['body_content_type'] ) : ?>
							<<?php echo esc_attr( $item['body_cell_type'] ); ?> class="xts-table-cell<?php echo esc_attr( $cell_classes ); ?>" colspan="<?php echo esc_attr( $item['body_cell_span'] ); ?>" rowspan="<?php echo esc_attr( $item['body_cell_row_span'] ); ?>">
								<?php echo wp_kses( $item['body_cell_text'], 'xts_table' ); ?>
							</<?php echo esc_attr( $item['body_cell_type'] ); ?>>
						<?php else : ?>
							<?php if ( $body_counter > 1 && $body_counter ) : ?>
								</tr><tr class="xts-table-row">
							<?php else : ?>
								<tr class="xts-table-row">
							<?php endif; ?>
						<?php endif; ?>

						<?php $body_counter++; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
