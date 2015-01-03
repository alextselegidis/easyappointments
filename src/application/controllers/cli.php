<?php
class cli extends CI_Controller
{
	public function __construct() {
		parent::__construct();

		if (!$this->input->is_cli_request())
		{
			show_404('page');
			exit();
		}

		// Set user's selected language.
		$this->lang->load('translations', $this->config->item('language')); // default
	}

	/**
	 * Sends email notificaitons to client who have appointments in the next 24
	 * hours. If the client books within 36 hours of their appointment
	 *
	 * This method is meant to be run from the command line through a cron job
	 * or scheduler at frequent intervals. It checks to see if there are any
	 * clients with upcoming appointments in the database and notifies them.
	 * Once the client has been notified the appointment is marked as
	 * 'notified' so that the client isnt' notified again.
	 */
	public function send_appointment_reminders($simulate = FALSE, $show_email_message = FALSE)
	{
		// TODO: make sure notified column exists. if not, add it
		if ( $this->input->is_cli_request())
		{
			$this->load->model('appointments_model');
			$query = $this->db
				->select('*')
				->from('ea_appointments')
				->where('`id_services` IS NOT NULL AND `notified` = FALSE  AND `start_datetime` > now() AND `start_datetime` < date_add(now(), INTERVAL 24 HOUR) AND `book_datetime` < date_sub(`start_datetime`, INTERVAL 36 HOUR)')
				->get();

			$sql = $this->db->last_query();
			//echo $sql . "\n";
			if ($query->num_rows())
			{
				echo "Sending Reminder Emails:\n";
			}
			else
			{
				//echo "No upcoming appointments\n";
			}

			for ($i = 0; $i < $query->num_rows(); $i++)
			{
				$appointment_data = $query->row_array($i);
				$this->send_appointment_reminder($appointment_data, $simulate, $show_email_message);
			}
		}
	}

	/**
	 * This method will update the installation to the latest available
	 * version in the server. IMPORTANT: The code files must exist in the
	 * server, this method will not fetch any new files but will update
	 * the database schema.
	 *
	 * This method can be used either by loading the page in the browser
	 * or by an ajax request. But it will answer with json encoded data.
	 */
	public function update() {
		if ( $this->input->is_cli_request()) {
			try {
				$this->load->library('migration');

				$result = $this->migration->current();

				if (!$result)
					throw new Exception($this->migration->error_string());

				if ("integer" == gettype($result))
					echo "Successfully updated schema version to $result\n";
				else
					echo "Schema version is up to date\n";

			} catch(Exception $exc) {
				//echo "$exc\n";
			}
		}
	}

	private function send_appointment_reminder($appointment_data, $simulate = FALSE, $show_email_message = FALSE)
	{
		$this->load->model('services_model');
		$this->load->model('customers_model');
		$this->load->model('settings_model');

		$service_data = $this->services_model->get_row($appointment_data['id_services']);
		$customer_data = $this->customers_model->get_row($appointment_data['id_users_customer']);
		$provider_data = $this->customers_model->get_row($appointment_data['id_users_provider']);

		$company_settings = array(
			'company_name'  => $this->settings_model->get_setting('company_name'),
			'company_link'  => $this->settings_model->get_setting('company_link'),
			'company_email' => $this->settings_model->get_setting('company_email'),
			'date_format' => $this->settings_model->get_setting('date_format'),
			'time_format' => $this->settings_model->get_setting('time_format')
		);

		try {
			$this->load->library('Notifications');

			$customer_title = $this->lang->line('appointment_reminder');
			$customer_message = $this->lang->line('thank_you_for_appointment');
			$customer_link = $this->config->item('base_url') . 'appointments/index/'
				. $appointment_data['hash'];


			$email_address = $customer_data['email'];
			$debug = FALSE;
			if ($simulate)
			{
				if ($show_email_message)
				{
					$debug = TRUE;
				}
			}
			else
			{
				$this->db->where('id', $appointment_data['id'])->update('ea_appointments', array( 'notified' => 1) );
			}

			echo "Sending reminder email to " . $email_address;
			echo "\n";

			if (!$simulate || $debug)
			{
				$this->notifications->send_appointment_details($appointment_data, $provider_data,
					$service_data, $customer_data, $company_settings, $customer_title,
					$customer_message, $customer_link, $email_address, $debug);
			}

		} catch(Exception $exc) {
			$view['exceptions'][] = $exc;
		}
	}
}
?>

