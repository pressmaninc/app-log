<?php
/*
Plugin Name: App Log
Plugin URI:
Description: A simple logger for debugging.
Version: 1.1.2
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

require_once dirname( __FILE__ ) . '/app-log-functions.php';

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

		register_activation_hook( __FILE__, array( $this, 'set_applog_options' ) );
		register_uninstall_hook( __FILE__, array( __CLASS__, 'remove_applog_options' ) );

		// Load text domain
		add_action( 'init', array( $this, 'load_aplg_textdomain' ), 10 );
		// Allow other plugins to output log using 'applog' hook
		add_action( 'applog', array( 'Aplg_Logger', 'log' ), 10, 3 );
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
	 * プラグインを有効化する時にオプションを追加する
	 *
	 * @return void
	 */
	public function set_applog_options() {
		$option = get_option( 'aplg_settings' );
		if ( ! $option ) {
			$default_settings = array(
				'log_directory' => Aplg_Settings::get_path_to_logdir(),
				'enable_disable_maillog' => 0,
			);
			update_option( 'aplg_settings', $default_settings );
		}
	}

	/**
	 * Applog設定をオプションから削除
	 *
	 * @return void
	 */
	public static function remove_applog_options() {
		delete_option( 'aplg_settings' );
	}
	
	/**
	 * Loads the text domain for app log plugin
	 */
	public function load_aplg_textdomain() {
		load_plugin_textdomain( 'aplg', false, basename( dirname( __FILE__ ) ) . '/lang' );
	}
}

App_Log::get_instance();
