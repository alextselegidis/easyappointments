<?php

class CustomersTest extends TestCase
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
				'POST',
				'api/v1/customers/post',
				<<<REQUEST_BODY
				{
				    "firstName": "John",
				    "lastName": "Doe",
				    "email": "john@doe.com",
				    "phone": "0123456789",
				    "address": "Some Str. 123",
				    "city": "Some City",
				    "zip": "12345",
				    "notes": "Test customer notes."
				}
				REQUEST_BODY,
				<<<EXPECTED
				{
				    "id": 4,
				    "firstName": "John",
				    "lastName": "Doe",
				    "email": "john@doe.com",
				    "phone": "0123456789",
				    "address": "Some Str. 123",
				    "city": "Some City",
				    "zip": "12345",
				    "notes": "Test customer notes."
				}
				EXPECTED
			],
			[
				'GET',
				'api/v1/customers/get',
				null,
				<<<EXPECTED
				[
				    {
				        "id": 3,
				        "firstName": "James",
				        "lastName": "Doe",
				        "email": "james@example.org",
				        "phone": "+1 (000) 000-0000",
				        "address": null,
				        "city": null,
				        "zip": null,
				        "notes": null
				    },
				    {
				        "id": 4,
				        "firstName": "John",
				        "lastName": "Doe",
				        "email": "john@doe.com",
				        "phone": "0123456789",
				        "address": "Some Str. 123",
				        "city": "Some City",
				        "zip": "12345",
				        "notes": "Test customer notes."
				    }
				]
				EXPECTED
			],
			[
				'GET',
				'api/v1/customers/get/4',
				null,
				<<<EXPECTED
				{
				    "id": 4,
				    "firstName": "John",
				    "lastName": "Doe",
				    "email": "john@doe.com",
				    "phone": "0123456789",
				    "address": "Some Str. 123",
				    "city": "Some City",
				    "zip": "12345",
				    "notes": "Test customer notes."
				}
				EXPECTED
			],
			[
				'GET',
				'api/v1/customers/get/invalid',
				null,
				<<<EXPECTED
				{
				    "code": 404,
				    "message": "The requested record was not found!"
				}
				EXPECTED
			],
			[
				'PUT',
				'api/v1/customers/put/4',
				<<<REQUEST_BODY
				{
				    "firstName": "Jane",
				    "lastName": "Doe",
				    "email": "jane@doe.com",
				    "phone": "9876543210",
				    "address": "Some Str. 3221",
				    "city": "Some Test City",
				    "zip": "54321",
				    "notes": "New test customer notes."
				}
				REQUEST_BODY,
				<<<EXPECTED
				{
				    "id": 4,
				    "firstName": "Jane",
				    "lastName": "Doe",
				    "email": "jane@doe.com",
				    "phone": "9876543210",
				    "address": "Some Str. 3221",
				    "city": "Some Test City",
				    "zip": "54321",
				    "notes": "New test customer notes."
				}
				EXPECTED
			],
			[
				'DELETE',
				'api/v1/customers/delete/2',
				null,
				<<<EXPECTED
				{
				    "code": 200,
				    "message": "Record was deleted successfully!"
				}
				EXPECTED
			]
		];
	}
}
