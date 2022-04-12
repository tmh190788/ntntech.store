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

$max_num_pages     = $wp_query->max_num_pages;
$is_ajax           = xts_is_ajax();
$distortion_effect = xts_get_opt( 'portfolio_distortion_effect' );
$image_size        = xts_get_opt( 'portfolio_image_size' );
$image_size_custom = xts_get_opt( 'portfolio_image_size_custom' );
$filters_type      = xts_get_opt( 'portfolio_filters_type' );
$design            = xts_get_opt( 'portfolio_design' );
$wrapper_classes   = xts_get_content_classes();

xts_set_loop_prop( 'portfolio_distortion_effect', $distortion_effect );
xts_set_loop_prop( 'portfolio_design', $design );
xts_set_loop_prop( 'portfolio_image_size', $image_size );
xts_set_loop_prop( 'portfolio_image_size_custom', $image_size_custom );

if ( xts_get_opt( 'ajax_portfolio' ) ) {
	$wrapper_classes .= ' xts-ajax-content';
}

xts_enqueue_js_library( 'photoswipe-bundle' );
xts_enqueue_js_script( 'portfolio-photoswipe' );

?>

<div class="xts-content-area<?php echo esc_attr( $wrapper_classes ); ?>">
	<?php if ( 'without' !== $filters_type && ( ( 'links' === $filters_type && is_tax() ) || ! is_tax() ) ) : ?>
		<?php xts_portfolio_filters( '', $filters_type ); ?>
	<?php endif ?>

	<?php xts_get_portfolio_main_loop(); ?>
</div>
