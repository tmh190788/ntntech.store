<?php
/**
 * Size guide table control.
 *
 * @package xts
 */

namespace XTS\Options\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Field;

/**
 * Size guide table control.
 */
class Size_Guide_Table extends Field {
	/**
	 * Displays the field control HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function render_control() {
		$name = $this->get_input_name();

		?>
		<div class="xts-size-guide-table-field">
			<textarea name="<?php echo esc_attr( $name ); ?>">
				<?php echo esc_html( $this->get_table_data() ); ?>
			</textarea>
		</div>
		<?php
	}

	/**
	 * Get table data
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_table_data() {
		$value = $this->get_field_value();

		if ( ! $value ) {
			$value = wp_json_encode(
				array(
					array( 'Size', 'UK', 'US', 'EU', 'Japan' ),
					array( 'XS', '6 - 8', '4', '34', '7' ),
					array( 'S', '8 -10', '6', '36', '9' ),
					array( 'M', '10 - 12', '8', '38', '11' ),
					array( 'L', '12 - 14', '10', '40', '13' ),
					array( 'XL', '14 - 16', '12', '42', '15' ),
					array( 'XXL', '16 - 28', '14', '44', '17' ),
				)
			);
		}

		return $value;
	}

	/**
	 * Enqueue color picker lib.
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		wp_enqueue_style( 'xts-edittable', XTS_FRAMEWORK_URL . '/assets/css/jquery.edittable.min.css', array(), XTS_VERSION );
		wp_enqueue_script( 'xts-edittable', XTS_FRAMEWORK_URL . '/assets/js-libs/jquery.edittable.min.js', array(), XTS_VERSION, true );
	}
}
