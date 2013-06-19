<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

// Google API PHP Client is necessary to perform sync operations.
require_once dirname(__FILE__) . '/external/google-api-php-client/Google_Client.php';
require_once dirname(__FILE__) . '/external/google-api-php-client/contrib/Google_CalendarService.php';
require_once dirname(dirname(dirname(__FILE__))) . '/configuration.php';

/**
 * Google Synchronization Class
 * 
 * This class implements all the core synchronization between the Google Calendar
 * and the Easy!Appointments system. 
 */
class Google_Sync {
	private $CI;
    private $client; 
    private $service;
    
    /**
     * Class Constructor
     * 
     * This method initializes the Google client class and the Calendar service
     * class so that they can be used by the other methods. 
     */
    public function __construct() {        
        $this->CI =& get_instance();
        
        if (!isset($_SESSION)) {
            @session_start();
        }
        
        // Initialize google client and calendar service.
        $this->client = new Google_Client();
        $this->client->setApplicationName(SystemConfiguration::$google_product_name);
        $this->client->setClientId(SystemConfiguration::$google_client_id);
        $this->client->setClientSecret(SystemConfiguration::$google_client_secret);
        $this->client->setDeveloperKey(SystemConfiguration::$google_api_key);
        $this->client->setRedirectUri($this->CI->config->item('base_url') . 'google/oauth_callback');
        $this->service = new Google_CalendarService($this->client);
    }
    
    /**
     * Get Google OAuth authorization url. 
     * 
     * This url must be used to redirect the user to the Google user consent page, 
     * where the user grants access to his data for the Easy!Appointments app.
     */
    public function get_auth_url() {
    	// "max_auth_age" is needed because the user needs to always log in 
    	// and not use an existing session.
    	return $this->client->createAuthUrl() . '&max_auth_age=0';
    }
    
    /**
     * Authenticate the Google API usage.
     * 
     * When the user grants consent for his data usage, google is going to redirect 
     * the browser back to the given redirect url. There a authentication code is 
     * provided. Using this code, we can authenticate the API usage and store the 
     * token information to the database.
     * 
     * @see Google Controller
     */
    public function authenticate($auth_code) {
        $this->client->authenticate($auth_code);
        return $this->client->getAccessToken();
    }
    
    /**
     * Refresh the Google Client access token.
     * 
     * This method must be executed every time we need to make actions on a 
     * provider's Google Calendar account. A new token is necessary and the 
     * only way to get it is to use the stored refresh token that was provided
     * when the provider granted consent to Easy!Appointments for use his 
     * Google Calendar account.
     * 
     * @param string $refresh_token The provider's refresh token. This value is
     * stored in the database and used every time we need to make actions to his
     * Google Caledar account.
     */
    public function refresh_token($refresh_token) {    	
    	$this->client->refreshToken($refresh_token);
    }
    
    /**
     * Add an appointment record to its providers Google Calendar account.
     * 
     * This method checks whether the appointment's provider has enabled the Google
     * Sync utility of Easy!Appointments and the stored access token is still valid. 
     * If yes, the selected appointment record is going to be added to the Google 
     * Calendar account. 
     * 
     * <strong>IMPORTANT!</strong> If the access token is not valid anymore the 
     * appointment cannot be added to the Google Calendar. A notification warning
     * must be sent to the provider in order to authorize the E!A again, and store
     * the new access token to the database.
     * 
     * @param int $appointment_id The record id of the appointment that is going to
     * be added to the database.
     * @return Google_Event Returns the Google_Event class object.
     * 
     * @task This library should not use the models. The data must be provided from 
     * the controllers (same for notification library).
     */
    public function add_appointment($appointment_id) {
        $this->CI->load->model('Appointments_Model');
        $this->CI->load->model('Providers_Model');
        $this->CI->load->model('Services_Model');
        $this->CI->load->model('Customers_Model');
        $this->CI->load->model('Settings_Model');
        
        $appointment_data   = $this->CI->Appointments_Model->get_row($appointment_id);
        $provider_data      = $this->CI->Providers_Model->get_row($appointment_data['id_users_provider']);
        $customer_data      = $this->CI->Customers_Model->get_row($appointment_data['id_users_customer']);
        $service_data       = $this->CI->Services_Model->get_row($appointment_data['id_services']);
        $company_name       = $this->CI->Settings_Model->get_setting('company_name');
        
        $this->CI->load->helper('general');
        
        $event = new Google_Event();
        $event->setSummary($service_data['name']);
        $event->setLocation($company_name);
        
        $start = new Google_EventDateTime();
        $start->setDateTime(date3339(strtotime($appointment_data['start_datetime'])));
        $event->setStart($start);
        
        $end = new Google_EventDateTime();
        $end->setDateTime(date3339(strtotime($appointment_data['end_datetime'])));
        $event->setEnd($end);
        
        $eventProvider = new Google_EventAttendee();
        $eventProvider->setDisplayName($provider_data['first_name'] . ' ' 
                . $provider_data['last_name']);
        $eventProvider->setEmail($provider_data['email']);
        
        $eventCustomer = new Google_EventAttendee();
        $eventCustomer->setDisplayName($customer_data['first_name'] . ' ' 
                . $customer_data['last_name']);
        $eventCustomer->setEmail($customer_data['email']);
        
        $event->attendees = array(
            $eventProvider, 
            $eventCustomer
        );
        
        // Add the new event to the "primary" calendar.
        $created_event = $this->service->events->insert('primary', $event);
        
        // Set the Google Calendar event id to the E!A database record.
        $appointment_data['id_google_calendar'] = $created_event['id'];
        $this->CI->Appointments_Model->add($appointment_data);
        
        return $created_event;
    }
    
    /**
     * Update an existing appointment that is already synced with Google Calendar.
     * 
     * @param int $appointment_id The appointment record id.
     */
    public function update_appointment($appointment_data, $provider_data, 
            $service_data, $customer_data) {
        $this->CI->load->model('Appointments_Model');
        $this->CI->load->model('Services_Model');
        $this->CI->load->model('Providers_Model');
        $this->CI->load->model('Customers_Model');
        $this->CI->load->model('Settings_Model');
        
        $appointment_data   = $this->CI->Appointments_Model->get_row($appointment_id);
        $provider_data      = $this->CI->Providers_Model->get_row($appointment_data['id_users_provider']);
        $customer_data      = $this->CI->Customers_Model->get_row($appointment_data['id_users_customer']);
        $service_data       = $this->CI->Services_Model->get_row($appointment_data['id_services']);
        $company_name       = $this->CI->Settings_Model->get_setting('company_name');
        
        $this->CI->load->helper('general');
        
    }
    
    /**
     * Delete an existing appointment from Google Calendar.
     * 
     * @param string $google_calendar_id The Google Calendar event id to
     * be deleted.
     */
    public function delete_appointment($google_calendar_id) {
        $this->service->events->delete('primary', $google_calendar_id);
    }
}

/* End of file google_sync.php */
/* Location: ./application/libraries/google_sync.php */