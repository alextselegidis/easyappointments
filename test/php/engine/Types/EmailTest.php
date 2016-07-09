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

class EmailTest extends \PHPUnit_Framework_TestCase {
    public function testEmailType() {
        $type = new Email('john@doe.com'); 
        $this->assertEquals('john@doe.com', $type->get()); 
    } 

    public function testEmailTypeThrowsExceptionWithInvalidEmail() {
        $this->setExpectedException('\InvalidArgumentException');
        new Email('abcdef');
    }

    public function testEmailTypeThrowsExceptionWithInvalidValue() {
        $this->setExpectedException('\InvalidArgumentException');
        new Email(null);
    }
}
