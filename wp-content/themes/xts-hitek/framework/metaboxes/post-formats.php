<?php
/**
 * Post formats metaboxes
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Metaboxes;

if ( ! function_exists( 'xts_register_post_formats_metaboxes' ) ) {
	/**
	 * Register post formats metaboxes
	 *
	 * @since 1.0.0
	 */
	function xts_register_post_formats_metaboxes() {
		$post_formats_metabox = Metaboxes::add_metabox(
			array(
				'id'         => 'xts_post_formats_metaboxes',
				'title'      => esc_html__( 'Post formats', 'xts-theme' ),
				'post_types' => array( 'post' ),
			)
		);

		$post_formats_metabox->add_section(
			array(
				'id'       => 'quote',
				'name'     => esc_html__( 'Quote', 'xts-theme' ),
				'priority' => 10,
				'icon'     => 'xf-quote',
			)
		);

		$post_formats_metabox->add_section(
			array(
				'id'       => 'link',
				'name'     => esc_html__( 'Link', 'xts-theme' ),
				'priority' => 20,
				'icon'     => 'xf-link',
			)
		);

		$post_formats_metabox->add_section(
			array(
				'id'       => 'gallery',
				'name'     => esc_html__( 'Gallery', 'xts-theme' ),
				'priority' => 30,
				'icon'     => 'xf-gallery',
			)
		);

		$post_formats_metabox->add_section(
			array(
				'id'       => 'video',
				'name'     => esc_html__( 'Video', 'xts-theme' ),
				'priority' => 40,
				'icon'     => 'xf-video',
			)
		);

		$post_formats_metabox->add_section(
			array(
				'id'       => 'audio',
				'name'     => esc_html__( 'Audio', 'xts-theme' ),
				'priority' => 50,
				'icon'     => 'xf-audio',
			)
		);

		/**
		 * Quote
		 */
		$post_formats_metabox->add_field(
			array(
				'name'     => esc_html__( 'Quote', 'xts-theme' ),
				'id'       => 'post_quote',
				'type'     => 'textarea',
				'wysiwyg'  => false,
				'section'  => 'quote',
				'priority' => 10,
			)
		);

		$post_formats_metabox->add_field(
			array(
				'name'        => esc_html__( 'Cite', 'xts-theme' ),
				'description' => esc_html__( 'Quote author\'s name', 'xts-theme' ),
				'id'          => 'post_quote_cite',
				'type'        => 'text_input',
				'section'     => 'quote',
				'priority'    => 20,
			)
		);

		/**
		 * Link
		 */
		$post_formats_metabox->add_field(
			array(
				'name'     => esc_html__( 'Link', 'xts-theme' ),
				'id'       => 'post_link',
				'type'     => 'text_input',
				'section'  => 'link',
				'priority' => 10,
			)
		);

		$post_formats_metabox->add_field(
			array(
				'id'       => 'post_link_blank',
				'type'     => 'switcher',
				'name'     => esc_html__( 'Open link in new window', 'xts-theme' ),
				'section'  => 'link',
				'default'  => '0',
				'priority' => 20,
			)
		);

		/**
		 * Gallery
		 */
		$post_formats_metabox->add_field(
			array(
				'name'     => esc_html__( 'Gallery', 'xts-theme' ),
				'id'       => 'post_gallery',
				'type'     => 'upload_list',
				'section'  => 'gallery',
				'priority' => 10,
			)
		);

		/**
		 * Video
		 */
		$post_formats_metabox->add_field(
			array(
				'id'       => 'video_source',
				'name'     => esc_html__( 'Video source', 'xts-theme' ),
				'type'     => 'buttons',
				'section'  => 'video',
				'options'  => array(
					'mp4'     => array(
						'name'  => esc_html__( 'MP4', 'xts-theme' ),
						'value' => 'mp4',
					),
					'youtube' => array(
						'name'  => esc_html__( 'YouTube', 'xts-theme' ),
						'value' => 'youtube',
					),
					'vimeo'   => array(
						'name'  => esc_html__( 'Vimeo', 'xts-theme' ),
						'value' => 'vimeo',
					),
				),
				'default'  => 'mp4',
				'priority' => 10,
			)
		);

		$post_formats_metabox->add_field(
			array(
				'name'     => esc_html__( 'Video MP4', 'xts-theme' ),
				'id'       => 'post_video_mp4',
				'type'     => 'upload',
				'section'  => 'video',
				'requires' => array(
					array(
						'key'     => 'video_source',
						'compare' => 'equals',
						'value'   => 'mp4',
					),
				),
				'priority' => 20,
			)
		);

		if ( apply_filters( 'xts_video_ogg_webm_formats', false ) ) {
			$post_formats_metabox->add_field(
				array(
					'name'     => esc_html__( 'Video WEBM', 'xts-theme' ),
					'id'       => 'post_video_webm',
					'type'     => 'upload',
					'section'  => 'video',
					'priority' => 30,
				)
			);

			$post_formats_metabox->add_field(
				array(
					'name'     => esc_html__( 'Video OGG', 'xts-theme' ),
					'id'       => 'post_video_ogg',
					'type'     => 'upload',
					'section'  => 'video',
					'priority' => 40,
				)
			);
		}

		$post_formats_metabox->add_field(
			array(
				'name'        => esc_html__( 'Video YouTube', 'xts-theme' ),
				'description' => esc_html__( 'Example: https://youtu.be/LXb3EKWsInQ', 'xts-theme' ),
				'id'          => 'post_video_youtube',
				'type'        => 'text_input',
				'section'     => 'video',
				'requires'    => array(
					array(
						'key'     => 'video_source',
						'compare' => 'equals',
						'value'   => 'youtube',
					),
				),
				'priority'    => 50,
			)
		);

		$post_formats_metabox->add_field(
			array(
				'name'        => esc_html__( 'Video Vimeo', 'xts-theme' ),
				'description' => esc_html__( 'Example: https://vimeo.com/259400046', 'xts-theme' ),
				'id'          => 'post_video_vimeo',
				'type'        => 'text_input',
				'section'     => 'video',
				'requires'    => array(
					array(
						'key'     => 'video_source',
						'compare' => 'equals',
						'value'   => 'vimeo',
					),
				),
				'priority'    => 60,
			)
		);

		$post_formats_metabox->add_field(
			array(
				'name'     => esc_html__( 'Aspect Ratio', 'xts-theme' ),
				'id'       => 'post_video_aspect_ratio',
				'type'     => 'select',
				'section'  => 'video',
				'options'  => array(
					'16-9' => array(
						'name'  => '16:9',
						'value' => '16-9',
					),
					'21-9' => array(
						'name'  => '21:9',
						'value' => '21-9',
					),
					'4-3'  => array(
						'name'  => '4:3',
						'value' => '4-3',
					),
					'3-2'  => array(
						'name'  => '3:2',
						'value' => '3-2',
					),
					'1-1'  => array(
						'name'  => '1:1',
						'value' => '1-1',
					),
					'9-16' => array(
						'name'  => '9:16',
						'value' => '9-16',
					),
				),
				'default'  => '16-9',
				'priority' => 70,
			)
		);

		/**
		 * Audio
		 */
		$post_formats_metabox->add_field(
			array(
				'name'        => esc_html__( 'SoundCloud track URL', 'xts-theme' ),
				'description' => esc_html__( 'Example: https://soundcloud.com/dontoliver/after-party', 'xts-theme' ),
				'id'          => 'post_audio_url',
				'type'        => 'text_input',
				'section'     => 'audio',
				'priority'    => 10,
			)
		);
	}

	add_action( 'init', 'xts_register_post_formats_metaboxes', 200 );
}
