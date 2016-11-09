<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed.');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Services Model
 *
 * @package Models
 */
class Services_Model extends CI_Model {
    /**
     * Add (insert or update) a service record on the database
     *
     * @param array $service Contains the service data. If an 'id' value is provided then
     * the record will be updated.
     *
     * @return numeric Returns the record id.
     */
    public function add($service) {
        $this->validate($service);

        if (!isset($service['id'])) {
            $service['id'] = $this->_insert($service);
        } else {
            $this->_update($service);
        }

        return intval($service['id']);
    }

    /**
     * Insert service record into database.
     *
     * @param array $service Contains the service record data.
     *
     * @return int Returns the new service record id.
     */
    protected function _insert($service) {
        if (!$this->db->insert('ea_services', $service)) {
            throw new Exception('Could not insert service record.');
        }
        return intval($this->db->insert_id());
    }

    /**
     * Update service record.
     *
     * @param array $service Contains the service data. The record id needs to be included in
     * the array.
     */
    protected function _update($service) {
       $this->db->where('id', $service['id']);
       if (!$this->db->update('ea_services', $service)) {
           throw new Exception('Could not update service record');
       }
    }

    /**
     * Checks whether an service record already exists in the database.
     *
     * @param array $service Contains the service data. Name, duration and price values
     * are mandatory in order to perform the checks.
     */
    public function exists($service) {
        if (!isset($service['name'])
                || !isset($service['duration'])
                || !isset($service['price'])) {
            throw new Exception('Not all service fields are provided in order to check whether '
                    . 'a service record already exists: ' . print_r($service, TRUE));
        }

        $num_rows = $this->db->get_where('ea_services', array(
            'name' => $service['name'],
            'duration' => $service['duration'],
            'price' => $service['price']
        ))->num_rows();

        return ($num_rows > 0) ? TRUE : FALSE;
    }

    /**
     * Validate a service record data.
     *
     * @param array $service Contains the service data.
     *
     * @return bool Returns the validation result.
     */
    public function validate($service) {
        $this->load->helper('data_validation');

        // If record id is provided we need to check whether the record exists
        // in the database.
        if (isset($service['id'])) {
            $num_rows = $this->db->get_where('ea_services', array('id' => $service['id']))
                    ->num_rows();
            if ($num_rows == 0) {
                throw new Exception('Provided service id does not exist in the database.');
            }
        }

        // Check if service category id is valid (only when present).
        if (!empty($service['id_service_categories'])) {
            $num_rows = $this->db->get_where('ea_service_categories',
                    array('id' => $service['id_service_categories']))->num_rows();
            if ($num_rows == 0) {
                throw new Exception('Provided service category id does not exist in database.');
            }
        }

        // Check for required fields
        if ($service['name'] == '') {
            throw new Exception('Not all required service fields where provided: '
                    . print_r($service, TRUE));
        }

        // Duration must be numeric
        if ($service['duration'] !== NULL) {
            if (!is_numeric($service['duration'])) {
                throw new Exception('Service duration is not numeric.');
            }
        }

        if ($service['price'] !== NULL) {
            if (!is_numeric($service['price'])) {
                throw new Exception('Service price is not numeric.');
            }
        }

        // Availabilities type must have the correct value. 
        if ($service['availabilities_type'] !== NULL && $service['availabilities_type'] !== AVAILABILITIES_TYPE_FLEXIBLE 
                && $service['availabilities_type'] !== AVAILABILITIES_TYPE_FIXED) {
            throw new Exception('Service availabilities type must be either ' . AVAILABILITIES_TYPE_FLEXIBLE 
                        . ' or ' . AVAILABILITIES_TYPE_FIXED . ' (given ' .  $service['availabilities_type'] . ')');
        }

        if ($service['attendants_number'] !== NULL && (!is_numeric($service['attendants_number']) 
                        || $service['attendants_number'] < 1)) {
            throw new Exception('Service attendants number must be numeric and greater or equal to one: ' 
                    . $service['attendants_number']);
        }

        return TRUE;
    }

    /**
     * Get the record id of an existing record.
     *
     * NOTICE! The record must exist, otherwise an exception will be raised.
     *
     * @param array $service Contains the service record data. Name, duration and price values
     * are mandatory for this method to complete.
     */
    public function find_record_id($service) {
        if (!isset($service['name'])
                || !isset($service['duration'])
                || !isset($service['price'])) {
            throw new Exception('Not all required fields where provided in order to find the '
                    . 'service record id.');
        }

        $result = $this->db->get_where('ea_services', array(
            'name' => $service['name'],
            'duration' => $service['duration'],
            'price' => $service['price']
        ));

        if ($result->num_rows() == 0) {
            throw new Exception('Could not find service record id');
        }

        return $result->row()->id;
    }

    /**
     * Delete a service record from database.
     *
     * @param numeric $service_id Record id to be deleted.
     *
     * @return bool Returns the delete operation result.
     */
    public function delete($service_id) {
        if (!is_numeric($service_id)) {
            throw new Exception('Invalid argument type $service_id (value:"' . $service_id . '"');
        }

        $num_rows = $this->db->get_where('ea_services', array('id' => $service_id))->num_rows();
        if ($num_rows == 0) {
            return FALSE; // Record does not exist
        }

        return $this->db->delete('ea_services', array('id' => $service_id));
    }

