<?php 

/* ----------------------------------------------------------------------------
 * A modification of Easy!Appointments - Open Source Web Scheduler
 * for use with EasyAppointments by A.Tselegidis <alextselegidis@gmail.com>
 * @link        http://easyappointments.org
 * this uses portions of Alex's code with modifications by Craig Tucker
 * craigtuckerlcsw@verizon.net
 * ---------------------------------------------------------------------------- */
//namespace EA\Engine\Notifications; 

//use \EA\Engine\Types\Text;
//use \EA\Engine\Types\NonEmptyText;
//use \EA\Engine\Types\Url;
//use \EA\Engine\Types\Email as EmailAddress;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User Controller 
 *
 * @package Controllers
 */
class Waitinglist extends CI_Controller {
    public function __construct() {
        parent::__construct();
		$this->load->library('email');
        $this->load->library('session');
		$this->load->model('settings_model');		
        
        // Set user's selected language.
        if ($this->session->userdata('language')) {
        	$this->config->set_item('language', $this->session->userdata('language'));
        	$this->lang->load('translations', $this->session->userdata('language'));
        } else {
        	$this->lang->load('translations', $this->config->item('language')); // default
        }
	}
		
    public function index() {
		if(!$this->input->is_cli_request()) {
			echo 'This script can only be accessed via the command line' . PHP_EOL;
			return;
		}
		$this->load->model('user_model');
		$this->load->model('services_model');
		$this->load->model( array('appointments_model'));		  
		$company_name = $this->settings_model->get_setting('company_name');
		$company_link = $this->settings_model->get_setting('company_link');
		$time_format = $this->settings_model->get_setting('time_format');
		$date_format = $this->settings_model->get_setting('date_format');
		$max_date = $this->settings_model->get_setting('max_date');
		$currentsched = $this->config->base_url();
		$appointment_link = $this->config->base_url().'index.php/appointments/index/';
		$msg = '';
		$str = '';
		$waiting_notices = $this->appointments_model->get_waitinglist();		
		$theme_color = $this->settings_model->get_setting('theme_color');
		switch($theme_color) {
				case 'green':
					$bgcolor='#39C678';
					$borderbottom='#C0F1D6';
					break;
				case 'blue':
					$bgcolor='#124E90';
					$borderbottom='#5884B4';
					break;
				case 'red':
					$bgcolor='#C3262E';
					$borderbottom='#75161B';
					break;
				default:
					$bgcolor='#1E6A40';
					$borderbottom='#123F26';
					break;				
		}

		if(!empty($waiting_notices)){
			foreach ($waiting_notices as $notice) {
				$email_field =NULL;
				$sms_field =NULL;
				$provider_id = $notice->id_users_provider;
				$service_duration = $notice->id_google_calendar;
				$service_id = $notice->id_services;
				$provider = $this->user_model->get_user_display_name($provider_id);
				$pemail = $this->user_model->get_user_email($provider_id);
				$emailphone = $notice->notes;
				
				$pos = strrpos($emailphone, ';');
				if ($pos > 0) {
					$addresses = explode(';', $emailphone);
					echo "array_size: " . (sizeof($addresses)-1) . PHP_EOL;					
					sizeof($addresses)-1; //to remove the trailing ";"
					$email_field = $addresses[0];
					$sms_field = $addresses[1];
				
				}else{

				}
				
				echo "email: " . $email_field . PHP_EOL;
				echo "sms: " . $sms_field . PHP_EOL;
				echo "time_format: " . $time_format . PHP_EOL;
				echo "date_format: " . $date_format . PHP_EOL;
				echo "max_date: " . $max_date . PHP_EOL;
					
				$subject = $this->lang->line('waiting_list_update') . ' ' .  $company_name;
				$sms_subject = $this->lang->line('waiting_list_sms');
				$availability = $this->availabilitylist($provider_id, $service_duration, $service_id);
				
				if (!empty ($sms_field)){
					$str = PHP_EOL;
					if (empty($availability[1])){
						$str .= strtoupper($subject) . PHP_EOL . $provider . ' ' . $this->lang->line('waiting_list_no_avail') . ' ';
						$str .= $this->lang->line('view_current_sched'). PHP_EOL;
					}else{
						$str .= strtoupper($subject). PHP_EOL;
						$str .= PHP_EOL;						
						$str .= $provider .' ' . $this->lang->line('waiting_list_has_avail') . PHP_EOL;
						$str .= PHP_EOL;
						$str .= $availability[1] . PHP_EOL;
						$str .= $this->lang->line('make_appointment') . PHP_EOL;
					}	
					
					$str .= PHP_EOL;
					$str .= $currentsched . PHP_EOL;
					$str .= $this->lang->line('remove_from_wl') . PHP_EOL;
					$str .= $appointment_link.$notice->hash . PHP_EOL;
					$str .= 'Powered by Easy!Appointments';
					
					$email = new \EA\Engine\Notifications\Email($this, $this->config->config);
					$email->sendTxtMail($pemail, $company_name, $sms_field, $sms_subject, $str);
					$str = '';
					echo $this->email->print_debugger();
				}				
				
				$msg .= '<html>';
				$msg .= '<head>';
				$msg .= '    <title>' . $subject . '</title>';
				$msg .= '</head>';
				$msg .= '<body style="font: 13px arial, helvetica, tahoma;">';
				$msg .= '    <div class="email-container" style="width: 650px; border: 1px solid #eee;">';
				$msg .= '        <div id="header" style="background-color: ' . $bgcolor . '; border-bottom: 4px solid ' . $borderbottom . ';';
				$msg .= '                height: 45px; padding: 10px 15px;">';
				$msg .= '            <strong id="logo" style="color: white; font-size: 20px;';
				$msg .= '                    text-shadow: 1px 1px 1px #8F8888; margin-top: 10px; display: inline-block">';
				$msg .= '                    ' . $company_name . '</strong>';
				$msg .= '        </div>';
				$msg .= '        <div id="content" style="padding: 10px 15px;">';
				$msg .= '            <h2>' . $subject . '</h2>';
				$msg .= '            <table id="waitinglist-details">';
				$msg .= '                <tr>';
				$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;"></td>';
				$msg .= '                    <td style="padding: 3px;">';

				if (empty($availability[0])){
					$msg .= $provider . ' ' . $this->lang->line('waiting_list_no_avail') . '<br>';
					$msg .= '<br>';
					$msg .= $this->lang->line('view_current_sched') . '<br>';
					$msg .= '<a href="' . $currentsched . '" style="text-decoration: none;">' . $this->lang->line('view_now') . '</a><br>';
					
				}else{
					$msg .= $provider . ' ' . $this->lang->line('waiting_list_has_avail') . '<br>';
					$msg .= '<br>';
					$msg .= '<font size="2">'.$availability[0].'</font><br>';
					$msg .= $this->lang->line('make_appointment') . '<br>';
					$msg .= '<a href="' . $currentsched . '" style="text-decoration: none;">' . $this->lang->line('book_now') . '</a><br>';
				}
				
				$msg .= '<br>';
				$msg .= $this->lang->line('remove_from_wl') . '<br>';
				$msg .= '<a href="' . $appointment_link.$notice->hash . '" style="text-decoration: none;">' . $this->lang->line('del_waiting'). '</a><br>';
				$msg .= '<br>';				

				$msg .= '				 </td>';
				$msg .= '                </tr>';
				$msg .= '            </table>';
				$msg .= '        </div>';
				$msg .= '        <div id="footer" style="padding: 10px; text-align: center; margin-top: 10px;';
				$msg .= '                border-top: 1px solid #EEE; background: #FAFAFA;">';
				$msg .= '            ' . $this->lang->line('powered_by') . '';
				$msg .= '            <a href="http://easyappointments.org" style="text-decoration: none;">Easy!Appointments</a>';
				$msg .= '            |';
				$msg .= '            <a href="' . $company_link . '" style="text-decoration: none;">' . $company_name . '</a>';
				$msg .= '        </div>';
				$msg .= '    </div>';
				$msg .= '</body>';
				$msg .= '</html>';
				
				$email = new \EA\Engine\Notifications\Email($this, $this->config->config);
				$email->sendHtmlMail($pemail, $company_name, $email_field, $subject, $msg);
				$msg = '';
				echo $this->email->print_debugger();
			}
		}
    }

