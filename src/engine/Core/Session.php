<?php

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Core;

/**
 * Class Session
 *
 * @package EA\Engine\Core
 */
class Session {
    /**
     * @var \EA\Engine\Core\Framework
     */
    private $framework;

    /**
     * Session constructor.
     *
     * @param Framework $framework
     */
    public function __construct(Framework $framework)
    {
        $this->framework = $framework;
    }


    /**
     * Create a new session for the current user.
     */
    public function create()
    {
        $this->framework->load->library('session');
    }

    /**
     * Destroy the session of the current user.
     */
    public function destroy()
    {
        $this->framework->session->sess_destroy();
    }

    /**
     * Get a session value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->framework->session->userdata($key);
    }

    /**
     * Set a session value.
     *
     * @param string $key
     *
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->framework->session->userdata($key, $value);
    }
}
