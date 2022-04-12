<?php
/**
 * Portfolio function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\AJAX_Response;
use XTS\Framework\Modules;

if ( ! function_exists( 'xts_portfolio_template' ) ) {
	/**
	 * Portfolio template.
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @return array|false|string|void
	 */
	function xts_portfolio_template( $element_args ) {
		global $index;

		$default_args = array(
			// General.
			'items_per_page'               => array( 'size' => 6 ),
			'taxonomies'                   => '',
			'include'                      => '',

			// Query.
			'orderby'                      => 'id',
			'order'                        => 'desc',
			'offset'                       => '',
			'exclude'                      => '',
			'meta_key'                     => '', // phpcs:ignore

			// General.
			'design'                       => 'inherit',
			'image_size'                   => 'medium',
			'image_size_custom'            => '',
			'distortion_effect'            => '0',

			// Layout.
			'pagination'                   => 'without',
			'filters_type'                 => 'masonry',
			'view'                         => 'grid',
			'columns'                      => array( 'size' => 3 ),
			'columns_tablet'               => array( 'size' => '' ),
			'columns_mobile'               => array( 'size' => '' ),
			'spacing'                      => xts_get_default_value( 'items_gap' ),
			'masonry'                      => '1',
			'different_images'             => '0',
			'different_images_position'    => '2,5,8,9',

			// Carousel.
			'carousel_items'               => array( 'size' => 3 ),
			'carousel_items_tablet'        => array( 'size' => '' ),
			'carousel_items_mobile'        => array( 'size' => '' ),
			'carousel_spacing'             => xts_get_default_value( 'items_gap' ),

			// Related posts.
			'related_post_ids'             => '',

			// Extra.
			'ajax_page'                    => '',
			'animation_in_view'            => '0',
			'xts_animation_items'          => 'short-in-up',
			'xts_animation_duration_items' => 'fast',
			'xts_animation_delay_items'    => '100',
			'loop'                         => 0,
			'lazy_loading'                 => 'no',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		xts_enqueue_js_library( 'photoswipe-bundle' );
		xts_enqueue_js_script( 'portfolio-photoswipe' );

		$paged                     = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$encoded_atts              = wp_json_encode( array_intersect_key( $element_args, $default_args ) );
		$is_ajax                   = xts_is_ajax();
		$different_images_position = explode( ',', $element_args['different_images_position'] );
		$wrapper_classes           = '';
		$carousel_attrs            = '';
		$uniqid                    = uniqid();

		if ( 'inherit' === $element_args['design'] ) {
			$element_args['design']            = xts_get_opt( 'portfolio_design' );
			$element_args['distortion_effect'] = xts_get_opt( 'portfolio_distortion_effect' );
		}

		xts_set_loop_prop( 'portfolio_distortion_effect', $element_args['distortion_effect'] );
		xts_set_loop_prop( 'portfolio_design', $element_args['design'] );
		xts_set_loop_prop( 'portfolio_image_size', $element_args['image_size'] );
		xts_set_loop_prop( 'portfolio_image_size_custom', $element_args['image_size_custom'] );

		if ( $element_args['ajax_page'] > 1 ) {
			$paged = $element_args['ajax_page'];
		}

		// Query.
		$query_args = array(
			'post_type'      => 'xts-portfolio',
			'posts_per_page' => $element_args['items_per_page']['size'],
			'orderby'        => $element_args['orderby'],
			'order'          => $element_args['order'],
			'paged'          => $paged,
		);

		if ( $element_args['meta_key'] ) {
			$query_args['meta_key'] = $element_args['meta_key']; // phpcs:ignore
		}

		if ( $element_args['include'] ) {
			$query_args['post__in'] = $element_args['include'];
		}

		if ( $element_args['exclude'] ) {
			$query_args['post__not_in'] = $element_args['exclude'];
		}

		if ( $element_args['offset'] ) {
			$query_args['offset'] = $element_args['offset'];
		}

		if ( $element_args['taxonomies'] ) {
			$terms = get_terms(
				array(
					'taxonomy'   => 'xts-portfolio-cat',
					'orderby'    => 'name',
					'include'    => $element_args['taxonomies'],
					'hide_empty' => false,
				)
			);

			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$query_args['tax_query'] = array( 'relation' => 'OR' ); // phpcs:ignore
				foreach ( $terms as $key => $term ) {
					$query_args['tax_query'][] = array(
						'taxonomy'         => $term->taxonomy,
						'field'            => 'slug',
						'terms'            => array( $term->slug ),
						'include_children' => true,
						'operator'         => 'IN',
					);
				}
			}
		}

		if ( $element_args['related_post_ids'] ) {
			$query_args['post__not_in'] = array( $element_args['related_post_ids'] );
		}

		$posts = new WP_Query( $query_args );

		if ( ! $posts || ! $posts->have_posts() ) {
			return;
		}

		// Wrapper classes.
		$wrapper_classes .= ' xts-portfolio-design-' . $element_args['design'];
		if ( 'parallax' === $element_args['design'] ) {
			xts_enqueue_js_library( 'parallax' );
			xts_enqueue_js_script( 'parallax-3d' );
		}
		if ( 'carousel' === $element_args['view'] ) {
			$wrapper_classes .= xts_get_carousel_classes( $element_args );
			$wrapper_classes .= xts_get_row_classes( $element_args['carousel_items']['size'], $element_args['carousel_items_tablet']['size'], $element_args['carousel_items_mobile']['size'], $element_args['carousel_spacing'] );

			$carousel_attrs .= xts_get_carousel_atts( $element_args );
		} else {
			$wrapper_classes .= xts_get_row_classes( $element_args['columns']['size'], $element_args['columns_tablet']['size'], $element_args['columns_mobile']['size'], $element_args['spacing'] );
		}
		if ( $element_args['masonry'] || 'masonry' === $element_args['filters_type'] ) {
			wp_enqueue_script( 'imagesloaded' );
			xts_enqueue_js_library( 'isotope-bundle' );
			xts_enqueue_js_script( 'masonry-layout' );
			$wrapper_classes .= ' xts-masonry-layout';
		}
		if ( $element_args['different_images'] ) {
			$wrapper_classes .= ' xts-different-images';
		}
		if ( $element_args['animation_in_view'] ) {
			xts_enqueue_js_script( 'items-animation-in-view' );
			$wrapper_classes .= ' xts-in-view-animation';
		}
		$wrapper_classes .= ' xts-autoplay-animations-off';

		$index = $element_args['loop'];

		// Lazy loading.
		$lazy_module = Modules::get( 'lazy-loading' );
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		}

		?>
		<?php if ( $element_args['related_post_ids'] ) : ?>
			<div class="xts-related-projects">
			<h3 class="xts-title xts-related-title">
				<?php esc_html_e( 'Related projects', 'xts-theme' ); ?>
			</h3>
		<?php endif ?>

		<?php if ( 'without' !== $element_args['filters_type'] && ! $is_ajax && ( ( 'links' === $element_args['filters_type'] && is_tax() ) || ! is_tax() ) && 'carousel' !== $element_args['view'] ) : ?>
			<?php xts_portfolio_filters( $element_args['taxonomies'], $element_args['filters_type'] ); ?>
		<?php endif ?>

		<?php if ( ! $is_ajax ) : ?>
			<div id="<?php echo esc_attr( $uniqid ); ?>" class="xts-portfolio-loop<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo wp_kses( $carousel_attrs, true ); ?> data-source="element" data-paged="1" data-atts="<?php echo esc_attr( $encoded_atts ); ?>" data-animation-delay="<?php echo esc_attr( $element_args['xts_animation_delay_items'] ); ?>">
		<?php endif ?>

		<?php if ( $is_ajax ) : ?>
			<?php ob_start(); ?>
		<?php endif; ?>

		<?php while ( $posts->have_posts() ) : ?>

			<?php $posts->the_post(); ?>

			<?php
			$index ++;

			$column_classes  = '';
			$column_classes .= xts_get_portfolio_categories_classes( get_the_ID() );

			if ( in_array( $index, $different_images_position ) && $element_args['different_images'] ) { // phpcs:ignore
				$column_classes .= ' xts-wide';
			}

			// Animations.
			if ( $element_args['animation_in_view'] && $element_args['xts_animation_items'] ) {
				$column_classes .= ' xts-animation-' . $element_args['xts_animation_items'];
				$column_classes .= ' xts-animation-' . $element_args['xts_animation_duration_items'];
			}

			?>

			<div class="xts-col<?php echo esc_attr( $column_classes ); ?>" data-loop="<?php echo esc_attr( $index ); ?>">
				<?php xts_get_template_part( 'templates/portfolio-' . $element_args['design'] ); ?>
			</div>

		<?php endwhile; ?>

		<?php if ( $is_ajax ) : ?>
			<?php $output = ob_get_clean(); ?>
		<?php endif; ?>

		<?php if ( ! $is_ajax ) : ?>
			</div>
		<?php endif ?>

		<?php if ( $element_args['related_post_ids'] ) : ?>
			</div>
		<?php endif ?>

		<?php if ( $posts->max_num_pages > 1 && ! $is_ajax && 'carousel' !== $element_args['view'] ) : ?>
			<?php if ( 'load_more' === $element_args['pagination'] || 'infinite' === $element_args['pagination'] ) : ?>
				<?php xts_loadmore_pagination( $element_args['pagination'], 'portfolio', $posts->max_num_pages, $uniqid, 'element' ); ?>
			<?php elseif ( 'links' === $element_args['pagination'] ) : ?>
				<?php xts_posts_pagination( $posts->max_num_pages ); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php

		// Lazy loading.
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init();
		}

		wp_reset_postdata();
		xts_reset_loop();

		if ( $is_ajax ) {
			$output = array(
				'items'  => $output,
				'status' => $posts->max_num_pages > $paged ? 'have-posts' : 'no-more-posts',
			);

			return $output;
		}
	}
}

