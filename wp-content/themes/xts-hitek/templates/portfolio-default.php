<?php
/**
 * Template used to display portfolio content.
 *
 * @package xts
 */

global $index;

$post_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' );

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( xts_get_portfolio_post_classes() ); ?>>
	<a href="<?php echo esc_url( get_permalink() ); ?>" class="xts-project-link xts-fill" rel="bookmark"></a>
	<div class="xts-project-actions">
		<div class="xts-project-photoswipe xts-action-btn xts-style-icon">
			<a href="<?php echo esc_url( $post_image_url[0] ); ?>" data-index="<?php echo esc_attr( $index - 1 ); ?>" data-width="<?php echo esc_attr( $post_image_url[1] ); ?>" data-height="<?php echo esc_attr( $post_image_url[2] ); ?>" class="xts-project-photoswipe" data-elementor-open-lightbox="no"></a>
		</div>

		<?php if ( xts_is_social_buttons_enable( 'share' ) ) : ?>
			<div class="xts-project-social xts-action-btn xts-style-icon xts-tooltip-init">
				<a></a>

				<div class="<?php echo esc_attr( xts_get_rtl_inverted_string( 'xts-tooltip tooltip bs-tooltip-left' ) ); ?>">
					<div class="arrow"></div>
					<div class="tooltip-inner">
						<?php xts_social_buttons_template( xts_get_default_value( 'portfolio_social_buttons_args' ) ); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<div class="xts-project-thumb">
		<div class="xts-project-image">
			<?php
			echo xts_get_image_html( // phpcs:ignore
				array(
					'image_size'             => xts_get_loop_prop( 'portfolio_image_size' ),
					'image_custom_dimension' => xts_get_loop_prop( 'portfolio_image_size_custom' ),
					'image'                  => array(
						'id' => get_post_thumbnail_id(),
					),
				),
				'image'
			);
			?>
		</div>

		<div class="xts-project-overlay xts-fill"></div>
	</div>

	<div class="xts-project-content xts-fill xts-scheme-light">
		<?php xts_meta_portfolio_categories(); ?>

		<h3 class="xts-project-title xts-entities-title">
			<?php the_title(); ?>
		</h3>
	</div>
</article>
