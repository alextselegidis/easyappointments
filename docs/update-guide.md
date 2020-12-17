# Update Guide

This page will guide you through the update procedure of your Easy!Appointments installation. You will need to follow the steps below in order to have successful results.

If you get a `500 Internal Server Error` message then you must check the error log files, located in the storage/logs directory.

Before you start following the steps below, make sure you **backup** your database and files. 

### Updating from v1.3.x to v1.4.x

###### Step 1: Update your Easy!Appointments files 

Replace all files and directories in your installation directory.

> Note: If you have any custom developed files in these directories, please make copies of them first.
        
###### Step 2: Run the database migrations 

Database migrations will bring your database structure to the latest state.

**Browser**

Open your browser to the Easy!Appointments installation URL, login to the backend and type in the browser address the following URL to complete the database upgrade: `https://url/to/easyappointments/folder/index.php/backend/update`

**Console**

If you have console access to your server then head to the installation directory and run `php index.php console migrate`.

###### Step 3: Remove unnecessary files 

The following directories are not needed: 

* /system

The following files are not needed:

* /autoload.php 

 
### Updating from v1.2.x to v1.3.x

###### Step 1: Update your Easy!Appointments files 

Replace all files and directories in your installation directory.

> Note: If you have any custom developed files in these directories, please make copies of them first.
        
###### Step 2: Run the database migrations 

Database migrations will bring your database structure to the latest state.

**Browser**

Open your browser to the Easy!Appointments installation URL, login to the backend and type in the browser address the following URL to complete the database upgrade: `https://url/to/easyappointments/folder/index.php/backend/update`


### Updating from v1.2.x to v1.3.x

###### Step 1: Update your Easy!Appointments files 

Replace all files and directories in your installation directory.

> Note: If you have any custom developed files in these directories, please make copies of them first.
        
###### Step 2: Run the database migrations 

Database migrations will bring your database structure to the latest state.

**Browser**

Open your browser to the Easy!Appointments installation URL, login to the backend and type in the browser address the following URL to complete the database upgrade: `https://url/to/easyappointments/folder/index.php/backend/update`

### Updating from v1.1.x to v1.2.x 

###### Step 1: Update your Easy!Appointments files 

Replace all files and directories in your installation directory.

> Note: If you have any custom developed files in these directories, please make copies of them first.
        
###### Step 2: Run the database migrations 

Database migrations will bring your database structure to the latest state.

**Browser**

Open your browser to the Easy!Appointments installation URL, login to the backend and type in the browser address the following URL to complete the database upgrade: `https://url/to/easyappointments/folder/index.php/backend/update`

###### Step 3: Make storage writable 

Version v1.2.x introduces two new folders in the root directory of the project, the "engine" and the "storage" directory. **Ensure that the storage directory is writable and has the correct permissions.**

### Updating from v1.0.x to v1.1.x

###### Step 1: Update your Easy!Appointments files 

Replace all files and directories in your installation directory.

> Note: If you have any custom developed files in these directories, please make copies of them first.
        
###### Step 2: Run the database migrations 

Database migrations will bring your database structure to the latest state.

**Browser**

Open your browser to the Easy!Appointments installation URL, login to the backend and type in the browser address the following URL to complete the database upgrade: `https://url/to/easyappointments/folder/index.php/backend/update`

###### Step 3: Migrate the configuration.php values

Use the data of the old `configuration.php` file in the new `config.php`. 

*This document applies to Easy!Appointments v1.4.1.*

[Back](readme.md)