if ( ! function_exists( 'xts_get_portfolio_element_ajax' ) ) {
	/**
	 * Return projects on AJAX
	 *
	 * @since 1.0.0
	 */
	function xts_get_portfolio_element_ajax() {
		if ( $_POST['atts'] ) { // phpcs:ignore
			$atts              = $_POST['atts']; // phpcs:ignore
			$atts['ajax_page'] = ! $_POST['paged'] ? 2 : (int) $_POST['paged'] + 1; // phpcs:ignore

			AJAX_Response::send_response( xts_portfolio_template( $atts ) );
		}
	}

	add_action( 'wp_ajax_xts_get_portfolio_element', 'xts_get_portfolio_element_ajax' );
	add_action( 'wp_ajax_nopriv_xts_get_portfolio_element', 'xts_get_portfolio_element_ajax' );
}

if ( ! function_exists( 'xts_get_portfolio_categories_classes' ) ) {
	/**
	 * Return portfolio categories classes
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id Post id.
	 *
	 * @return string
	 */
	function xts_get_portfolio_categories_classes( $id ) {
		$post_terms = wp_get_post_terms( $id, 'xts-portfolio-cat' );
		$classes    = '';

		if ( $post_terms ) {
			foreach ( $post_terms as $post_term ) {
				$classes .= ' xts-portfolio-cat-' . $post_term->slug;
			}
		}

		return $classes;
	}
}