    /**
     * Get a specific row from the services db table.
     *
     * @param numeric $service_id The record's id to be returned.
     *
     * @return array Returns an associative array with the selected record's data. Each key
     * has the same name as the database field names.
     */
    public function get_row($service_id) {
        if (!is_numeric($service_id)) {
            throw new Exception('$service_id argument is not an numeric (value: "' . $service_id . '")');
        }
        return $this->db->get_where('ea_services', array('id' => $service_id))->row_array();
    }

    /**
     * Get a specific field value from the database.
     *
     * @param string $field_name The field name of the value to be
     * returned.
     * @param int $service_id The selected record's id.
     *
     * @return string Returns the records value from the database.
     */
    public function get_value($field_name, $service_id) {
        if (!is_numeric($service_id)) {
            throw new Exception('Invalid argument provided as $service_id: ' . $service_id);
        }

        if (!is_string($field_name)) {
            throw new Exception('$field_name argument is not a string: ' . $field_name);
        }

        if ($this->db->get_where('ea_services', array('id' => $service_id))->num_rows() == 0) {
            throw new Exception('The record with the $service_id argument does not exist in the database: ' . $service_id);
        }

        $row_data = $this->db->get_where('ea_services', array('id' => $service_id))->row_array();
        if (!isset($row_data[$field_name])) {
            throw new Exception('The given $field_name argument does not exist in the database: ' . $field_name);
        }

        $setting = $this->db->get_where('ea_services', array('id' => $service_id))->row_array();
        return $setting[$field_name];
    }

    /**
     * Get all, or specific records from service's table.
     *
     * @example $this->Model->getBatch('id = ' . $recordId);
     *
     * @param string $whereClause (OPTIONAL) The WHERE clause of
     * the query to be executed. DO NOT INCLUDE 'WHERE' KEYWORD.
     *
     * @return array Returns the rows from the database.
     */
    public function get_batch($where_clause = NULL) {
        if ($where_clause != NULL) {
            $this->db->where($where_clause);
        }

        return $this->db->get('ea_services')->result_array();
    }

    /**
     * This method returns all the services from the database.
     *
     * @return array Returns an object array with all the database services.
     */
    public function get_available_services() {
    	$this->db->distinct();
        return $this->db
        		->select('ea_services.*, ea_service_categories.name AS category_name, '
                        . 'ea_service_categories.id AS category_id')
        		->from('ea_services')
        		->join('ea_services_providers',
        				'ea_services_providers.id_services = ea_services.id', 'inner')
                ->join('ea_service_categories',
                        'ea_service_categories.id = ea_services.id_service_categories', 'left')
        		->get()->result_array();
    }

    /**
     * Add (insert or update) a service category record into database.
     *
     * @param array $category Contains the service category data.
     *
     * @return int Returns the record id.s
     */
    public function add_category($category) {
        if (!$this->validate_category($category)) {
            throw new Exception('Service category data are invalid.');
        }

        if (!isset($category['id'])) {
            $this->db->insert('ea_service_categories', $category);
            $category['id'] = $this->db->insert_id();
        } else {
            $this->db->where('id', $category['id']);
            $this->db->update('ea_service_categories', $category);
        }

        return intval($category['id']);
    }

    /**
     * Delete a service category record from the database.
     *
     * @param numeric $category_id Record id to be deleted.
     *
     * @return bool Returns the delete operation result.
     */
    public function delete_category($category_id) {
        if (!is_numeric($category_id)) {
            throw new Exception('Invalid argument given for $category_id: ' . $category_id);
        }

        $num_rows = $this->db->get_where('ea_service_categories', array('id' => $category_id))
                ->num_rows();
        if ($num_rows == 0) {
            throw new Exception('Service category record not found in database.');
        }

        $this->db->where('id', $category_id);
        return $this->db->delete('ea_service_categories');
    }

    /**
     * Get a service category record data.
     *
     * @param numeric $category_id Record id to be retrieved.
     *
     * @return array Returns the record data from the database.
     */
    public function get_category($category_id) {
        if (!is_numeric($category_id)) {
            throw new Exception('Invalid argument type given $category_id: ' . $category_id);
        }

        $result = $this->db->get_where('ea_service_categories', array('id' => $category_id));

        if ($result->num_rows() == 0) {
            throw new Exception('Service category record does not exist.');
        }

        return $result->row_array();
    }

    /**
     * Get all service category records from database.
     *
     * @return array Returns an array that contains all the service category records.
     */
    public function get_all_categories($where = '') {
        if ($where !== '') $this->db->where($where);
        return $this->db->get('ea_service_categories')->result_array();
    }

    /**
     * Validate a service category record data. This method must be used before adding
     * a service category record into database in order to secure the record integrity.
     *
     * @param array $category Contains the service category data.
     *
     * @return bool Returns the validation result.
     */
    public function validate_category($category) {
        try {
            // Required Fields
            if (!isset($category['name'])) {
                throw new Exception('Not all required fields where provided ');
            }

            if ($category['name'] == '' || $category['name'] == NULL) {
                throw new Exception('Required fields cannot be empty or null ($category: '
                        . print_r($category, TRUE) . ')');
            }

            return TRUE;
        } catch(Exception $exc) {
            return FALSE;
        }

    }
}

/* End of file services_model.php */
/* Location: ./application/models/services_model.php */
