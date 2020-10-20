<?php

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Type;

use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase {
    public function testIntType()
    {
        $type = new Integer(1);
        $this->assertEquals(1, $type->get());
    }

    public function testIntTypeThrowsExceptionWithFloat()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Integer(100.00);
    }

    public function testIntTypeThrowsExceptionWithWithString()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Integer('invalid');
    }
}
