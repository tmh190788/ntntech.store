<?php
/**
 * Blog function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\AJAX_Response;
use XTS\Framework\Modules;

if ( ! function_exists( 'xts_blog_template' ) ) {
	/**
	 * Blog template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @return array|false|string|void
	 */
	function xts_blog_template( $element_args ) {
		$default_args = array(
			// General.
			'items_per_page'               => array( 'size' => 6 ),
			'taxonomies'                   => '',
			'include'                      => '',
			'post_format'                  => 'any',

			// Query.
			'orderby'                      => 'id',
			'order'                        => 'desc',
			'offset'                       => '',
			'exclude'                      => '',
			'meta_key'                     => '', // phpcs:ignore
			'query_type'                   => 'OR',

			// General style.
			'image_size'                   => 'medium',
			'image_size_custom'            => '',
			'design'                       => 'inherit',
			'chess_order'                  => '0',
			'black_white'                  => '0',
			'shadow'                       => '0',

			// Layout.
			'view'                         => 'carousel',
			'columns'                      => array( 'size' => 3 ),
			'columns_tablet'               => array( 'size' => '' ),
			'columns_mobile'               => array( 'size' => '' ),
			'spacing'                      => xts_get_default_value( 'items_gap' ),
			'masonry'                      => 'no',
			'different_sizes'              => '0',
			'different_sizes_position'     => '2,5,8,9',
			'pagination'                   => 'without',

			// Carousel.
			'carousel_items'               => array( 'size' => 3 ),
			'carousel_items_tablet'        => array( 'size' => 2 ),
			'carousel_items_mobile'        => array( 'size' => 2 ),
			'carousel_spacing'             => xts_get_default_value( 'items_gap' ),

			// Visibility.
			'title'                        => 'yes',
			'meta'                         => 'yes',
			'text'                         => 'yes',
			'categories'                   => 'yes',
			'blog_excerpt_length'          => '',

			// Related post.
			'related_post_ids'             => '',

			// Extra.
			'ajax_page'                    => '',
			'animation_in_view'            => 'no',
			'xts_animation_items'          => '',
			'xts_animation_duration_items' => 'normal',
			'xts_animation_delay_items'    => '',
			'lazy_loading'                 => 'no',
			'loop'                         => 0,
			'search'                       => '',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$paged                    = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$encoded_atts             = wp_json_encode( array_intersect_key( $element_args, $default_args ) );
		$is_ajax                  = $element_args['search'] ? false : xts_is_ajax();
		$different_sizes_position = explode( ',', $element_args['different_sizes_position'] );
		$wrapper_classes          = '';
		$carousel_attrs           = '';
		$uniqid                   = uniqid();

		if ( $element_args['ajax_page'] > 1 ) {
			$paged = $element_args['ajax_page'];
		}

		// Query.
		$query_args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'paged'               => $paged,
			'posts_per_page'      => $element_args['items_per_page']['size'],
			'orderby'             => $element_args['orderby'],
			'order'               => $element_args['order'],
			'ignore_sticky_posts' => 1,
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

		if ( $element_args['taxonomies'] || 'any' !== $element_args['post_format'] ) {
			$query_args['tax_query'] = array( 'relation' => $element_args['query_type'] ); // phpcs:ignore

			if ( $element_args['taxonomies'] ) {
				$terms = get_terms(
					get_object_taxonomies( 'post' ),
					array(
						'orderby'    => 'name',
						'include'    => $element_args['taxonomies'],
						'hide_empty' => false,
					)
				);

				if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
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

			if ( 'any' !== $element_args['post_format'] ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'post_format',
					'field'    => 'slug',
					'terms'    => array( 'post-format-' . $element_args['post_format'] ),
				);
			}
		}

		if ( $element_args['related_post_ids'] ) {
			$query_args = xts_get_related_posts_args( $element_args['related_post_ids'] );
		}

		if ( $element_args['search'] ) {
			$query_args['s'] = sanitize_text_field( $element_args['search'] );
		}

		$posts = new WP_Query( $query_args );

		if ( ! $posts || ! $posts->have_posts() ) {
			?>
			<?php if ( $element_args['search'] ) : ?>
				<div class="xts-notification xts-color-info">
					<?php esc_html_e( 'No posts were found matching your selection.', 'xts-theme' ); ?>
				</div>
			<?php endif; ?>
			<?php
			return;
		}

		if ( 'inherit' === $element_args['design'] ) {
			$element_args['design'] = xts_get_opt( 'blog_design', 'default' );
		}

		xts_set_loop_prop( 'blog_post_title', $element_args['title'] );
		xts_set_loop_prop( 'blog_post_meta', $element_args['meta'] );
		xts_set_loop_prop( 'blog_post_text', $element_args['text'] );
		xts_set_loop_prop( 'blog_post_categories', $element_args['categories'] );
		xts_set_loop_prop( 'blog_image_size', $element_args['image_size'] );
		xts_set_loop_prop( 'blog_image_size_custom', $element_args['image_size_custom'] );
		xts_set_loop_prop( 'blog_design', $element_args['design'] );
		xts_set_loop_prop( 'blog_post_black_white', $element_args['black_white'] );
		xts_set_loop_prop( 'blog_post_shadow', $element_args['shadow'] );
		if ( $element_args['blog_excerpt_length'] ) {
			xts_set_loop_prop( 'blog_excerpt_length', $element_args['blog_excerpt_length'] );
		}

		// Classes.
		if ( 'carousel' === $element_args['view'] ) {
			$wrapper_classes .= xts_get_carousel_classes( $element_args );
			$wrapper_classes .= xts_get_row_classes( $element_args['carousel_items']['size'], $element_args['carousel_items_tablet']['size'], $element_args['carousel_items_mobile']['size'], $element_args['carousel_spacing'] );
			$carousel_attrs  .= xts_get_carousel_atts( $element_args );
			if ( 1 === $element_args['carousel_items']['size'] ) {
				$wrapper_classes .= ' xts-blog-one-column';
			}
		} else {
			$wrapper_classes .= xts_get_row_classes( $element_args['columns']['size'], $element_args['columns_tablet']['size'], $element_args['columns_mobile']['size'], $element_args['spacing'] );
			if ( 1 === $element_args['columns']['size'] ) {
				$wrapper_classes .= ' xts-blog-one-column';
			}
		}
		if ( 'yes' === $element_args['masonry'] ) {
			wp_enqueue_script( 'imagesloaded' );
			xts_enqueue_js_library( 'isotope-bundle' );
			xts_enqueue_js_script( 'masonry-layout' );
			$wrapper_classes .= ' xts-masonry-layout';
		}
		if ( $element_args['different_sizes'] ) {
			$wrapper_classes .= ' xts-different-sizes';
		}
		if ( 'yes' === $element_args['animation_in_view'] ) {
			xts_enqueue_js_script( 'items-animation-in-view' );
			$wrapper_classes .= ' xts-in-view-animation';
		}
		if ( 'side' === xts_get_loop_prop( 'blog_design' ) && $element_args['chess_order'] ) {
			$wrapper_classes .= ' xts-post-order-chess';
		}
		if ( $element_args['black_white'] ) {
			$wrapper_classes .= ' xts-post-black-white';
		}
		if ( $element_args['shadow'] ) {
			$wrapper_classes .= ' xts-with-shadow';
		}
		$wrapper_classes .= ' xts-autoplay-animations-off';
		$wrapper_classes .= ' xts-post-design-' . $element_args['design'];

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
			<div class="xts-related-posts">
			<h3 class="xts-title xts-related-title">
				<?php esc_html_e( 'Related posts', 'xts-theme' ); ?>
			</h3>
		<?php endif ?>

		<?php if ( ! $is_ajax ) : ?>
		<div id="<?php echo esc_attr( $uniqid ); ?>" class="xts-blog<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo wp_kses( $carousel_attrs, true ); ?> data-source="element" data-paged="1" data-atts="<?php echo esc_attr( $encoded_atts ); ?>" data-animation-delay="<?php echo esc_attr( $element_args['xts_animation_delay_items'] ); ?>">
	<?php endif ?>

		<?php
		if ( $is_ajax ) {
			ob_start();
		}
		?>

		<?php while ( $posts->have_posts() ) : ?>
			<?php $posts->the_post(); ?>

			<?php
			$index ++;

			$column_classes = '';
			$post_format    = get_post_format();
			$design         = xts_get_loop_prop( 'blog_design' );

			if ( in_array( $index, $different_sizes_position ) && $element_args['different_sizes'] ) { // phpcs:ignore
				$column_classes .= ' xts-wide';
			}

			// Animations.
			if ( 'yes' === $element_args['animation_in_view'] && $element_args['xts_animation_items'] ) {
				$column_classes .= ' xts-animation-' . $element_args['xts_animation_items'];
				$column_classes .= ' xts-animation-' . $element_args['xts_animation_duration_items'];
			}

			if ( ( 'link' === $post_format || 'quote' === $post_format || 'image' === $post_format ) && xts_get_opt( 'blog_theme_post_formats', '0' ) ) {
				$design = 'format-' . $post_format;
			}

			?>

			<div class="xts-col<?php echo esc_attr( $column_classes ); ?>" data-loop="<?php echo esc_attr( $index ); ?>">
				<?php xts_get_template_part( 'templates/content-' . $design ); ?>
			</div>
		<?php endwhile; ?>

		<?php
		if ( $is_ajax ) {
			$output = ob_get_clean();
		}
		?>

		<?php if ( ! $is_ajax ) : ?>
		</div>
	<?php endif ?>

		<?php if ( $element_args['related_post_ids'] ) : ?>
			</div>
		<?php endif ?>

		<?php if ( $posts->max_num_pages > 1 && ! $is_ajax && 'carousel' !== $element_args['view'] ) : ?>
			<?php if ( 'load_more' === $element_args['pagination'] || 'infinite' === $element_args['pagination'] ) : ?>
				<?php xts_loadmore_pagination( $element_args['pagination'], 'blog', $posts->max_num_pages, $uniqid, 'element' ); ?>
			<?php elseif ( 'links' === $element_args['pagination'] ) : ?>
				<?php xts_posts_pagination( $posts->max_num_pages ); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php

		wp_reset_postdata();
		xts_reset_loop();

		// Lazy loading.
		if ( 'yes' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_disable( true );
		} elseif ( 'no' === $element_args['lazy_loading'] ) {
			$lazy_module->lazy_init();
		}

		if ( $is_ajax ) {
			$output = array(
				'items'  => $output,
				'status' => $posts->max_num_pages > $paged ? 'have-posts' : 'no-more-posts',
			);

			return $output;
		}
	}
}

