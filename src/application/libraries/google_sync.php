<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

// Google API PHP Client is necessary to perform sync operations.
require_once dirname(__FILE__) . '/google-api-php-client/Google_Client.php';
require_once dirname(__FILE__) . '/google-api-php-client/contrib/Google_CalendarService.php';
require_once dirname(dirname(dirname(__FILE__))) . '/configuration.php';

class Google_Sync {
    private $client; 
    private $service;
    
    /**
     * Class Constructor
     * 
     * This method initializes the google client and the calendar service
     * so that they can be used by the other methods.
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
        $this->client->setRedirectUri($CI->config->item('base_url') . 'google/api_auth');
        $this->service = new Google_CalendarService($this->client);
    }
    
    /**
     * Authorize google calendar api.
     * 
     * Before the system is able to use the google calendar api
     * it must be authorized to access the users appointment. This
     * method checks where an authorization token exists in the 
     * session cookie and handles the necessary actions.
     * 
     * <strong>IMPORTANT</strong> This method must be called every
     * time before the usage of the google api.
     */
    public function authorize_api() {
        // USE ONLY FOR RESETTING THE TOKEN - DEBUGGING
        //unset($_SESSION['google_api_token']); 
        
        // If the user is logged out there shouldn't be a token 
        // entry to the session array.
        if (isset($_GET['logout'])) { 
            unset($_SESSION['google_api_token']);
        }
        
        // If there is a 'code' entry available, authenticate the api. 
        if (isset($_GET['code'])) { 
            $this->client->authenticate($_GET['code']);
            $_SESSION['google_api_token'] = $this->client->getAccessToken();
            header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']); // refreshes current url
        }
        
        // If there is an active token then assign it to the client object.
        if (isset($_SESSION['google_api_token'])) { 
            $this->client->setAccessToken($_SESSION['google_api_token']); 
        }
        
        if (!$this->client->getAccessToken()) {
            $authUrl = $this->client->createAuthUrl();
            header('Location: ' . $authUrl);
        }
    }
    
    /**
     * Synchronize a sigle appointment with Google Calendar.
     * 
     * This method syncs an existing appointment with its' providers
     * google calendar account.
     * 
     * @expectedException Exception An error has occured during 
     * the sync operation.
     * 
     * @param int $appointment_id The appointments database id.
     * @return Google_Event Returns the Google_Event class object.
     */
    public function sync_appointment($appointment_id) {
        // Store the appointment id in the session cookies in case that 
        // we need to redirect to the authorization page of google.
        $_SESSION['sync_appointment_id'] = $appointment_id; 
        
        // Before procceeding to the sync operation we must authorize
        // the user must authorize the application.
        $this->authorize_api();
        
        // Load the models and retrieve the necessary data from db.
        $CI =& get_instance();
        
        $CI->load->model('Appointments_Model');
        $CI->load->model('Customers_Model');
        $CI->load->model('Providers_Model');
        $CI->load->model('Services_Model');
        $CI->load->model('Settings_Model');
        
        $appointment    = $CI->Appointments_Model->get_row($appointment_id);
        $customer       = $CI->Customers_Model->get_row($appointment['id_users_customer']);
        $provider       = $CI->Providers_Model->get_row($appointment['id_users_provider']);
        $service        = $CI->Services_Model->get_row($appointment['id_services']);
        
        // Add a new event to the user's google calendar.
        $CI->load->helper('general');
        
        $event = new Google_Event();
        $event->setSummary($service['name']);
        $event->setLocation($CI->Settings_Model->get_setting('business_name'));
        
        $start = new Google_EventDateTime();
        $start->setDateTime(date3339(strtotime($appointment['start_datetime'])));
        $event->setStart($start);
        
        $end = new Google_EventDateTime();
        $end->setDateTime(date3339(strtotime($appointment['end_datetime'])));
        $event->setEnd($end);
        
        $eventProvider = new Google_EventAttendee();
        $eventProvider->setDisplayName($provider['first_name'] . ' ' . $provider['last_name']);
        //$eventProvider->setSelf(true);
        $eventProvider->setEmail($provider['email']);
        
        $eventCustomer = new Google_EventAttendee();
        $eventCustomer->setDisplayName($customer['first_name'] . ' ' . $customer['last_name']);
        $eventCustomer->setEmail($customer['email']);
        
        $event->attendees = array ($eventProvider, $eventCustomer);
        
        $created_event = $this->service->events->insert('primary', $event);
        
        unset($_SESSION['sync_appointment_id']);
        
        return $created_event;
    }
}

/* End of file google_sync.php */
/* Location: ./application/libraries/google_sync.php */