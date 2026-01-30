<?php

namespace Tests\Unit\Helper;

use Tests\TestCase;

class ValidationHelperTest extends TestCase {
    public function testValidateDateTimeReturnsTrueOnValidValue()
    {
        $this->assertTrue(validate_datetime(date('Y-m-d H:i:s')));
    }

    public function testValidateDateTimeReturnsFalseOnInvalidValue()
    {
        $this->assertFalse(validate_datetime('invalid'));
    }
}
