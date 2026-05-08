# LDAP Integration

LDAP lets you import users from a central directory (like your company's employee directory) into Easy!Appointments. It also enables single sign-on (SSO), so users can log in with their existing company credentials.

> **Note:** This guide uses the Docker development setup. For production, replace the example values with your own LDAP server details.

## Local Development Setup (Docker)

The Docker setup includes OpenLDAP and phpLDAPadmin, ready to use with no extra configuration.

| Service | URL | Credentials |
|---------|-----|-------------|
| **phpLDAPadmin** | http://localhost:8200 | `cn=admin,dc=example,dc=org` / `admin` |
| **OpenLDAP** | `openldap:389` (inside Docker) | Same as above |

## Creating a Test User in phpLDAPadmin

Before you can log in via LDAP, the directory needs at least one user entry. With a fresh OpenLDAP container the tree is empty, so you have to create a group first (otherwise phpLDAPadmin's user form leaves the **GID Number** dropdown empty and refuses to save) and then the user.

### 1. Create a group

1. Open phpLDAPadmin at <http://localhost:8200> and log in with `cn=admin,dc=example,dc=org` / `admin`.
2. In the tree on the left, click `dc=example,dc=org` → **Create a child entry**.
3. Choose the **Generic: Posix Group** template.
4. Fill in:
   - **Group** (cn): `users`
   - **GID Number**: leave the suggested value (e.g. `500`) or set `10000`.
5. Click **Create Object** → **Commit**.

### 2. Create the user account

1. Click `dc=example,dc=org` → **Create a child entry** again.
2. Choose the **Generic: User Account** template.
3. Fill in:
   - **First name**: `Jane`
   - **Last name**: `Doe`
   - **Common Name**: `Jane Doe`
   - **User ID** (uid): `janedoe` — this is what you will type at the EA login screen.
   - **GID Number**: pick the `users` group you just created.
   - **Home directory**: leave the default.
   - **Login shell**: `/bin/bash` (or any value).
   - **Password**: `password` (choose **SSHA** as the hash type).
4. Click **Create Object** → **Commit**.
5. Open the new entry and copy its full **Distinguished Name**, e.g. `cn=Jane Doe,dc=example,dc=org`. You will need it in the next step.

> **Tip:** If you don't need POSIX attributes, you can skip the group and create the user with only the `inetOrgPerson` + `top` object classes. The GID dropdown then disappears entirely.

### 3. Link the LDAP entry to an Easy!Appointments user

1. Log in to the EA backend as admin.
2. Open **Users → Providers** (or Admins / Secretaries depending on the role) and edit **Jane Doe**.
3. Set the **Username** field to the same value as the LDAP `uid`, e.g. `janedoe`. The username **must match exactly** — EA looks up the account by username before it ever attempts an LDAP bind, so a mismatch results in a generic "invalid credentials" response.
4. Paste the DN from step 2.5 into the **LDAP DN** field.
5. Save the user.
6. You can now log in at the EA login screen with `janedoe` / `password`.

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
