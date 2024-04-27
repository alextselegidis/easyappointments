<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Recovery controller.
 *
 * Handles the recovery page functionality.
 *
 * @package Controllers
 */
class Recovery extends EA_Controller
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('accounts');
        $this->load->library('email_messages');
    }

    /**
     * Display the password recovery page.
     */
    public function index(): void
    {
        $company_name = setting('company_name');

        html_vars([
            'page_title' => lang('forgot_your_password'),
            'dest_url' => session('dest_url', site_url('backend')),
            'company_name' => $company_name,
        ]);

        $this->load->view('pages/recovery');
    }

    /**
     * Recover the user password and notify the user via email.
     */
    public function perform()
    {
        try {
            $username = request('username');

            if (empty($username)) {
                throw new InvalidArgumentException('No username value provided.');
            }

            $email = request('email');

            if (empty($email)) {
                throw new InvalidArgumentException('No email value provided.');
            }

            $new_password = $this->accounts->regenerate_password($username, $email);

            if ($new_password) {
                $settings = [
                    'company_name' => setting('company_name'),
                    'company_link' => setting('company_link'),
                    'company_email' => setting('company_email'),
                ];

                $this->email_messages->send_password($new_password, $email, $settings);
            }

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
