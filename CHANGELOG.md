# Easy!Appointments - Changelog
This file contains the code changes that were introduced into each release
(starting from v1.1.0) so that is easy for developers to maintain and readjust
their custom modifications on the main project codebase.

### Version 1.1.1
- Issue #116: Book advance timeout not taken into account for proposed appointments.
- Issue #118: Google Calendar and notification mail problem bug.

### Version 1.1.0
- Issue #4: Raising more useful exceptions and enable error logging by default.
- Issue #6: Business Logic created is not getting assigned to service provider.
- Issue #10: Unable to use address tags in email address.
- Issue #13: Upgrade to Bootstrap 3.x.x.
- Issue #14: Add Google Analytics tracking for the booking page.
- Issue #15: Add captcha to booking page.
- Issue #16: Responsive Frontend
- Issue #18: Duration is not changing when adding a new appointment.
- Issue #21: Fix E!A installation problems with AJAX requests.
- Issue #25: Add a disable customer mail notifications setting
- Issue #27: Support american time format within the app.
- Issue #31: Double booking when two users try to book the same appointment hour and at the same time.
- Issue #38: Renamed `configuration.php` file to `config.php` and changed the `SystemConfiguration` class to `Config`. This class will contain constants with the project configuration and will be statically used.
- Issue #39: Add latest translations to source code so that user can select them immediately.
- Issue #40: Removed `.htaccess` file and updated all the URLs with the `index.php` file so that mod_rewrite problems are eliminated.
- Issue #41: Removed `cancel.php` file. Frontend must use the `message.php` file for displaying simple messages to user.
- Issue #42: Place all external assets to "ext" directory.
- Issue #66: Trouble with breaks for providers.
