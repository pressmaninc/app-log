<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Aplg_Settings' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'classes/class-aplg-logger.php';
}

if ( ! function_exists( 'pm_log' ) ) {
	/**
	 * For compatibility with the original version pm_log
	 *
	 * @param mixed  $message
	 * @param string $dirname
	 * @param string $log_level
	 */
	function pm_log( $message, $dirname = '', $log_level = Aplg_Logger::LOG_LEVEL['TRACE_LOG'] ) {
		Aplg_Logger::log( $message, $dirname, $log_level );
	}
}

if ( ! function_exists( 'applog' ) ) {
	/**
	 * Ready-to-use function for logging
	 *
	 * @param mixed  $message
	 * @param string $dirname
	 * @param string $log_level
	 */
	function applog( $message, $dirname = '', $log_level = Aplg_Logger::LOG_LEVEL['TRACE_LOG'] ) {
		Aplg_Logger::log( $message, $dirname, $log_level );
	}
}

if ( ! function_exists( 'applog_trace' ) ) {
	/**
	 * Ready-to-use function for trace logs
	 *
	 * @param mixed  $message
	 * @param string $dirname
	 */
	function applog_trace( $message, $dirname = '' ) {
		Aplg_Logger::log( $message, $dirname, Aplg_Logger::LOG_LEVEL['TRACE_LOG'] );
	}
}

if ( ! function_exists( 'applog_debug' ) ) {
	/**
	 * Ready-to-use function for debug logs
	 *
	 * @param mixed  $message
	 * @param string $dirname
	 */
	function applog_debug( $message, $dirname = '' ) {
		Aplg_Logger::log( $message, $dirname, Aplg_Logger::LOG_LEVEL['DEBUG_LOG'] );
	}
}

if ( ! function_exists( 'applog_info' ) ) {
	/**
	 * Ready-to-use function for info logs
	 *
	 * @param mixed  $message
	 * @param string $dirname
	 */
	function applog_info( $message, $dirname = '' ) {
		Aplg_Logger::log( $message, $dirname, Aplg_Logger::LOG_LEVEL['INFO_LOG'] );
	}
}

if ( ! function_exists( 'applog_warn' ) ) {
	/**
	 * Ready-to-use function for warn logs
	 *
	 * @param mixed  $message
	 * @param string $dirname
	 */
	function applog_warn( $message, $dirname = '' ) {
		Aplg_Logger::log( $message, $dirname, Aplg_Logger::LOG_LEVEL['WARN_LOG'] );
	}
}

if ( ! function_exists( 'applog_error' ) ) {
	/**
	 * Ready-to-use function for error logs
	 *
	 * @param mixed  $message
	 * @param string $dirname
	 */
	function applog_error( $message, $dirname = '' ) {
		Aplg_Logger::log( $message, $dirname, Aplg_Logger::LOG_LEVEL['ERROR_LOG'] );
	}
}

if ( ! function_exists( 'applog_fatal' ) ) {
	/**
	 * Ready-to-use function for fatal logs
	 *
	 * @param mixed  $message
	 * @param string $dirname
	 */
	function applog_fatal( $message, $dirname = '' ) {
		Aplg_Logger::log( $message, $dirname, Aplg_Logger::LOG_LEVEL['FATAL_LOG'] );
	}
}
