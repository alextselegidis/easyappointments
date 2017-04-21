<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Cellcarrier_Model extends MY_Model {
    
    const DB_TABLE = 'ea_cellcarrier';
    const DB_TABLE_PK = 'id';
    
    /**
     * Cellcarrier unique identifier.
     * @var int
     */
    public $id;
    
    /**
     * Cell carrier name.
     * @var string
     */
    public $cellco;
	
	    /**
     * Cell carrier URL.
     * @var string
     */
    public $cellurl;
	
    public function get_cellcarriers() {
    	$this->db->distinct();
        return $this->db
        		->from('ea_cellcarrier')
        		->get()->result_array();
   }
}