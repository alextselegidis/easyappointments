# Console

Easy!Appointments includes a set of command-line tools you can run from a terminal. These are useful for automating tasks like backups and database updates.

## Before You Start

Make sure PHP is installed and available in your terminal. You can check by running:

```
php -v
```

This should print the PHP version number. If it doesn't, you need to install PHP or add it to your system's PATH.

## Available Commands

All commands start with `php index.php console` and are run from the Easy!Appointments root folder.

### Migrate

Updates your database to the latest version:

```
php index.php console migrate
```

To reset the database and re-run all migrations from scratch:

```
php index.php console migrate fresh
```

> **Warning:** The `fresh` option deletes all existing data.

### Seed

Adds sample data (a test admin, provider, customer, and service) so you can try things out:

```
php index.php console seed
```

### Install

Runs the installer from the command line (instead of using the browser):

```
php index.php console install
```

Make sure you've already filled in your `config.php` file before running this.

### Backup

Creates a backup of your data in the `storage/backups` folder:

```
php index.php console backup
```

To save the backup to a different folder:

```
php index.php console backup /path/to/your/folder
```

### Sync

Syncs calendars for all providers who have calendar sync enabled:

```
php index.php console sync
```

**Tip:** Set this up as a [cron job](https://en.wikipedia.org/wiki/Cron) to run automatically (e.g. every hour) so your calendars stay up to date without manual work.

### Help

Shows a summary of all available commands:

```
php index.php console help
```

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
