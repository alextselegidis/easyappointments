<?php
class SystemConfiguration {
    // General Settings
    public static $base_url    = 'http://url-to-easyappointments-folder/';
    
    // Database Settings
    public static $db_host     = 'localhost';
    public static $db_name     = ''; 
    public static $db_username = '';
    public static $db_password = '';
    
    // Google Calendar API Settings
    public static $google_sync_feature  = TRUE; // Enter TRUE or FALSE;
    public static $google_product_name  = '';
    public static $google_client_id     = '';
    public static $google_client_secret = '';
    public static $google_api_key       = '';
}

/* End of file configuration.php */
/* Location: ./configuration.php */