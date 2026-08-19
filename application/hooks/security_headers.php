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
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // Prevent clickjacking attacks. Controllers that are meant to be embedded call allow_iframe_embedding() from their
    // action, which runs after this hook and overrides the header again.
    header('X-Frame-Options: SAMEORIGIN');

    // Content Security Policy. The pages use inline scripts and styles, so those stay allowed, but plugins are blocked
    // outright and every other resource has to come from this installation.
    //
    // Framing is deliberately left out of this policy: the booking page is meant to be embedded in other websites and
    // controls that through the X-Frame-Options header above, which a frame-ancestors directive would override.
    header(
        "Content-Security-Policy: default-src 'self'; " .
            "script-src 'self' 'unsafe-inline'; " .
            "style-src 'self' 'unsafe-inline'; " .
            "img-src 'self' data:; " .
            "font-src 'self' data:; " .
            "object-src 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self'",
    );

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
