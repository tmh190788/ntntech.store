<?php
/**
 * Sticky bottom navbar.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Framework\Modules;
use XTS\Framework\Options;

/**
 * Product brands
 *
 * @since 1.0.0
 */
class Sticky_Bottom_Navbar extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ) );
		add_action( 'xts_after_site_wrapper', array( $this, 'template' ) );
	}

	/**
	 * Sticky toolbar template
	 *
	 * @since 1.0.0
	 */
	public function template() {
		$fields          = xts_get_opt( 'sticky_bottom_navbar_fields' );
		$wrapper_classes = '';

		if ( ! xts_get_opt( 'sticky_bottom_navbar' ) || ! $fields ) {
			return;
		}

		if ( xts_get_opt( 'sticky_bottom_navbar_texts' ) ) {
			$wrapper_classes .= ' xts-with-text';
		}

		?>
			<ul class="xts-sticky-navbar<?php echo esc_attr( $wrapper_classes ); ?>">
				<?php
				foreach ( $fields as $key => $value ) {
					switch ( $value ) {
						case 'wishlist':
							$this->wishlist_template();
							break;
						case 'cart':
							$this->cart_template();
							break;
						case 'compare':
							$this->compare_template();
							break;
						case 'account':
							$this->my_account_template();
							break;
						case 'sidebar':
							xts_enqueue_js_script( 'offcanvas-sidebar' );
							$this->sidebar_button_template();
							break;
						case 'mobile':
							$this->mobile_menu_template();
							break;
						case 'home':
							$this->link_template(
								array(
									'name' => 'home',
									'text' => esc_html__( 'Home', 'xts-theme' ),
									'url'  => get_home_url(),
								)
							);
							break;
						case 'blog':
							$this->link_template(
								array(
									'name' => 'blog',
									'text' => esc_html__( 'Blog', 'xts-theme' ),
									'url'  => get_permalink( get_option( 'page_for_posts' ) ),
								)
							);
							break;
						case 'shop':
							$this->link_template(
								array(
									'name' => 'shop',
									'text' => esc_html__( 'Shop', 'xts-theme' ),
									'url'  => xts_is_woocommerce_installed() ? get_permalink( wc_get_page_id( 'shop' ) ) : get_home_url(),
								)
							);
							break;
						case 'link_1':
							$this->link_template(
								array(
									'name' => 'link-1',
									'text' => xts_get_opt( 'sticky_bottom_navbar_link_1_text' ),
									'url'  => xts_get_opt( 'sticky_bottom_navbar_link_1_url' ),
									'icon' => xts_get_opt( 'sticky_bottom_navbar_link_1_icon' ),
								)
							);
							break;
						case 'link_2':
							$this->link_template(
								array(
									'name' => 'link-2',
									'text' => xts_get_opt( 'sticky_bottom_navbar_link_2_text' ),
									'url'  => xts_get_opt( 'sticky_bottom_navbar_link_2_url' ),
									'icon' => xts_get_opt( 'sticky_bottom_navbar_link_2_icon' ),
								)
							);
							break;
						case 'link_3':
							$this->link_template(
								array(
									'name' => 'link-3',
									'text' => xts_get_opt( 'sticky_bottom_navbar_link_3_text' ),
									'url'  => xts_get_opt( 'sticky_bottom_navbar_link_3_url' ),
									'icon' => xts_get_opt( 'sticky_bottom_navbar_link_3_icon' ),
								)
							);
							break;
						default:
							do_action( 'xts_mobile_navbar_extra_btn', $value );
							break;
					}
				}
				?>
			</ul>
		<?php
	}

	/**
	 * Link template.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data.
	 */
	public function link_template( $data = array() ) {
		$wrapper_classes = 'xts-navbar-' . $data['name'];

		// Wrapper classes.
		if ( isset( $data['opener'] ) && $data['opener'] ) {
			$wrapper_classes .= ' xts-opener';
		}
		if ( isset( $data['icon'] ) && isset( $data['icon']['id'] ) && $data['icon']['id'] ) {
			$wrapper_classes .= ' xts-custom-icon';
		}

		if ( ! $data['url'] || ! $data['text'] ) {
			return;
		}

		?>
			<li class="<?php echo esc_attr( $wrapper_classes ); ?>">
				<a href="<?php echo esc_url( $data['url'] ); ?>">
					<span class="xts-navbar-icon">
						<?php if ( isset( $data['count'] ) && $data['count'] ) : ?>
							<span class="xts-navbar-count">
								<?php echo wp_kses( $data['count'], true ); ?>
							</span>
						<?php endif; ?>

						<?php if ( isset( $data['icon'] ) && isset( $data['icon']['id'] ) && $data['icon']['id'] ) : ?>
							<?php echo wp_get_attachment_image( $data['icon']['id'] ); ?>
						<?php endif; ?>
					</span>

					<?php if ( xts_get_opt( 'sticky_bottom_navbar_texts' ) ) : ?>
						<span class="xts-navbar-text">
							<?php echo esc_html( $data['text'] ); ?>
						</span>
					<?php endif; ?>
				</a>
			</li>
		<?php
	}

	/**
	 * Sidebar button template.
	 *
	 * @since 1.0.0
	 */
	public function sidebar_button_template() {
		$sidebar_class = xts_get_sidebar_classes();
		$show          = false;

		if ( strstr( $sidebar_class, 'col-lg-0' ) || xts_is_maintenance_page() ) {
			return;
		}

		if ( is_singular( 'post' ) || xts_is_blog_archive() && xts_get_opt( 'blog_offcanvas_sidebar_mobile' ) ) {
			$show = true;
		} elseif ( xts_is_shop_archive() && xts_get_opt( 'shop_offcanvas_sidebar_mobile' ) ) {
			$show = true;
		} elseif ( is_singular( 'product' ) && xts_get_opt( 'single_product_offcanvas_sidebar_mobile' ) ) {
			$show = true;
		} elseif ( xts_get_opt( 'offcanvas_sidebar_mobile' ) ) {
			$show = true;
		}

		if ( ! $show ) {
			return;
		}

		$this->link_template(
			array(
				'name' => 'sidebar',
				'text' => esc_html__( 'Open sidebar', 'xts-theme' ),
				'url'  => '#',
			)
		);
	}

	/**
	 * Mobile menu template.
	 *
	 * @since 1.0.0
	 */
	public function mobile_menu_template() {
		$this->link_template(
			array(
				'name' => 'burger',
				'text' => esc_html__( 'Menu', 'xts-theme' ),
				'url'  => '#',
			)
		);
	}

	/**
	 * Account template.
	 *
	 * @since 1.0.0
	 */
	public function my_account_template() {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$settings = xts_get_header_settings();
		$opener   = isset( $settings['my-account'] ) && isset( $settings['my-account']['login_form'] ) && $settings['my-account']['login_form'] && ! is_user_logged_in();

		$this->link_template(
			array(
				'name'   => 'my-account',
				'text'   => esc_html__( 'Account', 'xts-theme' ),
				'url'    => get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ),
				'opener' => $opener,
			)
		);
	}

	/**
	 * Compare template.
	 *
	 * @since 1.0.0
	 */
	public function compare_template() {
		if ( ! xts_is_woocommerce_installed() || ! xts_get_opt( 'compare' ) ) {
			return;
		}

		$compare_module = Modules::get( 'wc-compare' );
		$settings       = xts_get_header_settings();
		$design         = isset( $settings['compare']['design'] ) ? $settings['compare']['design'] : false;

		xts_enqueue_js_script( 'product-compare' );

		$this->link_template(
			array(
				'name'  => 'compare',
				'text'  => esc_html__( 'Compare', 'xts-theme' ),
				'url'   => $compare_module->get_compare_page_url(),
				'count' => 'count' === $design ? $compare_module->get_compare_count() : false,
			)
		);
	}

	/**
	 * Cart template.
	 *
	 * @since 1.0.0
	 */
	public function cart_template() {
		if ( ! xts_is_woocommerce_installed() || ( ! is_user_logged_in() && xts_get_opt( 'login_to_see_price' ) ) ) {
			return;
		}

		$settings = xts_get_header_settings();
		$opener   = isset( $settings['cart']['widget_type'] ) && 'side' === $settings['cart']['widget_type'];

		$this->link_template(
			array(
				'name'   => 'cart',
				'text'   => esc_html__( 'Cart', 'xts-theme' ),
				'url'    => wc_get_cart_url(),
				'count'  => '<span class="xts-cart-count">' . WC()->cart->get_cart_contents_count() . '</span>',
				'opener' => $opener,
			)
		);
	}

	/**
	 * Wishlist template.
	 *
	 * @since 1.0.0
	 */
	public function wishlist_template() {
		if ( ! xts_is_woocommerce_installed() || ! xts_get_opt( 'wishlist' ) || ( xts_get_opt( 'wishlist_logged' ) && ! is_user_logged_in() ) ) {
			return;
		}

		$settings = xts_get_header_settings();
		$design   = isset( $settings['wishlist']['design'] ) ? $settings['wishlist']['design'] : false;

		xts_enqueue_js_script( 'product-wishlist' );

		$this->link_template(
			array(
				'name'  => 'wishlist',
				'text'  => esc_html__( 'Wishlist', 'xts-theme' ),
				'url'   => xts_get_whishlist_page_url(),
				'count' => 'count' === $design ? xts_get_wishlist_count() : false,
			)
		);
	}

	/**
	 * Add options.
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'sticky_bottom_navbar_section',
				'name'     => esc_html__( 'Mobile bottom navbar', 'xts-theme' ),
				'parent'   => 'general_section',
				'priority' => 40,
				'icon'     => 'xf-general',
			)
		);

		Options::add_field(
			array(
				'id'          => 'sticky_bottom_navbar',
				'name'        => esc_html__( 'Sticky navbar', 'xts-theme' ),
				'description' => esc_html__( 'Sticky navigation toolbar will be shown at the bottom on mobile devices.', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'sticky_bottom_navbar_section',
				'default'     => '0',
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'          => 'sticky_bottom_navbar_texts',
				'name'        => esc_html__( 'Buttons text', 'xts-theme' ),
				'description' => esc_html__( 'Show/hide texts under icons in the mobile navbar.', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'sticky_bottom_navbar_section',
				'default'     => '1',
				'priority'    => 20,
			)
		);

		Options::add_field(
			array(
				'id'          => 'sticky_bottom_navbar_fields',
				'name'        => esc_html__( 'Select buttons', 'xts-theme' ),
				'description' => esc_html__( 'Choose which buttons will be used for sticky navbar.', 'xts-theme' ),
				'type'        => 'select',
				'multiple'    => true,
				'select2'     => true,
				'section'     => 'sticky_bottom_navbar_section',
				'options'     => array(
					'shop'     => array(
						'name'  => esc_html__( 'Shop page', 'xts-theme' ),
						'value' => 'shop',
					),
					'sidebar'  => array(
						'name'  => esc_html__( 'Off canvas sidebar', 'xts-theme' ),
						'value' => 'sidebar',
					),
					'wishlist' => array(
						'name'  => esc_html__( 'Wishlist', 'xts-theme' ),
						'value' => 'wishlist',
					),
					'cart'     => array(
						'name'  => esc_html__( 'Cart', 'xts-theme' ),
						'value' => 'cart',
					),
					'account'  => array(
						'name'  => esc_html__( 'My account', 'xts-theme' ),
						'value' => 'account',
					),
					'mobile'   => array(
						'name'  => esc_html__( 'Mobile menu', 'xts-theme' ),
						'value' => 'mobile',
					),
					'home'     => array(
						'name'  => esc_html__( 'Home page', 'xts-theme' ),
						'value' => 'home',
					),
					'blog'     => array(
						'name'  => esc_html__( 'Blog page', 'xts-theme' ),
						'value' => 'blog',
					),
					'compare'  => array(
						'name'  => esc_html__( 'Compare', 'xts-theme' ),
						'value' => 'compare',
					),
					'link_1'   => array(
						'name'  => esc_html__( 'Custom button [1]', 'xts-theme' ),
						'value' => 'link_1',
					),
					'link_2'   => array(
						'name'  => esc_html__( 'Custom button [2]', 'xts-theme' ),
						'value' => 'link_2',
					),
					'link_3'   => array(
						'name'  => esc_html__( 'Custom button [3]', 'xts-theme' ),
						'value' => 'link_3',
					),
				),
				'default'     => array(
					'shop',
					'sidebar',
					'wishlist',
					'cart',
					'account',
				),
				'priority'    => 30,
			)
		);

		Options::add_field(
			array(
				'id'       => 'sticky_bottom_navbar_link_1_url',
				'name'     => esc_html__( 'Custom button URL', 'xts-theme' ),
				'group'    => esc_html__( 'Custom button [1]', 'xts-theme' ),
				'class'    => 'xts-col-6',
				'type'     => 'text_input',
				'section'  => 'sticky_bottom_navbar_section',
				'priority' => 40,
			)
		);

		Options::add_field(
			array(
				'id'       => 'sticky_bottom_navbar_link_1_text',
				'name'     => esc_html__( 'Custom button text', 'xts-theme' ),
				'group'    => esc_html__( 'Custom button [1]', 'xts-theme' ),
				'class'    => 'xts-col-6',
				'type'     => 'text_input',
				'section'  => 'sticky_bottom_navbar_section',
				'priority' => 50,
			)
		);

		Options::add_field(
			array(
				'id'       => 'sticky_bottom_navbar_link_1_icon',
				'name'     => esc_html__( 'Custom button icon', 'xts-theme' ),
				'group'    => esc_html__( 'Custom button [1]', 'xts-theme' ),
				'type'     => 'upload',
				'section'  => 'sticky_bottom_navbar_section',
				'priority' => 60,
			)
		);

		Options::add_field(
			array(
				'id'       => 'sticky_bottom_navbar_link_2_url',
				'name'     => esc_html__( 'Custom button URL', 'xts-theme' ),
				'group'    => esc_html__( 'Custom button [2]', 'xts-theme' ),
				'class'    => 'xts-col-6',
				'type'     => 'text_input',
				'section'  => 'sticky_bottom_navbar_section',
				'priority' => 70,
			)
		);

		Options::add_field(
			array(
				'id'       => 'sticky_bottom_navbar_link_2_text',
				'name'     => esc_html__( 'Custom button text', 'xts-theme' ),
				'group'    => esc_html__( 'Custom button [2]', 'xts-theme' ),
				'class'    => 'xts-col-6',
				'type'     => 'text_input',
				'section'  => 'sticky_bottom_navbar_section',
				'priority' => 80,
			)
		);

		Options::add_field(
			array(
				'id'       => 'sticky_bottom_navbar_link_2_icon',
				'name'     => esc_html__( 'Custom button icon', 'xts-theme' ),
				'group'    => esc_html__( 'Custom button [2]', 'xts-theme' ),
				'type'     => 'upload',
				'section'  => 'sticky_bottom_navbar_section',
				'priority' => 90,
			)
		);

		Options::add_field(
			array(
				'id'       => 'sticky_bottom_navbar_link_3_url',
				'name'     => esc_html__( 'Custom button URL', 'xts-theme' ),
				'group'    => esc_html__( 'Custom button [3]', 'xts-theme' ),
				'class'    => 'xts-col-6',
				'type'     => 'text_input',
				'section'  => 'sticky_bottom_navbar_section',
				'priority' => 100,
			)
		);

		Options::add_field(
			array(
				'id'       => 'sticky_bottom_navbar_link_3_text',
				'name'     => esc_html__( 'Custom button text', 'xts-theme' ),
				'group'    => esc_html__( 'Custom button [3]', 'xts-theme' ),
				'class'    => 'xts-col-6',
				'type'     => 'text_input',
				'section'  => 'sticky_bottom_navbar_section',
				'priority' => 110,
			)
		);

		Options::add_field(
			array(
				'id'       => 'sticky_bottom_navbar_link_3_icon',
				'name'     => esc_html__( 'Custom button icon', 'xts-theme' ),
				'group'    => esc_html__( 'Custom button [3]', 'xts-theme' ),
				'type'     => 'upload',
				'section'  => 'sticky_bottom_navbar_section',
				'priority' => 120,
			)
		);
	}
}
