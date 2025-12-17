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
 *
 * RAILWAY DEPLOYMENT:
 * This configuration supports environment variables for Railway deployment.
 * Set the following environment variables in Railway:
 * - BASE_URL: Your Railway app URL (e.g., https://your-app.up.railway.app)
 * - DB_HOST: Database host (use Railway's MySQL service variable)
 * - DB_NAME: Database name
 * - DB_USERNAME: Database username
 * - DB_PASSWORD: Database password
 * - LANGUAGE: Interface language (default: russian)
 * - DEBUG_MODE: Set to 'true' for debugging (default: false)
 */
class Config
{
    // ------------------------------------------------------------------------
    // GENERAL SETTINGS
    // ------------------------------------------------------------------------

    // Use environment variable or default value
    const BASE_URL = self::ENV_BASE_URL;
    const LANGUAGE = self::ENV_LANGUAGE;
    const DEBUG_MODE = self::ENV_DEBUG_MODE;

    // Environment variable helpers (evaluated at runtime via getenv)
    private const ENV_BASE_URL = null;
    private const ENV_LANGUAGE = null;
    private const ENV_DEBUG_MODE = null;

    /**
     * Get configuration value from environment or default
     */
    public static function getBaseUrl(): string
    {
        return getenv('BASE_URL') ?: 'http://localhost';
    }

    public static function getLanguage(): string
    {
        return getenv('LANGUAGE') ?: 'russian';
    }

    public static function getDebugMode(): bool
    {
        $debug = getenv('DEBUG_MODE');
        return $debug === 'true' || $debug === '1';
    }

    // ------------------------------------------------------------------------
    // DATABASE SETTINGS
    // ------------------------------------------------------------------------

    const DB_HOST = self::ENV_DB_HOST;
    const DB_NAME = self::ENV_DB_NAME;
    const DB_USERNAME = self::ENV_DB_USERNAME;
    const DB_PASSWORD = self::ENV_DB_PASSWORD;

    private const ENV_DB_HOST = null;
    private const ENV_DB_NAME = null;
    private const ENV_DB_USERNAME = null;
    private const ENV_DB_PASSWORD = null;

    public static function getDbHost(): string
    {
        return getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'mysql';
    }

    public static function getDbName(): string
    {
        return getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'easyappointments';
    }

    public static function getDbUsername(): string
    {
        return getenv('DB_USERNAME') ?: getenv('MYSQLUSER') ?: 'user';
    }

    public static function getDbPassword(): string
    {
        return getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: 'password';
    }

    public static function getDbPort(): int
    {
        return (int)(getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: 3306);
    }

    // ------------------------------------------------------------------------
    // GOOGLE CALENDAR SYNC
    // ------------------------------------------------------------------------

    const GOOGLE_SYNC_FEATURE = false; // Enter TRUE or FALSE
    const GOOGLE_CLIENT_ID = '';
    const GOOGLE_CLIENT_SECRET = '';

    public static function getGoogleSyncFeature(): bool
    {
        $sync = getenv('GOOGLE_SYNC_FEATURE');
        return $sync === 'true' || $sync === '1';
    }

    public static function getGoogleClientId(): string
    {
        return getenv('GOOGLE_CLIENT_ID') ?: '';
    }

    public static function getGoogleClientSecret(): string
    {
        return getenv('GOOGLE_CLIENT_SECRET') ?: '';
    }
}
