<?php
/**
 * Page title templates functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_shop_page_title' ) ) {
	/**
	 * Show shop page title in page title.
	 *
	 * @since 1.0.0
	 */
	function xts_shop_page_title() {
		$custom_page_title_tag = xts_get_opt( 'page_title_tag' );
		$title_tag             = 'h1';
		$title                 = woocommerce_page_title( false );

		if ( 'default' !== $custom_page_title_tag && $custom_page_title_tag ) {
			$title_tag = $custom_page_title_tag;
		}

		if ( is_singular( 'product' ) ) {
			$title_tag = 'h3';
			$title     = get_the_title();
		}

		?>
		<<?php echo esc_attr( $title_tag ); ?> class="xts-title title">
			<?php echo esc_html( $title ); // phpcs:ignore ?>
		</<?php echo esc_attr( $title_tag ); ?>>
		<?php
	}

	add_action( 'xts_shop_page_title', 'xts_shop_page_title', 10 );
}

if ( ! function_exists( 'xts_show_shop_categories_in_page_title' ) ) {
	/**
	 * Show shop categories in page title.
	 *
	 * @since 1.0.0
	 */
	function xts_show_shop_categories_in_page_title() {
		if ( is_singular( 'product' ) || ! xts_get_opt( 'page_title_shop_categories' ) ) {
			return;
		}

		xts_page_title_shop_categories();
	}

	add_action( 'xts_after_shop_page_title', 'xts_show_shop_categories_in_page_title', 30 );
}

