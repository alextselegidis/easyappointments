<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Easy!Appointments model.
 *
 * @property EA_Benchmark $benchmark
 * @property EA_Cache $cache
 * @property EA_Calendar $calendar
 * @property EA_Config $config
 * @property EA_DB_forge $dbforge
 * @property EA_DB_query_builder $db
 * @property EA_DB_utility $dbutil
 * @property EA_Email $email
 * @property EA_Encrypt $encrypt
 * @property EA_Encryption $encryption
 * @property EA_Exceptions $exceptions
 * @property EA_Hooks $hooks
 * @property EA_Input $input
 * @property EA_Lang $lang
 * @property EA_Loader $load
 * @property EA_Log $log
 * @property EA_Migration $migration
 * @property EA_Output $output
 * @property EA_Profiler $profiler
 * @property EA_Router $router
 * @property EA_Security $security
 * @property EA_Session $session
 * @property EA_Upload $upload
 * @property EA_URI $uri
 */
class EA_Model extends CI_Model
{
    /**
     * @var array
     */
    protected array $casts = [];

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
        if (method_exists($this, 'value')) {
            return $this->value($field, $record_id);
        }

        throw new RuntimeException('The "get_value" is not defined in model: ', __CLASS__);
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
        if (method_exists($this, 'find')) {
            return $this->find($record_id);
        }

        throw new RuntimeException('The "get_row" is not defined in model: ', __CLASS__);
    }

    /**
     * Get all records that match the provided criteria.
     *
     * param array|string $where Where conditions
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of records.
     */
    public function get_batch($where = null, int $limit = null, int $offset = null, string $order_by = null): array
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

    /**
     * Easily cast fields to the correct data type.
     *
     * The integrated MySQL library will return all values as strings, something that can easily becoming problematic,
     * especially when comparing database values.
     *
     * @param array $record Record data.
     */
    public function cast(array &$record)
    {
        foreach ($this->casts as $attribute => $cast) {
            if (!isset($record[$attribute])) {
                continue;
            }

            switch ($cast) {
                case 'integer':
                    $record[$attribute] = (int) $record[$attribute];
                    break;

                case 'float':
                    $record[$attribute] = (float) $record[$attribute];
                    break;

                case 'boolean':
                    $record[$attribute] = (bool) $record[$attribute];
                    break;

                case 'string':
                    $record[$attribute] = (string) $record[$attribute];
                    break;

                default:
                    throw new RuntimeException('Unsupported cast type provided: ' . $cast);
            }
        }
    }

    /**
     * Only keep the requested fields of the provided record.
     *
     * @param array $record Record data (single or multiple records).
     * @param array $fields Requested field names.
     */
    public function only(array &$record, array $fields)
    {
        if (is_assoc($record)) {
            $record = array_fields($record, $fields);
        } else {
            foreach ($record as &$record_item) {
                $record_item = array_fields($record_item, $fields);
            }
        }
    }

    /**
     * Ensure a field exists in an array by using its value or NULL.
     *
     * @param array $record Record data (single or multiple records).
     * @param array $fields Requested field names.
     */
    public function optional(array &$record, array $fields)
    {
        if (is_assoc($record)) {
            foreach ($fields as $field => $default) {
                $record[$field] = $record[$field] ?? $default;
            }
        } else {
            foreach ($record as &$record_item) {
                foreach ($fields as $field => $default) {
                    $record_item[$field] = $record_item[$field] ?? $default;
                }
            }
        }
    }

    /**
     * Get the DB field name based on an API field name.
     *
     * @param string $api_field API resource key.
     *
     * @return string|null Returns the column field or null if non found.
     */
    public function db_field(string $api_field): ?string
    {
        return $this->api_resource[$api_field] ?? null;
    }
}
