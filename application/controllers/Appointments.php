<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Appointments controller.
 *
 * Handles the appointment related operations.
 *
 * @package Controllers
 */
class Appointments extends EA_Controller {
    /**
     * Appointments constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('admins_model');
        $this->load->model('secretaries_model');
        $this->load->model('service_categories_model');
        $this->load->model('services_model');
        $this->load->model('customers_model');
        $this->load->model('settings_model');

        $this->load->library('timezones');
        $this->load->library('synchronization');
        $this->load->library('notifications');
        $this->load->library('availability');

        $this->load->driver('cache', ['adapter' => 'file']);
    }

    /**
     * Default callback method of the application.
     *
     * This method creates the appointment book wizard. If an appointment hash is provided then it means that the
     * customer followed the appointment manage link that was sent with the book success email.
     *
     * @param string $appointment_hash The appointment hash identifier.
     */
    public function index(string $appointment_hash = '')
    {
        try
        {
            if ( ! is_app_installed())
            {
                redirect('installation');
                return;
            }

            $available_services = $this->services_model->get_available_services();
            $available_providers = $this->providers_model->get_available_providers();
            $company_name = setting('company_name');
            $book_advance_timeout = setting('book_advance_timeout');
            $date_format = setting('date_format');
            $time_format = setting('time_format');
            $first_weekday = setting('first_weekday');
            $require_phone_number = setting('require_phone_number');
            $show_field['phone-number'] = setting('show_phone_number');
            $show_field['address'] = setting('show_address');
            $show_field['city'] = setting('show_city');
            $show_field['zip-code'] = setting('show_zip_code');
            $show_field['notes'] = setting('show_notes');
            $display_cookie_notice = setting('display_cookie_notice');
            $cookie_notice_content = setting('cookie_notice_content');
            $display_terms_and_conditions = setting('display_terms_and_conditions');
            $terms_and_conditions_content = setting('terms_and_conditions_content');
            $display_privacy_policy = setting('display_privacy_policy');
            $privacy_policy_content = setting('privacy_policy_content');
            $display_any_provider = setting('display_any_provider');
            $timezones = $this->timezones->to_array();

            // Remove the data that are not needed inside the $available_providers array.
            foreach ($available_providers as $index => $provider)
            {
                $stripped_data = [
                    'id' => $provider['id'],
                    'first_name' => $provider['first_name'],
                    'last_name' => $provider['last_name'],
                    'services' => $provider['services'],
                    'timezone' => $provider['timezone']
                ];
                $available_providers[$index] = $stripped_data;
            }

            // If an appointment hash is provided then it means that the customer is trying to edit a registered
            // appointment record.
            if ($appointment_hash !== '')
            {
                // Load the appointments data and enable the manage mode of the page.
                $manage_mode = TRUE;

                $results = $this->appointments_model->get(['hash' => $appointment_hash]);

                if (empty($results))
                {
                    // The requested appointment was not found in the database. 
                    $view = [
                        'message_title' => lang('appointment_not_found'),
                        'message_text' => lang('appointment_does_not_exist_in_db'),
                        'message_icon' => base_url('assets/img/error.png')
                    ];

                    $this->load->layout('layouts/message/message_layout', 'pages/booking/booking_message_page', $view);

                    return;
                }

                // If the requested appointment begin date is lower than book_advance_timeout. Display a message to the
                // customer.
                $start_datetime = strtotime($results[0]['start_datetime']);

                $limit = strtotime('+' . $book_advance_timeout . ' minutes', strtotime('now'));

                if ($start_datetime < $limit)
                {
                    $hours = floor($book_advance_timeout / 60);
                    $minutes = ($book_advance_timeout % 60);

                    $view = [
                        'message_title' => lang('appointment_locked'),
                        'message_text' => strtr(lang('appointment_locked_message'), [
                            '{$limit}' => sprintf('%02d:%02d', $hours, $minutes)
                        ]),
                        'message_icon' => base_url('assets/img/error.png')
                    ];

                    $this->load->layout('layouts/message/message_layout', 'pages/booking/booking_message_page', $view);

                    return;
                }

                $appointment = $results[0];

                $provider = $this->providers_model->find($appointment['id_users_provider']);

                $customer = $this->customers_model->find($appointment['id_users_customer']);

                $customer_token = md5(uniqid(mt_rand(), TRUE));

                // Save the token for 10 minutes.
                $this->cache->save('customer-token-' . $customer_token, $customer['id'], 600);
            }
            else
            {
                // The customer is going to book a new appointment so there is no need for the manage functionality to
                // be initialized.
                $manage_mode = FALSE;
                $customer_token = FALSE;
                $appointment = [];
                $provider = [];
                $customer = [];
            }

            // Load the book appointment view.
            $view = [
                'available_services' => $available_services,
                'available_providers' => $available_providers,
                'company_name' => $company_name,
                'manage_mode' => $manage_mode,
                'customer_token' => $customer_token,
                'date_format' => $date_format,
                'time_format' => $time_format,
                'first_weekday' => $first_weekday,
                'require_phone_number' => $require_phone_number,
                'show_field' => $show_field,
                'appointment_data' => $appointment,
                'provider_data' => $provider,
                'customer_data' => $customer,
                'display_cookie_notice' => $display_cookie_notice,
                'cookie_notice_content' => $cookie_notice_content,
                'display_terms_and_conditions' => $display_terms_and_conditions,
                'terms_and_conditions_content' => $terms_and_conditions_content,
                'display_privacy_policy' => $display_privacy_policy,
                'privacy_policy_content' => $privacy_policy_content,
                'timezones' => $timezones,
                'display_any_provider' => $display_any_provider,
            ];
        }
        catch (Throwable $e)
        {
            $view['exceptions'][] = $e;
        }

        $this->load->layout('layouts/booking/booking_layout', 'pages/booking/booking_page', $view);
    }

