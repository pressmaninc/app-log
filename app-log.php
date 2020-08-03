<?php
/*
Plugin Name: App Log
Plugin URI:
Description: A simple logger for debugging.
Version: 1.0
Author: PRESSMAN
Author URI: https://www.pressman.ne.jp/
Text Domain: aplg
Domain Path: /lang
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function applog( $message, $dirname = '' ) {
	Aplg_Logger::log( $message, $dirname );
}

/**
 * Class App_Log
 */
class App_Log {

	private static $instance;

	/**
	 * Plugin Class constructor
	 */
	private function __construct() {
		require_once plugin_dir_path( __FILE__ ) . 'admin/aplg-dashboard.php';
		require_once plugin_dir_path( __FILE__ ) . 'admin/aplg-settings.php';
		require_once plugin_dir_path( __FILE__ ) . 'classes/class-aplg-logger.php';
		require_once plugin_dir_path( __FILE__ ) . 'app-log-samples.php';

		// Load text domain
		add_action( 'init', array( $this, 'load_aplg_textdomain' ) );
		// Allow other plugins to output log using 'app_log' hook
		add_action( 'app_log', array( 'Aplg_Logger', 'log' ), 10 );
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
	 * Loads the text domain for app log plugin
	 */
	public function load_aplg_textdomain() {
		load_plugin_textdomain( 'aplg', false, basename( dirname( __FILE__ ) ) . '/lang' );
	}
}

App_Log::get_instance();
