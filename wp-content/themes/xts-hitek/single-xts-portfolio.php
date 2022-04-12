<?php
/**
 * The template for displaying single portfolio.
 *
 * @package xts
 */

get_header();

xts_get_sidebar( 'sidebar-left' );

?> 
	<div class="xts-content-area<?php echo esc_attr( xts_get_content_classes() ); ?>">
		<?php while ( have_posts() ) : ?>

			<?php the_post(); ?>

			<?php xts_get_template_part( 'templates/portfolio-single' ); ?>

			<?php if ( xts_get_opt( 'portfolio_single_navigation' ) ) : ?>
				<?php xts_get_template_part( 'templates/single-posts-navigation' ); ?>
			<?php endif; ?>

			<?php if ( xts_get_opt( 'portfolio_single_related' ) ) : ?>
				<?php
				xts_portfolio_template(
					array(
						'image_size'            => xts_get_loop_prop( 'portfolio_image_size' ),
						'image_size_custom'     => xts_get_loop_prop( 'portfolio_image_size_custom' ),
						'items_per_page'        => array( 'size' => xts_get_opt( 'portfolio_single_related_projects_count' ) ),
						'carousel_items'        => array( 'size' => xts_get_opt( 'portfolio_single_related_projects_per_row' ) ),
						'carousel_items_tablet' => array( 'size' => 2 ),
						'carousel_items_mobile' => array( 'size' => 1 ),
						'different_images'      => 0,
						'view'                  => 'carousel',
						'related_post_ids'      => $post->ID,
						'filters_type'          => 'without',
						'carousel_spacing'      => xts_get_opt( 'portfolio_spacing' ),
					)
				);
				?>
			<?php endif; ?>

		<?php endwhile; ?>
	</div>
<?php

xts_get_sidebar( 'sidebar-right' );

get_footer();
