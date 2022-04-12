<?php
/**
 * The loop template file.
 *
 * Included on pages like index.php, archive.php and search.php to display a loop of posts
 * Learn more: https://codex.wordpress.org/The_Loop
 *
 * @package xts
 */

global $paged, $wp_query;

$max_page                 = $wp_query->max_num_pages;
$is_ajax                  = xts_is_ajax();
$columns                  = xts_get_opt( 'blog_columns' );
$columns_tablet           = xts_get_opt( 'blog_columns_tablet' );
$columns_mobile           = xts_get_opt( 'blog_columns_mobile' );
$spacing                  = xts_get_opt( 'blog_spacing' );
$pagination               = xts_get_opt( 'blog_pagination' );
$masonry                  = xts_get_opt( 'blog_masonry' );
$different_sizes          = xts_get_opt( 'blog_different_sizes' );
$animation_in_view        = xts_get_opt( 'blog_animation_in_view' );
$animation                = xts_get_opt( 'blog_animation' );
$animation_duration       = xts_get_opt( 'blog_animation_duration' );
$animation_delay          = xts_get_opt( 'blog_animation_delay' );
$chess_order              = xts_get_opt( 'blog_post_chess_order' );
$black_white              = xts_get_opt( 'blog_post_black_white' );
$shadow                   = xts_get_opt( 'blog_post_shadow' );
$different_sizes_position = explode( ',', xts_get_opt( 'blog_different_sizes_position' ) );
$wrapper_classes          = '';

if ( is_search() ) {
	$pagination = 'links';
}

// Wrapper classes.
$wrapper_classes .= xts_get_row_classes( $columns, $columns_tablet, $columns_mobile, $spacing );
$wrapper_classes .= ' xts-post-design-' . xts_get_opt( 'blog_design', 'default' );
if ( 1 === (int) $columns ) {
	$wrapper_classes .= ' xts-blog-one-column';
}
if ( $masonry ) {
	wp_enqueue_script( 'imagesloaded' );
	xts_enqueue_js_library( 'isotope-bundle' );
	xts_enqueue_js_script( 'masonry-layout' );
	$wrapper_classes .= ' xts-masonry-layout';
}
if ( $different_sizes ) {
	$wrapper_classes .= ' xts-different-sizes';
}
if ( $animation_in_view ) {
	xts_enqueue_js_script( 'items-animation-in-view' );
	$wrapper_classes .= ' xts-in-view-animation';
}
if ( 'side' === xts_get_opt( 'blog_design', 'default' ) && $chess_order ) {
	$wrapper_classes .= ' xts-post-order-chess';
}
if ( $black_white ) {
	$wrapper_classes .= ' xts-post-black-white';
}
if ( $shadow ) {
	$wrapper_classes .= ' xts-with-shadow';
}

if ( ! $paged ) {
	$paged = 1; // phpcs:ignore
}

if ( $is_ajax && isset( $_GET['loop'] ) ) { // phpcs:ignore
	xts_set_loop_prop( 'blog_loop', (int) sanitize_text_field( $_GET['loop'] ) ); // phpcs:ignore
}

do_action( 'xts_before_loop' );

?>
<?php if ( ! $is_ajax ) : ?>
	<div class="xts-content-area<?php echo esc_attr( xts_get_content_classes() ); ?>">
<?php endif ?>

	<?php if ( ! $is_ajax ) : ?>
		<?php if ( is_author() && get_the_author_meta( 'description' ) ) : ?>
			<?php xts_author_bio(); ?>
		<?php endif; ?>
	<?php endif; ?>

		<?php if ( ! $is_ajax ) : ?>
			<div id="blog" class="xts-blog<?php echo esc_attr( $wrapper_classes ); ?>" data-source="main_loop" data-paged="1" data-animation-delay="<?php echo esc_attr( $animation_delay ); ?>">
		<?php endif ?>

			<?php if ( $is_ajax ) : ?>
				<?php ob_start(); ?>
			<?php endif; ?>

			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>

				<?php
				// Increase loop count.
				xts_set_loop_prop( 'blog_loop', xts_get_loop_prop( 'blog_loop' ) + 1 );

				$index = xts_get_loop_prop( 'blog_loop' );

				$column_classes = '';
				$post_format    = get_post_format();
				$design         = xts_get_opt( 'blog_design', 'default' );

				if ( in_array( $index, $different_sizes_position ) && $different_sizes ) { // phpcs:ignore
					$column_classes .= ' xts-wide';
				}

				// Animations.
				if ( $animation && $animation_in_view ) {
					$column_classes .= ' xts-animation-' . $animation;
					$column_classes .= ' xts-animation-' . $animation_duration;
				}

				if ( ( 'link' === $post_format || 'quote' === $post_format || 'image' === $post_format ) && xts_get_opt( 'blog_theme_post_formats', '0' ) ) {
					$design = 'format-' . $post_format;
				}

				?>

				<div class="xts-col<?php echo esc_attr( $column_classes ); ?>" data-loop="<?php echo esc_attr( $index ); ?>">
					<?php xts_get_template_part( 'templates/content-' . $design ); ?>
				</div>
			<?php endwhile; ?>

			<?php if ( $is_ajax ) : ?>
				<?php $output = ob_get_clean(); ?>
			<?php endif; ?>

		<?php if ( ! $is_ajax ) : ?>
			</div>
		<?php endif ?>

	<?php if ( $max_page > 1 && ! $is_ajax ) : ?>
		<?php if ( 'links' !== $pagination ) : ?>
			<?php xts_loadmore_pagination( $pagination, 'blog', $max_page, 'blog' ); ?>
		<?php else : ?>
			<?php xts_posts_pagination(); ?>
		<?php endif; ?>
	<?php endif ?>

<?php if ( ! $is_ajax ) : ?>
	</div>
<?php endif ?>

<?php

if ( $is_ajax ) {
	$output = array(
		'items'       => $output,
		'status'      => $max_page > $paged ? 'have-posts' : 'no-more-posts',
		'nextPage'    => add_query_arg( 'xts_ajax', '1', next_posts( $max_page, false ) ),
		'currentPage' => strtok( xts_get_current_url(), '?' ),
	);

	echo wp_json_encode( $output );
}

do_action( 'xts_after_loop' );
