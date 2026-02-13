<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Add security headers to protect against common attacks.
 *
 * This hook adds various security headers to HTTP responses to help protect
 * against XSS, clickjacking, MIME sniffing, and other attacks.
 */
function add_security_headers(): void
{
    // Prevent XSS attacks - allow inline scripts and styles for the app's functionality
    // But restrict to same-origin for other resources
    header('X-Content-Type-Options: nosniff');

    // Prevent clickjacking attacks
    header('X-Frame-Options: SAMEORIGIN');

    // Enable XSS filter in browsers (legacy, but still useful)
    header('X-XSS-Protection: 1; mode=block');

    // Referrer policy for privacy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Permissions policy - restrict sensitive features
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    // If the app is served over HTTPS, add HSTS header
    if (
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    ) {
        // Strict Transport Security - enforce HTTPS for 1 year
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
