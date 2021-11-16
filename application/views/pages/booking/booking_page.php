<?php
/**
 * @var string $company_name
 * @var string $customer_token
 * @var string $date_format
 * @var string $time_format
 * @var string $first_weekday
 * @var bool $manage_mode
 * @var array $appointment_data
 * @var array $provider_data
 * @var array $customer_data
 * @var array $available_services
 * @var array $available_providers
 * @var array $show_field
 * @var bool $require_phone_number
 * @var string $display_any_provider
 * @var string $display_terms_and_conditions
 * @var string $display_privacy_policy
 * @var string $display_cookie_notice
 */
?>

<?php extend('layouts/booking/booking_layout') ?>

<?php section('content') ?>

<!-- Booking Cancellation Frame -->

<?php require 'booking_cancellation.php' ?>

<!-- Select Service & Provider -->

<?php require 'booking_step_1.php' ?>

<!-- Pick An Appointment Date -->

<?php require 'booking_step_2.php' ?>

<!-- Enter Customer Information -->

<?php require 'booking_step_3.php' ?>

<!-- Appointment Data Confirmation -->

<?php require 'booking_step_4.php' ?>

<?php section('content') ?>

