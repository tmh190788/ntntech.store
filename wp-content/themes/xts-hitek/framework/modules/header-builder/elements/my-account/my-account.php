<?php
/**
 * My account element template
 *
 * @package xts
 */

if ( ! xts_is_woocommerce_installed() ) {
	return;
}

$wrapper_classes  = '';
$icon_classes     = '';
$dropdown_classes = '';
$color_scheme     = $params['color_scheme'];
$icon_style       = $params['icon_style'];
$user_current     = wp_get_current_user();
$account_link     = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

if ( 'custom' === $icon_style ) {
	$icon_classes .= ' xts-icon-custom';
}

$wrapper_classes .= ' xts-style-' . $params['style'];
if ( ! is_user_logged_in() && $params['login_form'] ) {
	$wrapper_classes .= ' xts-opener';
}

if ( 'dark' !== $color_scheme && $color_scheme ) {
	$dropdown_classes .= ' xts-scheme-' . $color_scheme;
}

if ( is_user_logged_in() ) {
	$label            = esc_html__( 'My Account', 'xts-theme' );
	$wrapper_classes .= ' xts-event-hover';

	if ( $params['with_username'] ) {
		/* translators: 1: User name */
		$label            = sprintf( esc_html__( 'Hello, %s', 'xts-theme' ), '<strong>' . esc_html( $user_current->display_name ) . '</strong>' );
		$wrapper_classes .= ' xts-with-username xts-event-hover';
	}
} else {
	$label = esc_html__( 'Login / Register', 'xts-theme' );
}

?>

<div class="xts-header-my-account xts-header-el<?php echo esc_attr( $wrapper_classes ); ?>">
	<a href="<?php echo esc_url( $account_link ); ?>">
		<span class="xts-header-el-icon<?php echo esc_attr( $icon_classes ); ?>">
			<?php if ( 'custom' === $icon_style ) : ?>
				<?php echo xts_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore ?>
			<?php endif; ?>
		</span>

		<span class="xts-header-el-label">
			<?php echo esc_html( $label ); ?>
		</span>
	</a>

	<?php if ( is_user_logged_in() ) : ?>
		<div class="xts-dropdown xts-dropdown-account xts-dropdown-menu xts-style-default<?php echo esc_attr( $dropdown_classes ); ?>">
			<div class="xts-dropdown-inner">
				<ul class="xts-sub-menu">
					<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
						<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
							<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
								<?php echo esc_html( $label ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>
</div>
