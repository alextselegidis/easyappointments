<?php

/**
 * Class Notifications
 *
 * This library handles all the notification email deliveries on the system.
 *
 * The email configuration settings are located at: /application/config/email.php
 */
class Notifications {
    /**
     * @var CI_Controller
     */
    private $framework;

    /**
     * Notifications constructor.
     */
    public function __construct()
    {
        $this->framework = &get_instance();
    }

    /**
     * Send an email notification for an appointment confirmation.
     *
     * @param array $appointment
     * @param array $service
     * @param array $provider
     * @param array $customer
     * @param array $settings
     * @param string $subject
     * @param string $message
     * @param string $link
     * @param string $recipient
     * @param string $ics
     */
    public function send_appointment_confirmation(
        $appointment,
        $service,
        $provider,
        $customer,
        $settings,
        $subject,
        $message,
        $link,
        $recipient,
        $ics
    )
    {
        // TODO: Port logic from engine/Notifications/Email.php
    }

    /**
     * Send an email notification for an appointment removal.
     *
     * @param array $appointment
     * @param array $service
     * @param array $provider
     * @param array $customer
     * @param array $settings
     * @param string $recipient
     * @param string $reason
     */
    public function send_appointment_removal(
        $appointment,
        $service,
        $provider,
        $customer,
        $settings,
        $recipient,
        $reason
    )
    {
        // TODO: Port logic from engine/Notifications/Email.php
    }

    /**
     * Send an email notification with the new password, used when recovering an account.
     *
     * @param string $password
     * @param string $recipient
     * @param string $settings
     */
    public function send_new_password($password, $recipient, $settings)
    {
        // TODO: Port logic from engine/Notifications/Email.php
    }
}
