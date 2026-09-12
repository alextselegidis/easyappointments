<?php
/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Easy!Appointments Configuration File
 *
 * Set your installation BASE_URL * without the trailing slash * and the database
 * credentials in order to connect to the database. You can enable the DEBUG_MODE
 * while developing the application.
 *
 * Set the default language by changing the LANGUAGE constant. For a full list of
 * available languages look at the /application/config/config.php file.
 *
 * IMPORTANT:
 * If you are updating from version 1.0 you will have to create a new "config.php"
 * file because the old "configuration.php" is not used anymore.
 */
class Config
{
    // ------------------------------------------------------------------------
    // GENERAL SETTINGS
    // ------------------------------------------------------------------------

    const BASE_URL = 'http://localhost';
    const LANGUAGE = 'english';
    const DEBUG_MODE = false;

    // ------------------------------------------------------------------------
    // DATABASE SETTINGS
    // ------------------------------------------------------------------------
    const DB_ACTIVE_GROUP = 'default';  // name of the group, can be anything you want. Defined in DB_SETTINGS below. e.x. "default", "custom1", "example2" etc.
    const DB_SETTINGS = [
        'default' => [  // name of the group, can be anything you want
            /* REQUIRED - Change the following settings to match your database credentials */
            'hostname' => 'mysql',                  // value of DB_HOST
            'database' => 'easyappointments',       // value of DB_NAME
            'username' => 'user',                   // value of DB_USERNAME
            'password' => 'password',               // value of DB_PASSWORD
            /* OPTIONAL - Use the following settings if you need different values instead of the defaults only! */
            // 'dbdriver' => 'mysqli',
            // 'dbprefix' => 'ea_',
            // 'pconnect' => FALSE,
            // 'db_debug' => TRUE,
            // 'cache_on' => FALSE,
            // 'cachedir' => '',
            // 'char_set' => 'utf8mb4',
            // 'dbcollat' => 'utf8mb4_unicode_ci',
            // 'swap_pre' => '',
            // 'autoinit' => TRUE,
            // 'stricton' => FALSE,
        ],
        // 'custom1' => [
        //     /* REQUIRED - Change the following settings to match your database credentials */
        //     'hostname' => 'mysql',
        //     'database' => 'easyappointments',
        //     'username' => 'user',
        //     'password' => 'password',
        //     /* OPTIONAL params see above */
        // ],
        // 'example2' => [
        //     /* REQUIRED - Change the following settings to match your database credentials */
        //     'hostname' => 'mysql',
        //     'database' => 'easyappointments',
        //     'username' => 'user',
        //     'password' => 'password',
        //     /* OPTIONAL params see above */
        // ],
    ];
    
    // ------------------------------------------------------------------------
    // EMAIL SETTINGS
    // ------------------------------------------------------------------------
    const EMAIL_SETTINGS = [
        /* OPTIONAL - Use the following settings if you need different values instead of the defaults only! */
        // 'useragent' => 'Easy!Appointments',
        // 'protocol' => 'mail',   // or 'smtp'
        // 'smtp_host' => '',
        // 'smtp_user' => '',
        // 'smtp_pass' => '',
        // 'smtp_crypto' => 'ssl',  // or 'tls'
        // 'smtp_port' => 25,
        // 'starttls' => FALSE,
        // 'smtp_debug' => 0,       // or '1'
        // 'smtp_auth' => TRUE,     // or FALSE for anonymous relay
        // 'from_name' => '',
        // 'from_address' => '',
        // 'reply_to' => '',
        // 'wordwrap' => TRUE,
        // 'wrapchars' => 76,
        // 'mailtype' => 'html',    // or 'text'
        // 'priority' => 3,
        // 'bcc_batch_mode' => FALSE,
        // 'bcc_batch_size' => 200,
        // 'dsn' => FALSE
        // 'crlf' => "\r\n",
        // 'newline' => "\r\n",
    ];

    // ------------------------------------------------------------------------
    // GOOGLE CALENDAR SYNC (Optional - can also be configured via UI)
    // ------------------------------------------------------------------------
    // These settings are optional and can be configured through the admin UI
    // at Settings > Integrations > Google Calendar. If configured here, they
    // will be used as fallback values.
    //
    // const GOOGLE_SYNC_FEATURE = false;
    // const GOOGLE_CLIENT_ID = '';
    // const GOOGLE_CLIENT_SECRET = '';
}
