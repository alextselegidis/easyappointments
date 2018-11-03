# Update Guide

This page will guide you through the update procedure of your Easy!Appointments installation. You will need to follow the steps below in order to have successful results.

  1. **Backup** your files and database! This number one rule is very important for all computer systems and tools. If something breaks in your installation you might have a hard time to restore it without a backup.
  2. **Copy** and replace the new Easy!Appointment version files in your installation directory WITHOUT REPLACING THE `config.php` FILE. This is the only file that does not need to be replaced in all versions.
  3. **Open** your browser and head to the following URL `http://url-to-ea-installation/index.php/backend/update` (needs admin authorization). You should then see a "SUCCESS" message which means that your database has been successfully patched and you can now use the new version!

If by chance you get a 500 Internal Server Error message then you must check the error log files of your server. These kind of errors comes mostly due to problematic server configuration (permissions, credentials etc). 

### Updating from v1.1.x to v1.2.x 

Version v1.2 introduces two new folders in the root directory of the project, the "engine" and the "storage" directory. Ensure that the storage directory is writable and has the correct permissions.

### Updating from v1.0 to v1.1.x

Many core files were changed in v1.1 and it would be better if you replace all the Easy!Appointments files of version 1.0 with the new ones. Use the data of the old `configuration.php` file in the new `config.php` and open the `http://url-to-ea-installation/index.php/backend/update` as already mentioned. Your new version should work just fine!

*This document applies to Easy!Appointments v1.4.0.*

[Back](readme.md)
