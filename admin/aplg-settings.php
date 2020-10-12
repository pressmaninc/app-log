<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Aplg_Settings {
	const APLG_LOG_DIR                 = '/applog/';
	const LOG_AUTO_DELETE_MAX_LIFETIME = 7776000; // Valid for 90 days
	const DELETE_KEY                   = 'aplg_delete';

	private static $instance;

	/**
	 * App Log Settings constructor
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ), 10 );
		add_action( 'admin_init', array( $this, 'init_settings' ), 10 );
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
	 * Adds App Log menu under Settings
	 */
	public function add_menu() {
		add_options_page(
			__( 'App Log Settings', 'aplg' ),
			__( 'App Log', 'aplg' ),
			'manage_options',
			'aplg_settings',
			array( $this, 'render_options_page' )
		);

		// Include css file for settings
		wp_enqueue_style( 'aplg-settings', plugin_dir_url( __DIR__ ) . 'assets/css/aplg-settings.css' );
	}

	/**
	 * Adds necessary fields for App Log settings
	 */
	public function init_settings() {
		register_setting( 'aplg_settings', 'aplg_settings' );

		add_settings_section(
			'aplg_settings_section',
			__( 'App Log Settings', 'aplg' ),
			null,
			'aplg_settings'
		);

		add_settings_field(
			'log_directory',
			__( 'Log Directory', 'aplg' ),
			array( $this, 'render_log_directory_setting_field' ),
			'aplg_settings',
			'aplg_settings_section',
			array( 'class' => 'aplg_settings_label' ),
		);

		add_settings_field(
			'enable_disable_maillog',
			__( 'Log emails sent by WordPress', 'aplg' ),
			array( $this, 'render_enable_disable_maillog' ),
			'aplg_settings',
			'aplg_settings_section',
			array( 'class' => 'aplg_settings_label' ),
		);
	}

	/**
	 * Displays log directory field
	 */
	public function render_log_directory_setting_field() {
		echo '<input type="text" name="aplg_settings[log_directory]" value="' . esc_attr( self::get_path_to_logdir() ) . '" style="width: 75%">';
	}

	/**
	 * Displays field to enable/disable mail log
	 */
	public function render_enable_disable_maillog() {
		$options = get_option( 'aplg_settings' );
		?>
		<label>
			<input type="checkbox" name="aplg_settings[enable_disable_maillog]" <?php checked( $options? $options['enable_disable_maillog'] : 0, 1 ); ?>  value="1"> 
			<?php echo __( 'Enable', 'aplg' ); ?>
		</label>
		<?php
	}

	/**
	 * Displays App Log Settings page
	 */
	public function render_options_page() {
		?>
		<div class="wrap">
			<form action='options.php' method='post'>
				<?php
				settings_fields( 'aplg_settings' );
				do_settings_sections( 'aplg_settings' );
				submit_button();
				?>
			</form>
		</div> 
		<?php
	}

	/**
	 * Getter for log directory
	 *
	 * @param string $dirname
	 *
	 * @return string
	 */
	public static function get_path_to_logdir( $dirname = '' ) {
		$dirname         = ( $dirname != '' ) ? $dirname : self::APLG_LOG_DIR;
		$path_to_log_dir = dirname( __DIR__ ) . $dirname;

		// If log directory option is set, use it instead of APLG_LOG_DIR
		$options         = get_option( 'aplg_settings' );
		$path_to_log_dir = ( $options && $options['log_directory'] != '' ) ? $options['log_directory'] : $path_to_log_dir;

		return $path_to_log_dir;
	}

	/**
	 * Getter for log lifetime
	 *
	 * @return int
	 */
	public static function get_log_lifetime() {
		return self::LOG_AUTO_DELETE_MAX_LIFETIME;
	}
}

if ( is_admin() ) {
	Aplg_Settings::get_instance();
}
