<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 * Test Controller
 *
 * NOTICE: This controller is outdated and must not be used.
 *
 * @deprecated v1.1.0
 *
 * @package Controllers
 */
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
        $this->session->set_userdata('dest_url', site_url('test'));
        if ($this->session->userdata('role_slug') != DB_SLUG_ADMIN) {
            header('Location: ' . site_url('user/login'));
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
