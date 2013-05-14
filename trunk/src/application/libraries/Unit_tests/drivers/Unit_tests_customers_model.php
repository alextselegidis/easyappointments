<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');  

class Unit_tests_customers_model extends CI_Driver {
    private $CI;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('Unit_test');
        $this->CI->load->model('Customers_Model');
    }
    
    /**
     * Run all the available tests
     */
    public function run_all() {
        // All the methods whose names start with "test" are going to be 
        // executed. If you want a method to not be executed remove the 
        // "test" keyword from the beginning.
        $class_methods = get_class_methods('Unit_tests_customers_model');
        foreach ($class_methods as $method_name) {
            if (substr($method_name, 0, 5) === 'test_') {
                call_user_func(array($this, $method_name));
            }
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    // UNIT TESTS
    /////////////////////////////////////////////////////////////////////////   
    private function test_true() {
        $this->CI->unit->run(TRUE, TRUE, 'True!');
    }
    
    
    
}

/* End of file Unit_tests_customers_model.php */
/* Location: ./application/libraries/Unit_tests/drivers/Unit_tests_customers_model.php */