<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointments extends CI_Controller {
	/**
	 * This page displays the book appointment wizard
     * for the customers.
	 */
	public function index()
	{
        $this->load->model('Settings');
        $viewData['businessName'] = $this->Settings->getSetting('business_name');
        
		$this->load->view('appointments/book', $viewData);
	}
}