if ( ! function_exists( 'xts_page_title' ) ) {
	/**
	 * Generate page title
	 *
	 * @since 1.0.0
	 */
	function xts_page_title() {
		global $wp_query;

		$wrapper_classes = '';
		$custom_styles   = '';

		$page_for_posts          = get_option( 'page_for_posts' );
		$page_id                 = xts_get_page_id();
		$title                   = get_the_title();
		$title_tag               = 'h1';
		$show_breadcrumbs        = xts_get_opt( 'page_title_breadcrumbs' );
		$design                  = xts_get_opt( 'page_title_design' );
		$color_scheme            = xts_get_opt( 'page_title_color_scheme' );
		$size                    = xts_get_opt( 'page_title_size' );
		$blog_single_design      = xts_get_opt( 'blog_single_design' );
		$portfolio_single_design = xts_get_opt( 'portfolio_single_design' );
		$custom_page_title_tag   = xts_get_opt( 'page_title_tag' );
		$metabox_bg_image        = get_post_meta( $page_id, '_xts_page_title_bg_image', true );
		$metabox_bg_color        = get_post_meta( $page_id, '_xts_page_title_bg_color', true );
		$metabox_design          = get_post_meta( $page_id, '_xts_page_title_design', true );

		if ( is_singular( 'post' ) && 'inherit' === $metabox_design ) {
			$design = xts_get_opt( 'blog_single_page_title_design', $design );
		}

		// Product category styles.
		if ( xts_is_woocommerce_installed() && is_product_category() ) {
			$category = $wp_query->get_queried_object();

			$category_bg_image = get_term_meta( $category->term_id, '_xts_page_title_bg_image', true );
			$category_bg_color = get_term_meta( $category->term_id, '_xts_page_title_bg_color', true );

			if ( ( ( isset( $category_bg_image['id'] ) && ! $category_bg_image['id'] ) || ! isset( $category_bg_image['id'] ) ) && ( ( isset( $category_bg_color['idle'] ) && ! $category_bg_color['idle'] ) || ! isset( $category_bg_color['idle'] ) ) ) {
				$category_bg_image = get_term_meta( $category->parent, '_xts_page_title_bg_image', true );
				$category_bg_color = get_term_meta( $category->parent, '_xts_page_title_bg_color', true );
			}

			if ( isset( $category_bg_image['id'] ) && $category_bg_image['id'] ) {
				$metabox_bg_image = $category_bg_image;
			}

			if ( isset( $category_bg_color['idle'] ) && $category_bg_color['idle'] ) {
				$metabox_bg_color = $category_bg_color;
			}
		}

		if ( 'default' !== $custom_page_title_tag && $custom_page_title_tag ) {
			$title_tag = $custom_page_title_tag;
		}

		if ( 'without' === $design || is_singular( 'xts-template' ) || is_singular( 'xts-html-block' ) || is_singular( 'xts-slide' ) || is_404() || ( xts_is_woocommerce_installed() && xts_is_shop_archive() && ! xts_get_opt( 'product_archive_page_title' ) ) ) {
			return;
		}

		if ( isset( $metabox_bg_image['id'] ) && $metabox_bg_image['id'] ) {
			$metabox_bg_image_url = wp_get_attachment_image_url( $metabox_bg_image['id'], 'full' );
			$custom_styles       .= ' background-image: url(' . esc_url( $metabox_bg_image_url ) . ');';
		}

		if ( isset( $metabox_bg_color['idle'] ) && $metabox_bg_color['idle'] ) {
			$custom_styles .= ' background-color: ' . $metabox_bg_color['idle'] . ';';
		}

		if ( ( 'page-title' === $blog_single_design && is_singular( 'post' ) ) || ( 'page-title' === $portfolio_single_design && is_singular( 'xts-portfolio' ) ) ) {
			$color_scheme = 'light';
		}

		// Wrapper classes.
		$wrapper_classes .= ' xts-size-' . $size;
		if ( ( ! is_singular( 'xts-portfolio' ) || ( is_singular( 'post' ) && 'page-title' !== $portfolio_single_design ) ) && ( ! is_singular( 'post' ) || ( is_singular( 'post' ) && 'page-title' !== $blog_single_design ) ) ) {
			$wrapper_classes .= ' xts-style-' . $design;
		}
		if ( 'inherit' !== $color_scheme ) {
			$wrapper_classes .= ' xts-scheme-' . $color_scheme;
		}

		if ( is_tax() ) {
			$title = single_term_title( '', false );
		}

		// Heading for blog and archives.
		if ( is_singular( 'post' ) || xts_is_blog_archive() ) {
			$title = ( ! empty( $page_for_posts ) ) ? get_the_title( $page_for_posts ) : esc_html__( 'Blog', 'xts-theme' );

			if ( is_tag() ) {
				$title = esc_html__( 'Tag Archives: ', 'xts-theme' ) . single_tag_title( '', false );
			}

			if ( is_category() ) {
				$title = single_cat_title( '', false );
			}

			if ( is_date() ) {
				if ( is_day() ) :
					$title = esc_html__( 'Daily Archives: ', 'xts-theme' ) . get_the_date();
				elseif ( is_month() ) :
					$title = esc_html__( 'Monthly Archives: ', 'xts-theme' ) . get_the_date( _x( 'F Y', 'monthly archives date format', 'xts-theme' ) );
				elseif ( is_year() ) :
					$title = esc_html__( 'Yearly Archives: ', 'xts-theme' ) . get_the_date( _x( 'Y', 'yearly archives date format', 'xts-theme' ) );
				else :
					$title = esc_html__( 'Archives', 'xts-theme' );
				endif;
			}

			if ( is_author() ) {

				the_post();

				$title = esc_html__( 'Posts by ', 'xts-theme' ) . get_the_author();

				rewind_posts();
			}

			if ( is_search() ) {
				$title = esc_html__( 'Search results for: ', 'xts-theme' ) . get_search_query();
			}

			if ( is_single() ) {
				$title_tag = 'h3';
			}
		}

		if ( 'page-title' === $blog_single_design && is_singular( 'post' ) ) {
			$image_url        = get_the_post_thumbnail_url( $page_id, apply_filters( 'xts_single_design_image_page_title_image_size', 'full' ) );
			$title            = get_the_title();
			$post_format      = get_post_format();
			$title_tag        = 'h1';
			$wrapper_classes .= ' xts-with-summary';

			if ( xts_get_opt( 'blog_single_parallax_scroll' ) ) {
				xts_enqueue_js_script( 'page-title-effect' );
				$wrapper_classes .= ' xts-parallax-scroll';
			}

			if ( $image_url ) {
				$custom_styles .= 'background-image: url(' . esc_url( $image_url ) . ');';
			}

			if ( 'default' !== $custom_page_title_tag && $custom_page_title_tag ) {
				$title_tag = $custom_page_title_tag;
			}

			?>
				<div class="xts-page-title<?php echo esc_attr( $wrapper_classes ); ?>">
					<div class="xts-page-title-overlay xts-fill" style="<?php echo esc_attr( $custom_styles ); ?>"></div>
					<div class="container">
						<?php xts_meta_post_categories(); ?>

						<?php if ( 'quote' === $post_format ) : ?>
							<?php xts_post_quote_template( false ); ?>
						<?php else : ?>
							<<?php echo esc_attr( $title_tag ); ?> class="xts-title title">
								<?php echo esc_html( $title ); ?>
							</<?php echo esc_attr( $title_tag ); ?>>

							<?php if ( 'link' === $post_format ) : ?>
								<?php xts_post_link_template( false, true ); ?>
							<?php endif; ?>
						<?php endif; ?>

						<div class="xts-post-meta">
							<?php xts_meta_post_author(); ?>
							<?php xts_meta_post_date(); ?>
						</div>
					</div>
				</div>
			<?php

			return;
		}

		// Heading for portfolio.
		if ( is_singular( 'xts-portfolio' ) || is_post_type_archive( 'xts-portfolio' ) || is_tax( 'xts-portfolio-cat' ) ) {
			$title = get_the_title( $page_id );

			if ( is_tax( 'xts-portfolio-cat' ) ) {
				$title = single_term_title( '', false );
			}

			if ( is_search() ) {
				$title = esc_html__( 'Search results for: ', 'xts-theme' ) . get_search_query();
			}
		}

		if ( 'page-title' === $portfolio_single_design && is_singular( 'xts-portfolio' ) ) {
			$image_url        = get_the_post_thumbnail_url( $page_id, apply_filters( 'xts_single_design_image_page_title_image_size', 'full' ) );
			$wrapper_classes .= ' xts-with-summary';

			if ( xts_get_opt( 'portfolio_single_parallax_scroll' ) ) {
				xts_enqueue_js_script( 'page-title-effect' );
				$wrapper_classes .= ' xts-parallax-scroll';
			}

			if ( $image_url ) {
				$custom_styles .= 'background-image: url(' . esc_url( $image_url ) . ');';
			}

			?>
				<div class="xts-page-title<?php echo esc_attr( $wrapper_classes ); ?>">
					<div class="xts-page-title-overlay xts-fill" style="<?php echo esc_attr( $custom_styles ); ?>"></div>
					<div class="container">
						<?php xts_meta_portfolio_categories(); ?>

						<<?php echo esc_attr( $title_tag ); ?> class="xts-title title">
							<?php echo esc_html( $title ); ?>
						</<?php echo esc_attr( $title_tag ); ?>>

						<?php if ( $show_breadcrumbs ) : ?>
							<?php xts_current_breadcrumbs(); ?>
						<?php endif; ?>
					</div>
				</div>
			<?php
			return;
		}

		// Heading for shop.
		if ( xts_is_shop_archive() || ( is_singular( 'product' ) && xts_get_opt( 'single_product_page_title' ) ) ) {
			$is_title_enabled = xts_get_opt( 'page_title_shop_title' );

			if ( ! $is_title_enabled ) {
				$wrapper_classes .= ' xts-without-title';
			}

			?>
				<div class="xts-page-title<?php echo esc_attr( $wrapper_classes ); ?>">
					<div class="xts-page-title-overlay xts-fill" style="<?php echo esc_attr( $custom_styles ); ?>"></div>
					<div class="container">

						<?php do_action( 'xts_before_shop_page_title' ); ?>

						<?php if ( $is_title_enabled ) : ?>
							<?php do_action( 'xts_shop_page_title' ); ?>
						<?php endif; ?>

						<?php do_action( 'xts_after_shop_page_title' ); ?>
					</div>
				</div>
			<?php

			return;
		}

		// Heading for pages.
		if ( ( is_singular( 'page' ) && ( ! $page_for_posts || ! is_page( $page_for_posts ) ) ) || ! is_singular( 'product' ) ) {
			?>
				<div class="xts-page-title<?php echo esc_attr( $wrapper_classes ); ?>">
					<div class="xts-page-title-overlay xts-fill" style="<?php echo esc_attr( $custom_styles ); ?>"></div>
					<div class="container">
						<?php do_action( 'xts_before_checkout_steps' ); ?>
						<?php if ( xts_is_woocommerce_installed() && xts_get_opt( 'checkout_steps' ) && ( is_cart() || is_checkout() ) ) : ?>
							<?php xts_page_title_checkout_steps(); ?>
						<?php else : ?>
							<<?php echo esc_attr( $title_tag ); ?> class="xts-title title">
								<?php echo esc_html( $title ); ?>
							</<?php echo esc_attr( $title_tag ); ?>>

							<?php if ( $show_breadcrumbs ) : ?>
								<?php xts_current_breadcrumbs(); ?>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php

			return;
		}
	}

	add_action( 'xts_before_site_content_container', 'xts_page_title' );
}

