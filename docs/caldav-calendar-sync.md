# CalDAV Calendar Sync

This guide explains how to set up a local CalDAV server (Baikal) for testing calendar sync with Easy!Appointments.

> **Note:** This guide uses the Docker development setup from `docker-compose.yml`.

## Setting Up Baikal

Baikal is a free, self-hosted calendar server. In the Docker setup, it runs at http://localhost:8100.

### First-Time Setup

1. Open http://localhost:8100 in your browser.
2. You'll see a setup form. Fill it in:
   - **Time zone:** Choose your local time zone (must match Easy!Appointments).
   - **WebDAV authentication type:** Select **Basic**.
   - **Admin password:** Set something simple like `admin` for local testing.
3. On the next page, keep the default settings and submit.

### Create a Test User

1. After setup, go to **Users and resources** in Baikal.
2. Create a new user (e.g. username: `testuser`, password: `testpass`).

## Connecting Easy!Appointments to Baikal

1. In Easy!Appointments, go to the **Calendar** page.
2. Click **Enable Sync** → **CalDAV**.
3. Enter the following:
   - **URL:** `http://baikal/dav.php/calendars/testuser/default/` (replace `testuser` with your Baikal username)
   - **Username:** `testuser`
   - **Password:** `testpass`

That's it — your appointments will now sync with the Baikal CalDAV server.

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
