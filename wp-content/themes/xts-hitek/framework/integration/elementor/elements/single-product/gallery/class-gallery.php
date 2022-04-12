<?php
/**
 * Gallery map
 *
 * @package xts
 */

namespace XTS\Elementor\Single_Product_Builder;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}
/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Gallery extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_single_product_gallery';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Gallery', 'xts-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'xf-woo-el-gallery';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'xts-product-elements' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		/**
		 * Style tab
		 */

		/**
		 * General settings
		 */
		$this->start_controls_section(
			'general_style_section',
			[
				'label' => esc_html__( 'General', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'thumbnails_gallery_position',
			[
				'label'       => esc_html__( 'Thumbnails position', 'xts-theme' ),
				'description' => esc_html__( 'Use vertical or horizontal position for thumbnails.', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'side'      => esc_html__( 'Side (vertical position)', 'xts-theme' ),
					'bottom'    => esc_html__( 'Bottom (horizontal carousel)', 'xts-theme' ),
					'grid-1'    => esc_html__( 'Bottom (1 columns)', 'xts-theme' ),
					'grid-2'    => esc_html__( 'Bottom (2 columns)', 'xts-theme' ),
					'grid-comb' => esc_html__( 'Combined grid', 'xts-theme' ),
					'without'   => esc_html__( 'Without', 'xts-theme' ),
				],
				'default'     => 'bottom',
			]
		);

		$this->add_control(
			'thumbnails_gallery_count',
			[
				'label'     => esc_html__( 'Thumbnails count', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 4,
				],
				'range'     => [
					'px' => [
						'min'  => 2,
						'max'  => 6,
						'step' => 1,
					],
				],
				'condition' => [
					'thumbnails_gallery_position' => [ 'side', 'bottom' ],
				],
			]
		);

		$this->add_control(
			'main_gallery_click_action',
			[
				'label'       => esc_html__( 'Main gallery click action', 'xts-theme' ),
				'description' => esc_html__( 'Enable/disable zoom option or switch to photoswipe popup.', 'xts-theme' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'zoom'       => esc_html__( 'Zoom', 'xts-theme' ),
					'photoswipe' => esc_html__( 'Photoswipe', 'xts-theme' ),
					'without'    => esc_html__( 'Without', 'xts-theme' ),
				],
				'default'     => 'zoom',
			]
		);

		$this->add_control(
			'main_gallery_photoswipe_btn',
			[
				'label'        => esc_html__( 'Show "Zoom Image" Button', 'xts-theme' ),
				'description'  => esc_html__( 'Click to open image in popup and swipe to zoom.', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
			]
		);

		$this->add_control(
			'main_gallery_lightbox_gallery',
			[
				'label'        => esc_html__( 'Show thumbnails in lightbox', 'xts-theme' ),
				'description'  => esc_html__( 'Display thumbnails navigation when you open the images lightbox.', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
			]
		);

		$this->add_control(
			'badges',
			[
				'label'        => esc_html__( 'Product badges', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '1',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => '1',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		global $post;

		if ( ! is_singular( 'product' ) ) {
			$post = xts_get_preview_product(); // phpcs:ignore
			setup_postdata( $post );
		}

		xts_single_product_gallery_template( $this->get_settings_for_display() );

		if ( ! is_singular( 'product' ) ) {
			wp_reset_postdata();
		}
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Gallery() );