if ( ! function_exists( 'xts_get_related_posts_args' ) ) {
	/**
	 * Return related posts args array
	 *
	 * @since 1.0.0
	 *
	 * @param integer $post_id Post id.
	 *
	 * @return array
	 */
	function xts_get_related_posts_args( $post_id ) {
		$tags = wp_get_post_tags( $post_id );
		$args = array();

		if ( $tags ) {
			$tag_ids = array();

			foreach ( $tags as $individual_tag ) {
				$tag_ids[] = $individual_tag->term_id;
			}

			$args = array(
				'tag__in'             => $tag_ids,
				'post__not_in'        => array( $post_id ),
				'posts_per_page'      => apply_filters( 'xts_related_posts_per_page', 8 ),
				'ignore_sticky_posts' => 1,
			);
		}

		return $args;
	}
}

if ( ! function_exists( 'xts_get_blog_element_ajax' ) ) {
	/**
	 * Return posts on AJAX
	 *
	 * @since 1.0.0
	 */
	function xts_get_blog_element_ajax() {
		if ( $_POST['atts'] ) { // phpcs:ignore
			$atts              = $_POST['atts']; // phpcs:ignore
			$atts['ajax_page'] = ! $_POST['paged'] ? 2 : (int) $_POST['paged'] + 1; // phpcs:ignore

			AJAX_Response::send_response( xts_blog_template( $atts ) );
		}
	}

	add_action( 'wp_ajax_xts_get_blog_element', 'xts_get_blog_element_ajax' );
	add_action( 'wp_ajax_nopriv_xts_get_blog_element', 'xts_get_blog_element_ajax' );
}
