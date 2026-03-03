<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

use Jsvrcek\ICS\Exception\CalendarEventException;

require_once __DIR__ . '/Google.php';
require_once __DIR__ . '/Caldav.php';

/**
 * Console controller.
 *
 * Handles all the Console related operations.
 */
class Console extends EA_Controller
{
    /**
     * Console constructor.
     */
    public function __construct()
    {
        if (!is_cli()) {
            exit('No direct script access allowed');
        }

        parent::__construct();

        $this->load->dbutil();

        $this->load->library('instance');

        $this->load->model('admins_model');
        $this->load->model('appointments_model');
        $this->load->model('customers_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');

        $this->load->library('email_messages');
        $this->load->library('notifications');
        $this->load->library('stripe_service');
    }

    /**
     * Perform a console installation.
     *
     * Use this method to install Easy!Appointments directly from the terminal.
     *
     * Usage:
     *
     * php index.php console install
     *
     * @throws Exception
     */
    public function install(): void
    {
        $this->instance->migrate('fresh');

        $password = $this->instance->seed();

        response(
            PHP_EOL . '⇾ Installation completed, login with "administrator" / "' . $password . '".' . PHP_EOL . PHP_EOL,
        );
    }

    /**
     * Migrate the database to the latest state.
     *
     * Use this method to upgrade an Easy!Appointments instance to the latest database state.
     *
     * Notice:
     *
     * Do not use this method to install the app as it will not seed the database with the initial entries (admin,
     * provider, service, settings etc.).
     *
     * Usage:
     *
     * php index.php console migrate
     *
     * php index.php console migrate fresh
     *
     * @param string $type
     */
    public function migrate(string $type = ''): void
    {
        $this->instance->migrate($type);
    }

    /**
     * Seed the database with test data.
     *
     * Use this method to add test data to your database
     *
     * Usage:
     *
     * php index.php console seed
     * @throws Exception
     */
    public function seed(): void
    {
        $this->instance->seed();
    }

    /**
     * Create a database backup file.
     *
     * Use this method to back up your Easy!Appointments data.
     *
     * Usage:
     *
     * php index.php console backup
     *
     * php index.php console backup /path/to/backup/folder
     *
     * @throws Exception
     */
    public function backup(): void
    {
        $this->instance->backup($GLOBALS['argv'][3] ?? null);
    }

    /**
     * Trigger the synchronization of all provider calendars with Google Calendar.
     *
     * Use this method in a cronjob to automatically sync events between Easy!Appointments and Google Calendar.
     *
     * Notice:
     *
     * Google syncing must first be enabled for each individual provider from inside the backend calendar page.
     *
     * Usage:
     *
     * php index.php console sync
     *
     * @throws CalendarEventException
     * @throws Exception
     * @throws Throwable
     */
    public function sync(): void
    {
        $providers = $this->providers_model->get();

        foreach ($providers as $provider) {
            if (filter_var($provider['settings']['google_sync'], FILTER_VALIDATE_BOOLEAN)) {
                Google::sync((string) $provider['id']);
            }

            if (filter_var($provider['settings']['caldav_sync'], FILTER_VALIDATE_BOOLEAN)) {
                Caldav::sync((string) $provider['id']);
            }
        }
    }

    public function remind(int $hours_ahead = 24, int $window_minutes = 60): void
    {
        $hours_ahead = max(1, $hours_ahead);
        $window_minutes = max(5, min(360, $window_minutes));
        $has_reminder_sent_at = $this->db->field_exists('reminder_sent_at', 'appointments');

        $start = new DateTime();
        $start->modify('+' . $hours_ahead . ' hours');

        $end = clone $start;
        $end->modify('+' . $window_minutes . ' minutes');

        $start_str = $start->format('Y-m-d H:i:s');
        $end_str = $end->format('Y-m-d H:i:s');

        $appointments = $this->db
            ->select('appointments.*')
            ->from('appointments')
            ->join('users customers', 'customers.id = appointments.id_users_customer', 'inner')
            ->where('appointments.is_unavailability', 0)
            ->where('appointments.start_datetime >=', $start_str)
            ->where('appointments.start_datetime <=', $end_str)
            ->where('customers.email !=', '')
            ->order_by('appointments.start_datetime', 'ASC')
            ->get()
            ->result_array();

        if ($has_reminder_sent_at) {
            $appointments = array_values(array_filter(
                $appointments,
                static fn(array $appointment): bool => empty($appointment['reminder_sent_at']),
            ));
        }

        $sent = 0;
        $skipped = 0;

        foreach ($appointments as $appointment) {
            $notes = (string) ($appointment['notes'] ?? '');

            if (str_contains($notes, '[reminder_sent_24h]')) {
                $skipped++;
                continue;
            }

            $service = $this->services_model->find((int) $appointment['id_services']);
            $provider = $this->providers_model->find((int) $appointment['id_users_provider']);
            $customer = $this->customers_model->find((int) $appointment['id_users_customer']);

            $settings = [
                'company_name' => setting('company_name'),
                'company_email' => setting('company_email'),
                'company_link' => setting('company_link'),
                'date_format' => setting('date_format'),
                'time_format' => setting('time_format'),
                'company_color' => setting('company_color'),
            ];

            $sent_ok = $this->notifications->notify_appointment_reminder(
                $appointment,
                $service,
                $provider,
                $customer,
                $settings,
            );

            if (!$sent_ok) {
                continue;
            }

            $updated_notes = trim($notes . ' [reminder_sent_24h]');

            $update_payload = [
                'notes' => $updated_notes,
                'update_datetime' => date('Y-m-d H:i:s'),
            ];

            if ($has_reminder_sent_at) {
                $update_payload['reminder_sent_at'] = date('Y-m-d H:i:s');
            }

            $this->db
                ->where('id', (int) $appointment['id'])
                ->update('appointments', $update_payload);

            $sent++;
        }

        response(
            PHP_EOL .
            '⇾ Reminder job done | window=[' .
            $start_str .
            ' - ' .
            $end_str .
            '] | matched=' .
            count($appointments) .
            ' | sent=' .
            $sent .
            ' | skipped=' .
            $skipped .
            PHP_EOL .
            PHP_EOL,
        );
    }

