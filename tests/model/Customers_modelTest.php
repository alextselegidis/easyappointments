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
                5
            ],
            [
                'add',
                [
                    [
                        'id' => 5,
                        'first_name' => 'Jane',
                        'last_name' => 'Doe',
                        'email' => 'Jane@example.org',
                        'phone_number' => '987654321',
                    ]
                ],
                5
            ],
            [
                'exists',
                [
                    [
                        'first_name' => 'Jane',
                        'last_name' => 'Doe',
                        'email' => 'Jane@example.org',
                        'phone_number' => '987654321'
                    ]
                ],
                true
            ],
            [
                'find_record_id',
                [
                    [
                        'email' => 'Jane@example.org'
                    ]
                ],
                5
            ],
            [
                'get_row',
                [
                    5
                ],
                [
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'email' => 'Jane@example.org',
                    'phone_number' => '987654321',
                    'id' => '5',
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
        ];
    }
}