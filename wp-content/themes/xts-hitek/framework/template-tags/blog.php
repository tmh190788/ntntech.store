<?php
/**
 * Blog templates functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_post_thumbnail' ) ) {
	/**
	 * Display a picture of the single post, depending on the format.
	 *
	 * @since 1.0.0
	 */
	function xts_single_post_thumbnail() {
		$post_format        = get_post_format();
		$post_id            = get_the_ID();
		$blog_single_design = xts_get_opt( 'blog_single_design' );
		$audio_url          = get_post_meta( $post_id, '_xts_post_audio_url', true );
		$link               = get_post_meta( $post_id, '_xts_post_link', true );
		$quote              = get_post_meta( $post_id, '_xts_post_quote', true );
		$gallery            = get_post_meta( $post_id, '_xts_post_gallery', true );

		?>
		<?php if ( 'quote' === $post_format && 'page-title' !== $blog_single_design && $quote ) : ?>
			<?php xts_post_quote_template( true ); ?>
		<?php elseif ( 'link' === $post_format && 'page-title' !== $blog_single_design && $link ) : ?>
			<?php xts_post_link_template( true, true ); ?>
		<?php elseif ( 'gallery' === $post_format && $gallery ) : ?>
			<?php
			xts_post_gallery_template(
				array(
					'auto_height'                => 'yes',
					'arrows'                     => 'yes',
					'dots'                       => 'no',
					'arrows_color_scheme'        => 'light',
					'arrows_horizontal_position' => 'inside',
					'image_size'                 => xts_get_opt( 'blog_single_image_size' ),
					'image_size_custom'          => xts_get_opt( 'blog_single_image_size_custom' ),
					'with_links'                 => 'no',
					'carousel_items'             => array( 'size' => 1 ),
					'carousel_items_tablet'      => array( 'size' => 1 ),
					'carousel_items_mobile'      => array( 'size' => 1 ),
					'carousel_spacing'           => 0,
				)
			);
			?>
		<?php elseif ( 'video' === $post_format && xts_post_have_video( $post_id ) ) : ?>
			<?php xts_single_post_video_template(); ?>
		<?php elseif ( 'audio' === $post_format && $audio_url ) : ?>
			<?php xts_post_audio_template(); ?>
		<?php elseif ( has_post_thumbnail() && 'page-title' !== $blog_single_design ) : ?>
			<?php
			echo xts_get_image_html( // phpcs:ignore
				array(
					'image_size'             => xts_get_opt( 'blog_single_image_size' ),
					'image_custom_dimension' => xts_get_opt( 'blog_single_image_size_custom' ),
					'image'                  => array(
						'id' => get_post_thumbnail_id(),
					),
				),
				'image'
			);
			?>
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'xts_post_thumbnail' ) ) {
	/**
	 * Display a picture of the post, depending on the format
	 *
	 * @since 1.0.0
	 *
	 * @param array $supports Supports formats.
	 */
	function xts_post_thumbnail( $supports = array() ) {
		$post_format = get_post_format();
		$post_id     = get_the_ID();
		$audio_url   = get_post_meta( $post_id, '_xts_post_audio_url', true );
		$gallery     = get_post_meta( $post_id, '_xts_post_gallery', true );

		?>
		<?php if ( 'video' === $post_format && in_array( 'video', $supports ) && xts_post_have_video( $post_id ) ) : // phpcs:ignore ?>
			<?php xts_post_video_template(); ?>
		<?php elseif ( 'audio' === $post_format && in_array( 'audio', $supports ) && $audio_url && ! has_post_thumbnail() ) : // phpcs:ignore ?>
			<?php xts_post_audio_template(); ?>
		<?php elseif ( 'gallery' === $post_format && in_array( 'gallery', $supports ) && $gallery ) : // phpcs:ignore ?>
			<?php
			xts_post_gallery_template(
				array(
					'auto_height'           => 'yes',
					'arrows'                => 'no',
					'dots'                  => 'no',
					'draggable'             => 'no',
					'image_size'            => xts_get_loop_prop( 'blog_image_size' ),
					'image_size_custom'     => xts_get_loop_prop( 'blog_image_size_custom' ),
					'with_links'            => 'yes',
					'carousel_items'        => array( 'size' => 1 ),
					'carousel_items_tablet' => array( 'size' => 1 ),
					'carousel_items_mobile' => array( 'size' => 1 ),
					'carousel_spacing'      => 0,
				)
			);
			?>
		<?php elseif ( has_post_thumbnail() ) : ?>
			<div class="xts-post-image">
				<?php
				echo xts_get_image_html( // phpcs:ignore
					array(
						'image_size'             => xts_get_loop_prop( 'blog_image_size' ),
						'image_custom_dimension' => xts_get_loop_prop( 'blog_image_size_custom' ),
						'image'                  => array(
							'id' => get_post_thumbnail_id(),
						),
					),
					'image'
				);
				?>
			</div>
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'xts_post_audio_template' ) ) {
	/**
	 * Post audio template
	 *
	 * @since 1.0.0
	 */
	function xts_post_audio_template() {
		$primary_color = xts_get_opt( 'primary_color' );
		$audio_url     = get_post_meta( get_the_ID(), '_xts_post_audio_url', true );

		if ( ! $audio_url ) {
			return;
		}

		?>
		<iframe class="xts-post-audio-soundcloud" width="100%" height="170" allow="autoplay" src="https://w.soundcloud.com/player/?url=<?php echo esc_attr( $audio_url ); ?>&show_artwork=false&buaudio_urlse&sharing=false&download=false&show_playcount=false&color=<?php echo esc_attr( str_replace( '#', '', $primary_color['idle'] ) ); ?>"></iframe>
		<?php
	}
}