    public function email_test(string $recipient_email = ''): void
    {
        try {
            $recipient_email = trim($recipient_email);

            if ($recipient_email === '') {
                response(
                    PHP_EOL .
                    '⇾ Missing recipient email.' .
                    PHP_EOL .
                    'Usage: php index.php console email_test you@example.com' .
                    PHP_EOL .
                    PHP_EOL,
                );

                return;
            }

            $subject = 'BUUMER email test (' . date('Y-m-d H:i:s') . ')';
            $message =
                'This is a transport test email sent by BUUMER console.' .
                PHP_EOL .
                'If you received this message, the configured mail protocol is working.';

            $this->email_messages->send_test_message($recipient_email, $subject, $message);

            response(
                PHP_EOL .
                '⇾ Test email sent successfully to ' .
                $recipient_email .
                PHP_EOL .
                PHP_EOL,
            );
        } catch (Throwable $e) {
            response(
                PHP_EOL .
                '⇾ Test email failed: ' .
                $e->getMessage() .
                PHP_EOL .
                PHP_EOL,
            );
        }
    }

    public function stripe_health(): void
    {
        $provider = (string) (config('payments_provider') ?: 'none');
        $secret_set = !empty(config('stripe_secret_key'));
        $publishable_set = !empty(config('stripe_publishable_key'));
        $webhook_set = !empty(config('stripe_webhook_secret'));
        $sdk_available = class_exists('\\Stripe\\StripeClient');
        $intents_table = $this->db->table_exists('stripe_payment_intents');
        $tenants_table = $this->db->table_exists('tenants');
        $configured = $this->stripe_service->is_configured();

        $lines = [
            '',
            '⇾ Stripe health check',
            'provider: ' . $provider,
            'stripe_secret_key: ' . ($secret_set ? 'set' : 'missing'),
            'stripe_publishable_key: ' . ($publishable_set ? 'set' : 'missing'),
            'stripe_webhook_secret: ' . ($webhook_set ? 'set' : 'missing'),
            'stripe_sdk: ' . ($sdk_available ? 'available' : 'missing'),
            'table_tenants: ' . ($tenants_table ? 'ok' : 'missing'),
            'table_stripe_payment_intents: ' . ($intents_table ? 'ok' : 'missing'),
            'service_is_configured: ' . ($configured ? 'yes' : 'no'),
            '',
        ];

        response(implode(PHP_EOL, $lines));
    }

