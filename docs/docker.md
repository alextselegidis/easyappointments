# Docker

Docker lets you run Easy!Appointments on your computer without installing Apache, PHP, or MySQL manually. Everything runs inside containers — isolated mini-environments that are easy to start and stop.

## Getting Started

1. Make sure you have [Docker](https://www.docker.com/) installed.
2. Clone or download the project.
3. Edit the `config.php` file in the root folder to match the Docker setup:

```php
class Config {
    // GENERAL SETTINGS
    const BASE_URL      = 'http://localhost';
    const LANGUAGE      = 'english';
    const DEBUG_MODE    = TRUE;

    // DATABASE SETTINGS
    const DB_HOST       = 'mysql';
    const DB_NAME       = 'easyappointments';
    const DB_USERNAME   = 'user';
    const DB_PASSWORD   = 'password';

    // GOOGLE CALENDAR SYNC
    const GOOGLE_SYNC_FEATURE   = FALSE;
    const GOOGLE_CLIENT_ID      = '';
    const GOOGLE_CLIENT_SECRET  = '';
}
```

4. Start everything with:

```bash
docker compose up
```

## What's Included

Once running, you can access these services in your browser:

| Service | URL | Credentials |
|---------|-----|-------------|
| **Easy!Appointments** | http://localhost | (your admin account) |
| **phpMyAdmin** (database manager) | http://localhost:8080 | `root` / `secret` |
| **Mailpit** (email testing) | http://localhost:8025 | (no login needed) |
| **Baikal** (CalDAV testing) | http://localhost:8100 | `admin` / `admin` |
| **phpLDAPadmin** (LDAP testing) | http://localhost:8200 | `cn=admin,dc=example,dc=org` / `admin` |

## CalDAV Sync with Baikal

To test CalDAV syncing locally:

1. Open Baikal at http://localhost:8100 and create a new user.
2. In Easy!Appointments, click **Enable Sync** → **CalDAV** and enter:
   - **URL:** `http://baikal/dav.php/calendars/<your-username>/default/`
   - **Username:** your Baikal username
   - **Password:** your Baikal password

## LDAP

OpenLDAP runs on the `openldap` container (ports `389` and `636`). You can manage it through phpLDAPadmin at http://localhost:8200.

> **Note:** This Docker setup is for **development only**. Don't use it in production. For a production Docker image, see: https://github.com/alextselegidis/easyappointments-docker

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
