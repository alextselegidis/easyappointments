<?php

class Customers_modelTest extends TestCase
{
    /** @var Services_model */
    private $model;
    public function setUp(): void
    {
        parent::setUp();
        $this->resetInstance();
        $this->CI->load->model('Customers_model');
        $this->model = $this->CI->Customers_model;
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
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'email' => 'john@example.org',
                        'phone_number' => '123456789',
                    ]
                ],
                4
            ],
            [
                'add',
                [
                    [
                        'id' => 4,
                        'first_name' => 'Juliet',
                        'last_name' => 'Doe',
                        'email' => 'juliet@example.org',
                        'phone_number' => '987654321',
                    ]
                ],
                4
            ],
            [
                'exists',
                [
                    [
                        'first_name' => 'Juliet',
                        'last_name' => 'Doe',
                        'email' => 'juliet@example.org',
                        'phone_number' => '987654321'
                    ]
                ],
                true
            ],
            [
                'find_record_id',
                [
                    [
                        'email' => 'juliet@example.org'
                    ]
                ],
                4
            ],
            [
                'get_row',
                [
                    4
                ],
                [
                    'first_name' => 'Juliet',
                    'last_name' => 'Doe',
                    'email' => 'juliet@example.org',
                    'phone_number' => '987654321',
                    'id' => '4',
                    'mobile_number' => null,
                    'address' => null,
                    'city' => null,
                    'state' => null,
                    'zip_code' => null,
                    'notes' => null,
                    'timezone' => 'UTC',
                    'language' => 'english',
                    'id_roles' => '3'
                ]
            ],
            [
                'get_value',
                [
                    'first_name',
                    4
                ],
                'Juliet'
            ],
            [
                'get_batch',
                [
                    ['first_name' => 'Juliet'],
                    null,
                    null,
                    null
                ],
                [
                    [
                        'first_name' => 'Juliet',
                        'last_name' => 'Doe',
                        'email' => 'juliet@example.org',
                        'phone_number' => '987654321',
                        'id' => '4',
                        'mobile_number' => null,
                        'address' => null,
                        'city' => null,
                        'state' => null,
                        'zip_code' => null,
                        'notes' => null,
                        'timezone' => 'UTC',
                        'language' => 'english',
                        'id_roles' => '3'
                    ]
                ]
            ]
        ];
    }
}