    /**
     * Cancel an existing appointment.
     *
     * This method removes an appointment from the company's schedule. In order for the appointment to be deleted, the
     * hash string must be provided. The customer can only cancel the appointment if the edit time period is not over
     * yet.
     *
     * @param string $appointment_hash This appointment hash identifier.
     */
    public function cancel(string $appointment_hash)
    {
        try
        {
            $occurrences = $this->appointments_model->get(['hash' => $appointment_hash]);

            if (empty($occurrences))
            {
                throw new Exception('No record matches the provided hash.');
            }

            $appointment = $occurrences[0];

            $provider = $this->providers_model->find($appointment['id_users_provider']);

            $customer = $this->customers_model->find($appointment['id_users_customer']);

            $service = $this->services_model->find($appointment['id_services']);

            $settings = [
                'company_name' => setting('company_name'),
                'company_email' => setting('company_email'),
                'company_link' => setting('company_link'),
                'date_format' => setting('date_format'),
                'time_format' => setting('time_format')
            ];

            $this->appointments_model->delete($appointment['id']);

            $this->synchronization->sync_appointment_deleted($appointment, $provider);

            $this->notifications->notify_appointment_deleted($appointment, $service, $provider, $customer, $settings);
        }
        catch (Throwable $e)
        {
            $exceptions[] = $e;
        }

        $view = [
            'message_title' => lang('appointment_cancelled_title'),
            'message_text' => lang('appointment_cancelled'),
            'message_icon' => base_url('assets/img/success.png')
        ];

        if (isset($exceptions))
        {
            $view['exceptions'] = $exceptions;
        }

        $this->load->layout('layouts/message/message_layout', 'pages/booking/booking_message_page', $view);
    }

