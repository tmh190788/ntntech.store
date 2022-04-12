<?php
/**
 * Social profiles options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

/**
 * Links
 */
Options::add_field(
	array(
		'id'       => 'email_link',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Email for social links', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 10,
		'default'  => '1',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'behance_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Behance', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 20,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'dribbble_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Dribbble', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 30,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'facebook_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Facebook', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 40,
		'default'  => '#',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'flickr_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Flickr', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 50,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'github_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Github', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 60,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'instagram_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Instagram', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 80,
		'default'  => '#',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'linkedin_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'LinkedIn', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 90,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'ok_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Odnoklassniki', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 100,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'pinterest_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Pinterest', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 110,
		'default'  => '#',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'snapchat_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Snapchat', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 120,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'soundcloud_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'SoundCloud', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 130,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'spotify_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Spotify', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 140,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'telegram_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Telegram', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 150,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'tumblr_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Tumblr', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 160,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'twitter_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Twitter', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 170,
		'default'  => '#',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'vimeo_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Vimeo', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 180,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'vk_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'VK', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 190,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'whatsapp_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'WhatsApp', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 200,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'youtube_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'Youtube', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 210,
		'default'  => '#',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'tiktok_link',
		'type'     => 'text_input',
		'name'     => esc_html__( 'TikTok', 'xts-theme' ),
		'section'  => 'social_links_section',
		'priority' => 220,
		'default'  => '#',
		'class'    => 'xts-col-6',
	)
);

/**
 * Share
 */
Options::add_field(
	array(
		'id'       => 'email_share',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Email for share links', 'xts-theme' ),
		'section'  => 'share_buttons_section',
		'priority' => 20,
		'default'  => '1',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'facebook_share',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Facebook', 'xts-theme' ),
		'section'  => 'share_buttons_section',
		'priority' => 30,
		'default'  => '1',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'ok_share',
		'type'     => 'switcher',
		'name'     => esc_html__( 'OK', 'xts-theme' ),
		'section'  => 'share_buttons_section',
		'priority' => 50,
		'default'  => '0',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'pinterest_share',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Pinterest', 'xts-theme' ),
		'section'  => 'share_buttons_section',
		'priority' => 60,
		'default'  => '1',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'telegram_share',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Telegram', 'xts-theme' ),
		'section'  => 'share_buttons_section',
		'priority' => 70,
		'default'  => '0',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'twitter_share',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Twitter', 'xts-theme' ),
		'section'  => 'share_buttons_section',
		'priority' => 80,
		'default'  => '1',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'vk_share',
		'type'     => 'switcher',
		'name'     => esc_html__( 'VK', 'xts-theme' ),
		'section'  => 'share_buttons_section',
		'priority' => 90,
		'default'  => '0',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'whatsapp_share',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Whatsapp', 'xts-theme' ),
		'section'  => 'share_buttons_section',
		'priority' => 100,
		'default'  => '0',
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'viber_share',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Viber', 'xts-theme' ),
		'section'  => 'share_buttons_section',
		'priority' => 110,
		'default'  => '0',
		'class'    => 'xts-col-6',
	)
);

