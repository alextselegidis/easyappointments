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
 * Backend API controller.
 *
 * Handles the backend API related operations.
 *
 * @package Controllers
 *
 * @deprecated Since 1.5
 */
class Backend_api extends EA_Controller
{
    /**
     * Get Calendar Events
     */
    public function ajax_get_calendar_events()
    {
        redirect('calendar/get_calendar_appointments_for_table_view');
    }

    /**
     * Get the registered appointments for the given date period and record.
     */
    public function ajax_get_calendar_appointments()
    {
        redirect('calendar/get_calendar_appointments');
    }

    /**
     * Save appointment changes that are made from the backend calendar page.
     */
    public function ajax_save_appointment()
    {
        redirect('calendar/save_appointment');
    }

    /**
     * Delete appointment from the database.
     */
    public function ajax_delete_appointment()
    {
        redirect('calendar/delete_appointment');
    }

    /**
     * Disable a providers sync setting.
     */
    public function ajax_disable_provider_sync()
    {
        redirect('google/disable_provider_sync');
    }

    /**
     * Filter the customer records with the given key string.
     */
    public function ajax_filter_customers()
    {
        redirect('customers/search');
    }

    /**
     * Insert or update an unavailability.
     */
    public function ajax_save_unavailability()
    {
        redirect('calendar/save_unavailability');
    }

    /**
     * Delete an unavailability time period from database.
     */
    public function ajax_delete_unavailability()
    {
        redirect('calendar/delete_unavailability');
    }

    /**
     * Insert of update working plan exceptions to database.
     */
    public function ajax_save_working_plan_exception()
    {
        redirect('calendar/save_working_plan_exception');
    }

    /**
     * Delete a working plan exceptions time period to database.
     */
    public function ajax_delete_working_plan_exception()
    {
        redirect('calendar/delete_working_plan_exception');
    }

    /**
     * Save (insert or update) a customer record.
     */
    public function ajax_save_customer()
    {
        redirect('customers/create'); // or "customers/update"
    }

    /**
     * Delete customer from database.
     */
    public function ajax_delete_customer()
    {
        redirect('customers/destroy');
    }

    /**
     * Save (insert or update) service record.
     */
    public function ajax_save_service()
    {
        redirect('services/create'); // or "services/update"
    }

    /**
     * Delete service record from database.
     */
    public function ajax_delete_service()
    {
        redirect('services/destroy');
    }

    /**
     * Filter service records by given key string.
     */
    public function ajax_filter_services()
    {
        redirect('services/search');
    }

    /**
     * Save (insert or update) category record.
     */
    public function ajax_save_service_category()
    {
        redirect('categories/create'); // or "categories/update"
    }

    /**
     * Delete category record from database.
     */
    public function ajax_delete_service_category()
    {
        redirect('categories/destroy');
    }

    /**
     * Filter services categories with key string.
     */
    public function ajax_filter_service_categories()
    {
        redirect('categories/search');
    }

    /**
     * Filter admin records with string key.
     */
    public function ajax_filter_admins()
    {
        redirect('admins/search');
    }

    /**
     * Save (insert or update) admin record into database.
     */
    public function ajax_save_admin()
    {
        redirect('admins/create'); // or "admins/update"
    }

    /**
     * Delete an admin record from the database.
     */
    public function ajax_delete_admin()
    {
        redirect('admins/destroy');
    }

    /**
     * Filter provider records with string key.
     */
    public function ajax_filter_providers()
    {
        redirect('providers/search');
    }

    /**
     * Save (insert or update) a provider record into database.
     */
    public function ajax_save_provider()
    {
        redirect('providers/create'); // or "providers/update"
    }

    /**
     * Delete a provider record from the database.
     */
    public function ajax_delete_provider()
    {
        redirect('providers/destroy');
    }

    /**
     * Filter secretary records with string key.
     */
    public function ajax_filter_secretaries()
    {
        redirect('secretaries/search');
    }

    /**
     * Save (insert or update) a secretary record into database.
     */
    public function ajax_save_secretary()
    {
        redirect('secretaries/create'); // or "secretaries/update"
    }

    /**
     * Delete a secretary record from the database.
     */
    public function ajax_delete_secretary()
    {
        redirect('secretaries/destroy');
    }

    /**
     * Save a setting or multiple settings in the database.
     */
    public function ajax_save_settings()
    {
        redirect('general_settings/save'); // or "business_settings/save", "booking_settings/save", "legal_settings/save"
    }

    /**
     * This method checks whether the username already exists in the database.
     */
    public function ajax_validate_username()
    {
        redirect('account/validate_username');
    }

    /**
     * Change system language for current user.
     */
    public function ajax_change_language()
    {
        redirect('account/change_language');
    }

    /**
     * This method will return a list of the available Google Calendars.
     */
    public function ajax_get_google_calendars()
    {
        redirect('google/get_google_calendars');
    }

    /**
     * Select a specific google calendar for a provider.
     */
    public function ajax_select_google_calendar()
    {
        redirect('google/select_google_calendar');
    }

    /**
     * Apply global working plan to all providers.
     */
    public function ajax_apply_global_working_plan()
    {
        redirect('business_settings/apply_global_working_plan');
    }
}
