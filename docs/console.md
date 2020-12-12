# Console

> This page contains information about the CLI support of Easy!Appointments.

### Introduction

Starting from v1.4.0 Easy!Appointments has introduced CLI support, providing a set of useful utilities to use with your installation. 

The commands can be executed with PHP installed on your server and available from within the terminal. To ensure that you have PHP configured properly run the following command from within a terminal session. 

```
php -v
```

It shall give you information about the currently installed PHP version.

The available commands are described below.  

### Commands

The entry point for the CLI commands is the root `index.php` file which means that you will be calling this file with additional arguments, depending on which command you want to run. 

##### Migrate 

```
php index.php migrate
```

This command will migrate the database to the latest state. 

```
php index.php migrate fresh
```

This command will reset any change made by the previous migrations and start from the beginning. 

##### Seed

```
php index.php seed
```

This command will add a test admin, provider, customer and service in the app, so that you can already run some tests.  

##### Install

```
php index.php install
```

This command will perform a CLI installation of Easy!Appointments. 

You can run this after your are done configuring your app from the root `config.php` file. 

##### Backup 

```
php index.php backup
```

This command will create a backup of the application data in the `storage/backups` directory. 


```
php index.php backup /path/to/backup/folter
``` 

You can also provide a custom directory for your backup files. 


##### Sync

```
php index.php sync
``` 

This command will trigger the calendar synchronization for all the system providers. 

It is especially important, because it can be automatically executed on a regular base with a cron job. 

This way the app provider schedules will always be updated. 


##### Help 

```
php index.php help
``` 

This command will give more information about the console capabilities.

*This document applies to Easy!Appointments v1.4.1.*

[Back](readme.md)
