<?php use JetBrains\PhpStorm\NoReturn;

defined('BASEPATH') or exit('No direct script access allowed');

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
 * Api library.
 *
 * Handles API related functionality.
 *
 * @package Libraries
 */
class Api
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * @var int
     */
    protected int $default_length = 20;

    /**
     * @var EA_Model
     */
    protected EA_Model $model;

    /**
     * Api constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->library('accounts');
    }

    /**
     * Load and use the provided model class.
     *
     * @param string $model
     */
    public function model(string $model): void
    {
        $this->CI->load->model($model);

        $this->model = $this->CI->{$model};
    }

    /**
     * Authorize the API request (Basic Auth or Bearer Token supported).
     */
    public function auth(): void
    {
        try {
            // Bearer token.
            $api_token = setting('api_token');

            if (!empty($api_token) && $api_token === $this->get_bearer_token()) {
                return;
            }

            // Basic auth.
            $username = $_SERVER['PHP_AUTH_USER'] ?? null;

            $password = $_SERVER['PHP_AUTH_PW'] ?? null;

            $user_data = $this->CI->accounts->check_login($username, $password);

            if (empty($user_data['role_slug']) || $user_data['role_slug'] !== DB_SLUG_ADMIN) {
                throw new RuntimeException(
                    'The provided credentials do not match any admin user!',
                    401,
                    'Unauthorized',
                );
            }
        } catch (Throwable) {
            $this->request_authentication();
        }
    }

    /**
     * Returns the bearer token value.
     *
     * @return string|null
     */
    protected function get_bearer_token(): ?string
    {
        $headers = $this->get_authorization_header();

        // HEADER: Get the access token from the header

        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Returns the authorization header.
     *
     * @return string|null
     */
    protected function get_authorization_header(): ?string
    {
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } else {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                // Nginx or fast CGI
                $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
            } elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();

                // Server-side fix for bug in old Android versions (a nice side effect of this fix means we don't care
                // about capitalization for Authorization).
                $requestHeaders = array_combine(
                    array_map('ucwords', array_keys($requestHeaders)),
                    array_values($requestHeaders),
                );

                if (isset($requestHeaders['Authorization'])) {
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
        }

        return $headers;
    }

    /**
     * Sets request authentication headers.
     */
    #[NoReturn]
    public function request_authentication(): void
    {
        header('WWW-Authenticate: Basic realm="Easy!Appointments"');
        header('HTTP/1.0 401 Unauthorized');
        exit('You are not authorized to use the API.');
    }

    /**
     * Get the search keyword value of the current request.
     *
     * @return string|null
     */
    public function request_keyword(): ?string
    {
        return request('q');
    }

    /**
     * Get the limit value of the current request.
     *
     * @return int|null
     */
    public function request_limit(): ?int
    {
        return request('length', $this->default_length);
    }

    /**
     * Get the limit value of the current request.
     *
     * @return int|null
     */
    public function request_offset(): ?int
    {
        $page = request('page', 1);

        $length = request('length', $this->default_length);

        return ($page - 1) * $length;
    }

    /**
     * Get the order by value of the current request.
     *
     * @return string|null
     */
    public function request_order_by(): ?string
    {
        $sort = request('sort');

        if (!$sort) {
            return null;
        }

        $sort_tokens = array_map('trim', explode(',', $sort));

        $order_by = [];

        foreach ($sort_tokens as $sort_token) {
            $api_field = substr($sort_token, 1);

            $direction_operator = substr($sort_token, 0, 1);

            if (!in_array($direction_operator, ['-', '+'])) {
                $direction_operator = '+';
                $api_field = $sort_token;
            }

            $db_field = $this->model->db_field($api_field);

            $direction = $direction_operator === '-' ? 'DESC' : 'ASC';

            $order_by[] = $db_field . ' ' . $direction;
        }

        return implode(', ', $order_by);
    }

    /**
     * Get the chosen "fields" array of the current request.
     *
     * @return array|null
     */
    public function request_fields(): ?array
    {
        $fields = request('fields');

        if (!$fields) {
            return null;
        }

        return array_map('trim', explode(',', $fields));
    }

    /**
     * Get the provided "with" array of the current request.
     *
     * @return array|null
     */
    public function request_with(): ?array
    {
        $with = request('with');

        if (!$with) {
            return null;
        }

        return array_map('trim', explode(',', $with));
    }
}
