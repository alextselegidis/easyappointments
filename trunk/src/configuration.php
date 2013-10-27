<?php
class SystemConfiguration {
    // General Settings
    public static $base_url    = 'http://localhost/dev/external/easy_appointments/trunk/src/';
    
    // Database Settings
    public static $db_host     = 'localhost';
    public static $db_name     = 'new_ea'; //'easy_appointments';
    public static $db_username = 'root';
    public static $db_password = '';
    
    // Google Calendar API Settings
    public static $google_sync_feature  = TRUE; // Enter TRUE or FALSE;
    public static $google_product_name  = 'Easy!Appointments';
    public static $google_client_id     = '396094740598-l9ohhdgs0hr6qi89628p3chf9lm59mkc.apps.googleusercontent.com';
    public static $google_client_secret = '3kKEgx3mgxfFInrWf3jTUn4D';
    public static $google_api_key       = 'AIzaSyCLMin3-ePz8xShJ7klSduo7iChDV7ldc0';
}

/* End of file configuration.php */
/* Location: ./configuration.php */