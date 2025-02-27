# REST API

## Introduction 

Easy!Appointments offers a flexible REST API that will enables you to handle all the information of your installations through HTTP requests. The API is using JSON as its data transaction format and features many best practices in order to make the resources easily consumable. 

### Open API Specification (Swagger File)

The project has a ready-made [OpenApi file](https://raw.githubusercontent.com/alextselegidis/easyappointments/master/openapi.yml) you can download and use in order to create your client. 

This file can also be imported into Postman, so that you can quickly get started making requests towards your installation. 

You will find more information about Open API and Swagger on [swagger.io](https://swagger.io).

## Making Requests

The API (v1) supports [Basic Authentication](https://en.wikipedia.org/wiki/Basic_access_authentication) which means that you will have to send the "Authorization" header with every request you make. **Always use SSL/TLS when making requests to a production installation.** That way you can ensure that no passwords will be stolen during the requests. The API expects the username and password of an administrator user.

Additionally you can configure your own API key in the settings page and pass it through as a Bearer Token with any of your requests.   

The API follows the REST structure which means that the client can use various HTTP verbs in order to perform various operations to the resources. For example you should use a GET request for fetching resources, a POST for creating new and PUT for updating existing ones in the database. Finally a DELETE request will remove a resource from the system. 

GET requests accept some parameter helpers that enable the sort, search, pagination and minification of the responses information. Take a look in the following examples: 

### Search

Provide the `q` parameter to perform a search in the resource.

```
http://ea-installation/index.php/api/v1/appointments?q=keyword
```

### Sort 

Sort the results in ascending (+) or descending (-) direction by providing the the respective sign and the property name to be used for sorting. 

```
http://ea-installation/index.php/api/v1/appointments?sort=-id,+book,-hash
```

You can provide up to three sorting fields which will be applied in the provided order. 

### Paginate

Paginate the result by providing the `page` parameter along with the optional `length` parameter that defaults to 20. 

```
http://ea-installation/index.php/api/v1/appointments?page=1&length=10
```

### Minimize

If you need to get only specific values from each JSON resource provide the `fields` GET parameter with a list of the required property names. 

```
http://ea-installation/index.php/api/v1/appointments?fields=id,book,hash,notes
```

### Aggregate (Deprecated)

Aggregate related data into result payload by providing the `aggregates` parameter.

> Notice: this parameter only applies to appointments and will be removed in the future (use the `with` paramter instead).  

```
http://ea-installation/index.php/api/v1/appointments?aggregates
```

*This parameter is currently only available for appointment resources.* 

### With

Attach resources to the response payload with this parameter.

```
http://ea-installation/index.php/api/v1/appointments?with=customer,service,provider
```

*This parameter is only available for resources that are related to other resources.* 

### Expected Responses
 
Most of the times the API will return the complete requested data in a JSON string but there are some cases that the responses will contain a simple message like the following: 

```
{
    "code": 404,
    "message": "The requested record was not found!"
}
```

Such simple messages contain the HTTP code and a message stating a problem or a success to an operation.

### Try it out!

At this point you can start experimenting with the API and your installation. The following section of this document describes the available resources and how they can be used. Before building your API consumer you can use [cURL](https://en.wikipedia.org/wiki/CURL) or [Postman](https://chrome.google.com/webstore/detail/postman/fhbjgbiflinjbdggehcddcbncdddomop) to try out the API. 

Get all the registered appointments: 

```
curl http://ea-installation/index.php/api/v1/appointments --user username:password
```

Get the data of a customer with ID 34: 

```
curl http://ea-installation/index.php/api/v1/customers/34 --user username:password
```

Update the name of a service category with ID 23: 

```
curl -H 'Content-Type: application/json' -X PUT -d '{"name": "New Name!"}' http://ea-installation/index.php/api/v1/service_categories/23 --user username:password
```

Delete the service with ID 15: 

```
curl -X DELETE http://ea-installation/index.php/api/v1/services/15 --user username:password
```

You can also try the GET requests with your browser by navigating to the respective URLs.

## Resources & URIs

### Availabilities 

**Resource JSON**

```
[
    "09:30",
    "13:00",
    "13:15",
    "14:00"
]
```

- `GET /api/v1/availabilities?providerId=:id&serviceId=:id[&date=:date]` Get the available appointment hours for a specific provider, service and date. The date must be in the following format `Y-m-d` e.g. `2016-07-19`.

### Appointments

**Resource JSON**

```
{
    "id": 1, 
    "book": "2016-07-08 12:57:00", 
    "start": "2016-07-08 18:00:00", 
    "end": "2016-07-08 18:30:00", 
    "hash": "apTWVbSvBJXR", 
    "location": "Test Street 1A, 12345 Some State, Some Place"
    "notes": "This is a test appointment.",
    "serviceId": 1,
    "providerId": 2,
    "customerId": 3,
    "googleCalendarId": null
}
```

- `GET /api/v1/appointments[/:id]` Get all the appointments or a specific one by providing the ID in the URI. 
- `POST /api/v1/appointments` Provide the new appointment JSON in the request body to insert a new record. 
- `PUT /api/v1/appointments/:id` Provide the updated appointment JSON in the request body to update an existing record. The ID in the URI is required. 
- `DELETE /api/v1/appointments/:id` Remove an existing appointment record.

### Unavailabilities

**Resource JSON**

```
{
    "id": 1, 
    "book": "2016-07-08 12:57:00", 
    "start": "2016-07-08 18:00:00", 
    "end": "2016-07-08 18:30:00",
    "hash": "apTWVbSvBJXR",
    "location": "Test Street 1A, 12345 Some State, Some Place", 
    "notes": "This is a test unavailability.",
    "providerId": 1,
    "googleCalendarId": null
}
```

- `GET /api/v1/unavailabilities[/:id]` Get all the unavailabilities or a specific one by providing the ID in the URI. 
- `POST /api/v1/unavailabilities` Provide the new unavailability JSON in the request body to insert a new record. 
- `PUT /api/v1/unavailabilities/:id` Provide the updated unavailability JSON in the request body to update an existing record. The ID in the URI is required. 
- `DELETE /api/v1/unavailabilities/:id` Remove an existing unavailability record.

### Customers

**Resource JSON**

```
{
    "id": 1,
    "firstName": "John", 
    "lastName": "Doe", 
    "email": "john@example.org", 
    "phone": "+10000000000",
    "address": "Test Street 1A", 
    "city": "Some Place", 
    "zip": "12345", 
    "timezone": "UTC", 
    "language": "english", 
    "notes": "This is a test customer."
}
```

- `GET /api/v1/customers[/:id]` Get all the customers or a specific one by providing the ID in the URI. 
- `POST /api/v1/customers` Provide the new customer JSON in the request body to insert a new record. 
- `PUT /api/v1/customers/:id` Provide the updated customer JSON in the request body to update an existing record. The ID in the URI is required. 
- `DELETE /api/v1/customers/:id` Remove an existing customer record.

### Services

**Resource JSON**

```
{
    "id": 1, 
    "name": "Test Service", 
    "duration": 15, 
    "price": 0.00,
    "currency": "EUR", 
    "description": "This is a test service.",
    "availabilitiesType": "flexible",
    "attendantsNumber": 1,
    "serviceCategoryId": null
}
```

- `GET /api/v1/services[/:id]` Get all the services or a specific one by providing the ID in the URI. 
- `POST /api/v1/services` Provide the new service JSON in the request body to insert a new record. 
- `PUT /api/v1/services/:id` Provide the updated service JSON in the request body to update an existing record. The ID in the URI is required. 
- `DELETE /api/v1/services/:id` Remove an existing service record.

* The `availabilitiesType` must be either `flexible` or `fixed`.

### Service Categories

**Resource JSON**

```
{
    "id": 1, 
    "name": "Test Category", 
    "description": "This is a test category."
}
```

- `GET /api/v1/service_categories[/:id]` Get all the service categories or a specific one by providing the ID in the URI. 
- `POST /api/v1/service_categories` Provide the new service category JSON in the request body to insert a new record. 
- `PUT /api/v1/service_categories/:id` Provide the updated service category JSON in the request body to update an existing record. The ID in the URI is required. 
- `DELETE /api/v1/service_categories/:id` Remove an existing service category record.

### Admins

**Resource JSON**

```
{  
    "id": 1,
    "firstName": "Jason",
    "lastName": "Doe",
    "email": "jason@example.org",
    "mobile": "+10000000000",
    "phone": "+10000000000",
    "address": "Test Street 1A",
    "city": "Some Place",
    "state": "Some State",
    "zip": "12345",
    "timezone": "UTC",
    "language": "english",
    "notes": "This is a test admin.",
    "settings":{  
        "username": "chrisdoe",
        "password": "Password@123",
        "notifications": true,
        "calendarView": "default"
    }
}
```

- `GET /api/v1/admins[/:id]` Get all the admins or a specific one by providing the ID in the URI. 
- `POST /api/v1/admins` Provide the new admin JSON in the request body to insert a new record. 
- `PUT /api/v1/admins/:id` Provide the updated admin JSON in the request body to update an existing record. The ID in the URI is required. 
- `DELETE /api/v1/admins/:id` Remove an existing admin record.

**Note: The `password` field is optional and should only be provided when creating (POST) or updating (PUT) a record.** 

### Providers

**Resource JSON**

```
{  
    "id": 1,
    "firstName": "Chris",
    "lastName": "Doe",
    "email": "chris@example.org",
    "mobile": "+10000000000",
    "phone": "+10000000000",
    "address": "Test Street 1A",
    "city": "Some Place",
    "state": "Some State",
    "zip": "12345",
    "timezone": "UTC",
    "language": "english",
    "notes": "This is a test provider.",
    "services": [
        1,
        5,
        9
    ],
    "settings":{  
        "username": "chrisdoe",
        "password": "Password@123",
        "notifications": true,
        "googleSync": false,
        "googleCalendar": null,
        "googleToken": null,
        "syncFutureDays": 90,
        "syncPastDays": 30,
        "calendarView": "default",
        "workingPlan":{  
            "monday":{  
                "start": "09:00",
                "end": "18:00",
                "breaks":[  
                    {  
                        "start": "14:30",
                        "end": "15:00"
                    }
                ]
            },
            "tuesday":{  
                "start": "09:00",
                "end": "18:00",
                "breaks":[  
                    {  
                        "start": "14:30",
                        "end": "15:00"
                    }
                ]
            },
            "wednesday": null,
            "thursday":{  
                "start": "09:00",
                "end": "18:00",
                "breaks":[  
                    {  
                        "start": "14:30",
                        "end": "15:00"
                    }
                ]
            },
            "friday": {  
                "start": "09:00",
                "end": "18:00",
                "breaks": [  
                    {  
                        "start": "14:30",
                        "end": "15:00"
                    }
                ]
            },
            "saturday": null,
            "sunday": null
        },
        "workingPlanExceptions": {
            "2020-01-01": {
                "start": "08:00",
                "end": "20:00",
                "breaks": [  
                    {  
                        "start": "12:00",
                        "end": "14:00"
                    }
                ]
            }
        }
    }
}
```

- `GET /api/v1/providers[/:id]` Get all the providers or a specific one by providing the ID in the URI. 
- `POST /api/v1/providers` Provide the new provider JSON in the request body to insert a new record. 
- `PUT /api/v1/providers/:id` Provide the updated provider JSON in the request body to update an existing record. The ID in the URI is required. 
- `DELETE /api/v1/providers/:id` Remove an existing provider record.

**Note: The `password` field is optional and should only be provided when creating (POST) or updating (PUT) a record.** 

### Secretaries

**Resource JSON**

```
{  
    "id": 1,
    "firstName": "Jessy",
    "lastName": "Doe",
    "email": "jessy@example.org",
    "mobile": "+10000000000",
    "phone": "+10000000000",
    "address": "Test Street 1A",
    "city": "Some Place",
    "state": "Some State",
    "zip": "12345",
    "timezone": "UTC",
    "language": "english",
    "notes": "This is a test secretary.",
    "providers": [
        53,
        17
    ],
    "settings":{  
        "username":"jessydoe",
        "password":"Password@123",
        "notifications": true,
        "calendarView": "default"
    }
}
```

- `GET /api/v1/secretaries[/:id]` Get all the secretaries or a specific one by providing the ID in the URI. 
- `POST /api/v1/secretaries` Provide the new secretary JSON in the request body to insert a new record. 
- `PUT /api/v1/secretaries/:id` Provide the updated secretary JSON in the request body to update an existing record. The ID in the URI is required. 
- `DELETE /api/v1/secretaries/:id` Remove an existing secretary record.

**Note: The `password` field is optional and should only be provided when creating (POST) or updating (PUT) a record.** 

### Settings

**Resource JSON**

```
{
    "name": "book_advance_timeout",
    "value": "100"
}
```

**Requests**

- `GET /api/v1/settings[/:name]` Get all the settings or a specific one by providing the setting name in the URI. 
- `PUT /api/v1/settings/:name` Insert or update a setting in the database. Provide a snake_case name in order to keep the conventions. 
- `DELETE /api/v1/settings/:name` Remove a setting from the database. **Notice:** Be careful when removing settings that are required by the application because this will cause error later on.

## API Roadmap 

Although the current state should be sufficient for working with the application data there are some other features of that will make the consume more flexible and powerful. These will be added gradually with the future releases of Easy!Appointments. 

[ ] Add auto-generated links whenever external resource IDs are provided.

[ ] Add pagination header links when the client provides pagination parameters.

[ ] Add support for sub-resourcing e.g. /api/v1/customers/:id/appointments must return all the appointments of a specific customer. 

[ ] Add custom filtering parameters e.g. /api/v1/appointments?book=>2016-07-10

[ ] Improved exception handling. 

Feel free to make pull requests if you have the time to develop one of those. 

## Troubleshooting

### Authorization Issues

If your server runs PHP through FastCGI you will the authorization will not work because the `Authorization` header is not available to the PHP scripts. You can easily fix this by applying the following adjustments depending your server software: 

### Apache

Add the following code snippet to an `.htaccess` file in the installation root directory if you have `mod_rewrite` installed and enabled: 

```
RewriteEngine on
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]
```

[[Source]](http://stackoverflow.com/a/22554102/1718162)

Add the following code snippet to an `.htaccess` file in the installation root directory if you have `mod_setenvif` installed and enabled: 

```
SetEnvIf Authorization .+ HTTP_AUTHORIZATION=$0
```

[[Source]](http://stackoverflow.com/a/27229807/1718162)

### NGINX

Add the following code snippet to the NGINX `.conf` file: 

```
fastcgi_param PHP_AUTH_USER $remote_user;
fastcgi_param PHP_AUTH_PW $http_authorization;
```

[[Source]](http://serverfault.com/a/520943)

*This document applies to Easy!Appointments v1.5.1.*

[Back](readme.md)
