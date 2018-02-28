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

class UrlTest extends \PHPUnit_Framework_TestCase {
    public function testUrlType()
    {
        $type = new Url('http://localhost');
        $this->assertEquals('http://localhost', $type->get());
    }

    public function testUrlTypeThrowsExceptionWithInvalidUrl()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new Url('abcdef');
    }

    public function testUrlTypeThrowsExceptionWithInvalidValue()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new Url(NULL);
    }
}
