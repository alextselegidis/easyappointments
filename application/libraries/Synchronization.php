<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Synchronization library.
 *
 * Handles external calendar synchronization functionality.
 *
 * @package Libraries
 */
class Synchronization {
    /**
     * @var EA_Controller
     */
    protected $CI;

    /**
     * Synchronization constructor.
     */
    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('providers_model');
        $this->CI->load->model('appointments_model');

        $this->CI->load->library('google_sync');
    }

    /**
     * Synchronize changes made to the appointment with external calendars.
     *
     * @param array $appointment Appointment record.
     * @param array $service Service record.
     * @param array $provider Provider record.
     * @param array $customer Customer record.
     * @param array $settings Required settings for the notification content.
     * @param bool|false $manage_mode True if the appointment is being edited.
     */
    public function sync_appointment_saved(array $appointment, array $service, array $provider, array $customer, array $settings, bool $manage_mode = FALSE)
    {
        try
        {
            $google_sync = filter_var(
                $this->CI->providers_model->get_setting($appointment['id_users_provider'], 'google_sync'),
                FILTER_VALIDATE_BOOLEAN
            );

            if ($google_sync === TRUE)
            {
                $google_token = json_decode(
                    $this->CI->providers_model->get_setting($appointment['id_users_provider'], 'google_token'));

                $this->CI->load->library('google_sync');

                $this->CI->google_sync->refresh_token($google_token->refresh_token);

                if ($manage_mode === FALSE)
                {
                    // Add appointment to Google Calendar.
                    $google_event = $this->CI->google_sync->add_appointment($appointment, $provider,
                        $service, $customer, $settings);
                    $appointment['id_google_calendar'] = $google_event->id;
                    $this->CI->appointments_model->add($appointment);
                }
                else
                {
                    // Update appointment to Google Calendar.
                    $appointment['id_google_calendar'] = $this->CI->appointments_model->value($appointment['id'], 'id_google_calendar');

                    $this->CI->google_sync->update_appointment($appointment, $provider,
                        $service, $customer, $settings);
                }
            }
        }
        catch (Exception $exception)
        {
            log_message('error', $exception->getMessage());
            log_message('error', $exception->getTraceAsString());
        }
    }

    /**
     * Synchronize removal of an appointment with external calendars.
     *
     * @param array $appointment Appointment data.
     * @param array $provider Provider data.
     */
    public function sync_appointment_deleted(array $appointment, array $provider)
    {
        if ( ! empty($appointment['id_google_calendar']))
        {
            try
            {
                $google_sync = filter_var(
                    $this->CI->providers_model->get_setting($appointment['id_users_provider'], 'google_sync'),
                    FILTER_VALIDATE_BOOLEAN
                );

                if ($google_sync === TRUE)
                {
                    $google_token = json_decode($this->CI->providers_model->get_setting($provider['id'], 'google_token'));
                    $this->CI->load->library('Google_sync');
                    $this->CI->google_sync->refresh_token($google_token->refresh_token);
                    $this->CI->google_sync->delete_appointment($provider, $appointment['id_google_calendar']);
                }
            }
            catch (Exception $exception)
            {
                $exceptions[] = $exception;
            }
        }
    }
}
