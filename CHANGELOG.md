# Easy!Appointments - Changelog
This file contains the code changes that were introduced into each release (starting from v1.1.0) so that is easy for 
developers to maintain and readjust their custom modifications on the main project codebase.

### Version 1.2.1

- #241: Set default sessions save_path directory because many servers do not have this option set.
- #306: Back-end login does not work with PHP 7.1. 

### Version 1.2.0
- #185: Enable fixed availabilities setting for services.
- #183: Display the appointments modal when the user clicks in an empty calendar slot.
- #182: Add new appointment dashboard view: Table Scheduler
- #180: Add login link to booking footer. 
- #178: Load the application/config/email.php settings to PHPMailer instance.
- #175: The backend must be responsive as well.
- #174: Implementation of a REST API.
- #173: JS Scrollbars do not work for backend/users entries.
- #137: Invalid responsive behavior in frontend booking wizard (step #2 - appointment date & time).
- #136: Loading spinner is not shown in during the installation.
- #127: Links in header contain index.php
- #109: Accept multiple attendants in a single service session.
- #63: Update vendor files (CodeIgniter, FullCalendar, Bootstrap, jQuery ...)
- #54: Default auto-complete for the appointment end time according to service duration.
- #45: Add secure flag to CI_SESSION when HTTPS is enabled.
- #24: Provide dedicated URL for separate provider/service bookings.
- #22: Google Calendar Sync - Time Zone Issue

### Version 1.1.1

- #116: Book advance timeout not taken into account for proposed appointments.
- #118: Google Calendar and notification mail problem bug.
- #120: Invalid appointment date set after editing an existing appointment.

### Version 1.1.0

- #4: Raising more useful exceptions and enable error logging by default.
- #6: Business Logic created is not getting assigned to service provider.
- #10: Unable to use address tags in email address.
- #13: Upgrade to Bootstrap 3.x.x.
- #14: Add Google Analytics tracking for the booking page.
- #15: Add captcha to booking page.
- #16: Responsive Frontend
- #18: Duration is not changing when adding a new appointment.
- #21: Fix E!A installation problems with AJAX requests.
- #25: Add a disable customer mail notifications setting
- #27: Support american time format within the app.
- #31: Double booking when two users try to book the same appointment hour and at the same time.
- #38: Renamed `configuration.php` file to `config.php` and changed the `SystemConfiguration` class to `Config`. This class will contain constants with the project configuration and will be statically used.
- #39: Add latest translations to source code so that user can select them immediately.
- #40: Removed `.htaccess` file and updated all the URLs with the `index.php` file so that mod_rewrite problems are eliminated.
- #41: Removed `cancel.php` file. Frontend must use the `message.php` file for displaying simple messages to user.
- #42: Place all external assets to "ext" directory.
- #66: Trouble with breaks for providers.
