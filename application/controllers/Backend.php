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

/*
|------------------------------------------------------------------------------
| Deprecation Notice
|------------------------------------------------------------------------------
|
| This file is still in the project for backwards compatibility reasons and for 
| providing additional information on how to migrate your code to the latest   
| codebase state. 
|
| Visit the Easy!Appointments Developers website for more information:  
|
|   https://developers.easyappointments.org
|
| Since v1.5, the methods of this controller were ported to standalone controller 
| classes, that can both handle the page rendering and all asynchronous HTTP  
| requests. 
|
*/

/**
 * Backend controller.
 *
 * Handles the backend related operations.
 *
 * @package Controllers
 *
 * @deprecated Since 1.5
 */
class Backend extends EA_Controller
{
    /**
     * Display the calendar page.
     *
     * @param string $appointment_hash Appointment edit dialog will appear when the page loads (default '').
     */
    public function index(string $appointment_hash = ''): void
    {
        if (empty($appointment_hash)) {
            redirect('calendar');
        } else {
            redirect('calendar/reschedule/' . $appointment_hash);
        }
    }

    /**
     * Display the customers page.
     */
    public function customers(): void
    {
        redirect('customers');
    }

    /**
     * Display the services page.
     */
    public function services(): void
    {
        redirect('services');
    }

    /**
     * Display the users page.
     *
     * Notice: Since the "users" page is split into multiple pages (providers, secretaries, admins), this method will
     * redirect to "providers" page by default
     */
    public function users(): void
    {
        redirect('providers');
    }

    /**
     * Display settings page.
     *
     * Notice: Since the "settings" page is split into multiple pages (general, business, booking etc), this method will
     * redirect to "general" page by default.
     */
    public function settings(): void
    {
        redirect('general_settings');
    }

    /**
     * Display the update page.
     */
    public function update(): void
    {
        redirect('update');
    }
}
