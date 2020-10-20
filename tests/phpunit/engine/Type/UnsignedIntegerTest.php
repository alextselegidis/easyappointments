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

class UnsignedIntegerTest extends TestCase {
    public function testUnsignedIntType()
    {
        $type = new UnsignedInteger(1);
        $this->assertEquals(1, $type->get());
    }

    public function testUnsignedIntTypeThrowsExceptionWithNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        new UnsignedInteger(-1);
    }

    public function testUnsignedIntTypeThrowsExceptionWithWithString()
    {
        $this->expectException(\InvalidArgumentException::class);
        new UnsignedInteger('invalid');
    }
}
