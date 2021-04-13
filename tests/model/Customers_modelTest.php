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
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'email' => 'john@example.org',
                        'phone_number' => '123456789',
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
                            'first_name' => 'Juliet',
                            'last_name' => 'Doe',
                            'email' => 'juliet@example.org',
                            'phone_number' => '987654321',
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
                        'first_name' => 'Juliet',
                        'last_name' => 'Doe',
                        'email' => 'juliet@example.org',
                        'phone_number' => '987654321',
                        'id' => (string) $this->lastId,
                        'mobile_number' => null,
                        'address' => null,
                        'city' => null,
                        'state' => null,
                        'zip_code' => null,
                        'notes' => null,
                        'timezone' => 'UTC',
                        'language' => 'english',
                        'id_roles' => '3'
                    ];
                    $this->assertEquals($expected, $actual);
                }
            ],
            [
                'get_value',
                function() {
                    return [
                        'first_name',
                        $this->lastId
                    ];
                },
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
                function ($actual) {
                    $expected = [
                        [
                            'first_name' => 'Juliet',
                            'last_name' => 'Doe',
                            'email' => 'juliet@example.org',
                            'phone_number' => '987654321',
                            'id' => (string) $this->lastId,
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
                    ];
                    $this->assertEquals($expected, $actual);
                }
            ]
        ];
    }
}