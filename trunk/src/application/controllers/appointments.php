<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointments extends CI_Controller {
	/**
	 * This page displays the book appointment wizard
     * for the customers.
	 */
	public function index() {
        // Get business name.
        $this->load->model('Settings');
        $viewData['businessName'] = $this->Settings->getSetting('business_name');
        
        // Get the available services and providers.
        $this->load->model('Services');
        $viewData['availableServices'] = $this->Services->getAvailableServices();
        
        $this->load->model('Providers');
        $viewData['availableProviders'] = $this->Providers->getAvailableProviders(); // Provider rows contain an array of which services they can provide.
        
        // Load the book appointment view.
		$this->load->view('appointments/book', $viewData);
	}
    
    /**
     * This method answers to an AJAX request. It calculates the 
     * available hours for the given service, provider and date.
     * 
     * @param array $_POST['postData'] An associative array that 
     * contains the user selected service, provider and date.
     */
    public function getAvailableHours() {
        /*** CHECK BUSINESS WORKING HOURS ***/
        /*** CHECK PROVIDERS WORKING HOURS ***/
        /*** CHECK ALREADY BOOKED APPOINTMENTS ***/
        /*** RETURN AVAILABLE HOURS ***/
        
        // ------------------------------------------------------------------
        // For now we just need to return a sample array. Dynamic calculation
        // will be adding in future development.
        $startTime  = strtotime($_POST['selectedDate'] . ' 08:00') / 60; // Convert to minutes
        $endTime    = strtotime($_POST['selectedDate'] . ' 16:00') / 60; // Convert to minutes
        
        for ($i=0; ($startTime*60 + $i*60)<=($endTime*60); $i+=intval($_POST['serviceDuration'])) {
            $availableHours[] = date('H:i', $startTime * 60 + $i * 60);
        }
        
        echo json_encode($availableHours);
    }
}
