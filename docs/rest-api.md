# REST API

Easy!Appointments has a REST API that lets you read and manage all your data (appointments, customers, services, etc.) through HTTP requests. This is useful if you want to connect Easy!Appointments to other software.

## OpenAPI Specification

The project includes a ready-made [OpenAPI file](https://raw.githubusercontent.com/alextselegidis/easyappointments/master/openapi.yml) that describes every endpoint. You can import it into tools like [Postman](https://www.postman.com/) to start making requests right away.

Learn more about OpenAPI at [swagger.io](https://swagger.io).

## Authentication

Every API request must include authentication. There are two options:

**Option 1 — Basic Authentication:** Send the username and password of an admin user with each request.

**Option 2 — API Key:** Set up an API key in **Settings** and send it as a Bearer Token.

> **Important:** Always use HTTPS in production so credentials aren't sent in plain text.

## How It Works

The API uses standard HTTP methods:

| Method | What It Does | Example |
|--------|-------------|---------|
| **GET** | Read data | Get a list of appointments |
| **POST** | Create new data | Add a new customer |
| **PUT** | Update existing data | Change an appointment time |
| **DELETE** | Remove data | Cancel an appointment |

All data is sent and received as **JSON**.

## Query Parameters

You can add these parameters to any GET request to control what comes back:

### Search

Find records matching a keyword:

```
/api/v1/appointments?q=keyword
```

### Sort

Sort results by one or more fields. Use `+` for ascending, `-` for descending:

```
/api/v1/appointments?sort=-id,+book
```

### Paginate

Get results one page at a time (default is 20 per page):

```
/api/v1/appointments?page=1&length=10
```

### Select Fields

Only return specific fields:

```
/api/v1/appointments?fields=id,book,hash,notes
```

### Include Related Data

Attach related records to the response:

```
/api/v1/appointments?with=customer,service,provider
```

### Aggregates (Deprecated)

```
/api/v1/appointments?aggregates
```

> This parameter only works with appointments and will be removed in the future. Use `with` instead.

## Quick Examples

**Get all appointments:**

```bash
curl http://your-site.com/index.php/api/v1/appointments --user admin:password
```

**Get a specific customer (ID 34):**

```bash
curl http://your-site.com/index.php/api/v1/customers/34 --user admin:password
```

**Update a service category name (ID 23):**

```bash
curl -H 'Content-Type: application/json' -X PUT -d '{"name": "New Name!"}' \
  http://your-site.com/index.php/api/v1/service_categories/23 --user admin:password
```

**Delete a service (ID 15):**

```bash
curl -X DELETE http://your-site.com/index.php/api/v1/services/15 --user admin:password
```

You can also test GET requests directly in your browser by visiting the URL.

## Error Responses

If something goes wrong, you'll get a JSON response like this:

```json
{
    "code": 404,
    "message": "The requested record was not found!"
}
```

## Available Resources

### Availabilities

Get available appointment times for a provider and service on a given date.

```
GET /api/v1/availabilities?providerId=1&serviceId=2&date=2016-07-19
```

**Response:**
```json
["09:30", "13:00", "13:15", "14:00"]
```

### Appointments

```json
{
    "id": 1,
    "book": "2016-07-08 12:57:00",
    "start": "2016-07-08 18:00:00",
    "end": "2016-07-08 18:30:00",
    "hash": "apTWVbSvBJXR",
    "location": "Test Street 1A, 12345 Some State, Some Place",
    "notes": "This is a test appointment.",
    "serviceId": 1,
    "providerId": 2,
    "customerId": 3,
    "googleCalendarId": null
}
```

- `GET /api/v1/appointments[/:id]` — Get all or one
- `POST /api/v1/appointments` — Create new
- `PUT /api/v1/appointments/:id` — Update
- `DELETE /api/v1/appointments/:id` — Delete

### Unavailabilities

```json
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

- `GET /api/v1/unavailabilities[/:id]` — Get all or one
- `POST /api/v1/unavailabilities` — Create new
- `PUT /api/v1/unavailabilities/:id` — Update
- `DELETE /api/v1/unavailabilities/:id` — Delete

### Customers

```json
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

- `GET /api/v1/customers[/:id]` — Get all or one
- `POST /api/v1/customers` — Create new
- `PUT /api/v1/customers/:id` — Update
- `DELETE /api/v1/customers/:id` — Delete

### Services

```json
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

- `GET /api/v1/services[/:id]` — Get all or one
- `POST /api/v1/services` — Create new
- `PUT /api/v1/services/:id` — Update
- `DELETE /api/v1/services/:id` — Delete

> The `availabilitiesType` must be either `flexible` or `fixed`.

### Service Categories

```json
{
    "id": 1,
    "name": "Test Category",
    "description": "This is a test category."
}
```

- `GET /api/v1/service_categories[/:id]` — Get all or one
- `POST /api/v1/service_categories` — Create new
- `PUT /api/v1/service_categories/:id` — Update
- `DELETE /api/v1/service_categories/:id` — Delete

### Admins

```json
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
    "settings": {
        "username": "chrisdoe",
        "password": "Password@123",
        "notifications": true,
        "calendarView": "default"
    }
}
```

- `GET /api/v1/admins[/:id]` — Get all or one
- `POST /api/v1/admins` — Create new
- `PUT /api/v1/admins/:id` — Update
- `DELETE /api/v1/admins/:id` — Delete

> The `password` field is optional — only include it when creating or updating a user.

### Providers

```json
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
    "services": [1, 5, 9],
    "settings": {
        "username": "chrisdoe",
        "password": "Password@123",
        "notifications": true,
        "googleSync": false,
        "googleCalendar": null,
        "googleToken": null,
        "syncFutureDays": 90,
        "syncPastDays": 30,
        "calendarView": "default",
        "workingPlan": {
            "monday": {
                "start": "09:00",
                "end": "18:00",
                "breaks": [{"start": "14:30", "end": "15:00"}]
            },
            "tuesday": {
                "start": "09:00",
                "end": "18:00",
                "breaks": [{"start": "14:30", "end": "15:00"}]
            },
            "wednesday": null,
            "thursday": {
                "start": "09:00",
                "end": "18:00",
                "breaks": [{"start": "14:30", "end": "15:00"}]
            },
            "friday": {
                "start": "09:00",
                "end": "18:00",
                "breaks": [{"start": "14:30", "end": "15:00"}]
            },
            "saturday": null,
            "sunday": null
        },
        "workingPlanExceptions": {
            "2020-01-01": {
                "start": "08:00",
                "end": "20:00",
                "breaks": [{"start": "12:00", "end": "14:00"}]
            }
        }
    }
}
```

- `GET /api/v1/providers[/:id]` — Get all or one
- `POST /api/v1/providers` — Create new
- `PUT /api/v1/providers/:id` — Update
- `DELETE /api/v1/providers/:id` — Delete

> The `password` field is optional — only include it when creating or updating a user.

### Secretaries

```json
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
    "providers": [53, 17],
    "settings": {
        "username": "jessydoe",
        "password": "Password@123",
        "notifications": true,
        "calendarView": "default"
    }
}
```

- `GET /api/v1/secretaries[/:id]` — Get all or one
- `POST /api/v1/secretaries` — Create new
- `PUT /api/v1/secretaries/:id` — Update
- `DELETE /api/v1/secretaries/:id` — Delete

> The `password` field is optional — only include it when creating or updating a user.

### Settings

```json
{
    "name": "book_advance_timeout",
    "value": "100"
}
```

- `GET /api/v1/settings[/:name]` — Get all or one setting
- `PUT /api/v1/settings/:name` — Create or update a setting
- `DELETE /api/v1/settings/:name` — Remove a setting

> **Be careful** when deleting settings — removing ones the app needs will cause errors.

## Troubleshooting

### Authentication Not Working

If your server runs PHP through FastCGI, the `Authorization` header may not reach PHP. Here's how to fix it:

**Apache** — add one of these to your `.htaccess` file:

```
RewriteEngine on
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]
```

Or, if you have `mod_setenvif`:

```
SetEnvIf Authorization .+ HTTP_AUTHORIZATION=$0
```

**NGINX** — add this to your server config:

```
fastcgi_param PHP_AUTH_USER $remote_user;
fastcgi_param PHP_AUTH_PW $http_authorization;
```

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
