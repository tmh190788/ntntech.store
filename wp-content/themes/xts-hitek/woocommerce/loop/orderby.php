<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$form_classes = '';

$style = isset( $style ) ? $style : 'dropdown';

if ( 'list' === $style ) {
	$form_classes .= ' xts-list';
}

?>

<form class="woocommerce-ordering<?php echo esc_attr( $form_classes ); ?>" method="get">
	<?php if ( 'list' === $style ) : ?>
		<ul>
			<?php foreach ( $catalog_orderby_options as $page_id => $name ) : ?>
				<?php
				$link         = add_query_arg( 'orderby', $page_id, xts_get_shop_page_link( true ) );
				$link_classes = '';
				if ( selected( $orderby, $page_id, false ) ) {
					$link_classes = ' xts-selected';
				}
				?>

				<li>
					<a href="<?php echo esc_url( $link ); ?>" data-order="<?php echo esc_attr( $page_id ); ?>" class="<?php echo esc_attr( $link_classes ); ?>"><?php echo esc_html( $name ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<select name="orderby" class="orderby" aria-label="<?php esc_attr_e( 'Shop order', 'woocommerce' ); ?>">
			<?php foreach ( $catalog_orderby_options as $page_id => $name ) : ?>
				<option value="<?php echo esc_attr( $page_id ); ?>" <?php selected( $orderby, $page_id ); ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
		</select>

		<?php
		foreach ( $_GET as $key => $val ) { // phpcs:ignore
			if ( 'orderby' === $key || 'submit' === $key ) {
				continue;
			}

			if ( is_array( $val ) ) {
				foreach ( $val as $inner_val ) {
					echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $inner_val ) . '" />';
				}
			} else {
				echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
			}
		}
		?>
	<?php endif ?>
	<?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged' ) ); ?>
</form>
