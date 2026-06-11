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

        log_message('error', $log_message);
    }
}

if (!function_exists('filter_sensitive_settings')) {
    /**
     * Filter sensitive data from settings array.
     *
     * This function removes sensitive settings like passwords, tokens, and secrets
     * that should never be exposed to the client-side.
     *
     * @param array $settings The settings array to filter.
     *
     * @return array The filtered settings array.
     */
    function filter_sensitive_settings(array $settings): array
    {
        $sensitive_setting_names = ['api_token', 'google_client_secret', 'ldap_password'];
        return array_values(
            array_filter($settings, function ($setting) use ($sensitive_setting_names) {
                if (isset($setting['name'])) {
                    return !in_array($setting['name'], $sensitive_setting_names, true);
                }
                return true;
            }),
        );
    }
}

if (!function_exists('filter_sensitive_user_settings')) {
    /**
     * Filter sensitive data from user settings array.
     *
     * This function removes sensitive user settings like passwords, tokens, and credentials
     * that should never be exposed to the client-side via script_vars.
     *
     * @param array $settings The user settings array to filter.
     *
     * @return array The filtered user settings array.
     */
    function filter_sensitive_user_settings(array $settings): array
    {
        $sensitive_fields = [
            'password',
            'salt',
            'password_reset_token',
            'password_reset_expires',
            'google_token',
            'caldav_password',
            'caldav_username',
        ];
        foreach ($sensitive_fields as $field) {
            unset($settings[$field]);
        }
        return $settings;
    }
}

if (!function_exists('filter_sensitive_user_data')) {
    /**
     * Filter sensitive data from a user/provider/admin data array.
     *
     * This function removes sensitive data from user records before exposing to client-side.
     *
     * @param array $user The user data array to filter.
     *
     * @return array The filtered user data array.
     */
    function filter_sensitive_user_data(array $user): array
    {
        if (isset($user['settings'])) {
            $user['settings'] = filter_sensitive_user_settings($user['settings']);
        }
        return $user;
    }
}

if (!function_exists('filter_sensitive_users_data')) {
    /**
     * Filter sensitive data from an array of users/providers/admins.
     *
     * @param array $users The array of user data to filter.
     *
     * @return array The filtered array of user data.
     */
    function filter_sensitive_users_data(array $users): array
    {
        return array_map('filter_sensitive_user_data', $users);
    }
}

if (!function_exists('normalize_embed_origin')) {
    /**
     * Normalize and validate a single embed origin value.
     *
     * @param string $origin
     *
     * @return string|null
     */
    function normalize_embed_origin(string $origin): ?string
    {
        $origin = rtrim(trim($origin), '/');

        if ($origin === '' || preg_match('/[\s;\'"<>]/', $origin)) {
            return null;
        }

        if (!str_contains($origin, '://')) {
            $origin = 'https://' . $origin;
        }

        $parsed = parse_url($origin);

        if ($parsed === false || empty($parsed['host'])) {
            return null;
        }

        $host = strtolower($parsed['host']);

        if ($host !== 'localhost' && !str_contains($host, '.')) {
            return null;
        }

        $scheme = strtolower($parsed['scheme'] ?? '');

        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        if (!empty($parsed['user']) || !empty($parsed['pass'])) {
            return null;
        }

        $path = $parsed['path'] ?? '';

        if ($path !== '' && $path !== '/') {
            return null;
        }

        if (!empty($parsed['query']) || !empty($parsed['fragment'])) {
            return null;
        }

        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';

        return $scheme . '://' . $parsed['host'] . $port;
    }
}

if (!function_exists('sanitize_embed_allowed_origins')) {
    /**
     * Sanitize embed allowed origins from a comma- or newline-separated value.
     *
     * @param mixed $origins_raw
     *
     * @return string
     */
    function sanitize_embed_allowed_origins(mixed $origins_raw): string
    {
        if (!is_string($origins_raw) || trim($origins_raw) === '') {
            return '';
        }

        $origins = preg_split('/(?:\R|,)+/', $origins_raw) ?: [];
        $normalized = [];

        foreach ($origins as $origin) {
            $value = normalize_embed_origin((string) $origin);

            if ($value !== null) {
                $normalized[] = $value;
            }
        }

        return implode("\n", array_values(array_unique($normalized)));
    }
}

