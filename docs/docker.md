# Docker

Run the development containers of Easy!Appointments with Docker and Docker Compose utility. Docker allows you to compose your application in microservices, so that you can easily get started with the local development.

Simply clone the project and run `docker compose up` to start the environment.

You will need modify the root `config.php` so that it matches the following example:

```php 
class Config {
    // ------------------------------------------------------------------------
    // GENERAL SETTINGS
    // ------------------------------------------------------------------------
    
    const BASE_URL      = 'http://localhost'; 
    const LANGUAGE      = 'english';
    const DEBUG_MODE    = TRUE;

    // ------------------------------------------------------------------------
    // DATABASE SETTINGS
    // ------------------------------------------------------------------------
    
    const DB_HOST       = 'mysql';
    const DB_NAME       = 'easyappointments';
    const DB_USERNAME   = 'user';
    const DB_PASSWORD   = 'password';

    // ------------------------------------------------------------------------
    // GOOGLE CALENDAR SYNC
    // ------------------------------------------------------------------------
    
    const GOOGLE_SYNC_FEATURE   = FALSE; // You can optionally enable the Google Sync feature. 
    const GOOGLE_CLIENT_ID      = '';
    const GOOGLE_CLIENT_SECRET  = '';
}
```

In the host machine the server is accessible from `http://localhost` and the database from `localhost:3306`.

You can additionally access phpMyAdmin from `http://localhost:8080` (credentials are `root` / `secret`) and Mailpit from `http://localhost:8025`.

**Attention:** This configuration is meant to make development easier. It is not intended to server as a production environment!

A production image of Easy!Appointments can be found at: https://github.com/alextselegidis/easyappointments-docker

*This document applies to Easy!Appointments v1.4.3.*

[Back](readme.md)
