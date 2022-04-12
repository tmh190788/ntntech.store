<?php
/**
 * Navigation walker file
 *
 * @package xts
 * @version 1.0
 */

namespace XTS\Module\Mega_Menu;

use Walker_Nav_Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Navigation walker
 *
 * @since 1.0.0
 */
class Walker extends Walker_Nav_Menu {
	/**
	 * Color scheme.
	 *
	 * @var string
	 */
	private $color_scheme;

	/**
	 * Design.
	 *
	 * @var string
	 */
	private $design = 'default';

	/**
	 * Menu style.
	 *
	 * @var string
	 */
	private $menu_style;

	/**
	 * Constructor.
	 *
	 * @param string $menu_style Menu style.
	 */
	public function __construct( $menu_style ) {
		$this->color_scheme = 'default';
		$this->menu_style   = $menu_style;
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param mixed  $args   An array of arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent        = str_repeat( "\t", $depth );
		$classes       = '';
		$is_nav_mobile = strstr( $args->menu_class, 'xts-nav-mobile' );
		$is_nav_fs     = strstr( $args->menu_class, 'xts-nav-fs' );

		if ( 0 === $depth && ! $is_nav_mobile ) {
			xts_enqueue_js_script( 'menu-offsets' );

			if ( 'default' !== $this->color_scheme ) {
				$classes .= ' xts-scheme-' . $this->color_scheme;
			}
			$classes .= ' xts-style-' . $this->design;

			if ( $is_nav_fs ) {
				$output .= $indent . '<div class="xts-dropdown-menu' . $classes . '">';
			} else {
				$output .= $indent . '<div class="xts-dropdown xts-dropdown-menu' . $classes . '">';
			}
			$output .= $indent . '<div class="container xts-dropdown-inner">';
		}

		if ( 0 === $depth ) {
			if ( ( 'full' === $this->design || 'sized' === $this->design || 'container' === $this->design ) && ! $is_nav_mobile ) {
				$sub_menu_class = 'sub-menu xts-sub-menu row';
			} else {
				$sub_menu_class = 'sub-menu xts-sub-menu';
			}
		} else {
			if ( 'default' === $this->design && ! $is_nav_mobile ) {
				$sub_menu_class = 'sub-sub-menu xts-dropdown-inner';
			} else {
				$sub_menu_class = 'sub-sub-menu';
			}
		}

		if ( 'default' === $this->design && 0 !== $depth && ! $is_nav_mobile ) {
			if ( $is_nav_fs ) {
				$output .= '<div class="xts-dropdown-menu xts-style-default">';
			} else {
				$output .= '<div class="xts-dropdown xts-dropdown-menu xts-style-default">';
			}
		}

		$output .= $indent . '<ul class="' . $sub_menu_class . '">';

		$this->color_scheme = 'default';
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param mixed  $args   An array of arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= $indent . '</ul>';

		if ( 'default' === $this->design && 0 !== $depth && ! strstr( $args->menu_class, 'xts-nav-mobile' ) ) {
			$output .= '</div>';
		}

		if ( 0 === $depth && ! strstr( $args->menu_class, 'xts-nav-mobile' ) ) {
			$output .= $indent . '</div>';
			$output .= $indent . '</div>';
		}
	}

