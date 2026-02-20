<?php defined('BASEPATH') or exit('No direct script access allowed');

// Email configuration. Values can be provided via environment variables
// to allow configuring SMTP when deploying with Docker.
// Supported env vars: MAIL_PROTOCOL, MAIL_MAILTYPE, MAIL_SMTP_HOST,
// MAIL_SMTP_USER, MAIL_SMTP_PASS, MAIL_SMTP_CRYPTO, MAIL_SMTP_PORT,
// MAIL_FROM_ADDRESS, MAIL_FROM_NAME, MAIL_SMTP_AUTH

// helper to get env with fallback
if (! function_exists('env_config')) {
	function env_config($name, $default = null)
	{
		$v = getenv($name);
		return ($v === false) ? $default : $v;
	}
}

$config['useragent'] = 'Easy!Appointments';
$config['protocol'] = env_config('MAIL_PROTOCOL', 'mail'); // or 'smtp'
$config['mailtype'] = env_config('MAIL_MAILTYPE', 'html'); // or 'text'

// SMTP specific values (leave empty if not using SMTP)
$config['smtp_host'] = env_config('MAIL_SMTP_HOST', '');
$config['smtp_user'] = env_config('MAIL_SMTP_USER', '');
$config['smtp_pass'] = env_config('MAIL_SMTP_PASS', '');
$config['smtp_crypto'] = env_config('MAIL_SMTP_CRYPTO', ''); // 'ssl' or 'tls'

$smtp_port = env_config('MAIL_SMTP_PORT', '');
$config['smtp_port'] = ($smtp_port === '') ? 25 : (int) $smtp_port;

$smtp_auth = env_config('MAIL_SMTP_AUTH', null);
if ($smtp_auth === null) {
	// default to TRUE when smtp_host is set, otherwise FALSE
	$config['smtp_auth'] = $config['smtp_host'] ? true : false;
} else {
	$config['smtp_auth'] = filter_var($smtp_auth, FILTER_VALIDATE_BOOLEAN);
}

$config['from_name'] = env_config('MAIL_FROM_NAME', '');
$config['from_address'] = env_config('MAIL_FROM_ADDRESS', '');
$config['reply_to'] = env_config('MAIL_REPLY_TO', '');

$config['crlf'] = "\r\n";
$config['newline'] = "\r\n";
