<?php
/**
 * Search form
 *
 * @package xts
 */

?>

<div class="xts-search-wrapper<?php echo esc_attr( $wrapper_classes ); ?>">
	<?php do_action( 'xts_after_search_wrapper', $args['type'] ); ?>

	<?php if ( 'full-screen' === $args['type'] ) : ?>
		<div class="container">
			<div class="xts-search-close xts-action-btn xts-style-inline">
				<a href="#">
					<?php esc_html_e( 'Close', 'xts-theme' ); ?>
				</a>
			</div>
	<?php endif ?>

	<form role="search" method="get" class="searchform<?php echo esc_attr( $form_classes ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>" <?php echo wp_kses( $data, true ); ?>>
		<div class="searchform-input">
			<input type="text" class="s" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
			<input type="hidden" name="post_type" value="<?php echo esc_attr( $args['post_type'] ); ?>">

			<?php if ( 'yes' === $args['categories_dropdown'] && 'product' === $args['post_type'] ) : ?>
				<?php xts_enqueue_js_script( 'search-categories-dropdown' ); ?>
				<div class="xts-search-cats">
					<input type="hidden" name="product_cat" value="0">
					<a href="#" data-val="0">
						<span><?php esc_html_e( 'Select category', 'xts-theme' ); ?></span>
					</a>

					<div class="xts-dropdown xts-dropdown-search-cats">
						<div class="xts-dropdown-inner xts-scroll">
							<ul class="xts-sub-menu xts-scroll-content">
								<li class="xts-cat-item">
									<a href="#" data-val="0">
										<?php esc_html_e( 'Select category', 'xts-theme' ); ?>
									</a>
								</li>

								<?php
								wp_list_categories(
									array(
										'title_li' => false,
										'taxonomy' => 'product_cat',
										'use_desc_for_title' => false,
										'orderby'  => 'meta_value',
										'meta_key' => 'order', // phpcs:ignore
									)
								);
								?>
							</ul>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<button type="submit" class="searchsubmit<?php echo esc_attr( $button_classes ); ?>">
			<?php if ( 'custom' === $args['icon_type'] ) : ?>
				<?php echo xts_get_custom_icon( $args['custom_icon'] ); // phpcs:ignore ?>
			<?php endif; ?>

			<span class="submit-text">
				<?php esc_html_e( 'Search', 'xts-theme' ); ?>
			</span>
		</button>
	</form>

	<?php if ( 'full-screen' === $args['type'] ) : ?>
		<div class="xts-search-info">
			<?php echo esc_html( $description ); ?>
		</div>
	<?php endif ?>

	<?php if ( $args['ajax'] ) : ?>
		<?php if ( 'full-screen' === $args['type'] || 'mobile' === $args['location'] ) : ?>
			<div class="xts-search-results-wrapper xts-scroll">
				<div class="xts-search-results xts-scroll-content"></div>
			</div>
		<?php else : ?>
			<div class="xts-search-results-wrapper">
				<div class="xts-dropdown xts-search-results xts-scroll<?php echo esc_attr( $dropdown_classes ); ?>">
					<div class="xts-dropdown-inner xts-scroll-content"></div>
				</div>
			</div>
		<?php endif ?>
	<?php endif ?>

	<?php if ( 'full-screen' === $args['type'] ) : ?>
		</div>
	<?php endif ?>
</div>
