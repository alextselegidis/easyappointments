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
4. `baikal` is on a private network, so the dialog asks you to confirm it. Click **Allow and connect**.

That's it — your appointments will now sync with the Baikal CalDAV server.

## Servers on a Private Network

CalDAV URLs that resolve to a private or reserved network address are rejected by default. Without that check, a
URL entered in the backend could make Easy!Appointments send requests into its own internal network, and report
back what it found there.

A server you run yourself on a local network is a perfectly normal setup, so you can allow it. There are two ways:

- **When connecting.** If the host is on a private network, the connection dialog says so and offers an
  **Allow and connect** button. One click adds the host and continues. This is the easiest way, and it is only
  offered to users who may change the system settings.
- **Up front.** Go to **Settings** → **Integrations** → **CalDAV** and add the connection URL, one per line, to
  **Allowed Connection URLs**. Each line may be a full URL or a bare host name.

Only add servers that you run yourself. Every host on the list may be reached by the synchronization, including
addresses that are otherwise unreachable from outside your network.

### When Synchronization Fails

If a sync fails, the calendar page shows an alert explaining what went wrong and what to correct:

| What you see | What it means |
| --- | --- |
| The stored connection URL could not be used | The host is not allowed, or it cannot be resolved. The message names the host, so you can add it under **Allowed Connection URLs**. |
| The server rejected the user name and the password | The credentials changed. Disable the sync and connect again. |
| The server could not be reached | The CalDAV server is down, or the URL is wrong. |

The alert has a **Configure** button that takes you straight to the CalDAV settings page.

*This document applies to Easy!Appointments v1.6.1.*

[Back](readme.md)
