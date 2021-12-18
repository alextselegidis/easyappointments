<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

/**
 * Consents model.
 *
 * Handles all the database operations of the consent resource.
 *
 * @package Models
 */
class Consents_model extends EA_Model {
    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];
    
    /**
     * Save (insert or update) a consent.
     *
     * @param array $consent Associative array with the consent data.
     *
     * @return int Returns the consent ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $consent): int
    {
        $this->validate($consent);

        if (empty($consent['id']))
        {
            return $this->insert($consent);
        }
        else
        {
            return $this->update($consent);
        }
    }


    /**
     * Validate the consent data.
     *
     * @param array $consent Associative array with the consent data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $consent)
    {
        if (
            empty($consent['first_name'])
            || empty($consent['last_name'])
            || empty($consent['email'])
            || empty($consent['ip'])
            || empty($consent['type'])
        )
        {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($consent, TRUE));
        }
    }

    /**
     * Insert a new consent into the database.
     *
     * @param array $consent Associative array with the consent data.
     *
     * @return int Returns the consent ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $consent): int
    {
        $consent['created'] = date('Y-m-d H:i:s');
        $consent['modified'] = date('Y-m-d H:i:s');

        if ( ! $this->db->insert('consents', $consent))
        {
            throw new RuntimeException('Could not insert consent.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing consent.
     *
     * @param array $consent Associative array with the consent data.
     *
     * @return int Returns the consent ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $consent): int
    {
        $consent['modified'] = date('Y-m-d H:i:s');

        if ( ! $this->db->update('consents', $consent, ['id' => $consent['id']]))
        {
            throw new RuntimeException('Could not update consent.');
        }

        return $consent['id'];
    }

    /**
     * Remove an existing consent from the database.
     *
     * @param int $consent_id Consent ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $consent_id)
    {
        if ( ! $this->db->delete('consents', ['id' => $consent_id]))
        {
            throw new RuntimeException('Could not delete consent.');
        }
    }

    /**
     * Get a specific consent from the database.
     *
     * @param int $consent_id The ID of the record to be returned.
     *
     * @return array Returns an array with the consent data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $consent_id): array
    {
        if ( ! $this->db->get_where('consents', ['id' => $consent_id])->num_rows())
        {
            throw new InvalidArgumentException('The provided consent ID was not found in the database: ' . $consent_id);
        }

        $consent = $this->db->get_where('consents', ['id' => $consent_id])->row_array();
        
        $this->cast($consent); 
        
        return $consent;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $consent_id Consent ID.
     * @param string $field Name of the value to be returned.
     *
     * @return string Returns the selected consent value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $consent_id, string $field): string
    {
        if (empty($field))
        {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($consent_id))
        {
            throw new InvalidArgumentException('The consent ID argument cannot be empty.');
        }

        // Check whether the consent exists.
        $query = $this->db->get_where('consents', ['id' => $consent_id]);

        if ( ! $query->num_rows())
        {
            throw new InvalidArgumentException('The provided consent ID was not found in the database: ' . $consent_id);
        }

        // Check if the required field is part of the consent data.
        $consent = $query->row_array();
        
        $this->cast($consent);

        if ( ! array_key_exists($field, $consent))
        {
            throw new InvalidArgumentException('The requested field was not found in the consent data: ' . $field);
        }

        return $consent[$field];
    }

    /**
     * Get all consents that match the provided criteria.
     *
     * @param array|string $where Where conditions.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of consents.
     */
    public function get($where = NULL, int $limit = NULL, int $offset = NULL, string $order_by = NULL): array
    {
        if ($where !== NULL)
        {
            $this->db->where($where);
        }

        if ($order_by !== NULL)
        {
            $this->db->order_by($order_by);
        }

        $consents = $this->db->get('consents', $limit, $offset)->result_array();
        
        foreach($consents as &$consent)
        {
            $this->cast($consent); 
        }
        
        return $consents; 
    }

    /**
     * Get the query builder interface, configured for use with the consents table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('consents');
    }

    /**
     * Search consents by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of consents.
     */
    public function search(string $keyword, int $limit = NULL, int $offset = NULL, string $order_by = NULL): array
    {
        $consents = $this
            ->db
            ->select()
            ->from('consents')
            ->like('first_name', $keyword)
            ->or_like('last_name', $keyword)
            ->or_like('email', $keyword)
            ->or_like('ip', $keyword)
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by)
            ->get()
            ->result_array();

        foreach($consents as &$consent)
        {
            $this->cast($consent);
        }

        return $consents;
    }

    /**
     * Load related resources to a consent.
     *
     * @param array $consent Associative array with the consent data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$consent, array $resources)
    {
        // Consents do not currently have any related resources. 
    }
}