    public function integrations_check(): void
    {
        $this->config->load('email');

        $lines = [''];
        $overall_ok = true;

        $email_protocol = (string) (config('protocol') ?: 'mail');
        $company_email = (string) (setting('company_email') ?: '');
        $from_address = (string) (config('from_address') ?: $company_email);
        $reply_to = (string) (config('reply_to') ?: $company_email);
        $customer_notifications = filter_var(setting('customer_notifications'), FILTER_VALIDATE_BOOLEAN);

        $lines[] = '⇾ Integrations readiness check';
        $lines[] = '';
        $lines[] = '[EMAIL]';
        $lines[] = 'protocol: ' . $email_protocol;
        $lines[] = 'customer_notifications: ' . ($customer_notifications ? 'enabled' : 'disabled');

        if (!filter_var($from_address, FILTER_VALIDATE_EMAIL)) {
            $lines[] = 'FAIL from_address/company_email: invalid';
            $overall_ok = false;
        } else {
            $lines[] = 'PASS from_address/company_email: valid';
        }

        if (!filter_var($reply_to, FILTER_VALIDATE_EMAIL)) {
            $lines[] = 'WARN reply_to: invalid (will fallback to from)';
        } else {
            $lines[] = 'PASS reply_to: valid';
        }

        if ($email_protocol === 'ses') {
            $ses_user = (string) (config('ses_smtp_user') ?: '');
            $ses_pass = (string) (config('ses_smtp_pass') ?: '');
            $ses_region = (string) (config('ses_region') ?: 'eu-west-1');
            $lines[] = 'ses_region: ' . $ses_region;
            if ($ses_user === '' || $ses_pass === '') {
                $lines[] = 'FAIL ses_smtp_user/ses_smtp_pass: missing';
                $overall_ok = false;
            } else {
                $lines[] = 'PASS ses_smtp_user/ses_smtp_pass: set';
            }
        } elseif ($email_protocol === 'smtp') {
            $smtp_host = (string) (config('smtp_host') ?: '');
            $smtp_user = (string) (config('smtp_user') ?: '');
            $smtp_pass = (string) (config('smtp_pass') ?: '');
            if ($smtp_host === '' || $smtp_user === '' || $smtp_pass === '') {
                $lines[] = 'FAIL smtp_host/smtp_user/smtp_pass: incomplete';
                $overall_ok = false;
            } else {
                $lines[] = 'PASS smtp_host/smtp_user/smtp_pass: set';
            }
        } else {
            $lines[] = 'WARN protocol=mail (acceptable for local/dev, not recommended for production)';
        }

        $provider = (string) (config('payments_provider') ?: 'none');
        $secret_set = !empty(config('stripe_secret_key'));
        $publishable_set = !empty(config('stripe_publishable_key'));
        $webhook_set = !empty(config('stripe_webhook_secret'));
        $sdk_available = class_exists('\\Stripe\\StripeClient');
        $intents_table = $this->db->table_exists('stripe_payment_intents');
        $tenants_table = $this->db->table_exists('tenants');
        $service_configured = $this->stripe_service->is_configured();

        $lines[] = '';
        $lines[] = '[STRIPE]';
        $lines[] = 'payments_provider: ' . $provider;
        $lines[] = 'table_tenants: ' . ($tenants_table ? 'ok' : 'missing');
        $lines[] = 'table_stripe_payment_intents: ' . ($intents_table ? 'ok' : 'missing');

        if (!$tenants_table || !$intents_table) {
            $lines[] = 'FAIL stripe schema: missing required table(s)';
            $overall_ok = false;
        }

        if ($provider !== 'stripe') {
            $lines[] = 'WARN payments_provider != stripe (Stripe disabled)';
        } else {
            if (!$secret_set || !$publishable_set || !$webhook_set) {
                $lines[] = 'FAIL stripe keys/webhook secret: incomplete';
                $overall_ok = false;
            } else {
                $lines[] = 'PASS stripe keys/webhook secret: set';
            }

            if (!$sdk_available) {
                $lines[] = 'FAIL stripe SDK: missing';
                $overall_ok = false;
            } else {
                $lines[] = 'PASS stripe SDK: available';
            }

            $lines[] = 'service_is_configured: ' . ($service_configured ? 'yes' : 'no');
            if (!$service_configured) {
                $overall_ok = false;
            }
        }

        $lines[] = '';
        $lines[] = $overall_ok ? 'RESULT: PASS (readiness baseline OK)' : 'RESULT: FAIL/WARN (see checks above)';
        $lines[] = '';
        $lines[] = 'Next commands:';
        $lines[] = '- php index.php console email_test you@example.com';
        $lines[] = '- php index.php console stripe_health';
        $lines[] = '';

        response(implode(PHP_EOL, $lines));
    }

    /**
     * Show help information about the console capabilities.
     *
     * Use this method to see the available commands.
     *
     * Usage:
     *
     * php index.php console help
     */
    public function help(): void
    {
        $help = [
            '',
            'Easy!Appointments ' . config('version'),
            '',
            'Usage:',
            '',
            '⇾ php index.php console [command] [arguments]',
            '',
            'Commands:',
            '',
            '⇾ php index.php console migrate',
            '⇾ php index.php console migrate fresh',
            '⇾ php index.php console migrate up',
            '⇾ php index.php console migrate down',
            '⇾ php index.php console seed',
            '⇾ php index.php console install',
            '⇾ php index.php console backup',
            '⇾ php index.php console sync',
            '⇾ php index.php console remind',
            '⇾ php index.php console remind 24 60',
            '⇾ php index.php console email_test you@example.com',
            '⇾ php index.php console stripe_health',
            '⇾ php index.php console integrations_check',
            '',
            '',
        ];

        response(implode(PHP_EOL, $help));
    }
}
