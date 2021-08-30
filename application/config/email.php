<?php defined('BASEPATH') or exit('No direct script access allowed');

// Add custom values by settings them to the $config array.
// Example: $config['smtp_host'] = 'smtp.gmail.com';
// @link https://codeigniter.com/user_guide/libraries/email.html

$config['useragent']   = 'Easy!Appointments';
$config['protocol']    = Config::EMAIL_PROTOCOL;
$config['mailtype']    = Config::EMAIL_MAILTYPE;
$config['smtp_debug']  = Config::EMAIL_SMTP_DEBUG;
$config['smtp_auth']   = Config::EMAIL_SMTP_AUTH;
$config['smtp_host']   = Config::EMAIL_SMTP_HOST;
$config['smtp_user']   = Config::EMAIL_SMTP_USER;
$config['smtp_pass']   = Config::EMAIL_SMTP_PASS;
$config['smtp_crypto'] = Config::EMAIL_SMTP_CRYPTO;
$config['smtp_port']   = Config::EMAIL_SMTP_PORT;