	/**
	 * Start the element output.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param mixed  $args   An array of arguments.
	 * @param int    $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$height    = '';
		$label_out = '';

		$indent    = $depth ? str_repeat( "\t", $depth ) : '';
		$classes   = ! $item->classes ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		$classes[] = 'item-level-' . $depth;

		$design        = get_post_meta( $item->ID, '_menu_item_design', true );
		$width         = get_post_meta( $item->ID, '_menu_item_width', true );
		$event         = get_post_meta( $item->ID, '_menu_item_event', true );
		$label         = get_post_meta( $item->ID, '_menu_item_label', true );
		$label_text    = get_post_meta( $item->ID, '_menu_item_label-text', true );
		$block         = get_post_meta( $item->ID, '_menu_item_block', true );
		$dropdown_ajax = get_post_meta( $item->ID, '_menu_item_dropdown-ajax', true );
		$opanchor      = get_post_meta( $item->ID, '_menu_item_opanchor', true );
		$color_scheme  = get_post_meta( $item->ID, '_menu_item_colorscheme', true );
		$image_id      = get_post_meta( $item->ID, '_menu_item_image', true );

		if ( $color_scheme ) {
			$this->color_scheme = $color_scheme;
		}

		if ( empty( $design ) ) {
			$design = 'default';
		}

		if ( ! is_object( $args ) ) {
			return;
		}

		if ( 0 === $depth && ! strstr( $args->menu_class, 'xts-nav-mobile' ) ) {
			if ( in_array( $design, array( 'sized', 'full', 'container' ), false ) ) { // phpcs:ignore
				$classes[] = 'xts-item-mega-menu';
			}
		}

		if ( 0 === $depth ) {
			$event = empty( $event ) ? 'hover' : $event;

			if ( 'click' === $event ) {
				xts_enqueue_js_script( 'menu-click-event' );
			}

			$classes[] = 'xts-event-' . $event;

			if ( $design ) {
				$this->design = $design;
			}
		}

		if ( 0 !== $depth && $args->walker->has_children && 'default' === $this->design && ! strstr( $args->menu_class, 'xts-nav-mobile' ) ) {
			$classes[] = 'xts-event-hover';
		}

		if ( $block && strstr( $args->menu_class, 'xts-nav-mobile' ) ) {
			$classes[] = 'xts-dropdown-html';
		}

		if ( 'enable' === $opanchor ) {
			xts_enqueue_js_script( 'menu-one-page' );
			$classes[] = 'xts-onepage-link';
			$key       = array_search( 'current-menu-item', $classes ); // phpcs:ignore
			if ( $key ) {
				unset( $classes[ $key ] );
			}
		}

		if ( ! empty( $label ) ) {
			$label_out = '<span class="xts-nav-label xts-color-' . $label . '">' . esc_attr( $label_text ) . '</span>';
		}

		if ( 0 === $depth && ! empty( $block ) && 'default' !== $this->design && ! $args->walker->has_children ) {
			$classes[] = 'menu-item-has-children';
		}

		if ( 'yes' === $dropdown_ajax && $block && 'default' !== $this->design ) {
			xts_enqueue_js_script( 'menu-dropdown-ajax' );
			$classes[] = 'xts-dropdown-ajax';
		}

		if ( ( 'full' === $this->design || 'sized' === $this->design || 'container' === $this->design ) && 1 === $depth && ! strstr( $args->menu_class, 'xts-nav-mobile' ) ) {
			$classes[] .= 'col-auto';
		}

		/**
		 * Filter the CSS class(es) applied to a menu item's list item element.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's list item element.
		 *
		 * @since 1.0.0
		 *
		 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';
		$atts['class']  = 'xts-nav-link';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );

				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . '>';

		/**
		 * Add background image to dropdown.
		 */
		if ( $image_id ) {
			$item_output .= wp_get_attachment_image(
				$image_id,
				'full',
				false,
				array(
					'class' => 'xts-nav-img',
				)
			);
		}

		if ( 0 === $depth ) {
			if ( 'underline-2' === $this->menu_style ) {
				$item_output .= '<span class="xts-nav-text"><span>' . $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after . '</span></span>';
			} else {
				$item_output .= '<span class="xts-nav-text">' . $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after . '</span>';
			}
		} else {
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		}

		$item_output .= $label_out;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$styles  = '';
		$classes = '';

		if ( 0 === $depth && ! strstr( $args->menu_class, 'xts-nav-mobile' ) && $block && ! $args->walker->has_children && 'default' !== $this->design ) { // phpcs:ignore
			xts_enqueue_js_script( 'menu-offsets' );
			if ( 'default' !== $this->color_scheme ) {
				$classes .= ' xts-scheme-' . $this->color_scheme;
			}
			$classes .= ' xts-style-' . $this->design;

			if ( strstr( $args->menu_class, 'xts-nav-fs' ) ) {
				$item_output .= $indent . '<div class="xts-dropdown-menu' . $classes . '">';
			} else {
				$item_output .= $indent . '<div class="xts-dropdown xts-dropdown-menu' . $classes . '">';
			}

			if ( 'yes' === $dropdown_ajax ) {
				$item_output .= '<div class="xts-dropdown-placeholder xts-fill" data-id="' . $block . '"></div>';
			}

			$item_output .= $indent . '<div class="container xts-dropdown-inner">';

			if ( 'yes' !== $dropdown_ajax ) {
				$item_output .= xts_html_block_shortcode( array( 'id' => $block ) );
			}

			$item_output .= $indent . '</div>';
			$item_output .= $indent . '</div>';

			$this->color_scheme = 'default';
		}

		if ( 0 === $depth && 'sized' === $this->design && ( ! empty( $height ) || ! empty( $width ) ) && ! strstr( $args->menu_class, 'xts-nav-mobile' ) ) {
			$styles .= '.menu-item-' . $item->ID . ' .xts-dropdown-menu.xts-style-sized {';
			$styles .= 'width: ' . $width . 'px; ';
			$styles .= '}';
		}

		if ( $styles && ! strstr( $args->menu_class, 'xts-nav-mobile' ) ) {
			$item_output .= '<style type="text/css">';
			$item_output .= $styles;
			$item_output .= '</style>';
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
