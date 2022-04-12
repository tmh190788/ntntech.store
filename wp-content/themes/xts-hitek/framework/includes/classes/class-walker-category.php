<?php
/**
 * Walker category class
 *
 * @since 1.0.0
 * @package xts
 */

namespace XTS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Walker category class
 */
class Walker_Category extends \Walker_Category {
	/**
	 * Menu style
	 *
	 * @var string
	 */
	private $menu_style = 'default';

	/**
	 * Constructor.
	 *
	 * @param string $menu_style Menu style.
	 */
	public function __construct( $menu_style ) {
		$this->menu_style = $menu_style;
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 2.1.0
	 *
	 * @see Walker::start_lvl()
	 *
	 * @param string $output Used to append additional content. Passed by reference.
	 * @param int    $depth  Optional. Depth of category. Used for tab indentation. Default 0.
	 * @param array  $args   Optional. An array of arguments. Will only append content if style argument
	 *                       value is 'list'. See wp_list_categories(). Default empty array.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= $indent . '<div class="xts-dropdown xts-dropdown-menu xts-style-default">';
		$output .= $indent . '<ul class="children xts-dropdown-inner xts-sub-menu">';
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @since 2.1.0
	 *
	 * @see Walker::end_lvl()
	 *
	 * @param string $output Used to append additional content. Passed by reference.
	 * @param int    $depth  Optional. Depth of category. Used for tab indentation. Default 0.
	 * @param array  $args   Optional. An array of arguments. Will only append content if style argument
	 *                       value is 'list'. See wp_list_categories(). Default empty array.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= $indent . '</ul>';
		$output .= $indent . '</div>';
	}

	/**
	 * Starts the element output.
	 *
	 * @since 2.1.0
	 *
	 * @see Walker::start_el()
	 *
	 * @param string $output   Used to append additional content (passed by reference).
	 * @param object $category Category data object.
	 * @param int    $depth    Optional. Depth of category in reference to parents. Default 0.
	 * @param array  $args     Optional. An array of arguments. See wp_list_categories(). Default empty array.
	 * @param int    $id       Optional. ID of the current category. Default 0.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		$cat_name = apply_filters(
			'list_cats',
			esc_attr( $category->name ),
			$category
		);

		// Don't generate an element if the category name is empty.
		if ( ! $cat_name ) {
			return;
		}

		$link = '<a href="' . esc_url( get_term_link( $category ) ) . '" class="xts-nav-link" data-val="' . esc_attr( $category->slug ) . '"';

		$link .= '>';

		$icon = get_term_meta( $category->term_id, '_xts_page_title_shop_category_icon', true );

		if ( isset( $icon['id'] ) && $icon['id'] ) {
			$link .= '<img src="' . esc_url( wp_get_attachment_image_url( $icon['id'] ) ) . '" alt="' . esc_attr( $category->cat_name ) . '" class="xts-nav-img" />';
		}

		$link .= '<span class="xts-nav-summary">';

		if ( 'underline-2' === $this->menu_style ) {
			$link .= '<span class="xts-nav-text"><span>' . $cat_name . '</span></span>';
		} else {
			$link .= '<span class="xts-nav-text">' . $cat_name . '</span>';
		}

		if ( $args['show_count'] ) {
			$link .= '<span class="xts-nav-count">' . number_format_i18n( $category->count ) . ' ' . _n( 'Product', 'Products', $category->count, 'xts-theme' ) . '</span>';
		}

		$link .= '</span>';
		$link .= '</a>';

		if ( 'list' === $args['style'] ) {
			$default_cat = get_option( 'default_product_cat' );
			$output     .= "\t<li";
			$css_classes = array(
				'xts-cat-item',
				'xts-cat-item-' . $category->term_id,
			);

			if ( $category->term_id == $default_cat ) { // phpcs:ignore
				$css_classes[] = 'xts-wc-default-cat';
			}

			if ( $args['walker']->has_children ) {
				$css_classes[] = 'xts-event-hover';
				$css_classes[] = 'xts-has-children';
			}

			if ( ! empty( $args['current_category'] ) ) {
				// 'current_category' can be an array, so we use `get_terms()`.
				$_current_terms = get_terms(
					$category->taxonomy,
					array(
						'include'    => $args['current_category'],
						'hide_empty' => false,
					)
				);

				foreach ( $_current_terms as $_current_term ) {
					if ( $category->term_id === $_current_term->term_id ) {
						$css_classes[] = 'xts-active';
					} elseif ( $category->term_id === $_current_term->parent ) {
						$css_classes[] = 'xts-active-parent';
					}

					while ( $_current_term->parent ) {
						if ( $category->term_id === $_current_term->parent ) {
							$css_classes[] = 'xts-active-ancestor';
							break;
						}

						$_current_term = get_term( $_current_term->parent, $category->taxonomy );
					}
				}
			}

			/**
			 * Filter the list of CSS classes to include with each category in the list.
			 *
			 * @since 4.2.0
			 *
			 * @see wp_list_categories()
			 *
			 * @param array  $css_classes An array of CSS classes to be applied to each list item.
			 * @param object $category    Category data object.
			 * @param int    $depth       Depth of page, used for padding.
			 * @param array  $args        An array of wp_list_categories() arguments.
			 */
			$css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );

			$output .= ' class="' . $css_classes . '"';
			$output .= ">$link\n";
		} elseif ( isset( $args['separator'] ) ) {
			$output .= "\t$link" . $args['separator'] . "\n";
		} else {
			$output .= "\t$link<br />\n";
		}
	}
}
