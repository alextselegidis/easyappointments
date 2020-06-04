<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Add custom values by settings them to the $config array.
// Example: $config['smtp_host'] = 'smtp.gmail.com'; 
// @link https://codeigniter.com/user_guide/libraries/email.html

$config['useragent'] = 'Easy!Appointments';
$config['mailtype'] = 'html'; // or 'text'

$config['protocol'] = Config::MAIL_PROTOCOL;

// SMTP configuration
$config['smtp_host'] = Config::MAIL_SMTP_HOST;
$config['smtp_user'] = Config::MAIL_SMTP_USER; 
$config['smtp_pass'] = Config::MAIL_SMTP_PASS;
$config['smtp_crypto'] = Config::MAIL_SMTP_CRYPTO;
$config['smtp_port'] = Config::MAIL_SMTP_PORT;
