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
    private $client; 
    private $service;
    
    /**
     * Class Constructor
     * 
     * This method initializes the Google client class and the Calendar service
     * class so that they can be used by the other methods. 
     */
    public function __construct() {
        session_start();
        
        $CI =& get_instance();
        
        // Initialize google client and calendar service.
        $this->client = new Google_Client();
        $this->client->setApplicationName(SystemConfiguration::$google_product_name);
        $this->client->setClientId(SystemConfiguration::$google_client_id);
        $this->client->setClientSecret(SystemConfiguration::$google_client_secret);
        $this->client->setDeveloperKey(SystemConfiguration::$google_api_key);
        $this->client->setRedirectUri('http://localhost/oauth_callback');
        $this->service = new Google_CalendarService($this->client);
    }
    
    /**
     * Validate the Google API access token of a provider.
     * 
     * In order to manage a Google user's data, one need a valid access token. 
     * This token is provided when the user grants the permission to a system
     * to access his Google account data. So before making any action we need 
     * to make sure that the available token is still valid.
     * 
     * <strong>IMPORTANT!</strong> Always use this method before anything else
     * in order to make sure that the token is being set and still valid.
     * 
     * @param string $access_token This token is normally stored in the database.
     * @return bool Returns the validation result.
     */
    public function validate_token($access_token) {
         $this->client->setAccessToken($access_token);
         return $this->client->isAccessTokenExpired();
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
     */
    public function add_appointment($appointment_id) {
        $CI =& get_instance();
        
        $CI->load->model('Appointments_Model');
        $CI->load->model('Service_Model');
        $CI->load->model('Provider_Model');
        $CI->load->model('Customers_Model');
        $CI->load->model('Settings_Model');
        
        $appointment_data   = $CI->Appointments_Model->get_row($appointment_id);
        $provider_data      = $CI->Providers_Model->get_row($appointment_data['id_users_provider']);
        $customer_data      = $CI->Customers_Model->get_row($appointment_data['id_users_customer']);
        $service_data       = $CI->Services_Model->get_row($appointment_data['id_services']);
        $company_name       = $CI->Settings_Model->get_setting('company_name');
        
        $CI->load->helper('general');
        
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
        
        return $created_event;
    }
    
    /**
     * Update an existing appointment that is already synced with Google Calendar.
     * 
     * @param int $appointment_id
     */
    public function update_appointment($appointment_id) {
        
    }
    
    /**
     * Delete an existing appointment from Google Calendar.
     * 
     * @param type $appointment_id
     */
    public function delete_appointment($appointment_id) {
        
    }
}

/* End of file google_sync.php */
/* Location: ./application/libraries/google_sync.php */