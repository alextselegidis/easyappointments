<?php

namespace Tests\Unit\Helper;

use Tests\TestCase;

class ValidationHelperTest extends TestCase
{
    public function testValidateDateTimeReturnsTrueOnValidValue()
    {
        $this->assertTrue(validate_datetime(date('Y-m-d H:i:s')));
    }

    public function testValidateDateTimeReturnsFalseOnInvalidValue()
    {
        $this->assertFalse(validate_datetime('invalid'));
    }

    public function testValidateDateTimeReturnsFalseOnOutOfRangeValue()
    {
        $this->assertFalse(validate_datetime('2024-02-30 10:00:00'));
        $this->assertFalse(validate_datetime('2024-13-01 25:61:00'));
    }

    public function testValidateColorAcceptsHexValues()
    {
        $this->assertTrue(validate_color('#7cbae8'));
        $this->assertTrue(validate_color('#FFF'));
    }

    public function testValidateColorRejectsNonHexValues()
    {
        $this->assertFalse(validate_color('javascript:alert(1)'));
        $this->assertFalse(validate_color('red" onmouseover="x'));
        $this->assertFalse(validate_color('#12345'));
    }
}
