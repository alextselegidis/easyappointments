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
            $view_data['company_name'] = $this->Settings_Model->get_setting('company_name');

            // Get the available services and providers.
            $this->load->model('Services_Model');
            $view_data['available_services'] = $this->Services_Model->get_available_services();

            $this->load->model('Providers_Model');
            $view_data['available_providers'] = $this->Providers_Model->get_available_providers();

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
            try {
                $this->notifications->send_book_success($post_data['customer'], $post_data['appointment']);
                $this->notifications->send_new_appointment($post_data['customer'], $post_data['appointment']);
            } catch (NotificationException $not_exc) {
                $view_data['notification_error'] = '<br><br><pre>An unexpected error occured while sending  ' 
                        . 'you an email. Please backup the appointment details so that you can restore them '
                        . 'later. <br><br>Error:<br>' . $not_exc->getMessage() . '</pre>';
            }
            
            // Load the book appointment view.
            $this->load->view('appointments/book_success', $view_data);
        }
	}
    
    /**
     * [AJAX] Get the available appointment hours for the given date.
     * 
     * This method answers to an AJAX request. It calculates the 
     * available hours for the given service, provider and date.
     * 
     * @param array $_POST['post_data'] An associative array that 
     * contains the user selected 'service_id', 'provider_id', 
     * 'selected_date' and 'service_duration' in minutes.
     * @return Returns a json object with the available hours.
     */
    public function ajax_get_available_hours() {
        $this->load->model('Providers_Model');
        $this->load->model('Appointments_Model');
        $this->load->model('Settings_Model');
        
        // Get the provider's working plan and reserved appointments.        
        $working_plan = json_decode($this->Providers_Model
                ->get_value('working_plan', $_POST['provider_id']), true);
        
        $reserved_appointments = $this->Appointments_Model->get_batch(
                array(
                    'DATE(start_datetime)' => date('Y-m-d', strtotime($_POST['selected_date'])),
                    'id_users_provider' => $_POST['provider_id'],
                    'id_services' => $_POST['service_id']
                ));

        // Find the empty spaces on the plan. The first split between 
        // the plan is due to a break (if exist). After that every reserved 
        // appointment is considered to be a taken space in the plan.
        $sel_date_working_plan = $working_plan[strtolower(date('l', 
                strtotime($_POST['selected_date'])))];
        $empty_spaces_with_breaks = array();
        
        if (isset($sel_date_working_plan['breaks'])) {
            foreach($sel_date_working_plan['breaks'] as $index=>$break) {
                // Split the working plan to available time periods that do not
                // contain the breaks in them.
                $last_break_index = $index - 1;
                
                if (count($empty_spaces_with_breaks) === 0) {
                    $start_hour = $sel_date_working_plan['start'];
                    $end_hour = $break['start'];
                } else {
                    $start_hour = $sel_date_working_plan['breaks'][$last_break_index]['end'];
                    $end_hour = $break['start'];
                }
                
                $empty_spaces_with_breaks[] = array(
                                        'start' => $start_hour,
                                        'end'   => $end_hour
                                    );
            }
            
            // Add the space from the last break to the end of the day.
            $empty_spaces_with_breaks[] = array(
                'start' => $sel_date_working_plan['breaks'][$index]['end'],
                'end'   => $sel_date_working_plan['end']
            );
        }
       
        // Break the empty spaces with the reserved appointments.
        $empty_spaces_with_appointments = array();
        if (count($reserved_appointments) > 0) {
            foreach($empty_spaces_with_breaks as $space) {
                foreach($reserved_appointments as $index=>$appointment) {
                    $appointment_start  = date('H:i', strtotime($appointment['start_datetime']));
                    $appointment_end    = date('H:i', strtotime($appointment['end_datetime']));
                    $space_start        = date('H:i', strtotime($space['start']));
                    $space_end          = date('H:i', strtotime($space['end']));
                    
                    if ($space_start < $appointment_start && $space_end > $appointment_end) {
                        // Current appointment is within the current empty space. So 
                        // we need to break the empty space into two other spaces that 
                        // don't include the appointment.
                        $empty_spaces_with_appointments[] = array(
                                                    'start' => $space_start,
                                                    'end'   => $appointment_start
                                                );
                        $empty_spaces_with_appointments[] = array(
                                                    'start' => $appointment_end,
                                                    'end'   => $space_end
                                                );
                    } else {
                        $empty_spaces_with_appointments[] = array(
                                                    'start' => $space_start,
                                                    'end'   => $space_end
                                                );
                    }
                }
            }
        } else {
            $empty_spaces_with_appointments = $empty_spaces_with_breaks;
        }
        
        $empty_spaces = $empty_spaces_with_appointments;
        
        // Calculate the available appointment hours for the given date. 
        // The empty spaces are broken down to 15 min and if the service
        // fit in each quarter then a new available hour is added to the
        // $available hours array.
        $available_hours = array();
        
        foreach($empty_spaces as $space) {
            $start_hour = new DateTime($_POST['selected_date'] . ' ' . $space['start']);
            $end_hour = new DateTime($_POST['selected_date'] . ' ' . $space['end']);
            $curr_hour = $start_hour;
            
            $diff = $curr_hour->diff($end_hour);
            while(($diff->h * 60 + $diff->i) > intval($_POST['service_duration'])) {
                $available_hours[] = $curr_hour->format('H:i');
                $curr_hour->add(new DateInterval("PT15M"));
                $diff = $curr_hour->diff($end_hour);
            }
        }
        
        // If the selected date is today, remove past hours. It is important 
        // include the timeout before booking that is set in the backoffice
        // the system. Normally we might want the customer to book an appointment
        // that is at least half or one hour from now. The setting is stored in 
        // minutes.
        if (date('m/d/Y', strtotime($_POST['selected_date'])) == date('m/d/Y')) {
            $book_advance_timeout = $this->Settings_Model->get_setting('book_advance_timeout');
            
            foreach($available_hours as $index=>$value) {
                $available_hour = strtotime($value);
                $current_hour   = strtotime('+' . $book_advance_timeout 
                        . ' minutes', strtotime('now'));
        
                if ($available_hour <= $current_hour) {
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
            $view_data['message'] = 'An unexpected error occured during the sync '
                    . 'operation: <br/><pre>' . $exc->getMessage() . '<br/>' 
                    . $exc->getTraceAsString() . '</pre>';
            $view_data['image'] = 'error.png';
        }
        
        $this->load->view('appointments/google_sync', $view_data);
    }
}

/* End of file appointments.php */
/* Location: ./application/controllers/appointments.php */