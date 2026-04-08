# Google Calendar Sync

Easy!Appointments can sync appointments with Google Calendar in both directions. When a provider links their Google Calendar, any changes made in either system will be reflected in the other.

## What You Need

- A working Easy!Appointments installation with at least one service and provider set up.
- A Google account.

## Step 1: Create Google API Credentials

You need to tell Google that your Easy!Appointments installation is allowed to access calendar data.

1. Go to the [Google Cloud Console](https://console.cloud.google.com/) and **create a new project** (or select an existing one).
2. In the project dashboard, go to **APIs & Services** > **Library** and search for **Google Calendar API**. Click on it and press **Enable**.
3. Go to **APIs & Services** > **Credentials** and click **Create Credentials** > **OAuth client ID**.
4. If prompted, fill in the **OAuth consent screen** information first.
5. Select **Web Application** as the application type and give it a name.
6. Under **Authorised JavaScript origins**, add your domain (just the domain, e.g. `http://mywebsite.com`).
7. Under **Authorised redirect URIs**, add:
   ```
   https://your-domain.com/easyappointments/index.php/google/oauth_callback
   ```
   Replace `your-domain.com/easyappointments` with your actual installation URL.
8. Click **Create**. Google will show you a **Client ID** and **Client Secret** — copy both.

## Step 2: Create a Browser API Key

Customers can add appointments to their own Google Calendar from the booking page. This requires an API key.

1. Still in **APIs & Services** > **Credentials**, click **Create Credentials** > **API Key**.
2. A key will be generated. You can optionally restrict it to your domain for security.

## Step 3: Update Easy!Appointments Config

Open your `config.php` file and update these values:

```php
const GOOGLE_SYNC_FEATURE   = TRUE;
const GOOGLE_CLIENT_ID      = 'your-client-id-here';
const GOOGLE_CLIENT_SECRET  = 'your-client-secret-here';
```

## Step 4: Link a Provider's Google Calendar

1. Log in to the Easy!Appointments backend and go to the **Calendar** page.
2. Select a provider and click **Enable Sync**.
3. A Google sign-in window will appear. Log in with the provider's Google account and grant permission.
4. The sync is now active!

## Good to Know

- Sync is triggered from the Easy!Appointments backend or whenever appointments change.
- Each provider can only be linked to **one** Google Calendar account.
- Recurring events are **not supported** yet.

## Useful Links

- [Google Calendar API Docs](https://developers.google.com/google-apps/calendar)
- [E!A Support Group](https://groups.google.com/forum/#!forum/easy-appointments)

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
