<?php
/**
 * Header templates functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_page_top_part' ) ) {
	/**
	 * Generate page top part
	 *
	 * @since 1.0.0
	 */
	function xts_page_top_part() {
		$sidebar_classes = '';

		if ( is_singular( 'post' ) || xts_is_blog_archive() ) {
			if ( xts_get_opt( 'blog_offcanvas_sidebar_desktop' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'blog_offcanvas_sidebar_mobile' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-md';
			}
		} elseif ( xts_is_shop_archive() ) {
			if ( xts_get_opt( 'shop_offcanvas_sidebar_desktop' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'shop_offcanvas_sidebar_mobile' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-md';
			}
		} elseif ( is_singular( 'product' ) || is_singular( 'xts-template' ) ) {
			if ( xts_get_opt( 'single_product_offcanvas_sidebar_desktop' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'single_product_offcanvas_sidebar_mobile' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-md';
			}
		} else {
			if ( xts_get_opt( 'offcanvas_sidebar_desktop' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'offcanvas_sidebar_mobile' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-md';
			}
		}

		?>
		<?php if ( ! xts_is_ajax() ) : ?>
			<div class="xts-site-content">
		<?php elseif ( xts_is_ajax() ) : ?>
			<title><?php wp_title(); ?></title>

			<?php if ( xts_get_document_description() ) : ?>
				<meta name="description" content="<?php echo esc_attr( xts_get_document_description() ); ?>" />
			<?php endif; ?>
		<?php endif ?>

		<?php do_action( 'xts_before_site_content_container' ); ?>

		<div class="<?php echo esc_attr( xts_get_site_content_container_classes( get_the_ID() ) ); ?>">
			<div class="row row-spacing-40<?php echo esc_attr( $sidebar_classes ); ?>">
		<?php
	}
}

if ( ! function_exists( 'xts_mobile_menu' ) ) {
	/**
	 * Generate mobile menu
	 *
	 * @since 1.0.0
	 */
	function xts_mobile_menu() {
		$menu_locations = get_nav_menu_locations();
		$location       = 'main-menu';
		$menu_link      = get_admin_url( null, 'nav-menus.php' );
		$search_args    = apply_filters(
			'xts_mobile_menu_search_default_args',
			array(
				'search_style' => 'icon-alt',
				'location'     => 'mobile',
				'ajax'         => true,
			)
		);
		$settings       = xts_get_header_settings();

		if ( isset( $settings['search'] ) ) {
			$search_args['post_type'] = $settings['search']['post_type'];
			$search_args['ajax']      = isset( $settings['search']['ajax'] ) ? $settings['search']['ajax'] : true;
		}

		if ( ! isset( $settings['burger'] ) ) {
			return;
		}

		$search_form     = isset( $settings['burger']['search_form'] ) ? $settings['burger']['search_form'] : true;
		$wrapper_classes = '';
		$position        = $settings['burger']['position'];
		$color_scheme    = isset( $settings['burger']['color_scheme'] ) ? $settings['burger']['color_scheme'] : '';

		$wrapper_classes .= ' xts-side-' . $position;
		if ( 'dark' !== $color_scheme && $color_scheme ) {
			$wrapper_classes .= ' xts-scheme-' . $color_scheme;
			$wrapper_classes .= ' xts-widget-scheme-' . $color_scheme;
		}

		xts_enqueue_js_script( 'menu-mobile' );

		echo '<div class="xts-side-mobile xts-side-hidden' . esc_attr( $wrapper_classes ) . '">';

		if ( $search_form ) {
			xts_search_form( $search_args );
		}

		if ( isset( $menu_locations['mobile-menu'] ) && 0 !== $menu_locations['mobile-menu'] ) {
			$location = 'mobile-menu';
		}

		if ( has_nav_menu( $location ) ) {
			wp_nav_menu(
				array(
					'theme_location'  => $location,
					'container_class' => 'xts-nav-mobile-wrapper',
					'menu_class'      => 'menu xts-nav xts-nav-mobile xts-direction-v',
					'walker'          => new XTS\Module\Mega_Menu\Walker( 'default' ),
				)
			);
		} else {
			?>
			<div class="xts-nav-msg">
			<?php
				printf(
					wp_kses(
						/* translators: 1: menu settings link */
						__( 'Create your first <a href="%s"><strong>navigation menu here</strong></a>', 'xts-theme' ),
						'default'
					),
					esc_attr( $menu_link )
				);
			?>
			</div>
			<?php
		}

		?>

		<?php if ( is_active_sidebar( 'mobile-menu-widget-sidebar' ) ) : ?>
			<div class="xts-widgetarea-mobile">
				<?php dynamic_sidebar( 'mobile-menu-widget-sidebar' ); ?>	
			</div>
		<?php endif; ?>

		<?php

		echo '</div>';
	}

	add_action( 'xts_after_site_wrapper', 'xts_mobile_menu', 130 );
}
