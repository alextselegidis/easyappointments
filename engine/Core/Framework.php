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
 * Class Framework
 *
 * @package EA\Engine\Core
 */
class Framework {
    /**
     * Get the framework instance.
     *
     * @return \CI_Controller
     */
    public static function getInstance()
    {
        return get_instance();
    }

    /**
     * Magit get method that gives access to the internal members of the framework intance.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return self::getInstance()->$key;
    }
}
