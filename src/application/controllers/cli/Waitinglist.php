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
		$email_message = '';
		$waiting_notices = $this->appointments_model->get_waitinglist();		
		$theme_color = $this->settings_model->get_setting('theme_color');
		switch($theme_color) {
			case 'green':
				$background_color='#1E6A40';
				$border_bottom='#123F26';
				break;
			case 'blue':
				$background_color='#517DAE';
				$border_bottom='#012448';
				break;
			case 'red':
				$background_color='#C3262E';
				$border_bottom='#75161B';
				break;
			default:
				$background_color='#1E6A40';
				$border_bottom='#123F26';
				break;
		}

		if(!empty($waiting_notices)){
			foreach ($waiting_notices as $notice) {
				$email_field =NULL;
				$sms_field =NULL;
				$this_lang=NULL;
				$provider_id = $notice->id_users_provider;
				$provider = $this->user_model->get_user_display_name($provider_id);
				$pemail = $this->user_model->get_user_email($provider_id);
				$appointment_service_id = $notice->id_services;
				$appointment_service = $this->services_model->get_value('name', $appointment_service_id);
				$appointment_price = $this->services_model->get_value('price', $appointment_service_id);
				$appointment_currency = $this->services_model->get_value('currency', $appointment_service_id);
				$appointment_price_currency = $appointment_price . ' ' . $appointment_currency;
				$provider_street = $this->user_model->get_value('address', $provider_id);
				$provider_city = $this->user_model->get_value('city', $provider_id);
				$provider_state = $this->user_model->get_value('state', $provider_id);
				$provider_zip_code = $this->user_model->get_value('zip_code', $provider_id);
				$provider_address = $provider_street.', '.$provider_city.', '.$provider_state.' '.$provider_zip_code; 
				$emailphone = $notice->notes;
				$this_lang = $notice->lang;
				
				$pos = strrpos($emailphone, ';');
				if ($pos > 0) {
					$addresses = explode(';', $emailphone);
					echo "array_size: " . (sizeof($addresses)-1) . PHP_EOL;					
					switch (sizeof($addresses)-1) //to remove the trailing ";"
					{
						case 1:
						$email_field = $addresses[0];
						break;
						
						case 2:
						$email_field = $addresses[0];
						$sms_field = $addresses[1];
						break;
						
						default:
						$this_lang = $addresses[0];
						break;
					}
				}else{

				}
				echo "lang: " . $this_lang . PHP_EOL;
				echo "email: " . $email_field . PHP_EOL;
				echo "sms: " . $sms_field . PHP_EOL;
				echo "time_format: " . $time_format . PHP_EOL;
				echo "date_format: " . $date_format . PHP_EOL;
				echo "max_date: " . $max_date . PHP_EOL;
					
				$this->lang->load('translations', $this_lang);
				
				$subject = $this->lang->line('waiting_list_update') . ' ' .  $company_name;
				$availability = $this->availabilitylist($provider_id);
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				$this->email->set_newline(PHP_EOL);
				$this->email->from($pemail, $company_name);
				$this->email->to($email_field);
				$this->email->bcc($sms_field);
				$this->email->subject($subject);
				$email_message .= $provider . ' ' . $this->lang->line('waiting_list_has_avail') . '<br>';
				$email_message .= '<br>';
				if (!empty($availability)){
					$email_message .= $availability.'<br>';
					$email_message .= $this->lang->line('make_appointment') . '<br>';
					$email_message .= '<a href="' . $currentsched . '" style="text-decoration: none;">' . $currentsched . '</a>' . '<br>';
				}
				$email_message .= '<br>';
				$email_message .= $this->lang->line('remove_from_wl') . '<br>';
				$email_message .= '<a href="' . $appointment_link.$notice->hash . '" style="text-decoration: none;">' . $appointment_link.$notice->hash . '</a>' . '<br>';
				$email_message .= '<br>';

				$msg .= '<html>';
				$msg .= '<head>';
				$msg .= '    <title>' . $subject . '</title>';
				$msg .= '</head>';
				$msg .= '<body style="font: 13px arial, helvetica, tahoma;">';
				$msg .= '    <div class="email-container" style="width: 650px; border: 1px solid #eee;">';
				$msg .= '        <div id="header" style="background-color: ' . $background_color . '; border-bottom: 4px solid ' . $border_bottom . ';';
				$msg .= '                height: 45px; padding: 10px 15px;">';
				$msg .= '            <strong id="logo" style="color: white; font-size: 20px;';
				$msg .= '                    text-shadow: 1px 1px 1px #8F8888; margin-top: 10px; display: inline-block">';
				$msg .= '                    ' . $company_name . '</strong>';
				$msg .= '        </div>';
				$msg .= '        <div id="content" style="padding: 10px 15px;">';
				$msg .= '            <h2>' . $subject . '</h2>';
				$msg .= '            <h2>' . $this->lang->line('appointment_details_title') . '</h2>';
				$msg .= '            <table id="appointment-details">';
				$msg .= '                <tr>';
				$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('service') . '</td>';
				$msg .= '                    <td style="padding: 3px;">' . $appointment_service . '</td>';
				$msg .= '                </tr>';
				$msg .= '                <tr>';
				$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('provider') . '</td>';
				$msg .= '                    <td style="padding: 3px;">' . $provider . '</td>';
				$msg .= '                </tr>';
				$msg .= '                <tr>';
				$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('price') . '</td>';
				$msg .= '                    <td style="padding: 3px;">' . $appointment_price_currency . '</td>';
				$msg .= '                </tr>';
				$msg .= '				 <!--Google Maps Mod Craig Tucker start -->';
				$msg .= '                <tr>';
				$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('address') . '</td>';
				$msg .= '                    <td style="padding: 3px;"><a href="www.google.com/maps/place/' . $provider_address . '">' . $provider_address . '</a></td>';
				$msg .= '				 </tr>';
				$msg .= '				 <!--Google Maps Mod Craig Tucker end -->';
				$msg .= '            </table>';
				$msg .= '            <h2>' . $this->lang->line('customer_details_title') . '</h2>';
				$msg .= '            <table id="customer-details">';
				$msg .= '                <tr>';
				$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('email') . '</td>';
				$msg .= '                    <td style="padding: 3px;">' . $email_field . '</td>';
				$msg .= '                </tr>';
				$msg .= '                <tr>';
				$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('phone') . '</td>';
				$msg .= '                    <td style="padding: 3px;">' . $sms_field . '</td>';
				$msg .= '                </tr>';
				$msg .= '            </table>';
				$msg .= '            <h2>' . $this->lang->line('waiting_list_details_title') . '</h2>';
				$msg .= '            <table id="waitinglist-details">';
				$msg .= '                <tr>';
				$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;"></td>';
				$msg .= '                    <td style="padding: 3px;">' . $email_message . '</td>';
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

				$this->email->message($msg);
				$this->email->send();
				$msg = '';
				echo $this->email->print_debugger();  
			}
		}
    }

    public function get_available_hours($selected_date, $provider_id) {
        $this->load->model('providers_model');
        $this->load->model('appointments_model');
        $this->load->model('settings_model');
		$time_format = $this->settings_model->get_setting('time_format');
        
        $empty_periods = $this->get_provider_available_time_periods($selected_date, $provider_id);

            // Calculate the available appointment hours for the given date. The empty spaces 
            // are broken down to 15 min and if the service fit in each quarter then a new 
            // available hour is added to the "$available_hours" array.

        $available_hours = array();

        foreach ($empty_periods as $period) {
                $start_hour = new DateTime($selected_date . ' ' . $period['start']);
                $end_hour = new DateTime($selected_date . ' ' . $period['end']);

                $minutes = $start_hour->format('i');

                if ($minutes % 15 != 0) {
                    // Change the start hour of the current space in order to be
                    // on of the following: 00, 15, 30, 45.
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
				//intval() is the default duration to search for availability
                while (($diff->h * 60 + $diff->i) >= intval(60)) {
					if ($time_format == '24HR')	{
						$available_hours[] = $current_hour->format('H:i');
					}else{
						$available_hours[] = $current_hour->format('g:i a');
					}
                    $current_hour->add(new DateInterval("PT60M"));
                    $diff = $current_hour->diff($end_hour);
                }
            }

		//sort($available_hours, SORT_STRING );
        $available_hours = array_values($available_hours);
        return array_values($available_hours); 
	}

    public function get_provider_available_time_periods($selected_date, $provider_id) {
        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        
        // Get the provider's working plan and reserved appointments.        
        $working_plan = json_decode($this->providers_model->get_setting('working_plan', $provider_id), true);
        
        $where_clause = array(
            //'DATE(start_datetime)' => date('Y-m-d', strtotime($selected_date)),
            'id_users_provider' => $provider_id
        );     
        
        $reserved_appointments = $this->appointments_model->get_batch($where_clause);
        
        
        // Find the empty spaces on the plan. The first split between the plan is due to 
        // a break (if exist). After that every reserved appointment is considered to be 
        // a taken space in the plan.
        $selected_date_working_plan = $working_plan[strtolower(date('l', strtotime($selected_date)))];
        $available_periods_with_breaks = array();
        
        if (isset($selected_date_working_plan['breaks'])) {
            if (count($selected_date_working_plan['breaks'])) {
                foreach($selected_date_working_plan['breaks'] as $index=>$break) {
                    // Split the working plan to available time periods that do not
                    // contain the breaks in them.
                    $last_break_index = $index - 1;

                    if (count($available_periods_with_breaks) === 0) {
                        $start_hour = $selected_date_working_plan['start'];
                        $end_hour = $break['start'];
                    } else {
                        $start_hour = $selected_date_working_plan['breaks'][$last_break_index]['end'];
                        $end_hour = $break['start'];
                    }

                    $available_periods_with_breaks[] = array(
                        'start' => $start_hour,
                        'end' => $end_hour
                    );
                }

                // Add the period from the last break to the end of the day.
                $available_periods_with_breaks[] = array(
                    'start' => $selected_date_working_plan['breaks'][$index]['end'],
                    'end' => $selected_date_working_plan['end']
                );
            } else {
                $available_periods_with_breaks[] = array(
                    'start' => $selected_date_working_plan['start'],
                    'end' => $selected_date_working_plan['end']
                );
            }
        }
        
        // Break the empty periods with the reserved appointments.
        $available_periods_with_appointments = $available_periods_with_breaks;
        
        foreach($reserved_appointments as $appointment) {
            foreach($available_periods_with_appointments as $index => &$period) {
            
                $a_start = strtotime($appointment['start_datetime']);
                $a_end =  strtotime($appointment['end_datetime']);
                $p_start = strtotime($selected_date .  ' ' . $period['start']);
                $p_end = strtotime($selected_date .  ' ' .$period['end']);

                if ($a_start <= $p_start && $a_end <= $p_end && $a_end <= $p_start) {
                    // The appointment does not belong in this time period, so we
                    // will not change anything.
                } else if ($a_start <= $p_start && $a_end <= $p_end && $a_end >= $p_start) {
                    // The appointment starts before the period and finishes somewhere inside.
                    // We will need to break this period and leave the available part.
                    $period['start'] = date('H:i', $a_end);

                } else if ($a_start >= $p_start && $a_end <= $p_end) {
                    // The appointment is inside the time period, so we will split the period
                    // into two new others.
                    unset($available_periods_with_appointments[$index]);
                    $available_periods_with_appointments[] = array(
                        'start' => date('H:i', $p_start),
                        'end' => date('H:i', $a_start)
                    );
                    $available_periods_with_appointments[] = array(
                        'start' => date('H:i', $a_end),
                        'end' => date('H:i', $p_end)
                    );

                } else if ($a_start >= $p_start && $a_end >= $p_start && $a_start <= $p_end) {
                    // The appointment starts in the period and finishes out of it. We will
                    // need to remove the time that is taken from the appointment.
                    $period['end'] = date('H:i', $a_start);

                } else if ($a_start >= $p_start && $a_end >= $p_end && $a_start >= $p_end) {
                    // The appointment does not belong in the period so do not change anything.
                } else if ($a_start <= $p_start && $a_end >= $p_end && $a_start <= $p_end) {
                    // The appointment is bigger than the period, so this period needs to be 
                    // removed.
                    unset($available_periods_with_appointments[$index]);
                }
            }
        }
        return array_values($available_periods_with_appointments);
    }

	public function availabilitylist($provider_id){
		$max_date = $this->settings_model->get_setting('max_date');
		$date_format = $this->settings_model->get_setting('date_format');
		$time_format = $this->settings_model->get_setting('time_format');
		$availabilitylist = "";
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
			
			$availabehours=$this->get_available_hours($date, $provider_id);
			if (!empty($availabehours)) {
				$availableslots = $this->get_available_hours($date, $provider_id);

				//$foundAm = false;
				//$foundPm = false;
				//foreach( $availableslots as $index => $timeSlot ) {
				//	if( strpos( $timeSlot, "am" ) !== false && $foundAm === false ) {
				//			$foundAm = true;
				//			continue;
				//	} else if( strpos( $timeSlot, "pm" ) !== false && $foundPm === false ) {
				//			$foundPm = true;
				//			continue;
				//	}
				//	 $availableslots[$index] = trim( str_replace( array( "am", "pm" ) , "", $timeSlot ) );
				//}
				
				$cstimes = implode(", ", $availableslots);
				$daysandslots = $dateview.": ".$cstimes. PHP_EOL . PHP_EOL . '<br>';
				//echo "daysandslots: " . $daysandslots . PHP_EOL; 
				$availabilitylist .= $daysandslots;
			}
			$date = date ('d-m-Y', strtotime("+1 day", strtotime($date)));
		}
		return $availabilitylist;
	}
}
/* End of file 'waitinglist'.php */
/* Location: ./application/controllers/ci/'waitinglist'.php */