    /**
     * Display the appointment registration success page.
     *
     * @param string $appointment_hash The appointment hash identifier.
     *
     * @throws Exception
     */
    public function book_success(string $appointment_hash)
    {
        $occurrences = $this->appointments_model->get(['hash' => $appointment_hash]);

        if (empty($occurrences))
        {
            redirect('appointments'); // The appointment does not exist.
            return;
        }

        $appointment = $occurrences[0];

        unset($appointment['notes']);

        $customer = $this->customers_model->find($appointment['id_users_customer']);

        $provider = $this->providers_model->find($appointment['id_users_provider']);

        $service = $this->services_model->find($appointment['id_services']);

        $company_name = setting('company_name');

        $exceptions = $this->session->flashdata('book_success');

        $view = [
            'appointment_data' => $appointment,
            'provider_data' => [
                'id' => $provider['id'],
                'first_name' => $provider['first_name'],
                'last_name' => $provider['last_name'],
                'email' => $provider['email'],
                'timezone' => $provider['timezone'],
            ],
            'customer_data' => [
                'id' => $customer['id'],
                'first_name' => $customer['first_name'],
                'last_name' => $customer['last_name'],
                'email' => $customer['email'],
                'timezone' => $customer['timezone'],
            ],
            'service_data' => $service,
            'company_name' => $company_name,
        ];

        if ($exceptions)
        {
            $view['exceptions'] = $exceptions;
        }

        $this->load->layout('layouts/message/message_layout', 'pages/booking/booking_success_page', $view);
    }

    /**
     * Get the available appointment hours for the selected date.
     *
     * This method answers to an AJAX request. It calculates the available hours for the given service, provider and
     * date.
     */
    public function ajax_get_available_hours()
    {
        try
        {
            $provider_id = request('provider_id');
            $service_id = request('service_id');
            $selected_date = request('selected_date');

            // Do not continue if there was no provider selected (more likely there is no provider in the system).
            if (empty($provider_id))
            {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([]));

                return;
            }

            // If manage mode is TRUE then the following we should not consider the selected appointment when
            // calculating the available time periods of the provider.
            $exclude_appointment_id = request('manage_mode') === 'true' ? request('appointment_id') : NULL;

            // If the user has selected the "any-provider" option then we will need to search for an available provider
            // that will provide the requested service.
            $service = $this->services_model->find($service_id);

            if ($provider_id === ANY_PROVIDER)
            {
                $providers = $this->providers_model->get();

                $available_hours = [];

                foreach ($providers as $provider)
                {
                    if ( ! in_array($service_id, $provider['services']))
                    {
                        continue;
                    }

                    $provider_available_hours = $this->availability->get_available_hours($selected_date, $service, $provider, $exclude_appointment_id);

                    $available_hours = array_merge($available_hours, $provider_available_hours);
                }

                $response = array_unique(array_values($available_hours));
            }
            else
            {
                $provider = $this->providers_model->find($provider_id);

                $response = $this->availability->get_available_hours($selected_date, $service, $provider, $exclude_appointment_id);
            }
        }
        catch (Throwable $e)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $e->getMessage(),
                'trace' => config('debug') ? $e->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Search for any provider that can handle the requested service.
     *
     * This method will return the database ID of the provider with the most available periods.
     *
     * @param int $service_id Service ID
     * @param string $date Selected date (Y-m-d).
     * @param string|null $hour Selected hour (H:i).
     *
     * @return int|null Returns the ID of the provider that can provide the service at the selected date.
     *
     * @throws Exception
     */
    protected function search_any_provider(int $service_id, string $date, string $hour = NULL): ?int
    {
        $available_providers = $this->providers_model->get_available_providers();

        $service = $this->services_model->find($service_id);

        $provider_id = NULL;

        $max_hours_count = 0;

        foreach ($available_providers as $provider)
        {
            foreach ($provider['services'] as $provider_service_id)
            {
                if ($provider_service_id == $service_id)
                {
                    // Check if the provider is available for the requested date.
                    $available_hours = $this->availability->get_available_hours($date, $service, $provider);

                    if (count($available_hours) > $max_hours_count && (empty($hour) || in_array($hour, $available_hours, FALSE)))
                    {
                        $provider_id = $provider['id'];

                        $max_hours_count = count($available_hours);
                    }
                }
            }
        }

        return $provider_id;
    }


