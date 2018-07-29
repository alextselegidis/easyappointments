<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Paypaltimer extends CI_Controller { 
	public function __construct() { 
		parent::__construct(); 
		
	}
	
  public function index(){
	if(!$this->input->is_cli_request()) {
		echo 'This script can only be accessed via the command line' . PHP_EOL;
		return;
	}
	 
	//delete any appointments that are pending and delete the ones that are older than 10 minutes
	 
	$this->db->where('pending <> ""')->where('book_datetime < (NOW() - INTERVAL 10 MINUTE)');
	$this->db->delete('ea_appointments'); 

  }
}
?>



		