if ( ! function_exists( 'xts_breadcrumbs' ) ) { // phpcs:disable
	/**
	 * Breadcrumbs function
	 *
	 * @since 1.0.0
	 */
	function xts_breadcrumbs() {
		global $post;

		/**
		 * Options
		 */
		$text['home'] = esc_html__( 'Home', 'xts-theme' );
		$text['404']  = esc_html__( 'Error 404', 'xts-theme' );

		/* translators: %s: category name */
		$text['category'] = esc_html__( 'Archive by Category "%s"', 'xts-theme' );

		/* translators: %s: search query */
		$text['search'] = esc_html__( 'Search Results for "%s" Query', 'xts-theme' );

		/* translators: %s: tag name */
		$text['tag'] = esc_html__( 'Posts Tagged "%s"', 'xts-theme' );

		/* translators: %s: author name */
		$text['author'] = esc_html__( 'Articles Posted by %s', 'xts-theme' );

		$show_current_post = 1; // 1 - show current post.
		$show_current      = 1; // 1 - show current post/page/category title in breadcrumbs, 0 - don't show.
		$show_on_home      = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show.
		$show_home_link    = 1; // 1 - show the 'Home' link, 0 - don't show.
		$show_title        = 1; // 1 - show the title for the links, 0 - don't show.
		$delimiter         = '<span class="xts-delimiter"></span>'; // delimiter between crumbs.
		$before            = '<span class="xts-active">'; // tag before the current crumb.
		$after             = '</span>'; // tag after the current crumb.

		$home_link     = home_url( '/' );
		$link_before   = '<span typeof="v:Breadcrumb">';
		$link_after    = '</span>';
		$link_attr     = ' rel="v:url" property="v:title"';
		$link          = $link_before . '<a' . $link_attr . ' href="%1$s">%2$s</a>' . $link_after;
		$parent_id     = $parent_id_2 = ( ! empty( $post ) && is_a( $post, 'WP_Post' ) ) ? $post->post_parent : 0;
		$front_page_id = get_option( 'page_on_front' );
		$projects_id   = xts_get_opt( 'portfolio_page' );

		do_action( 'xts_before_breadcrumbs' );

		if ( apply_filters( 'xts_show_breadcrumbs', false ) ) {
			return '';
		}
		
		if ( function_exists( 'is_bbpress' ) && function_exists( 'bbp_breadcrumb' ) && is_bbpress() && apply_filters( 'xts_bbpress_breadcrumbs', false ) ) {
			bbp_breadcrumb();
			return '';
		}

		if ( is_front_page() ) {
			if ( 1 === $show_on_home ) {
				echo '<div class="xts-breadcrumbs"><a href="' . $home_link . '">' . $text['home'] . '</a></div>';
			}
		} else {
			echo '<div class="xts-breadcrumbs" xmlns:v="https://schema.org/">';

			if ( 1 === $show_home_link ) {
				echo '<a href="' . $home_link . '" rel="v:url" property="v:title">' . $text['home'] . '</a>';

				if ( 0 === $front_page_id || $parent_id !== $front_page_id ) {
					echo wp_kses( $delimiter, 'xts_breadcrumbs' );
				}
			}

			if ( is_category() ) {
				$this_cat = get_category( get_query_var( 'cat' ), false );

				if ( 0 !== $this_cat->parent ) {
					$cats = get_category_parents( $this_cat->parent, true, $delimiter );

					if ( 0 === $show_current ) {
						$cats = preg_replace( "#^(.+)$delimiter$#", '$1', $cats );
					}

					$cats = str_replace( '<a', $link_before . '<a' . $link_attr, $cats );
					$cats = str_replace( '</a>', '</a>' . $link_after, $cats );

					if ( 0 === $show_title ) {
						$cats = preg_replace( '/ title="(.*?)"/', '', $cats );
					}

					echo wp_kses( $cats, 'xts_breadcrumbs' );
				}

				if ( 1 === $show_current ) {
					echo wp_kses( $before . sprintf( $text['category'], single_cat_title( '', false ) ) . $after, 'xts_breadcrumbs' );
				}
			} elseif ( is_home() ) {
				echo wp_kses( $before . get_the_title( get_option( 'page_for_posts' ) ) . $after, 'xts_breadcrumbs' );
			} elseif ( is_tax( 'xts-portfolio-cat' ) ) {
				printf( $link, get_the_permalink( $projects_id ), get_the_title( $projects_id ) );
			} elseif ( is_search() ) {
				echo wp_kses( $before . sprintf( $text['search'], get_search_query() ) . $after, 'xts_breadcrumbs' );
			} elseif ( is_day() ) {
				echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $delimiter;
				echo sprintf( $link, get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), get_the_time( 'F' ) ) . $delimiter;
				echo wp_kses( $before . get_the_time( 'd' ) . $after, 'xts_breadcrumbs' );

			} elseif ( is_month() ) {
				echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $delimiter;
				echo wp_kses( $before . get_the_time( 'F' ) . $after, 'xts_breadcrumbs' );
			} elseif ( is_year() ) {
				echo wp_kses( $before . get_the_time( 'Y' ) . $after, 'xts_breadcrumbs' );
			} elseif ( is_single() && ! is_attachment() ) {
				if ( 'xts-portfolio' === get_post_type() ) {
					printf( $link, get_the_permalink( $projects_id ), get_the_title( $projects_id ) );

					if ( 1 === $show_current ) {
						echo wp_kses( $delimiter . $before . get_the_title() . $after, 'xts_breadcrumbs' );
					}
				} elseif ( 'post' !== get_post_type() ) {
					$post_type = get_post_type_object( get_post_type() );
					$slug      = $post_type->rewrite;

					if ( isset( $slug['slug'] ) ) {
						printf( $link, $home_link . $slug['slug'] . '/', $post_type->labels->singular_name );
					}

					if ( 1 === $show_current ) {
						echo wp_kses( $delimiter . $before . get_the_title() . $after, 'xts_breadcrumbs' );
					}
				} else {
					$cat = get_the_category();

					if ( isset( $cat[0] ) ) {
						$cat  = $cat[0];
						$cats = get_category_parents( $cat, true, $delimiter );

						if ( 0 === $show_current ) {
							$cats = preg_replace( "#^(.+)$delimiter$#", '$1', $cats );
						}

						$cats = str_replace( '<a', $link_before . '<a' . $link_attr, $cats );
						$cats = str_replace( '</a>', '</a>' . $link_after, $cats );

						if ( 0 === $show_title ) {
							$cats = preg_replace( '/ title="(.*?)"/', '', $cats );
						}

						echo wp_kses( $cats, 'xts_breadcrumbs' );
					}

					if ( 1 === $show_current_post ) {
						echo wp_kses( $before . get_the_title() . $after, 'xts_breadcrumbs' );
					}
				}
			} elseif ( ! is_single() && ! is_page() && 'post' !== get_post_type() && ! is_404() ) {
				$post_type = get_post_type_object( get_post_type() );

				if ( is_object( $post_type ) ) {
					echo wp_kses( $before . $post_type->labels->name . $after, 'xts_breadcrumbs' );
				}
			} elseif ( is_attachment() ) {
				$parent = get_post( $parent_id );
				$cat    = get_the_category( $parent->ID );
				$cat    = isset( $cat[0] ) ? $cat[0] : '';

				if ( $cat ) {
					$cats = get_category_parents( $cat, true, $delimiter );
					$cats = str_replace( '<a', $link_before . '<a' . $link_attr, $cats );
					$cats = str_replace( '</a>', '</a>' . $link_after, $cats );

					if ( 0 === $show_title ) {
						$cats = preg_replace( '/ title="(.*?)"/', '', $cats );
					}

					echo wp_kses( $cats, 'xts_breadcrumbs' );
				}

				printf( $link, get_permalink( $parent ), $parent->post_title );

				if ( 1 === $show_current ) {
					echo wp_kses( $delimiter . $before . get_the_title() . $after, 'xts_breadcrumbs' );
				}
			} elseif ( is_page() && ! $parent_id ) {
				if ( 1 === $show_current ) {
					echo wp_kses( $before . get_the_title() . $after, 'xts_breadcrumbs' );
				}
			} elseif ( is_page() && $parent_id ) {
				if ( $parent_id !== $front_page_id ) {
					$breadcrumbs = array();

					while ( $parent_id ) {
						$page = get_post( $parent_id );

						if ( $parent_id !== $front_page_id ) {
							$breadcrumbs[] = sprintf( $link, get_permalink( $page->ID ), get_the_title( $page->ID ) );
						}

						$parent_id = $page->post_parent;
					}

					$breadcrumbs_count = count( array_reverse( $breadcrumbs ) );

					for ( $i = 0; $i < $breadcrumbs_count; $i++ ) {
						echo isset( $breadcrumbs[ $i ] ) ? $breadcrumbs[ $i ] : '';

						if ( $i !== $breadcrumbs_count - 1 ) {
							echo wp_kses( $delimiter, 'xts_breadcrumbs' );
						}
					}
				}

				if ( 1 === $show_current ) {
					if ( 1 === $show_home_link || ( 0 !== $parent_id_2 && $parent_id_2 !== $front_page_id ) ) {
						echo wp_kses( $delimiter, 'xts_breadcrumbs' );
					}

					echo wp_kses( $before . get_the_title() . $after, 'xts_breadcrumbs' );
				}
			} elseif ( is_tag() ) {
				echo wp_kses( $before . sprintf( $text['tag'], single_tag_title( '', false ) ) . $after, 'xts_breadcrumbs' );
			} elseif ( is_author() ) {
				global $author;
				$user_data = get_userdata( $author );
				echo wp_kses( $before . sprintf( $text['author'], $user_data->display_name ) . $after, 'xts_breadcrumbs' );
			} elseif ( is_404() ) {
				echo wp_kses( $before . $text['404'] . $after, 'xts_breadcrumbs' );
			} elseif ( has_post_format() && ! is_singular() ) {
				echo get_post_format_string( get_post_format() );
			}

			if ( get_query_var( 'paged' ) ) {

				echo wp_kses( $delimiter, 'xts_breadcrumbs' );

				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
					echo ' (';
				}

				echo wp_kses( $before . esc_html__( 'Page', 'xts-theme' ) . ' ' . get_query_var( 'paged' ) . $after, 'xts_breadcrumbs' );

				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
					echo ')';
				}
			}

			echo '</div><!-- .breadcrumbs -->';
		}
	}
} // phpcs:enable


