<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Aplg_Settings' ) ) {
	require_once plugin_dir_path( __DIR__ ) . 'admin/aplg-settings.php';
}

class Aplg_Logger {
	const LOG_AUTO_DELETE_PROBABILITY = 10; // Auto-delete will be run within 10% probability

	/**
	 * Outputs log to the specified directory
	 *
	 * @param mixed  $message
	 * @param string $dirname
	 */
	public static function log( $message, $dirname = '' ) {

		if ( ! is_string( $message ) ) {
			$message = print_r( $message, true );
		}

		$message  = date_i18n( 'Y-m-d H:i:s' ) . ' ' . $message . "\n";
		$filename = date_i18n( 'Ymd' ) . '.log';
		$log_dir  = Aplg_Settings::get_path_to_logdir( $dirname );
		$log_file = $log_dir . $filename;

		// Create directory if it doesn't exist
		if ( ! file_exists( $log_dir ) || ! is_dir( $log_dir ) ) {
			$flag = mkdir( $log_dir, 0777 );
		}

		// TODO: Add processing in the case when log directory creation fails

		// Write to file
		$fp = fopen( $log_file, 'a' );
		fwrite( $fp, $message );
		fclose( $fp );

		// Delete old logs in bulk (for garbage collection)
		self::log_auto_delete( $log_dir );
	}

	/**
	 * Deletes old logs in specified directory in bulk (for garbage collection)
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
		$path_to_file = Aplg_Settings::get_path_to_logdir() . $filename;
		if ( is_file( $path_to_file ) ) {
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
	}
}
