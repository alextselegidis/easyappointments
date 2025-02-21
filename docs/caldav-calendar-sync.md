# CalDAV Calendar Sync

This page will guide you through the local configuration of Baikal, an Open Source DAV server that can be used to test
the CalDAV integration of Easy!Appointments.

> Note: This guide refers to the available Docker development configuration using docker-compose.yml

## Initial Setup

By default, Baikal is configured to run on localhost:8100, so it can be accessed by opening the browser to this address.

The first time the app is executed, it will display a small initial-configuration form that has to be submitted for
Baikal to work.

### First Configuration Page

- The right time zone value needs to be selected for synchronization to match the information Easy!Appointments sends.
- The "WebDAV authentication type" needs to be set to "Basic"
- The default username is "admin", so the password for this development account could also be "admin" or something
  similar (easy to remember)

### Second Configuration Page

- The default values can be submitted unless MySQL use is preferred (although totally optional).

### Test User

After the configuration is completed and Baikal works go to the "Users and resources" page and create a test user that
will be used to sync with.

## Enabling Sync

After making sure that the local Baikal server works, Easy!Appointments will be able to connect to it and sync the
appointment data.

Baikal supports multiple user accounts, but for simplicity this guide will refer to the default account created during
the initial setup.

### Credentials

While trying to enable the CalDAV sync from the Easy!Appointments calendar page, use the following credentials after
clicking on "Enable Sync" > "CalDAV".

- URL: http://baikal/dav.php/calendars/<username-from-previous-step>/default/
- Username: <username-from-previous-step>
- Password: <password-from-previous-step>

*This document applies to Easy!Appointments v1.5.1.*

[Back](readme.md)
