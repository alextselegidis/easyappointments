<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
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
            if ( ! $provider['settings']['google_sync'])
            {
                return;
            }

            if (empty($provider['settings']['google_token']))
            {
                throw new RuntimeException('No google token available for the provider: ' . $provider['id']);
            }

            $google_token = json_decode($provider['settings']['google_token'], TRUE);

            $this->CI->google_sync->refresh_token($google_token['refresh_token']);

            if (empty($appointment['id_google_calendar']))
            {
                $google_event = $this->CI->google_sync->add_appointment(
                    $appointment,
                    $provider,
                    $service,
                    $customer,
                    $settings
                );

                $appointment['id_google_calendar'] = $google_event->getId();

                $this->CI->appointments_model->save($appointment);
            }
            else
            {
                $this->CI->google_sync->update_appointment(
                    $appointment,
                    $provider,
                    $service,
                    $customer,
                    $settings
                );
            }
        }
        catch (Throwable $e)
        {
            log_message('error', 'Synchronization - Could not sync confirmation details of appointment (' . ($appointment['id'] ?? '-') . ') : ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());
        }
    }

    /**
     * Synchronize removal of an appointment with external calendars.
     *
     * @param array $appointment Appointment record.
     * @param array $provider Provider record.
     */
    public function sync_appointment_deleted(array $appointment, array $provider)
    {
        try
        {
            if ( ! $provider['settings']['google_sync'] || empty($appointment['id_google_calendar']))
            {
                return;
            }

            if (empty($provider['settings']['google_token']))
            {
                throw new RuntimeException('No google token available for the provider: ' . $provider['id']);
            }

            $google_token = json_decode($provider['settings']['google_token'], TRUE);

            $this->CI->google_sync->refresh_token($google_token['refresh_token']);

            $this->CI->google_sync->delete_appointment($provider, $appointment['id_google_calendar']);

        }
        catch (Throwable $e)
        {
            log_message('error', 'Synchronization - Could not sync cancellation details of appointment (' . ($appointment['id'] ?? '-') . ') : ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());
        }
    }

    /**
     * Synchronize changes made to the unavailability with external calendars.
     *
     * @param array $unavailability Unavailability record.
     * @param array $provider Provider record.
     */
    public function sync_unavailability_saved(array $unavailability, array $provider)
    {
        try
        {
            if ( ! $provider['settings']['google_sync'])
            {
                return;
            }

            if (empty($provider['settings']['google_token']))
            {
                throw new RuntimeException('No google token available for the provider: ' . $provider['id']);
            }

            $google_token = json_decode($provider['settings']['google_token'], TRUE);

            $this->CI->google_sync->refresh_token($google_token['refresh_token']);

            if (empty($unavailability['id_google_calendar']))
            {
                $google_event = $this->CI->google_sync->add_unavailability($provider, $unavailability);

                $unavailability['id_google_calendar'] = $google_event->getId();

                $this->CI->unavailabilities_model->save($unavailability);
            }
            else
            {
                $this->CI->google_sync->update_unavailability($provider, $unavailability);
            }
        }
        catch (Throwable $e)
        {
            log_message('error', 'Synchronization - Could not sync cancellation details of unavailability (' . ($appointment['id'] ?? '-') . ') : ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());
        }
    }

    /**
     * Synchronize removal of an unavailability with external calendars.
     *
     * @param array $unavailability Unavailability record.
     * @param array $provider Provider record.
     */
    public function sync_unavailability_deleted(array $unavailability, array $provider)
    {
        try
        {
            if ( ! $provider['settings']['google_sync'] || empty($unavailability['id_google_calendar']))
            {
                return;
            }

            if (empty($provider['settings']['google_token']))
            {
                throw new RuntimeException('No google token available for the provider: ' . $provider['id']);
            }

            $google_token = json_decode($provider['settings']['google_token'], TRUE);

            $this->CI->google_sync->refresh_token($google_token['refresh_token']);

            $this->CI->google_sync->delete_unavailability($provider, $unavailability['id_google_calendar']);
        }
        catch (Throwable $e)
        {
            log_message('error', 'Synchronization - Could not sync cancellation details of unavailability (' . ($appointment['id'] ?? '-') . ') : ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());
        }
    }
}
