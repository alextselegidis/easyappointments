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
    public function get_batch($where = null, ?int $limit = null, ?int $offset = null, ?string $order_by = null): array
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
                $record[$field] = $record[$field] ?? null ?: $default;
            }
        } else {
            foreach ($record as &$record_item) {
                foreach ($fields as $field => $default) {
                    $record_item[$field] = $record_item[$field] ?? null ?: $default;
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

    /**
     * Escape the order by statements in order to avoid SQL injection issues
     *
     * @param string|null $order_by
     *
     * @return string|null
     */
    function quote_order_by(?string $order_by): ?string
    {
        if (!$order_by) {
            return null;
        }

        $parts = explode(',', $order_by);
        $quoted_parts = [];

        foreach ($parts as $part) {
            $tokens = preg_split('/\s+/', trim($part));
            $column = array_shift($tokens); // first token is column
            $direction = strtoupper($tokens[0] ?? ''); // optional ASC/DESC

            // Add backticks (or quotes) around column name
            $column = '`' . str_replace('`', '', $column) . '`';

            if ($direction === 'ASC' || $direction === 'DESC') {
                $quoted_parts[] = $column . ' ' . $direction;
            } else {
                $quoted_parts[] = $column;
            }
        }

        return implode(', ', $quoted_parts);
    }

    /**
     * Sort column orders from table
     * 
     * @param string $table Table
     * @param string $column Column to sort (Default is row_order)
     */
    public function sort_column(string $table, string $column = 'row_order')
    {
        if (empty($table))
            throw new InvalidArgumentException("Table parameter must be defined");
        if (empty($column))
            throw new InvalidArgumentException("Column parameter must be defined");
        
            
        $rows = $this->db
                ->select(['id', $column])
                ->from($table)
                ->order_by($column)
                ->get()
                ->result_array();
        
        for($i=0; $i < count($rows); $i++)
        {
            if ($rows[$i][$column] != $i)
            {
                $rows[$i][$column] = $i;
                if (! $this->db->update($table, [$column => $i], [ 'id'=> $rows[$i]['id'] ]))
                {
                    throw new RuntimeException('Could not sort table '.$table . ": Db error");
                }
            }
            else {

            }
        }
    }


    /**
     * Inserts entry order after defined entry
     * 
     * @param string $table Table
     * @param array $entry Entity to insert
     * @param mixed|bool $afterId ID of entry where to insert at. Or false to set at beginning
     * @param string [$order_column] Ordering Column name (Default: row_order)
     * 
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */

    public function insert_row_order_after(string $table, array &$entry, $afterId, string $order_column='row_order')
    {
        if (empty($table))
            throw new InvalidArgumentException("Table parameter must be defined");
        if (empty($order_column))
            throw new InvalidArgumentException("Column parameter must be defined");

        if (!array_key_exists('id', $entry))
            throw new InvalidArgumentException('Entry does not contain ID column');
        if (!array_key_exists($order_column,$entry))
            throw new InvalidArgumentException('Entry does not contain sorting column'); 
            

        // Get position of desired entry:
        if (is_int($afterId) && $afterId > 0)
        {
            $position = $this->db->from($table)
                        ->select([$order_column])
                        ->where('id',$afterId)
                        ->get()
                        ->row_array();
            if ($position === false)
            {
                throw new InvalidArgumentException("Could not found service with ID $afterId");
            }
            $position = intval($position[$order_column]);
        }
        else {
            $position = FALSE;
        }
    
        if (! $this->insert_row_order($table,$entry,$position,$order_column))
        {
            throw new RuntimeException('Could not update order, database error');
        }

    }


    /**
     * Inserts entry to specified order in table
     * 
     * @param string $table Table
     * @param array $entry Entry, should be associative array containing columns 'Id' and desired $column sort value.
     * @param int|bool $position Position to set entry to. If set to False, it will be positioned to first.
     * @param string $column Column name that contains sorting data (default is 'row_order').
     * 
     * @return bool TRUE on success, FALSE on failure
     * @throws InvalidArgumentException
     */

     protected function insert_row_order(string $table, array &$entry, $position, string $column = 'row_order')
     {
        if (empty($table))
            throw new InvalidArgumentException("Table parameter must be defined");
        if (empty($column))
            throw new InvalidArgumentException("Column parameter must be defined");

        if (!array_key_exists('id', $entry))
            throw new InvalidArgumentException('Entry does not contain ID column');
        if (!array_key_exists($column,$entry))
            throw new InvalidArgumentException('Entry does not contain sorting column');

        
        if (is_int($position))
        {
            $newOr = $position +1;
        }
        else
        {
            $newOr = 0;
        }
        
        $this->db->update($table, [$column => $newOr ], [ 'id'=> $entry['id'] ]);
        

        $rows = $this->db
            ->select(['id', $column])
            ->from($table)
            ->where($column .'>=', $newOr)
            ->order_by($column)
            ->get()
            ->result_array();

        // Move entries after inserted:
        foreach ($rows as $row)
        {
            $id = $row['id'];
            if ($id == $entry['id'])
            {
                continue;
            }

            $newOr++;
            if ($this->db->update($table, [$column => $newOr], [ 'id'=> $row['id'] ]) === FALSE)
            { // Failed!
                return FALSE;
            }
            
        }

        // And fix empty gaps:
        $this->sort_column($table,$column);

        return TRUE;
     }
}
