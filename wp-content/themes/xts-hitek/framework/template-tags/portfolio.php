<?php
/**
 * Portfolio templates functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_portfolio_main_loop' ) ) {
	/**
	 * Main portfolio loop
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $fragments Fragments.
	 */
	function xts_get_portfolio_main_loop( $fragments = false ) {
		global $paged, $wp_query;

		$max_page                  = $wp_query->max_num_pages;
		$filters_type              = xts_get_opt( 'portfolio_filters_type' );
		$pagination                = xts_get_opt( 'portfolio_pagination' );
		$design                    = xts_get_opt( 'portfolio_design' );
		$masonry                   = xts_get_opt( 'portfolio_masonry' );
		$different_images          = xts_get_opt( 'portfolio_different_images' );
		$columns                   = xts_get_opt( 'portfolio_columns' );
		$columns_tablet            = xts_get_opt( 'portfolio_columns_tablet' );
		$columns_mobile            = xts_get_opt( 'portfolio_columns_mobile' );
		$spacing                   = xts_get_opt( 'portfolio_spacing' );
		$animation_in_view         = xts_get_opt( 'portfolio_animation_in_view' );
		$animation_items           = xts_get_opt( 'portfolio_animation' );
		$animation_duration_items  = xts_get_opt( 'portfolio_animation_duration' );
		$animation_delay_items     = xts_get_opt( 'portfolio_animation_delay' );
		$different_images_position = explode( ',', xts_get_opt( 'portfolio_different_images_position' ) );
		$uniqid                    = uniqid();
		$wrapper_classes           = '';
		$carousel_attrs            = '';

		if ( is_search() ) {
			$pagination = 'links';
		}

		if ( $fragments && isset( $_GET['loop'] ) ) { // phpcs:ignore
			xts_set_loop_prop( 'portfolio_loop', (int) sanitize_text_field( $_GET['loop'] ) ); // phpcs:ignore
		}

		// Wrapper classes.
		$wrapper_classes .= xts_get_row_classes( $columns, $columns_tablet, $columns_mobile, $spacing );
		$wrapper_classes .= ' xts-portfolio-design-' . $design;
		if ( 'parallax' === $design ) {
			xts_enqueue_js_library( 'parallax' );
			xts_enqueue_js_script( 'parallax-3d' );
		}
		if ( $masonry || 'masonry' === $filters_type ) {
			wp_enqueue_script( 'imagesloaded' );
			xts_enqueue_js_library( 'isotope-bundle' );
			xts_enqueue_js_script( 'masonry-layout' );
			$wrapper_classes .= ' xts-masonry-layout';
		}
		if ( $different_images ) {
			$wrapper_classes .= ' xts-different-images';
		}
		if ( $animation_in_view ) {
			xts_enqueue_js_script( 'items-animation-in-view' );
			$wrapper_classes .= ' xts-in-view-animation';
		}
		$wrapper_classes .= ' xts-autoplay-animations-off';

		if ( $fragments ) {
			ob_start();
		}

		?>

		<?php if ( xts_get_opt( 'ajax_portfolio' ) && ! $fragments ) : ?>
			<?php xts_enqueue_js_script( 'sticky-loader-position' ); ?>
			<div class="xts-sticky-loader">
				<span class="xts-loader"></span>
			</div>
		<?php endif; ?>

		<?php if ( ! $fragments ) : ?>
			<div id="<?php echo esc_attr( $uniqid ); ?>" class="xts-portfolio-loop<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo wp_kses( $carousel_attrs, true ); ?> data-source="main_loop" data-paged="1" data-animation-delay="<?php echo esc_attr( $animation_delay_items ); ?>">
		<?php endif ?>

		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>

			<?php
			// Increase loop count.
			xts_set_loop_prop( 'portfolio_loop', xts_get_loop_prop( 'portfolio_loop' ) + 1 );

			$index = xts_get_loop_prop( 'portfolio_loop' );

			$column_classes = '';

			if ( in_array( $index, $different_images_position ) && $different_images ) { // phpcs:ignore
				$column_classes .= ' xts-wide';
			}

			// Animations.
			if ( $animation_in_view && $animation_items ) {
				$column_classes .= ' xts-animation-' . $animation_items;
				$column_classes .= ' xts-animation-' . $animation_duration_items;
			}

			?>

			<div class="xts-col<?php echo esc_attr( $column_classes ); ?>" data-loop="<?php echo esc_attr( $index ); ?>">
				<?php xts_get_template_part( 'templates/portfolio-' . $design ); ?>
			</div>
		<?php endwhile; ?>

		<?php if ( ! $fragments ) : ?>
			</div>
		<?php endif ?>

		<?php if ( $max_page > 1 && ! $fragments ) : ?>
			<?php if ( 'load_more' === $pagination || 'infinite' === $pagination ) : ?>
				<?php xts_loadmore_pagination( $pagination, 'portfolio', $max_page, $uniqid ); ?>
			<?php elseif ( 'links' === $pagination ) : ?>
				<?php xts_posts_pagination( $max_page ); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php

		if ( $fragments ) {
			$output = array(
				'items'       => ob_get_clean(),
				'status'      => $max_page > $paged ? 'have-posts' : 'no-more-posts',
				'nextPage'    => str_replace( '&#038;', '&', next_posts( $max_page, false ) ),
				'currentPage' => strtok( xts_get_current_url(), '?' ),
			);

			echo wp_json_encode( $output );
		}
	}
}

