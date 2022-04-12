<?php
/**
 * Circle progress template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_circle_progress_template' ) ) {
	/**
	 * Circle progress template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_circle_progress_template( $element_args ) {
		$default_args = array(
			// General.
			'circle_size'                     => array( 'size' => 185 ),
			'stroke_width'                    => array( 'size' => 7 ),
			'duration'                        => 1000,

			// Value.
			'value_position'                  => 'inside',
			'value_type'                      => 'percent',
			'suffix'                          => '%',
			'percent_value'                   => array( 'size' => 50 ),
			'absolute_value_current'          => 50,
			'absolute_value_max'              => 200,

			// General.
			'circle_progress_shadow_switcher' => 'no',

			// Value.
			'value_color_presets'             => 'default',
			'value_text_size'                 => 'l',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$circle_value     = '';
		$value_value      = '';
		$value_classes    = '';
		$progress_classes = '';

		// Circle settings.
		$size          = $element_args['circle_size']['size'];
		$center        = $size / 2;
		$radius        = $center - ( $element_args['stroke_width']['size'] / 2 );
		$circumference = 2 * M_PI * $radius;

		if ( 'percent' === $element_args['value_type'] ) {
			$circle_value = $element_args['percent_value']['size'];
			$value_value  = $element_args['percent_value']['size'];
		} elseif ( 0 !== absint( $element_args['absolute_value_max'] ) ) {
			$circle_value = round( ( ( absint( $element_args['absolute_value_current'] ) * 100 ) / absint( $element_args['absolute_value_max'] ) ), 0 );
			$value_value  = $element_args['absolute_value_current'];
		}

		// Counter classes.
		$value_classes .= ' xts-position-' . $element_args['value_position'];
		$value_classes .= ' xts-textcolor-' . $element_args['value_color_presets'];
		if ( 'default' !== $element_args['value_text_size'] ) {
			$value_classes .= ' xts-fontsize-' . $element_args['value_text_size'];
		}

		// Progress classes.
		if ( 'yes' === $element_args['circle_progress_shadow_switcher'] ) {
			$progress_classes .= ' xts-with-shadow';
		}

		xts_enqueue_js_script( 'circle-progress-bar-element' );

		?>
			<div class="xts-circle-progress" data-duration="<?php echo esc_attr( $element_args['duration'] ); ?>" data-circumference="<?php echo esc_attr( $circumference ); ?>">
				<svg
				class="xts-circle-bar<?php echo esc_attr( $progress_classes ); ?>"
				viewBox="0 0 <?php echo esc_attr( $size ); ?> <?php echo esc_attr( $size ); ?>"
				>
					<circle
					class="xts-circle-meter"
					cx="<?php echo esc_attr( $center ); ?>"
					cy="<?php echo esc_attr( $center ); ?>"
					r="<?php echo esc_attr( $radius ); ?>"
					fill="none"
					viewBox="0 0 <?php echo esc_attr( $size ); ?> <?php echo esc_attr( $size ); ?>"
					></circle>

					<circle
					class="xts-circle-meter-value"
					cx="<?php echo esc_attr( $center ); ?>"
					cy="<?php echo esc_attr( $center ); ?>"
					r="<?php echo esc_attr( $radius ); ?>"
					data-value="<?php echo esc_attr( $circle_value ); ?>"
					fill="none"
					style="stroke-dasharray: <?php echo esc_attr( $circumference ); ?>; stroke-dashoffset: <?php echo esc_attr( $circumference ); ?>;"
					viewBox="0 0 <?php echo esc_attr( $size ); ?> <?php echo esc_attr( $size ); ?>"
					></circle>
				</svg>

				<div class="xts-circle-value<?php echo esc_attr( $value_classes ); ?>">
					<span class="xts-circle-number" data-state="new" data-final="<?php echo esc_attr( $value_value ); ?>">
						0
					</span>

					<span class="xts-circle-suffix">
						<?php echo esc_html( $element_args['suffix'] ); ?>
					</span>
				</div>
			</div>
		<?php
	}
}