    public function get_available_hours($selected_date, $provider_id, $service_duration,$service_id) {
        $this->load->model('providers_model');
        $this->load->model('appointments_model');
        $this->load->model('settings_model');
        $this->load->model('services_model');
		
		$time_format = $this->settings_model->get_setting('time_format');
        $empty_periods = array();
        $empty_periods = $this->get_provider_available_time_periods($selected_date,$provider_id);
		
		$service = $this->services_model->get_row($service_id);
		$availabilities_type = $service['availabilities_type'];

            // Calculate the available appointment hours for the given date. The empty spaces 
            // are broken down to 15 min and if the service fit in each quarter then a new 
            // available hour is added to the "$available_hours" array.

        $available_hours = [];
		
		$starts = $empty_periods[0];
		
        foreach ($empty_periods[1] as $period)
        {
		    $start_hour = new DateTime($selected_date . ' ' . $period['start']);
            $end_hour = new DateTime($selected_date . ' ' . $period['end']);
            $interval = $availabilities_type === AVAILABILITIES_TYPE_FIXED ? (int)$service_duration : $this->settings_model->get_setting('interval_time');

			if ($availabilities_type === AVAILABILITIES_TYPE_Q15){  
			$minutes = $start_hour->format('i');			
				if ($minutes % 15 != 0) {
					if ($minutes < 15) {
						$start_hour->setTime($start_hour->format('H'), 15);
					} else if ($minutes < 30) {
						$start_hour->setTime($start_hour->format('H'), 30);
					} else if ($minutes < 45) {
						$start_hour->setTime($start_hour->format('H'), 45);
					} else {
						$start_hour->setTime($start_hour->format('H') + 1, 00);
					}
				} else if ((!in_array($period['start'], $starts)) && ($minutes % 15 == 0)) {
					if ($minutes == 15) {
						$start_hour->setTime($start_hour->format('H'), 30);
					} else if ($minutes == 30) {
						$start_hour->setTime($start_hour->format('H'), 45);
					} else if ($minutes == 45) {
						$start_hour->setTime($start_hour->format('H') + 1, 00);
					} else {
						$start_hour->setTime($start_hour->format('H'), 15);
					}
				}
			}

			if ($availabilities_type === AVAILABILITIES_TYPE_Q30){  
			$minutes = $start_hour->format('i');
				if ($minutes % 30 != 0) {
					if ($minutes < 30) {
						$start_hour->setTime($start_hour->format('H'), 30);
					} else {
						$start_hour->setTime($start_hour->format('H') + 1, 00);
					}
				} else if ((!in_array($period['start'], $starts))&& ($minutes % 30 == 0)) {
					if ($minutes == 30) {
						$start_hour->setTime($start_hour->format('H') + 1, 00);
					} else {
						$start_hour->setTime($start_hour->format('H'), 30);
					}
				}
			}
			
            $current_hour = $start_hour;
            $diff = $current_hour->diff($end_hour);

            while (($diff->h * 60 + $diff->i) >= intval($service_duration))
            {
                $available_hours[] = $current_hour->format('H:i');
                $current_hour->add(new DateInterval('PT' . $interval . 'M'));
                $diff = $current_hour->diff($end_hour);
            }
        }

        return $available_hours;

	}

