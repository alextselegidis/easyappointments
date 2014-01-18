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
     * 
     * We only test models at the moment. In the future the unit test will be 
     * improved.
     */
    public function index() {
        // User must be logged in as an admin in order to run the tests.
        $this->load->library('session');
        $this->session->set_userdata('dest_url', $this->config->item('base_url') . 'test');
        if ($this->session->userdata('role_slug') != DB_SLUG_ADMIN) {
            header('Location: ' . $this->config->item('base_url') . 'user/login');
            return;
        }
        
        if (ENVIRONMENT !== 'development') {
            $this->output->set_output('Tests are available only at development environment. '
                    . 'Please check your "index.php" file settings.');
            return;
        }
        
        $this->load->view('general/test');
        $this->unit_tests->run_all_tests();
    } 
    
    /**
     * Test only the app models.
     */
    public function models() {
        //$this->load->view('general/test');
        //$this->unit_tests->run_model_tests();
    }
    
    /**
     * Test only the app libraries.
     */
    public function libraries() {
        //$this->load->view('general/test');
        //$this->unit_tests->run_library_tests();
    }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */