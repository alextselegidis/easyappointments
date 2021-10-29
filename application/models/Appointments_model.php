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
 * Appointments model.
 *
 * @package Models
 */
class Appointments_model extends EA_Model {
    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_unavailable' => 'boolean',
        'id_users_provider' => 'integer',
        'id_users_customer' => 'integer',
        'id_services' => 'integer',
    ];

    /**
     * Save (insert or update) an appointment.
     *
     * @param array $appointment Associative array with the appointment data.
     *
     * @return int Returns the appointment ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $appointment): int
    {
        $this->validate($appointment);

        if (empty($appointment['id']))
        {
            return $this->insert($appointment);
        }
        else
        {
            return $this->update($appointment);
        }
    }

    /**
     * Validate the appointment data.
     *
     * @param array $appointment Associative array with the appointment data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $appointment)
    {
        // If an appointment ID is provided then check whether the record really exists in the database.
        if ( ! empty($appointment['id']))
        {
            $count = $this->db->get_where('appointments', ['id' => $appointment['id']])->num_rows();

            if ( ! $count)
            {
                throw new InvalidArgumentException('The provided appointment ID does not exist in the database: ' . $appointment['id']);
            }
        }

        // Make sure all required fields are provided. 
        if (
            empty($appointment['start_datetime'])
            || empty($appointment['end_datetime'])
            || empty($appointment['id_services'])
            || empty($appointment['id_users_provider'])
            || empty($appointment['id_users_customer'])
        )
        {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($appointment, TRUE));
        }

        // Make sure that the provided appointment date time values are valid.
        if ( ! validate_datetime($appointment['start_datetime']))
        {
            throw new InvalidArgumentException('The appointment start date time is invalid.');
        }

        if ( ! validate_datetime($appointment['end_datetime']))
        {
            throw new InvalidArgumentException('The appointment end date time is invalid.');
        }

        // Make the appointment lasts longer than the minimum duration (in minutes).
        $diff = (strtotime($appointment['end_datetime']) - strtotime($appointment['start_datetime'])) / 60;

        if ($diff < EVENT_MINIMUM_DURATION)
        {
            throw new InvalidArgumentException('The appointment duration cannot be less than ' . EVENT_MINIMUM_DURATION . ' minutes.');
        }

        // Make sure the provider ID really exists in the database. 
        $count = $this
            ->db
            ->select()
            ->from('users')
            ->join('roles', 'roles.id = users.id_roles', 'inner')
            ->where('users.id', $appointment['id_users_provider'])
            ->where('roles.slug', DB_SLUG_PROVIDER)
            ->get()
            ->num_rows();

        if ( ! $count)
        {
            throw new InvalidArgumentException('The appointment provider ID was not found in the database: ' . $appointment['id_users_provider']);
        }

        if ( ! filter_var($appointment['is_unavailable'], FILTER_VALIDATE_BOOLEAN))
        {
            // Make sure the customer ID really exists in the database. 
            $count = $this
                ->db
                ->select()
                ->from('users')
                ->join('roles', 'roles.id = users.id_roles', 'inner')
                ->where('users.id', $appointment['id_users_customer'])
                ->where('roles.slug', DB_SLUG_CUSTOMER)
                ->get()
                ->num_rows();

            if ( ! $count)
            {
                throw new InvalidArgumentException('The appointment customer ID was not found in the database: ' . $appointment['id_users_customer']);
            }

            // Make sure the service ID really exists in the database. 
            $count = $this->db->get_where('services', ['id' => $appointment['id_services']])->num_rows();

            if ( ! $count)
            {
                throw new InvalidArgumentException('Appointment service id is invalid.');
            }
        }
    }

    /**
     * Insert a new appointment into the database.
     *
     * @param array $appointment Associative array with the appointment data.
     *
     * @return int Returns the appointment ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $appointment): int
    {
        $appointment['book_datetime'] = date('Y-m-d H:i:s');
        $appointment['hash'] = random_string('alnum', 12);

        if ( ! $this->db->insert('appointments', $appointment))
        {
            throw new RuntimeException('Could not insert appointment.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing appointment.
     *
     * @param array $appointment Associative array with the appointment data.
     *
     * @return int Returns the appointment ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $appointment): int
    {
        $this->db->where('id', $appointment['id']);

        if ( ! $this->db->update('appointments', $appointment, ['id' => $appointment['id']]))
        {
            throw new RuntimeException('Could not update appointment record.');
        }

        return $appointment['id'];
    }

    /**
     * Remove an existing appointment from the database.
     *
     * @param int $appointment_id Appointment ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $appointment_id)
    {
        if ( ! $this->db->delete('users', ['id' => $appointment_id]))
        {
            throw new RuntimeException('Could not delete appointment.');
        }
    }

    /**
     * Get a specific appointment from the database.
     *
     * @param int $appointment_id The ID of the record to be returned.
     *
     * @return array Returns an array with the appointment data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $appointment_id): array
    {
        if ( ! $this->db->get_where('appointments', ['id' => $appointment_id])->num_rows())
        {
            throw new InvalidArgumentException('The provided appointment ID was not found in the database: ' . $appointment_id);
        }

        $appointment = $this->db->get_where('appointments', ['id' => $appointment_id])->row_array();

        $this->cast($appointment);

        return $appointment;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $appointment_id Appointment ID.
     * @param string $field Name of the value to be returned.
     *
     * @return string Returns the selected appointment value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $appointment_id, string $field): string
    {
        if (empty($field))
        {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($appointment_id))
        {
            throw new InvalidArgumentException('The appointment ID argument cannot be empty.');
        }

        // Check whether the appointment exists.
        $query = $this->db->get_where('appointments', ['id' => $appointment_id]);

        if ( ! $query->num_rows())
        {
            throw new InvalidArgumentException('The provided appointment ID was not found in the database: ' . $appointment_id);
        }

        // Check if the required field is part of the appointment data.
        $appointment = $query->row_array();

        $this->cast($appointment);

        if ( ! array_key_exists($field, $appointment))
        {
            throw new InvalidArgumentException('The requested field was not found in the appointment data: ' . $field);
        }

        return $appointment[$field];
    }

    /**
     * Get all appointments that match the provided criteria.
     *
     * @param array|string $where Where conditions.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of appointments.
     */
    public function get($where = NULL, int $limit = NULL, int $offset = NULL, string $order_by = NULL): array
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

        foreach ($appointments as &$appointment)
        {
            $this->cast($appointment);
        }

        return $appointments;
    }

    /**
     * Save (insert or update) an unavailable.
     *
     * @param array $unavailable Associative array with the unavailable data.
     *
     * @return int Returns the unavailable ID.
     *
     * @throws InvalidArgumentException
     */
    public function save_unavailable(array $unavailable): int
    {
        // Make sure the start date time is before the end date time.
        $start = strtotime($unavailable['start_datetime']);

        $end = strtotime($unavailable['end_datetime']);

        if ($start > $end)
        {
            throw new InvalidArgumentException('Unavailable period start date time must be before the end date time.');
        }

        // Make sure the provider ID really exists in the database.
        $role = $this->db->get_where('roles', ['slug' => DB_SLUG_PROVIDER])->row_array();

        $count = $this->db->get_where('users', [
            'id' => $unavailable['id_users_provider'],
            'id_roles' => $role['id'],
        ])->num_rows();

        if ( ! $count)
        {
            throw new InvalidArgumentException('Provider id was not found in database.');
        }

        if (empty($unavailable['id']))
        {
            $unavailable['book_datetime'] = date('Y-m-d H:i:s');

            $unavailable['is_unavailable'] = TRUE;

            $this->db->insert('appointments', $unavailable);

            return $this->db->insert_id();
        }
        else
        {
            return $this->db->update('appointments', $unavailable, ['id' => $unavailable['id']]);
        }
    }

    /**
     * Remove all the Google Calendar event IDs from appointment records.
     *
     * @param int $provider_id Matching provider ID.
     */
    public function clear_google_sync_ids(int $provider_id)
    {
        $this->db->update('appointments', ['id_google_calendar' => NULL], ['id_users_provider' => $provider_id]);
    }

    /**
     * Get the attendants number for the requested period.
     *
     * @param DateTime $start Period start.
     * @param DateTime $end Period end.
     * @param int $service_id Service ID.
     * @param int $provider_id Provider ID.
     * @param int|null $exclude_appointment_id Exclude an appointment from the result set.
     *
     * @return int Returns the number of appointments that match the provided criteria.
     */
    public function get_attendants_number_for_period(DateTime $start, DateTime $end, int $service_id, int $provider_id, int $exclude_appointment_id = NULL): int
    {
        if ($exclude_appointment_id)
        {
            $this->db->where('id !=', $exclude_appointment_id);
        }

        $result = $this
            ->db
            ->select('count(*) AS attendants_number')
            ->from('appointments')
            ->group_start()
            ->group_start()
            ->where('start_datetime <=', $start->format('Y-m-d H:i:s'))
            ->where('end_datetime >', $start->format('Y-m-d H:i:s'))
            ->group_end()
            ->or_group_start()
            ->where('start_datetime <', $end->format('Y-m-d H:i:s'))
            ->where('end_datetime >=', $end->format('Y-m-d H:i:s'))
            ->group_end()
            ->group_end()
            ->where('id_services', $service_id)
            ->where('id_users_provider', $provider_id)
            ->get()
            ->row_array();

        return $result['attendants_number'];
    }

    /**
     *
     * Returns the number of the other service attendants number for the provided time slot.
     *
     * @param DateTime $start Period start.
     * @param DateTime $end Period end.
     * @param int $service_id Service ID.
     * @param int $provider_id Provider ID.
     * @param int|null $exclude_appointment_id Exclude an appointment from the result set.
     *
     * @return int Returns the number of appointments that match the provided criteria.
     */
    public function get_other_service_attendants_number(DateTime $start, DateTime $end, int $service_id, int $provider_id, int $exclude_appointment_id = NULL): int
    {
        if ($exclude_appointment_id)
        {
            $this->db->where('id !=', $exclude_appointment_id);
        }

        $result = $this
            ->db
            ->select('count(*) AS attendants_number')
            ->from('appointments')
            ->group_start()
            ->group_start()
            ->where('start_datetime <=', $start->format('Y-m-d H:i:s'))
            ->where('end_datetime >', $start->format('Y-m-d H:i:s'))
            ->group_end()
            ->or_group_start()
            ->where('start_datetime <', $end->format('Y-m-d H:i:s'))
            ->where('end_datetime >=', $end->format('Y-m-d H:i:s'))
            ->group_end()
            ->group_end()
            ->where('id_services !=', $service_id)
            ->where('id_users_provider', $provider_id)
            ->get()
            ->row_array();

        return $result['attendants_number'];
    }

    /**
     * Get the query builder interface, configured for use with the appointments table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('appointments');
    }

    /**
     * Search appointments by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of appointments.
     */
    public function search(string $keyword, int $limit = NULL, int $offset = NULL, string $order_by = NULL): array
    {
        $appointments = $this
            ->db
            ->select()
            ->from('appointments')
            ->join('services', 'services.id = appointments.id_services', 'left')
            ->join('users AS providers', 'providers.id = appointments.id_users_provider', 'inner')
            ->join('users AS customers', 'customers.id = appointment.id_users_customer', 'left')
            ->like('appointments.start_datetime', $keyword)
            ->or_like('appointments.end_datetime', $keyword)
            ->or_like('appointments.location', $keyword)
            ->or_like('appointments.hash', $keyword)
            ->or_like('appointments.notes', $keyword)
            ->or_like('service.name', $keyword)
            ->or_like('service.description', $keyword)
            ->or_like('providers.first_name', $keyword)
            ->or_like('providers.last_name', $keyword)
            ->or_like('providers.email', $keyword)
            ->or_like('providers.phone_number', $keyword)
            ->or_like('customers.first_name', $keyword)
            ->or_like('customers.last_name', $keyword)
            ->or_like('customers.email', $keyword)
            ->or_like('customers.phone_number', $keyword)
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by)
            ->get()
            ->result_array();

        foreach ($appointments as &$appointment)
        {
            $this->cast($appointment);
        }

        return $appointments;
    }

    /**
     * Attach related resources to an appointment.
     *
     * @param array $appointment Associative array with the appointment data.
     * @param array $resources Resource names to be attached ("service", "provider", "customer" supported).
     *
     * @throws InvalidArgumentException
     */
    public function attach(array &$appointment, array $resources)
    {
        if (empty($appointment) || empty($resources))
        {
            return;
        }

        foreach ($resources as $resource)
        {
            switch ($resource)
            {
                case 'service':
                    $appointment['service'] = $this
                        ->db
                        ->get_where('services', [
                            'id' => $appointment['id_services']
                        ])
                        ->row_array();
                    break;

                case 'provider':
                    $appointment['provider'] = $this
                        ->db
                        ->get_where('users', [
                            'id' => $appointment['id_users_provider']
                        ])
                        ->row_array();
                    break;

                case 'customer':
                    $appointment['customer'] = $this
                        ->db
                        ->get_where('users', [
                            'id' => $appointment['id_users_customer']
                        ])
                        ->row_array();
                    break;

                default:
                    throw new InvalidArgumentException('The requested appointment relation is not supported: ' . $resource);
            }
        }
    }
}
