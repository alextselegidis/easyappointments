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
            $viewData['businessName'] = $this->Settings_Model->getSetting('business_name');

            // Get the available services and providers.
            $this->load->model('Services_Model');
            $viewData['availableServices'] = $this->Services_Model->getAvailableServices();

            $this->load->model('Providers_Model');
            $viewData['availableProviders'] = $this->Providers_Model->getAvailableProviders(); // Provider rows contain an array of which services they can provide.

            // Load the book appointment view.
            $this->load->view('appointments/book', $viewData);
        } else { 
            // Add the appointment to the database and display the success
            // page to the customer.
            $customerData = array(
                'first_name'    => $_POST['firstName'],
                'last_name'     => $_POST['lastName'],
                'email'         => $_POST['email'],
                'phone_number'  => $_POST['phoneNumber'],
                'address'       => $_POST['address'],
                'city'          => $_POST['city'],
                'zip_code'      => $_POST['zipCode']
            );
            
            $this->load->model('Customers_Model');
            $customerId = $this->Customers_Model->add($customerData);
            
            $appointmentData = array(
                'start_datetime'    => $_POST['startDatetime'],
                'end_datetime'      => $_POST['endDatetime'],
                'notes'             => $_POST['notes'],
                'id_users_provider' => $_POST['providerId'],
                'id_users_customer' => $customerId,
                'id_services'       => $_POST['serviceId'],
            );
            
            $this->load->model('Appointments_Model');
            $this->Appointments_Model->add($appointmentData);
            
            // Send an email to the customer with the appointment info.
            $to      = $customerData['email'];
            $subject = 'Appointment Book Success';
            $message = 'Hi there! Your appointment has been successfully booked.';
            $headers = 'From: alextselegidis@gmail.com' . "\r\n" .
                'Reply-To: alextselegidis@gmail.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);
            
            // Load the book appointment view.
            $this->load->view('appointments/book-success');
        }
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
        $endTime    = strtotime($_POST['selectedDate'] . ' 19:00') / 60; // Convert to minutes
        
        for ($i=0; ($startTime*60 + $i*60)<=($endTime*60); $i+=intval($_POST['serviceDuration'])) {
            $availableHours[] = date('H:i', $startTime * 60 + $i * 60);
        }
        
        // If the selected date is today, remove past hours.
        if (date('m/d/Y', strtotime($_POST['selectedDate'])) == date('m/d/Y')) {
            foreach($availableHours as $index=>$value) {
                $availableHour = date('m/d/Y H:i', strtotime($value));
                $currentHour = date('m/d/Y H:i');

                if ($availableHour < $currentHour) {
                    unset($availableHours[$index]);
                }
            }
        }
        
        $availableHours = array_values($availableHours);
        
        echo json_encode($availableHours);
    }
}
