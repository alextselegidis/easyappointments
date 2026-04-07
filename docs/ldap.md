# LDAP Integration

LDAP lets you import users from a central directory (like your company's employee directory) into Easy!Appointments. It also enables single sign-on (SSO), so users can log in with their existing company credentials.

> **Note:** This guide uses the Docker development setup. For production, replace the example values with your own LDAP server details.

## Local Development Setup (Docker)

The Docker setup includes OpenLDAP and phpLDAPadmin, ready to use with no extra configuration.

| Service | URL | Credentials |
|---------|-----|-------------|
| **phpLDAPadmin** | http://localhost:8200 | `cn=admin,dc=example,dc=org` / `admin` |
| **OpenLDAP** | `openldap:389` (inside Docker) | Same as above |

## Enabling LDAP in Easy!Appointments

1. Log in to the backend.
2. Go to **Settings** → **Integrations** → **LDAP**.
3. Turn on the integration and fill in the connection settings:

| Setting | Docker Value | Description |
|---------|-------------|-------------|
| **Host** | `openldap` | Your LDAP server address |
| **Port** | `389` | Your LDAP server port |
| **User DN** | `cn=admin,dc=example,dc=org` | The admin account used to connect |
| **Password** | `admin` | The admin account password |
| **Base DN** | `dc=example,dc=org` | Where to start searching for users |
| **Filter** | (default is fine) | Controls which LDAP entries are returned. The `{{KEYWORD}}` placeholder is replaced with your search term |

### Field Mapping

You can map LDAP fields to Easy!Appointments fields so that user information is automatically filled in when importing. This is a JSON object, for example:

```json
{"first_name": "givenName", "last_name": "sn", "email": "mail"}
```

## Importing Users

1. On the LDAP settings page, use the **search** box to find users in your LDAP directory.
2. Click **Import** on a user to open a form with their details pre-filled.
3. Review and adjust the information, then save.

> **Note:** Backend users (admins, secretaries, providers) need a local password before import. If you only plan to use LDAP login, set a long random password.

Make sure the **LDAP DN** field points to the correct LDAP entry, and the **username** matches the LDAP username (usually the `cn` attribute).

## Single Sign-On (SSO)

Once LDAP is enabled, users can log in with their LDAP credentials:

1. Easy!Appointments first tries the **local password**.
2. If that doesn't work, it checks the password against **LDAP**.
3. If the LDAP password matches, the user is logged in.

For this to work:

- The user must already be imported into Easy!Appointments.
- The **username** must match between both systems.
- The **LDAP DN** field must point to the correct LDAP entry.

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
