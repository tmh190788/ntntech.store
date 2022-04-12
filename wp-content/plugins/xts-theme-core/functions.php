<?php
/**
 * Functions.
 *
 * @package xts.
 */

if ( ! function_exists( 'xts_is_ipad_not_mobile' ) ) {
	/**
	 * Filter page content.
	 *
	 * @param boolean $is_mobile Is mobile.
	 *
	 * @return string|void
	 */
	function xts_is_ipad_not_mobile( $is_mobile ) {
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) ) { // phpcs:ignore
			$is_mobile = false;
		}

		return $is_mobile;
	}

	add_filter( 'wp_is_mobile', 'xts_is_ipad_not_mobile' );
}

if ( ! function_exists( 'xts_compress' ) ) {
	/**
	 * Encodes data to MIME base64 format.
	 *
	 * @param string $data data.
	 *
	 * @return string
	 */
	function xts_compress( $data ) {
		return base64_encode( $data ); // phpcs:ignore
	}
}

if ( ! function_exists( 'xts_calculate_image' ) ) {
	/**
	 * Encodes data to MIME base64 format.
	 *
	 * @param int[]  $size_array    Data.
	 * @param string $image_src     Data.
	 * @param array  $image_meta    Data.
	 * @param int    $attachment_id Data.
	 *
	 * @return string
	 */
	function xts_calculate_image( $size_array, $image_src, $image_meta, $attachment_id = 0 ) {
		return wp_calculate_image_srcset( $size_array, $image_src, $image_meta, $attachment_id );
	}
}

if ( ! function_exists( 'xts_decompress' ) ) {
	/**
	 * Decodes data encoded by MIME base64.
	 *
	 * @param string $data   The encoded data.
	 * @param bool   $strict Returns false if input contains character from outside the base64.
	 *
	 * @return string
	 */
	function xts_decompress( $data, $strict = false ) {
		return base64_decode( $data, $strict ); // phpcs:ignore
	}
}
if ( ! function_exists( 'xts_get_content_file' ) ) {
	/**
	 * Reads entire file into a string.
	 *
	 * @param string $file_name Filename.
	 *
	 * @return false|string
	 */
	function xts_get_content_file( $file_name ) {
		return file_get_contents( $file_name ); // phpcs:ignore
	}
}

if ( ! function_exists( 'xts_add_box' ) ) {
	/**
	 * Adds a meta box to one or more screens.
	 *
	 * @param string                 $id            Meta box ID.
	 * @param string                 $title         Title of the meta box.
	 * @param callable               $callback      Function that fills the box with the desired content.
	 * @param string|array|WP_Screen $screen        The screen or screens on which to show the box
	 *                                              (such as a post type, 'link', or 'comment').
	 * @param string                 $context       The context within the screen where the box
	 *                                              should display.
	 * @param string                 $priority      Optional. The priority within the context where the box should show.
	 *                                              Accepts 'high', 'core', 'default', or 'low'. Default 'default'.
	 * @param array                  $callback_args Optional. Data that should be set as the $args property
	 *                                              of the box array. Default null.
	 */
	function xts_add_box( $id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null ) {
		add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
	}
}

if ( ! function_exists( 'xts_taxonomy_register_on_import' ) ) {
	/**
	 * Register taxonomy.
	 *
	 * @param array $attr Attribute.
	 */
	function xts_taxonomy_register_on_import( $attr ) {
		register_taxonomy(
			'pa_' . $attr['slug'],
			'product',
			array(
				'labels' => array(
					'name' => $attr['name'],
				),
			)
		);
	}
}

