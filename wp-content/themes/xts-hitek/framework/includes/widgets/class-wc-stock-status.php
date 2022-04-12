<?php
/**
 * Sort by class.
 *
 * @package xts
 */

namespace XTS\Widget;

use XTS\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Sort by widget
 */
class WC_Stock_Status extends Widget_Base {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( '[XTemos] Stock status', 'xts-theme' ),
			'description' => esc_html__( 'Filter stock and on-sale products', 'xts-theme' ),
			'slug'        => 'xts-widget-stock-status',
			'fields'      => array(
				array(
					'id'      => 'title',
					'type'    => 'text',
					'name'    => esc_html__( 'Title', 'xts-theme' ),
					'default' => 'Stock status',
				),

				array(
					'id'      => 'instock',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'On Sale filter', 'xts-theme' ),
					'default' => true,
				),

				array(
					'id'      => 'onsale',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'In Stock filter', 'xts-theme' ),
					'default' => true,
				),
			),
		);

		$this->create_widget( $args );
		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'woocommerce_product_query', array( $this, 'show_in_stock_products' ) );
		add_filter( 'loop_shop_post_in', array( $this, 'show_on_sale_products' ) );
		add_filter( 'woocommerce_widget_get_current_page_url', array( $this, 'update_current_page_url' ) );
	}

	/**
	 * Update woocommerce link.
	 *
	 * @since 1.0.0
	 *
	 * @param string $link Link.
	 *
	 * @return string
	 */
	public function update_current_page_url( $link ) {
		if ( isset( $_GET['stock_status'] ) ) { // phpcs:ignore
			$link = add_query_arg( 'stock_status', wc_clean( $_GET['stock_status'] ), $link ); // phpcs:ignore
		}

		return $link;
	}

	/**
	 * In stock.
	 *
	 * @since 1.0.0
	 *
	 * @param object $query Query object.
	 */
	public function show_in_stock_products( $query ) {
		$current_stock_status = isset( $_GET['stock_status'] ) ? explode( ',', $_GET['stock_status'] ) : array(); // phpcs:ignore
		if ( in_array( 'instock', $current_stock_status ) ) { // phpcs:ignore
			$meta_query = array(
				'relation' => 'AND',
				array(
					'key'     => '_stock_status',
					'value'   => 'instock',
					'compare' => '=',
				),
			);

			$query->set( 'meta_query', array_merge( WC()->query->get_meta_query(), $meta_query ) );
		}
	}

	/**
	 * On sale.
	 *
	 * @since 1.0.0
	 *
	 * @param array $ids Product ids.
	 *
	 * @return array
	 */
	public function show_on_sale_products( $ids ) {
		$current_stock_status = isset( $_GET['stock_status'] ) ? explode( ',', $_GET['stock_status'] ) : array(); // phpcs:ignore
		if ( in_array( 'onsale', $current_stock_status ) ) { // phpcs:ignore
			$ids = array_merge( $ids, wc_get_product_ids_on_sale() );
		}

		return $ids;
	}

	/**
	 * Get link.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Status.
	 *
	 * @return string
	 */
	public function get_link( $status ) {
		$base_link            = xts_get_shop_page_link( true );
		$link                 = remove_query_arg( 'stock_status', $base_link );
		$current_stock_status = isset( $_GET['stock_status'] ) ? explode( ',', $_GET['stock_status'] ) : array(); // phpcs:ignore
		$option_is_set        = in_array( $status, $current_stock_status ); // phpcs:ignore

		if ( ! in_array( $status, $current_stock_status ) ) { // phpcs:ignore
			$current_stock_status[] = $status;
		}

		foreach ( $current_stock_status as $key => $value ) {
			if ( $option_is_set && $value === $status ) {
				unset( $current_stock_status[ $key ] );
			}
		}

		if ( $current_stock_status ) {
			asort( $current_stock_status );
			$link = add_query_arg( 'stock_status', implode( ',', $current_stock_status ), $link );
			$link = str_replace( '%2C', ',', $link );
		}

		return $link;
	}

	/**
	 * Output widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		echo wp_kses( $args['before_widget'], 'xts_widget' );

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo wp_kses( $args['before_title'], 'xts_widget' ) . $instance['title'] . wp_kses( $args['after_title'], 'xts_widget' ); // phpcs:ignore
		}

		$current_stock_status = isset( $_GET['stock_status'] ) ? explode( ',', $_GET['stock_status'] ) : array(); // phpcs:ignore

		?>
		<ul>
			<?php if ( $instance['onsale'] ) : ?>
				<?php $this->get_link( 'onsale' ); ?>
				<li>
					<a href="<?php echo esc_attr( $this->get_link( 'onsale' ) ); ?>" class="<?php echo in_array( 'onsale', $current_stock_status ) ? 'xts-selected' : ''; // phpcs:ignore ?>">
						<?php esc_html_e( 'On sale', 'xts-theme' ); ?>
					</a>
				</li>
			<?php endif; ?>

			<?php if ( $instance['instock'] ) : ?>
				<?php $this->get_link( 'instock' ); ?>
				<li>
					<a href="<?php echo esc_attr( $this->get_link( 'instock' ) ); ?>" class="<?php echo in_array( 'instock', $current_stock_status ) ? 'xts-selected' : ''; // phpcs:ignore ?>">
						<?php esc_html_e( 'In stock', 'xts-theme' ); ?>
					</a>
				</li>
			<?php endif; ?>
		</ul>
		<?php

		echo wp_kses( $args['after_widget'], 'xts_widget' );
	}
}

