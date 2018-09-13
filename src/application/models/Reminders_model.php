<?php
//This was based upon http://glennstovall.com/blog/2013/01/07/writing-cron-jobs-and-command-line-scripts-in-codeigniter/ with modifications for Easy!Appointments by Craig Tucker, 7/18/2014.
//There would be a much more graceful way of handling the query rather than using this "if" statement.  But, I do not have time to think about it and 
//this was the fastest way for me to get the job done now.

class Reminders_model extends CI_Model {
    public function __construct() {
        parent::__construct();	
	
		$this->load->model('settings_model');
	}
	
	public function get_days_appointments($day) {
		$day_start = date('Y-m-d 00:00:00', $day);
		$day_end = date('Y-m-d 23:59:59', $day);
		
		$conf_notice = $this->settings_model->get_setting('conf_notice');
		
		if ($conf_notice =='yes'){
			return $this->db->select('e.id AS appt_id, 
				e.start_datetime, 
				e.end_datetime, 
				e.hash, 
				e.id_google_calendar, 
				u1.first_name AS customer_first_name, 
				u1.last_name AS customer_last_name, 
				u1.email AS customer_email, 
				u1.address AS customer_address, 
				u1.city AS customer_city, 
				u1.zip_code AS customer_zip_code, 
				u1.phone_number AS customer_phone_number,
				u1.notes AS customer_notes,	
				u1.notifications as notifications,	
				u2.first_name AS provider_first_name, 
				u2.last_name AS provider_last_name, 
				u2.email AS provider_email, 
				u2.phone_number AS provider_phone_number, 
				s.name, 
				s.duration, 
				s.price, 
				s.description,
				cc.cellurl AS customer_cellurl')
			->from('ea_appointments e')
			->join('ea_users u1', 'e.id_users_customer = u1.id','left') 
			->join('ea_users u2', 'e.id_users_provider = u2.id','left') 
			->join('ea_services s', 'e.id_services = s.id','left')
			->join('ea_cellcarrier cc', 'u1.id_cellcarrier = cc.id','left')
			->where('e.start_datetime >',$day_start)			
			->where('e.start_datetime <',$day_end)
			->where('e.is_unavailable =',0)
			->where('u1.notifications =',1)
		->get()->result();
		} else {
			return $this->db->select('e.id AS appt_id, 
				e.start_datetime, 
				e.end_datetime, 
				e.hash, 
				e.id_google_calendar, 
				u1.first_name AS customer_first_name, 
				u1.last_name AS customer_last_name, 
				u1.email AS customer_email, 
				u1.address AS customer_address, 
				u1.city AS customer_city, 
				u1.zip_code AS customer_zip_code, 
				u1.phone_number AS customer_phone_number,
				u1.notes AS customer_notes,	
				u2.first_name AS provider_first_name, 
				u2.last_name AS provider_last_name, 
				u2.email AS provider_email, 
				u2.phone_number AS provider_phone_number, 
				s.name, 
				s.duration, 
				s.price, 
				s.description,
				cc.cellurl AS customer_cellurl')
			->from('ea_appointments e')
			->join('ea_users u1', 'e.id_users_customer = u1.id','left') 
			->join('ea_users u2', 'e.id_users_provider = u2.id','left') 
			->join('ea_services s', 'e.id_services = s.id','left')
			->join('ea_cellcarrier cc', 'u1.id_cellcarrier = cc.id','left')
			->where('e.is_unavailable =',0)
			->where('e.start_datetime >',$day_start)			
			->where('e.start_datetime <',$day_end)
		->get()->result();			
		}
	}
}
/* End of file reminders_model.php */
/* Location: ./application/models/reminders_model.php */
