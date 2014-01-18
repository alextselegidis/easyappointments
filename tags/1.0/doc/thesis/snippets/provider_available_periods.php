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
 * the appointments that will not be taken into consideration when the 
 * available time periods are calculated.
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
		'DATE(start_datetime)'=>date('Y-m-d', strtotime($selected_date)),
		'id_users_provider'=>$provider_id
	);     
	
	$reserved_appointments = $this->appointments_model
			->get_batch($where_clause);
	
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
	
	// Find the empty spaces on the plan. The first split between the 
	// plan is due to a break (if exist). After that every reserved 
	// appointment is considered to be a taken space in the plan.
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
				$start_hour = $selected_date_working_plan['breaks']
						[$last_break_index]['end'];
				$end_hour = $break['start'];
			}
			
			$available_periods_with_breaks[] = array(
				'start' => $start_hour,
				'end' => $end_hour
			);
		}
		// Add the period from the last break to the end of the day.
		$available_periods_with_breaks[] = array(
			'start'=>$selected_date_working_plan['breaks'][$index]['end'],
			'end'=>$selected_date_working_plan['end']
		);
	}
	
	// Break the empty periods with the reserved appointments.
	$available_periods_with_appointments = 
					$available_periods_with_breaks;
	
	foreach($reserved_appointments as $appointment) {
		foreach($available_periods_with_appointments as $index=>&$period){
			$a_start = 
				date('H:i',strtotime($appointment['start_datetime']));
			$a_end = 
				date('H:i', strtotime($appointment['end_datetime']));
			$p_start = 
				date('H:i', strtotime($period['start']));
			$p_end = 
				date('H:i', strtotime($period['end']));

			if ($a_start <= $p_start && $a_end <= $p_end 
					&& $a_end <= $p_start) {
				// The appointment does not belong in this time period, so 
				// we will not change anything.
			} else if ($a_start <= $p_start && $a_end <= $p_end 
					&& $a_end >= $p_start) {
				// The appointment starts before the period and finishes 
				// somewhere inside.We will need to break this period and 
				// leave the available part.
				$period['start'] = $a_end;
			} else if ($a_start >= $p_start && $a_end <= $p_start) {
				// The appointment is inside the time period, so we will 
				// split the period into two new others.
				unset($available_periods_with_appointments[$index]);
				$available_periods_with_appointments[] = array(
					'start' => $p_start,
					'end' => $a_start
				);
				$available_periods_with_appointments[] = array(
					'start' => $a_end,
					'end' => $p_end
				);
			} else if ($a_start >= $p_start && $a_end >= $p_start 
					&& $a_start <= $p_end) {
				// The appointment starts in the period and finishes out 
				// of it. We will need to remove the time that is taken 
				// from the appointment.
				$period['end'] = $a_start;
			} else if ($a_start >= $p_start && $a_end >= $p_end 
					&& $a_start >= $p_end) {
				// The appointment does not belong in the period so do not 
				// change anything.
			} else if ($a_start <= $p_start && $a_end >= $p_end 
					&& $a_start <= $p_end) {
				// The appointment is bigger than the period, so this period 
				// needs to be removed.
				unset($available_periods_with_appointments[$index]);
			}
		}
	}
	return array_values($available_periods_with_appointments);
}