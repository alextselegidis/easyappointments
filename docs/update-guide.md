# Update Guide

This guide explains how to update Easy!Appointments from an older version to a newer one.

> **Important:** Always **back up your database and files** before updating. If something goes wrong, you can restore them.

If you see a `500 Internal Server Error` after updating, check the error log files in the `storage/logs` folder for details.

## General Update Steps

Every update follows the same basic process:

### Step 1: Replace the Files

Download the new version and replace all the files and folders in your installation directory with the new ones.

- **Keep your `config.php`** — it has your database credentials and settings.
- If you've customized any other files, make copies of them first so you don't lose your changes.

### Step 2: Run Database Migrations

The database may need updates to work with the new version. You have two ways to do this:

**Option A — From Your Browser:**

Log in to the backend and go to:
```
https://your-domain.com/easyappointments/index.php/backend/update
```

**Option B — From the Command Line:**

If you have terminal access, go to the installation folder and run:
```
php index.php console migrate
```

### Step 3: Version-Specific Steps (If Needed)

Some updates have extra steps. Check the sections below for your specific version jump.

## Version-Specific Notes

### Updating from v1.0.x to v1.1.x

After Steps 1 and 2 above, also:

- Copy your settings from the old `configuration.php` into the new `config.php` file.

### Updating from v1.1.x to v1.2.x

After Steps 1 and 2 above, also:

- Make sure the `storage` folder has write permissions (it was added in this version).

### Updating from v1.2.x to v1.3.x

No extra steps — just follow Steps 1 and 2.

### Updating from v1.3.x to v1.4.x

After Steps 1 and 2 above, also:

- Delete the `/system` folder — it's no longer needed.
- Delete the `/autoload.php` file — it's no longer needed.

### Updating from v1.4.x to v1.5.x

No extra steps — just follow Steps 1 and 2.

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