if ( ! function_exists( 'xts_portfolio_filters' ) ) {
	/**
	 * Generate portfolio filters
	 *
	 * @since 1.0.0
	 *
	 * @param string $category Parent category.
	 * @param string $type Filters type.
	 */
	function xts_portfolio_filters( $category, $type ) {
		$categories = get_terms( 'xts-portfolio-cat', array( 'parent' => $category ) );
		$menu_style = xts_get_default_value( 'portfolio_filters_menu_style' );

		if ( is_wp_error( $categories ) || ! $categories ) {
			return;
		}

		$wrapper_classes    = '';
		$all_link_classes   = '';
		$navigation_classes = '';

		$navigation_classes .= ' xts-style-' . $menu_style;

		$wrapper_classes .= ' xts-type-' . $type;

		if ( 'masonry' === $type ) {
			xts_enqueue_js_script( 'portfolio-filters' );
			$all_link_url      = '#';
			$all_link_classes .= ' xts-active';
		} else {
			$all_link_url = get_post_type_archive_link( 'xts-portfolio' );

			if ( is_post_type_archive( 'xts-portfolio' ) || ! is_tax( 'xts-portfolio-cat' ) ) {
				$all_link_classes .= ' xts-active';
			}
		}

		?>
			<div class="xts-nav-wrapper xts-nav-portfolio-wrapper xts-mb-action-swipe<?php echo esc_attr( $wrapper_classes ); ?>">
				<ul class="xts-nav xts-nav-portfolio xts-gap-m<?php echo esc_attr( $navigation_classes ); ?>">
					<li data-filter="*" class="<?php echo esc_attr( $all_link_classes ); ?>">
						<a href="<?php echo esc_url( $all_link_url ); ?>" class="xts-nav-link">
							<?php if ( 'underline-2' === $menu_style ) : ?>
								<span class="xts-nav-text">
									<span>
										<?php esc_html_e( 'All', 'xts-theme' ); ?>
									</span>
								</span>
							<?php else : ?>
								<span class="xts-nav-text">
									<?php esc_html_e( 'All', 'xts-theme' ); ?>
								</span>
							<?php endif; ?>
						</a>
					</li>

					<?php foreach ( $categories as $key => $category ) : ?>
						<?php
						$link_classes = '';
						$current_tax  = get_queried_object();

						if ( 'masonry' === $type ) {
							$link_url = '#';
						} else {
							$link_url = get_term_link( $category->term_id );

							if ( is_tax( 'xts-portfolio-cat' ) && $category->term_id === $current_tax->term_id ) {
								$link_classes .= ' xts-active';
							}
						}

						?>

						<li data-filter="xts-portfolio-cat-<?php echo esc_attr( $category->slug ); ?>" class="<?php echo esc_attr( $link_classes ); ?>">
							<a href="<?php echo esc_url( $link_url ); ?>" class="xts-nav-link">
								<?php if ( 'underline-2' === $menu_style ) : ?>
									<span class="xts-nav-text">
										<span>
											<?php echo esc_html( $category->name ); ?>
										</span>
									</span>
								<?php else : ?>
									<span class="xts-nav-text">
										<?php echo esc_html( $category->name ); ?>
									</span>
								<?php endif; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php
	}
}

if ( ! function_exists( 'xts_meta_portfolio_categories' ) ) {
	/**
	 * Prints HTML with meta information for the categories.
	 *
	 * @since 1.0.0
	 */
	function xts_meta_portfolio_categories() {
		$categories = wp_get_post_terms( get_the_ID(), 'xts-portfolio-cat' );

		?>
			<?php if ( $categories ) : ?>
				<ul class="xts-project-categories">
					<?php foreach ( $categories as $category ) : ?>
						<?php
						$styles  = apply_filters( 'xts_categories_label_styles', '', $category->term_id );
						$classes = apply_filters( 'xts_categories_label_classes', '', $category->term_id );

						if ( $styles ) {
							$styles = 'style="' . esc_attr( $styles ) . '"';
						}

						if ( $classes ) {
							$classes = 'class="' . esc_attr( $classes ) . '"';
						}

						?>

						<li <?php echo wp_kses( $styles, true ); ?> <?php echo wp_kses( $classes, true ); ?>>
							<?php echo esc_html( $category->name ); ?>
						</li>

					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		<?php
	}
}
