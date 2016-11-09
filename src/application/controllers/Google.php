<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Google Controller
 *
 * This controller handles the Google Calendar synchronization operations.
 *
 * @package Controllers
 */
class Google extends CI_Controller {
	/**
	 * Class Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

    /**
     * Authorize Google Calendar API usage for a specific provider.
     *
     * Since it is required to follow the web application flow, in order to retrieve
     * a refresh token from the Google API service, this method is going to authorize
     * the given provider.
     *
     * @param int $provider_id The provider id, for whom the sync authorization is made.
     */
    public function oauth($provider_id) {
    	// Store the provider id for use on the callback function.
    	if (!isset($_SESSION)) {
            @session_start();
        }
    	$_SESSION['oauth_provider_id'] = $provider_id;

        // Redirect browser to google user content page.
        $this->load->library('Google_sync');
        header('Location: ' . $this->google_sync->get_auth_url());
    }

    /**
     * Callback method for the Google Calendar API authorization process.
     *
     * Once the user grants consent with his Google Calendar data usage, the Google
     * OAuth service will redirect him back in this page. Here we are going to store
     * the refresh token, because this is what will be used to generate access tokens
     * in the future.
     *
     * <strong>IMPORTANT!</strong> Because it is necessary to authorize the application
     * using the web server flow (see official documentation of OAuth), every
     * Easy!Appointments installation should use its own calendar api key. So in every
     * api console account, the "http://path-to-e!a/google/oauth_callback" should be
     * included in an allowed redirect url.
     */
    public function oauth_callback() {
       	if (isset($_GET['code'])) {
            $this->load->library('Google_sync');
            $token = $this->google_sync->authenticate($_GET['code']);

       		// Store the token into the database for future reference.
            if (!isset($_SESSION)) {
                @session_start();
            }

            if (isset($_SESSION['oauth_provider_id'])) {
                $this->load->model('providers_model');
                $this->providers_model->set_setting('google_sync', TRUE,
                        $_SESSION['oauth_provider_id']);
                $this->providers_model->set_setting('google_token', $token,
                        $_SESSION['oauth_provider_id']);
                $this->providers_model->set_setting('google_calendar', 'primary',
                        $_SESSION['oauth_provider_id']);
            } else {
                echo '<h1>Sync provider id not specified!</h1>';
            }
       	} else {
            echo '<h1>Authorization Failed!</h1>';
       	}
    }