if ( ! function_exists( 'xts_single_post_video_template' ) ) {
	/**
	 * Single post video template
	 *
	 * @since 1.0.0
	 */
	function xts_single_post_video_template() {
		$post_id       = get_the_ID();
		$video_mp4     = get_post_meta( $post_id, '_xts_post_video_mp4', true );
		$video_webm    = get_post_meta( $post_id, '_xts_post_video_webm', true );
		$video_ogg     = get_post_meta( $post_id, '_xts_post_video_ogg', true );
		$video_youtube = get_post_meta( $post_id, '_xts_post_video_youtube', true );
		$video_vimeo   = get_post_meta( $post_id, '_xts_post_video_vimeo', true );
		$primary_color = xts_get_opt( 'primary_color' );

		xts_enqueue_js_script( 'video-element' );

		?>
		<?php if ( ( isset( $video_mp4['id'] ) && $video_mp4['id'] ) || ( isset( $video_webm['id'] ) && $video_webm['id'] ) || ( isset( $video_ogg['id'] ) && $video_ogg['id'] ) && ! $video_youtube ) : ?>
			<div class="xts-post-video xts-post-video-html5 xts-ar-16-9">
				<?php
				echo xts_get_hosted_video( // phpcs:ignore
					array(
						'video_hosted_url'    => array(
							'url' => wp_get_attachment_url( $video_mp4['id'] ),
						),
						'video_loop'          => 'no',
						'video_mute'          => 'yes',
						'video_controls'      => 'yes',
						'video_autoplay'      => 'no',
						'video_action_button' => 'without',
					)
				);
				?>
			</div>
		<?php elseif ( $video_youtube ) : ?>
			<div class="xts-post-video xts-post-video-youtube xts-ar-16-9">
				<?php if ( xts_is_elementor_installed() ) : ?>
					<?php
					echo Elementor\Embed::get_embed_html( // phpcs:ignore
						$video_youtube,
						array(
							'rel' => 0,
						),
						array(
							'lazy_load' => 1,
						),
						array(
							'width'  => '100%',
							'height' => '100%',
						)
					);
					?>
				<?php endif; ?>
			</div>
		<?php elseif ( $video_vimeo ) : ?>
			<div class="xts-post-video xts-post-video-vimeo xts-ar-16-9">
				<?php if ( xts_is_elementor_installed() ) : ?>
					<?php
					echo Elementor\Embed::get_embed_html( // phpcs:ignore
						$video_vimeo,
						array(
							'color' => str_replace( '#', '', $primary_color['idle'] ),
						),
						array(
							'lazy_load' => 1,
						),
						array(
							'width'  => '100%',
							'height' => '100%',
						)
					);
					?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'xts_post_video_template' ) ) {
	/**
	 * Post video template
	 *
	 * @since 1.0.0
	 */
	function xts_post_video_template() {
		$post_id = get_the_ID();

		if ( ! xts_post_have_video( $post_id ) ) {
			return;
		}

		$video_mp4     = get_post_meta( $post_id, '_xts_post_video_mp4', true );
		$video_webm    = get_post_meta( $post_id, '_xts_post_video_webm', true );
		$video_ogg     = get_post_meta( $post_id, '_xts_post_video_ogg', true );
		$video_youtube = get_post_meta( $post_id, '_xts_post_video_youtube', true );
		$video_vimeo   = get_post_meta( $post_id, '_xts_post_video_vimeo', true );
		$aspect_ratio  = get_post_meta( $post_id, '_xts_post_video_aspect_ratio', true ) ? get_post_meta( $post_id, '_xts_post_video_aspect_ratio', true ) : '16-9';

		$video_wrapper_classes  = '';
		$video_wrapper_classes .= ' xts-ar-' . $aspect_ratio

		?>
		<div class="xts-post-image xts-with-bg xts-fill" style="background-image:url(<?php echo esc_url( get_the_post_thumbnail_url() ); ?>);"></div>

		<?php if ( ( isset( $video_mp4['id'] ) && $video_mp4['id'] ) || ( isset( $video_webm['id'] ) && $video_webm['id'] ) || ( isset( $video_ogg['id'] ) && $video_ogg['id'] ) ) : ?>
			<div class="xts-post-video xts-post-video-html5<?php echo esc_attr( $video_wrapper_classes ); ?>">
				<?php
				echo xts_get_hosted_video( // phpcs:ignore
					array(
						'video_hosted_url'    => array(
							'url' => wp_get_attachment_url( $video_mp4['id'] ),
						),
						'video_loop'          => 'yes',
						'video_mute'          => 'yes',
						'video_controls'      => 'no',
						'video_autoplay'      => 'no',
						'video_action_button' => 'without',
					)
				);
				?>
			</div>
		<?php elseif ( $video_youtube ) : ?>
			<div class="xts-post-video xts-post-video-youtube xts-video-resize<?php echo esc_attr( $video_wrapper_classes ); ?>">
				<?php if ( xts_is_elementor_installed() ) : ?>
					<?php
					echo Elementor\Embed::get_embed_html( // phpcs:ignore
						$video_youtube,
						array(
							'enablejsapi' => 1,
							'controls'    => 0,
							'showinfo'    => 0,
							'loop'        => 1,
							'mute'        => 1,
							'rel'         => 0,
						),
						array(
							'lazy_load' => 1,
						),
						array(
							'allow'  => 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture',
							'width'  => '100%',
							'height' => '100%',
						)
					);
					?>
				<?php endif; ?>
			</div>
		<?php elseif ( $video_vimeo ) : ?>
			<div class="xts-post-video xts-post-video-vimeo xts-video-resize<?php echo esc_attr( $video_wrapper_classes ); ?>">
				<?php if ( xts_is_elementor_installed() ) : ?>
					<?php
					echo Elementor\Embed::get_embed_html( // phpcs:ignore
						$video_vimeo,
						array(
							'api'        => 1,
							'muted'      => 1,
							'background' => 1,
							'loop'       => 1,
						),
						array(
							'lazy_load' => 1,
						),
						array(
							'allow'  => 'autoplay',
							'width'  => '100%',
							'height' => '100%',
						)
					);
					?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'xts_post_quote_template' ) ) {
	/**
	 * Post quote template
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $image Is background image needed.
	 */
	function xts_post_quote_template( $image = false ) {
		$post_id = get_the_ID();
		$quote   = get_post_meta( $post_id, '_xts_post_quote', true );
		$cite    = get_post_meta( $post_id, '_xts_post_quote_cite', true );
		$style   = '';

		if ( ! $quote ) {
			return;
		}

		if ( $image && has_post_thumbnail() ) {
			$style = ' style="background-image:url(' . esc_url( get_the_post_thumbnail_url() ) . ');"';
		}

		?>
		<div class="xts-post-quote"<?php echo wp_kses( $style, true ); ?>>
			<div class="xts-post-quote-text">
				<?php echo wp_kses_post( $quote ); ?>
			</div>

			<div class="xts-post-quote-cite">
				<?php echo wp_kses_post( $cite ); ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'xts_post_link_template' ) ) {
	/**
	 * Post link template
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $image        Is background image needed.
	 * @param boolean $overlay_link Overlay link.
	 */
	function xts_post_link_template( $image = false, $overlay_link = false ) {
		$style        = '';
		$target       = '_self';
		$text_classes = '';
		$link         = get_post_meta( get_the_ID(), '_xts_post_link', true );
		$link_target  = get_post_meta( get_the_ID(), '_xts_post_link_blank', true );

		if ( ! $link ) {
			return;
		}

		if ( $image && has_post_thumbnail() ) {
			$style = ' style="background-image:url(' . esc_url( get_the_post_thumbnail_url() ) . ');"';
		}

		if ( $link_target ) {
			$target        = ' _blank';
			$text_classes .= ' xts-target-blank';
		}

		?>
		<div class="xts-post-url"<?php echo wp_kses( $style, true ); ?>>
			<?php if ( $overlay_link ) : ?>
				<a href="<?php echo esc_url( $link ); ?>" class="xts-post-link xts-fill" target="<?php echo esc_attr( $target ); ?>"></a>
			<?php endif; ?>

			<div class="xts-post-url-text<?php echo esc_attr( $text_classes ); ?>">
				<?php echo wp_kses( $link, true ); ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'xts_post_gallery_template' ) ) {
	/**
	 * Post gallery template
	 *
	 * @since 1.0.0
	 *
	 * @param array $carousel_config Carousel config.
	 */
	function xts_post_gallery_template( $carousel_config = array() ) {
		$images_data = get_post_meta( get_the_ID(), '_xts_post_gallery', true );

		if ( ! $images_data ) {
			return;
		}

		$images_id       = explode( ',', $images_data );
		$wrapper_classes = '';
		$carousel_attrs  = '';

		$wrapper_classes .= xts_get_carousel_classes( $carousel_config );
		$wrapper_classes .= xts_get_row_classes( 1, 1, 1, 0 );
		$carousel_attrs  .= xts_get_carousel_atts( $carousel_config );

		?>
		<div class="xts-post-gallery<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo wp_kses( $carousel_attrs, true ); ?>>
			<?php foreach ( $images_id as $image_id ) : ?>
				<?php
				if ( ! $image_id ) {
					continue;
				}

				?>
				<div class="xts-col xts-post-gallery-col">
					<?php if ( 'yes' === $carousel_config['with_links'] ) : ?>
						<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php endif; ?>

					<?php
					echo xts_get_image_html( // phpcs:ignore
						array(
							'image_size'             => $carousel_config['image_size'],
							'image_custom_dimension' => $carousel_config['image_size_custom'],
							'image'                  => array(
								'id' => $image_id,
							),
						),
						'image'
					);
					?>

					<?php if ( $carousel_config['with_links'] ) : ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'xts_loadmore_pagination' ) ) {
	/**
	 * Display load more pagination button
	 *
	 * @since 1.0.0
	 *
	 * @param string  $action    Button action.
	 * @param string  $post_type Post type.
	 * @param integer $max_page  Max blog page count.
	 * @param bool    $uniqid    Unique id.
	 * @param string  $source    Source.
	 */
	function xts_loadmore_pagination( $action, $post_type, $max_page, $uniqid = false, $source = 'loop' ) {
		$classes = '';

		$classes .= ' xts-type-' . $post_type;
		$classes .= ' xts-action-' . $action;

		wp_enqueue_script( 'imagesloaded' );

		xts_enqueue_js_script( $post_type . '-load-more' );

		?>
		<?php if ( $max_page > 1 && ( ( get_next_posts_link() && 'loop' === $source ) || 'element' === $source ) ) : ?>
			<div class="xts-load-more-wrapper">
				<a href="<?php echo esc_url( add_query_arg( 'xts_ajax', '1', next_posts( $max_page, false ) ) ); ?>" rel="nofollow" class="xts-button xts-load-more<?php echo esc_attr( $classes ); ?>" data-id="<?php echo esc_attr( $uniqid ); ?>">
					<?php esc_html_e( 'Load more', 'xts-theme' ); ?>
				</a>
				<span class="xts-button xts-load-more xts-button-loader">
					<?php esc_html_e( 'Loading...', 'xts-theme' ); ?>
				</span>
			</div>
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'xts_comments_link_attributes' ) ) {
	/**
	 * Add attributes to comment link on post
	 *
	 * @since 1.0.0
	 *
	 * @param string $attributes Attributes.
	 *
	 * @return string
	 */
	function xts_comments_link_attributes( $attributes ) {
		$attributes .= ' title="' . esc_attr__( 'Comment on', 'xts-theme' ) . ' ' . get_the_title() . '" ';

		return $attributes;
	}

	add_filter( 'comments_popup_link_attributes', 'xts_comments_link_attributes' );
}

