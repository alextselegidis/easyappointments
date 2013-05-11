<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->load->driver('Unit_tests');
    }
    
    /**
     * Run all available unit tests.
     */
    public function index() {
        $this->load->view('general/test');
        $this->unit_tests->run_all_tests();
    } 
    
    /**
     * Test only the app models.
     */
    public function models() {
        $this->load->view('general/test');
        $this->unit_tests->run_model_tests();
    }
    
    /**
     * Test only the app libraries.
     */
    public function libraries() {
        $this->load->view('general/test');
        $this->unit_tests->run_library_tests();
    }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */