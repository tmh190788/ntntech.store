<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package xts
 */

?>
<div class="xts-content-area<?php echo esc_attr( xts_get_content_classes() ); ?>"> 
	<article id="post-0" class="post xts-no-results-page">
		<h2 class="xts-no-results-title"><?php esc_html_e( 'Nothing found', 'xts-theme' ); ?></h2>
		<div class="xts-no-results-content">
			<p class="xts-fontsize-larger"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'xts-theme' ); ?></p>
			<?php
			xts_search_form(
				array(
					'ajax'      => false,
					'post_type' => xts_is_portfolio_archive() ? 'xts-portfolio' : 'post',
				)
			);
			?>
		</div>
	</article>
</div> 

