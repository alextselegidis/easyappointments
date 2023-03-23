<?php

namespace Unit\Helper;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../application/helpers/array_helper.php';

class TestArrayHelper extends TestCase {
    public function test_is_assoc_returns_true_on_associative_array()
    {
        $this->assertTrue(is_assoc(['test' => 'value']));
    }

    public function test_is_assoc_returns_false_on_indexed_array()
    {
        $this->assertFalse(is_assoc(['one', 'two', 'three']));
    }

    public function test_is_assoc_returns_true_on_mixed_array()
    {
        $this->assertTrue(is_assoc(['one', 'two', 'three' => 'value']));
    }

    public function test_array_find_returns_correct_element()
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

    public function test_array_fields_returns_stripped_array()
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
