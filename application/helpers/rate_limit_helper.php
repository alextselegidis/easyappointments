<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.1.0
 * ---------------------------------------------------------------------------- */

if (!function_exists('rate_limit')) {
    /**
     * Rate-limit the application requests.
     *
     * Example:
     *
     * rate_limit($CI->input->ip_address(), 100, 300);
     *
     * @link https://github.com/alexandrugaidei-atomate/ratelimit-codeigniter-filebased
     *
     * @param string $ip Client IP address.
     * @param int $max_requests Number of allowed requests, defaults to 100.
     * @param int $duration In seconds, defaults to 2 minutes.
     */
    function rate_limit(string $ip, int $max_requests = 100, int $duration = 120): void
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        $rate_limiting = $CI->config->item('rate_limiting');

        if (!$rate_limiting || is_cli()) {
            return;
        }

        $server = $_SERVER ?? [];

        $client_ip = '';

        if (!empty($server['HTTP_CF_CONNECTING_IP'])) {
            $client_ip = trim((string) $server['HTTP_CF_CONNECTING_IP']);
        } elseif (!empty($server['HTTP_X_REAL_IP'])) {
            $client_ip = trim((string) $server['HTTP_X_REAL_IP']);
        } elseif (!empty($server['HTTP_X_FORWARDED_FOR'])) {
            $forwarded = explode(',', (string) $server['HTTP_X_FORWARDED_FOR']);
            $client_ip = trim((string) ($forwarded[0] ?? ''));
        }

        if ($client_ip === '') {
            $client_ip = $ip;
        }

        $route = (string) ($server['REQUEST_URI'] ?? 'global');
        $signature = sha1($client_ip . '|' . $route);

        $cache_path = (string) ($CI->config->item('cache_path') ?: '');
        $cache_path = rtrim(str_replace('\\', '/', $cache_path), '/');

        if ($cache_path === '') {
            $cache_path = rtrim(str_replace('\\', '/', sys_get_temp_dir()), '/');
        }

        $rate_limit_dir = $cache_path . '/rate_limit';

        if (!is_dir($rate_limit_dir) && !@mkdir($rate_limit_dir, 0775, true) && !is_dir($rate_limit_dir)) {
            return;
        }

        $rate_limit_file = $rate_limit_dir . '/rl_' . $signature . '.json';
        $now = time();

        $state = [
            'count' => 0,
            'reset_at' => $now + $duration,
        ];

        if (is_file($rate_limit_file)) {
            $raw = @file_get_contents($rate_limit_file);
            $decoded = json_decode((string) $raw, true);

            if (is_array($decoded) && isset($decoded['count'], $decoded['reset_at'])) {
                $state = [
                    'count' => (int) $decoded['count'],
                    'reset_at' => (int) $decoded['reset_at'],
                ];
            }
        }

        if ($now > $state['reset_at']) {
            $state['count'] = 0;
            $state['reset_at'] = $now + $duration;
        }

        $state['count']++;

        @file_put_contents($rate_limit_file, json_encode($state), LOCK_EX);

        if ($state['count'] > $max_requests) {
            $retry_after = max(1, $state['reset_at'] - $now);
            header('Retry-After: ' . $retry_after);
            header('HTTP/1.0 429 Too Many Requests');
            exit();
        }
    }
}
