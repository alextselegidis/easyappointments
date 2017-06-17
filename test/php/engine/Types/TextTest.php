<?php 

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Types; 

class TextTest extends \PHPUnit_Framework_TestCase {
    public function testStringType() {
        $type = new Text('Hello!');
        $this->assertEquals('Hello!', $type->get()); 
    } 

    public function testStringTypeThrowsExceptionWithInvalidValue() {
        $this->setExpectedException('\InvalidArgumentException');
        new Text(null);
    }
}
