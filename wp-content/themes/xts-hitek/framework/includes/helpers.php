<?php
/**
 * Framework helper functions.
 *
 * @package xts
 */

use Elementor\Core\Files\CSS\Post;
use XTS\Framework;
use XTS\Config;
use XTS\Framework\Modules;
use XTS\Theme_Features;
use Elementor\Group_Control_Image_Size;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_get_post_read_more_link' ) ) {
	/**
	 * Get post read more link.
	 *
	 * @since 1.0.0
	 */
	function xts_get_post_read_more_link() {
		global $post;

		$link = get_permalink();

		if ( has_block( 'more', get_the_ID() ) || preg_match( '/<!--more(.*?)?-->/', $post->post_content ) ) {
			$link .= '#more-' . get_the_ID();
		}

		return $link;
	}
}

if ( ! function_exists( 'xts_get_sidebar' ) ) {
	/**
	 * Returns document title for the current page.
	 *
	 * @since 1.0.0
	 */
	function xts_get_sidebar( $position ) {
		if ( $position === xts_get_page_layout() ) {
			get_sidebar();
		}
	}
}

if ( ! function_exists( 'xts_get_document_title' ) ) {
	/**
	 * Returns document title for the current page.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|string
	 */
	function xts_get_document_title() {
		$title = wp_get_document_title();

		$post_meta = get_post_meta( xts_get_page_id(), '_yoast_wpseo_title', true );
		if ( property_exists( get_queried_object(), 'term_id' ) && function_exists( 'YoastSEO' ) ) {
			$taxonomy_helper = YoastSEO()->helpers->taxonomy;
			$meta            = $taxonomy_helper->get_term_meta( get_queried_object() );

			if ( isset( $meta['wpseo_title'] ) && $meta['wpseo_title'] ) {
				$title = wpseo_replace_vars( $meta['wpseo_title'], get_queried_object() );
			}
		} elseif ( $post_meta && function_exists( 'wpseo_replace_vars' ) ) {
			$title = wpseo_replace_vars( $post_meta, get_post( xts_get_page_id() ) );
		}

		return $title;
	}

	add_filter( 'wp_title', 'xts_get_document_title' );
}

if ( ! function_exists( 'xts_get_document_description' ) ) {
	/**
	 * Returns document description for the current page.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|string
	 */
	function xts_get_document_description() {
		$description = '';

		$post_meta = get_post_meta( xts_get_page_id(), '_yoast_wpseo_metadesc', true );

		if ( property_exists( get_queried_object(), 'term_id' ) && function_exists( 'YoastSEO' ) ) {
			$taxonomy_helper = YoastSEO()->helpers->taxonomy;
			$meta            = $taxonomy_helper->get_term_meta( get_queried_object() );

			if ( isset( $meta['wpseo_desc'] ) && $meta['wpseo_desc'] ) {
				$description = wpseo_replace_vars( $meta['wpseo_desc'], get_queried_object() );
			}
		} elseif ( $post_meta && function_exists( 'wpseo_replace_vars' ) ) {
			$description = wpseo_replace_vars( $post_meta, get_post( xts_get_page_id() ) );
		}

		return $description;
	}
}

if ( ! function_exists( 'xts_fix_transitions_flicking' ) ) {
	/**
	 * Fix for transitions flicking.
	 *
	 * @since 1.0.0
	 */
	function xts_fix_transitions_flicking() {
		echo '<script type="text/javascript" id="xts-flicker-fix">//flicker fix.</script>';
	}

	add_action( 'wp_body_open', 'xts_fix_transitions_flicking', 1 );
}

if ( ! function_exists( 'xts_get_product_templates_array' ) ) {
	/**
	 * Get all templates options.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_product_templates_array() {
		global $post;

		$templates = array();

		$args = array(
			'posts_per_page' => 200, // phpcs:ignore
			'post_type'      => 'xts-template',
		);

		$posts_list = get_posts( $args );

		foreach ( $posts_list as $post ) { // phpcs:ignore
			setup_postdata( $post );

			$templates[ get_the_ID() ] = array(
				'name'  => get_the_title(),
				'value' => get_the_ID(),
			);
		}

		wp_reset_postdata();

		return $templates;
	}
}

if ( ! function_exists( 'xts_get_opt' ) ) {
	/**
	 * Get option.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $name    Option name.
	 * @param boolean $default Default value.
	 *
	 * @return mixed
	 */
	function xts_get_opt( $name, $default = false ) {
		$xts_options = isset( $GLOBALS[ 'xts_' . XTS_THEME_SLUG . '_options' ] ) ? $GLOBALS[ 'xts_' . XTS_THEME_SLUG . '_options' ] : array();

		$opt = $default;

		if ( isset( $xts_options[ $name ] ) ) {
			$opt = $xts_options[ $name ];
		}

		return apply_filters( 'xts_get_opt_' . $name, $opt );
	}
}

if ( ! function_exists( 'xts_get_page_id' ) ) {
	/**
	 * Get current page id
	 *
	 * @since 1.0.0
	 *
	 * @return integer
	 */
	function xts_get_page_id() {
		global $post;

		$page_id = 0;

		$page_for_posts    = get_option( 'page_for_posts' );
		$page_for_shop     = get_option( 'woocommerce_shop_page_id' );
		$page_for_projects = xts_get_opt( 'portfolio_page' );
		$custom_404_id     = xts_get_opt( 'custom_404_page' );

		if ( isset( $post->ID ) ) {
			$page_id = $post->ID;
		}

		if ( isset( $post->ID ) && ( is_singular( 'page' ) || is_singular( 'post' ) ) ) {
			$page_id = $post->ID;
		} elseif ( is_home() || is_singular( 'post' ) || is_search() || is_tag() || is_category() || is_date() || is_author() ) {
			$page_id = $page_for_posts;
		} elseif ( is_archive() && 'xts-portfolio' === get_post_type() ) {
			$page_id = $page_for_projects;
		} elseif ( is_singular( 'xts-template' ) ) {
			$preview_product = xts_get_preview_product();
			$page_id         = $preview_product->ID;
		}

		if ( xts_is_woocommerce_installed() && function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_product_tag() || xts_is_product_attribute_archive() ) ) {
			$page_id = $page_for_shop;
		}

		if ( is_404() && $custom_404_id && 'default' !== $custom_404_id ) {
			$page_id = $custom_404_id;
		}

		return $page_id;
	}
}

if ( ! function_exists( 'xts_tpl2id' ) ) {
	/**
	 * Get page ID by it's template name
	 *
	 * @since 1.0.0
	 *
	 * @param string $tpl Template name.
	 *
	 * @return integer
	 */
	function xts_tpl2id( $tpl = '' ) {
		$pages = get_pages(
			array(
			'meta_key'   => '_wp_page_template', // phpcs:ignore
			'meta_value' => $tpl, // phpcs:ignore
			)
		);

		if ( ! $pages ) {
			return '';
		}

		$page = array_shift( $pages );

		return $page->ID;
	}
}

