<?php

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Api\V1;

use CI_Controller;
use EA\Engine\Types\NonEmptyText;
use EA_Controller;

/**
 * API v1 Authorization Class
 *
 * This class will handle the authorization procedure of the API.
 *
 * @deprecated
 */
class Authorization {
    /**
     * Framework Instance
     *
     * @var EA_Controller
     */
    protected $CI;

    /**
     * Class Constructor
     *
     * @param CI_Controller $CI
     */
    public function __construct(EA_Controller $CI)
    {
        $this->CI = $CI;
    }

    /**
     * Perform Basic Authentication
     *
     * @param NonEmptyText $username Admin Username
     * @param NonEmptyText $password Admin Password
     *
     * @throws \EA\Engine\Api\V1\Exception Throws 401-Unauthorized exception if the authentication fails.
     */
    public function basic(NonEmptyText $username, NonEmptyText $password)
    {
        $this->CI->load->model('user_model');

        if ( ! $this->CI->user_model->check_login($username->get(), $password->get()))
        {
            throw new Exception('The provided credentials do not match any admin user!', 401, 'Unauthorized');
        }
    }
}
