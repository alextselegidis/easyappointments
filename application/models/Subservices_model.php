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
 * Sub services model.
 *
 * Handles all the database operations of the sub service resource.
 *
 * @package Models
 */
class Subservices_model extends Services_model {
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'price' => 'float',
        'attendants_number' => 'integer',
        'is_private' => 'boolean',
        'id_service_categories' => 'integer',
        'parentservice' => 'integer',
    ];

    /**
     * Get all services references in the ea_subservices table as being subservices
     * 
     * @param bool $without_private Only include the public services.
     *
     * @return array Returns an array of services.
     */
    public function get_available_subservices(bool $without_private = false): array {
        if ($without_private) {
            $this->db->where('s1.is_private', false);
        }

        $services = $this->db
            ->distinct()
            ->select(
                's1.*, 1 AS is_subservice, "" AS service_category_name, 0 AS service_category_id, sub.service as parentservice',
            )
            ->from('services s1')
            ->join('subservices sub', 'sub.subservice = s1.id','inner')
            ->order_by('name ASC')
            ->get()
            ->result_array();
        
        foreach ($services as &$service) {
            $this->cast($service);
        }

        return $services;

    }
}