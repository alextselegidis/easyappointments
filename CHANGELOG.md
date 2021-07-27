# Easy!Appointments - Changelog

This file contains the code changes that were introduced into each release (starting from v1.1.0) so that is easy for 
developers to maintain and readjust their custom modifications on the main project codebase.

## [1.4.2] - 2021-07-27

### Added 

- #1004: Add support for line breaks when displaying the service description in the frontend.
- #1040: Support all-day events while syncing with Google Calendar.

### Fixed

- #961: Timezone/UX issue: Wrong day is selected when timezone differs by -1 day.
- #966: Secretaries are getting notification emails for providers that are not assigned to them.
- #980: Missing Pacific (and potentially other) timezones.
- #982: The Any-Provider option might lead to double bookings, if all the providers have the same number of appointments for the selected date.
- #986: Managed to replicate appointment hash collisions. 
- #989: Fix Critical mistake resulting in wrong date
- #990: The API availabilities controller throws an error when generating availability for services with multiple attendants.
- #991: Available hours generated with the "Any Provider" option in the booking page, may use the information of a provider that is not assigned to the selected service.
- #993: Add support for PHP8 (vendor packages need to be updated).
- #1000: Small fix for the display of the delete button in table view.
- #1011: Working plan exception - details pane shows incorrect details.
- #1023: Backend calendar table events missing or duplicated.
- #1026: The timepicker sliders do not work when using an iOS device.
- #1029: Enhance SMTP functions of PHPMailer.
- #1043: Unavailable events do not block time from services with multiple attendants.
- #1046: Make sure that saving the modifications of a single break does not cancel any pending break edits.
- #1068: Set minimum service duration field value to honor the value of EVENT_MINIMUM_DURATION.
- #1073: Update PHPMailer dependencies.
- #1074: In case of deletion of one appointment, system sends email to admins anyway even if they have email notifications disabled.
- #1092: Javascript RangeError on appointment change causing disabled calendar dates.

## [1.4.1] - 2020-12-14

### Added 

- #952: Add timezone support in the REST API, when managing users.
- #955: Display confirmation modal when disabling a connected Google Calendar Sync.

### Fixed 

- #945: Google Calendar sync throws an error with all day Google Calendar Events.
- #946: Typo in JavaScript code leads to a broken calendar view, when loading unavailability events with note contents.
- #948: Multiple attendant services may lead to double booking.
- #950: Cannot create provider without services via the API, some values (other endpoints) are optional too.
- #953: Current time indicator in fullcalendar is showing time in local timezone and not in the user selected timezone.
- #954: The password must be provided via the API when creating new users.

## [1.4.0] - 2020-12-09

### Added
 
- #203: Appointment location / 12-hour format / sync notes and location in Google Calendar.
- #221: Fixed/Improved sort breaks increasingly by hour within day.
- #247: Add new system-wide setting for removing the "Any Provider" option of the booking page.
- #251: Automatically populate the appointment end datetime in API.
- #301: Automatically reload the backend calendar events.
- #313: How to set the timezone from the user booking the appointment.
- #365: Only allow appointments for a few weeks in advance.
- #431: Add support for working plan exceptions.
- #471: Add new system-wide setting that enable users to choose the first day of the week.
- #496: Add pagination on every backend page in order to make filter requests faster.
- #501: Integrate script for assets minification.
- #502: Config::DEBUG value toggles the use of normal or minified asset files.
- #546: Add appointment edit link in the backend customers page.
- #550: Multi-Lang Front-End selection popup not working on mobile.
- #551: Front-End booking calendar not syncing with business logic working plan.
- #572: Ensure the database structure is compatible to at least MySQL 5.5.
- #576: Appointment cancelled exception not showing properly.
- #610: Token based authentication for the Rest API.
- #648: Add a warning when customers delete their personal information.
- #655: Creating an appointment requires user to enter their phone number enhancement.
- #659: Automatically detect browser language enhancement.
- #663: Language selector not working under legacy iOS (v.10.3.1).
- #680: Generate new password in the generate_random_string function may create duplicate passwords, plus it is not secure enough.
- #739: Enhance the table view mode by replacing the tables with fullcalendar instances.
- #770: Store customer's language and use it with notifications or when the customer manages and existing appointment.
- #889: Notify admins and secretaries on appointment changes.

### Changed 

- #386: Service price should be optional.
- #428: Enable book advance timeout values in days.
- #568: Sort providers alphabetically in the booking page.
- #745: Add appointment notes preview in the event popover.

### Fixed 

- #171: Google calendar sync - wrong timezone for appointments.
- #195: Fix Google calendar sync activation error (JavaScript).
- #298: Provider availability issue when selecting the "Any Provider" option.
- #396: Start and end time do not update correctly during calendar time selection on iPad (and other Safari based devices).
- #447: Captcha error using docker (500 error).
- #506: Working plan created in version v1.2.1 wrongly displayed in backend with version v1.3.1.
- #507: Need to manually clean the cache when migrating from v1.2.1 to v1.3.1.
- #541: Can't remove (empty) customer notes field.
- #549: Querying appointments API endpoint with the q parmeter produces PHP warnings.
- #557: App not connecting to MySQL with fresh docker run.
- #562: Unavailability periods with length of more than 1 day are not handled correctly.
- #563: Description field overflows with long text.
- #600: Unable to select Language on mobile phones.
- #611: Double replacement when using translation to other languages.
- #664: Easy!Appointments v1.3.2 allows sensitive information disclosure (username and password hash).
- #687: Errors when the provider modifies an appointment.
- #705: The alert notification of the installation is not being displayed on error.
- #757: Corrected display of datetimepickers when editing events.
- #801: Invalid time duration during appointment registration could lead to DOS of the service.
- #813: Hyperlinks are not being displayed correctly inside legal contents (they are escaped).
- #839: Provider is missing on appointment modal opened after a click on the link sent with the provider email confirmation.
- #840: Start/end datetime are not correctly initialized on Safari when the appointment modal is opened after a click in the backend calendar.
- #883: Appointment date is wrongly changed to today in some case.
- #903: Notification not working when creating/updating/deleting an appointment from the REST API.

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
