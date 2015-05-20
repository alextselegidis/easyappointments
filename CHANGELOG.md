# Easy!Appointments - Changelog 
This file contains the code changes that were introduced into each release (starting from v1.1) so that is easy for developers to maintain and readjust their custom modifications on the main project codebase.

### Version 1.1 
- Renamed `configuration.php` file to `config.php` and changed the `SystemConfiguration` class to `Config`. This class will contain constants with the project configuration and will be statically used.
- Updated project to Bootstrap v3.3.4 and modified frontend CSS so that it is responsive.
- Removed `cancel.php` file. Frontend must use the `message.php` file for displaying simple messages to user.
- Added new translations to project (japanese, polish, luxembourgish, protuguese-br, french, chinese, italian, spanish, dutch, danish).
- Removed `.htaccess` file and updated all the URLs with the `index.php` file so that mod_rewrite problems are eliminated.
