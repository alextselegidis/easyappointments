<?php

namespace Unit\Helper;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../application/helpers/validation_helper.php';

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
