<?php

class ServicesTest extends TestCase
{
	public function testGetWithoutId()
	{
		$output = $this->request('GET', 'api/v1/services/get');
		$expected = <<<INPUT
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
		INPUT;
		$this->assertEquals($expected, $output);
	}

	public function testGetWithId()
	{
		$output = $this->request('GET', 'api/v1/services/get/1');
		$expected = <<<INPUT
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
		INPUT;
		$this->assertEquals($expected, $output);
	}

	public function testGetWithInvalidId()
	{
		$output = $this->request('GET', 'api/v1/services/get/invalid');
		$expected = <<<INPUT
		{
		    "code": 404,
		    "message": "The requested record was not found!"
		}
		INPUT;
		$this->assertEquals($expected, $output);
	}

	public function testPostSuccess()
	{
		$requestBody = <<<INPUT
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
		INPUT;
		$output = $this->request('POST', 'api/v1/services/post', $requestBody);
		$expected = <<<INPUT
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
		INPUT;
		$this->assertEquals($expected, $output);
	}
}
