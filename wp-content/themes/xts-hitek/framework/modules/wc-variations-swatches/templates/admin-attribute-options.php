<?php
/**
 * Product attribute options template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<div class="xts-field xts-col xts-buttons-control xts-label_color_scheme-field" data-id="label_color_scheme">
	<div class="xts-field-title">
		<span>
			<?php esc_html_e( 'Enable swatch', 'xts-theme' ); ?>
		</span>
	</div>

	<div class="xts-field-inner">
		<input <?php checked( $attribute_swatch, 'on' ); ?> name="attribute_swatch" id="attribute_swatch" type="checkbox">

		<p class="xts-description">
			<?php esc_html_e( 'Attribute dropdown will be replaces with squared buttons.', 'xts-theme' ); ?>
		</p>
	</div>
</div>

<div class="xts-field xts-col xts-buttons-control xts-label_color_scheme-field" data-id="label_color_scheme">
	<div class="xts-field-title">
		<span>
			<?php esc_html_e( 'Swatch size', 'xts-theme' ); ?>
		</span>
	</div>

	<div class="xts-field-inner">
		<select name="attribute_swatch_size" id="attribute_swatch_size" class="xts-select">
			<option value="m" <?php selected( $swatch_size, 'm' ); ?>>
				<?php esc_html_e( 'Medium', 'xts-theme' ); ?>
			</option>

			<option value="s" <?php selected( $swatch_size, 's' ); ?>>
				<?php esc_html_e( 'Small', 'xts-theme' ); ?>
			</option>

			<option value="l" <?php selected( $swatch_size, 'l' ); ?>>
				<?php esc_html_e( 'Large', 'xts-theme' ); ?>
			</option>
		</select>

		<p class="xts-description">
			<?php esc_html_e( 'The size of the swatches elements on the shop.', 'xts-theme' ); ?>
		</p>
	</div>
</div>
