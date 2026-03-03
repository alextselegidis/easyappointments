<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['payments_provider'] = 'none'; // 'none' | 'stripe'

$config['stripe_secret_key'] = '';
$config['stripe_publishable_key'] = '';
$config['stripe_webhook_secret'] = '';
$config['stripe_currency'] = 'eur';

$config['stripe_platform_fee_percent'] = 10.0;
$config['stripe_enable_payment_for_booking'] = false;
