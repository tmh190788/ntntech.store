<?php
/**
 * Price plan switcher template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_price_plan_switcher_template' ) ) {
	/**
	 * Price plan switcher template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_price_plan_switcher_template( $element_args ) {
		$default_args = array(
			'style'                                 => 'default',
			'text_align'                            => 'center',
			'pp_switcher_background_color_switcher' => 'no',
			'pp_switcher_shadow_switcher'           => 'no',
			'pp_switcher_border_switcher'           => 'no',

			// Pricing.
			'price_1'                               => 'Month',
			'price_2'                               => 'Year',
			'price_3'                               => 'Lifetime',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes = '';
		$ul_classes      = '';

		// Wrapper classes.
		$wrapper_classes .= ' xts-textalign-' . $element_args['text_align'];

		// Classes.
		$ul_classes .= ' xts-style-' . $element_args['style'];
		if ( 'yes' === $element_args['pp_switcher_background_color_switcher'] ) {
			$ul_classes .= ' xts-with-bg-color';
		}
		if ( 'yes' === $element_args['pp_switcher_shadow_switcher'] ) {
			$ul_classes .= ' xts-with-shadow';
		}
		if ( 'yes' === $element_args['pp_switcher_border_switcher'] ) {
			$ul_classes .= ' xts-with-border';
		}

		xts_enqueue_js_script( 'price-plan-switcher-element' );

		?>
		<div class="xts-nav-wrapper xts-nav-pp-switcher-wrapper xts-mb-action-swipe<?php echo esc_attr( $wrapper_classes ); ?>">
			<ul class="xts-nav xts-nav-pp-switcher<?php echo esc_attr( $ul_classes ); ?>">
				<?php if ( $element_args['price_1'] ) : ?>
					<li class="xts-active" data-action="price_1">
						<a href="#" class="xts-nav-link">
							<span class="xts-nav-text">
								<?php echo esc_html( $element_args['price_1'] ); ?>
							</span>
						</a>
					</li>
				<?php endif; ?>

				<?php if ( $element_args['price_2'] ) : ?>
					<li data-action="price_2">
						<a href="#" class="xts-nav-link">
							<span class="xts-nav-text">
								<?php echo esc_html( $element_args['price_2'] ); ?>
							</span>
						</a>
					</li>
				<?php endif; ?>

				<?php if ( $element_args['price_3'] ) : ?>
					<li data-action="price_3">
						<a href="#" class="xts-nav-link">
							<span class="xts-nav-text">
								<?php echo esc_html( $element_args['price_3'] ); ?>
							</span>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
		<?php
	}
}
