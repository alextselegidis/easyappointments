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
        $output = call_user_func_array([$this->model, $method], [$arguments]);
        $this->assertEquals($expected, $output);
    }

    public function providerModel()
    {
        return [
            [
                'add',
                [
                    'name' => 'Service 02',
                ],
                3
            ]
        ];
    }
}