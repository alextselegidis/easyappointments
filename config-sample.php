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

    const DB_HOST = 'mysql';
    const DB_NAME = 'easyappointments';
    const DB_USERNAME = 'user';
    const DB_PASSWORD = 'password';

    // ------------------------------------------------------------------------
    // GOOGLE CALENDAR SYNC
    // ------------------------------------------------------------------------

    const GOOGLE_SYNC_FEATURE = false; // Enter TRUE or FALSE
    const GOOGLE_CLIENT_ID = '';
    const GOOGLE_CLIENT_SECRET = '';

    // ------------------------------------------------------------------------
    // E-MAIL SETTINGS
    // ------------------------------------------------------------------------

    // Add custom values by settings them to the $config array in application/config/email.php.
    // @link https://codeigniter.com/user_guide/libraries/email.html

    const EMAIL_USERAGENT = 'Easy!Appointments';
    const EMAIL_PROTOCOL = 'mail'; // or 'smtp'
    const EMAIL_MAILTYPE = 'html';  // or 'text'
    const EMAIL_SMTP_DEBUG = '0';  // or '1'
    const EMAIL_SMTP_AUTH = TRUE; //or FALSE for anonymous relay.
    const EMAIL_SMTP_HOST = '';
    const EMAIL_SMTP_USER = '';
    const EMAIL_SMTP_PASS = '';
    const EMAIL_SMTP_CRYPTO = 'ssl';
    const EMAIL_SMTP_PORT = 25;
    const EMAIL_FROM_NAME = '';
    const EMAIL_FROM_ADDRESS = '';
    const EMAIL_REPLY_TO = '';


    /* Example Exchange OnPremise 2019
    const EMAIL_PROTOCOL = 'smtp'; // or 'smtp'
    const EMAIL_SMTP_AUTH = TRUE; //or FALSE for anonymous relay.
    const EMAIL_SMTP_HOST = ''; // hostname
    const EMAIL_SMTP_USER = ''; // e-mail
    const EMAIL_SMTP_PASS = ''; // password
    const EMAIL_SMTP_CRYPTO = 'tls'; // STARTTLS necessary
    const EMAIL_SMTP_PORT = 587;
    const EMAIL_FROM_ADDRESS = ''; // e-mail
    */
}
