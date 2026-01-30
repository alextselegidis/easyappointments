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

if (!function_exists('sanitize_filename')) {
    /**
     * Sanitize a filename to prevent directory traversal and other attacks.
     *
     * @param string $filename The filename to sanitize.
     * @param bool $relative_path Allow relative paths (default: false).
     *
     * @return string The sanitized filename.
     */
    function sanitize_filename(string $filename, bool $relative_path = false): string
    {
        // Remove any null bytes
        $filename = str_replace("\0", '', $filename);

        // Remove directory traversal patterns
        $filename = str_replace(['../', '..\\', '..'], '', $filename);

        if (!$relative_path) {
            // Remove all directory separators for flat filenames
            $filename = str_replace(['/', '\\'], '', $filename);
        }

        // Only allow safe characters
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename);

        // Remove leading dots to prevent hidden files
        $filename = ltrim($filename, '.');

        return $filename;
    }
}

if (!function_exists('validate_id')) {
    /**
     * Validate that an ID is a positive integer.
     *
     * @param mixed $id The ID to validate.
     *
     * @return int The validated ID.
     *
     * @throws InvalidArgumentException If the ID is invalid.
     */
    function validate_id(mixed $id): int
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if ($id === false || $id <= 0) {
            throw new InvalidArgumentException('Invalid ID provided.');
        }

        return $id;
    }
}

if (!function_exists('validate_hash')) {
    /**
     * Validate that a hash string is valid (alphanumeric).
     *
     * @param string $hash The hash to validate.
     * @param int $length Expected length (default: 32).
     *
     * @return string The validated hash.
     *
     * @throws InvalidArgumentException If the hash is invalid.
     */
    function validate_hash(string $hash, int $length = 32): string
    {
        $hash = trim($hash);

        if (!preg_match('/^[a-fA-F0-9]{' . $length . '}$/', $hash)) {
            throw new InvalidArgumentException('Invalid hash format.');
        }

        return strtolower($hash);
    }
}

if (!function_exists('validate_email')) {
    /**
     * Validate an email address.
     *
     * @param string $email The email to validate.
     *
     * @return string The validated email.
     *
     * @throws InvalidArgumentException If the email is invalid.
     */
    function validate_email(string $email): string
    {
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        return $email;
    }
}

if (!function_exists('validate_url')) {
    /**
     * Validate a URL.
     *
     * @param string $url The URL to validate.
     * @param array $allowed_schemes Allowed URL schemes (default: ['http', 'https']).
     *
     * @return string The validated URL.
     *
     * @throws InvalidArgumentException If the URL is invalid.
     */
    function validate_url(string $url, array $allowed_schemes = ['http', 'https']): string
    {
        $url = trim($url);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format.');
        }

        $parsed = parse_url($url);

        if (!isset($parsed['scheme']) || !in_array(strtolower($parsed['scheme']), $allowed_schemes, true)) {
            throw new InvalidArgumentException('URL scheme not allowed.');
        }

        return $url;
    }
}

if (!function_exists('generate_secure_token')) {
    /**
     * Generate a cryptographically secure random token.
     *
     * @param int $length The length of the token in bytes (default: 32).
     *
     * @return string The generated token as a hexadecimal string.
     *
     * @throws Exception If random bytes generation fails.
     */
    function generate_secure_token(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}

if (!function_exists('constant_time_compare')) {
    /**
     * Compare two strings in constant time to prevent timing attacks.
     *
     * @param string $known The known string.
     * @param string $user The user-provided string.
     *
     * @return bool True if strings are equal, false otherwise.
     */
    function constant_time_compare(string $known, string $user): bool
    {
        return hash_equals($known, $user);
    }
}

if (!function_exists('is_safe_redirect_url')) {
    /**
     * Check if a URL is safe for redirection (same origin or relative).
     *
     * @param string $url The URL to check.
     *
     * @return bool True if the URL is safe for redirection.
     */
    function is_safe_redirect_url(string $url): bool
    {
        // Allow relative URLs
        if (strpos($url, '/') === 0 && strpos($url, '//') !== 0) {
            return true;
        }

        // Parse the URL
        $parsed = parse_url($url);

        if (!$parsed || !isset($parsed['host'])) {
            return false;
        }

        // Get the current host
        $current_host = $_SERVER['HTTP_HOST'] ?? '';

        // Compare hosts (case-insensitive)
        return strcasecmp($parsed['host'], $current_host) === 0;
    }
}

if (!function_exists('log_security_event')) {
    /**
     * Log a security-related event.
     *
     * @param string $event_type The type of security event.
     * @param string $message Additional message/details.
     * @param array $context Additional context data.
     */
    function log_security_event(string $event_type, string $message, array $context = []): void
    {
        $CI = &get_instance();

        $log_message = sprintf(
            '[SECURITY] [%s] %s | IP: %s | User-Agent: %s',
            $event_type,
            $message,
            $CI->input->ip_address(),
            $CI->input->user_agent(),
        );

        if (!empty($context)) {
            $log_message .= ' | Context: ' . json_encode($context);
        }

        log_message('warning', $log_message);
    }
}
