<?php

namespace Unit\Helper;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../application/helpers/array_helper.php';

class ArrayHelperTest extends TestCase {
    public function testIsAssocReturnsTrueOnAssociativeArray()
    {
        $this->assertTrue(is_assoc(['test' => 'value']));
    }

    public function testIsAssocReturnsFalseOnIndexedArray()
    {
        $this->assertFalse(is_assoc(['one', 'two', 'three']));
    }

    public function testIsAssocReturnsTrueOnMixedArray()
    {
        $this->assertTrue(is_assoc(['one', 'two', 'three' => 'value']));
    }

    public function testArrayFindReturnsCorrectElement()
    {
        $arr = [
            [
                'id' => 1
            ],
            [
                'id' => 2
            ],
            [
                'id' => 3
            ],
        ];

        $this->assertSame($arr[0], array_find($arr, fn($element) => $element['id'] === 1));
    }

    public function testArrayFieldsReturnsStrippedArray()
    {
        $arr = [
            'name' => 'John',
            'email' => 'john@example.org',
        ];

        $stripped = array_fields($arr, ['name']);

        $this->assertArrayHasKey('name', $stripped);
        
        $this->assertArrayNotHasKey('email', $stripped);
    }
}
