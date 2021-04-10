<?php

class ServicesTest extends TestCase
{
	/**
	 * @dataProvider providerRequest
	 */
	public function testRequest($method, $route, $requestBody, $expected)
	{
		$output = $this->request($method, $route, $requestBody);
		$this->assertEquals($expected, $output);
	}
	
	public function providerRequest()
	{
		return [
			[
				'GET',
				'api/v1/services/get',
				null,
				<<<EXPECTED
				[
				    {
				        "id": 1,
				        "name": "Service",
				        "duration": 30,
				        "price": 0,
				        "currency": "",
				        "description": null,
				        "location": null,
				        "availabilitiesType": "flexible",
				        "attendantsNumber": 1,
				        "categoryId": null
				    }
				]
				EXPECTED
			],
			[
				'GET',
				'api/v1/services/get/1',
				null,
				<<<EXPECTED
				{
				    "id": 1,
				    "name": "Service",
				    "duration": 30,
				    "price": 0,
				    "currency": "",
				    "description": null,
				    "location": null,
				    "availabilitiesType": "flexible",
				    "attendantsNumber": 1,
				    "categoryId": null
				}
				EXPECTED
			],
			[
				'GET',
				'api/v1/services/get/invalid',
				null,
				<<<EXPECTED
				{
				    "code": 404,
				    "message": "The requested record was not found!"
				}
				EXPECTED
			],
			[
				'POST',
				'api/v1/services/post',
				<<<REQUEST_BODY
				{
				    "name": "Service 02",
				    "duration": 30,
				    "price": 0,
				    "currency": "",
				    "description": null,
				    "location": null,
				    "availabilitiesType": "flexible",
				    "attendantsNumber": 1,
				    "categoryId": null
				}
				REQUEST_BODY,
				<<<EXPECTED
				{
				    "id": 2,
				    "name": "Service 02",
				    "duration": 30,
				    "price": 0,
				    "currency": "",
				    "description": null,
				    "location": null,
				    "availabilitiesType": "flexible",
				    "attendantsNumber": 1,
				    "categoryId": null
				}
				EXPECTED
			]
		];
	}
}
