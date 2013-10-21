<?php
class SystemConfiguration {
    // General Settings
    public static $base_url    = 'http://localhost/dev/external/easy_appointments/trunk/src/';
    
    // Database Settings
    public static $db_host     = 'localhost';
    public static $db_name     = 'Enter DB Name Here'; 
    public static $db_username = 'Enter DB Username Here';
    public static $db_password = 'Enter DB Password Here';
    
    // Google Calendar API Settings
    public static $google_sync_feature  = FALSE; // Enter TRUE or FALSE;
    public static $google_product_name  = 'Easy!Appointments';
    public static $google_client_id     = 'Enter Google Client Id Here';
    public static $google_client_secret = 'Enter Google Client Secret Here';
    public static $google_api_key       = 'Enter Google API Key Here';
}

/* End of file configuration.php */
/* Location: ./configuration.php */