    /**
     * Complete synchronization of appointments between Google Calendar and Easy!Appointments.
     *
     * This method will completely sync the appointments of a provider with his Google Calendar
     * account. The sync period needs to be relatively small, because a lot of API calls might
     * be necessary and this will lead to consuming the Google limit for the Calendar API usage.
     *
     * @param numeric $provider_id Provider record to be synced.
     */
    public function sync($provider_id = NULL) {
        try {
            // The user must be logged in.
            $this->load->library('session');
            if ($this->session->userdata('user_id') == FALSE) return;

            if ($provider_id === NULL) {
                throw new Exception('Provider id not specified.');
            }

            $this->load->model('appointments_model');
            $this->load->model('providers_model');
            $this->load->model('services_model');
            $this->load->model('customers_model');
            $this->load->model('settings_model');

            $provider = $this->providers_model->get_row($provider_id);

            // Check whether the selected provider has google sync enabled.
            $google_sync = $this->providers_model->get_setting('google_sync', $provider['id']);
            if (!$google_sync) {
                throw new Exception('The selected provider has not the google synchronization '
                        . 'setting enabled.');
            }

            $google_token = json_decode($this->providers_model->get_setting('google_token', $provider['id']));
            $this->load->library('google_sync');
            $this->google_sync->refresh_token($google_token->refresh_token);

            // Fetch provider's appointments that belong to the sync time period.
            $sync_past_days = $this->providers_model->get_setting('sync_past_days', $provider['id']);
            $sync_future_days = $this->providers_model->get_setting('sync_future_days', $provider['id']);
            $start = strtotime('-' . $sync_past_days . ' days', strtotime(date('Y-m-d')));
            $end = strtotime('+' . $sync_future_days . ' days', strtotime(date('Y-m-d')));

            $where_clause = array(
                'start_datetime >=' => date('Y-m-d H:i:s', $start),
                'end_datetime <=' => date('Y-m-d H:i:s', $end),
                'id_users_provider' => $provider['id']
            );

            $appointments = $this->appointments_model->get_batch($where_clause);

            $company_settings = array(
                'company_name' => $this->settings_model->get_setting('company_name'),
                'company_link' => $this->settings_model->get_setting('company_link'),
                'company_email' => $this->settings_model->get_setting('company_email')
            );

            // Sync each appointment with Google Calendar by following the project's sync
            // protocol (see documentation).
            foreach($appointments as $appointment) {
                if ($appointment['is_unavailable'] == FALSE) {
                    $service = $this->services_model->get_row($appointment['id_services']);
                    $customer = $this->customers_model->get_row($appointment['id_users_customer']);
                } else {
                    $service = NULL;
                    $customer = NULL;
                }

                // If current appointment not synced yet, add to gcal.
                if ($appointment['id_google_calendar'] == NULL) {
                    $google_event = $this->google_sync->add_appointment($appointment, $provider,
                            $service, $customer, $company_settings);
                    $appointment['id_google_calendar'] = $google_event->id;
                    $this->appointments_model->add($appointment); // Save gcal id
                } else {
                    // Appointment is synced with google calendar.
                    try {
                        $google_event = $this->google_sync->get_event($provider, $appointment['id_google_calendar']);

                        if ($google_event->status == 'cancelled') {
                            throw new Exception('Event is cancelled, remove the record from Easy!Appointments.');
                        }

                        // If gcal event is different from e!a appointment then update e!a record.
                        $is_different = FALSE;
                        $appt_start = strtotime($appointment['start_datetime']);
                        $appt_end = strtotime($appointment['end_datetime']);
                        $event_start = strtotime($google_event->getStart()->getDateTime());
                        $event_end = strtotime($google_event->getEnd()->getDateTime());

                        if ($appt_start != $event_start || $appt_end != $event_end) {
                            $is_different = TRUE;
                        }

                        if ($is_different) {
                            $appointment['start_datetime'] = date('Y-m-d H:i:s', $event_start);
                            $appointment['end_datetime'] = date('Y-m-d H:i:s', $event_end);
                            $this->appointments_model->add($appointment);
                        }

                    } catch(Exception $exc) {
                        // Appointment not found on gcal, delete from e!a.
                        $this->appointments_model->delete($appointment['id']);
                        $appointment['id_google_calendar'] = NULL;
                    }
                }
            }

            // :: ADD GCAL EVENTS THAT ARE NOT PRESENT ON E!A
            $google_calendar = $provider['settings']['google_calendar'];
            $events = $this->google_sync->get_sync_events($google_calendar, $start, $end);

            foreach($events->getItems() as $event) {
                $results = $this->appointments_model->get_batch(array('id_google_calendar' => $event->getId()));
                if (count($results) == 0) {
                    // Record doesn't exist in E!A, so add the event now.
                    $appointment = array(
                        'start_datetime' => date('Y-m-d H:i:s', strtotime($event->start->getDateTime())),
                        'end_datetime' => date('Y-m-d H:i:s', strtotime($event->end->getDateTime())),
                        'is_unavailable' => TRUE,
                        'notes' => $event->getSummary() . ' ' . $event->getDescription(),
                        'id_users_provider' => $provider_id,
                        'id_google_calendar' => $event->getId(),
                        'id_users_customer' => NULL,
                        'id_services' => NULL,
                    );

                    $this->appointments_model->add($appointment);
                }
            }

            echo json_encode(AJAX_SUCCESS);

        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavaScript($exc))
            ));
        }
    }
}

/* End of file google.php */
/* Location: ./application/controllers/google.php */
