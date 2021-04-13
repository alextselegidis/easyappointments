<?php

class Services_modelTest extends TestCase
{
    /** @var Services_model */
    private $model;
    public function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('Services_model');
        $this->model = $this->CI->Services_model;
    }

    /**
     * @dataProvider providerModel
     */
    public function testModel($method, $arguments, $expected)
    {
        if (is_callable($arguments)) {
            $arguments = $arguments();
        }
        $actual = call_user_func([$this->model, $method], ...$arguments);
        if (is_callable($expected)) {
            $expected($actual);
        } else {
            $this->assertEquals($expected, $actual);
        }
    }

    public function providerModel()
    {
        return [
            [
                'add',
                [
                    [
                        'name' => 'Service 01',
                        'duration' => 60,
                        'price' => 10.00
                    ]
                ],
                function($actual) {
                    $this->assertIsNumeric($actual);
                    $this->lastId = $actual;
                }
            ],
            [
                'add',
                function() {
                    return [
                        [
                            'id' => $this->lastId,
                            'name' => 'Service 01',
                            'duration' => 80,
                        ]
                    ];
                },
                function($actual) {
                    $this->assertIsNumeric($actual);
                    $this->assertEquals($this->lastId, $actual);
                }
            ],
            [
                'exists',
                [
                    [
                        'name' => 'Service 01',
                        'duration' => 80,
                        'price' => 10.00
                    ]
                ],
                true
            ],
            [
                'find_record_id',
                [
                    [
                        'name' => 'Service 01',
                        'duration' => 80,
                        'price' => 10.00
                    ]
                ],
                function($actual) {
                    $this->assertIsNumeric($actual);
                    $this->assertEquals($this->lastId, $actual);
                }
            ],
            [
                'get_row',
                function() {
                    return [$this->lastId];
                },
                function($actual) {
                    $expected = [
                        'name' => 'Service 01',
                        'duration' => '80',
                        'price' => '10.00',
                        'id' => (string) $this->lastId,
                        'currency' => null,
                        'description' => null,
                        'location' => null,
                        'availabilities_type' => 'flexible',
                        'attendants_number' => '1',
                        'id_service_categories' => null,
                        'slug' => null
                    ];
                    $this->assertEquals($expected, $actual);
                }
            ],
            [
                'get_value',
                function() {
                    return [
                        'name',
                        $this->lastId
                    ];
                },
                'Service 01'
            ],
            [
                'get_batch',
                [
                    ['name' => 'Service 01'],
                    null,
                    null,
                    null
                ],
                function ($actual) {
                    $expected = [
                        [
                            'name' => 'Service 01',
                            'duration' => '80',
                            'price' => '10.00',
                            'id' => (string) $this->lastId,
                            'currency' => null,
                            'description' => null,
                            'location' => null,
                            'availabilities_type' => 'flexible',
                            'attendants_number' => '1',
                            'id_service_categories' => null,
                            'slug' => null
                        ]
                    ];
                    $this->assertEquals($expected, $actual);
                }
            ],
            [
                'get_available_services',
                [null],
                function($actual) {
                    $expected = [
                        'name' => 'Service 01',
                        'duration' => '80',
                        'price' => '10.00',
                        'id' => (string) $this->lastId,
                        'currency' => null,
                        'description' => null,
                        'location' => null,
                        'availabilities_type' => 'flexible',
                        'attendants_number' => '1',
                        'id_service_categories' => null,
                        'category_name' => null,
                        'category_id' => null,
                        'slug' => null
                    ];
                    $this->assertGreaterThan(0, count($actual));
                    $this->assertEquals($expected, end($actual));
                }
            ],
            [
                'delete',
                function () {
                    return [
                        $this->lastId
                    ];
                },
                true
            ]
        ];
    }
}