<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

/**
 * Consents controller.
 *
 * Handles user consent related operations.
 *
 * @package Controllers
 */
class Consents extends EA_Controller
{
    /**
     * Consents constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('consents_model');
    }

    /**
     * Save (insert or update) the consent
     */
    public function save(): void
    {
        try {
            $consent = request('consent');

            $consent['ip'] = $this->input->ip_address();

            $occurrences = $this->consents_model->get(['ip' => $consent['ip']], 1, 0, 'create_datetime DESC');

            if (!empty($occurrences)) {
                $last_consent = $occurrences[0];

                $last_consent_create_datetime_instance = new DateTime($last_consent['create_datetime']);

                $threshold_datetime_instance = new DateTime('-24 hours');

                if ($last_consent_create_datetime_instance > $threshold_datetime_instance) {
                    // Do not create a new consent.

                    json_response([
                        'success' => true,
                    ]);

                    return;
                }
            }

            $consent['id'] = $this->consents_model->save($consent);

            json_response([
                'success' => true,
                'id' => $consent['id'],
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
