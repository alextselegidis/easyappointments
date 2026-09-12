<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file contains the database settings.
| The settings are now saved in the config file for easy future updates.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
|				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = defined('Config::DB_ACTIVE_GROUP') && !empty(Config::DB_ACTIVE_GROUP) ? Config::DB_ACTIVE_GROUP : 'default';
$query_builder = TRUE;

/* ====================================================================
 * ATTENTION
 * ====================================================================
 * The configuration settings below are default values.
 * To customize them, please set your own values in the main config file
 * (config.php) to ensure they persist across application updates.
 * Have a look into "config-sample.php" for an example of how to set your
 * own database settings.
 * ====================================================================
 */

$base_db_settings = [
    'hostname' => 'mysql',
    'username' => 'user',
    'password' => 'password',
    'database' => 'easyappointments',
    'dbdriver' => 'mysqli',
    'dbprefix' => 'ea_',
    'pconnect' => FALSE,
    'db_debug' => TRUE,
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_unicode_ci',
    'swap_pre' => '',
    'autoinit' => TRUE,
    'stricton' => FALSE,
];

// The default database settings use the old database settings for backward compatibility.
$db['default']['hostname'] = defined('Config::DB_HOST') && !empty(Config::DB_HOST) ? Config::DB_HOST : $base_db_settings['hostname'];
$db['default']['username'] = defined('Config::DB_USERNAME') && !empty(Config::DB_USERNAME) ? Config::DB_USERNAME : $base_db_settings['username'];
$db['default']['password'] = defined('Config::DB_PASSWORD') && !empty(Config::DB_PASSWORD) ? Config::DB_PASSWORD : $base_db_settings['password'];
$db['default']['database'] = defined('Config::DB_NAME') && !empty(Config::DB_NAME) ? Config::DB_NAME : $base_db_settings['database'];

// The default database settings use the new database settings for backward compatibility
$db['default'] = array_replace($base_db_settings, $db['default']);

if (defined(Config::class . '::DB_SETTINGS') && is_array(Config::DB_SETTINGS)) {
    foreach (Config::DB_SETTINGS as $group => $settings) {
        $db[$group] = array_replace(
            $base_db_settings,
            $settings
        );
    }
}

/* End of file database.php */
/* Location: ./application/config/database.php */
