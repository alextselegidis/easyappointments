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

class AuthorizationTest extends \PHPUnit_Framework_TestCase {
    public function testIntType() {
        $type = new Int(1); 
        $this->assertEquals(1, $type->get()); 
    } 

    public function testIntTypeThrowsExceptionWithInvalidValue() {
        $this->setExpectedException('\InvalidArgumentException');
        new Int('invalid');
    }
}
