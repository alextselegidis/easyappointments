# Easy!Appointments - Changelog

This file contains the code changes that were introduced into each release (starting from v1.1.0) so that is easy for 
developers to maintain and readjust their custom modifications on the main project codebase.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.3.2] - 2018-07-29

### Fixed

- #480: Make the app GDPR - new EU privacy regulations compliant.
- #485: Make REST API search check with "q" parameter case insensitive. 
- #489: REST API response headers must use the Content-Type application/json value.
- #500: Performance optimization in backend calendar page, after the user clicks the insert appointment button.
- #510: Providers should not be able to create appointments for other providers in the backend calendar page.
- #512: Only show appointments of the currently logged in provider.

## [1.3.1] - 2018-06-03

### Added

- #410: Time format from American style to European
- #441: Added time format selection
- #452: Provide more information when errors occur during the installation. 

### Changed

- #494: French translation corrections/improvements.

### Fixed

- #433: Selected date when editing an appointment
- #436: All days unavailable in agendaDay view
- #438: Error on update process from 1.2.1 to 1.3.0
- #440: Correct label text for customer phone number in back-end calendar event popup.
- #453: Unavailable periods not taken into account when more than one customer
- #455: French Spelling
- #459: Aggregate Appointment API crashes when a break was added
- #461: Invalid working plan parsing for foreign languages on day view of the default calendar view.
- #475: Booking page date selection is broken with any_provider option selected.
- #483: In backend, calendar for providers become unselectable if switched to calendar for service.
- #491: Replace hardcoded string with translation in appointment details email template.
- #495: Database migration fixes (from 1.2.1 to 1.3.x).
- #497: Backend settings are not being displayed on page load when the user is not an admin. 

## [1.3.0] - 2018-02-28

### Added 

- #65: Insert new appointment by clicking directly on the calendar.
- #122: Add customer email and phone number in the event popover of the backend/calendar page.
- #152: Add support for American Time Format (AM/PM).
- #176: Add Docker container for Easy!Appointments development.
- #362: Add Arabic language translation.
- #395: Add aggregates GET parameter in the appointments REST API resource.
- #397: Allow PHP v7.1 compatibility.
- #398: Send ICS files with customer/provider email confirmations.
- #399: Integrate cache busting for assets into the app.
- #402: Create simple "update" page.

### Changed 

- #276: Update FullCalendar dependency.
- #394: Corrections in the Bootstrap classes in view files.
- #401: Replace loading spinner graphic with a newer one.
- #403: All calendars will start with Sunday as the first day.

### Fixed

- #155: Appointment management modal is not updated after appointment duration resize.
- #236: Duplicate availabilities with short service duration and unavailabilities ignorance. 
- #315: Calendar doesn't update when Attendants number changes. 
- #334: Use of session_start() function may cause issues as the default options are not being used.
- #336: Deleting provider doesn't work in some languages.
- #337: Full day appointment with multiple attendants are not being taken into concern during availabilities generation.
- #342: Email notifications must honor the date format value.
- #370: AJAX Error: SyntaxError: Unexpected token < in JSON at position 0

### Removed

- #400: Remove jscrollpane dependency.

### Deprecated

- The availabilities generation and AJAX endpoints will change with a future release.

## [1.2.1] - 2017-05-21

### Changed

- #241: Set default sessions save_path directory because many servers do not have this option set.

### Fixed 

- #306: Back-end login does not work with PHP 7.1. 

## [1.2.0] - 2016-11-09

### Added 

- #24: Provide dedicated URL for separate provider/service bookings.
- #45: Add secure flag to CI_SESSION when HTTPS is enabled.
- #54: Default auto-complete for the appointment end time according to service duration.
- #109: Accept multiple attendants in a single service session.
- #180: Add login link to booking footer. 
- #182: Add new appointment dashboard view: Table Scheduler
- #183: Display the appointments modal when the user clicks in an empty calendar slot.
- #185: Enable fixed availabilities setting for services.
- #174: Implementation of a REST API.
- #175: The backend must be responsive as well.
- #178: Load the application/config/email.php settings to PHPMailer instance.

### Changed

- #63: Update vendor files (CodeIgniter, FullCalendar, Bootstrap, jQuery ...)

### Fixed

- #173: JS Scrollbars do not work for backend/users entries.
- #137: Invalid responsive behavior in frontend booking wizard (step #2 - appointment date & time).
- #136: Loading spinner is not shown in during the installation.
- #127: Links in header contain index.php
- #22: Google Calendar Sync - Time Zone Issue

## [1.1.1] - 2016-02-14

### Fixed 

- #116: Book advance timeout not taken into account for proposed appointments.
- #118: Google Calendar and notification mail problem bug.
- #120: Invalid appointment date set after editing an existing appointment.

## [1.1.0] 2016-01-24

### Added

- #14: Add Google Analytics tracking for the booking page.
- #15: Add captcha to booking page.
- #16: Responsive Frontend
- #25: Add a disable customer mail notifications setting
- #27: Support american time format within the app.
- #31: Double booking when two users try to book the same appointment hour and at the same time.

### Changed

- #4: Raising more useful exceptions and enable error logging by default.
- #13: Upgrade to Bootstrap 3.x.x.
- #38: Renamed `configuration.php` file to `config.php` and changed the `SystemConfiguration` class to `Config`. This class will contain constants with the project configuration and will be statically used.
- #39: Add latest translations to source code so that user can select them immediately.
- #42: Place all external assets to "ext" directory.

### Removed 

- #40: Removed `.htaccess` file and updated all the URLs with the `index.php` file so that mod_rewrite problems are eliminated.
- #41: Removed `cancel.php` file. Frontend must use the `message.php` file for displaying simple messages to user.

### Fixed

- #6: Business Logic created is not getting assigned to service provider.
- #10: Unable to use address tags in email address.
- #18: Duration is not changing when adding a new appointment.
- #21: Fix Easy!Appointments installation problems with AJAX requests.
- #66: Trouble with breaks for providers.

## [1.0.0] - 2014-01-19 

First Easy!Appointments release ever! ☺
