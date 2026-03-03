<?php defined('BASEPATH') or exit('No direct script access allowed');

// Add custom values by settings them to the $config array.
// Example: $config['smtp_host'] = 'smtp.gmail.com';
// @link https://codeigniter.com/user_guide/libraries/email.html

$config['useragent'] = 'Easy!Appointments';
$config['protocol'] = 'mail'; // 'mail', 'smtp' or 'ses'
$config['mailtype'] = 'html'; // or 'text'
// $config['smtp_debug'] = '0'; // or '1'
// $config['smtp_auth'] = TRUE; //or FALSE for anonymous relay.
// $config['smtp_host'] = '';
// $config['smtp_user'] = '';
// $config['smtp_pass'] = '';
// $config['smtp_crypto'] = 'ssl'; // or 'tls'
// $config['smtp_port'] = 25;
// $config['ses_region'] = 'eu-west-1';
// $config['ses_smtp_host'] = ''; // optional override, defaults to email-smtp.{region}.amazonaws.com
// $config['ses_smtp_user'] = ''; // AWS SES SMTP username
// $config['ses_smtp_pass'] = ''; // AWS SES SMTP password
// $config['ses_smtp_port'] = 587;
// $config['ses_smtp_crypto'] = 'tls';
// $config['from_name'] = '';
// $config['from_address'] = '';
// $config['reply_to'] = '';
$config['crlf'] = "\r\n";
$config['newline'] = "\r\n";
