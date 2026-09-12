<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Email Configuration
 *
 * This file provides default email settings for Easy!Appointments.
 * Custom email settings should be configured in the main config file to ensure
 * they persist across application updates. This file will be overwritten during updates.
 * Example: 'smtp_host' => 'smtp.gmail.com';
 *
 * @link https://codeigniter.com/user_guide/libraries/email.html
 */

/* ====================================================================
 * ATTENTION
 * ====================================================================
 * The configuration settings below are default values.
 * To customize them, please set your own values in the main config file
 * (config.php) to ensure they persist across application updates.
 * Have a look into "config-sample.php" for an example of how to set your
 * own email settings.
 * ====================================================================
 */

$config['useragent'] = 'Easy!Appointments';
$config['protocol'] = 'mail'; // or 'smtp'
$config['mailtype'] = 'html'; // or 'text'
// $config['smtp_debug'] = '0'; // or '1'
// $config['smtp_auth'] = TRUE; //or FALSE for anonymous relay.
// $config['smtp_host'] = '';
// $config['smtp_user'] = '';
// $config['smtp_pass'] = '';
// $config['smtp_crypto'] = 'ssl'; // or 'tls'
// $config['smtp_port'] = 25;
// $config['from_name'] = '';
// $config['from_address'] = '';
// $config['reply_to'] = '';
$config['crlf'] = "\r\n";
$config['newline'] = "\r\n";

// default settings for email library, can be overridden in the main config file (config.php)
if (defined(Config::class . '::EMAIL_SETTINGS') && is_array(Config::EMAIL_SETTINGS)) {
    $config = array_replace($config, Config::EMAIL_SETTINGS ?? []);
}

/* End of file email.php */
/* Location: ./application/config/email.php */
