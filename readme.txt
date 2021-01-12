=== App Log ===
Contributors: pressmaninc, hiroshisekiguchi, kazunao, muraokashotaro, razelpaldo
Tags: pressman, debug, log
Requires at least: 5.2.2
Tested up to: 5.6
Requires PHP: 5.6.20
Stable tag:　1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple logger for debugging.

== Description ==
App Log is a Wordpress plugin that allows users (mainly developers) to output logs for debugging purposes.
Log files are listed on the Administrator Dashboard page for easier viewing and deleting.
By default, log files are stored in */wp-content/plugins/app-log/applog* but log directory can be changed either via Dashboard page or Settings page.

== Installation ==
1. Upload the 'app-log' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the Plugins menu in WordPress.

== Changelog ==
= 1.1.2 =
* add hook 'pre_applog_write'

= 1.1.1 =
* bug fix on warning message displayed during password reset
* updated Japanese translation and some field labels

= 1.1 =
* added log level
* changed hook name from app_log to applog
* added filter to allow changing of log file extension

= 1.0 =
* first version.