if ( ! function_exists( 'xts_is_pjax' ) ) {
	/**
	 * Is pjax request
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	function xts_is_pjax() {
		$request_headers = function_exists( 'getallheaders' ) ? getallheaders() : array();

		return isset( $_REQUEST['_pjax'] ) && ( ( isset( $request_headers['X-Requested-With'] ) && 'xmlhttprequest' === strtolower( $request_headers['X-Requested-With'] ) ) || ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) ); // phpcs:ignore
	}
}

if ( ! function_exists( 'xts_core_social_buttons_template' ) ) {
	/**
	 * Social buttons template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_core_social_buttons_template( $element_args ) {
		$default_args = array(
			// Content.
			'type'                  => 'share',

			// Label.
			'label_text'            => '',
			'label_color_presets'   => 'default',
			'label_text_size'       => 'default',

			// Style.
			'align'                 => 'center',
			'style'                 => 'default',
			'size'                  => 'm',
			'shape'                 => 'round',
			'color_scheme'          => 'dark',

			// Extra.
			'wrapper_extra_classes' => '',
			'page_link'             => false,
			'name'                  => 'no',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes = '';
		$inner_classes   = '';
		$label_classes   = '';
		$thumb_id        = get_post_thumbnail_id();
		$page_title      = get_the_title();

		if ( ! $element_args['page_link'] ) {
			$element_args['page_link'] = get_the_permalink();
		}

		// Label classes.
		$label_classes .= ' xts-textcolor-' . $element_args['label_color_presets'];
		$label_classes .= ' xts-fontsize-' . $element_args['label_text_size'];
		if ( xts_elementor_is_edit_mode() ) {
			$label_classes .= ' elementor-inline-editing';
		}

		// Wrapper classes.
		if ( 'inherit' !== $element_args['align'] ) {
			$wrapper_classes .= ' xts-textalign-' . $element_args['align'];
		}
		if ( $element_args['wrapper_extra_classes'] ) {
			$wrapper_classes .= ' ' . $element_args['wrapper_extra_classes'];
		}

		// Inner classes.
		$inner_classes .= ' xts-type-' . $element_args['type'];
		$inner_classes .= ' xts-style-' . $element_args['style'];
		$inner_classes .= ' xts-size-' . $element_args['size'];
		if ( 'default' !== $element_args['style'] ) {
			$inner_classes .= ' xts-shape-' . $element_args['shape'];
		}
		if ( 'dark' !== $element_args['color_scheme'] ) {
			$inner_classes .= ' xts-scheme-' . $element_args['color_scheme'];
		}

		$thumb_url = wp_get_attachment_image_src( $thumb_id, 'thumbnail-size', true );

		?>

		<div class="xts-social-buttons-wrapper<?php echo esc_attr( xts_get_rtl_inverted_string( $wrapper_classes ) ); ?>">

			<?php if ( $element_args['label_text'] ) : ?>
				<span class="xts-social-label<?php echo esc_attr( $label_classes ); ?>" data-elementor-setting-key="label_text"><?php echo esc_html( $element_args['label_text'] ); ?></span>
			<?php endif; ?>

			<div class="xts-social-buttons xts-social-icons<?php echo esc_attr( $inner_classes ); ?>">

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'behance_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'behance' ); // phpcs:ignore ?>>
						<i class="xts-i-behance"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Behance', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'dribbble_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'dribbble' ); // phpcs:ignore ?>>
						<i class="xts-i-dribbble"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Dribbble', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $element_args['type'] && xts_get_opt( 'email_share' ) ) || ( 'follow' === $element_args['type'] && xts_get_opt( 'email_link' ) ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'email', 'mailto:?subject=' . esc_html__( 'Check this ', 'xts-theme' ) . esc_url( $element_args['page_link'] ) ); // phpcs:ignore ?>>
						<i class="xts-i-email"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Email', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $element_args['type'] && xts_get_opt( 'facebook_share' ) ) || ( 'follow' === $element_args['type'] && xts_get_opt( 'facebook_link' ) ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . $element_args['page_link'] ); // phpcs:ignore ?>>
						<i class="xts-i-facebook"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Facebook', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'flickr_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'flickr' ); // phpcs:ignore ?>>
						<i class="xts-i-flickr"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Flickr', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'github_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'github' ); // phpcs:ignore ?>>
						<i class="xts-i-github"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'GitHub', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'instagram_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'instagram' ); // phpcs:ignore ?>>
						<i class="xts-i-instagram"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Instagram', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'linkedin_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'linkedin' ); // phpcs:ignore ?>>
						<i class="xts-i-linkedin"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'LinkedIn', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $element_args['type'] && xts_get_opt( 'ok_share' ) ) || ( 'follow' === $element_args['type'] && xts_get_opt( 'ok_link' ) ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'ok', 'https://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl=' . $element_args['page_link'] ); // phpcs:ignore ?>>
						<i class="xts-i-ok"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Odnoklassniki', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $element_args['type'] && xts_get_opt( 'pinterest_share' ) ) || ( 'follow' === $element_args['type'] && xts_get_opt( 'pinterest_link' ) ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'pinterest', 'https://pinterest.com/pin/create/button/?url=' . $element_args['page_link'] . '&media=' . $thumb_url[0] . '&description=' . urlencode( $page_title ) ); // phpcs:ignore ?>>
						<i class="xts-i-pinterest"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Pinterest', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'snapchat_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'snapchat' ); // phpcs:ignore ?>>
						<i class="xts-i-snapchat"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Snapchat', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'soundcloud_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'soundcloud' ); // phpcs:ignore ?>>
						<i class="xts-i-soundcloud"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'SoundCloud', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'spotify_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'spotify' ); // phpcs:ignore ?>>
						<i class="xts-i-spotify"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Spotify', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $element_args['type'] && xts_get_opt( 'telegram_share' ) ) || ( 'follow' === $element_args['type'] && xts_get_opt( 'telegram_link' ) ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'telegram', 'https://telegram.me/share/url?url=' . $element_args['page_link'] ); // phpcs:ignore ?>>
						<i class="xts-i-telegram"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Telegram', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'tumblr_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'tumblr' ); // phpcs:ignore ?>>
						<i class="xts-i-tumblr"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Tumblr', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $element_args['type'] && xts_get_opt( 'twitter_share' ) ) || ( 'follow' === $element_args['type'] && xts_get_opt( 'twitter_link' ) ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'twitter', 'https://twitter.com/share?url=' . $element_args['page_link'] ); // phpcs:ignore ?>>
						<i class="xts-i-twitter"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Twitter', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'vimeo_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'vimeo' ); // phpcs:ignore ?>>
						<i class="xts-i-vimeo"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Vimeo', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $element_args['type'] && xts_get_opt( 'vk_share' ) ) || ( 'follow' === $element_args['type'] && xts_get_opt( 'vk_link' ) ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'vk', 'https://vk.com/share.php?url=' . $element_args['page_link'] . '&image=' . $thumb_url[0] . '&title=' . urlencode( $page_title ) ); // phpcs:ignore ?>>
						<i class="xts-i-vk"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'VK', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $element_args['type'] && xts_get_opt( 'whatsapp_share' ) ) || ( 'follow' === $element_args['type'] && xts_get_opt( 'whatsapp_link' ) ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'whatsapp', 'https://api.whatsapp.com/send?text=' . urlencode( $element_args['page_link'] ), 'xts-hide-md' ); // phpcs:ignore ?>>
						<i class="xts-i-whatsapp"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'WhatsApp', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>

					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'whatsapp', 'whatsapp://send?text=' . $element_args['page_link'], 'xts-hide-lg' ); // phpcs:ignore ?>>
						<i class="xts-i-whatsapp"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'WhatsApp', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'youtube_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'youtube' ); // phpcs:ignore ?>>
						<i class="xts-i-youtube"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Youtube', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $element_args['type'] && xts_get_opt( 'tiktok_link' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'tiktok' ); // phpcs:ignore ?>>
						<i class="xts-i-tiktok"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'TikTok', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'share' === $element_args['type'] && xts_get_opt( 'viber_share' ) ) : ?>
					<a data-elementor-open-lightbox="no" <?php echo xts_get_social_buttons_link_attrs( $element_args['type'], 'viber', 'viber://forward?text=' . $element_args['page_link'] ); // phpcs:ignore ?>>
						<i class="xts-i-viber"></i>
						<?php if ( 'yes' === $element_args['name'] || 'with-text' === $element_args['style'] ) : ?>
							<span class="xts-social-name"><?php esc_html_e( 'Viber', 'xts-theme' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

			</div>

		</div>
		<?php
	}
}
