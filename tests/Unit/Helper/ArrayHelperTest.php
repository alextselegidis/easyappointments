<?php

namespace Tests\Unit\Helper;

use Tests\TestCase;

class ArrayHelperTest extends TestCase {
    public function testIsAssocReturnsTrueOnAssociativeArray(): void
    {
        $this->assertTrue(is_assoc(['test' => 'value']));
    }

    public function testIsAssocReturnsFalseOnIndexedArray(): void
    {
        $this->assertFalse(is_assoc(['one', 'two', 'three']));
    }

    public function testIsAssocReturnsTrueOnMixedArray(): void
    {
        $this->assertTrue(is_assoc(['one', 'two', 'three' => 'value']));
    }

    public function testArrayFindReturnsCorrectElement(): void
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

    public function testArrayFieldsReturnsStrippedArray(): void
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
