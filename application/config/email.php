<?php defined('BASEPATH') or exit('No direct script access allowed');

// Add custom values by settings them to the $config array.
// Example: $config['smtp_host'] = 'smtp.gmail.com';
// @link https://codeigniter.com/user_guide/libraries/email.html

$config['useragent'] = 'Easy!Appointments';
$config['protocol'] = 'smtp'; // or 'smtp'
$config['mailtype'] = 'html'; // or 'text'

// $config['smtp_debug'] = '1'; // or '1'
// $config['smtp_auth'] = TRUE; //or FALSE for anonymous relay NOTE: DONT USE QUOTES ' !

// $config['smtp_host'] = 'smtp.mailgun.org';
// $config['smtp_user'] = 'contato@mg.lt.coop.br';
// $config['smtp_pass'] = '801618e761fc69da5642e63592d7ea35-b6190e87-68eeb148';
// $config['smtp_crypto'] = 'tls'; // or 'tls'
// $config['smtp_port'] = 587;
$config['smtp_host'] = 'mailhog';
$config['smtp_port'] = 1025;