if ( ! function_exists( 'xts_get_all_image_sizes' ) ) {
	/**
	 * Retrieve available image sizes
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_all_image_sizes() {
		global $_wp_additional_image_sizes;

		$default_image_sizes = array(
			'thumbnail',
			'medium',
			'medium_large',
			'large',
			'woocommerce_thumbnail',
			'woocommerce_single',
			'woocommerce_gallery_thumbnail',
		);
		$image_sizes         = array();

		foreach ( $default_image_sizes as $size ) {
			$image_sizes[ $size ] = array(
				'width'  => (int) get_option( $size . '_size_w' ),
				'height' => (int) get_option( $size . '_size_h' ),
				'crop'   => (bool) get_option( $size . '_crop' ),
			);
		}

		if ( $_wp_additional_image_sizes ) {
			$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
		}

		$image_sizes['full'] = array();

		return $image_sizes;
	}
}

if ( ! function_exists( 'xts_get_all_image_sizes_names' ) ) {
	/**
	 * Retrieve available image sizes names
	 *
	 * @since 1.0.0
	 *
	 * @param string $style Array output style.
	 *
	 * @return array
	 */
	function xts_get_all_image_sizes_names( $style = 'default' ) {
		$available_sizes = xts_get_all_image_sizes();
		$image_sizes     = array();

		foreach ( $available_sizes as $size => $size_attributes ) {
			$name = ucwords( str_replace( '_', ' ', $size ) );
			if ( is_array( $size_attributes ) && ( isset( $size_attributes['width'] ) && $size_attributes['width'] || isset( $size_attributes['height'] ) && $size_attributes['height'] ) ) {
				$name .= ' - ' . $size_attributes['width'] . ' x ' . $size_attributes['height'];
			}

			if ( 'elementor' === $style ) {
				$image_sizes[ $size ] = $name;
			} elseif ( 'header_builder' === $style ) {
				$image_sizes[ $size ] = array(
					'label' => $name,
					'value' => $size,
				);
			} elseif ( 'default' === $style ) {
				$image_sizes[ $size ] = array(
					'name'  => $name,
					'value' => $size,
				);
			} elseif ( 'widget' === $style ) {
				$image_sizes[ $name ] = $size;
			}
		}

		if ( 'elementor' === $style ) {
			$image_sizes['custom'] = esc_html__( 'Custom', 'xts-theme' );
		} elseif ( 'header_builder' === $style ) {
			$image_sizes['custom'] = array(
				'label' => esc_html__( 'Custom', 'xts-theme' ),
				'value' => 'custom',
			);
		} elseif ( 'default' === $style ) {
			$image_sizes['custom'] = array(
				'name'  => esc_html__( 'Custom', 'xts-theme' ),
				'value' => 'custom',
			);
		} elseif ( 'widget' === $style ) {
			$image_sizes[ esc_html__( 'Custom', 'xts-theme' ) ] = 'custom';
		}

		return $image_sizes;
	}
}

