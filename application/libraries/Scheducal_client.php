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
 * ScheduCal client library.
 *
 * Handles ScheduCal API integration for sending calendar invitations.
 *
 * @package Libraries
 */
class Scheducal_client
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * @var array ScheduCal configuration
     */
    protected array $config;

    /**
     * Scheducal_client constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('appointments_model');
        $this->CI->load->model('customers_model');
        $this->CI->load->model('providers_model');
        $this->CI->load->model('services_model');
        $this->CI->load->model('settings_model');

        // Load ScheduCal configuration
        $this->CI->load->config('scheducal');
        $this->config = [
            'enabled' => $this->CI->config->item('scheducal_enabled'),
            'api_key' => $this->CI->config->item('scheducal_api_key'),
            'api_secret' => $this->CI->config->item('scheducal_api_secret'),
            'api_url' => $this->CI->config->item('scheducal_api_url'),
            'base_url' => Config::BASE_URL,
        ];

    }

    /**
     * Check if ScheduCal integration is enabled and properly configured.
     *
     * @return bool
     */
    public function is_enabled(): bool
    {
        return $this->config['enabled'] &&
               !empty($this->config['api_key']) &&
               !empty($this->config['api_secret']);
    }

    /**
     * Send a calendar invitation for a new appointment via ScheduCal.
     *
     * @param array $appointment Appointment data.
     * @param array $provider Provider data.
     * @param array $service Service data.
     * @param array $customer Customer data.
     * @param array $settings Company settings.
     *
     * @return string|null Returns the ScheduCal appointment ID or null on failure.
     *
     * @throws Exception
     */
    public function create_appointment(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings
    ): ?string
    {
        if (!$this->is_enabled())
        {
            return null;
        }

        try
        {
            // Get appointment timezone
            $appointment_timezone = new DateTimeZone($provider['timezone']);
            $appointment_start = new DateTime($appointment['start_datetime'], $appointment_timezone);
            $appointment_end = new DateTime($appointment['end_datetime'], $appointment_timezone);

            // Build appointment management link.
            //
            // Use booking/reschedule, which is what upstream's own Notifications
            // library links to. appointments/index is a backwards-compatibility
            // shim marked "@deprecated Since 1.5" for pre-1.5 links, and in 1.6.0
            // it validates the hash against /^[a-fA-F0-9]{32}$/ while the model
            // generates random_string('alnum', 12) - so it rejects every hash the
            // application issues. Nobody upstream noticed because nothing upstream
            // links there; we did because this line pointed at it.
            $appointment_link = '';
            if (!empty($this->config['base_url']) && !empty($appointment['hash']))
            {
                $appointment_link = rtrim($this->config['base_url'], '/') . '/index.php/booking/reschedule/' . $appointment['hash'];
            }

            // Prepare data for ScheduCal API
            $data = [
                'appointmentSubject' => $service['name'],
                'appointmentBody' => 'Appointment with ' . $provider['first_name'] . ' ' . $provider['last_name'] . '.' .
                    (!empty($appointment_link) ? '<br><br>Click <a href="' . $appointment_link . '">here</a> to manage this appointment.' : ''),
                'appointmentStart' => $appointment_start->format('Y-m-d\TH:i:s'),
                'appointmentEnd' => $appointment_end->format('Y-m-d\TH:i:s'),
                'appointmentTimeZone' => $appointment_timezone->getName(),
                'appointmentLocation' => $settings['company_name'],
                'name' => $customer['first_name'] . ' ' . $customer['last_name'],
                'address' => $customer['email'],
            ];

            // Make API request
            $response = $this->make_api_request($this->config['api_url'], 'POST', $data);

            // Extract appointment ID from response
            if ($response && isset($response['data']['appointmentId']))
            {
                return trim($response['data']['appointmentId']);
            }
            elseif (is_string($response))
            {
                return $response;
            }

            return null;
        }
        catch (Exception $exception)
        {
            log_message('error', 'ScheduCal create appointment error: ' . $exception->getMessage());
            throw new Exception('ScheduCal create() error: ' . $exception->getMessage());
        }
    }

    /**
     * Send an updated calendar invitation via ScheduCal.
     *
     * @param array $appointment Appointment data (must include id_google_calendar).
     * @param array $provider Provider data.
     * @param array $service Service data.
     * @param array $customer Customer data.
     * @param array $settings Company settings.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws Exception
     */
    public function update_appointment(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings
    ): bool
    {
        if (!$this->is_enabled())
        {
            return false;
        }

        if (empty($appointment['id_google_calendar']))
        {
            return false;
        }

        try
        {
            // Get appointment timezone
            $appointment_timezone = new DateTimeZone($provider['timezone']);
            $appointment_start = new DateTime($appointment['start_datetime'], $appointment_timezone);
            $appointment_end = new DateTime($appointment['end_datetime'], $appointment_timezone);

            // Prepare data for ScheduCal API
            $data = [
                'appointmentId' => $appointment['id_google_calendar'],
                'appointmentStart' => $appointment_start->format('Y-m-d\TH:i:s'),
                'appointmentEnd' => $appointment_end->format('Y-m-d\TH:i:s'),
                'appointmentTimeZone' => $appointment_timezone->getName(),
            ];

            // Make API request
            $url = $this->config['api_url'] . '/' . urlencode($appointment['id_google_calendar']);
            $this->make_api_request($url, 'PUT', $data);

            return true;
        }
        catch (Exception $exception)
        {
            log_message('error', 'ScheduCal update appointment error: ' . $exception->getMessage());
            throw new Exception('ScheduCal update() error: ' . $exception->getMessage());
        }
    }

    /**
     * Send a cancellation notice via ScheduCal.
     *
     * @param array $appointment Appointment data (must include id_google_calendar).
     * @param string $reason Reason for cancellation.
     *
     * @return bool Returns true on success, false on failure.
     *
     * @throws Exception
     */
    public function delete_appointment(array $appointment, string $reason = ''): bool
    {
        if (!$this->is_enabled())
        {
            return false;
        }

        if (empty($appointment['id_google_calendar']))
        {
            return false;
        }

        try
        {
            // The appointment ID travels in the URL and credentials in the
            // Authorization header, so the only body field the cancel endpoint
            // reads is the optional comment. With no reason, send no body.
            $data = $reason !== '' ? ['comment' => $reason] : [];

            // Make API request
            $url = $this->config['api_url'] . '/' . urlencode($appointment['id_google_calendar']);
            $this->make_api_request($url, 'DELETE', $data);

            return true;
        }
        catch (Exception $exception)
        {
            log_message('error', 'ScheduCal delete appointment error: ' . $exception->getMessage());
            throw new Exception('ScheduCal delete() error: ' . $exception->getMessage());
        }
    }

    /**
     * Make an HTTP request to the ScheduCal API.
     *
     * @param string $url API endpoint URL.
     * @param string $method HTTP method (GET, POST, PUT, DELETE).
     * @param array $data Request data. Never include credentials here: they are
     *                    sent in the Authorization header (HTTP Basic).
     *
     * @return array|string|null Response data.
     *
     * @throws Exception
     */
    protected function make_api_request(string $url, string $method, array $data = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  // 5 seconds to establish connection
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);         // 10 seconds total for the request
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        // Authorization: Basic base64(apiKey:apiSecret). The API no longer wants
        // credentials in the JSON body (deprecated there, to be removed).
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->config['api_key'] . ':' . $this->config['api_secret']);

        if ($method === 'POST')
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        elseif ($method === 'PUT')
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        elseif ($method === 'DELETE')
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

            if (!empty($data))
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);

        curl_close($ch);

        if ($curl_error)
        {
            throw new Exception('cURL error: ' . $curl_error);
        }

        if ($http_code < 200 || $http_code >= 300)
        {
            log_message('error', 'ScheduCal API error: HTTP ' . $http_code . ' - ' . $response);
        }

        // Try to decode JSON response
        $decoded = json_decode($response, true);
        return $decoded !== null ? $decoded : $response;
    }
}
