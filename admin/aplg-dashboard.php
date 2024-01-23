<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Aplg_Settings' ) ) {
	require_once plugin_dir_path( __DIR__ ) . 'admin/aplg-settings.php';
}

class Aplg_Dashboard {

	private static $instance;
	private static $notices;

	/**
	 * Dashboard constructor
	 */
	private function __construct() {
		// Dashboard widget display
		add_action( 'wp_dashboard_setup', array( $this, 'register' ), 10 );
		// Log Deletion
		add_action( 'admin_init', array( $this, 'delete_log' ), 10 );
		// Successful Log Deletion notification
		add_action( 'admin_init', array( $this, 'delete_done' ), 10 );
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
	 * Register dashboard widget.
	 */
	public function register() {
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		/**
		 * Filters the flag which decide wether the dashboard widget will be shown or not.
		 *
		 * @param boolian $flag
		 */
		if ( false === apply_filters( 'app_log_add_dashboard_widget', true ) ) {
			return;
		}

		wp_add_dashboard_widget(
			'aplg_widget',
			__( 'App Log', 'aplg' ),
			array( $this, 'display' ),
		);
	}

	/**
	 * Prepares the contents to be displayed in the Dashboard Widget
	 */
	public function display() {
		// Include javascript file for dashbord
		wp_enqueue_script( 'aplg-dashboard', plugin_dir_url( __DIR__ ) . 'assets/js/aplg-dashboard.js', array( 'jquery' ), null, true );
		wp_localize_script(
			'aplg-dashboard',
			'aplg_dashboard_obj',
			array(
				'delete_confirm_message' => __( 'File will be deleted. Are you sure you want to proceed?', 'aplg' ),
			)
		);
		// Include css file for dashboard
		wp_enqueue_style( 'aplg-dashboard', plugin_dir_url( __DIR__ ) . 'assets/css/aplg-dashboard.css' );

		$path_to_log_dir = Aplg_Settings::get_path_to_log_dir();
		$path_to_log_dir = realpath( $path_to_log_dir ) ?: $path_to_log_dir;
		$list_html       = '';

		if ( $path_to_log_dir !== false && file_exists( $path_to_log_dir ) ) {
			// Link to the actual file
			$url_to_log_dir = '';
			if ( strpos( $path_to_log_dir, ABSPATH ) === 0 ) {
				$url_to_log_dir = str_replace( ABSPATH, home_url( '/' ), $path_to_log_dir . '/' );
			}

			// Link for file deletion
			$url_for_delete_log_dir = wp_nonce_url( admin_url( '/' ), Aplg_Settings::DELETE_KEY ) . '&' . Aplg_Settings::DELETE_KEY . '=';
			$delete_label           = __( 'Delete', 'aplg' );

			// Get the log list & prepare html.
			$files = scandir( $path_to_log_dir );
			$list  = array();
			foreach ( $files as $file ) {
				preg_match( '/^\./', $file, $m );
				if ( ! is_dir( $file ) && empty( $m ) ) {
					if ( $url_to_log_dir === '' ) {
						$file_display = $file;
					} else {
						$file_display = '<a target="_blank" href="' . $url_to_log_dir . $file . '">' . $file . '</a>';
					}

					$list[] = '<li>' . $file_display
								. '<span class="delete_btn"><a href="' . $url_for_delete_log_dir . urlencode( $file ) . '">' . $delete_label . '</a></span>'
								. '</li>';
				}
			}
		}

		if ( empty( $list ) ) {
			$list = '<span>' . __( 'No logs found.', 'aplg' ) . '</span>';
		}

		self::show_content( $list, $path_to_log_dir );
	}

	/**
	 * Show content of the widget.
	 *
	 * @param mixed  $list
	 * @param string $log_dir
	 * @return void
	 */
	public function show_content( $list, string $log_dir ): void {
		$html = '';
		if ( is_array( $list ) ) {
			$html = '<ul>' . "\n" . implode( "\n", $list ) . "\n" . '</ul>' . "\n";
		} else {
			$html = $list;
		}

		?>
		<div class='wrapper'>
			<p><?php _e( 'Log File List', 'aplg' ); ?><br/>(<?php echo __( 'Path', 'aplg' ) . ': ' . esc_html( $log_dir ); ?>)</p>
			<?php echo $html; ?>
		</div>
		<?php
	}

	/**
	 * Delete the selected log file.
	 *
	 * TODO: Implement deletion using AJAX.
	 *
	 * @return void
	 */
	public function delete_log() {
		$delete_result = array();

		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) || defined( 'DOING_CRON' ) ) {
			return;
		}

		if ( ! isset( $_GET[ Aplg_Settings::DELETE_KEY ] ) || $_GET[ Aplg_Settings::DELETE_KEY ] == '' || ! isset( $_GET['_wpnonce'] ) ) {
			return;
		}

		$log_for_deletion = $_GET[ Aplg_Settings::DELETE_KEY ];
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], Aplg_Settings::DELETE_KEY ) ) {
			$delete_result = array(
				'type'    => 'error',
				'message' => __( 'Invalid access', 'aplg' ),
			);
		} // Valid access
		else {
			$delete_result = Aplg_Logger::delete_log( urldecode( $log_for_deletion ) );
		}

		if ( is_array( $delete_result ) && count( $delete_result ) > 0 ) {
			if ( 'success' === $delete_result['type'] ) {
				wp_redirect( admin_url( '/?' . Aplg_Settings::DELETE_KEY . '_done=' . $log_for_deletion ), 302 ); // To avoid multiple deletion
			} else {
				self::$notices = (array) $delete_result;
				add_action( 'admin_notices', array( $this, 'display_notice' ), 10 );
			}
		}
	}

	/**
	 * Display success notification after deletion
	 */
	public function delete_done() {
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) || defined( 'DOING_CRON' ) ) {
			return;
		}

		if ( ! isset( $_GET [ Aplg_Settings::DELETE_KEY . '_done' ] ) || $_GET [ Aplg_Settings::DELETE_KEY . '_done' ] == '' ) {
			return;
		}

		$deleted_log = urldecode( $_GET [ Aplg_Settings::DELETE_KEY . '_done' ] );
		// Data from GET not sanitized but decoded instead since the correct filename is needed to confirm if file is correctly deleted or not
		$log_dir   = realpath( Aplg_Settings::get_path_to_log_dir() );
		$file_path = $log_dir . '/' . $deleted_log;
		if ( $log_dir !== false && ! file_exists( $file_path ) ) {
			self::$notices = array(
				'type'    => 'success',
				'message' => sprintf( __( '%s successfully deleted.', 'aplg' ), esc_html( $deleted_log ) ),
			);
			add_action( 'admin_notices', array( $this, 'display_notice' ), 10 );
		}
	}

	/**
	 * Displays notification message in admin page
	 */
	public function display_notice() {
		?>
		<div class="notice notice-<?php echo self::$notices['type']; ?>">
			<p><?php echo self::$notices['message']; ?></p>
		</div>
		<?php
		self::$notices = array();
	}
}

Aplg_Dashboard::get_instance();
