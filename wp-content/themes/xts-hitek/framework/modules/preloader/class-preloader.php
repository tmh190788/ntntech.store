<?php
/**
 * Preloader class.
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
 * Preloader class.
 *
 * @since 1.0.0
 */
class Preloader extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'hooks' ) );
		add_action( 'init', array( $this, 'add_options' ) );
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'preloader_section',
				'name'     => esc_html__( 'Preloader', 'xts-theme' ),
				'priority' => 40,
				'parent'   => 'general_performance_section',
				'icon'     => 'xf-performance',
			)
		);

		Options::add_field(
			array(
				'id'          => 'preloader',
				'name'        => esc_html__( 'Preloader (beta)', 'xts-theme' ),
				'description' => esc_html__( 'Enable preloader animation while loading your website content. Useful when you move all the CSS to the footer.', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'preloader_section',
				'default'     => '0',
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'       => 'preloader_image',
				'name'     => esc_html__( 'Animated image', 'xts-theme' ),
				'type'     => 'upload',
				'section'  => 'preloader_section',
				'priority' => 20,
			)
		);

		Options::add_field(
			array(
				'id'       => 'preloader_background_color',
				'name'     => esc_html__( 'Background for loader screen', 'xts-theme' ),
				'type'     => 'color',
				'default'  => array(
					'idle' => '#ffffff',
				),
				'section'  => 'preloader_section',
				'priority' => 30,
			)
		);
	}

	/**
	 * Hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'xts_before_site_wrapper', array( $this, 'template' ), 500 );
	}

	/**
	 * Template
	 *
	 * @since 1.0.0
	 */
	public function template() {
		if ( ! xts_get_opt( 'preloader' ) ) {
			return;
		}

		$background_color = xts_get_opt( 'preloader_background_color' );
		$image            = xts_get_opt( 'preloader_image' );
		?>
		<style class="xts-preloader-style">
			html {
				overflow: hidden;
			}
		</style>
		<div class="xts-preloader">
			<style>
				<?php if ( isset( $background_color['idle'] ) && $background_color['idle'] ) : ?>
				.xts-preloader {
					background-color: <?php echo esc_attr( $background_color['idle'] ); ?>
				}
				<?php endif; ?>

				<?php if ( ! isset( $image['id'] ) || ( isset( $image['id'] ) && ! $image['id'] ) ) : ?>

				@keyframes xts-preloader-Rotate {
					0%{
						transform:scale(1) rotate(0deg);
					}
					50%{
						transform:scale(0.8) rotate(360deg);
					}
					100%{
						transform:scale(1) rotate(720deg);
					}
				}

				.xts-preloader-img:before {
					content: "";
					display: block;
					width: 50px;
					height: 50px;
					border: 2px solid #BBB;
					border-top-color: #000;
					border-radius: 50%;
					animation: xts-preloader-Rotate 2s cubic-bezier(0.63, 0.09, 0.26, 0.96) infinite ;
				}
				<?php endif; ?>

				@keyframes xts-preloader-fadeOut {
					from {
						visibility: visible; }
					to {
						visibility: hidden; }
				}

				.xts-preloader {
					position: fixed;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					opacity: 1;
					visibility: visible;
					z-index: 2500;
					display: flex;
					justify-content: center;
					align-items: center;
					animation: xts-preloader-fadeOut 20s ease both;
					transition: opacity .4s ease;
				}

				.xts-preloader.xts-preloader-hide {
					pointer-events: none;
					opacity: 0 !important;
				}

				.xts-preloader-img {
					max-width: 300px;
					max-height: 300px;
				}
			</style>

			<script>
				jQuery(window).on('load', function() {
					jQuery('.xts-preloader').delay(parseInt(xts_settings.preloader_delay)).addClass('xts-preloader-hide');
					jQuery('.xts-preloader-style').remove();
					setTimeout(function() {
						jQuery('.xts-preloader').remove();
					}, 200);
				});
			</script>

			<div class="xts-preloader-img">
				<?php if ( isset( $image['id'] ) && $image['id'] ) : ?>
					<img src="<?php echo esc_url( wp_get_attachment_url( $image['id'] ) ); ?>" alt="<?php esc_attr_e( 'preloader', 'xts-theme' ); ?>">
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
