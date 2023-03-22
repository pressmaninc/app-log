<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Aplg_Settings {

	// const LOG_DIR                      = '/applog/';
	const LOG_AUTO_DELETE_MAX_LIFETIME = 7776000; // Valid for 90 days
	const DELETE_KEY                   = 'aplg_delete';
	const OUTPUT_VAR_DUMP_MODE         = false;
	const READONLY_ATTR                = 'readonly="readonly"';

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
			App_Log::OPTION_KEY,
			array( $this, 'render_options_page' )
		);

		// Include css file for settings
		wp_enqueue_style( App_Log::OPTION_KEY . '-setting', plugin_dir_url( __DIR__ ) . 'assets/css/aplg-settings.css' );
	}

	/**
	 * Adds necessary fields for App Log settings
	 */
	public function init_settings() {
		register_setting( App_Log::OPTION_KEY, App_Log::OPTION_KEY );

		add_settings_section(
			App_Log::OPTION_KEY . '_section',
			'',
			null,
			App_Log::OPTION_KEY,
		);

		add_settings_field(
			'log_directory',
			__( 'Log Directory', 'aplg' ),
			array( $this, 'render_log_directory_setting_field' ),
			App_Log::OPTION_KEY,
			App_Log::OPTION_KEY . '_section',
			array( 'class' => App_Log::OPTION_KEY . '_label' ),
		);

		add_settings_field(
			'enable_disable_maillog',
			__( 'Log emails sent by WordPress', 'aplg' ),
			array( $this, 'render_enable_disable_maillog' ),
			App_Log::OPTION_KEY,
			App_Log::OPTION_KEY . '_section',
			array( 'class' => App_Log::OPTION_KEY . '_label' ),
		);
	}

	/**
	 * Displays log directory field
	 */
	public function render_log_directory_setting_field() {
		$readonly = ( has_filter( 'app_log_path_to_log_dir' ) ) ? self::READONLY_ATTR : '';
		echo '<input type="text" name="' . esc_attr( App_Log::OPTION_KEY ) . '[log_directory]" value="' . esc_attr( self::get_path_to_log_dir() ) . '"' . $readonly . '>';
	}

	/**
	 * Displays field to enable/disable mail log
	 */
	public function render_enable_disable_maillog() {
		$options = get_option( App_Log::OPTION_KEY );
		$checked = ( ! empty( $options ) && isset( $options['enable_disable_maillog'] ) ) ? $options['enable_disable_maillog'] : 0;
		?>
		<label>
			<input type="checkbox" name="<?php echo App_Log::OPTION_KEY; ?>[enable_disable_maillog]" <?php checked( $checked, 1 ); ?>  value="1"> 
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
			<h1><?php echo __( 'App Log Settings', 'aplg' ); ?></h1>
			<p class="description"><?php echo __('If a value has been overwritten using a filter hook, you cannot change the settings on this screen.', 'aplg' ); ?></p>
			<form action='options.php' method='post'>
				<?php
				settings_fields( App_Log::OPTION_KEY );
				do_settings_sections( App_Log::OPTION_KEY );
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
	public static function get_path_to_log_dir( string $dirname = App_Log::LOG_DIR ):string {
		$app = App_Log::get_instance();
		$path_to_log_dir = $app->get_plugin_root_path() . $dirname;

		// If log directory option is set, use it instead of LOG_DIR
		$options         = get_option( App_Log::OPTION_KEY );
		$path_to_log_dir = ( $options && '' !== $options['log_directory'] ) ? (string) $options['log_directory'] : $path_to_log_dir;

		/**
		 * Filters the path for log directory.
		 *
		 * @param string $path_to_log_dir
		 */
		$path_to_log_dir = (string) apply_filters( 'app_log_path_to_log_dir', $path_to_log_dir );

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
