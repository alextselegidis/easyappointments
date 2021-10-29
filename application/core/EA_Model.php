<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Easy!Appointments model.
 *
 * @property CI_Benchmark $benchmark
 * @property CI_Cache $cache
 * @property CI_Calendar $calendar
 * @property CI_Config $config
 * @property CI_DB_forge $dbforge
 * @property CI_DB_query_builder $db
 * @property CI_DB_utility $dbutil
 * @property CI_Email $email
 * @property CI_Encrypt $encrypt
 * @property CI_Encryption $encryption
 * @property CI_Exceptions $exceptions
 * @property CI_Hooks $hooks
 * @property CI_Input $input
 * @property CI_Lang $lang
 * @property CI_Loader $load
 * @property CI_Log $log
 * @property CI_Migration $migration
 * @property CI_Output $output
 * @property CI_Profiler $profiler
 * @property CI_Router $router
 * @property CI_Security $security
 * @property CI_Session $session
 * @property CI_URI $uri
 * @property CI_Upload $upload
 *
 * @property Admins_model $admins_model
 * @property Appointments_model $appointments_model
 * @property Consents_model $consents_model
 * @property Customers_model $customers_model
 * @property Providers_model $providers_model
 * @property Roles_model $roles_model
 * @property Secretaries_model $secretaries_model
 * @property Service_categories_model $service_categories_model
 * @property Services_model $services_model
 * @property Settings_model $settings_model
 * @property Users_model $users_model
 *
 * @property Accounts $accounts
 * @property Availability $availability
 * @property Google_Sync $google_sync
 * @property Ics_file $ics_file
 * @property Notifications $notifications
 * @property Synchronization $synchronization
 * @property Timezones $timezones
 */
class EA_Model extends CI_Model {
    /**
     * EA_Model constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get a specific field value from the database.
     *
     * @param string $field Name of the value to be returned.
     * @param int $record_id Record ID.
     *
     * @return string Returns the selected record value from the database.
     *
     * @throws InvalidArgumentException
     *
     * @deprecated Since 1.5
     */
    public function get_value(string $field, int $record_id): string
    {
        return $this->value($field, $record_id);
    }

    /**
     * Get a specific record from the database.
     *
     * @param int $record_id The ID of the record to be returned.
     *
     * @return array Returns an array with the record data.
     *
     * @throws InvalidArgumentException
     *
     * @deprecated Since 1.5
     */
    public function get_row(int $record_id): array
    {
        return $this->find($record_id);
    }

    /**
     * Get all records that match the provided criteria.
     *
     * @param mixed|null $where
     * @param int|null $limit
     * @param int|null $offset
     * @param string|null $order_by
     *
     * @return array Returns an array of records.
     */
    public function get_batch($where = NULL, int $limit = NULL, int $offset = NULL, string $order_by = NULL): array
    {
        return $this->get($where, $limit, $offset, $order_by);
    }

    /**
     * Save (insert or update) a record.
     *
     * @param array $record Associative array with the record data.
     *
     * @return int Returns the record ID.
     *
     * @throws InvalidArgumentException
     */
    public function add(array $record): int
    {
        return $this->save($record);
    }
}
