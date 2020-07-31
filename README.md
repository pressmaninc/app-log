# App Log
App Log is a Wordpress plugin that allows users (mainly developers) to output logs for debugging purposes.

# Installation
1.	Upload the 'app-log' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the Plugins menu in WordPress.

# How to use
There are 2 ways to use the plugin
1. Call the ready-to-use applog function\
	By calling the function `applog`, the message will written to the log file.\
	`applog( 'Hello' ) ` will output 'Hello' in the log file

2. Use the 'app_log' hook\
	If another plugin will use this to output logs, it is best to use this option instead of calling `applog` function to avoid Fatal Error in case this plugin is deactivated.\
	`do_action( 'app_log' , 'Hello' )` will output 'Hello' in the log file

Log files are listed on the Administrator Dashboard page for easier viewing and deleting.

By default, log files are stored in */wp-content/plugins/app-log/applog* but log directory can be changed either via Dashboard page or Settings page.