if ( ! function_exists( 'xts_meta_post_date' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 *
	 * @since 1.0.0
	 */
	function xts_meta_post_date() {
		?>
		<div class="xts-post-date">
			<?php if ( get_the_time( 'U' ) === get_the_modified_time( 'U' ) ) : ?>
				<time class="published updated" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
			<?php else : ?>
				<time class="published" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
				<time class="updated" datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>">
					<?php echo esc_html( get_the_modified_date() ); ?>
				</time>
			<?php endif; ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'xts_meta_post_labels' ) ) {
	/**
	 * Prints meta post labels.
	 *
	 * @since 1.0.0
	 */
	function xts_meta_post_labels() {
		$format    = get_post_format();
		$is_sticky = is_sticky();

		?>
		<?php if ( $format || $is_sticky ) : ?>
			<?php if ( $format ) : ?>
				<?php if ( 'video' === $format ) : ?>
					<?php xts_enqueue_js_script( 'post-video-controls' ); ?>
					<div class="xts-post-label xts-format-video">
						<div class="xts-post-controls">
							<div class="xts-post-control xts-play"></div>
							<div class="xts-post-control xts-mute"></div>
						</div>
					</div>
				<?php elseif ( 'gallery' === $format ) : ?>
					<div class="xts-post-label xts-format-gallery">
						<div class="xts-post-controls">
							<div class="xts-post-control xts-prev"></div>
							<div class="xts-post-control xts-next"></div>
						</div>
					</div>
				<?php else : ?>
					<div class="xts-post-label xts-format-<?php echo esc_attr( $format ); ?>"></div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'xts_meta_post_author' ) ) {
	/**
	 * Prints HTML with meta information about post author.
	 *
	 * @since 1.0.0
	 */
	function xts_meta_post_author() {
		$args      = xts_get_default_value( 'meta_post_author_args' );
		$author_id = get_post_field( 'post_author', get_the_ID() );

		if ( ! $author_id ) {
			$author_id = get_the_author_meta( 'ID' );
		}

		?>
			<div class="xts-post-author">
				<?php if ( $args['avatar'] ) : ?>
					<?php if ( $args['avatar_link'] ) : ?>
						<a class="xts-avatar url fn n" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
					<?php endif; ?>

					<?php echo get_avatar( $author_id, $args['avatar_size'] ); ?>

					<?php if ( $args['avatar_link'] ) : ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( $args['label'] ) : ?>
					<span class="xts-label">
						<?php esc_html_e( 'By', 'xts-theme' ); ?>
					</span>
				<?php endif; ?>

				<?php if ( $args['name'] ) : ?>
					<span class="xts-name author vcard">
						<a class="url fn n" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
							<?php echo esc_html( get_userdata( $author_id )->display_name ); ?>
						</a>
					</span>
				<?php endif; ?>
			</div>
		<?php
	}
}

if ( ! function_exists( 'xts_meta_post_categories' ) ) {
	/**
	 * Prints HTML with meta information for the categories.
	 *
	 * @since 1.0.0
	 */
	function xts_meta_post_categories() {
		global $wp_rewrite;

		$categories = get_the_category();
		$rel        = is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ? 'category tag' : 'category';

		?>
		<?php if ( $categories ) : ?>
			<div class="xts-post-categories">
				<?php foreach ( $categories as $category ) : ?>
					<?php
					$styles  = apply_filters( 'xts_categories_label_styles', '', $category->term_id );
					$classes = apply_filters( 'xts_categories_label_classes', '', $category->term_id );

					if ( $styles ) {
						$styles = 'style="' . $styles . '"';
					}

					?>

					<a <?php echo wp_kses( $styles, true ); ?> class="xts-post-category<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" rel="<?php echo esc_attr( $rel ); ?>">
						<?php echo esc_html( $category->name ); ?>
					</a>

				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'xts_meta_post_tags' ) ) {
	/**
	 * Prints HTML with meta information for the tags.
	 *
	 * @since 1.0.0
	 *
	 * @param string $delimiter Delimiter.
	 */
	function xts_meta_post_tags( $delimiter = ', ' ) {
		?>
		<?php if ( get_the_tag_list() ) : ?>
			<div class="xts-meta-tags">
				<?php the_tags( '', $delimiter ); ?>
			</div>
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'xts_meta_post_comments' ) ) {
	/**
	 * Prints HTML with meta information for the comments.
	 *
	 * @since 1.0.0
	 */
	function xts_meta_post_comments() {
		$comment_link = '<span class="replies-count">%s</span> <span class="replies-count-label">%s</span>';

		?>
		<?php if ( comments_open() ) : ?>
			<div class="xts-post-reply">
				<?php
				comments_popup_link( sprintf( $comment_link, '0', esc_html__( 'comments', 'xts-theme' ) ), sprintf( $comment_link, '1', esc_html__( 'comment', 'xts-theme' ) ), sprintf( $comment_link, '%', esc_html__( 'comments', 'xts-theme' ) ) );
				?>
			</div>
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'xts_posts_pagination' ) ) {
	/**
	 * Display posts pagination.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $pages Pages count.
	 * @param integer $range Show item range.
	 */
	function xts_posts_pagination( $pages = '', $range = 2 ) {
		$show_items = ( $range * 2 ) + 1;

		global $paged;

		$page = $paged;

		if ( empty( $page ) ) {
			$page = 1;
		}

		if ( '' === $pages ) {
			global $wp_query;
			$pages = $wp_query->max_num_pages;
			if ( ! $pages ) {
				$pages = 1;
			}
		}

		if ( 1 !== $pages ) {
			echo '<nav class="xts-pagination">';
			echo '<ul>';

			if ( $page > 2 && $page > $range + 1 && $show_items < $pages ) {
				echo '<li><a href="' . esc_url( get_pagenum_link() ) . '" class="page-numbers">&laquo;</a></li>';
			}

			if ( $page > 1 && $show_items < $pages ) {
				echo '<li><a href="' . esc_url( get_pagenum_link( $page - 1 ) ) . '" class="page-numbers">&lsaquo;</a></li>';
			}

			for ( $i = 1; $i <= $pages; $i ++ ) {
				if ( 1 !== $pages && ( ! ( $i >= $page + $range + 1 || $i <= $page - $range - 1 ) || $pages <= $show_items ) ) {
					if ( $page === $i ) {
						?>
						<li>
							<span class="current page-numbers">
								<?php echo esc_html( $i ); ?>
							</span>
						</li>
						<?php
					} else {
						?>
						<li>
							<a href="<?php echo esc_url( get_pagenum_link( $i ) ); ?>" class="page-numbers" >
								<?php echo esc_html( $i ); ?>
							</a>
						</li>
						<?php
					}
				}
			}

			if ( $page < $pages && $show_items < $pages ) {
				echo '<li><a href="' . esc_url( get_pagenum_link( $page + 1 ) ) . '" class="page-numbers">&rsaquo;</a></li>';
			}

			if ( $page < $pages - 1 && $page + $range - 1 < $pages && $show_items < $pages ) {
				echo '<li><a href="' . esc_url( get_pagenum_link( $pages ) ) . '" class="page-numbers">&raquo;</a></li>';
			}

			echo '</ul>';
			echo '</nav>';
		}
	}
}

if ( ! function_exists( 'xts_get_related_posts' ) ) {
	/**
	 * Get related posts.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post Post.
	 */
	function xts_get_related_posts( $post ) {
		xts_blog_template(
			array(
				'image_size'            => xts_get_loop_prop( 'blog_image_size' ),
				'image_size_custom'     => xts_get_loop_prop( 'blog_image_size_custom' ),
				'design'                => xts_get_default_value( 'blog_single_related_posts_design' ),
				'items_per_page'        => array( 'size' => xts_get_opt( 'blog_single_related_posts_count' ) ),
				'carousel_items'        => array( 'size' => xts_get_opt( 'blog_single_related_posts_per_row' ) ),
				'carousel_items_tablet' => array( 'size' => 2 ),
				'carousel_items_mobile' => array( 'size' => 1 ),
				'view'                  => 'carousel',
				'black_white'           => xts_get_loop_prop( 'blog_post_black_white' ),
				'shadow'                => xts_get_loop_prop( 'blog_post_shadow' ),
				'related_post_ids'      => $post->ID,
				'carousel_spacing'      => xts_get_opt( 'blog_spacing' ),
				'text'                  => false,
			)
		);
	}
}

if ( ! function_exists( 'xts_author_bio' ) ) {
	/**
	 * The template for displaying Author bios
	 *
	 * @since 1.0.0
	 */
	function xts_author_bio() {
		?>
		<div class="author-info xts-single-post-author">
			<div class="author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), 90 ); ?>
			</div>

			<div class="author-description">
				<h4 class="author-title">
					<?php
					printf( /* translators: s: author name */ esc_html__( 'About %s', 'xts-theme' ), get_the_author() );
					?>
				</h4>

				<div class="author-bio">
					<?php the_author_meta( 'description' ); ?>
				</div>

				<?php if ( ! is_author() ) : ?>
					<a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
						<?php
						printf( /* translators: s: author name */ wp_kses( __( 'View all posts by %s', 'xts-theme' ), array( 'span' => array( 'class' ) ) ), get_the_author() );
						?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'xts_the_content' ) ) {
	/**
	 * Post content
	 *
	 * @since 1.0.0
	 */
	function xts_the_content() {
		global $post;

		$content_type = xts_get_opt( 'blog_excerpt' );

		if ( 'full' === $content_type ) {
			echo str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', get_the_content( false ) ) );
		} else {
			if ( $post->post_excerpt ) {
				the_excerpt();
			} else {
				echo xts_get_excerpt_from_content( $post->post_content, intval( xts_get_loop_prop( 'blog_excerpt_length' ) ) ); // phpcs:ignore
			}
		}
	}
}

if ( ! function_exists( 'xts_get_excerpt_from_content' ) ) {
	/**
	 * Get excerpt from post content
	 *
	 * @since 1.0.0
	 *
	 * @param string  $post_content Post content.
	 * @param integer $limit        Word or letter limit.
	 *
	 * @return array|false|string
	 */
	function xts_get_excerpt_from_content( $post_content, $limit ) {
		$post_content     = preg_replace( '/\[caption(.*)\[\/caption\]/i', '', $post_content );
		$post_content     = preg_replace( '`\[[^\]]*\]`', '', $post_content );
		$post_content     = stripslashes( wp_filter_nohtml_kses( $post_content ) );
		$words_or_letters = xts_get_opt( 'blog_excerpt_words_or_letters' );
		$start            = 0;

		if ( strstr( $post_content, '<!-- wp:paragraph -->' ) ) {
			$start = '22';
		}

		if ( 'letters' === $words_or_letters ) {
			$excerpt = mb_substr( $post_content, $start, $limit );
			if ( mb_strlen( $excerpt ) >= $limit ) {
				$excerpt .= '...';
			}
		} else {
			$limit ++;
			$excerpt = explode( ' ', $post_content, $limit );

			if ( count( $excerpt ) >= $limit ) {
				array_pop( $excerpt );
				$excerpt = implode( ' ', $excerpt ) . '...';
			} else {
				$excerpt = implode( ' ', $excerpt );
			}
		}

		$excerpt = wp_strip_all_tags( $excerpt );

		if ( '...' === trim( $excerpt ) ) {
			return '';
		}

		return $excerpt;
	}
}