if (!function_exists('get_embed_allowed_origins')) {
    /**
     * Get the list of origins allowed to embed the booking page in an iframe.
     *
     * Origins are configured via the EMBED_ALLOWED_ORIGINS constant (comma-separated).
     *
     * @return array
     */
    function get_embed_allowed_origins(): array
    {
        if (!defined('EMBED_ALLOWED_ORIGINS') || EMBED_ALLOWED_ORIGINS === '') {
            return [];
        }

        $sanitized = sanitize_embed_allowed_origins((string) EMBED_ALLOWED_ORIGINS);

        if ($sanitized === '') {
            return [];
        }

        return explode("\n", $sanitized);
    }
}

if (!function_exists('is_booking_framing_route')) {
    /**
     * Determine whether the current request serves a public booking page that may be embedded.
     */
    function is_booking_framing_route(): bool
    {
        if (!function_exists('get_instance')) {
            return false;
        }

        /** @var EA_Controller|null $CI */
        $CI = &get_instance();

        $router = $CI->router ?? null;

        if (empty($router)) {
            return false;
        }

        $booking_controllers = ['booking', 'booking_confirmation', 'booking_cancellation'];

        return in_array($router->class, $booking_controllers, true);
    }
}

if (!function_exists('is_public_booking_flow_route')) {
    /**
     * Determine whether the current request is part of the public booking flow.
     */
    function is_public_booking_flow_route(): bool
    {
        if (!function_exists('get_instance')) {
            return false;
        }

        /** @var EA_Controller|null $CI */
        $CI = &get_instance();

        $router = $CI->router ?? null;

        if (empty($router)) {
            return false;
        }

        $public_booking_controllers = [
            'booking',
            'booking_confirmation',
            'booking_cancellation',
            'captcha',
            'localization',
        ];

        return in_array($router->class, $public_booking_controllers, true);
    }
}

if (!function_exists('cross_site_cookies_required')) {
    /**
     * Determine whether cookies must be sent in cross-site iframe contexts.
     */
    function cross_site_cookies_required(): bool
    {
        if (!function_exists('get_instance')) {
            return false;
        }

        /** @var EA_Controller|null $CI */
        $CI = &get_instance();

        if (!isset($CI->db) || !function_exists('is_app_installed') || !is_app_installed()) {
            return false;
        }

        if (empty(get_embed_allowed_origins()) || !is_public_booking_flow_route()) {
            return false;
        }

        if (function_exists('is_embedded_booking_request') && is_embedded_booking_request()) {
            return true;
        }

        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }
}

if (!function_exists('set_application_cookie')) {
    /**
     * Set an application cookie with the correct SameSite attributes.
     */
    function set_application_cookie(
        string $name,
        string $value,
        int $expires,
        ?bool $secure = null,
        ?bool $httponly = null,
        ?bool $cross_site = null,
    ): bool {
        $cross_site ??= cross_site_cookies_required();

        $secure = $secure ?? (bool) ($cross_site || config_item('cookie_secure'));
        $httponly = $httponly ?? (bool) config_item('cookie_httponly');

        if ($secure && !is_https()) {
            return false;
        }

        $options = [
            'expires' => $expires,
            'path' => config_item('cookie_path') ?: '/',
            'domain' => config_item('cookie_domain') ?: '',
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $cross_site ? 'None' : (config_item('cookie_samesite') ?: 'Lax'),
        ];

        if ($cross_site && defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 80500) {
            $options['partitioned'] = true;
        }

        return setcookie($name, $value, $options);
    }
}

if (!function_exists('booking_csrf_secret')) {
    /**
     * Secret used to sign stateless booking CSRF tokens for iframe embeds.
     */
    function booking_csrf_secret(): string
    {
        $key = defined('ENCRYPTION_KEY') && ENCRYPTION_KEY !== ''
            ? ENCRYPTION_KEY
            : (string) config_item('encryption_key');

        return hash('sha256', 'booking-embed-csrf:' . $key);
    }
}