    /**
     * Register the appointment to the database.
     */
    public function ajax_register_appointment()
    {
        try
        {
            $post_data = request('post_data');
            $captcha = request('captcha');
            $manage_mode = filter_var($post_data['manage_mode'], FILTER_VALIDATE_BOOLEAN);
            $appointment = $post_data['appointment'];
            $customer = $post_data['customer'];

            if ( ! array_key_exists('address', $customer))
            {
                $customer['address'] = '';
            }
            if ( ! array_key_exists('city', $customer))
            {
                $customer['city'] = '';
            }
            if ( ! array_key_exists('zip_code', $customer))
            {
                $customer['zip_code'] = '';
            }
            if ( ! array_key_exists('notes', $customer))
            {
                $customer['notes'] = '';
            }
            if ( ! array_key_exists('phone_number', $customer))
            {
                $customer['address'] = '';
            }

            // Check appointment availability before registering it to the database.
            $appointment['id_users_provider'] = $this->check_datetime_availability();

            if ( ! $appointment['id_users_provider'])
            {
                throw new Exception(lang('requested_hour_is_unavailable'));
            }

            $provider = $this->providers_model->find($appointment['id_users_provider']);

            $service = $this->services_model->find($appointment['id_services']);

            $require_captcha = (bool)setting('require_captcha');

            $captcha_phrase = session('captcha_phrase');

            // Validate the CAPTCHA string.
            if ($require_captcha && $captcha_phrase !== $captcha)
            {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'captcha_verification' => FALSE
                    ]));

