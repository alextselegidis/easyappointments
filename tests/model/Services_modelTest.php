<?php

class Services_modelTest extends TestCase
{
    /** @var Services_model */
    private $services_model;
    public function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('Services_model');
        $this->services_model = $this->CI->Services_model;
    }

    /**
     * @dataProvider providerModel
     */
    public function testModel($method, $arguments, $expected)
    {
        if (is_callable($arguments)) {
            $arguments = $arguments();
        }
        $actual = call_user_func([$this->services_model, $method], ...$arguments);
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
                    $this->lastServiceId = $actual;
                }
            ],
            [
                'add',
                function() {
                    return [
                        [
                            'id' => $this->lastServiceId,
                            'name' => 'Service 01',
                            'duration' => 80,
                        ]
                    ];
                },
                function($actual) {
                    $this->assertIsNumeric($actual);
                    $this->assertEquals($this->lastServiceId, $actual);
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
                    $this->assertEquals($this->lastServiceId, $actual);
                }
            ],
            [
                'get_row',
                function() {
                    return [$this->lastServiceId];
                },
                function($actual) {
                    $expected = [
                        'name' => 'Service 01',
                        'duration' => '80',
                        'price' => '10.00',
                        'id' => (string) $this->lastServiceId,
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
                        $this->lastServiceId
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
                            'id' => (string) $this->lastServiceId,
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
                function() {
                    $CI = & get_instance();
                    $CI->load->model('Providers_model');
                    $this->lastProviderId = $CI->Providers_model->add([
                        'last_name' => 'Test',
                        'email' => 'test@test.test',
                        'phone_number' => '12345789',
                        'services' => [
                            $this->lastServiceId
                        ],
                        'settings' => [
                            'password' => 123456789
                        ]
                    ]);
                    return [];
                },
                function($actual) {
                    $expected = [
                        'name' => 'Service 01',
                        'duration' => '80',
                        'price' => '10.00',
                        'id' => (string) $this->lastServiceId,
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
                        $this->lastServiceId
                    ];
                },
                true
            ]
        ];
    }
}