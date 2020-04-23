<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Appointments Model
 *
 * @property CI_DB_query_builder db
 * @property CI_Loader load
 *
 * @package Models
 */
class Appointments_Model extends CI_Model {
    /**
     * Add an appointment record to the database.
     *
     * This method adds a new appointment to the database. If the appointment doesn't exists it is going to be inserted,
     * otherwise the record is going to be updated.
     *
     * @param array $appointment Associative array with the appointment data. Each key has the same name with the
     * database fields.
     *
     * @return int Returns the appointments id.
     * @throws Exception
     */
    public function add($appointment)
    {
        // Validate the appointment data before doing anything.
        $this->validate($appointment);

        // Perform insert() or update() operation.
        if ( ! isset($appointment['id']))
        {
            $appointment['id'] = $this->insert($appointment);
        }
        else
        {
            $this->update($appointment);
        }

        return $appointment['id'];
    }

    /**
     * Validate appointment data before the insert or update operations are executed.
     *
     * @param array $appointment Contains the appointment data.
     *
     * @return bool Returns the validation result.
     *
     * @throws Exception If appointment validation fails.
     */
    public function validate($appointment)
    {
        $this->load->helper('data_validation');

        // If a appointment id is given, check whether the record exists
        // in the database.
        if (isset($appointment['id']))
        {
            $num_rows = $this->db->get_where('appointments',
                ['id' => $appointment['id']])->num_rows();
            if ($num_rows == 0)
            {
                throw new Exception('Provided appointment id does not exist in the database.');
            }
        }

        // Check if appointment dates are valid.
        if ( ! validate_mysql_datetime($appointment['start_datetime']))
        {
            throw new Exception('Appointment start datetime is invalid.');
        }

        if ( ! validate_mysql_datetime($appointment['end_datetime']))
        {
            throw new Exception('Appointment end datetime is invalid.');
        }

        // Check if the provider's id is valid.
        $num_rows = $this->db
            ->select('*')
            ->from('users')
            ->join('roles', 'roles.id = ea_users.id_roles', 'inner')
            ->where('users.id', $appointment['id_users_provider'])
            ->where('roles.slug', DB_SLUG_PROVIDER)
            ->get()->num_rows();
        if ($num_rows == 0)
        {
            throw new Exception('Appointment provider id is invalid.');
        }

        if ($appointment['is_unavailable'] == FALSE)
        {
            // Check if the customer's id is valid.
            $num_rows = $this->db
                ->select('*')
                ->from('users')
                ->join('roles', 'roles.id = ea_users.id_roles', 'inner')
                ->where('users.id', $appointment['id_users_customer'])
                ->where('roles.slug', DB_SLUG_CUSTOMER)
                ->get()->num_rows();
            if ($num_rows == 0)
            {
                throw new Exception('Appointment customer id is invalid.');
            }

            // Check if the service id is valid.
            $num_rows = $this->db->get_where('services',
                ['id' => $appointment['id_services']])->num_rows();
            if ($num_rows == 0)
            {
                throw new Exception('Appointment service id is invalid.');
            }
        }

        return TRUE;
    }

    /**
     * Insert a new appointment record to the database.
     *
     * @param array $appointment Associative array with the appointment's data. Each key has the same name with the
     * database fields.
     *
     * @return int Returns the id of the new record.
     *
     * @throws Exception If appointment record could not be inserted.
     */
    protected function insert($appointment)
    {
        $appointment['book_datetime'] = date('Y-m-d H:i:s');
        $appointment['hash'] = $this->generate_hash();

        if ( ! $this->db->insert('appointments', $appointment))
        {
            throw new Exception('Could not insert appointment record.');
        }

        return (int)$this->db->insert_id();
    }

    /**
     * Generate a unique hash for the given appointment data.
     *
     * This method uses the current date-time to generate a unique hash string that is later used to identify this
     * appointment. Hash is needed when the email is send to the user with an edit link.
     *
     * @return string Returns the unique appointment hash.
     */
    public function generate_hash()
    {
        $current_date = new DateTime();
        return md5($current_date->getTimestamp());
    }

