<?php
/**
 * Menu edit walker
 *
 * @package xts
 */

namespace XTS\Module\Mega_Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Menu edit walker
 *
 * @since 1.0.0
 */
class Edit_Walker extends \Walker_Nav_Menu_Edit {
	/**
	 * Starts the element output.
	 *
	 * @param string  $output Used to append additional content (passed by reference).
	 * @param object  $item   Menu item data object.
	 * @param integer $depth  Depth of menu item. Used for padding.
	 * @param array   $args   An object of wp_nav_menu() arguments.
	 * @param integer $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		parent::start_el( $output, $item, $depth, $args, $id );

		$item_id = $item->ID;

		$design        = get_post_meta( $item_id, '_menu_item_design', true );
		$width         = get_post_meta( $item_id, '_menu_item_width', true ) ? get_post_meta( $item_id, '_menu_item_width', true ) : '';
		$event         = get_post_meta( $item_id, '_menu_item_event', true );
		$label         = get_post_meta( $item_id, '_menu_item_label', true );
		$label_text    = get_post_meta( $item_id, '_menu_item_label-text', true );
		$block         = get_post_meta( $item_id, '_menu_item_block', true );
		$dropdown_ajax = get_post_meta( $item_id, '_menu_item_dropdown-ajax', true );
		$opanchor      = get_post_meta( $item_id, '_menu_item_opanchor', true );
		$color_scheme  = get_post_meta( $item_id, '_menu_item_colorscheme', true );
		$image_id      = get_post_meta( $item_id, '_menu_item_image', true );
		$classes       = $image_id ? ' xts-active' : '';

		$blocks = xts_get_html_blocks_array();

		ob_start();
		?>
			<div class="xts-mega-menu-custom-fields">
				<h4><?php esc_html_e( 'Custom fields [for theme]', 'xts-theme' ); ?></h4>

				<p class="description description-wide xts-design">
					<label for="edit-menu-item-design-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Design', 'xts-theme' ); ?>

						<select id="edit-menu-item-design-<?php echo esc_attr( $item_id ); ?>" data-field="xts-design" class="widefat" name="menu-item-design[<?php echo esc_attr( $item_id ); ?>]">
							<option value="default" <?php selected( $design, 'default', true ); ?>><?php esc_html_e( 'Default', 'xts-theme' ); ?></option>
							<option value="full" <?php selected( $design, 'full', true ); ?>><?php esc_html_e( 'Full width', 'xts-theme' ); ?></option>
							<option value="sized" <?php selected( $design, 'sized', true ); ?>><?php esc_html_e( 'Set sizes', 'xts-theme' ); ?></option>
							<option value="container" <?php selected( $design, 'container', true ); ?>><?php esc_html_e( 'Container', 'xts-theme' ); ?></option>
						</select>
					</label>
				</p>

				<p class="description description-wide xts-block">
					<label for="edit-menu-item-block-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'HTML Block for the dropdown', 'xts-theme' ); ?>

						<select id="edit-menu-item-block-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="menu-item-block[<?php echo esc_attr( $item_id ); ?>]">
							<option value="" <?php selected( $block, '', true ); ?>><?php esc_html_e( 'None', 'xts-theme' ); ?></option>
							<?php foreach ( $blocks as $key => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" data-edit-link="<?php echo esc_url( admin_url( 'post.php?post=' . $key . '&action=elementor' ) ); ?>" <?php selected( $block, $key, true ); ?>><?php echo esc_html( $value['name'] ); ?></option>
							<?php endforeach ?>
						</select>

						<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $block . '&action=elementor' ) ); ?>" style="<?php echo ! $block ? 'display:none;' : ''; ?>" class="edit-block-link" target="_blank"><?php esc_html_e( 'Edit this block with Elementor', 'xts-theme' ); ?></a>
						<span> |</span>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=xts-html-block' ) ); ?>" class="add-block-link" target="_blank"><?php esc_html_e( 'Add new', 'xts-theme' ); ?></a>
					</label>
				</p>

				<p class="description description-wide xts-width">
					<label for="edit-menu-item-width-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Dropdown Width', 'xts-theme' ); ?>

						<input type="number" id="edit-menu-item-width-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="menu-item-width[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $width ); ?>">
					</label>
				</p>

				<p class="description description-wide xts-dropdown-ajax">
					<label for="edit-menu-item-dropdown-ajax-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Load HTML dropdown with AJAX', 'xts-theme' ); ?>

						<select id="edit-menu-item-dropdown-ajax-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="menu-item-dropdown-ajax[<?php echo esc_attr( $item_id ); ?>]">
							<option value="no" <?php selected( $dropdown_ajax, 'no', true ); ?>><?php esc_html_e( 'No', 'xts-theme' ); ?></option>
							<option value="yes" <?php selected( $dropdown_ajax, 'yes', true ); ?>><?php esc_html_e( 'Yes', 'xts-theme' ); ?></option>
						</select>
					</label>
				</p>

				<p class="description description-wide xts-event">
					<label for="edit-menu-item-event-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Open on mouse event', 'xts-theme' ); ?>

						<select id="edit-menu-item-event-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="menu-item-event[<?php echo esc_attr( $item_id ); ?>]">
							<option value="hover" <?php selected( $event, 'hover', true ); ?>><?php esc_html_e( 'Hover', 'xts-theme' ); ?></option>
							<option value="click" <?php selected( $event, 'click', true ); ?>><?php esc_html_e( 'Click', 'xts-theme' ); ?></option>
						</select>
					</label>
				</p>

				<p class="description description-wide xts-label-text">
					<label for="edit-menu-item-label-text-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Label text', 'xts-theme' ); ?>

						<input type="text" id="edit-menu-item-label-text-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="menu-item-label-text[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $label_text ); ?>">
					</label>
				</p>

				<p class="description description-wide xts-label-color">
					<label for="edit-menu-item-label-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Label color', 'xts-theme' ); ?>

						<select id="edit-menu-item-label-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="menu-item-label[<?php echo esc_attr( $item_id ); ?>]">
							<option value=""><?php esc_html_e( 'Select', 'xts-theme' ); ?></option>
							<option value="primary" <?php selected( $label, 'primary', true ); ?>><?php esc_html_e( 'Primary Color', 'xts-theme' ); ?></option>
							<option value="secondary" <?php selected( $label, 'secondary', true ); ?>><?php esc_html_e( 'Secondary', 'xts-theme' ); ?></option>
							<option value="red" <?php selected( $label, 'red', true ); ?>><?php esc_html_e( 'Red', 'xts-theme' ); ?></option>
							<option value="green" <?php selected( $label, 'green', true ); ?>><?php esc_html_e( 'Green', 'xts-theme' ); ?></option>
							<option value="blue" <?php selected( $label, 'blue', true ); ?>><?php esc_html_e( 'Blue', 'xts-theme' ); ?></option>
							<option value="orange" <?php selected( $label, 'orange', true ); ?>><?php esc_html_e( 'Orange', 'xts-theme' ); ?></option>
							<option value="grey" <?php selected( $label, 'grey', true ); ?>><?php esc_html_e( 'Grey', 'xts-theme' ); ?></option>
							<option value="black" <?php selected( $label, 'black', true ); ?>><?php esc_html_e( 'Black', 'xts-theme' ); ?></option>
							<option value="white" <?php selected( $label, 'white', true ); ?>><?php esc_html_e( 'White', 'xts-theme' ); ?></option>
						</select>
					</label>
				</p>

				<p class="description description-wide">
					<label for="edit-menu-item-colorscheme-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Dropdown text color scheme', 'xts-theme' ); ?>

						<select id="edit-menu-item-colorscheme-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="menu-item-colorscheme[<?php echo esc_attr( $item_id ); ?>]">
							<option value="default" <?php selected( $color_scheme, 'default', true ); ?>><?php esc_html_e( 'Default', 'xts-theme' ); ?></option>
							<option value="dark" <?php selected( $color_scheme, 'dark', true ); ?>><?php esc_html_e( 'Dark', 'xts-theme' ); ?></option>
							<option value="light" <?php selected( $color_scheme, 'light', true ); ?>><?php esc_html_e( 'Light', 'xts-theme' ); ?></option>
						</select>
					</label>
				</p>

				<p class="description description-wide xts-opanchor">
					<label for="edit-menu-item-opanchor-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'One page anchor', 'xts-theme' ); ?>

						<select id="edit-menu-item-opanchor-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="menu-item-opanchor[<?php echo esc_attr( $item_id ); ?>]">
							<option value="disable" <?php selected( $opanchor, 'disable', true ); ?>><?php esc_html_e( 'Disable', 'xts-theme' ); ?></option>
							<option value="enable" <?php selected( $opanchor, 'enable', true ); ?>><?php esc_html_e( 'Enable', 'xts-theme' ); ?></option>
						</select>
						<span class="description"><?php esc_html_e( 'Enable this to use one page navigation menu. If enabled you need to set the link for this item to be like this: http://your_site.com/home_page/#anchor_id where anchor_id will be the ID of the ROW on your home page.', 'xts-theme' ); ?></span>
					</label>
				</p>

				<p class="description description-wide xts-image">
					<label for="edit-menu-item-image-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Image', 'xts-theme' ); ?>

						<div class="xts-mega-menu-image-wrapper">
							<input type="hidden" class="xts-mega-menu-image-id" name="menu-item-image[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $image_id ); ?>">

							<div class="xts-mega-menu-image">
								<a href="#" class="xts-mega-menu-upload">
									<div class="xts-mega-menu-image-preview xts-upload-preview">
										<?php if ( $image_id ) : ?>
											<?php echo wp_get_attachment_image( $image_id ); ?>
										<?php endif; ?>
									</div>
								</a>
							</div>

							<div class="xts-upload-btns">
								<a href="#" class="xts-mega-menu-upload xts-btn xts-upload-btn">
									<?php esc_html_e( 'Upload', 'xts-theme' ); ?>
								</a>

								<a href="#" class="xts-mega-menu-remove xts-btn xts-btn-remove xts-btn-disable <?php echo esc_attr( $classes ); ?>">
									<?php esc_html_e( 'Remove', 'xts-theme' ); ?>
								</a>
							</div>
						</div>
					</label>
				</p>
			</div>
		<?php
		$output .= ob_get_clean();
	}
}
