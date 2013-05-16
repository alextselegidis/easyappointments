<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointments extends CI_Controller {
	/**
	 * This page displays the book appointment wizard
     * for the customers.
	 */
	public function index() {
        if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') { 
            // Display the appointment booking page to the customer.
            // Get business name.
            $this->load->model('Settings_Model');
            $view_data['business_name'] = $this->Settings_Model->get_setting('business_name');

            // Get the available services and providers.
            $this->load->model('Services_Model');
            $view_data['available_services'] = $this->Services_Model->get_available_services();

            $this->load->model('Providers_Model');
            $view_data['available_providers'] = $this->Providers_Model->get_available_providers(); // Provider rows contain an array of which services they can provide.

            // Load the book appointment view.
            $this->load->view('appointments/book', $view_data);
        } else { 
            $post_data = json_decode($_POST['post_data'], true);
            
            // Add customer 
            $this->load->model('Customers_Model');
            $customer_id = $this->Customers_Model->add($post_data['customer']);
            
            // Add appointment
            $post_data['appointment']['id_users_customer'] = $customer_id;
            $this->load->model('Appointments_Model');
            $view_data['appointment_id'] = $this->Appointments_Model->add($post_data['appointment']);
            
            // Send an email to the customer with the appointment info.
            $this->load->library('Notifications');
            $this->notifications->send_book_success($post_data['customer'], $post_data['appointment']);
            $this->notifications->send_new_appointment($post_data['customer'], $post_data['appointment']);
            
            // Load the book appointment view.
            $this->load->view('appointments/book_success', $view_data);
        }
	}
    
    /**
     * This method answers to an AJAX request. It calculates the 
     * available hours for the given service, provider and date.
     * 
     * @param array $_POST['post_data'] An associative array that 
     * contains the user selected service, provider and date.
     */
    public function ajax_get_available_hours() {
        /*** CHECK BUSINESS WORKING HOURS ***/
        /*** CHECK PROVIDERS WORKING HOURS ***/
        /*** CHECK ALREADY BOOKED APPOINTMENTS ***/
        /*** RETURN AVAILABLE HOURS ***/
        
        // ------------------------------------------------------------------
        // For now we just need to return a sample array. Dynamic calculation
        // will be adding in future development.
        $start_time  = strtotime($_POST['selected_date'] . ' 08:00') / 60; // Convert to minutes
        $end_time    = strtotime($_POST['selected_date'] . ' 19:00') / 60; // Convert to minutes
        
        for ($i=0; ($start_time*60 + $i*60)<=($end_time*60); $i+=intval($_POST['service_duration'])) {
            $available_hours[] = date('H:i', $start_time * 60 + $i * 60);
        }
        
        // If the selected date is today, remove past hours.
        if (date('m/d/Y', strtotime($_POST['selected_date'])) == date('m/d/Y')) {
            foreach($available_hours as $index=>$value) {
                $available_hour = date('m/d/Y H:i', strtotime($value));
                $current_hour   = date('m/d/Y H:i');

                if ($available_hour < $current_hour) {
                    unset($available_hours[$index]);
                }
            }
        }
        
        $available_hours = array_values($available_hours);
        
        echo json_encode($available_hours);
    }
    
    /**
     * Synchronize appointment with its' providers google calendar.
     * 
     * This method syncs the registered appointment with the
     * google calendar of the user.
     * 
     * @task This method needs to be changed. Everytime a customer 
     * books a new appointment the synchronization process must be
     * executed.
     */
    public function google_sync($appointment_id) {
        try {
            $this->load->library('Google_Sync');
            $this->google_sync->sync_appointment($appointment_id);
            $view_data['message'] = 'Your appointment has been successfully added to Google Calendar!';
            $view_data['image'] = 'success.png';
        } catch (Exception $exc) {
            $view_data['message'] = 'An unexpected error occured during the sync operation: <br/><pre>' . $exc->getMessage() 
                    . '<br/>' . $exc->getTraceAsString() . '</pre>';
            $view_data['image'] = 'error.png';
        }
        
        $this->load->view('appointments/google_sync', $view_data);
    }
}

/* End of file appointments.php */
/* Location: ./application/controllers/appointments.php */