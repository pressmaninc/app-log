<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Aplg_Settings' ) ) {
	require_once plugin_dir_path( __DIR__ ) . 'admin/aplg-settings.php';
}

class Aplg_Logger {
	const LOG_AUTO_DELETE_PROBABILITY = 10; // Auto-delete will be run within 10% probabiliïty

	const LOG_LEVEL                   = array(
		'TRACE_LOG' => 'TRACE',
		'DEBUG_LOG' => 'DEBUG',
		'INFO_LOG'  => 'INFO',
		'WARN_LOG'  => 'WARN',
		'ERROR_LOG' => 'ERROR',
		'FATAL_LOG' => 'FATAL',
	);

	const ALLOWED_FILE_EXT            = array(
		'LOG' => '.log',
		'TXT' => '.txt',
	);

	/**
	 * Outputs log to the specified directory
	 *
	 * @param mixed  $org_message
	 * @param string $dirname
	 */
	public static function log( $org_message, $dirname = '', $log_level = self::LOG_LEVEL['TRACE_LOG'] ) {
		if ( ! in_array( $log_level, array_values( self::LOG_LEVEL ) ) ) {
			$log_level = self::LOG_LEVEL['TRACE_LOG'];
		}

		$message = self::prepare_message( $org_message );

		$log_header = '[' . self::format_date_by_wp_version( 'Y-m-d H:i:s' ) . '] [' . str_pad( $log_level, 5, ' ' ) . '] ';
		$process_id = getmypid();
		if ( $process_id ) {
			$log_header .= '(PID: ' . $process_id . ') ';
		}
		$message = $log_header . $message . "\n";

		/**
		 * Filters the file extension of log.
		 *
		 * @param string $log_file_ext
		 */
		$log_file_ext = apply_filters( 'app_log_file_ext', self::ALLOWED_FILE_EXT['LOG'] );
		if ( strpos( $log_file_ext, '.' ) !== 0 ) {
			$log_file_ext = '.' . $log_file_ext;
		}

		if ( ! in_array( $log_file_ext, self::ALLOWED_FILE_EXT, true ) ) {
			$log_file_ext = self::ALLOWED_FILE_EXT['LOG'];
		}

		$filename = self::format_date_by_wp_version( 'Ymd' ) . $log_file_ext;
		$log_dir  = Aplg_Settings::get_path_to_log_dir( $dirname );

		// Create directory if it doesn't exist
		if ( realpath( $log_dir ) === false || ! is_dir( $log_dir ) ) {
			$res = mkdir( $log_dir, 0777, true );
			if ( ! $res ) {
				wp_die( 'Making output directory is failed. (' . esc_attr( $log_dir ) . ')' );
			}
		}

		// Write to file
		$log_file = realpath( $log_dir ) . '/' . $filename;

		/**
		 * Filters the flag which decide wether the message will be sent or not.
		 * This hook will be obsoleted in next major release.
		 *
		 * @param mixed   $pre
		 * @param string  $message
		 * @param mixed   $org_message
		 * @param string  $log_level
		 * @param string  $log_file
		 * @param integer $process_id
		 */
		$pre = apply_filters( 'pre_applog_write', null, $message, $org_message, $log_level, $log_file, $process_id );
		if ( ! is_null( $pre ) ) {
			return;
		}

		/**
		 * Filters the message before sending.
		 * If you return false, the message will not be sent.
		 *
		 * @param string  $message
		 * @param mixed   $org_message
		 * @param string  $log_level
		 * @param string  $log_file
		 * @param integer $process_id
		 */
		$message = apply_filters( 'app_log_write_log_before', $message, $org_message, $log_level, $log_file, $process_id );
		if ( false === $message ) {
			return;
		}

		$fp = fopen( $log_file, 'a' );
		fwrite( $fp, $message );
		fclose( $fp );

		/**
		 * Fires after the message is sent.
		 *
		 * @param string  $message
		 * @param mixed   $org_message
		 * @param string  $log_level
		 * @param string  $log_file
		 * @param integer $process_id
		 */
		do_action( 'app_log_write_log_after', $message, $org_message, $log_level, $log_file, $process_id );

		// Delete old logs in bulk.
		self::log_auto_delete( $log_dir );
	}

	/**
	 * Delete old logs in the specified directory in bulk.
	 *
	 * @param string $log_dir
	 */
	public static function log_auto_delete( $log_dir ) {
		// Check if auto-delete will be performed or not
		$rand = rand( 1, 100 );
		if ( $rand > self::LOG_AUTO_DELETE_PROBABILITY ) {
			return;
		}

		// Get all files
		$files = glob( $log_dir . '*' );

		// Delete log files that are older than the specified lifetime (dot files are excluded)
		if ( $files && ! empty( $files ) ) {
			foreach ( $files as $file ) {
				preg_match( '/^\./', $file, $m );
				if ( is_file( $file ) && empty( $m ) ) {
					if ( filemtime( $file ) < time() - Aplg_Settings::get_log_lifetime() ) {
						unlink( $file );
					}
				}
			}
		}
	}

	/**
	 * Deletes the specified log file
	 *
	 * @param string $filename
	 * @return array
	 */
	public static function delete_log( $filename ) {
		$path_to_file = realpath( Aplg_Settings::get_path_to_log_dir() ) . '/' . $filename;
		if ( ! is_file( $path_to_file ) ) {
			return array(
				'type'    => 'error',
				'message' => __( 'Failed to delete log.', 'aplg' ),
			);
		}

		$flag = unlink( $path_to_file );
		if ( $flag ) {
			return array(
				'type'    => 'success',
				'message' => sprintf( __( '%s successfully deleted.', 'aplg' ), $filename ),
			);
		} else {
			return array(
				'type'    => 'error',
				'message' => __( 'Failed to delete log.', 'aplg' ),
			);
		}
	}

	/**
	 * Prepare the given message for output.
	 *
	 * @param mixed $message
	 * @return string
	 */
	public static function prepare_message( mixed $message ):string {
		$var_dump_mode = apply_filters( 'app_log_ouput_var_dump_mode', Aplg_Settings::OUTPUT_VAR_DUMP_MODE );

		if ( ! $var_dump_mode ) {
			$message = print_r( $message, true );
		} else {
			ob_start();
			var_dump( $message );
			$message = ob_get_clean();
			trim( $message );
		}

		return $message;
	}

	public static function format_date_by_wp_version($format) {
		/**
		 * Filters format of date.
		 *
		 * @param int $log_lifetime
		 */
		$format = apply_filters('app_log_date_format', $format);
		if ( version_compare($GLOBALS['wp_version'], '5.3', '<' ) ) {
			return date_i18n($format);
		} else {
			return wp_date($format);
		}
	}
}
