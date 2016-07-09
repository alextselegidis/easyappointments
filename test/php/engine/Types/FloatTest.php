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

namespace EA\Engine\Types; 

class FloatTest extends \PHPUnit_Framework_TestCase {
    public function testFloatType() {
        $type = new Float(100.00); 
        $this->assertEquals(100.00, $type->get()); 
    } 

    public function testFloatTypeThrowsExceptionWithInvalidValue() {
        $this->setExpectedException('\InvalidArgumentException');
        new Float(null);
    }
}
