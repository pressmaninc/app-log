<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Aplg_Log_Samples {

	private static $instance;

	/**
	 * App Log Samples constructor
	 */
	private function __construct() {
		add_filter( 'wp_mail', array( $this, 'log_mail' ), 10 );
	}

	/**
	 * Getter for the class' instance
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Logs mail details when maillog setting in App Log is enabled
	 *
	 * @param array $args
	 */
	public function log_mail( $args ) {
		$options = get_option( 'aplg_settings' );
		if ( $options && array_key_exists( 'enable_disable_maillog', $options ) && '1' === $options['enable_disable_maillog'] ) {
			applog( $args['subject'] . '|' . $args['message'] );
		}

		return $args;
	}
}

Aplg_Log_Samples::get_instance();
