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
 * Class Input
 *
 * @package EA\Engine\Core
 */
class Input {
    /**
     * @var \EA\Engine\Core\Framework
     */
    private $framework;

    /**
     * Input constructor.
     *
     * @param Framework $framework
     */
    public function __construct(Framework $framework)
    {
        $this->framework = $framework;
    }


    /**
     * Get request (either GET or POST) parameter (not filtered/escaped).
     *
     * @param string $key
     *
     * @return string
     */
    public function request($key)
    {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : NULL;
    }

    /**
     * Get GET parameter value.
     *
     * @param string $key
     */
    public function get($key)
    {
        $this->framework->input->get($key);
    }

    /**
     * Get POST parameter value.
     *
     * @param string $key
     */
    public function post($key)
    {
        $this->framework->input->post($key);
    }

    /**
     * Get COOKIE parameter value.
     *
     * @param string $key
     *
     * @return string
     */
    public function cookie($key)
    {
        return $this->framework->input->cookie($key);
    }

    /**
     * Get SERVER parameter value.
     *
     * @param string $key
     *
     * @return string
     */
    public function server($key)
    {
        return $this->framework->input->server($key);
    }

    /**
     * Get raw request input.
     *
     * @return string
     */
    public function raw()
    {
        return $this->framework->input->raw_input_stream;
    }

    /**
     * Get json decoded raw request input.
     *
     * @return string
     */
    public function json()
    {
        return json_decode($this->framework->input->raw_input_stream);
    }

    /**
     * Get request headers as an array.
     *
     * @return array
     */
    public function headers()
    {
        return $this->framework->input->request_headers();
    }

    /**
     * Get request header value.
     *
     * @param string $key
     *
     * @return string
     */
    public function header($key)
    {
        return $this->framework->input->get_request_header($key);
    }

    /**
     * Determine if this is a CLI request or not.
     *
     * @return bool
     */
    public function isCliRequest()
    {
        return $this->framework->is_cli_request();
    }
}
