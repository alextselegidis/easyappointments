<?php 

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Api\V1; 

use \EA\Engine\Types\NonEmptyText;

/**
 * API v1 Authorization Class 
 *
 * This class will handle the authorization procedure of the API.
 */
class Authorization {
    /**
     * Framework Instance 
     * 
     * @var CI_Controller
     */
    protected $framework; 

    /**
     * Class Constructor 
     * 
     * @param \CI_Controller $framework
     */
    public function __construct(\CI_Controller $framework) {
        $this->framework = $framework;
    }

    /**
     * Perform Basic Authentication
     * 
     * @param NonEmptyText $username Admin Username
     * @param NonEmptyText $password Admin Password
     *
     * @throws \EA\Engine\Api\V1\Exception Throws 401-Unauthorized exception if the authentication fails.
     */
    public function basic(NonEmptyText $username, NonEmptyText $password) {
        $this->framework->load->model('user_model'); 

        if (!$this->framework->user_model->check_login($username->get(), $password->get())) { 
            throw new Exception('The provided credentials do not match any admin user!', 401, 'Unauthorized');
        }
    }
}
