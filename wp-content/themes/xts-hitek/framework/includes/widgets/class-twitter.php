<?php
/**
 * Twitter class.
 *
 * @package xts
 */

namespace XTS\Widget;

use XTS\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Twitter widget
 */
class Twitter extends Widget_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( '[XTemos] Twitter', 'xts-theme' ),
			'description' => esc_html__( 'Twitter posts', 'xts-theme' ),
			'slug'        => 'xts-widget-twitter',
			'fields'      => array(
				array(
					'id'      => 'title',
					'type'    => 'text',
					'name'    => esc_html__( 'Title', 'xts-theme' ),
					'default' => 'Twitter',
				),

				array(
					'id'   => 'access_token',
					'type' => 'text',
					'name' => esc_html__( 'Access token', 'xts-theme' ),
				),

				array(
					'id'   => 'access_token_secret',
					'type' => 'text',
					'name' => esc_html__( 'Access token secret', 'xts-theme' ),
				),

				array(
					'id'   => 'consumer_key',
					'type' => 'text',
					'name' => esc_html__( 'Consumer key', 'xts-theme' ),
				),

				array(
					'id'          => 'consumer_secret',
					'type'        => 'text',
					'name'        => esc_html__( 'Consumer secret', 'xts-theme' ),
					'description' => 'You can obtain your twitter consumer key and secret values after creating an APP on <a href="https://developer.twitter.com/en/docs/basics/apps/overview" target="_blank">Twitter developers service</a>.',
				),

				array(
					'id'          => 'user_name',
					'type'        => 'text',
					'name'        => esc_html__( 'User name', 'xts-theme' ),
					'description' => 'https://twitter.com/{{SpaceX}}',
					'default'     => 'SpaceX',
				),

				array(
					'id'      => 'count',
					'type'    => 'number',
					'name'    => esc_html__( 'Tweets count', 'xts-theme' ),
					'default' => 5,
				),

				array(
					'id'      => 'exclude_replies',
					'type'    => 'checkbox',
					'name'    => esc_html__( 'Exclude replies', 'xts-theme' ),
					'default' => false,
				),
			),
		);

		$this->create_widget( $args );
	}

	/**
	 * Output widget.
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 *
	 * @throws \Exception Exception.
	 */
	public function widget( $args, $instance ) {
		echo wp_kses( $args['before_widget'], 'xts_widget' );

		$default_args = array(
			'title'               => '',
			'consumer_key'        => '',
			'consumer_secret'     => '',
			'access_token'        => '',
			'access_token_secret' => '',
			'user_name'           => 'SpaceX',
			'count'               => 5,
			'exclude_replies'     => false,
		);

		$instance = wp_parse_args( $instance, $default_args );

		$element_args = array(
			'consumer_key'        => $instance['consumer_key'],
			'consumer_secret'     => $instance['consumer_secret'],
			'access_token'        => $instance['access_token'],
			'access_token_secret' => $instance['access_token_secret'],
			'user_name'           => $instance['user_name'],
			'count'               => array( 'size' => $instance['count'] ),
			'exclude_replies'     => $instance['exclude_replies'] ? 'yes' : 'no',
		);

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo wp_kses( $args['before_title'], 'xts_widget' ) . $instance['title'] . wp_kses( $args['after_title'], 'xts_widget' ); // phpcs:ignore
		}

		xts_twitter_template( $element_args );

		echo wp_kses( $args['after_widget'], 'xts_widget' );
	}
}
