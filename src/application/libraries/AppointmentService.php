<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.0
 * ---------------------------------------------------------------------------- */

 class AppointmentService {

    private $ci;

    public function __construct()
    {
        log_message('info', 'Creating new instance of AppointmentService...');
        $this->ci =& get_instance();
        $this->ci->load->model('services_model');
        $this->ci->load->model('appointments_model');
        log_message('info', 'AppointmentService created!');
    }

    function service1(){
        log_message('info', 'Calling my library service1');
    }

 }