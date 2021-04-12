<?php

class Services_modelTest extends TestCase
{
    /** @var Services_model */
    private $model;
    public function setUp(): void
    {
        parent::setUp();
        $this->resetInstance();
        $this->CI->load->model('Services_model');
        $this->model = $this->CI->Services_model;
    }

    /**
     * @dataProvider providerModel
     */
    public function testModel($method, $arguments, $expected)
    {
        $output = call_user_func([$this->model, $method], ...$arguments);
        $this->assertEquals($expected, $output);
    }

    public function providerModel()
    {
        return [
            [
                'add',
                [
                    [
                        'name' => 'Service 02',
                        'duration' => 60,
                        'price' => 10.00
                    ]
                ],
                2
            ],
            [
                'add',
                [
                    [
                        'id' => 2,
                        'name' => 'Service 02',
                        'duration' => 80,
                    ]
                ],
                2
            ],
            [
                'exists',
                [
                    [
                        'name' => 'Service 02',
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
                        'name' => 'Service 02',
                        'duration' => 80,
                        'price' => 10.00
                    ]
                ],
                2
            ],
            [
                'get_row',
                [
                    2
                ],
                [
                    'name' => 'Service 02',
                    'duration' => '80',
                    'price' => '10.00',
                    'id' => '2',
                    'currency' => null,
                    'description' => null,
                    'location' => null,
                    'availabilities_type' => 'flexible',
                    'attendants_number' => '1',
                    'id_service_categories' => null
                ]
            ],
            [
                'get_value',
                [
                    'name',
                    2
                ],
                'Service 02'
            ],
            [
                'get_batch',
                [
                    ['name' => 'Service 02'],
                    null,
                    null,
                    null
                ],
                [
                    [
                        'name' => 'Service 02',
                        'duration' => '80',
                        'price' => '10.00',
                        'id' => '2',
                        'currency' => null,
                        'description' => null,
                        'location' => null,
                        'availabilities_type' => 'flexible',
                        'attendants_number' => '1',
                        'id_service_categories' => null
                    ]
                ]
            ],
            [
                'get_available_services',
                [null],
                [
                    [
                        'name' => 'Service',
                        'duration' => '30',
                        'price' => '0.00',
                        'id' => '1',
                        'currency' => '',
                        'description' => null,
                        'location' => null,
                        'availabilities_type' => 'flexible',
                        'attendants_number' => '1',
                        'id_service_categories' => null,
                        'category_name' => null,
                        'category_id' => null
                    ],
                    [
                        'name' => 'Service 02',
                        'duration' => '80',
                        'price' => '10.00',
                        'id' => '2',
                        'currency' => null,
                        'description' => null,
                        'location' => null,
                        'availabilities_type' => 'flexible',
                        'attendants_number' => '1',
                        'id_service_categories' => null,
                        'category_name' => null,
                        'category_id' => null
                    ]
                ]
            ],
            [
                'delete',
                [
                    2
                ],
                true
            ]
        ];
    }
}