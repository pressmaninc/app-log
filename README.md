# App Log
App Log is a Wordpress plugin that allows users (mainly developers) to output logs for debugging purposes.

# Installation
1.	Upload the 'app-log' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the Plugins menu in WordPress.

# How to use
There are 2 ways to use the plugin
1. Call the ready-to-use functions below
   - `applog( message, directory, log level )`
   - `applog_trace( message, directory )`
   - `applog_debug( message, directory )`
   - `applog_info( message, directory )`
   - `applog_warn( message, directory )`
   - `applog_error( message, directory )`
   - `applog_fatal( message, directory )`

  	The directory parameter refers to a subdirectory inside the plugin directory. Default value is empty. If it is empty, log will be stored in the log directory path displayed in the settings page.

	The log level parameter in the `applog` function has a default value of 'TRACE'. If no value or an invalid value is passed, log level is automatically set to 'TRACE'.

	By calling any of the functions above, the message will written to the log file.\
	For example, `applog( 'Hello' ) ` will output 'Hello' with TRACE log level in the log file

2. Use the 'applog' hook\
	If another plugin will use this to output logs, it is best to use this option instead of calling `applog` function to avoid Fatal Error in case this plugin is deactivated.\
	`do_action( 'applog' , 'Hello' )` will output 'Hello' with TRACE log level in the log file

Log files are listed on the Administrator Dashboard page for easier viewing and deleting.

By default, log files are stored in */wp-content/plugins/app-log/applog* but log directory can be changed either via Dashboard page or Settings page.