if ( ! function_exists( 'xts_current_breadcrumbs' ) ) {
	/**
	 * Get current breadcrumbs
	 *
	 * @since 1.0.0
	 */
	function xts_current_breadcrumbs() {
		if ( xts_get_opt( 'yoast_pages_breadcrumbs' ) && function_exists( 'yoast_breadcrumb' ) ) {
			?>
				<div class="xts-yoast-breadcrumb xts-breadcrumbs">
					<?php echo yoast_breadcrumb(); // phpcs:ignore ?>
				</div>
			<?php
		} else {
			xts_breadcrumbs();
		}
	}
}

if ( ! function_exists( 'xts_current_shop_breadcrumbs' ) ) {
	/**
	 * Get current shop breadcrumbs
	 *
	 * @since 1.0.0
	 */
	function xts_current_shop_breadcrumbs() {
		if ( xts_get_opt( 'yoast_shop_breadcrumbs' ) && function_exists( 'yoast_breadcrumb' ) ) {
			?>
				<div class="xts-yoast-breadcrumb xts-breadcrumbs">
					<?php echo yoast_breadcrumb(); // phpcs:ignore ?>
				</div>
			<?php
		} else {
			woocommerce_breadcrumb();
		}
	}

	add_action( 'xts_shop_tools_left_area', 'xts_current_shop_breadcrumbs' );
}

if ( ! function_exists( 'xts_wc_breadcrumbs_defaults' ) ) {
	/**
	 * Woocommerce breadcrumbs defaults
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 */
	function xts_wc_breadcrumbs_defaults( $args ) {
		$args['delimiter']   = '<span class="xts-delimiter"></span>';
		$args['wrap_before'] = '<nav class="woocommerce-breadcrumb xts-breadcrumbs">';
		$args['wrap_after']  = '</nav>';

		return $args;
	}

	add_filter( 'woocommerce_breadcrumb_defaults', 'xts_wc_breadcrumbs_defaults', 10 );
}
