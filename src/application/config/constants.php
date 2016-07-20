<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/*
|--------------------------------------------------------------------------
| Application Data
|--------------------------------------------------------------------------
|
| These constants are used globally from the application when handling data.
|
*/
define('DB_SLUG_CUSTOMER', 'customer');
define('DB_SLUG_PROVIDER', 'provider');
define('DB_SLUG_ADMIN', 'admin');
define('DB_SLUG_SECRETARY', 'secretary');

define('FILTER_TYPE_PROVIDER', 'provider');
define('FILTER_TYPE_SERVICE', 'service');

define('AJAX_SUCCESS', 'SUCCESS');
define('AJAX_FAILURE', 'FAILURE');

define('SETTINGS_SYSTEM', 'SETTINGS_SYSTEM');
define('SETTINGS_USER', 'SETTINGS_USER');

define('PRIV_VIEW', 1);
define('PRIV_ADD', 2);
define('PRIV_EDIT', 4);
define('PRIV_DELETE', 8);

define('PRIV_APPOINTMENTS', 'appointments');
define('PRIV_CUSTOMERS', 'customers');
define('PRIV_SERVICES', 'services');
define('PRIV_USERS', 'users');
define('PRIV_SYSTEM_SETTINGS', 'system_settings');
define('PRIV_USER_SETTINGS', 'user_settings');

define('DATE_FORMAT_DMY', 'DMY');
define('DATE_FORMAT_MDY', 'MDY');
define('DATE_FORMAT_YMD', 'YMD');

define('MIN_PASSWORD_LENGTH', 7);
define('ANY_PROVIDER', 'any-provider');

define('CALENDAR_VIEW_DEFAULT', 'default'); 
define('CALENDAR_VIEW_TABLE', 'table'); 

define('AVAILABILITIES_TYPE_FLEXIBLE', 'flexible');
define('AVAILABILITIES_TYPE_FIXED', 'fixed');

/* End of file constants.php */
/* Location: ./application/config/constants.php */
