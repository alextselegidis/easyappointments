<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * A modification of Easy!Appointments - Open Source Web Scheduler
 * for use with EasyAppointments by A.Tselegidis <alextselegidis@gmail.com>
 * @link        http://easyappointments.org
 * this uses portions of Alex's code with modifications by Craig Tucker
 * craigtuckerlcsw@verizon.net
 * ---------------------------------------------------------------------------- */

//This was based upon http://glennstovall.com/blog/2013/01/07/writing-cron-jobs-and-command-line-scripts-in-codeigniter/ with modifications for Easy!Appointments by Craig Tucker, 7/18/2014.
//--NZ-- need to fix once i can bok appointments - look at language logic from cli/WaitingList
class Reminders extends CI_Controller {
    public function __construct() {
        parent::__construct();
		
		$this->load->library('email');
        $this->load->library('session');
		$this->load->model('settings_model');		
        $this->load->model('reminders_model');
		$this->load->model('appointments_model');
		
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
		$d = $this->settings_model->get_setting('reminder_days_out'); //Number of days out for the reminder
		
		if ($d =='') {
			$d = '3,'; //default number of days out is 3
		}else{
			$d;
		}
		//Check if rightmost character is a comma
		if (substr($d, -1, 1) != ',') {
			$d = $d .',';
		}
		$pos = strrpos($d, ',');
		if ($pos > 0) {
			$days = explode(',', $d);
			for($i = 0; $i < count($days); $i++){
				echo 'Piece' . $i . ' = ' . $days[$i] . PHP_EOL;
				$timestamp = strtotime("+".$days[$i]." days");
				$appointments = $this->reminders_model->get_days_appointments($timestamp);
				$baseurl = $this->config->base_url();
				$company_name = $this->settings_model->get_setting('company_name');
				$appointment_link = $this->config->base_url().'index.php/appointments/index/';
				$time_format = $this->settings_model->get_setting('time_format');
				$date_format = $this->settings_model->get_setting('date_format');
		
				$msg = '';
					switch($date_format) {
						case 'DMY':
							$dateview='d/m/Y';
							break;
						case 'MDY':
							$dateview='m/d/Y';
							break;
						case 'YMD':
							$dateview='Y/m/d';
							break;
						default:
							$dateview='Y/m/d';
							break;
					}			
		
					switch($time_format) {
						case '24HR':
							$timeview=' H:i';
							break;
						case 'AM/PM':
							$timeview='g:i a';
							break;
						default:
							$timeview=' H:i';
							break;
					}			
				if(!empty($appointments)) {
					foreach($appointments as $appointment) {
						//$aptdatetime=date('D g:i a',strtotime($appointment->start_datetime));
						//Tucker $startdatetime=date('l, F j, Y, g:i a',strtotime($appointment->start_datetime));
						//Time format-- Military 'H:i' AM/PM 'h:i a'
						$email_field = NULL;
						$sms_field = NULL;
						$carrier = NULL;
						$notes_field = NULL;
						$this_lang = NULL;
						$longDay = NULL;
						$date_field = NULL;
						$time_field = NULL;
	
						$email_field = $appointment->customer_email;
						$sms_field = $appointment->customer_phone_number;
						$carrier = $appointment->customer_cellurl;
						$appt_id = $appointment->appt_id;
						$notes_field = $this->appointments_model->get_value('notes', $appt_id);
						$this_lang = $appointment->customer_lang;	
						$this->lang->load('translations', $this_lang);
				
						if ($days[$i] == "1") {
							$notice = $this->lang->line('notice_reminder');
						} else {
							$notice = $days[$i] . ' ' . $this->lang->line('notice_reminder_days');
						}

						$longDay = $this->lang->line(strtolower(date('l',strtotime($appointment->start_datetime))));
						$date_field = date($dateview,strtotime($appointment->start_datetime));
						$time_field = date($timeview,strtotime($appointment->start_datetime));
		
						echo "noticeGENERIC: " . $notice . PHP_EOL;
						echo "lang: " . $this_lang . PHP_EOL;
						echo "email: " . $email_field . PHP_EOL;
						echo "sms: " . $sms_field . PHP_EOL;
						echo "carrier: " . $carrier . PHP_EOL;
						echo "time_format: " . $time_format . PHP_EOL;
						echo "date_format: " . $date_format . PHP_EOL;
						echo "longDay: " . $longDay . PHP_EOL;
						echo "date_field: " . $date_field . PHP_EOL;
						echo "time_field: " . $time_field . PHP_EOL;
						echo "notes_field: " . $notes_field . PHP_EOL;

						if ($time_format == '24HR') {
							$startdatetime=$date_field . $time_field;
						} else {
							$startdatetime=$longDay . ', ' . $date_field . $time_field;
						}				
					
						$config['mailtype'] = 'text';
						$this->email->initialize($config);
						$this->email->set_newline(PHP_EOL);
						$this->email->to($appointment->customer_email);
							if (!empty($appointment->customer_cellurl)){
								$phone = $appointment->customer_phone_number;
								$phone = preg_replace('/[^\dxX]/', '', $phone);			
								$this->email->bcc($phone.$appointment->customer_cellurl);
							}
						$this->email->from($appointment->provider_email, $company_name);
						$this->email->subject($notice);
							$msg .= $company_name . PHP_EOL;
							$msg .= $this->lang->line('reminder_your_appt_with') . ' ' . $appointment->provider_first_name . ' ' .
								$appointment->provider_last_name . ' ' .  $this->lang->line('is_on') . ' ' . $startdatetime . PHP_EOL;
							$msg .=  PHP_EOL;
							$msg .=  $this->lang->line('notes') . ':' . PHP_EOL;
							$msg .=  $notes_field . PHP_EOL;
							$msg .=  PHP_EOL;
							$msg .= $this->lang->line('msg_line1') . PHP_EOL;
							$msg .= $this->lang->line('msg_line2') . PHP_EOL;
							$msg .=  PHP_EOL;
							$msg .= $this->lang->line('msg_line3') . PHP_EOL;
							$msg .= $appointment_link.$appointment->hash . PHP_EOL;
							$msg .=  PHP_EOL;
							$msg .= $this->lang->line('msg_line4') . PHP_EOL;
					
						$this->email->message($msg);
						$this->email->send();
						$msg = '';
						echo $this->email->print_debugger();  
					}
				}
			}
		}
	}
}
/* End of file reminders.php */
/* Location: ./application/controllers/cli/reminders.php */
