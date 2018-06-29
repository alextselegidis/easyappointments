<?php defined("BASEPATH") or exit("No direct script access allowed");

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 *  MODIFICATION by Craig Tucker
 * ---------------------------------------------------------------------------- */

  class Migration_Add_ea_cellcarrier extends CI_Migration {

    public function up() {
		if (!$this->db->table_exists('ea_cellcarrier') ){
			  $this->dbforge->add_field('id');
			  $this->dbforge->add_field(array(

				'cellco' => array(
				  'type' => 'VARCHAR',
				  'constraint' => '30',
				  'null' => TRUE,
				),
				'cellurl' => array(
				  'type' => 'VARCHAR',
				  'constraint' => '30',
				  'null' => TRUE,
				),
			  ));
			  
			$attributes = array(
				'ENGINE' => 'InnoDB',
				'AUTO_INCREMENT' => 17,
				'DEFAULT CHARSET' => 'utf8'
			);
			
			$this->dbforge->create_table('ea_cellcarrier',TRUE,$attributes);


			$data = array(
					array(
							'id' => 1,
							'cellco' => 'AT&T',
							'cellurl' => '@mms.att.net'
					),
					array(
							'id' => 2,
							'cellco' => 'T-Mobile',
							'cellurl' => '@tmomail.net'
					),
					array(
							'id' => 3,
							'cellco' => 'Verizon',
							'cellurl' => '@vtext.com'
					),
					array(
							'id' => 4,
							'cellco' => 'Sprint',
							'cellurl' => '@messaging.sprintpcs.com'
					),
					array(
							'id' => 5,
							'cellco' => 'Sprint PM',
							'cellurl' => '@pm.sprint.com'
					),
					array(
							'id' => 6,
							'cellco' => 'Virgin Mobile',
							'cellurl' => '@vmobl.com'
					),
					array(
							'id' => 7,
							'cellco' => 'Tracfone',
							'cellurl' => '@mmst5.tracfone.com'
					),
					array(
							'id' => 8,
							'cellco' => 'Metro PCS',
							'cellurl' => '@mymetropcs.com'
					),
					array(
							'id' => 9,
							'cellco' => 'Boost Mobile',
							'cellurl' => '@myboostmobile.com'
					),
					array(
							'id' => 10,
							'cellco' => 'Cricket',
							'cellurl' => '@sms.cricketwireless.com'
					),
					array(
							'id' => 12,
							'cellco' => 'Alltel',
							'cellurl' => '@message.alltel.com'
					),
					array(
							'id' => 13,
							'cellco' => 'Ptel',
							'cellurl' => '@ptel.com'
					),
					array(
							'id' => 14,
							'cellco' => 'Suncom',
							'cellurl' => '@tms.suncom.com'
					),
					array(
							'id' => 15,
							'cellco' => 'Qwest',
							'cellurl' => '@qwestmp.com'
					),
					array(
							'id' => 16,
							'cellco' => 'U.S. Cellular',
							'cellurl' => '@email.uscc.net'
					),
					array(
							'id' => 17,
							'cellco' => 'Bell',
							'cellurl' => '@txt.bell.ca'
					),
					array(
							'id' => 18,
							'cellco' => 'Bell Mobility',
							'cellurl' => '@txt.bellmobility.ca'
					),
					array(
							'id' => 19,
							'cellco' => 'Koodo Mobile',
							'cellurl' => '@msg.koodomobile.com'
					),
					array(
							'id' => 20,
							'cellco' => 'Fido (Microcell)',
							'cellurl' => '@fido.ca'
					),
					array(
							'id' => 21,
							'cellco' => 'Manitoba Telecom Systems',
							'cellurl' => '@text.mtsmobility.com'
					),
					array(
							'id' => 22,
							'cellco' => 'NBTel',
							'cellurl' => '@wirefree.informe.ca'
					),
					array(
							'id' => 23,
							'cellco' => 'PageNet',
							'cellurl' => '@pagegate.pagenet.ca'
					),
					array(
							'id' => 24,
							'cellco' => 'Rogers',
							'cellurl' => '@pcs.rogers.com'
					),
					array(
							'id' => 25,
							'cellco' => 'Sasktel',
							'cellurl' => '@sms.sasktel.com'
					),
					array(
							'id' => 26,
							'cellco' => 'Telus',
							'cellurl' => '@msg.telus.com'
					),
					array(
							'id' => 27,
							'cellco' => 'Virgin Mobile-CA',
							'cellurl' => '@vmobile.ca'
					)					
			);

			$this->db->insert_batch('ea_cellcarrier', $data);			
		}
		
		if (!$this->db->field_exists('id_cellcarrier', 'ea_users')) {			
			$this->load->dbforge();

			$fields = [
				'id_cellcarrier' => [ 
					'type' => 'bigint',
					'constraint' => '20', 
					'default' => NULL,
					]
			]; 

			$this->dbforge->add_column('ea_users', $fields); 
			$this->db->update('ea_users', ['id_cellcarrier' => NULL]);
		}		
    }

    public function down() {
		if ($this->db->table_exists('ea_cellcarrier') ){
			  $this->dbforge->drop_table('ea_cellcarrier');
		}
		
		if ($this->db->field_exists('id_cellcarrier', 'ea_users')) {			
			$this->load->dbforge();
			$this->dbforge->drop_column('ea_users', 'id_cellcarrier');
		}		
    }
  }
?>