    /**
     * Update an existing appointment record in the database.
     *
     * The appointment data argument should already include the record ID in order to process the update operation.
     *
     * @param array $appointment Associative array with the appointment's data. Each key has the same name with the
     * database fields.
     *
     * @throws Exception If appointment record could not be updated.
     */
    protected function update($appointment)
    {
        $this->db->where('id', $appointment['id']);
        if ( ! $this->db->update('appointments', $appointment))
        {
            throw new Exception('Could not update appointment record.');
        }
    }

    /**
     * Check if a particular appointment record already exists.
     *
     * This method checks whether the given appointment already exists in the database. It doesn't search with the id,
     * but by using the following fields: "start_datetime", "end_datetime", "id_users_provider", "id_users_customer",
     * "id_services".
     *
     * @param array $appointment Associative array with the appointment's data. Each key has the same name with the
     * database fields.
     *
     * @return bool Returns whether the record exists or not.
     *
     * @throws Exception If appointment fields are missing.
     */
    public function exists($appointment)
    {
        if ( ! isset($appointment['start_datetime'])
            || ! isset($appointment['end_datetime'])
            || ! isset($appointment['id_users_provider'])
            || ! isset($appointment['id_users_customer'])
            || ! isset($appointment['id_services']))
        {
            throw new Exception('Not all appointment field values are provided: '
                . print_r($appointment, TRUE));
        }

        $num_rows = $this->db->get_where('appointments', [
            'start_datetime' => $appointment['start_datetime'],
            'end_datetime' => $appointment['end_datetime'],
            'id_users_provider' => $appointment['id_users_provider'],
            'id_users_customer' => $appointment['id_users_customer'],
            'id_services' => $appointment['id_services'],
        ])
            ->num_rows();

        return ($num_rows > 0) ? TRUE : FALSE;
    }

    /**
     * Find the database id of an appointment record.
     *
     * The appointment data should include the following fields in order to get the unique id from the database:
     * "start_datetime", "end_datetime", "id_users_provider", "id_users_customer", "id_services".
     *
     * IMPORTANT: The record must already exists in the database, otherwise an exception is raised.
     *
     * @param array $appointment Array with the appointment data. The keys of the array should have the same names as
     * the db fields.
     *
     * @return int Returns the db id of the record that matches the appointment data.
     *
     * @throws Exception If appointment could not be found.
     */
    public function find_record_id($appointment)
    {
        $this->db->where([
            'start_datetime' => $appointment['start_datetime'],
            'end_datetime' => $appointment['end_datetime'],
            'id_users_provider' => $appointment['id_users_provider'],
            'id_users_customer' => $appointment['id_users_customer'],
            'id_services' => $appointment['id_services']
        ]);

        $result = $this->db->get('appointments');

        if ($result->num_rows() == 0)
        {
            throw new Exception('Could not find appointment record id.');
        }

        return $result->row()->id;
    }

    /**
     * Delete an existing appointment record from the database.
     *
     * @param int $appointment_id The record id to be deleted.
     *
     * @return bool Returns the delete operation result.
     *
     * @throws Exception If $appointment_id argument is invalid.
     */
    public function delete($appointment_id)
    {
        if ( ! is_numeric($appointment_id))
        {
            throw new Exception('Invalid argument type $appointment_id (value:"' . $appointment_id . '")');
        }

        $num_rows = $this->db->get_where('appointments', ['id' => $appointment_id])->num_rows();

        if ($num_rows == 0)
        {
            return FALSE; // Record does not exist.
        }

        $this->db->where('id', $appointment_id);
        return $this->db->delete('appointments');
    }

