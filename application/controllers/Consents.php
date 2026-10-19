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
     * Fields a client is allowed to provide when recording a consent.
     *
     * The "id" field is deliberately excluded, so that this public endpoint can only insert new records and never
     * update (or re-link) an existing one. The "id_users" and "ip" fields are set server-side only.
     *
     * @var array
     */
    public array $allowed_consent_fields = ['first_name', 'last_name', 'email', 'type'];

    /**
     * Consents constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('consents_model');
    }

    /**
     * Save consent record to the database.
     */
    public function save(): void
    {
        try {
            method('post');

            check('consent', 'array');

            $consent = request('consent');

            $this->consents_model->only($consent, $this->allowed_consent_fields);

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
