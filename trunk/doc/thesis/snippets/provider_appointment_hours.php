<?php
/**
 * [AJAX] Get the available appointment hours for the given date.
 * 
 * This method answers to an AJAX request. It calculates the 
 * available hours for thegiven service, provider and date.
 *
 * @param numeric $_POST['provider_id'] The selected provider's 
 * record id.
 * @param string $_POST['selected_date'] The selected date of 
 * which the available hours we want to see.
 * @param numeric $_POST['service_duration'] The selected service 
 * duration in minutes.
 * @param string $_POST['manage_mode'] Contains either 'true' or 
 * 'false' and determines the if current user is managing an already 
 * booked appointment or not.
 * @return Returns a json object with the available hours.
 */
public function ajax_get_available_hours() {
	$this->load->model('providers_model');
	$this->load->model('appointments_model');
	$this->load->model('settings_model');
	
	try {
		// If manage mode is TRUE then the following we should not 
		// consider the selected appointment when calculating the 
		// available time periods of the provider.
		$exclude_appointments = ($_POST['manage_mode'] === 'true') 
				? array($_POST['appointment_id'])
				: array();    

		$empty_periods = $this->get_provider_available_time_periods( 
				$_POST['provider_id'], $_POST['selected_date'], 
				$exclude_appointments);

		// Calculate the available appointment hours for the given 
		// date. The empty spaces are broken down to 15 min and if 
		// the service fit in each quarter then a new available hour 
		// is added to the "$available_hours" array.

		$available_hours = array();

		foreach ($empty_periods as $period) {
			$start_hour = new DateTime($_POST['selected_date'] 
					. ' ' . $period['start']);
			$end_hour = new DateTime($_POST['selected_date'] 
					. ' ' . $period['end']);

			$minutes = $start_hour->format('i');

			if ($minutes % 15 != 0) {
				// Change the start hour of the current space in 
				// order to be on of the following: 00, 15, 30, 45.
				if ($minutes < 15) {
					$start_hour->setTime($start_hour->format('H'), 15);
				} else if ($minutes < 30) {
					$start_hour->setTime($start_hour->format('H'), 30);
				} else if ($minutes < 45) {
					$start_hour->setTime($start_hour->format('H'), 45);
				} else {
					$start_hour->setTime($start_hour->format('H') + 1, 00);
				}
			}

			$current_hour = $start_hour;
			$diff = $current_hour->diff($end_hour);

			while (($diff->h * 60 + $diff->i) 
					> intval($_POST['service_duration'])) {
				$available_hours[] = $current_hour->format('H:i');
				$current_hour->add(new DateInterval("PT15M"));
				$diff = $current_hour->diff($end_hour);
			}
		}

		// If the selected date is today, remove past hours. It is 
		// important include the timeout before booking that is set 
		// in the backoffice the system. Normally we might want the 
		// customer to book an appointment that is at least half or 
		// one hour from now. The setting is stored in minutes.
		if (date('m/d/Y', strtotime($_POST['selected_date'])) 
				== date('m/d/Y')) {
			if ($_POST['manage_mode'] === 'true') {
				$book_advance_timeout = 0;
			} else {
				$book_advance_timeout = 
					$this->settings_model->get_setting('book_advance_timeout');
			}

			foreach($available_hours as $index => $value) {
				$available_hour = strtotime($value);
				$current_hour = strtotime('+' . $book_advance_timeout 
						. ' minutes', strtotime('now'));
				if ($available_hour <= $current_hour) {
					unset($available_hours[$index]);
				}
			}
		}

		$available_hours = array_values($available_hours);
		echo json_encode($available_hours);
		
	} catch(Exception $exc) {
		echo json_encode(array(
			'exceptions' => array(exceptionToJavaScript($exc))
		));
	}
}