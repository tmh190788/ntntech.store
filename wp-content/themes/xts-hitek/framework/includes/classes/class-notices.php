<?php
/**
 * Notices data sctructure.
 *
 * @package xts
 */

namespace XTS\Framework;

/**
 * Notices helper class
 */
class Notices {

	/**
	 * Notices storage.
	 *
	 * @var array
	 */
	public $notices;
	/**
	 * Global notices ignorance key.
	 *
	 * @var string
	 */
	public $ignore_key = '';

	/**
	 * Construct the Notices class..
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->notices = array();

		add_action( 'admin_init', array( $this, 'nag_ignore' ) );
		add_action( 'admin_notices', array( $this, 'add_notice' ), 50 );
	}

	/**
	 * Add message.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $msg Notice text.
	 * @param  string  $type Message type.
	 * @param  boolean $global Global notice.
	 */
	public function add_msg( $msg, $type, $global = false ) {
		$this->notices[] = array(
			'msg'    => $msg,
			'type'   => $type,
			'global' => $global,
		);

		$this->nag_ignore();
	}

	/**
	 * Get only error messages.
	 *
	 * @since 1.0.0
	 */
	public function get_errors() {
		return $this->get_msgs( false, 'error' );
	}

	/**
	 * Get only error messages string
	 *
	 * @since 1.0.0
	 */
	public function get_errors_string() {
		$errors = $this->get_msgs( false, 'error' );

		$text = '';

		foreach ( $errors as $err ) {
			$text .= $err['msg'] . ' ';
		}

		return $text;
	}

	/**
	 * Get messages globals or by type.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $globals Should we take global messages.
	 * @param bool    $type    Messages type.
	 *
	 * @return array
	 */
	public function get_msgs( $globals = false, $type = false ) {
		if ( $globals ) {
			return array_filter(
				$this->notices,
				function( $v ) {
					return $v['global'];
				}
			);
		}

		if ( $type ) {
			return array_filter(
				$this->notices,
				function( $v ) use ( $type ) {
					return $v['type'] === $type;
				}
			);
		}

		return $this->notices;
	}

	/**
	 * Clear messages.
	 *
	 * @since 1.0.0
	 *
	 * @param  boolean $globals Should we clear global messages.
	 */
	public function clear_msgs( $globals = true ) {
		if ( $globals ) {
			$this->notices = array_filter(
				$this->notices,
				function( $v ) {
					return ! $v['global'];
				}
			);
		} else {
			$this->notices = array();
		}
	}

	/**
	 * Display all the messages HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param  boolean $globals Should we clear global messages.
	 */
	public function show_msgs( $globals = false ) {
		$msgs = $this->get_msgs( $globals );

		if ( ! empty( $msgs ) ) {
			foreach ( $msgs as $key => $msg ) {
				if ( ! $globals && $msg['global'] ) {
					continue;
				}
				echo '<div class="xts-msg xts-notice xts-' . esc_attr( $msg['type'] ) . '">';
					echo '<span>' . esc_html( $msg['msg'] ) . '</span>';
				echo '</div>';
			}
		}

		$this->clear_msgs( $globals );
	}

	/**
	 * Add global notices.
	 *
	 * @since 1.0.0
	 */
	public function add_notice() {
		$msgs = $this->get_msgs( true );
		global $current_user;

		$user_id = $current_user->ID;

		if ( ! empty( $msgs ) ) {
			foreach ( $msgs as $key => $msg ) {
				$hash = md5( serialize( $msg['msg'] ) ); // phpcs:ignore
				if ( get_user_meta( $user_id, $hash ) ) {
					continue;
				}
				echo '<div class="xts-msg updated">';
					echo '<p class="xts-msg-' . esc_attr( $msg['type'] ) . '">' . esc_html( $msg['msg'] ) . '</p>';
					echo '<a href="' . esc_url( wp_nonce_url( add_query_arg( 'xts-hide-notice', $hash ) ) ) . '">Dismiss Notice</a>';
				echo '</div>';
			}
		}
	}

	/**
	 * Add error type notice.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $msg Message text.
	 * @param  boolean $global Should we add it to global messages.
	 */
	public function add_error( $msg, $global = false ) {
		$this->add_msg( $msg, 'error', $global );
	}

	/**
	 * Add warning type notice.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $msg Message text.
	 * @param  boolean $global Should we add it to global messages.
	 */
	public function add_warning( $msg, $global = false ) {
		$this->add_msg( $msg, 'warning', $global );
	}

	/**
	 * Add success type notice.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $msg Message text.
	 * @param  boolean $global Should we add it to global messages.
	 */
	public function add_success( $msg, $global = false ) {
		$this->add_msg( $msg, 'success', $global );
	}

	/**
	 * Hide global notices.
	 *
	 * @since 1.0.0
	 */
	public function nag_ignore() {
		if ( ! isset( $_GET['xts-hide-notice'] ) ) { // phpcs:ignore
			return;
		}

		global $current_user;
		$user_id = $current_user->ID;

		$hide_notice = sanitize_text_field( wp_unslash( $_GET['xts-hide-notice'] ) ); // phpcs:ignore

		/* If user clicks to ignore the notice, add that to their user meta */
		if ( $hide_notice ) {
			add_user_meta( $user_id, $hide_notice, true );
		}
	}
}
