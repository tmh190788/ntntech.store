<?php
/**
 * Twitter template function
 *
 * @package xts
 */

use XTS\Twitter_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_twitter_template' ) ) {
	/**
	 * Twitter template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 *
	 * @throws Exception Exception.
	 */
	function xts_twitter_template( $element_args ) {
		$default_args = array(
			'consumer_key'        => '',
			'consumer_secret'     => '',
			'access_token'        => '',
			'access_token_secret' => '',
			'user_name'           => 'SpaceX',
			'count'               => array( 'size' => 5 ),
			'exclude_replies'     => 'no',
			'follow_btn'          => 'yes',
			'account_info'        => 'yes',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$exclude_replies = 'yes' === $element_args['exclude_replies'] ? 'true' : 'false';

		if ( ! $element_args['consumer_key'] || ! $element_args['consumer_secret'] ) {
			?>
				<div class="xts-notification xts-color-info">
					<?php esc_html_e( 'You need to enter your Consumer key and secret to display your recent twitter feed.', 'xts-theme' ); ?>
				</div>
			<?php
			return;
		}

		$twitter = new Twitter_API(
			array(
				'oauth_access_token'        => $element_args['access_token'],
				'oauth_access_token_secret' => $element_args['access_token_secret'],
				'consumer_key'              => $element_args['consumer_key'],
				'consumer_secret'           => $element_args['consumer_secret'],
			)
		);

		$posts_data_transient_name = 'xts-twitter-posts-data-' . sanitize_title_with_dashes( $element_args['user_name'] . $element_args['count']['size'] . $element_args['exclude_replies'] );
		$user_data_transient_name  = 'xts-twitter-user-data-' . sanitize_title_with_dashes( $element_args['user_name'] . $element_args['count']['size'] . $element_args['exclude_replies'] );

		if ( ! xts_is_core_module_exists() ) {
			return;
		}

		$posts     = maybe_unserialize( xts_decompress( get_transient( $posts_data_transient_name ) ) );
		$user_info = maybe_unserialize( xts_decompress( get_transient( $user_data_transient_name ) ) );

		if ( ! $posts ) {
			$posts = json_decode(
				$twitter->setGetfield( '?screen_name=' . $element_args['user_name'] . '&count=' . $element_args['count']['size'] . '&exclude_replies=' . $exclude_replies )
				->buildOauth( 'https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET' )
				->performRequest()
			);

			if ( is_object( $posts ) && property_exists( $posts, 'errors' ) ) {
				?>
					<div class="xts-notification xts-color-info">
						<?php echo esc_html( $posts->errors[0]->code . ' : ' . $posts->errors[0]->message ); ?>
					</div>
				<?php
				return;
			}

			$encode_posts = xts_is_core_module_exists() ? xts_compress( maybe_serialize( $posts ) ) : '';
			set_transient( $posts_data_transient_name, $encode_posts, apply_filters( 'xts_twitter_cache_time', HOUR_IN_SECONDS * 2 ) );
		}

		if ( ! $user_info ) {
			$user_info = json_decode(
				$twitter->setGetfield( '?screen_name=' . $element_args['user_name'] )
				->buildOauth( 'https://api.twitter.com/1.1/users/lookup.json', 'GET' )
				->performRequest()
			);

			if ( is_object( $user_info ) && property_exists( $user_info, 'errors' ) ) {
				?>
					<div class="xts-notification xts-color-info">
						<?php echo esc_html( $user_info->errors[0]->code . ' : ' . $user_info->errors[0]->message ); ?>
					</div>
				<?php
				return;
			}

			$encode_user_info = xts_is_core_module_exists() ? xts_compress( maybe_serialize( $user_info ) ) : '';
			set_transient( $user_data_transient_name, $encode_user_info, apply_filters( 'xts_twitter_cache_time', HOUR_IN_SECONDS * 2 ) );
		}

		if ( ! $posts || ! $user_info ) {
			?>
				<div class="xts-notification xts-color-info">
					<?php echo esc_html__( 'Twitter not return any data', 'xts-theme' ); ?>
				</div>
			<?php
			return;
		}

		$author_name = $user_info[0]->screen_name;
		$profile_url = 'https://twitter.com/' . $author_name;

		?>

		<div class="xts-twitter">
			<?php if ( 'yes' === $element_args['account_info'] ) : ?>
				<div class="xts-twitter-header">
					<a class="xts-twitter-avatar" href="<?php echo esc_url( $profile_url ); ?>">
						<?php echo apply_filters( 'xts_image', '<img src="' . esc_url( $user_info[0]->profile_image_url_https ) . '">' ); // phpcs:ignore ?>
					</a>

					<div class="xts-twitter-info">
						<h6 class="xts-twitter-name">
							<?php echo esc_html( $author_name ); ?>
						</h6>

						<a class="xts-twitter-username" href="<?php echo esc_url( $profile_url ); ?>">
							<?php echo esc_html( '@' . $author_name ); ?>
						</a>
					</div>

					<div class="xts-twitter-counters">
						<div class="xts-twitter-counter">
						<span>
							<?php echo esc_html( $user_info[0]->friends_count ); ?>
						</span>

							<?php esc_html_e( 'Following', 'xts-theme' ); ?>
						</div>

						<div class="xts-twitter-counter">
						<span>
							<?php echo esc_html( xts_get_pretty_number( $user_info[0]->followers_count ) ); ?>
						</span>

							<?php esc_html_e( 'Followers', 'xts-theme' ); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<ul class="xts-twitter-tweets">
				<?php foreach ( $posts as $key => $post ) : ?>
					<?php
						$post_id = $post->id_str;
					?>

					<li class="xts-twitter-tweet">
						<div class="xts-twitter-desc">
							<?php echo xts_get_twitter_text_with_links( $post ); // phpcs:ignore ?>
						</div>

						<div class="xts-twitter-tweet-footer">
							<a href="<?php echo esc_url( 'https://twitter.com/' . $post->user->screen_name . '/status/' . $post_id ); ?>" class="xts-twitter-date">
								<?php echo esc_html( date( 'm.d.y', strtotime( $post->created_at ) ) ); ?>
							</a>

							<div class="xts-twitter-actions">
								<a class="xts-twitter-reply" href="https://twitter.com/intent/tweet?in_reply_to=<?php echo esc_attr( $post_id ); ?>"></a>
								<a class="xts-twitter-retweet" href="https://twitter.com/intent/retweet?tweet_id=<?php echo esc_attr( $post_id ); ?>"></a>
								<a class="xts-twitter-favorite" href="https://twitter.com/intent/favorite?tweet_id=<?php echo esc_attr( $post_id ); ?>"></a>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( 'yes' === $element_args['follow_btn'] ) : ?>
				<div class="xts-twitter-footer">
					<a class="xts-twitter-follow-btn xts-button xts-color-primary" href="<?php echo esc_url( $profile_url ); ?>">
						<?php esc_html_e( 'Follow', 'xts-theme' ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'xts_get_twitter_text_with_links' ) ) {
	/**
	 * Get twitter post text with links
	 *
	 * @since 1.0.0
	 *
	 * @param object $post Twitter post.
	 *
	 * @return string|string[]|null
	 */
	function xts_get_twitter_text_with_links( $post ) {
		if ( isset( $post->retweeted_status ) ) {
			$rt_section = current( explode( ':', $post->text ) );
			$text       = $rt_section . ': ';
			$text      .= $post->retweeted_status->text;
		} else {
			$text = $post->text;
		}

		$text = preg_replace( '/((http)+(s)?:\/\/[^<>\s]+)/i', '<a href="$0" target="_blank" rel="nofollow">$0</a>', $text );
		$text = preg_replace( '/[@]+([A-Za-z0-9-_]+)/', '<a href="https://twitter.com/$1" target="_blank" rel="nofollow">@\\1</a>', $text );
		$text = preg_replace( '/[#]+([A-Za-z0-9-_]+)/', '<a href="https://twitter.com/search?q=%23$1" target="_blank" rel="nofollow">$0</a>', $text );
		$text = preg_replace( '/[\xF0-\xF7][\x80-\xBF]{3}/', '', $text );

		return $text;
	}
}
