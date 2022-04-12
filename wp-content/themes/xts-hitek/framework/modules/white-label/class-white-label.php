<?php
/**
 * White label class.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Framework\Options;


/**
 * White label class.
 *
 * @since 1.1.0
 */
class White_Label extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.1.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'hooks' ) );
		add_action( 'init', array( $this, 'add_options' ) );
	}

	/**
	 * Add options.
	 *
	 * @since 1.1.0
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'       => 'white_label',
				'name'     => esc_html__( 'White label', 'xts-theme' ),
				'group'    => esc_html__( 'White label', 'xts-theme' ),
				'type'     => 'switcher',
				'section'  => 'miscellaneous_section',
				'default'  => '0',
				'priority' => 90,
			)
		);

		Options::add_field(
			array(
				'id'       => 'white_label_license',
				'name'     => esc_html__( 'Theme license tab', 'xts-theme' ),
				'group'    => esc_html__( 'White label', 'xts-theme' ),
				'type'     => 'switcher',
				'section'  => 'miscellaneous_section',
				'requires' => array(
					array(
						'key'     => 'white_label',
						'compare' => 'equals',
						'value'   => '1',
					),
				),
				'default'  => '1',
				'priority' => 100,
			)
		);

		Options::add_field(
			array(
				'id'          => 'white_label_dummy_content',
				'name'        => esc_html__( 'Dummy content tab', 'xts-theme' ),
				'description' => esc_html__( 'Turn on/off the dummy content section in theme\'s dashboard.', 'xts-theme' ),
				'group'       => esc_html__( 'White label', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'miscellaneous_section',
				'requires'    => array(
					array(
						'key'     => 'white_label',
						'compare' => 'equals',
						'value'   => '1',
					),
				),
				'default'     => '1',
				'priority'    => 110,
			)
		);

		Options::add_field(
			array(
				'id'       => 'white_label_theme_name',
				'name'     => esc_html__( 'Theme name', 'xts-theme' ),
				'group'    => esc_html__( 'White label', 'xts-theme' ),
				'type'     => 'text_input',
				'section'  => 'miscellaneous_section',
				'requires' => array(
					array(
						'key'     => 'white_label',
						'compare' => 'equals',
						'value'   => '1',
					),
				),
				'default'  => '',
				'priority' => 120,
			)
		);
	}

	/**
	 * Hooks.
	 *
	 * @since 1.1.0
	 */
	public function hooks() {
		add_action( 'admin_print_styles', array( $this, 'custom_css' ), -100 );
	}

	/**
	 * Template.
	 *
	 * @since 1.1.0
	 */
	public function custom_css() {
		if ( ! xts_get_opt( 'white_label', '0' ) ) {
			return;
		}

		$theme_slug = 'xts-' . XTS_THEME_SLUG;

		?>

		<style>
			.theme[aria-describedby="<?php echo esc_html( $theme_slug ); ?>-action <?php echo esc_html( $theme_slug ); ?>-name"] img, .theme[aria-describedby="<?php echo esc_attr( $theme_slug ); ?>-child-action <?php echo esc_html( $theme_slug ); ?>-child-name"] img, .xts-space-theme img, .xts-space-theme .theme-info{
				display: none;
			}

			.theme-browser .theme[aria-describedby="<?php echo esc_html( $theme_slug ); ?>-action <?php echo esc_html( $theme_slug ); ?>-name"]:focus .theme-screenshot, .theme-browser .theme[aria-describedby="<?php echo esc_attr( $theme_slug ); ?>-action <?php echo esc_html( $theme_slug ); ?>-name"]:hover .theme-screenshot, .theme-browser .theme[aria-describedby="<?php echo esc_html( $theme_slug ); ?>-child-action <?php echo esc_html( $theme_slug ); ?>-child-name"]:focus .theme-screenshot, .theme-browser .theme[aria-describedby="<?php echo esc_html( $theme_slug ); ?>-child-action <?php echo esc_html( $theme_slug ); ?>-child-name"]:hover .theme-screenshot {
				opacity: 0.4;
			}

			.theme[aria-describedby="<?php echo esc_attr( $theme_slug ); ?>-action <?php echo esc_html( $theme_slug ); ?>-name"] .theme-screenshot:before,  .theme[aria-describedby="<?php echo esc_attr( $theme_slug ); ?>-child-action <?php echo esc_html( $theme_slug ); ?>-child-name"] .theme-screenshot:before, .xts-space-theme .screenshot:before{
				content: "<?php echo esc_html( xts_get_opt( 'white_label_theme_name' ) ); ?>";
				position: absolute;
				left: 0;
				right: 0;
				text-align: center;
				top: 50%;
				font-weight: 600;
				font-size: 50px;
				transform: translateY(-50%);
			}

			.theme-name#<?php echo esc_html( $theme_slug ); ?>-name:after {
				content: "<?php echo esc_html( xts_get_opt( 'white_label_theme_name' ) ); ?>";
				font-size: 15px;
				margin-left: 5px;
			}

			.theme-name#<?php echo esc_html( $theme_slug ); ?>-child-name:after {
				content: "<?php echo esc_html( xts_get_opt( 'white_label_theme_name' ) ); ?> Child";
				font-size: 15px;
				margin-left: 5px;
			}

			.theme-name#<?php echo esc_html( $theme_slug ); ?>-name span , .theme-name#<?php echo esc_html( $theme_slug ); ?>-child-name span{
				font-size: 15px;
			}

			.theme-name#<?php echo esc_html( $theme_slug ); ?>-name, .theme-name#<?php echo esc_html( $theme_slug ); ?>-child-name {
				font-size: 0;
			}

			#toplevel_page_xts_dashboard .wp-menu-image:before, .xf-dashboard:before {
				content: "\e901" !important;
			}
		</style>
		<?php
	}
}
