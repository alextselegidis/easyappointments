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
 * Errors Controller
 *
 * @package Controllers
 */
class Errors extends CI_Controller {
	/**
	 * Class Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		// Set user's selected language.
		if ($this->session->userdata('language')) {
			$this->config->set_item('language', $this->session->userdata('language'));
			$this->lang->load('translations', $this->session->userdata('language'));
		} else {
			$this->lang->load('translations', $this->config->item('language')); // default
		}
	}

    public function index() {
        $this->e404();
    }

    public function error404() {
		$this->load->helper('google_analytics'); 
        $this->load->model('settings_model');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $this->load->view('general/error404', $view);
    }
}

/* End of file errors.php */
/* Location: ./application/controllers/errors.php */
