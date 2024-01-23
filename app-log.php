<?php
/*
Plugin Name: App Log
Plugin URI:
Description: A simple logger for debugging.
Version: 1.1.4
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

require_once __DIR__ . '/app-log-functions.php';

/**
 * Main class.
 */
class App_Log {

	private static $instance;

	const PREFIX     = 'aplg_';
	const OPTION_KEY = 'aplg_settings';
	const LOG_DIR    = '/logs/';

	/**
	 * Plugin Class constructor
	 */
	private function __construct() {
		require_once plugin_dir_path( __FILE__ ) . 'admin/aplg-dashboard.php';
		require_once plugin_dir_path( __FILE__ ) . 'admin/aplg-settings.php';
		require_once plugin_dir_path( __FILE__ ) . 'classes/class-aplg-logger.php';
		require_once plugin_dir_path( __FILE__ ) . 'app-log-samples.php';

		register_activation_hook( __FILE__, array( $this, 'set_options' ) );
		register_uninstall_hook( __FILE__, array( __CLASS__, 'remove_options' ) );

		// Load text domain
		add_action( 'init', array( $this, 'load_textdomain' ), 10 );

		// Allow other plugins to output log using 'applog' hook
		add_action( 'applog', array( 'Aplg_Logger', 'log' ), 10, 3 );
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @return static The singleton instance of the class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set the default setting to option.
	 *
	 * @return void
	 */
	public function set_options() {
		$option = get_option( self::OPTION_KEY );
		if ( ! $option ) {
			update_option( self::OPTION_KEY, $this->get_default_setting() );
		}
	}

	/**
	 * Remove the option setting.
	 *
	 * @return void
	 */
	public static function remove_options() {
		delete_option( self::OPTION_KEY );
	}

	/**
	 * Loads the text domain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'aplg', false, basename( __DIR__ ) . '/lang' );
	}

	/**
	 * Get path to plugin root.
	 *
	 * @return string
	 */
	public function get_plugin_root_path(): string {
		return __DIR__;
	}

	/**
	 * Get default setting.
	 *
	 * @return array
	 */
	public function get_default_setting(): array {
		$default = array(
			'log_directory'          => Aplg_Settings::get_path_to_log_dir(),
			'enable_disable_maillog' => 0,
		);
		return $default;
	}
}

App_Log::get_instance();