                return;
            }

            if ($this->customers_model->exists($customer))
            {
                $customer['id'] = $this->customers_model->find_record_id($customer);
            }

            if (empty($appointment['location']) && ! empty($service['location']))
            {
                $appointment['location'] = $service['location'];
            }

            // Save customer language (the language which is used to render the booking page).
            $customer['language'] = session('language') ?? config('language');
            $customer_id = $this->customers_model->save($customer);

            $appointment['id_users_customer'] = $customer_id;
            $appointment['is_unavailable'] = (int)$appointment['is_unavailable'];
            $appointment['id'] = $this->appointments_model->save($appointment);
            $appointment['hash'] = $this->appointments_model->value($appointment['id'], 'hash');

            $settings = [
                'company_name' => setting('company_name'),
                'company_link' => setting('company_link'),
                'company_email' => setting('company_email'),
                'date_format' => setting('date_format'),
                'time_format' => setting('time_format')
            ];

            $this->synchronization->sync_appointment_saved($appointment, $service, $provider, $customer, $settings, $manage_mode);

            $this->notifications->notify_appointment_saved($appointment, $service, $provider, $customer, $settings, $manage_mode);

            $response = [
                'appointment_id' => $appointment['id'],
                'appointment_hash' => $appointment['hash']
            ];
        }
        catch (Throwable $e)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $e->getMessage(),
                'trace' => config('debug') ? $e->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Check whether the provider is still available in the selected appointment date.
     *
     * It is possible that two or more customers select the same appointment date and time concurrently. The app won't
     * allow this to happen, so one of the two will eventually get the selected date and the other one will have
     * to choose for another one.
     *
     * Use this method just before the customer confirms the appointment registration. If the selected date was reserved
     * in the meanwhile, the customer must be prompted to select another time.
     *
     * @return int Returns the ID of the provider that is available for the appointment.
     *
     * @throws Exception
     */
    protected function check_datetime_availability(): ?int
    {
        $post_data = request('post_data');

        $appointment = $post_data['appointment'];

        $appointment_start = new DateTime($appointment['start_datetime']);

        $date = $appointment_start->format('Y-m-d');

        $hour = $appointment_start->format('H:i');

        if ($appointment['id_users_provider'] === ANY_PROVIDER)
        {
            $appointment['id_users_provider'] = $this->search_any_provider($appointment['id_services'], $date, $hour);

            return $appointment['id_users_provider'];
        }

        $service = $this->services_model->find($appointment['id_services']);

        $exclude_appointment_id = $appointment['id'] ?? NULL;

        $provider = $this->providers_model->find($appointment['id_users_provider']);

        $available_hours = $this->availability->get_available_hours($date, $service, $provider, $exclude_appointment_id);

        $is_still_available = FALSE;

        $appointment_hour = date('H:i', strtotime($appointment['start_datetime']));

        foreach ($available_hours as $available_hour)
        {
            if ($appointment_hour === $available_hour)
            {
                $is_still_available = TRUE;
                break;
            }
        }

        return $is_still_available ? $appointment['id_users_provider'] : NULL;
    }

    /**
     * Get Unavailable Dates
     *
     * Get an array with the available dates of a specific provider, service and month of the year. Provide the
     * "provider_id", "service_id" and "selected_date" as GET parameters to the request. The "selected_date" parameter
     * must have the Y-m-d format.
     *
     * Outputs a JSON string with the unavailable dates. that are unavailable.
     */
    public function ajax_get_unavailable_dates()
    {
        try
        {
            $provider_id = request('provider_id');
            $service_id = request('service_id');
            $appointment_id = request('appointment_id');
            $manage_mode = filter_var(request('manage_mode'), FILTER_VALIDATE_BOOLEAN);
            $selected_date_string = request('selected_date');
            $selected_date = new DateTime($selected_date_string);
            $number_of_days_in_month = (int)$selected_date->format('t');
            $unavailable_dates = [];

            $provider_ids = $provider_id === ANY_PROVIDER
                ? $this->search_providers_by_service($service_id)
                : [$provider_id];

            $exclude_appointment_id = $manage_mode ? $appointment_id : NULL;

            // Get the service record.
            $service = $this->services_model->find($service_id);

            for ($i = 1; $i <= $number_of_days_in_month; $i++)
            {
                $current_date = new DateTime($selected_date->format('Y-m') . '-' . $i);

                if ($current_date < new DateTime(date('Y-m-d 00:00:00')))
                {
                    // Past dates become immediately unavailable.
                    $unavailable_dates[] = $current_date->format('Y-m-d');
                    continue;
                }

                // Finding at least one slot of availability.
                foreach ($provider_ids as $current_provider_id)
                {
                    $provider = $this->providers_model->find($current_provider_id);

                    $available_hours = $this->availability->get_available_hours(
                        $current_date->format('Y-m-d'),
                        $service,
                        $provider,
                        $exclude_appointment_id
                    );

                    if ( ! empty($available_hours))
                    {
                        break;
                    }
                }

                // No availability amongst all the provider.
                if (empty($available_hours))
                {
                    $unavailable_dates[] = $current_date->format('Y-m-d');
                }
            }

            $response = $unavailable_dates;
        }
        catch (Throwable $e)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $e->getMessage(),
                'trace' => config('debug') ? $e->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Search for any provider that can handle the requested service.
     *
     * This method will return the database ID of the providers affected to the requested service.
     *
     * @param int $service_id The requested service ID.
     *
     * @return array Returns the ID of the provider that can provide the requested service.
     */
    protected function search_providers_by_service(int $service_id): array
    {
        $available_providers = $this->providers_model->get_available_providers();
        $provider_list = [];

        foreach ($available_providers as $provider)
        {
            foreach ($provider['services'] as $provider_service_id)
            {
                if ($provider_service_id === $service_id)
                {
                    // Check if the provider is affected to the selected service.
                    $provider_list[] = $provider['id'];
                }
            }
        }

        return $provider_list;
    }


}
