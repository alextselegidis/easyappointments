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

namespace \EA\Engine\Types; 

/**
 * Abstract Type Class 
 *
 * This class needs to be extended by the type classes which must implement the validation logic.
 */
abstract class Type {
    /**
     * Class Constructor 
     * 
     * @param mixed $value The type value to be validated.
     */
    public function __construct($value) {
        $this->_validate($value);
        $this->value = $value; 
    }

    /**
     * Get the type value. 
     * 
     * @return mixed
     */
    public function get() {
        return $this->value;
    }

    /**
     * Implement the validation logic in the children classes.
     * 
     * @throws \InvalidArgumentException If the validation fails.
     */
    abstract protected function _validate();
}
