<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const LOG_LEVEL = array(
	'TRACE_LOG' => 'TRACE',
	'DEBUG_LOG' => 'DEBUG',
	'INFO_LOG'  => 'INFO',
	'WARN_LOG'  => 'WARN',
	'ERROR_LOG' => 'ERROR',
	'FATAL_LOG' => 'FATAL',
);

/**
 * For compatibility with the original version pm_log
 *
 * @param mixed  $message
 * @param string $dirnmae
 * @param string $log_level
 */
function pm_log( $message, $dirnmae = '', $log_level = LOG_LEVEL[ 'TRACE_LOG' ] ) {
	if ( ! in_array( $log_level, array_values( LOG_LEVEL ) ) ) {
		$log_level = LOG_LEVEL[ 'TRACE_LOG' ];
	}
	Aplg_Logger::log( $message, $dirname, $log_level );
}

/**
 * Ready-to-use function for logging
 *
 * @param mixed  $message
 * @param string $dirname
 * @param string $log_level
 */
function applog( $message, $dirname = '', $log_level = LOG_LEVEL[ 'TRACE_LEVEL' ] ) {
	if ( ! in_array( $log_level, array_values( LOG_LEVEL ) ) ) {
		$log_level = LOG_LEVEL[ 'TRACE_LOG' ];
	}
	Aplg_Logger::log( $message, $dirname, $log_level );
}

/**
 * Ready-to-use function for trace logs
 *
 * @param mixed  $message
 * @param string $dirname
 */
function applog_trace( $message, $dirname = '' ) {
	Aplg_Logger::log( $message, $dirname, LOG_LEVEL[ 'TRACE_LOG' ] );
}

/**
 * Ready-to-use function for debug logs
 *
 * @param mixed  $message
 * @param string $dirname
 */
function applog_debug( $message, $dirname = '' ) {
	Aplg_Logger::log( $message, $dirname, LOG_LEVEL[ 'DEBUG_LOG' ] );
}

/**
 * Ready-to-use function for info logs
 *
 * @param mixed  $message
 * @param string $dirname
 */
function applog_info( $message, $dirname = '' ) {
	Aplg_Logger::log( $message, $dirname, LOG_LEVEL[ 'INFO_LOG' ] );
}

/**
 * Ready-to-use function for warn logs
 *
 * @param mixed  $message
 * @param string $dirname
 */
function applog_warn( $message, $dirname = '' ) {
	Aplg_Logger::log( $message, $dirname, LOG_LEVEL[ 'WARN_LOG' ] );
}

/**
 * Ready-to-use function for error logs
 *
 * @param mixed  $message
 * @param string $dirname
 */
function applog_error( $message, $dirname = '' ) {
	Aplg_Logger::log( $message, $dirname, LOG_LEVEL[ 'ERROR_LOG' ] );
}

/**
 * Ready-to-use function for fatal logs
 *
 * @param mixed  $message
 * @param string $dirname
 */
function applog_fatal( $message, $dirname = '' ) {
	Aplg_Logger::log( $message, $dirname, LOG_LEVEL[ 'FATAL_LOG' ] );
}
