# Docker

Run the development containers of Easy!Appointments with Docker and Docker Compose utility. Docker allows you to compose 
your application from micro-services, without worrying about inconsistencies between development and production 
environments and without locking into any platform or language. 

Enter the `docker` directory and run `docker-compose up` to start the environment. 

You will need modify the root `config.php` so that it matches the following example: 

```php 
class Config {
    // ------------------------------------------------------------------------
    // GENERAL SETTINGS
    // ------------------------------------------------------------------------
    
    const BASE_URL      = 'http://localhost:8000'; 
    const LANGUAGE      = 'english';
    const DEBUG_MODE    = TRUE;

    // ------------------------------------------------------------------------
    // DATABASE SETTINGS
    // ------------------------------------------------------------------------
    
    const DB_HOST       = 'easyappointments-database:3306';
    const DB_NAME       = 'easyappointments';
    const DB_USERNAME   = 'root';
    const DB_PASSWORD   = 'root';

    // ------------------------------------------------------------------------
    // GOOGLE CALENDAR SYNC
    // ------------------------------------------------------------------------
    
    const GOOGLE_SYNC_FEATURE   = FALSE; // You can optionally enable the Google Sync feature. 
    const GOOGLE_PRODUCT_NAME   = '';
    const GOOGLE_CLIENT_ID      = '';
    const GOOGLE_CLIENT_SECRET  = '';
    const GOOGLE_API_KEY        = '';
}
```

In the host machine the server is accessible from `http://localhost:8000` and the database from `localhost:8001`.  

You can remove the docker containers with `docker rm easyappointments-server easyappointments-database`. 

You can remove the server image with `docker rmi easyappointments-server:v1`.

*This document applies to Easy!Appointments v1.4.1.*

[Back](readme.md)
