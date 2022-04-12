<?php
/**
 * Elementor google json controls
 *
 * @package xts
 */

namespace XTS\Elementor\Controls;

use Elementor\Base_Data_Control;
use Elementor\Modules\DynamicTags\Module as TagsModule;

/**
 * Elementor xts_google_json control.
 *
 * @since 1.0.0
 */
class Google_Json extends Base_Data_Control {

	/**
	 * Get xts_buttons control type.
	 *
	 * Retrieve the control type, in this case `xts_buttons`.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'xts_google_json';
	}

	/**
	 * Get xts_buttons control default settings.
	 *
	 * Retrieve the default settings of the xts_buttons control. Used to return the
	 * default settings while initializing the xts_buttons control.
	 *
	 * @since  1.8.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'label_block' => true,
			'dynamic' => [
				'categories' => [ TagsModule::TEXT_CATEGORY ],
			],
		];
	}

	/**
	 * Enqueue control scripts and styles.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue() {
		wp_enqueue_script( 'xts-google-json-control', XTS_FRAMEWORK_URL . '/integration/elementor/assets/js/google-json.js', [ 'jquery' ], XTS_VERSION, false );
	}

	/**
	 * Render textarea control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper elementor-control-dynamic-switcher-wrapper">
				<textarea class="elementor-control-tag-area" data-setting="" rows="5"></textarea>
				<input type="hidden" id="<?php echo esc_attr( $control_uid ); ?>" data-setting="{{ data.name }}">
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