    /**
     * Get a specific row from the appointments table.
     *
     * @param int $appointment_id The record's id to be returned.
     *
     * @return array Returns an associative array with the selected record's data. Each key has the same name as the
     * database field names.
     *
     * @throws Exception If $appointment_id argumnet is invalid.
     */
    public function get_row($appointment_id)
    {
        if ( ! is_numeric($appointment_id))
        {
            throw new Exception('Invalid argument given. Expected integer for the $appointment_id: '
                . $appointment_id);
        }

        $appointment = $this->db->get_where('appointments', ['id' => $appointment_id])->row_array();

        $this->load->model('timezones_model');

        $appointment = $this->timezones_model->convert_event_timezone($appointment);

        return $appointment;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param string $field_name The field name of the value to be returned.
     * @param int $appointment_id The selected record's id.
     *
     * @return string Returns the records value from the database.
     *
     * @throws Exception If $appointment_id argument is invalid.
     * @throws Exception If $field_name argument is invalid.
     * @throws Exception If requested appointment record was not found.
     * @throws Exception If requested field name does not exist.
     */
    public function get_value($field_name, $appointment_id)
    {
        if ( ! is_numeric($appointment_id))
        {
            throw new Exception('Invalid argument given, expected integer for the $appointment_id: '
                . $appointment_id);
        }

        if ( ! is_string($field_name))
        {
            throw new Exception('Invalid argument given, expected  string for the $field_name: ' . $field_name);
        }

        if ($this->db->get_where('appointments', ['id' => $appointment_id])->num_rows() == 0)
        {
            throw new Exception('The record with the provided id '
                . 'does not exist in the database: ' . $appointment_id);
        }

        $row_data = $this->db->get_where('appointments', ['id' => $appointment_id])->row_array();

        if ( ! isset($row_data[$field_name]))
        {
            throw new Exception('The given field name does not exist in the database: ' . $field_name);
        }

        $this->load->model('timezones_model');

        $row_data = $this->timezones_model->convert_event_timezone($row_data);

        return $row_data[$field_name];
    }

    /**
     * Get all, or specific records from appointment's table.
     *
     * Example:
     *
     * $this->Model->getBatch('id = ' . $recordId);
     *
     * @param mixed|null $where (OPTIONAL) The WHERE clause of the query to be executed. DO NOT INCLUDE 'WHERE'
     * KEYWORD.
     * @param mixed|null $order_by
     * @param int|null $limit
     * @param int|null $offset
     * @param bool $aggregates (OPTIONAL) Defines whether to add aggregations or not.
     *
     * @return array Returns the rows from the database.
     */
    public function get_batch($where = NULL, $order_by = NULL, $limit = NULL, $offset = NULL, $aggregates = FALSE)
    {
        if ($where !== NULL)
        {
            $this->db->where($where);
        }

        if ($order_by)
        {
            $this->db->order_by($order_by);
        }

        $appointments = $this->db->get('appointments', $limit, $offset)->result_array();

        $this->load->model('timezones_model');

        foreach ($appointments as &$appointment)
        {
            $appointment = $this->timezones_model->convert_event_timezone($appointment);

            if ($aggregates)
            {
                $appointment = $this->get_aggregates($appointment);
            }
        }

        return $appointments;
    }

    /**
     * Get the aggregates of an appointment.
     *
     * @param array $appointment Appointment data.
     *
     * @return array Returns the appointment with the aggregates.
     */
    private function get_aggregates(array $appointment)
    {
        $appointment['service'] = $this->db->get_where('services',
            ['id' => $appointment['id_services']])->row_array();
        $appointment['provider'] = $this->db->get_where('users',
            ['id' => $appointment['id_users_provider']])->row_array();
        $appointment['customer'] = $this->db->get_where('users',
            ['id' => $appointment['id_users_customer']])->row_array();
        return $appointment;
    }

    /**
     * Inserts or updates an unavailable period record in the database.
     *
     * @param array $unavailable Contains the unavailable data.
     *
     * @return int Returns the record id.
     *
     * @throws Exception If unavailability validation fails.
     * @throws Exception If provider record could not be found in database.
     */
    public function add_unavailable($unavailable)
    {
        // Validate period
        $start = strtotime($unavailable['start_datetime']);
        $end = strtotime($unavailable['end_datetime']);
        if ($start > $end)
        {
            throw new Exception('Unavailable period start must be prior to end.');
        }

        // Validate provider record
        $where_clause = [
            'id' => $unavailable['id_users_provider'],
            'id_roles' => $this->db->get_where('roles', ['slug' => DB_SLUG_PROVIDER])->row()->id
        ];

        if ($this->db->get_where('users', $where_clause)->num_rows() == 0)
        {
            throw new Exception('Provider id was not found in database.');
        }

        // Add record to database (insert or update).
        if ( ! isset($unavailable['id']))
        {
            $unavailable['book_datetime'] = date('Y-m-d H:i:s');
            $unavailable['is_unavailable'] = TRUE;

            $this->db->insert('appointments', $unavailable);
            $unavailable['id'] = $this->db->insert_id();
        }
        else
        {
            $this->db->where(['id' => $unavailable['id']]);
            $this->db->update('appointments', $unavailable);
        }

        return $unavailable['id'];
    }

    /**
     * Delete an unavailable period.
     *
     * @param int $unavailable_id Record id to be deleted.
     *
     * @return bool Returns the delete operation result.
     *
     * @throws Exception If $unavailable_id argument is invalid.
     */
    public function delete_unavailable($unavailable_id)
    {
        if ( ! is_numeric($unavailable_id))
        {
            throw new Exception('Invalid argument type $unavailable_id: ' . $unavailable_id);
        }

        $num_rows = $this->db->get_where('appointments', ['id' => $unavailable_id])->num_rows();

        if ($num_rows == 0)
        {
            return FALSE; // Record does not exist.
        }

        $this->db->where('id', $unavailable_id);

        return $this->db->delete('appointments');
    }

    /**
     * Clear google sync IDs from appointment record.
     *
     * @param int $provider_id The appointment provider record id.
     *
     * @throws Exception If $provider_id argument is invalid.
     */
    public function clear_google_sync_ids($provider_id)
    {
        if ( ! is_numeric($provider_id))
        {
            throw new Exception('Invalid argument type $provider_id: ' . $provider_id);
        }

        $this->db->update('appointments', ['id_google_calendar' => NULL],
            ['id_users_provider' => $provider_id]);
    }

    /**
     * Get appointment count for the provided start datetime.
     *
     * @param int $service_id Selected service ID.
     * @param string $selected_date Selected date string.
     * @param string $hour Selected hour string.
     *
     * @return int Returns the appointment number at the selected start time.
     */
    public function appointment_count_for_hour($service_id, $selected_date, $hour)
    {
        return $this->db->get_where('appointments', [
            'id_services' => $service_id,
            'start_datetime' => date('Y-m-d H:i:s', strtotime($selected_date . ' ' . $hour . ':00'))
        ])->num_rows();
    }

    /**
     * Returns the attendants number for selection period.
     *
     * @param DateTime $slot_start When the slot starts
     * @param DateTime $slot_end When the slot ends.
     * @param int $service_id Selected service ID.
     *
     * @return int Returns the number of attendants for selected time period.
     */
    public function get_attendants_number_for_period(DateTime $slot_start, DateTime $slot_end, $service_id)
    {
        return (int)$this->db
            ->select('count(*) AS attendants_number')
            ->from('appointments')
            ->group_start()
            ->where('start_datetime <=', $slot_start->format('Y-m-d H:i:s'))
            ->where('end_datetime >', $slot_start->format('Y-m-d H:i:s'))
            ->group_end()
            ->or_group_start()
            ->where('start_datetime <', $slot_end->format('Y-m-d H:i:s'))
            ->where('end_datetime >=', $slot_end->format('Y-m-d H:i:s'))
            ->group_end()
            ->where('id_services', $service_id)
            ->get()
            ->row()
            ->attendants_number;
    }
}
