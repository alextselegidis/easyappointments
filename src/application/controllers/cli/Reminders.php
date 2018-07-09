<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * A modification of Easy!Appointments - Open Source Web Scheduler
 * for use with EasyAppointments by A.Tselegidis <alextselegidis@gmail.com>
 * @link        http://easyappointments.org
 * this uses portions of Alex's code with modifications by Craig Tucker
 * craigtuckerlcsw@verizon.net
 * ---------------------------------------------------------------------------- */

//This was based upon http://glennstovall.com/blog/2013/01/07/writing-cron-jobs-and-command-line-scripts-in-codeigniter/ with modifications for Easy!Appointments by Craig Tucker, 7/18/2014.
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
		$this->load->model('user_model');
		$this->load->model('services_model');
		
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
				$company_link = $this->settings_model->get_setting('company_link');
				$appointment_link = $this->config->base_url().'index.php/appointments/index/';
				$time_format = $this->settings_model->get_setting('time_format');
				$date_format = $this->settings_model->get_setting('date_format');
				$str = '';
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
						case 'military':
							$timeview=' H:i';
							break;
						case 'regular':
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
						$longDay = NULL;
						$date_field = NULL;
						$time_field = NULL;
						$email_message = NULL;
						$appointment_duration = NULL;
						$customer_name = NULL;
						$customer_street = NULL;
						$customer_city = NULL;
						$customer_zip_code = NULL;						
	
						$email_field = $appointment->customer_email;
						$sms_field = $appointment->customer_phone_number;
						$carrier = $appointment->customer_cellurl;
						$customer_name = $appointment->customer_first_name . ' ' . $appointment->customer_last_name;
						$customer_street = $appointment->customer_address;
						$customer_city = $appointment->customer_city;
						$customer_zip_code = $appointment->customer_zip_code;
						$appt_id = $appointment->appt_id;
						$notes_field = $this->appointments_model->get_value('notes', $appt_id);
						$provider_id = $this->appointments_model->get_value('id_users_provider', $appt_id);
						$provider = $this->user_model->get_user_display_name($provider_id);
						$pemail = $this->user_model->get_user_email($provider_id);
						$appointment_service_id = $this->appointments_model->get_value('id_services', $appt_id);
						$appointment_service = $this->services_model->get_value('name', $appointment_service_id);
						$appointment_price = $this->services_model->get_value('price', $appointment_service_id);
						$appointment_currency = $this->services_model->get_value('currency', $appointment_service_id);
						$appointment_price_currency = $appointment_price . ' ' . $appointment_currency;
						$appointment_duration = $this->services_model->get_value('duration', $appointment_service_id) . ' ' . $this->lang->line('minutes');
						$provider_street = $this->user_model->get_value('address', $provider_id);
						$provider_city = $this->user_model->get_value('city', $provider_id);
						$provider_state = $this->user_model->get_value('state', $provider_id);
						$provider_zip_code = $this->user_model->get_value('zip_code', $provider_id);

						$address_provider = $provider_street . ', ' . $provider_city . ', ' . $provider_state.' '.$provider_zip_code;
						$address_customer = $customer_street . ', ' . $customer_city . ' - ' . $customer_zip_code;

						$provider_address = str_replace(', ,','',$address_provider);						
						$customer_address = str_replace(', -','',$address_customer);
						
						$limitdetails = $this->settings_model->get_setting('show_minimal_details');
				
						if ($days[$i] == "1") {
							$notice = $this->lang->line('notice_reminder');
							$notice_sms = $this->lang->line('notice_reminder_sms');
						} else {
							$notice = $days[$i] . ' ' . $this->lang->line('notice_reminder_days');
							$notice_sms = $days[$i] . ' ' . $this->lang->line('notice_reminder_days_sms');
						}

						$longDay = $this->lang->line(strtolower(date('l',strtotime($appointment->start_datetime))));
						$date_field = date($dateview,strtotime($appointment->start_datetime));
						$time_field = date($timeview,strtotime($appointment->start_datetime));
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
		
						echo "noticeGENERIC: " . $notice . PHP_EOL;
						echo "email: " . $email_field . PHP_EOL;
						echo "sms: " . $sms_field . PHP_EOL;
						echo "carrier: " . $carrier . PHP_EOL;
						echo "time_format: " . $time_format . PHP_EOL;
						echo "date_format: " . $date_format . PHP_EOL;
						echo "longDay: " . $longDay . PHP_EOL;
						echo "date_field: " . $date_field . PHP_EOL;
						echo "time_field: " . $time_field . PHP_EOL;
						echo "notes_field: " . $notes_field . PHP_EOL;

						if ($time_format == 'regular') {
							$startdatetime=$date_field . $time_field;
						} else {
							$startdatetime=$longDay . ', ' . $date_field . $time_field;
						}				
						$longDay = $this->lang->line(strtolower(date('l',strtotime($appointment->start_datetime))));
						$date_field = date($dateview,strtotime($appointment->start_datetime));
						$time_field = date($timeview,strtotime($appointment->start_datetime));
						if ($time_format == 'regular') {				
							$appointment_start_date_pre = $date_field .' ' . $time_field;
						} else {
							$appointment_start_date_pre = $longDay . ', ' . $date_field .' ' . $time_field;
						}			
						$longDay = $this->lang->line(strtolower(date('l',strtotime($appointment->end_datetime))));
						$date_field = date($dateview,strtotime($appointment->end_datetime));
						$time_field = date($timeview,strtotime($appointment->end_datetime));
													   
																 
						if ($time_format == 'regular') {
							$appointment_end_date_pre = $date_field .' ' . $time_field;
							$end_time = $time_field;
						} else {
							$appointment_end_date_pre = $longDay . ', ' . $date_field .' ' . $time_field;
							$end_time = $time_field;
						}

						if (!empty($appointment->customer_cellurl)){
							$phone = $appointment->customer_phone_number;
							$phone = preg_replace('/[^\dxX]/', '', $phone);
							$sms_field = $phone.$appointment->customer_cellurl;
							$sms_subject = $notice_sms;

							$str .= strtoupper($company_name) . '--' . $notice . PHP_EOL;
							$str .= $this->lang->line('provider') . '--' . $provider . PHP_EOL;
							$str .= $provider_address . PHP_EOL;
							if ($limitdetails == 'no') {
								$str .= $appointment_service. PHP_EOL;
								$str .= $this->lang->line('price') . ' '  . $appointment_price_currency. PHP_EOL;
							}
							$str .= $appointment_start_date_pre. '--'. $end_time . PHP_EOL;
							$str .= $this->lang->line('edit_reschedule_cancel_appointment') . PHP_EOL;
							$str .= $appointment_link.$appointment->hash . PHP_EOL . PHP_EOL;

							if (!empty($this->lang->line('msg_line1'))) {
								$str .= $this->lang->line('msg_line1') . PHP_EOL;
								$str .= $this->lang->line('url_line1') . PHP_EOL;
							}
							if (!empty($this->lang->line('msg_line2'))) {
								$str .= $this->lang->line('msg_line2') . PHP_EOL;
								$str .= $this->lang->line('url_line2') . PHP_EOL;
							}
							if ($limitdetails == 'no') {
								$str .= strtoupper($this->lang->line('customer_details_title')) . PHP_EOL;
								$str .= $customer_name . PHP_EOL;
								$str .= $email_field . PHP_EOL;
								$str .= $sms_field . PHP_EOL;
								$str .= $customer_address . PHP_EOL;
								$str .= $notes_field . PHP_EOL;
							}
							$str .= $this->lang->line('powered_by') . ' Easy!Appointments' . PHP_EOL;

							$email = new \EA\Engine\Notifications\Email($this, $this->config->config);
							$email->sendTxtMail($pemail, $company_name, $sms_field, $sms_subject, $str);
							$str = '';
						}						
						
						$email_field = $appointment->customer_email;
						$subject = $notice;
						
						$msg .= '<html>';
						$msg .= '<head>';
						$msg .= '    <title>' . $notice . '</title>';
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
						$msg .= '            <h2>' . $notice . '</h2>';
						$msg .= '            <h2>' . $this->lang->line('appointment_details_title') . '</h2>';
						$msg .= '            <table id="appointment-details">';
						$msg .= '                <tr>';
						$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('provider') . '</td>';
						$msg .= '                    <td style="padding: 3px;">' . $provider . '</td>';
						$msg .= '                </tr>';
						$msg .= '				 <!--Google Maps Mod Craig Tucker start -->';
						$msg .= '                <tr>';
						$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('address') . '</td>';
						$msg .= '                    <td style="padding: 3px;"><a href="http://maps.google.com/maps?q=' . $provider_address . '">' . $provider_address . '</a></td>';
						$msg .= '				 </tr>';
						$msg .= '				 <!--Google Maps Mod Craig Tucker end -->';

						if ($limitdetails == 'no') {
							
							$msg .= '                <tr>';
							$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('service') . '</td>';
							$msg .= '                    <td style="padding: 3px;">' . $appointment_service . '</td>';
							$msg .= '                </tr>';
							$msg .= '                <tr>';
							$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('duration') . '</td>';
							$msg .= '                    <td style="padding: 3px;">' . $appointment_duration . '</td>';
							$msg .= '                </tr>';
							$msg .= '                <tr>';
							$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('price') . '</td>';
							$msg .= '                    <td style="padding: 3px;">' . $appointment_price_currency . '</td>';
							$msg .= '                </tr>';
						}

						$msg .= '                <tr>';
						$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('appointment') . '</td>';
						$msg .= '                    <td style="padding: 3px;">' . $appointment_start_date_pre . '</td>';
						$msg .= '                </tr>';
						$msg .= '                <tr>';
						$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">&#9724;</td>';
						$msg .= '                    <td style="padding: 3px;"><a href="' . $appointment_link.$appointment->hash . '">' . $this->lang->line('edit_reschedule_cancel_appointment') . '</td>';
						$msg .= '                </tr>';
						if (!empty($this->lang->line('msg_line1'))) {
							$msg .= '                <tr>';
							$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">&#9724;</td>';
							$msg .= '                    <td style="padding: 3px;"><a href="' . $this->lang->line('url_line1') . '">' .$this->lang->line('msg_line1') .  '</td>';
							$msg .= '                </tr>';
						}
						if (!empty($this->lang->line('msg_line2'))) {
							$msg .= '                <tr>';
							$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">&#9724;</td>';
							$msg .= '                    <td style="padding: 3px;"><a href="' . $this->lang->line('url_line2') . '">' .$this->lang->line('msg_line2') . '</td>';
							$msg .= '                </tr>';							
							$msg .= '            </table>';
						}
						if ($limitdetails == 'no') {
							$msg .= '            <h2>' . $this->lang->line('customer_details_title') . '</h2>';
							$msg .= '            <table id="customer-details">';
							$msg .= '                <tr>';
							$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('name') . '</td>';
							$msg .= '                    <td style="padding: 3px;">' . $customer_name . '</td>';
							$msg .= '                </tr>';
							$msg .= '                <tr>';
							$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('email') . '</td>';
							$msg .= '                    <td style="padding: 3px;">' . $email_field . '</td>';
							$msg .= '                </tr>';
							$msg .= '                <tr>';
							$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('phone') . '</td>';
							$msg .= '                    <td style="padding: 3px;">' . $phone . '</td>';
							$msg .= '                </tr>';
							$msg .= '                <tr>';
							$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('address') . '</td>';
							$msg .= '                    <td style="padding: 3px;">' . $customer_address . '</td>';
							$msg .= '                </tr>';
							$msg .= '                    <td class="label" style="padding: 3px;font-weight: bold;">' . $this->lang->line('notes') . '</td>';
							$msg .= '                    <td style="padding: 3px;">' . $notes_field . '</td>';
							$msg .= '                </tr>';
							$msg .= '            </table>';
							$msg .= '        </div>';
						}
						
						$msg .= '        <div id="footer" style="padding: 10px; text-align: center; margin-top: 10px;';
						$msg .= '                border-top: 1px solid #EEE; background: #FAFAFA;">';
						$msg .= '            ' . $this->lang->line('powered_by') . ' ';
						$msg .= '            <a href="http://easyappointments.org" style="text-decoration: none;">Easy!Appointments</a>';
						$msg .= '            |';
						$msg .= '            <a href="' . $company_link . '" style="text-decoration: none;">' . $company_name . '</a>';
						$msg .= '        </div>';
						$msg .= '    </div>';
						$msg .= '</body>';
						$msg .= '</html>';
						$msg .= '';

						$email = new \EA\Engine\Notifications\Email($this, $this->config->config);
						$email->sendHtmlMail($pemail, $company_name, $email_field, $subject, $msg);
						$msg = '';
					}
				}
			}
		}
	}
}
/* End of file reminders.php */
/* Location: ./application/controllers/cli/reminders.php */
