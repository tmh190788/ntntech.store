<?php
/**
 * Recent posts class.
 *
 * @package xts
 */

namespace XTS\Widget;

use WP_Query;
use XTS\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Recent posts widget
 */
class Recent_Posts extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( '[XTemos] Recent posts', 'xts-theme' ),
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your site’s most recent Posts.', 'xts-theme' ),
			'slug'        => 'xts-widget-recent-posts',
			'fields'      => array(
				array(
					'id'      => 'title',
					'type'    => 'text',
					'name'    => esc_html__( 'Title', 'xts-theme' ),
					'default' => 'Recent posts',
				),

				array(
					'id'      => 'order',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Order', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'Descending', 'xts-theme' ) => 'DESC',
						esc_html__( 'Ascending', 'xts-theme' )  => 'ASC',
					),
					'default' => 'DESC',
				),

				array(
					'id'      => 'orderby',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Orderby', 'xts-theme' ),
					'fields'  => array(
						esc_html__( 'ID', 'xts-theme' )    => 'ID',
						esc_html__( 'Author', 'xts-theme' ) => 'author',
						esc_html__( 'Title', 'xts-theme' ) => 'title',
						esc_html__( 'Date', 'xts-theme' )  => 'date',
						esc_html__( 'Modified', 'xts-theme' ) => 'modified',
						esc_html__( 'Random', 'xts-theme' ) => 'rand',
						esc_html__( 'Comment count', 'xts-theme' ) => 'comment_count',
						esc_html__( 'Menu order', 'xts-theme' ) => 'menu_order',
					),
					'default' => 'date',
				),

				array(
					'id'      => 'category',
					'type'    => 'dropdown',
					'name'    => esc_html__( 'Category', 'xts-theme' ),
					'fields'  => $this->get_categories_list(),
					'default' => 'all',
				),

				array(
					'id'      => 'limit',
					'type'    => 'text',
					'name'    => esc_html__( 'Number of posts to show', 'xts-theme' ),
					'default' => 4,
				),

				array(
					'id'   => 'offset',
					'type' => 'text',
					'name' => esc_html__( 'Offset', 'xts-theme' ),
				),

				array(
					'id'      => 'thumb',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Display thumbnail', 'xts-theme' ),
					'default' => true,
				),

				array(
					'id'      => 'thumb_height',
					'type'    => 'text',
					'name'    => esc_html__( 'Thumbnail (height)', 'xts-theme' ),
					'default' => 45,
				),

				array(
					'id'      => 'thumb_width',
					'type'    => 'text',
					'name'    => esc_html__( 'Thumbnail (width)', 'xts-theme' ),
					'default' => 45,
				),

				array(
					'id'      => 'comment_count',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Display comment count', 'xts-theme' ),
					'default' => true,
				),

				array(
					'id'      => 'date',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Display date', 'xts-theme' ),
					'default' => true,
				),
			),
		);

		$this->create_widget( $args );
	}

	/**
	 * Get categories list
	 */
	public function get_categories_list() {
		$categories = array(
			esc_html__( 'All', 'xts-theme' ) => 'all',
		);

		foreach ( get_categories() as $category ) {
			$categories[ $category->name ] = $category->term_id;
		}

		return $categories;
	}

	/**
	 * Output widget.
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		$offset         = isset( $instance['offset'] ) ? $instance['offset'] : 0;
		$posts_per_page = isset( $instance['limit'] ) ? $instance['limit'] : 4;
		$orderby        = isset( $instance['orderby'] ) ? $instance['orderby'] : 'date';
		$category       = isset( $instance['category'] ) ? $instance['category'] : 'all';
		$order          = isset( $instance['order'] ) ? $instance['order'] : 'DESC';
		$thumb_height   = isset( $instance['thumb_height'] ) ? $instance['thumb_height'] : 45;
		$thumb_width    = isset( $instance['thumb_width'] ) ? $instance['thumb_width'] : 45;
		$thumb          = isset( $instance['thumb'] ) ? $instance['thumb'] : true;
		$comment_count  = isset( $instance['comment_count'] ) ? $instance['comment_count'] : true;
		$date           = isset( $instance['date'] ) ? $instance['date'] : true;

		$image_size = ! $thumb_width || ! $thumb_height ? 'thumbnail' : 'custom';

		$query = array(
			'offset'         => $offset,
			'posts_per_page' => $posts_per_page,
			'orderby'        => $orderby,
			'order'          => $order,
		);

		if ( 'all' !== $category ) {
			$query['tax_query'] = array( // phpcs:ignore
				array(
					'taxonomy' => 'category',
					'field'    => 'id',
					'terms'    => $category,
				),
			);
		}

		$posts = new WP_Query( $query );

		echo wp_kses( $args['before_widget'], 'xts_widget' );

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo wp_kses( $args['before_title'], 'xts_widget' ) . $instance['title'] . wp_kses( $args['after_title'], 'xts_widget' ); // phpcs:ignore
		}

		?>
		<?php if ( $posts->have_posts() ) : ?>
			<ul class="xts-recent-list">
				<?php while ( $posts->have_posts() ) : ?>
					<?php $posts->the_post(); ?>

					<li>
						<?php if ( $thumb ) : ?>
							<?php if ( has_post_thumbnail() ) : ?>
								<a class="xts-recent-thumb" href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
									<?php
									echo xts_get_image_html( // phpcs:ignore
										array(
											'image_size' => $image_size,
											'image_custom_dimension' => array(
												'width'  => $thumb_width,
												'height' => $thumb_height,
											),
											'image'      => array(
												'id' => get_post_thumbnail_id(),
											),
										),
										'image'
									);
									?>
								</a>
							<?php endif ?>
						<?php endif ?>

						<div class="xts-recent-content">
							<h5 class="xts-recent-title xts-entities-title">
								<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
									<?php echo esc_html( get_the_title() ); ?>
								</a>
							</h5>

							<?php if ( $date ) : ?>
								<time class="xts-recent-time xts-recent-meta" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
									<?php echo esc_html( get_the_date() ); ?>
								</time>
							<?php endif ?>

							<?php if ( $comment_count ) : ?>
								<?php
								if ( 0 === get_comments_number() ) {
									$comments = esc_html__( 'No Comments', 'xts-theme' );
								} elseif ( get_comments_number() > 1 ) {
									/* translators: %s: comment number */
									$comments = sprintf( esc_html__( '%s Comments', 'xts-theme' ), get_comments_number() );
								} else {
									$comments = esc_html__( '1 Comment', 'xts-theme' );
								}
								?>

								<a class="xts-recent-comment xts-recent-meta" href="<?php echo esc_url( get_comments_link() ); ?>">
									<?php echo esc_html( $comments ); ?>
								</a>
							<?php endif ?>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php endif ?>

		<?php
		wp_reset_postdata();

		echo wp_kses( $args['after_widget'], 'xts_widget' );
	}
}
