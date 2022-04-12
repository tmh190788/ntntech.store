<?php
/**
 * Upload your custom fonts.
 *
 * @package xts
 */

namespace XTS\Options\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Field;

/**
 * Custom fonts control class.
 */
class Custom_Fonts extends Field {
	/**
	 * Default field value.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $_default_value = array(
		'font-name'   => '',
		'font-weight' => 400,
		'font-woff'   => array(
			'url' => '',
			'id'  => '',
		),
		'font-woff2'  => array(
			'url' => '',
			'id'  => '',
		),
	);

	/**
	 * Contruct the object.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args     Field args array.
	 * @param arary  $options  Options from the database.
	 * @param string $type     Field type.
	 * @param string $object   Object.
	 */
	public function __construct( $args, $options, $type = 'options', $object = 'post' ) {
		parent::__construct( $args, $options, $type, $object );

		$this->args = $args;
	}

	/**
	 * Displays the field control HTML.
	 *
	 * @since 1.0.0
	 */
	public function render_control() {
		$value = $this->get_field_value();

		// get last index from the array.
		$key = 0;
		if ( is_array( $value ) ) {
			end( $value );
			$key = key( $value );
		}

		?>
			<div id="<?php echo esc_attr( $this->get_id() ); ?>" data-id="<?php echo esc_attr( $this->get_id() ); ?>" data-key="<?php echo esc_attr( $key ); ?>" class="xts-custom-fonts">

				<div class="xts-custom-fonts-sections">
					<?php if ( is_array( $value ) && count( $value ) > 0 ) : ?>
						<?php foreach ( $value as $index => $value ) : ?>
							<?php $this->render_section( $index ); ?>
						<?php endforeach; ?>
					<?php else : ?>
						<?php $this->render_section( 0 ); ?>
					<?php endif; ?>
				</div>

				<?php $this->section_template( false, $this->_default_value ); ?>

				<div class="xts-custom-fonts-btn-add xts-inline-btn xts-inline-btn-add"><?php esc_html_e( 'Add font', 'xts-theme' ); ?></div>

			</div>
		<?php
	}

	/**
	 * Renders one typography settings section based on index.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $index  Section index.
	 */
	public function render_section( $index ) {
		$default_value = $this->_default_value;
		$value         = $this->get_field_value();
		$section_value = array();

		if ( '{{index}}' === $index ) {
			return;
		}

		if ( isset( $value[ $index ] ) ) {
			$section_value = wp_parse_args( $value[ $index ], $default_value );
		} else {
			$section_value = $default_value;
		}

		$this->section_template( $index, $section_value );
	}

	/**
	 * Displays the section template.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $index  Section index.
	 * @param array   $section_value  Section data.
	 */
	public function section_template( $index, $section_value ) {
		$hide_class = false === $index ? ' xts-custom-fonts-template hide' : '';
		$index      = false === $index ? '{{index}}' : $index;

		$font_weight = array(
			esc_html__( 'Ultra-Light 100', 'xts-theme' ) => 100,
			esc_html__( 'Light 200', 'xts-theme' )       => 200,
			esc_html__( 'Book 300', 'xts-theme' )        => 300,
			esc_html__( 'Normal 400', 'xts-theme' )      => 400,
			esc_html__( 'Medium 500', 'xts-theme' )      => 500,
			esc_html__( 'Semi-Bold 600', 'xts-theme' )   => 600,
			esc_html__( 'Bold 700', 'xts-theme' )        => 700,
			esc_html__( 'Extra-Bold 800', 'xts-theme' )  => 800,
			esc_html__( 'Ultra-Bold 900', 'xts-theme' )  => 900,
		);

		$font_name = esc_html__( 'Custom font', 'xts-theme' );
		if ( $section_value['font-name'] && $section_value['font-weight'] ) {
			$font_name .= ' - ' . $section_value['font-name'] . ' (' . $section_value['font-weight'] . ')';
		}

		?>

			<div class="xts-custom-fonts-section<?php echo esc_attr( $hide_class ); ?>" data-id="<?php echo esc_attr( $this->get_id() ); ?>-<?php echo esc_attr( $index ); ?>">
				<div class="xts-row xts-row-spacing-20">
					<h3 class="xts-custom-fonts-title xts-col"><?php echo esc_html( $font_name ); ?></h3>
					<div class="xts-custom-fonts-field xts-col xts-col-6">
						<label class="xts-custom-fonts-label">
							<?php esc_html_e( 'Font name', 'xts-theme' ); ?>
						</label>
						<input type="text" name="<?php echo esc_attr( $this->get_input_name( $index, 'font-name' ) ); ?>" value="<?php echo esc_attr( $section_value['font-name'] ); ?>">
						<p class="xts-description"><?php esc_html_e( 'Enter your name with letters and spacing only. It will be used in a list of fonts under the Typography section. For example: Indie Flower', 'xts-theme' ); ?></p>
					</div>

					<div class="xts-custom-fonts-field xts-col xts-col-6">
						<label class="xts-custom-fonts-label">
							<?php esc_html_e( 'Font weight', 'xts-theme' ); ?>
						</label>
						<select name="<?php echo esc_attr( $this->get_input_name( $index, 'font-weight' ) ); ?>">
							<?php foreach ( $font_weight as $key => $value ) : ?>
								<?php
									$selected = (int) $section_value['font-weight'] === (int) $value ? 'selected' : '';
								?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php echo esc_attr( $selected ); ?>>
									<?php echo esc_html( $key ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<?php foreach ( $this->args['fonts'] as $font ) : ?>
						<?php
							/* translators: 1: Font name */
							$title  = sprintf( __( 'Font (.%s)', 'xts-theme' ), esc_attr( $font ) );
							$values = $section_value[ 'font-' . $font ];
							$name   = $this->get_input_name( $index, 'font-' . $font );
						?>
						<?php $this->upload_template( $title, $values, $name ); ?>
					<?php endforeach; ?>

				</div>
				<div class="xts-custom-fonts-btn-remove-wrap"><div class="xts-custom-fonts-btn-remove xts-inline-btn xts-inline-btn-remove"><?php esc_html_e( 'Remove', 'xts-theme' ); ?></div></div>
			</div>
		<?php
	}

	/**
	 * Displays the upload field template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title Field title.
	 * @param array  $values Field values.
	 * @param array  $name Field name.
	 */
	public function upload_template( $title, $values, $name ) {
		?>
			<div class="xts-custom-fonts-field xts-col xts-col-6 xts-upload-control">
				<label class="xts-custom-fonts-label"><?php echo esc_html( $title ); ?></label>
				<div class="xts-custon-font-inner">
					<div class="xts-upload-preview">
						<input type="text" class="xts-upload-preview-input" disabled value="<?php echo esc_url( $values['url'] ); ?>"> 
					</div>
					<div class="xts-upload-btns">
						<button class="xts-btn xts-upload-btn"><?php esc_html_e( 'Upload', 'xts-theme' ); ?></button>
						<button class="xts-btn xts-btn-disable xts-btn-remove<?php echo ( isset( $values['url'] ) && ! empty( $values['url'] ) ) ? ' xts-active' : ''; ?>"><?php esc_html_e( 'Remove', 'xts-theme' ); ?></button>

						<input type="hidden" class="xts-upload-input-url" name="<?php echo esc_attr( $name . '[url]' ); ?>" value="<?php echo esc_attr( $values['url'] ); ?>" />
						<input type="hidden" class="xts-upload-input-id" name="<?php echo esc_attr( $name . '[id]' ); ?>" value="<?php echo esc_attr( $values['id'] ); ?>" />
					</div>
				</div>
			</div>
		<?php
	}
}


