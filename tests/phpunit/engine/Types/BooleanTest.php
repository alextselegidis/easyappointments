<?php

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Types;

class BooleanTest extends \PHPUnit_Framework_TestCase {
    public function testBoolType()
    {
        $type = new Boolean(TRUE);
        $this->assertEquals(TRUE, $type->get());
    }

    public function testBoolTypeThrowsExceptionWithInvalidValue()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new Boolean(NULL);
    }
}