if ( ! function_exists( 'xts_get_menus_array' ) ) {
	/**
	 * Get all menu
	 *
	 * @since 1.0.0
	 *
	 * @param string $style Array output style.
	 *
	 * @return array
	 */
	function xts_get_menus_array( $style = 'header_builder' ) {
		$output = array();

		$menus = wp_get_nav_menus();

		if ( 'widget' === $style ) {
			$output[ esc_html__( 'Select', 'xts-theme' ) ] = '';
		} elseif ( 'elementor' === $style ) {
			$output['0'] = esc_html__( 'Select', 'xts-theme' );
		}

		foreach ( $menus as $menu ) {
			if ( 'header_builder' === $style ) {
				$output[ $menu->slug ] = array(
					'label' => $menu->name,
					'value' => $menu->slug,
				);
			} elseif ( 'elementor' === $style ) {
				$output[ $menu->slug ] = $menu->name;
			} elseif ( 'widget' === $style ) {
				$output[ $menu->name ] = $menu->slug;
			} elseif ( 'default' === $style ) {
				$output[ $menu->slug ] = array(
					'name'  => $menu->name,
					'value' => $menu->slug,
				);
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'xts_get_sidebars_array' ) ) {
	/**
	 * Get registered sidebars dropdown
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_sidebars_array() {
		global $wp_registered_sidebars;

		$output = array();

		foreach ( $wp_registered_sidebars as $id => $sidebar ) {
			$output[ $id ] = array(
				'name'  => $sidebar['name'],
				'value' => $id,
			);
		}

		return $output;
	}
}

if ( ! function_exists( 'xts_get_html_blocks_array' ) ) {
	/**
	 * Function to get array of HTML Blocks
	 *
	 * @since 1.0.0
	 *
	 * @param string $style Array output style.
	 *
	 * @return array
	 */
	function xts_get_html_blocks_array( $style = 'default' ) {
		$output = array();

		$posts = get_posts(
			array(
				'posts_per_page' => 200, // phpcs:ignore
				'post_type'      => 'xts-html-block',
			)
		);

		if ( 'elementor' === $style ) {
			$output['0'] = esc_html__( 'Select', 'xts-theme' );
		}

		if ( 'widget' === $style ) {
			$output[ esc_html__( 'Select', 'xts-theme' ) ] = '';
		}

		foreach ( $posts as $post ) {
			if ( 'default' === $style ) {
				$output[ $post->ID ] = array(
					'name'  => $post->post_title,
					'value' => $post->ID,
				);
			} elseif ( 'widget' === $style ) {
				$output[ $post->post_title ] = $post->ID;
			} elseif ( 'elementor' === $style ) {
				$output[ $post->ID ] = $post->post_title;
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'xts_get_size_guides_array' ) ) {
	/**
	 * Function to get array of Side guides
	 *
	 * @since 1.0.0
	 *
	 * @param string $style Array output style.
	 *
	 * @return array
	 */
	function xts_get_size_guides_array( $style = 'default' ) {
		$output = array();

		$args = array(
			'posts_per_page' => 200, // phpcs:ignore
			'post_type'      => 'xts-size-guide',
		);

		$posts = get_posts( $args );

		foreach ( $posts as $post ) { // phpcs:ignore
			if ( 'default' === $style ) {
				$output[ $post->ID ] = array(
					'name'  => $post->post_title,
					'value' => $post->ID,
				);
			} elseif ( 'elementor' === $style ) {
				$output[ $post->ID ] = $post->post_title;
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'xts_get_headers_array' ) ) {
	/**
	 * Get custom header array created with header builder
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $options Is function called in theme options.
	 *
	 * @return array
	 */
	function xts_get_headers_array( $options = false ) {
		if ( $options ) {
			$list = get_option( 'xts_saved_headers' );
		} else {
			$list = xts_get_header_builder()->list->get_all();
		}

		$output = array();

		foreach ( $list as $key => $header ) {
			$output[ $key ] = array(
				'name'  => $header['name'],
				'value' => $key,
			);
		}

		return $output;
	}
}

if ( ! function_exists( 'xts_get_pages_array' ) ) {
	/**
	 * Get all pages array
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_pages_array() {
		$pages = array();

		foreach ( get_pages() as $page ) {
			$pages[ $page->ID ] = array(
				'name'  => $page->post_title,
				'value' => $page->ID,
			);
		}

		return $pages;
	}
}

if ( ! function_exists( 'xts_return_empty' ) ) {
	/**
	 * Return empty value.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function xts_return_empty() {
		return '';
	}
}

if ( ! function_exists( 'xts_get_rtl_inverted_string' ) ) {
	/**
	 * Invert RTL position.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string String.
	 *
	 * @return string
	 */
	function xts_get_rtl_inverted_string( $string ) {
		if ( is_rtl() && strpos( $string, 'left' ) ) {
			return str_replace( 'left', 'right', $string );
		}

		if ( is_rtl() && strpos( $string, 'right' ) ) {
			return str_replace( 'right', 'left', $string );
		}

		return $string;
	}
}

if ( ! function_exists( 'xts_get_current_url' ) ) {
	/**
	 * Get current url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function xts_get_current_url() {
		global $wp;

		return home_url( $wp->request );
	}
}

if ( ! function_exists( 'xts_elementor_no_gap' ) ) {
	/**
	 * No gap option.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function xts_elementor_no_gap() {
		$negative_gap = get_post_meta( get_the_ID(), '_xts_negative_gap', true );

		if ( 'inherit' === $negative_gap || ! $negative_gap ) {
			$negative_gap = xts_get_opt( 'negative_gap', 'enabled' );
		}

		return $negative_gap;
	}
}

if ( ! function_exists( 'array_key_first' ) ) {
	/**
	 * PHP Helper function.
	 *
	 * @param array $arr Array.
	 *
	 * @return int|string|null
	 */
	function array_key_first( array $arr ) {
		foreach ( $arr as $key => $unused ) {
			return $key;
		}

		return null;
	}
}

if ( ! function_exists( 'xts_elementor_get_content' ) ) {
	/**
	 * Retrieve builder content for display.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id The post ID.
	 *
	 * @return string
	 */
	function xts_elementor_get_content( $id ) {
		$inline_css = true;
		$post       = new Post( $id );
		$meta       = $post->get_meta();

		ob_start();

		if ( $post::CSS_STATUS_FILE === $meta['status'] && apply_filters( 'xts_elementor_content_file_css', true ) ) {
			?>
			<link rel="stylesheet" id="elementor-post-<?php echo esc_attr( $id ); ?>-css" href="<?php echo esc_url( $post->get_url() ); ?>" type="text/css" media="all">
			<?php
			$inline_css = false;
		}

		echo Plugin::$instance->frontend->get_builder_content_for_display( $id, $inline_css );

		wp_deregister_style( 'elementor-post-' . $id );
		wp_dequeue_style( 'elementor-post-' . $id );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'xts_get_hosted_video' ) ) {
	/**
	 * Get hosted video
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @return string
	 */
	function xts_get_hosted_video( $element_args ) {
		if ( isset( $element_args['video_hosted_url']['url'] ) && ! $element_args['video_hosted_url']['url'] || ! isset( $element_args['video_hosted_url'] ) ) {
			return '';
		}

		$video_params = array();

		if ( 'yes' === $element_args['video_loop'] ) {
			$video_params['loop'] = '';
		}

		if ( 'yes' === $element_args['video_mute'] ) {
			$video_params['muted'] = 'muted';
		}

		if ( 'yes' === $element_args['video_controls'] ) {
			$video_params['controls'] = '';
		}

		if ( 'yes' === $element_args['video_autoplay'] && 'without' === $element_args['video_action_button'] ) {
			$video_params['autoplay'] = '';
		}

		$video_attributes = 'data-lazy-load="' . esc_url( $element_args['video_hosted_url']['url'] ) . '"';

		ob_start();

		?>
		<video <?php echo wp_kses( $video_attributes, 'xts_media' ); ?> <?php echo Utils::render_html_attributes( $video_params ); // phpcs:ignore ?>></video>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'xts_get_link_without_http' ) ) {
	/**
	 * Remove http or https from the link.
	 *
	 * @since 1.0.0
	 *
	 * @param string $link Link.
	 *
	 * @return string
	 */
	function xts_get_link_without_http( $link ) {
		return preg_replace( '#^https?:#', '', $link );
	}
}

if ( ! function_exists( 'xts_get_svg' ) ) {
	/**
	 * Get svg
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Image name.
	 * @param string $id   Image id.
	 * @param string $url  File url.
	 *
	 * @return string
	 */
	function xts_get_svg( $name = '', $id = '', $url = '' ) {
		if ( $name ) {
			$file = XTS_ASSETS_IMAGES_URL . '/' . $name . '.svg';
		} elseif ( $url ) {
			$file = $url;
		} else {
			return '';
		}

		$content   = xts_get_svg_content( $file );
		$start_tag = '<svg';

		if ( $id ) {
			$pattern = '/id="(\w)+"/';
			if ( preg_match( $pattern, $content ) ) {
				$content = preg_replace( $pattern, 'id="' . $id . '"', $content, 1 );
			} else {
				$content = preg_replace( '/<svg/', '<svg id="' . $id . '"', $content );
			}
		}

		// Strip doctype.
		$position = strpos( $content, $start_tag );

		return substr( $content, $position );
	}
}

if ( ! function_exists( 'xts_get_svg_content' ) ) {
	/**
	 * Get svg content
	 *
	 * @since 1.0.0
	 *
	 * @param integer $file File path.
	 *
	 * @return string
	 */
	function xts_get_svg_content( $file ) {
		if ( ! xts_is_core_module_exists() ) {
			return '';
		}

		if ( ! apply_filters( 'xts_svg_cache', true ) ) {
			return xts_get_content_file( $file );
		}

		$file_path = array_reverse( explode( '/', $file ) );
		$slug      = 'xts-svg-' . $file_path[2] . '-' . $file_path[1] . '-' . $file_path[0];
		$content   = get_transient( $slug );

		if ( ! $content ) {
			$get_contents_file = xts_get_content_file( $file );

			if ( strstr( $get_contents_file, '<svg' ) ) {
				if ( apply_filters( 'xts_svg_unique_ids', true ) ) {
					$get_contents_file = xts_svg_unique_ids( $get_contents_file );
				}
				$content = xts_compress( $get_contents_file );
				set_transient( $slug, $content, apply_filters( 'xts_svg_cache_time', 60 * 60 * 24 * 7 ) );
			}
		}

		return xts_decompress( $content );
	}
}

if ( ! function_exists( 'xts_svg_unique_ids' ) ) {
	/**
	 * Replace all IDs in the SVG content.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $content SVG content.
	 *
	 * @return string
	 */
	function xts_svg_unique_ids( $content ) {
		$matches = array();
		$result  = preg_match_all( '/id="\w+/', $content, $matches );

		if ( $result > 0 && isset( $matches[0] ) ) {
			$old_ids = array();
			$new_ids = array();

			foreach ( $matches[0] as $id ) {
				$unique_suffix = wp_rand( 1000, 9999 );
				$id            = substr( $id, 4 );
				$new_id        = $id . '_' . $unique_suffix;

				array_unshift( $old_ids, $id );
				array_unshift( $new_ids, $new_id );
			}

			$content = str_replace( array_unique( $old_ids ), array_unique( $new_ids ), $content );
		}

		return $content;
	}
}

if ( ! function_exists( 'xts_has_post_thumbnail' ) ) {
	/**
	 * Is post has thumbnail
	 *
	 * @since 1.0.0
	 *
	 * @param integer $post_id   Post id.
	 * @param bool    $is_single Is single.
	 *
	 * @return bool
	 */
	function xts_has_post_thumbnail( $post_id, $is_single = false ) {
		$blog_single_design = xts_get_opt( 'blog_single_design' );
		$post_format        = get_post_format();
		$gallery            = get_post_meta( $post_id, '_xts_post_gallery', true );
		$audio              = get_post_meta( $post_id, '_xts_post_audio_url', true );
		$link               = get_post_meta( $post_id, '_xts_post_link', true );
		$quote              = get_post_meta( $post_id, '_xts_post_quote', true );
		$has_on_single      = is_singular( 'post' ) && $is_single && 'page-title' !== $blog_single_design;
		$has_on_grid        = ! $is_single && has_post_thumbnail();

		return $has_on_grid || xts_post_have_video( $post_id ) || ( $quote && $has_on_single ) || ( $link && $has_on_single ) || $audio || ( 'gallery' === $post_format && $gallery ) || ( $has_on_single && has_post_thumbnail() );
	}
}

if ( ! function_exists( 'xts_post_have_video' ) ) {
	/**
	 * Is video added to post
	 *
	 * @since 1.0.0
	 *
	 * @param integer $post_id Post id.
	 *
	 * @return bool
	 */
	function xts_post_have_video( $post_id ) {
		$video_mp4     = get_post_meta( $post_id, '_xts_post_video_mp4', true );
		$video_webm    = get_post_meta( $post_id, '_xts_post_video_webm', true );
		$video_ogg     = get_post_meta( $post_id, '_xts_post_video_ogg', true );
		$video_youtube = get_post_meta( $post_id, '_xts_post_video_youtube', true );
		$video_vimeo   = get_post_meta( $post_id, '_xts_post_video_vimeo', true );

		return ( isset( $video_mp4['id'] ) && $video_mp4['id'] ) || ( isset( $video_webm['id'] ) && $video_webm['id'] ) || ( isset( $video_ogg['id'] ) && $video_ogg['id'] ) || $video_youtube || $video_vimeo;
	}
}

if ( ! function_exists( 'xts_get_pretty_number' ) ) {
	/**
	 * Gives a readable view to likes, comments, followers count.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $number Likes, comments or followers count.
	 *
	 * @return int|string
	 */
	function xts_get_pretty_number( $number = 0 ) {
		$number = (int) $number;

		if ( $number > 1000000 ) {
			return floor( $number / 1000000 ) . 'M';
		}

		if ( $number > 2000 ) {
			return floor( $number / 1000 ) . 'k';
		}

		return $number;
	}
}

if ( ! function_exists( 'xts_get_animations_array' ) ) {
	/**
	 * Get theme animations array
	 *
	 * @since 1.0.0
	 *
	 * @param string $style Array style.
	 *
	 * @return array
	 */
	function xts_get_animations_array( $style = 'elementor' ) {
		$animations = xts_get_available_options( 'animations' );

		if ( 'default' === $style ) {
			foreach ( $animations as $key => $value ) {
				$animations[ $key ] = array(
					'name'  => $value,
					'value' => $key,
				);
			}
		}

		return $animations;
	}
}

if ( ! function_exists( 'xts_extract_numbers_from_string' ) ) {
	/**
	 * Get numbers from string
	 *
	 * @since 1.0.0
	 *
	 * @param string $string String.
	 *
	 * @return string
	 */
	function xts_extract_numbers_from_string( $string ) {
		$result = '';

		foreach ( str_split( $string ) as $char ) {
			if ( is_numeric( $char ) ) {
				$result .= $char;
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'xts_elementor_is_edit_mode' ) ) {
	/**
	 * Whether the edit mode is active.
	 *
	 * @since 1.0.0
	 */
	function xts_elementor_is_edit_mode() {
		if ( ! xts_is_elementor_installed() ) {
			return false;
		}

		return Plugin::$instance->editor->is_edit_mode();
	}
}

if ( ! function_exists( 'xts_elementor_is_preview_mode' ) ) {
	/**
	 * Whether the preview mode is active.
	 *
	 * @since 1.0.0
	 */
	function xts_elementor_is_preview_mode() {
		if ( ! xts_is_elementor_installed() ) {
			return false;
		}

		return Plugin::$instance->preview->is_preview_mode();
	}
}

if ( ! function_exists( 'xts_get_allowed_html' ) ) {
	/**
	 * Return allowed html tags
	 *
	 * @since 1.0.0
	 * @return array
	 */
	function xts_get_allowed_html() {
		return array(
			'br'     => array(),
			'i'      => array(),
			'b'      => array(),
			'u'      => array(),
			'em'     => array(),
			'strong' => array(),
			'span'   => array(
				'style' => true,
				'class' => true,
			),
			'a'      => array(
				'href'  => true,
				'class' => true,
			),
		);
	}
}

if ( ! function_exists( 'xts_is_social_buttons_enable' ) ) {
	/**
	 * Is social button enable
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Social button type.
	 *
	 * @return boolean
	 */
	function xts_is_social_buttons_enable( $type ) {
		if ( 'share' === $type && ( xts_get_opt( 'facebook_share' ) || xts_get_opt( 'twitter_share' ) || xts_get_opt( 'google_share' ) || xts_get_opt( 'pinterest_share' ) || xts_get_opt( 'ok_share' ) || xts_get_opt( 'whatsapp_share' ) || xts_get_opt( 'email_share' ) || xts_get_opt( 'vk_share' ) || xts_get_opt( 'telegram_share' ) || xts_get_opt( 'viber_share' ) ) ) {
			return true;
		}

		if ( 'follow' === $type && ( xts_get_opt( 'facebook_link' ) || xts_get_opt( 'twitter_link' ) || xts_get_opt( 'instagram_link' ) || xts_get_opt( 'pinterest_link' ) || xts_get_opt( 'youtube_link' ) || xts_get_opt( 'tumblr_link' ) || xts_get_opt( 'linkedin_link' ) || xts_get_opt( 'vimeo_link' ) || xts_get_opt( 'flickr_link' ) || xts_get_opt( 'github_link' ) || xts_get_opt( 'dribbble_link' ) || xts_get_opt( 'behance_link' ) || xts_get_opt( 'soundcloud_link' ) || xts_get_opt( 'spotify_link' ) || xts_get_opt( 'ok_link' ) || xts_get_opt( 'whatsapp_link' ) || xts_get_opt( 'vk_link' ) || xts_get_opt( 'snapchat_link' ) || xts_get_opt( 'telegram_link' ) || xts_get_opt( 'email_link' ) || xts_get_opt( 'tiktok_link' ) ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'xts_is_ajax' ) ) {
	/**
	 * Is ajax request
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_ajax() {
		if ( isset( $_REQUEST['action'] ) && 'xts_load_html_dropdowns' === $_REQUEST['action'] && xts_is_core_module_exists() ) { // phpcs:ignore
			return false;
		}

		$request_headers = function_exists( 'getallheaders' ) ? getallheaders() : array();

		if ( xts_is_elementor_installed() && xts_elementor_is_edit_mode() ) {
			return apply_filters( 'xts_is_ajax', false );
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return apply_filters( 'xts_is_ajax', 'wp-ajax' );
		}

		if ( isset( $request_headers['x-pjax'] ) || isset( $request_headers['X-PJAX'] ) || isset( $request_headers['X-Pjax'] ) || ( xts_is_core_module_exists() && xts_is_pjax() ) ) {
			return apply_filters( 'xts_is_ajax', 'full-page' );
		}

		if ( isset( $_REQUEST['xts_ajax'] ) ) { // phpcs:ignore
			return apply_filters( 'xts_is_ajax', 'fragments' );
		}

		return apply_filters( 'xts_is_ajax', false );
	}
}

if ( ! function_exists( 'xts_is_blog_archive' ) ) {
	/**
	 * If current page blog archive
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_blog_archive() {
		return is_home() || is_search() || is_tag() || is_category() || is_date() || is_author();
	}
}

if ( ! function_exists( 'xts_needs_header' ) ) {
	/**
	 * If page needs header
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_needs_header() {
		return ! is_singular( 'xts-html-block' ) && ! is_singular( 'xts-slide' ) && ! xts_is_maintenance_page();
	}
}

if ( ! function_exists( 'xts_needs_footer' ) ) {
	/**
	 * If page needs footer
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_needs_footer() {
		return ! is_singular( 'xts-html-block' ) && ! is_singular( 'xts-slide' ) && ! xts_is_maintenance_page();
	}
}

if ( ! function_exists( 'xts_get_preview_product' ) ) {
	/**
	 * Get some product post for preview
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	function xts_get_preview_product() {
		$id = xts_get_opt( 'single_product_custom_template_preview_product' );

		if ( ! $id ) {
			$args = array(
				'orderby'        => 'rand',
				'posts_per_page' => '1',
				'post_type'      => 'product',
			);

			$random_product = new WP_Query( $args );

			while ( $random_product->have_posts() ) {
				$random_product->the_post();
				$id = get_the_ID();
			}

			wp_reset_postdata();
		}

		return get_post( $id );
	}
}

if ( ! function_exists( 'xts_is_maintenance_page' ) ) {
	/**
	 * Is maintenance enabled and current page is maintenance page
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_maintenance_page() {
		return xts_get_opt( 'maintenance_mode' ) && xts_get_opt( 'maintenance_page' ) && is_page( xts_get_opt( 'maintenance_page' ) );
	}
}

if ( ! function_exists( 'xts_reset_loop' ) ) {
	/**
	 * Reset loop
	 *
	 * @since 1.0.0
	 */
	function xts_reset_loop() {
		unset( $GLOBALS['xts_loop'] );
		xts_setup_loop();
	}

	add_action( 'woocommerce_after_shop_loop', 'xts_reset_loop', 1000 );
	add_action( 'loop_end', 'xts_reset_loop', 1000 );
}

if ( ! function_exists( 'xts_get_loop_prop' ) ) {
	/**
	 * Get loop prop
	 *
	 * @since 1.0.0
	 *
	 * @param string $prop    Loop property.
	 * @param mixed  $default Loop value.
	 *
	 * @return mixed
	 */
	function xts_get_loop_prop( $prop, $default = '' ) {
		xts_setup_loop();

		return isset( $GLOBALS['xts_loop'], $GLOBALS['xts_loop'][ $prop ] ) ? $GLOBALS['xts_loop'][ $prop ] : $default;
	}
}

if ( ! function_exists( 'xts_set_loop_prop' ) ) {
	/**
	 * Set loop prop
	 *
	 * @since 1.0.0
	 *
	 * @param string $prop  Loop property.
	 * @param mixed  $value Loop value.
	 */
	function xts_set_loop_prop( $prop, $value = '' ) {
		if ( ! isset( $GLOBALS['xts_loop'] ) ) {
			xts_setup_loop();
		}

		$GLOBALS['xts_loop'][ $prop ] = $value;
	}
}

if ( ! function_exists( 'xts_is_svg' ) ) {
	/**
	 * Is SVG image
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Image url.
	 *
	 * @return bool
	 */
	function xts_is_svg( $url ) {
		return substr( $url, - 3, 3 ) === 'svg';
	}
}

if ( ! function_exists( 'xts_is_woocommerce_installed' ) ) {
	/**
	 * Check if WooCommerce is activated
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_woocommerce_installed() {
		return class_exists( 'WooCommerce' );
	}
}

if ( ! function_exists( 'xts_is_scpo_installed' ) ) {
	/**
	 * Check if Simple Custom Post Order is activated
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_scpo_installed() {
		return class_exists( 'SCPO_Engine' );
	}
}

if ( ! function_exists( 'xts_is_elementor_installed' ) ) {
	/**
	 * Check if Elementor is activated
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_elementor_installed() {
		return did_action( 'elementor/loaded' );
	}
}

if ( ! function_exists( 'xts_is_elementor_pro_installed' ) ) {
	/**
	 * Check if Elementor PRO is activated
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_elementor_pro_installed() {
		return defined( 'ELEMENTOR_PRO_VERSION' );
	}
}

if ( ! function_exists( 'xts_is_contact_form_7_installed' ) ) {
	/**
	 * Check if Contact form 7 is activated
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_contact_form_7_installed() {
		return defined( 'WPCF7_PLUGIN' );
	}
}

if ( ! function_exists( 'xts_is_mailchimp_installed' ) ) {
	/**
	 * Check if Mailchimp is activated
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_mailchimp_installed() {
		return defined( 'MC4WP_VERSION' );
	}
}

if ( ! function_exists( 'xts_is_revslider_installed' ) ) {
	/**
	 * Check if Slider Revolution is activated
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_revslider_installed() {
		return defined( 'RS_PLUGIN_PATH' );
	}
}

if ( ! function_exists( 'xts_is_yoast_installed' ) ) {
	/**
	 * Check if Yoast is activated
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_yoast_installed() {
		return defined( 'WPSEO_VERSION' );
	}
}

if ( ! function_exists( 'xts_get_http_protocol' ) ) {
	/**
	 * Get protocol (http or https)
	 *
	 * @since 1.0.0
	 * @return string
	 */
	function xts_get_http_protocol() {
		if ( is_ssl() ) {
			return 'https';
		} else {
			return 'http';
		}
	}
}

if ( ! function_exists( 'xts_is_footer_empty' ) ) {
	/**
	 * Checks if there are widgets in the footer
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_footer_empty() {
		$is_empty = true;

		$footers = array(
			'footer-1',
			'footer-2',
			'footer-3',
			'footer-4',
			'footer-5',
			'footer-6',
			'footer-7',
		);

		foreach ( $footers as $footer ) {
			if ( is_active_sidebar( $footer ) ) {
				$is_empty = false;
			}
		}

		return $is_empty;
	}
}

if ( ! function_exists( 'xts_get_config' ) ) {
	/**
	 * Get config file
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Config name.
	 * @param string $from Where search config.
	 *
	 * @return mixed
	 */
	function xts_get_config( $name, $from = 'framework' ) {
		return Config::get_instance()->get_config( $name, $from );
	}
}

if ( ! function_exists( 'xts_get_typography_selectors' ) ) {
	/**
	 * Get selectors from config file.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $key Selector key.
	 *
	 * @return string
	 */
	function xts_get_typography_selectors( $key ) {
		$selectors = xts_get_config( 'selectors', 'theme' );

		if ( is_array( $key ) ) {
			$output = '';
			$index  = 0;
			foreach ( $key as $value ) {
				if ( ! isset( $selectors[ $value ] ) ) {
					continue;
				}

				if ( $index > 0 ) {
					$output .= ', ';
				}
				$output .= $selectors[ $value ];
				$index ++;
			}

			return $output;
		}

		return isset( $selectors[ $key ] ) ? $selectors[ $key ] : '';
	}
}

if ( ! function_exists( 'xts_get_default_value' ) ) {
	/**
	 * Get default theme settings value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Value key.
	 *
	 * @return mixed
	 */
	function xts_get_default_value( $key ) {
		$default_values = xts_get_config( 'framework-defaults' );
		$theme_values   = xts_get_config( 'theme-defaults', 'theme' );

		if ( $theme_values ) {
			$default_values = wp_parse_args( $theme_values, $default_values );
		}

		return isset( $default_values[ $key ] ) ? $default_values[ $key ] : '';
	}
}

if ( ! function_exists( 'xts_get_js_scripts' ) ) {
	/**
	 * Get js scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function xts_get_js_scripts() {
		$default_values = xts_get_config( 'framework-js-scripts' );
		$theme_values   = xts_get_config( 'theme-js-scripts', 'theme' );

		if ( $theme_values ) {
			$default_values = wp_parse_args( $theme_values, $default_values );
		}

		return $default_values;
	}
}

if ( ! function_exists( 'xts_get_available_options' ) ) {
	/**
	 * Get available options.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Value key.
	 *
	 * @return mixed
	 */
	function xts_get_available_options( $key ) {
		$default_values = xts_get_config( 'framework-available-options' );
		$theme_values   = xts_get_config( 'theme-available-options', 'theme' );

		if ( isset( $theme_values[ $key ] ) ) {
			$add_after = isset( $theme_values[ $key ]['add_after'] ) ? $theme_values[ $key ]['add_after'] : '';

			foreach ( $theme_values[ $key ] as $theme_key => $value ) {
				if ( strstr( $theme_key, '!' ) || 'add_after' === $theme_key ) {
					unset( $default_values[ $key ][ str_replace( '!', '', $theme_key ) ] );
					continue;
				}

				if ( $add_after ) {
					$default_values[ $key ] = xts_array_insert_after( $add_after, $default_values[ $key ], $theme_key, $value );
					$add_after              = $theme_key;
				} else {
					$default_values[ $key ][ $theme_key ] = $value;
				}
			}
		}

		unset( $default_values[ $key ]['add_after'] );

		return isset( $default_values[ $key ] ) ? $default_values[ $key ] : '';
	}
}

if ( ! function_exists( 'xts_array_insert_after' ) ) {
	/**
	 * Inserts a new key/value after the key in the array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key       The key to insert after.
	 * @param array  $array     An array to insert in to.
	 * @param string $new_key   The key to insert.
	 * @param mixed  $new_value An value to insert.
	 *
	 * @return array
	 */
	function xts_array_insert_after( $key, $array, $new_key, $new_value ) {
		if ( ! array_key_exists( $key, $array ) ) {
			return $array;
		}

		$new = array();

		foreach ( $array as $k => $value ) {
			$new[ $k ] = $value;

			if ( (string) $k === (string) $key ) {
				$new[ $new_key ] = $new_value;
			}
		}

		return $new;
	}
}

if ( ! function_exists( 'xts_get_footer_grid' ) ) {
	/**
	 * Get footer grid.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	function xts_get_footer_grid() {
		$default_values = xts_get_config( 'framework-footer-grid' );
		$theme_values   = xts_get_config( 'theme-footer-grid', 'theme' );

		if ( $theme_values ) {
			foreach ( $theme_values as $key => $value ) {
				$default_values[ $key ] = $value;
			}
		}

		return $default_values;
	}
}

if ( ! function_exists( 'xts_get_overridable_file' ) ) {
	/**
	 * Get config file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path File path.
	 */
	function xts_get_file( $path ) {
		$file_path = xts_get_theme_root_path( $path );

		if ( $file_path ) {
			include $file_path;
		} else {
			wp_die( esc_html__( 'File is not found', 'xts-theme' ) . ' ' . esc_html( $path ) );
		}
	}
}

if ( ! function_exists( 'xts_theme_supports' ) ) {
	/**
	 * Does this theme supports some feature.
	 *
	 * @since 1.0.0
	 *
	 * @param string $feature Feature key.
	 *
	 * @return bool
	 */
	function xts_theme_supports( $feature ) {
		return Theme_Features::supports( $feature );
	}
}

if ( ! function_exists( 'xts_add_theme_supports' ) ) {
	/**
	 * Add this theme support.
	 *
	 * @since 1.0.0
	 *
	 * @param string $feature Feature key.
	 */
	function xts_add_theme_supports( $feature ) {
		Theme_Features::add( $feature );
	}
}

if ( ! function_exists( 'xts_remove_theme_supports' ) ) {
	/**
	 * Remove this theme support.
	 *
	 * @since 1.0.0
	 *
	 * @param string $feature Feature key.
	 */
	function xts_remove_theme_supports( $feature ) {
		Theme_Features::remove( $feature );
	}
}

if ( ! function_exists( 'xts_get_token' ) ) {
	/**
	 * Get activation token.
	 *
	 * @since 1.0.0
	 */
	function xts_get_token() {
		$slug = get_option( 'xts_all_themes_license' ) ? get_option( 'xts_all_themes_license' ) : XTS_THEME_SLUG;
		return get_option( 'xts_' . $slug . '_token' );
	}
}

if ( ! function_exists( 'xts_get_license_key' ) ) {
	/**
	 * Get activation token.
	 *
	 * @since 1.0.0
	 */
	function xts_get_license_key() {
		$slug = get_option( 'xts_all_themes_license' ) ? get_option( 'xts_all_themes_license' ) : XTS_THEME_SLUG;
		return get_option( 'xts_' . $slug . '_license_key' );
	}
}

if ( ! function_exists( 'xts_get_license_data' ) ) {
	/**
	 * Get license data.
	 *
	 * @since 1.0.0
	 */
	function xts_get_license_data() {
		$slug               = get_option( 'xts_all_themes_license' ) ? get_option( 'xts_all_themes_license' ) : XTS_THEME_SLUG;
		$data               = get_option( 'xts_' . $slug . '_license_data' );
		$expire             = isset( $data['next_payment_date'] ) ? strtotime( $data['next_payment_date'] ) : '';
		$today              = strtotime( 'today midnight' );
		$end_date           = isset( $data['end_date'] ) ? strtotime( $data['end_date'] ) : '';
		$data['is_expired'] = $today >= $expire;

		if ( $data['is_expired'] ) {
			$data['status'] = 'expired';
		}

		if ( $end_date ) {
			$data['status']     = 'pending cancellation';
			$data['is_expired'] = false;
		}

		return $data;
	}
}

if ( ! function_exists( 'xts_is_activated_license' ) ) {
	/**
	 * Is theme activated.
	 *
	 * @since 1.0.0
	 */
	function xts_is_activated_license() {
		$slug = get_option( 'xts_all_themes_license' ) ? get_option( 'xts_all_themes_license' ) : XTS_THEME_SLUG;
		return get_option( 'xts_' . $slug . '_license_active' );
	}
}

if ( ! function_exists( 'pretty_bytes' ) ) {
	/**
	 * Is theme activated.
	 *
	 * @since 1.0.0
	 *
	 * @param string $bytes Bytes.
	 * @param array  $units Units.
	 *
	 * @return string
	 */
	function xts_pretty_bytes( $bytes, $units = array( 'B', 'KB', 'MB', 'GB', 'TB' ) ) {
		$bytes = max( $bytes, 0 );
		$pow   = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow   = min( $pow, count( $units ) - 1 );

		$bytes /= pow( 1024, $pow );

		return round( $bytes, 2 ) . ' ' . $units[ $pow ];
	}
}

if ( ! function_exists( 'xts_get_template_part' ) ) {
	/**
	 * Loads a template part into a template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug name for the generic template.
	 */
	function xts_get_template_part( $slug ) {
		$file_path = XTS_ABSPATH . $slug . '.php';

		if ( file_exists( $file_path ) ) {
			load_template( $file_path, false );
		}
	}
}

if ( ! function_exists( 'xts_get_framework_path' ) ) {
	/**
	 * Get full path to the framework file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path File path.
	 *
	 * @return string
	 */
	function xts_get_framework_path( $path ) {
		return XTS_FRAMEWORK_ABSPATH . $path . '.php';
	}
}

if ( ! function_exists( 'xts_get_theme_path' ) ) {
	/**
	 * Get full path to the theme file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path File path.
	 *
	 * @return string
	 */
	function xts_get_theme_path( $path ) {
		return XTS_ABSPATH . 'theme/' . $path . '.php';
	}
}

if ( ! function_exists( 'xts_get_theme_root_path' ) ) {
	/**
	 * Get full path to the theme root.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path File path.
	 *
	 * @return string
	 */
	function xts_get_theme_root_path( $path ) {
		return apply_filters( 'xts_theme_root_path', XTS_ABSPATH . '/' . $path . '.php', $path );
	}
}

if ( ! function_exists( 'xts_get_child_theme_path' ) ) {
	/**
	 * Get full path to the child theme file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path File path.
	 *
	 * @return string
	 */
	function xts_get_child_theme_path( $path ) {
		return get_stylesheet_directory() . '/' . $path . '.php';
	}
}

if ( ! function_exists( 'xts_get_theme_info' ) ) {
	/**
	 * Returns information about a theme such as name, version
	 *
	 * @since 1.0.0
	 *
	 * @param string $parameter The parameter you want to get.
	 *
	 * @return string
	 */
	function xts_get_theme_info( $parameter ) {
		$theme_info = wp_get_theme();

		if ( is_child_theme() ) {
			$parent = $theme_info->parent();

			if ( $parent ) {
				$theme_info = wp_get_theme( $parent->__get( 'template' ) );
			}
		}

		return $theme_info->get( $parameter );
	}
}

if ( ! function_exists( 'xts_is_elementor_full_width' ) ) {
	/**
	 * Check if Elementor full width.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	function xts_is_elementor_full_width() {
		$page_template = get_post_meta( xts_get_page_id(), '_wp_page_template', true );

		if ( xts_is_elementor_pro_installed() && xts_is_elementor_installed() ) {
			$manager = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' )->get_conditions_manager();

			if ( $manager->get_documents_for_location( 'single' ) || $manager->get_documents_for_location( 'archive' ) ) {
				$page_template = 'elementor_header_footer';
			}
		}

		return 'elementor_header_footer' === $page_template && 'enabled' !== xts_elementor_no_gap();
	}
}

if ( ! function_exists( 'ar' ) ) {
	/**
	 * Prints human-readable information about a variable
	 *
	 * @since 1.0.0
	 *
	 * @param mixed   $ar Array.
	 * @param boolean $x  Is print only with X Get.
	 */
	function ar( $ar, $x = false ) {
		if ( $x ) {
			if ( isset( $_GET['x'] ) ) { // phpcs:ignore
				echo '<pre>';
				print_r( $ar ); // phpcs:ignore
				echo '</pre>';
			}
		} else {
			echo '<pre>';
			print_r( $ar ); // phpcs:ignore
			echo '</pre>';
		}
	}
}

if ( ! function_exists( 'xts_get_content_classes' ) ) {
	/**
	 * Get CSS classes for the content element
	 *
	 * @since 1.0.0
	 * @return string
	 */
	function xts_get_content_classes() {
		return Framework::get_instance()->layout->get_content_classes();
	}
}

if ( ! function_exists( 'xts_get_sidebar_classes' ) ) {
	/**
	 * Get CSS classes for the sidebar element
	 *
	 * @since 1.0.0
	 * @return string
	 */
	function xts_get_sidebar_classes() {
		return Framework::get_instance()->layout->get_sidebar_classes();
	}
}

if ( ! function_exists( 'xts_get_content_column_width' ) ) {
	/**
	 * Get content column width
	 *
	 * @since 1.0.0
	 * @return integer
	 */
	function xts_get_content_column_width() {
		return Framework::get_instance()->layout->get_content_column_width();
	}
}

if ( ! function_exists( 'xts_get_sidebar_column_width' ) ) {
	/**
	 * Get sidebar column width
	 *
	 * @since 1.0.0
	 * @return integer
	 */
	function xts_get_sidebar_column_width() {
		return Framework::get_instance()->layout->get_sidebar_column_width();
	}
}

if ( ! function_exists( 'xts_get_page_layout' ) ) {
	/**
	 * Get page layout
	 *
	 * @since 1.0.0
	 * @return string
	 */
	function xts_get_page_layout() {
		return Framework::get_instance()->layout->get_page_layout();
	}
}

if ( ! function_exists( 'xts_get_sidebar_name' ) ) {
	/**
	 * Get sidebar name
	 *
	 * @since 1.0.0
	 * @return string
	 */
	function xts_get_sidebar_name() {
		return Framework::get_instance()->layout->get_sidebar_name();
	}
}

if ( ! function_exists( 'xts_get_image_url' ) ) {
	/**
	 * Get image url
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id             Image id.
	 * @param string  $image_size_key Settings key for image size.
	 * @param array   $settings       Control settings.
	 *
	 * @return string
	 */
	function xts_get_image_url( $id, $image_size_key, $settings ) {
		if ( ! xts_is_elementor_installed() ) {
			return wp_get_attachment_image_src( $id, $settings[ $image_size_key . '_size' ] )[0];
		}

		return Group_Control_Image_Size::get_attachment_image_src( $id, $image_size_key, $settings );
	}
}

if ( ! function_exists( 'xts_get_image_html' ) ) {
	/**
	 * Get image url
	 *
	 * @since 1.0.0
	 *
	 * @param array  $settings       Control settings.
	 * @param string $image_size_key Settings key for image size.
	 *
	 * @return string
	 */
	function xts_get_image_html( $settings, $image_size_key = '' ) {
		if ( ! xts_is_elementor_installed() ) {
			return wp_get_attachment_image( $settings[ $image_size_key ]['id'], $settings[ $image_size_key . '_size' ] );
		}

		return Group_Control_Image_Size::get_attachment_image_html( $settings, $image_size_key );
	}
}

if ( ! function_exists( 'xts_get_elementor_placeholder_image_src' ) ) {
	/**
	 * Get placeholder image source.
	 *
	 * @since 1.0.0
	 *
	 * @param string $size Placeholder size.
	 *
	 * @return string
	 */
	function xts_get_elementor_placeholder_image_src( $size = 'thumbnail' ) {
		return XTS_THEME_URL . '/images/placeholder-' . $size . '.jpg';
	}
}

if ( ! function_exists( 'xts_set_cookie' ) ) {
	/**
	 * Set cookies.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name  Name.
	 * @param string $value Value.
	 */
	function xts_set_cookie( $name, $value ) {
		$expire = time() + intval( apply_filters( 'xts_session_expiration', 60 * 60 * 24 * 7 ) );
		setcookie( $name, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, false, false );
		$_COOKIE[ $name ] = $value;
	}
}

if ( ! function_exists( 'xts_get_cookie' ) ) {
	/**
	 * Get cookie.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name.
	 *
	 * @return string
	 */
	function xts_get_cookie( $name ) {
		return isset( $_COOKIE[ $name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) ) : false; // phpcs:ignore
	}
}

if ( ! function_exists( 'xts_get_template' ) ) {
	/**
	 * Loads a template part into a template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments.
	 * @param string $module_name   Module name.
	 * @param string $template_path Template path.
	 */
	function xts_get_template( $template_name, $args = array(), $module_name = '', $template_path = '' ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args ); // phpcs:ignore
		}

		$template = xts_locate_template( $template_name, $module_name, $template_path );

		include $template;
	}
}

if ( ! function_exists( 'xts_get_template_html' ) ) {
	/**
	 * Like xts_get_template, but returns the HTML instead of outputting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments.
	 * @param string $module_name   Module name.
	 * @param string $template_path Template path.
	 *
	 * @return string
	 */
	function xts_get_template_html( $template_name, $args = array(), $module_name = '', $template_path = '' ) {
		ob_start();
		xts_get_template( $template_name, $args, $module_name, $template_path );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'xts_locate_template' ) ) {
	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_name Template name.
	 * @param string $module_name   Module name.
	 * @param string $template_path Template path.
	 *
	 * @return string
	 */
	function xts_locate_template( $template_name, $module_name, $template_path ) {
		if ( $module_name ) {
			return XTS_FRAMEWORK_ABSPATH . 'modules/' . $module_name . '/templates/' . $template_name;
		} else {
			return XTS_THEME_DIR . '/' . $template_path . '/' . $template_name;
		}
	}
}

if ( ! function_exists( 'xts_is_core_module_exists' ) ) {
	/**
	 * Checks whether active xts-core plugin.
	 *
	 * @return bool
	 */
	function xts_is_core_module_exists() {
		return defined( 'XTS_CORE_VERSION' ) || Modules::is_module_exists( 'core' );
	}
}

if ( ! function_exists( 'xts_is_build_for_space' ) ) {
	/**
	 * Check is build type.
	 *
	 * @return bool
	 */
	function xts_is_build_for_space() {
		return defined( 'XTS_BUILD_TYPE' ) && 'space' === XTS_BUILD_TYPE;
	}
}

if ( ! function_exists( 'xts_get_link_by_key' ) ) {
	/**
	 * Get links.
	 *
	 * @param string $key Link key.
	 *
	 * @return string
	 */
	function xts_get_link_by_key( $key ) {
		$links = xts_get_config( 'links-theme-setting' );
		return isset( $links[ $key ] ) ? $links[ $key ] : '';
	}
}

if ( ! function_exists( 'xts_kses_allowed_html' ) ) {
	/**
	 * Cleans the tag.
	 *
	 * @return array
	 */
	function xts_kses_allowed_html( $tags, $context ) {
		switch ( $context ) {
			case 'xts_notice':
				$tags = array(
					'a'      => array(
						'href'     => true,
						'rel'      => true,
						'target'   => true,
						'property' => true,
					),
					'strong' => array(),
					'br'     => array(),
				);
				break;
			case 'xts_table':
				$tags = array(
					'span' => array(
						'style' => true,
					),
					'br'   => array(),
				);
				break;
			case 'xts_breadcrumbs':
				$tags = array(
					'a'    => array(
						'href'     => true,
						'rel'      => true,
						'target'   => true,
						'property' => true,
					),
					'span' => array(
						'class'  => true,
						'typeof' => true,
					),
				);
				break;
			case 'xts_widget':
				$tags = array(
					'div'  => array(
						'id'    => true,
						'class' => true,
					),
					'span' => array(
						'class' => true,
					),
					'p'    => array(
						'class' => true,
					),
				);
				break;
			case 'xts_media':
				$tags = array(
					'iframe'  => array(
						'align'           => true,
						'src'             => true,
						'height'          => true,
						'width'           => true,
						'title'           => true,
						'class'           => true,
						'allow'           => true,
						'style'           => true,
						'data-lazy-load'  => true,
						'frameborder'     => true,
						'allowfullscreen' => true,
						'marginheight'    => true,
						'marginwidth'     => true,
						'hspace'          => true,
						'name'            => true,
						'scrolling'       => true,
						'seamless'        => true,
						'srcdoc'          => true,
						'vspace'          => true,
					),
					'img'     => array(
						'align'                   => true,
						'alt'                     => true,
						'border'                  => true,
						'class'                   => true,
						'data-xts-src'            => true,
						'data-src'                => true,
						'data-large_image'        => true,
						'data-large_image_width'  => true,
						'data-large_image_height' => true,
						'data-srcset'             => true,
						'data-caption'            => true,
						'height'                  => true,
						'hspace'                  => true,
						'ismap'                   => true,
						'longdesc'                => true,
						'loading'                 => true,
						'lowsrc'                  => true,
						'src'                     => true,
						'srcset'                  => true,
						'sizes'                   => true,
						'style'                   => true,
						'title'                   => true,
						'vspace'                  => true,
						'width'                   => true,
						'usemap'                  => true,
					),
					'i'       => array(
						'class' => true,
					),
					'svg'     => array(
						'width'       => true,
						'xmlns'       => true,
						'height'      => true,
						'viewBox'     => true,
						'fill'        => true,
						'class'       => true,
						'viewbox'     => true,
						'aria-hidden' => true,
						'role'        => true,
						'focusable'   => true,
					),
					'path'    => array(
						'fill'                => true,
						'fill-rule'           => true,
						'd'                   => true,
						'transform'           => true,
						'class'               => true,
						'data-old_color'      => true,
						'data-original'       => true,
						'stroke'              => true,
						'id'                  => true,
						'tabindex'            => true,
						'style'               => true,
						'clip-path'           => true,
						'clip-rule'           => true,
						'color'               => true,
						'color-interpolation' => true,
						'color-rendering'     => true,
						'cursor'              => true,
						'display'             => true,
						'fill-opacity'        => true,
						'filter'              => true,
						'mask'                => true,
						'opacity'             => true,
						'pointer-events'      => true,
						'shape-rendering'     => true,
						'stroke-dasharray'    => true,
						'stroke-linecap'      => true,
						'stroke-linejoin'     => true,
						'stroke-miterlimit'   => true,
						'stroke-opacity'      => true,
						'stroke-width'        => true,
						'vector-effect'       => true,
						'visibility'          => true,
					),
					'polygon' => array(
						'id'                  => true,
						'tabindex'            => true,
						'class'               => true,
						'style'               => true,
						'fill'                => true,
						'fill-opacity'        => true,
						'fill-rule'           => true,
						'points'              => true,
						'transform'           => true,
						'focusable'           => true,
						'clip-path'           => true,
						'clip-rule'           => true,
						'color'               => true,
						'color-interpolation' => true,
						'color-rendering'     => true,
						'cursor'              => true,
						'display'             => true,
						'filter'              => true,
						'mask'                => true,
						'opacity'             => true,
						'pointer-events'      => true,
						'shape-rendering'     => true,
						'stroke'              => true,
						'stroke-dasharray'    => true,
						'stroke-dashoffset'   => true,
						'stroke-linecap'      => true,
						'stroke-linejoin'     => true,
						'stroke-miterlimit'   => true,
						'stroke-opacity'      => true,
						'stroke-width'        => true,
						'vector-effect'       => true,
						'visibility'          => true,
					),
					'circle'  => array(
						'cx'           => true,
						'cy'           => true,
						'r'            => true,
						'stroke'       => true,
						'stroke-width' => true,
						'fill'         => true,
						'class'        => true,
						'style'        => true,
						'transform'    => true,
					),
					'g'       => array(
						'id' => true,
					),
					'div'     => array(
						'class' => true,
						'style' => true,
					),
				);
				break;
		}

		return $tags;
	}

	add_filter( 'wp_kses_allowed_html', 'xts_kses_allowed_html', 10, 2 );
}
