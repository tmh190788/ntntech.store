<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

/**
 * XTS Settings
 */
$menu_style         = xts_get_default_value( 'single_product_tabs_menu_style' );
$menu_gap           = xts_get_default_value( 'single_product_tabs_menu_gap' );
$wrapper_classes    = '';
$tab_classes        = '';
$navigation_classes = '';
$tab_count          = 0;
$content_count      = 0;
$layout             = isset( $layout ) ? $layout : xts_get_opt( 'single_product_tabs_layout' );

$wrapper_classes .= ' xts-layout-' . $layout;
if ( 'accordion' === $layout ) {
	xts_enqueue_js_script( 'single-product-accordion' );
	xts_enqueue_js_script( 'accordion-element' );
	$wrapper_classes .= ' xts-accordion xts-style-bordered xts-scroll';
	$tab_classes     .= ' xts-scroll-content xts-accordion-desc xts-accordion-content';
}

$navigation_classes .= ' xts-style-' . $menu_style;
$navigation_classes .= ' xts-gap-' . $menu_gap;

if ( comments_open() ) {
	xts_enqueue_js_script( 'wc-comments' );
}

?>

<?php if ( ! empty( $product_tabs ) ) : ?>
	<div class="woocommerce-tabs wc-tabs-wrapper<?php echo esc_attr( $wrapper_classes ); ?>" data-toggle-self="yes" data-state="first">
		<div class="xts-nav-wrapper xts-nav-product-tabs-wrapper xts-mb-action-swipe">
			<ul class="tabs wc-tabs xts-nav xts-nav-product-tabs xts-direction-h<?php echo esc_attr( $navigation_classes ); ?>" role="tablist">
				<?php foreach ( $product_tabs as $key => $tab ) : // phpcs:ignore ?>
					<li class="<?php echo esc_attr( $key ); ?>_tab <?php echo 0 === $tab_count ? 'active' : ''; ?>" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
						<a href="#tab-<?php echo esc_attr( $key ); ?>" class="xts-nav-link" data-tab-index="<?php echo esc_attr( $key ); ?>">
							<?php if ( 'underline-2' === $menu_style ) : ?>
								<span class="xts-nav-text">
									<span>
										<?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); // phpcs:ignore ?>
									</span>
								</span>
							<?php else : ?>
								<span class="xts-nav-text">
									<?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); // phpcs:ignore ?>
								</span>
							<?php endif; ?>
						</a>
					</li>
					<?php $tab_count++; ?>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php foreach ( $product_tabs as $key => $tab ) : // phpcs:ignore ?>
			<?php if ( 'accordion' === $layout ) : ?>
				<div class="xts-accordion-item">
					<div class="xts-accordion-title xts-icon-right <?php echo 0 === $content_count ? 'xts-active' : ''; ?>" data-accordion-index="<?php echo esc_attr( $key ); ?>">
						<span class="xts-accordion-title-text">
							<?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); // phpcs:ignore ?>
						</span>

						<span class="xts-accordion-icon xts-style-arrow"></span>
					</div>
			<?php endif; ?>
				<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab<?php echo esc_attr( $tab_classes ); ?> <?php echo 0 === $content_count ? 'xts-active' : ''; ?>" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>" data-accordion-index="<?php echo esc_attr( $key ); ?>">
					<?php if ( isset( $tab['callback'] ) ) : ?>
						<?php call_user_func( $tab['callback'], $key, $tab ); ?>
					<?php endif; ?>
				</div>
			<?php if ( 'accordion' === $layout ) : ?>
				</div>
			<?php endif; ?>
			<?php $content_count++; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