if (!function_exists('issue_booking_csrf_token')) {
    /**
     * Issue a stateless CSRF token that does not rely on cookies or server-side storage.
     */
    function issue_booking_csrf_token(): string
    {
        $timestamp = str_pad(dechex(time()), 8, '0', STR_PAD_LEFT);
        $nonce = bin2hex(random_bytes(12));
        $payload = $timestamp . $nonce;
        $mac = substr(hash_hmac('sha256', $payload, booking_csrf_secret()), 0, 32);

        return $payload . $mac;
    }
}

if (!function_exists('verify_booking_csrf_token_string')) {
    /**
     * Verify a stateless booking CSRF token.
     */
    function verify_booking_csrf_token_string(string $token): bool
    {
        if (strlen($token) !== 64 || !ctype_xdigit($token)) {
            return false;
        }

        $timestamp_hex = substr($token, 0, 8);
        $nonce = substr($token, 8, 24);
        $mac = substr($token, 32);
        $timestamp = hexdec($timestamp_hex);

        if ($timestamp <= 0) {
            return false;
        }

        $expire = (int) (config_item('csrf_expire') ?: 7200);

        if (time() < $timestamp || time() - $timestamp > $expire) {
            return false;
        }

        $payload = $timestamp_hex . $nonce;

        return hash_equals(substr(hash_hmac('sha256', $payload, booking_csrf_secret()), 0, 32), $mac);
    }
}

if (!function_exists('prepare_booking_csrf_for_embed')) {
    /**
     * Configure CSRF for booking pages that may run inside third-party iframes.
     */
    function prepare_booking_csrf_for_embed(EA_Security $security): string
    {
        $security->csrf_set_cookie();

        return issue_booking_csrf_token();
    }
}

if (!function_exists('get_csrf_cookie_value')) {
    /**
     * Read the CSRF cookie value, including any configured prefix.
     */
    function get_csrf_cookie_value(): ?string
    {
        $cookie_name = (string) (config_item('cookie_prefix') ?: '') . (config_item('csrf_cookie_name') ?: 'csrf_cookie');

        if (!isset($_COOKIE[$cookie_name]) || !is_string($_COOKIE[$cookie_name])) {
            return null;
        }

        return $_COOKIE[$cookie_name];
    }
}

if (!function_exists('verify_booking_csrf_token')) {
    /**
     * Verify CSRF token for booking submissions, including embedded iframe flows.
     *
     * @throws RuntimeException
     */
    function verify_booking_csrf_token(): void
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        $csrf_token = request('csrf_token') ?? $CI->input->get_request_header('X-CSRF');
        $csrf_cookie = get_csrf_cookie_value();

        if (
            is_string($csrf_token)
            && verify_booking_csrf_token_string($csrf_token)
        ) {
            return;
        }

        if (
            is_string($csrf_token)
            && is_string($csrf_cookie)
            && $csrf_token !== ''
            && hash_equals($csrf_cookie, $csrf_token)
        ) {
            return;
        }

        log_message(
            'error',
            'Invalid CSRF token in booking request from IP: '
            . $CI->input->ip_address()
            . ' cookie_present=' . ($csrf_cookie ? 'yes' : 'no')
            . ' token_length=' . (is_string($csrf_token) ? strlen($csrf_token) : 0),
        );
        throw new RuntimeException('Security validation failed. Please refresh the page and try again.');
    }
}

if (!function_exists('apply_frame_embedding_headers')) {
    /**
     * Apply headers that control whether the booking page can be embedded in iframes.
     */
    function apply_frame_embedding_headers(): void
    {
        if (!is_booking_framing_route()) {
            header('X-Frame-Options: SAMEORIGIN');

            return;
        }

        $origins = get_embed_allowed_origins();

        if (empty($origins)) {
            header('X-Frame-Options: SAMEORIGIN');

            return;
        }

        $ancestors = array_merge(["'self'"], $origins);

        header_remove('X-Frame-Options');
        header('Content-Security-Policy: frame-ancestors ' . implode(' ', $ancestors));
    }
}
