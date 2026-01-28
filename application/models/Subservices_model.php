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
        'service' => 'integer',
        'subservice' => 'integer',
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
            ->order_by('name ASC');

        $services = $services->get()
            ->result_array();
        
        foreach ($services as &$service) {
            $this->cast($service);
        }

        return $services;

    }

    public function getSubserviceIds(int $service_id): array
    {
        $services = $this->db->get_where('subservices', ['service' => $service_id])->result_array();
		$ids = [];
        foreach($services as $service) {
			array_push( $ids, (int) $service['subservice'] );
        }

        return $ids;
    }

    public function deleteSubservice(int $service_id, int $subservice_id): void 
    {
		$this->db->delete( 'subservices', [ 'service' => $service_id, 'subservice' => $subservice_id] );
    }

    public function deleteAllSubservicesByService(int $service_id): void 
    {
        $this->db->delete( 'subservices', [ 'service' => $service_id ] );
    }

    public function deleteAllSubservicesBySubService(int $subservice_id): void 
    {
        $this->db->delete( 'subservices', [ 'subservice' => $subservice_id ] );
    }

    public function saveSubservice(int $service_id, int $subservice_id): void 
    {
        $subservice = $this->db->get_where('subservices', 
            [ 'service' => $service_id, 'subservice' => $subservice_id] )->result_array();
        
        if ($subservice) {
            // Already exists, do nothing
			return;
        }

        $subservice['create_datetime'] = date('Y-m-d H:i:s');
        $subservice['update_datetime'] = date('Y-m-d H:i:s');
		$subservice['service'] = $service_id;
        $subservice['subservice'] = $subservice_id;
        
        if (!$this->db->insert('subservices', $subservice)) {
            throw new RuntimeException('Could not insert subservice.');
        }

    }
}