    protected function get_provider_available_time_periods(
        $selected_date,
        $provider_id
    ) {
        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');

        // Get the service, provider's working plan and provider appointments.
        $working_plan = json_decode($this->providers_model->get_setting('working_plan', $provider_id), TRUE);

        $provider_appointments = $this->appointments_model->get_batch([
            'id_users_provider' => $provider_id,
        ]);

        // Find the empty spaces on the plan. The first split between the plan is due to a break (if any). After that
        // every reserved appointment is considered to be a taken space in the plan.
        $selected_date_working_plan = $working_plan[strtolower(date('l', strtotime($selected_date)))];

        $periods = [];
		$starts = [];
		
        if (isset($selected_date_working_plan['breaks']))
        {
            $periods[] = [
                'start' => $selected_date_working_plan['start'],
                'end' => $selected_date_working_plan['end']
            ];

            $day_start = new DateTime($selected_date_working_plan['start']);
            $day_end = new DateTime($selected_date_working_plan['end']);

			$starts[] =  $day_start->format('H:i');
			
            // Split the working plan to available time periods that do not contain the breaks in them.
            foreach ($selected_date_working_plan['breaks'] as $index => $break)
            {
                $break_start = new DateTime($break['start']);
                $break_end = new DateTime($break['end']);
				
				$starts[] = $break_end->format('H:i');

                if ($break_start < $day_start)
                {
                    $break_start = $day_start;
                }

                if ($break_end > $day_end)
                {
                    $break_end = $day_end;
                }

                if ($break_start >= $break_end)
                {
                    continue;
                }

                foreach ($periods as $key => $period)
                {
                    $period_start = new DateTime($period['start']);
                    $period_end = new DateTime($period['end']);

                    $remove_current_period = FALSE;

                    if ($break_start > $period_start && $break_start < $period_end && $break_end > $period_start)
                    {
                        $periods[] = [
                            'start' => $period_start->format('H:i'),
                            'end' => $break_start->format('H:i')
                        ];

                        $remove_current_period = TRUE;
                    }

                    if ($break_start < $period_end && $break_end > $period_start && $break_end < $period_end)
                    {
                        $periods[] = [
                            'start' => $break_end->format('H:i'),
                            'end' => $period_end->format('H:i')
                        ];

                        $remove_current_period = TRUE;
                    }

                    if ($break_start == $period_start && $break_end == $period_end)
                    {
                        $remove_current_period = TRUE;
                    }

                    if ($remove_current_period)
                    {
                        unset($periods[$key]);
                    }
                }
            }
        }

        // Break the empty periods with the reserved appointments.
        foreach ($provider_appointments as $provider_appointment)
        {
            foreach ($periods as $index => &$period)
            {
                $appointment_start = new DateTime($provider_appointment['start_datetime']);
                $appointment_end = new DateTime($provider_appointment['end_datetime']);
                $period_start = new DateTime($selected_date . ' ' . $period['start']);
                $period_end = new DateTime($selected_date . ' ' . $period['end']);

                if ($appointment_start <= $period_start && $appointment_end <= $period_end && $appointment_end <= $period_start)
                {
                    // The appointment does not belong in this time period, so we  will not change anything.
                }
                else
                {
                    if ($appointment_start <= $period_start && $appointment_end <= $period_end && $appointment_end >= $period_start)
                    {
                        // The appointment starts before the period and finishes somewhere inside. We will need to break
                        // this period and leave the available part.
                        $period['start'] = $appointment_end->format('H:i');
						if ($provider_appointment['is_unavailable'] == 1)
						{
							$starts[] = $appointment_end->format('H:i');
						}
                    }
                    else
                    {
                        if ($appointment_start >= $period_start && $appointment_end < $period_end)
                        {
                            // The appointment is inside the time period, so we will split the period into two new
                            // others.
                            unset($periods[$index]);

                            $periods[] = [
                                'start' => $period_start->format('H:i'),
                                'end' => $appointment_start->format('H:i')
                            ];

                            $periods[] = [
                                'start' => $appointment_end->format('H:i'),
                                'end' => $period_end->format('H:i')
                            ];
							
							if ($provider_appointment['is_unavailable'] == 1)
							{
								$starts[] = $appointment_end->format('H:i');
							}
                        }
                        else if ($appointment_start == $period_start && $appointment_end == $period_end)
                        {
                            unset($periods[$index]); // The whole period is blocked so remove it from the available periods array.
                        }
                        else
                        {
                            if ($appointment_start >= $period_start && $appointment_end >= $period_start && $appointment_start <= $period_end)
                            {
                                // The appointment starts in the period and finishes out of it. We will need to remove
                                // the time that is taken from the appointment.
                                $period['end'] = $appointment_start->format('H:i');
                            }
                            else
                            {
                                if ($appointment_start >= $period_start && $appointment_end >= $period_end && $appointment_start >= $period_end)
                                {
                                    // The appointment does not belong in the period so do not change anything.
                                }
                                else
                                {
                                    if ($appointment_start <= $period_start && $appointment_end >= $period_end && $appointment_start <= $period_end)
                                    {
                                        // The appointment is bigger than the period, so this period needs to be removed.
                                        unset($periods[$index]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return array($starts ,array_values($periods));
    }

	public function availabilitylist($provider_id, $service_duration,$service_id){
		$max_date = $this->settings_model->get_setting('max_date');
		$date_format = $this->settings_model->get_setting('date_format');
		$time_format = $this->settings_model->get_setting('time_format');
		$availabilitylist = "";
		$availabilityliststr = "";
		// Start date
		$date = date('d-m-Y',strtotime('+1 days')); //to change the days out strtotime e.g.('+2 days')
		// End date
		$end_date = date('d-m-Y',strtotime('+' . $max_date . 'days')); //to change the days out strtotime e.g('+2 days')
		
		while (strtotime($date) <= strtotime($end_date)) {
			$longDay = $this->lang->line(strtolower(date('l',strtotime($date))));
			//echo "longDay: " . $longDay . PHP_EOL;
			switch($date_format) {
				case 'DMY':
					$dateview=date('d/m/Y, ',strtotime($date));
					$dateview=$dateview . $longDay;
					break;
				case 'MDY':
					$dateview=date('m/d/Y, ',strtotime($date));
					$dateview=$dateview . $longDay;
					break;
				case 'YMD':
					$dateview=date('Y/m/d, ',strtotime($date));
					$dateview=$dateview . $longDay;
					break;
				default:
					$dateview=date('Y/m/d, ',strtotime($date));
					$dateview=$dateview . $longDay;
					break;
			}			
			
			$availabehours=$this->get_available_hours($date, $provider_id, $service_duration,$service_id);
			if (!empty($availabehours)) {
				$availableslots = $this->get_available_hours($date, $provider_id, $service_duration,$service_id);

				$cstimes = implode(", ", $availableslots);
				$daysandslots = "<strong>".$dateview."</strong>: <br>".$cstimes. PHP_EOL . PHP_EOL . '<br>';
				$daysandslotsstr = strtoupper($dateview). PHP_EOL .$cstimes. PHP_EOL ;
				//echo "daysandslots: " . $daysandslots . PHP_EOL; 
				$availabilitylist .= $daysandslots;
				$availabilityliststr .= $daysandslotsstr;
			}
			$date = date ('d-m-Y', strtotime("+1 day", strtotime($date)));
		}
		
		return array($availabilitylist,$availabilityliststr);
	}
}
/* End of file 'waitinglist'.php */
/* Location: ./application/controllers/ci/'waitinglist'.php */