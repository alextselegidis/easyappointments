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

// ------------------------------------------------------------------------
// CROSS-ORIGIN REQUESTS (Optional)
// ------------------------------------------------------------------------
// Uncomment and list the origins that are allowed to send cross-origin
// requests to this installation, separated by commas. Every origin must
// include the scheme and, when it is not the default one, the port.
//
// Leave this commented out unless another website needs to call this
// installation from the browser: when it is not defined, all cross-origin
// requests are denied. Embedding the booking page in an iframe does not
// need it either.
//
// define('CORS_ALLOWED_ORIGINS', 'https://example.org,https://www.example.org');

// ------------------------------------------------------------------------
// CALDAV SERVERS ON THE LOCAL NETWORK (Optional)
// ------------------------------------------------------------------------
// CalDAV URLs that resolve to a private or reserved address are rejected, so
// that a URL entered in the backend cannot make this installation reach into
// its own network.
//
// Uncomment and list the host names of the CalDAV servers that are allowed to
// resolve to such an address, separated by commas. Only add hosts you run
// yourself.
//
// define('CALDAV_ALLOWED_HOSTS', 'caldav.internal,baikal');

// ------------------------------------------------------------------------
// EMBEDDING THE BOOKING PAGE ON ANOTHER DOMAIN (Optional)
// ------------------------------------------------------------------------
// The booking page can be embedded in an iframe on any website out of the
// box, and booking an appointment works there without any of the settings
// below.
//
// Browsers do withhold the cookies of this installation inside an iframe of
// another domain, so anything that needs the session does not work there.
// In practice that is the image CAPTCHA, whose phrase is kept in the
// session: every request would start a new one and the check could never
// pass. ALTCHA is verified without the session, so prefer it when the
// booking page is embedded.
//
// Uncomment the line below to send the cookies in that context anyway.
//
// IMPORTANT: "None" requires HTTPS, as browsers reject such a cookie over a
// plain connection, and it makes the cookies of this installation reachable
// from requests started by other websites. Leave it alone unless the
// embedded CAPTCHA is actually needed.
//
// define('COOKIE_SAMESITE', 'None');
