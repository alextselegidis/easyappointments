# Docker

Run the development containers of Easy!Appointments with Docker and Docker Compose utility. Docker allows you to compose your application from micro-services, without worrying about inconsistencies between development and production environments and without locking into any platform or language. 

Copy the `.env.example` file to `.env` inside the `docker` directory and set the desired environment variables.

Make sure that you have Docker and Docker Compose installed and configured in your system and execute the following command through your terminal while being in `docker` directory of your Git clone: 

`docker-compose up`

Then modify the root `config.php` so that it matches the following example: 

```php 
class Config {
    // ------------------------------------------------------------------------
    // GENERAL SETTINGS
    // ------------------------------------------------------------------------
    
    const BASE_URL      = 'http://{DOCKER-SERVER-IP}'; // e.g. 192.168.99.100
    const LANGUAGE      = 'english';
    const DEBUG_MODE    = TRUE;

    // ------------------------------------------------------------------------
    // DATABASE SETTINGS
    // ------------------------------------------------------------------------
    
    const DB_HOST       = 'database:3306';
    const DB_NAME       = 'easyappointments';
    const DB_USERNAME   = 'easyappointments';
    const DB_PASSWORD   = 'easyappointments';

    // ------------------------------------------------------------------------
    // GOOGLE CALENDAR SYNC
    // ------------------------------------------------------------------------
    
    const GOOGLE_SYNC_FEATURE   = FALSE; 
    const GOOGLE_PRODUCT_NAME   = '';
    const GOOGLE_CLIENT_ID      = '';
    const GOOGLE_CLIENT_SECRET  = '';
    const GOOGLE_API_KEY        = '';
}
```

*This document applies to Easy!Appointments v1.3.2.*

[Back](readme.md)
