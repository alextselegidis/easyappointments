<?php
/**
 * Get an array containing the free time periods (start - end) of a 
 * selected date.
 * 
 * This method is very important because there are many cases where 
 * the system needs to know when a provider is avaible for an 
 * appointment. This method will return an array that belongs to the 
 * selected date and contains values that have the start and the end 
 * time of an available time period.
 * 
 * @param numeric $provider_id The provider's record id.
 * @param string $selected_date The date to be checked (MySQL  
 * formatted string).
 * @param array $exclude_appointments This array contains the ids of  
 * the appointments that will not be taken into consideration when   
 * the available time periods are calculated.
 * @return array Returns an array with the available time periods of  
 * the provider.
 */
private function get_provider_available_time_periods($provider_id,  
		$selected_date, $exclude_appointments = array()) {
	$this->load->model('appointments_model');
	$this->load->model('providers_model');
	
	// Get the provider's working plan and reserved appointments.        
	$working_plan = json_decode($this->providers_model
			->get_setting('working_plan', $provider_id), true);
	
	$where_clause = array(
		'DATE(start_datetime)' => 
				date('Y-m-d', strtotime($selected_date)),
		'id_users_provider' => $provider_id
	);     
	
	$reserved_appointments = 
			$this->appointments_model->get_batch($where_clause);
	
	// Sometimes it might be necessary to not take into account some 
	// appointment records in order to display what the providers' 
	// available time periods would be without them.
	foreach ($exclude_appointments as $excluded_id) {
		foreach ($reserved_appointments as $index => $reserved) {
			if ($reserved['id'] == $excluded_id) {
				unset($reserved_appointments[$index]);
			}
		}
	}
	
	// Find the empty spaces on the plan. The first split between 
	// the plan is due to a break (if exist). After that every 
	// reserved appointment is considered to be a taken space in the
	// plan.
	$selected_date_working_plan = 
		$working_plan[strtolower(date('l', strtotime($selected_date)))];
	$available_periods_with_breaks = array();
	
	if (isset($selected_date_working_plan['breaks'])) {
		foreach($selected_date_working_plan['breaks'] as $index=>$break){
			// Split the working plan to available time periods that do not
			// contain the breaks in them.
			$last_break_index = $index - 1;
			
			if (count($available_periods_with_breaks) === 0) {
				$start_hour = $selected_date_working_plan['start'];
				$end_hour = $break['start'];
			} else {
				$start_hour = $selected_date_working_plan
						['breaks'][$last_break_index]['end'];
				$end_hour = $break['start'];
			}
			
			$available_periods_with_breaks[] = array(
				'start' => $start_hour,
				'end' => $end_hour
			);
		}
		
		// Add the period from the last break to the end of the day.
		$available_periods_with_breaks[] = array(
			'start' =>$selected_date_working_plan['breaks'][$index]['end'],
			'end' => $selected_date_working_plan['end']
		);
	}
	
	// Break the empty periods with the reserved appointments.
	$available_periods_with_appointments = array();
	
	if (count($reserved_appointments) > 0) {
		
		foreach($available_periods_with_breaks as $period) {
			
			foreach($reserved_appointments as $index=>$reserved) {
				$appointment_start = 
					date('H:i', strtotime($reserved['start_datetime']));
				$appointment_end = 
					date('H:i', strtotime($reserved['end_datetime']));
				$period_start = 
					date('H:i', strtotime($period['start']));
				$period_end = 
					date('H:i', strtotime($period['end']));
				
				if ($period_start <= $appointment_start 
						&& $period_end >= $appointment_end) {
					// We need to check whether another appointment fits
					// in the current time period. If this happens, then 
					// we need to consider the whole appointment time as 
					// one, because the provider will not be available.
					foreach ($reserved_appointments as $tmp_appointment) {
						$appt_start = date('H:i', 
							strtotime($tmp_appointment['start_datetime']));
						$appt_end = date('H:i', 
							strtotime($tmp_appointment['end_datetime']));
						
						if ($period_start < $appt_start 
								&& $period_end > $appt_end) {
							if ($appointment_start > $appt_start) {
								$appointment_start = $appt_start;
							}
							
							if ($appointment_end < $appt_end) { 
								$appointment_end = $appt_end;
							}
						}
					}

					// Current appointment is within the current 
					// empty space. So we need to break the empty 
					// space into two other spaces that don't include 
					// the appointment.
					$new_period = array(
						'start' => $period_start,
						'end' => $appointment_start
					);
					
					if (!in_array($new_period, 
							$available_periods_with_appointments)) {
						$available_periods_with_appointments[] = $new_period;
					}
						
					$new_period = array(
						'start' => $appointment_end,
						'end' => $period_end
					);
					
					if (!in_array($new_period, 
							$available_periods_with_appointments)) {
						$available_periods_with_appointments[] = $new_period;
					}
					
				} else {
					// Check if there are any other appointments 
					// between this time space. If not, it is going 
					// to be added as it is.
					$found = FALSE;
					
					foreach ($reserved_appointments as $tmp_appointment) {
						$appt_start = date('H:i', 
							strtotime($tmp_appointment['start_datetime']));
						$appt_end  = date('H:i', 
							strtotime($tmp_appointment['end_datetime']));
						
						if ($period_start < $appt_start 
								&& $period_end > $appt_end) {
							$found = TRUE;
						}
					}
					
					// It is also necessary to check that this time 
					// period doesn't already exist in the 
					// "$empty_spaces_with_appointments" array.
					$empty_period = array(
						'start' => $period_start,
						'end' => $period_end
					);
					
					$already_exist = in_array($empty_period, 
							$available_periods_with_appointments);
					
					if ($found === FALSE && $already_exist === FALSE) {
						$available_periods_with_appointments[] = $empty_period;
					}
				}
			}
		}
	} else {
		$available_periods_with_appointments = 
				$available_periods_with_breaks;
	}
	
	return $available_periods_with_appointments;
}