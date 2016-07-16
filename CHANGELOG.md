# Easy!Appointments - Changelog
This file contains the code changes that were introduced into each release
(starting from v1.1.0) so that is easy for developers to maintain and readjust
their custom modifications on the main project codebase.


### Version 1.2.0
- Issue #180: Add login link to booking footer. 
- Issue #178: Load the application/config/email.php settings to PHPMailer instance.
- Issue #175: The backend must be responsive as well.
- Issue #174: Implementation of a REST API.
- Issue #173: JS Scrollbars do not work for backend/users entries.
- Issue #152: American Time Format (AM/PM)
- Issue #137: Invalid responsive behavior in frontend booking wizard (step #2 - appointment date & time).
- Issue #136: Loading spinner is not shown in during the installation.
- Issue #127: Links in header contain index.php
- Issue #109: Accept multiple attendants in a single service session.
- Issue #63: Update vendor files (CodeIgniter, FullCalendar, Bootstrap, jQuery ...)
- Issue #54: Default auto-complete for the appointment end time according to service duration.
- Issue #45: Add secure flag to CI_SESSION when HTTPS is enabled.
- Issue #24: Provide dedicated URL for separate provider/service bookings.
- Issue #22: Google Calendar Sync - Time Zone Issue

### Version 1.1.1
- Issue #116: Book advance timeout not taken into account for proposed appointments.
- Issue #118: Google Calendar and notification mail problem bug.
- Issue #120: Invalid appointment date set after editing an existing appointment.

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
